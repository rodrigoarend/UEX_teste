<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ViaCepController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Busca endereços no ViaCEP a partir de UF, cidade e logradouro.
     *
     * GET /api/via-cep/search?uf=PR&city=Curitiba&street=Rua+XV
     */
    public function search(Request $request)
    {
        $data = $request->validate([
            'uf'    => ['required', 'string', 'size:2'],
            'city'  => ['required', 'string'],
            'street'=> ['required', 'string'],
        ]);

        $uf     = strtoupper($data['uf']);
        $city   = urlencode($data['city']);
        $street = urlencode($data['street']);

        $url = "https://viacep.com.br/ws/{$uf}/{$city}/{$street}/json/";

        $response = Http::get($url);

        if (!$response->successful()) {
            return response()->json([
                'message' => 'Erro ao consultar o ViaCEP.',
            ], 502);
        }

        $result = $response->json();

        return response()->json($result);
    }

    /**
     * Exemplo de uso do Google Maps Geocoding:
     * recebe um endereço completo e retorna latitude/longitude.
     *
     * GET /api/geocode?address=Rua+XV+de+Novembro,+Curitiba,+PR
     */
    public function geocode(Request $request)
    {
        $data = $request->validate([
            'address' => ['required', 'string'],
        ]);

        $apiKey = config('services.google_maps.key');

        if (!$apiKey) {
            return response()->json([
                'message' => 'Chave da API do Google Maps não configurada.',
            ], 500);
        }

        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => $data['address'],
            'key'     => $apiKey,
        ]);

        if (!$response->successful()) {
            return response()->json([
                'message' => 'Erro ao consultar o Google Geocoding API.',
            ], 502);
        }

        $body = $response->json();

        if (empty($body['results'][0]['geometry']['location'])) {
            return response()->json([
                'message' => 'Endereço não encontrado.',
            ], 404);
        }

        $location = $body['results'][0]['geometry']['location'];

        return response()->json([
            'lat' => $location['lat'],
            'lng' => $location['lng'],
        ]);
    }
}
