<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeocodeService
{
    public function geocode(string $fullAddress): ?array
    {
        $apiKey = config('services.google_maps.key');

        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => $fullAddress,
            'key'     => $apiKey,
        ]);

        if ($response->failed()) {
            return null;
        }

        $data = $response->json();

        if (($data['status'] ?? '') !== 'OK' || empty($data['results'][0]['geometry']['location'])) {
            return null;
        }

        $location = $data['results'][0]['geometry']['location'];

        return [
            'lat' => $location['lat'],
            'lng' => $location['lng'],
        ];
    }
}