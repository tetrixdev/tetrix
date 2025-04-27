<?php

namespace Tetrix\Middlewares;

use Closure;
use Illuminate\Http\RedirectResponse;

class TetrixRedirect
{
    public function handle($request, Closure $next)
    {
        // Let the request proceed through the app:
        $response = $next($request);

        // Only transform if it's an HTMX request
        if ($request->header('HX-Request')) {
            // Only manipulate if the response is a redirect
            if ($response instanceof RedirectResponse) {
                // Check if there's a validation error bag in the session
                $errors = $request->session()->get('errors');
                $hasErrors = $errors && count($errors->all()) > 0;

                // Ignore if the response is already a 303 redirect
                if ($response->status() === 303) {
                    return $response;
                }

                if ($hasErrors) {
                    // Force 303 so HTMX will convert subsequent request to GET
                    $response->setStatusCode(303);

                    // TODO: check if this is needed with the macro for redirect->back
                    // If we have errors, we want to redirect back to the modal.
                    // We can override the redirect code + URL.
                    $modalUrl = $request->header('TX-Modal-Referer');

                    if ($modalUrl) {
                        // Change the redirect URL to the modal URL
                        $response->setTargetUrl($modalUrl);
                    }
                } else {
                    // No errors. We want to do a full-page redirect in the browser:
                    // In HTMX, returning an `HX-Redirect` header with 200 status
                    // tells HTMX to load that URL in the full window.
                    $targetUrl = $response->getTargetUrl();

                    // We have to create new response object with status 200 and add HX-Redirect header because current response object is responseRedirect and ignore function
                    // $response->setStatusCode(200)
                    return response('', 200)->header('HX-Redirect', $targetUrl);
                }
            }
        }

        return $response;
    }
}
