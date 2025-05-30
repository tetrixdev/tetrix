<?php

if (!function_exists('tx_value')) {
    function tx_value($param) {
        return old($param) ?? request()?->input($param) ?? null;
    }
}

if (!function_exists('tx_referer')) {
    function tx_referer() {
        return request()->header('TX-Referer') ?? request()->headers->get('referer');
    }
}
