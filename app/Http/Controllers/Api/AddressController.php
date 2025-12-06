<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AddressController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'uf'     => ['required', 'string', 'size:2'],
            'city'   => ['required', 'string'],
            'street' => ['required', 'string'],
        ]);

        $uf     = $request->get('uf');
        $city   = $request->get('city');
        $street = $request->get('street');

        $baseUrl = config('services.via_cep.base_url');

        $response = Http::get("{$baseUrl}/{$uf}/{$city}/{$street}/json");

        if ($response->failed()) {
            return response()->json(['message' => 'Erro ao consultar ViaCEP'], 502);
        }

        return response()->json($response->json());
    }

    public function geocode(Request $request)
    {
        $request->validate([
            'address' => ['required', 'string'],
        ]);

        $address = $request->get('address');
        $key     = config('services.google_maps.key');
        $url     = config('services.google_maps.geocoding_url');

        $response = Http::get($url, [
            'address' => $address,
            'key'     => $key,
        ]);

        if ($response->failed()) {
            return response()->json(['message' => 'Erro ao consultar Google Maps'], 502);
        }

        $data = $response->json();

        if (($data['status'] ?? '') !== 'OK') {
            return response()->json(['message' => 'Endereço não encontrado'], 404);
        }

        $location = $data['results'][0]['geometry']['location'] ?? null;

        return response()->json([
            'latitude'  => $location['lat'] ?? null,
            'longitude' => $location['lng'] ?? null,
        ]);
    }
}