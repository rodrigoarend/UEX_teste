<?php

namespace App\Http\Controllers;
use App\Services\ViaCepService;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __invoke(Request $request, ViaCepService $viaCepService)
    {
        $request->validate([
            'uf'     => ['required', 'string', 'size:2'],
            'city'   => ['required', 'string'],
            'street' => ['required', 'string'],
        ]);

        $addresses = $viaCepService->searchAddress(
            strtoupper($request->uf),
            $request->city,
            $request->street
        );

        return response()->json($addresses);
    }
}