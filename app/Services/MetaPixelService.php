<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaPixelService
{
    private string $pixelId;
    private string $token;
    private string $apiUrl;

    public function __construct()
    {
        $this->pixelId = env('FB_PIXEL_ID', '');
        $this->token   = env('FB_CAPI_TOKEN', '');
        $this->apiUrl  = "https://graph.facebook.com/v19.0/{$this->pixelId}/events";
    }

    public function purchase(
        string $orderNumber,
        int    $total,
        string $currency,
        array  $items,
        string $phone,
        string $clientIp    = '',
        string $userAgent   = ''
    ): void {
        if (!$this->pixelId || !$this->token) return;

        $contentIds = array_values(array_filter(
            array_map(fn($i) => isset($i['product_id']) ? (string) $i['product_id'] : null, $items)
        ));

        $payload = [
            'data' => [[
                'event_name'       => 'Purchase',
                'event_time'       => time(),
                'event_id'         => $orderNumber,
                'event_source_url' => 'https://yahyastore.dz/',
                'action_source'    => 'website',
                'user_data'        => array_filter([
                    'ph'                 => [$this->hash($this->normalizePhone($phone))],
                    'client_ip_address'  => $clientIp  ?: null,
                    'client_user_agent'  => $userAgent  ?: null,
                ]),
                'custom_data'      => array_filter([
                    'value'        => $total,
                    'currency'     => $currency,
                    'content_ids'  => $contentIds ?: null,
                    'content_type' => 'product',
                    'num_items'    => array_sum(array_column($items, 'quantity')),
                    'order_id'     => $orderNumber,
                ]),
            ]],
            'access_token' => $this->token,
        ];

        try {
            Http::timeout(5)->post($this->apiUrl, $payload);
        } catch (\Throwable $e) {
            Log::warning('Meta CAPI Purchase failed: ' . $e->getMessage());
        }
    }

    private function normalizePhone(string $phone): string
    {
        // Convert Algerian local format (0612345678) to E.164 (+213612345678)
        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '213' . substr($phone, 1);
        }
        return $phone;
    }

    private function hash(string $value): string
    {
        return hash('sha256', strtolower(trim($value)));
    }
}
