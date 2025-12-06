<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ViaCepService
{
    public function searchAddress(string $uf, string $city, string $streetPart): array
    {
        $streetPart = urlencode($streetPart);
        $url = "https://viacep.com.br/ws/{$uf}/{$city}/{$streetPart}/json/";

        $response = Http::get($url);

        if ($response->failed()) {
            return [];
        }

        return $response->json() ?? [];
    }
}