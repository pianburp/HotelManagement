<?php

if (!function_exists('money')) {
    function money($amount, $currency = 'RM', $decimals = 2) {
        return $currency . number_format((float)$amount, $decimals);
    }
}
