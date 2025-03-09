# Tetrix

## Installation

Installing Tetrix on a fresh Laravel project is super simple. First require this package:

```bash
composer require tetrix/tetrix
```

#### Automatic setup of Tetrix NPM packages (recommended)

Tetrix will automatically install the necessary npm packages and run the necessary commands to get you up and running. You can run the following command:

```bash
php artisan tetrix:install
```

#### Manual Installation

If you prefer to install the npm packages manually, we require the following packages:

Package.json (devDependencies):
- `"alpinejs": "^3.0"`
- `"htmx.org": "^2.0"`
- `"tailwindcss": "^4.0"`

You can install these packages by running:

```bash
npm install alpinejs@^3.0 htmx.org@^2.0 tailwindcss@^4.0 --save-dev
```

After installing the npm packages, you have to include the following lines in your `resources/css/app.css` file:

```css
    @import './../../vendor/tetrix/tetrix/src/Resources/css/tetrix.css')
```

And in your `resources/js/app.js` file:

```javascript
    import './../../vendor/tetrix/tetrix/src/Resources/js/tetrix.js'
```

## Usage

TODO: Write usage instructions