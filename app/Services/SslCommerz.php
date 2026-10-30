<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Small client for the SSLCommerz payment gateway (bKash, Nagad, Rocket, cards).
 * Docs: https://developer.sslcommerz.com/doc/v4/
 */
class SslCommerz
{
    public static function enabled(): bool
    {
        return filled(config('gardener.sslcommerz.store_id')) && filled(config('gardener.sslcommerz.store_password'));
    }

    private static function baseUrl(): string
    {
        return config('gardener.sslcommerz.sandbox')
            ? 'https://sandbox.sslcommerz.com'
            : 'https://securepay.sslcommerz.com';
    }

    /**
     * Start a payment session. Returns the gateway page URL to redirect to,
     * or null if SSLCommerz refused the request.
     */
    public static function initiate(array $data): array
    {
        $response = Http::asForm()->timeout(30)->post(self::baseUrl().'/gwprocess/v4/api.php', array_merge([
            'store_id' => config('gardener.sslcommerz.store_id'),
            'store_passwd' => config('gardener.sslcommerz.store_password'),
            'currency' => config('gardener.currency', 'BDT'),
            'shipping_method' => 'NO',
            'product_category' => 'Service',
            'product_profile' => 'non-physical-goods',
            'cus_country' => 'Bangladesh',
        ], $data));

        return $response->json() ?? [];
    }

    /** Ask SSLCommerz whether a payment (val_id) is really valid. */
    public static function validate(string $valId): array
    {
        $response = Http::timeout(30)->get(self::baseUrl().'/validator/api/validationserverAPI.php', [
            'val_id' => $valId,
            'store_id' => config('gardener.sslcommerz.store_id'),
            'store_passwd' => config('gardener.sslcommerz.store_password'),
            'format' => 'json',
        ]);

        return $response->json() ?? [];
    }
}
