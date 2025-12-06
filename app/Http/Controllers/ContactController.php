<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreContactReq;
use App\Http\Requests\UpdateContactReq;
use App\Models\Contact;
use App\Services\GeocodeService;

class ContactController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = $user->contacts()->orderBy('name', 'asc');

        if ($search = $request->query('search')) {
            $search = trim($search);
            $query->where(function ($req) use ($search) {
                $req->where('name', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', "%{$search}%");
            });
        }

        $contacts = $query->paginate($request->query('per_page', 15));

        return response()->json($contacts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactReq $request, GeocodeService $geocodingService)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        if (empty($data['latitude']) || empty($data['longitude'])) {
            $fullAddress = "{$data['street']}, {$data['number']} - {$data['city']} - {$data['state']}, {$data['cep']}";
            if ($coords = $geocodingService->geocode($fullAddress)) {
                $data['latitude'] = $coords['lat'];
                $data['longitude'] = $coords['lng'];
            }
        }

        $contact = Contact::create($data);

        return response()->json($contact, 201);
    }

    protected function authorizeContact(Contact $contact): void
    {
        if ($contact->user_id !== auth()->id()) {
            abort(403, 'Acesso negado.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        $this->authorizeContact($contact);

        return response()->json($contact);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactReq $request, Contact $contact, GeocodeService $geocodingService)
    {
        $this->authorizeContact($contact);

        $data = $request->validated();

        $contact->fill($data);

        if ($contact->isDirty(['street', 'number', 'city', 'state', 'cep']) || empty($contact->latitude) || empty($contact->longitude)) {
            $fullAddress = "{$contact->street}, {$contact->number} - {$contact->city} - {$contact->state}, {$contact->cep}";
            if ($coords = $geocodingService->geocode($fullAddress)) {
                $contact->latitude = $coords['lat'];
                $contact->longitude = $coords['lng'];
            }
        }

        $contact->save();

        return response()->json($contact);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        $this->authorizeContact($contact);

        $contact->delete();

        return response()->json([], 204);
    }
}
