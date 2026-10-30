<?php

if (! function_exists('money')) {
    /** Format an amount with the site currency, e.g. money(1500) → "৳1,500.00" */
    function money($amount): string
    {
        return config('gardener.currency_symbol', '৳').number_format((float) $amount, 2);
    }
}
