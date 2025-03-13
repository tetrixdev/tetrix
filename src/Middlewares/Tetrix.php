<?php

namespace Tetrix\Middlewares;

use Closure;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Tetrix
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only process if the TX-Targets header is present
        $txTargets = $request->header('TX-Targets');
        if (!$txTargets) {
            return $response;
        }

        // If the TX-Targets header is present, we need to ensure the Vary header is set to TX-Targets
        // We're adding it this way so the header is present regardless of whether we encounter an error or not
        header('Vary: TX-Targets');

        // Ensure response is HTML
        $contentType = $response->headers->get('Content-Type', '');
        if (!str_contains($contentType, 'text/html')) {
            trigger_error('TX-Targets header present but response is not HTML', E_USER_WARNING);
        }

        $DOMDocument = new DomDocument('1.0', 'UTF-8');
        // We need to suppress errors because DomDocument gives errors on html5 tags
        // Possibly want to switch to Dom\HTMLDocument in the future, but it's PHP8.4+
        $DOMDocument->loadHTML($response->getContent(), LIBXML_NOERROR);
        $xpath = new DOMXPath($DOMDocument);

        $selectors = explode(',', $txTargets);

        if(empty($selectors)) {
            throw new \RuntimeException('No selectors found in TX-Targets header');
        }

        $collectedHtml = [];

        foreach ($selectors as $selector) {
            $selector = trim($selector);
            $elements = $xpath->query("//*[@id='{$selector}']");

            if ($elements->length > 1) {
                throw new \RuntimeException('Multiple elements found with id: ' . $selector);
            }

            if ($elements->length < 1) {
                throw new \RuntimeException('No elements found with id: ' . $selector);
            }

            $element = $elements->item(0);
            $element->setAttribute('hx-swap-oob', 'true');
            $collectedHtml[] = $DOMDocument->saveHTML($element);
        }

        $newResponseHTML = implode('', $collectedHtml);
        $newResponse = $response->setContent($newResponseHTML);

        // We're adding the HX-Reswap header as we're not using the HX-Target header to determine what to replace, so we don't want to replace the content of the default HX-Target.
        $newResponse = $newResponse->header('HX-Reswap', 'none');

        return $newResponse;
    }
}
