<?php

namespace Tetrix\Middlewares;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Masterminds\HTML5;
use Symfony\Component\HttpFoundation\Response;

class TetrixTargets
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // If the response is redirect than just pass
        if ($response instanceof RedirectResponse) {
            return $response;
        }

        // Only process if the TX-Targets header is present
        $txTargets = $request->header('TX-Targets');
        if (!$txTargets) {
            return $response;
        }

        Log::debug('TetrixTargets');
        Log::debug('TetrixTargets, old values: '.json_encode(old()));
        Log::debug('TetrixTargets, input values: '.json_encode($request->input()));

        // If the TX-Targets header is present, we need to ensure the Vary header is set to TX-Targets
        // We're adding it this way so the header is present regardless of whether we encounter an error or not
        header('Vary: TX-Targets');

        // Ensure response is HTML
        $contentType = $response->headers->get('Content-Type', '');
        if (!str_contains($contentType, 'text/html')) {
            trigger_error('TX-Targets header present but response is not HTML', E_USER_WARNING);
        }

        $html = $response->getContent();
        $html5 = new HTML5();
        $dom = $html5->loadHTML($html);

        $xpath = new \DOMXPath($dom);
        $selectors = explode(',', $txTargets);

        if (empty($selectors)) {
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

            // Use HTML5 serializer to get exact HTML with attributes intact
            $collectedHtml[] = $html5->saveHTML($element);
        }

        $newResponseHTML = implode('', $collectedHtml);
        $newResponse = $response->setContent($newResponseHTML);

        // We're adding the HX-Reswap header as we're not using the HX-Target header to determine what to replace, so we don't want to replace the content of the default HX-Target.
        $newResponse = $newResponse->header('HX-Reswap', 'none');

        return $newResponse;
    }
}
