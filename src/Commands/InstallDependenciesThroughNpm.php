<?php

namespace Tetrix\Commands;

use Composer\Semver\Semver;
use Illuminate\Console\Command;

class InstallDependenciesThroughNpm extends Command
{
    protected $signature = 'tetrix:install-npm';
    protected $description = 'Check and add missing front-end dependencies to package.json and app.js';

    public function handle()
    {
        $dependencies = [
            'alpinejs' => '^3.0', // 'https://registry.npmjs.org/alpinejs/latest'
            'tailwindcss' => '^4.0', // 'https://registry.npmjs.org/tailwindcss/latest'
            'htmx.org' => '^2.0', // 'https://registry.npmjs.org/htmx.org/latest'
            "@fortawesome/fontawesome-free" => "^6.7" // 'https://registry.npmjs.org/@fortawesome/fontawesome-free/latest'
        ];

        $packageJsonPath = base_path('package.json');

        if (!file_exists($packageJsonPath)) {
            $this->error('package.json not found. Make sure you are in the right project directory.');
            return 1;
        }

        // Read package.json
        $packageJson = json_decode(file_get_contents($packageJsonPath), true);

        // Ensure devDependencies exists
        if (!isset($packageJson['devDependencies'])) {
            $packageJson['devDependencies'] = [];
        }

        // Ensure devDependencies exists
        if (!isset($packageJson['dependencies'])) {
            $packageJson['dependencies'] = [];
        }

        $missingDependencies = [];
        $existingDependencies = [];
        $problematicDependencies = [];

        foreach ($dependencies as $package => $constraintString) {
            // Check if the package doesn't exist in either dependencies or devDependencies
            if (!isset($packageJson['devDependencies'][$package]) && !isset($packageJson['dependencies'][$package])) {
                $packageJson['devDependencies'][$package] = $constraintString;
                $missingDependencies[$package] = $constraintString;
                continue;
            }

            if(isset($packageJson['devDependencies'][$package])) {
                $allowedVersions = $this->extractSemverVersions($packageJson['devDependencies'][$package]);
                if(count(Semver::satisfiedBy($allowedVersions, $constraintString)) === count($allowedVersions)) {
                    $existingDependencies[$package] = $constraintString;
                } else {
                    $incompatibleVersions = array_diff($allowedVersions, Semver::satisfiedBy($allowedVersions, $constraintString));
                    $problematicDependencies[$package] = [
                        'constraint' => $constraintString,
                        'incompatibleVersions' => $incompatibleVersions
                    ];
                }
            }

            if(isset($packageJson['dependencies'][$package])) {
                $allowedVersions = $this->extractSemverVersions($packageJson['dependencies'][$package]);
                if(count(Semver::satisfiedBy($allowedVersions, $constraintString)) === count($allowedVersions)) {
                    $existingDependencies[$package] = $constraintString;
                } else {
                    $incompatibleVersions = array_diff($allowedVersions, Semver::satisfiedBy($allowedVersions, $constraintString));
                    $problematicDependencies[$package] = [
                        'constraint' => $constraintString,
                        'incompatibleVersions' => $incompatibleVersions
                    ];
                }
            }
        }

        // If there are problematic dependencies, show them and exit
        if(!empty($problematicDependencies)) {
            $this->error("The following dependencies have possible incompatible versions:");
            foreach ($problematicDependencies as $package => $data) {
                $this->error(" - {$package}: {$data['constraint']} (allowed incompatible versions: " . implode(', ', $data['incompatibleVersions']) . ")");
            }
            return 1;
        }

        // If no changes, exit
        if (empty($missingDependencies)) {
            $this->info('All required dependencies are already present.');
            $this->npmInstall();
            $this->updateAppJs();
            $this->updateAppCss();
            return 0;
        }

        // Show user what will be added
        $this->info("The following dependencies will be added to package.json:");
        foreach ($missingDependencies as $package => $constraintString) {
            $this->info(" - {$package}: " . $constraintString);
        }

        // Confirm changes
        if (!$this->confirm('Do you want to proceed with adding these dependencies to package.json?', true)) {
            $this->warn('Operation canceled.');
            return 0;
        }

        // Remove empty devDependencies or dependencies
        if(empty($packageJson['devDependencies'])) {
            unset($packageJson['devDependencies']);
        }
        if(empty($packageJson['dependencies'])) {
            unset($packageJson['dependencies']);
        }

        // Write changes to package.json
        file_put_contents($packageJsonPath, json_encode($packageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $this->info("Dependencies (package.json) updated successfully.");

        $this->npmInstall();
        $this->updateAppJs();
        $this->updateAppCss();
    }

    private function updateAppJs()
    {
        // Find last import statement in app.js
        $appJsPath = base_path('resources/js/app.js');
        $appJsContent = file_get_contents($appJsPath);

        // Check if tetrix.js is already imported
        if (strpos($appJsContent, 'tetrix.js') !== false) {
            $this->info("tetrix.js is already imported in app.js.");
            return 0;
        }

        // get all import statements
        preg_match('/import .+;/m', $appJsContent, $matches);

       // Check if import statement was found
        if (empty($matches)) {
            $this->error("Error: Could not find any import statements in app.js.");
            return 1;
        }

        // get the last import statement
        $lastImportStatement = end($matches);

        // Add import statement for js/tetrix.js, have to use the vendor path
        $newImportStatement = "\nimport './../../vendor/tetrix/tetrix/src/Resources/js/tetrix.js';";
        $appJsContent = str_replace($lastImportStatement, $lastImportStatement . $newImportStatement, $appJsContent);

        // Write changes to app.js
        file_put_contents($appJsPath, $appJsContent);

        $this->info("app.js updated successfully.");
        return 0;
    }

    private function updateAppCss()
    {
        // Find last import statement in app.css
        $appCssPath = base_path('resources/css/app.css');
        $appCssContent = file_get_contents($appCssPath);

        // Check if tetrix.css is already imported
        if (strpos($appCssContent, 'tetrix.css') !== false) {
            $this->info("tetrix.css is already imported in app.css.");
            return 0;
        }

        // get all import statements
        preg_match('/@import .+;/m', $appCssContent, $matches);

        // Check if import statement was found
        if (empty($matches)) {
            $this->error("Error: Could not find any import statements in app.css.");
            return 1;
        }

        // get the last import statement
        $lastImportStatement = end($matches);

        // Add import statement for css/tetrix.css, have to use the vendor path
        $newImportStatement = "\n@import './../../vendor/tetrix/tetrix/src/Resources/css/tetrix.css';";
        $appCssContent = str_replace($lastImportStatement, $lastImportStatement . $newImportStatement, $appCssContent);

        // Write changes to app.css
        file_put_contents($appCssPath, $appCssContent);

        $this->info("app.css updated successfully.");
        return 0;
    }

    private function npmInstall()
    {
        // Run npm install
        $this->info("We will try to run `npm install`, if this fails then you should do this manually. Running npm install...");
        $result = shell_exec('npm install');

        // Check if npm install was successful
        if (strpos($result, 'added') === false && strpos($result, 'up to date') === false) {
            $this->error("Error: npm install failed. Please run `npm install` manually.");
            return 1;
        }

        // Check if npm install was successful
        if (strpos($result, 'added') === true) {
            $this->info("Dependencies installed successfully.");
            return 0;
        }

        $this->info("Dependencies are already up to date.");
        return 0;
    }

    /**
     * Extracts all valid semver versions from a string.
     */
    private function extractSemverVersions($versionString)
    {
        // Match all semver versions (major.minor.patch) in the string
        preg_match_all('/(\d+)(?:\.(\d+))?(?:\.(\d+))?/', $versionString, $matches);

        if (empty($matches[0])) {
            throw new \RuntimeException("Error: Could not extract a valid version from '{$versionString}'.");
        }

        return $matches[0]; // Return all found versions
    }
}
