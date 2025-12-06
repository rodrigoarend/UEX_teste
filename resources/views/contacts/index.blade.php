{{-- resources/views/contacts/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Contatos')

@section('content')
<div class="container py-4">

    {{-- Título --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Lista de Contatos</h1>
        <a href="{{ route('logout') }}" class="btn btn-outline-danger btn-sm">Sair</a>
    </div>

    {{-- Filtro por nome/CPF --}}
    <form method="GET" action="{{ route('contacts.index') }}" class="row g-2 mb-4">
        <div class="col-md-4">
            <input
                type="text"
                name="q"
                class="form-control"
                placeholder="Buscar por nome ou CPF..."
                value="{{ request('q') }}"
            >
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
        <div class="col-md-2 d-grid">
            <a href="{{ route('contacts.index') }}" class="btn btn-secondary">Limpar</a>
        </div>
    </form>

    <div class="row">
        {{-- Lista + formulário --}}
        <div class="col-md-6">

            {{-- Tabela de contatos --}}
            <div class="card mb-4">
                <div class="card-header">
                    Contatos (clique para centralizar no mapa)
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0 table-hover">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>CPF</th>
                                <th>Telefone</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contacts as $contact)
                                <tr
                                    class="contact-row"
                                    data-lat="{{ $contact->latitude }}"
                                    data-lng="{{ $contact->longitude }}"
                                >
                                    <td>{{ $contact->name }}</td>
                                    <td>{{ $contact->cpf }}</td>
                                    <td>{{ $contact->phone }}</td>
                                    <td class="text-end">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            onclick="centerMapOnContact(contact.latitude, contact.longitude)";
                                        >
                                            Ver no mapa
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3">
                                        Nenhum contato encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($contacts, 'links'))
                    <div class="card-footer">
                        {{ $contacts->withQueryString()->links() }}
                    </div>
                @endif
            </div>

            {{-- FORMULÁRIO DE CADASTRO / EDIÇÃO --}}
            <div class="card">
                <div class="card-header">
                    Cadastrar / Editar Contato
                </div>
                <div class="card-body">
                    {{-- Exemplo: se tiver edição, você passa $editing = true e $contact sendo editado --}}
                    @php
                        /** @var \App\Models\Contact|null $editingContact */
                        $editingContact = $editingContact ?? null;
                    @endphp

                    <form
                        method="POST"
                        action="{{ $editingContact ? route('contacts.update', $editingContact) : route('contacts.store') }}"
                    >
                        @csrf
                        @if($editingContact)
                            @method('PUT')
                        @endif

                        {{-- Nome --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $editingContact->name ?? '') }}"
                                required
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- CPF --}}
                        <div class="mb-3">
                            <label for="cpf" class="form-label">CPF</label>
                            <input
                                type="text"
                                id="cpf"
                                name="cpf"
                                class="form-control @error('cpf') is-invalid @enderror"
                                value="{{ old('cpf', $editingContact->cpf ?? '') }}"
                                placeholder="000.000.000-00"
                                required
                            >
                            @error('cpf')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Telefone --}}
                        <div class="mb-3">
                            <label for="phone" class="form-label">Telefone</label>
                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                class="form-control @error('phone') is-invalid @enderror"
                                value="{{ old('phone', $editingContact->phone ?? '') }}"
                                required
                            >
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- CEP + botão de buscar endereço --}}
                        <div class="mb-3">
                            <label for="cep" class="form-label">CEP</label>
                            <div class="input-group">
                                <input
                                    type="text"
                                    id="cep"
                                    name="cep"
                                    class="form-control @error('cep') is-invalid @enderror"
                                    value="{{ old('cep', $editingContact->cep ?? '') }}"
                                    placeholder="00000-000"
                                    required
                                >
                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    id="btnBuscaCep"
                                >
                                    Buscar endereço (ViaCEP)
                                </button>
                            </div>
                            @error('cep')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Também há autocomplete usando UF/Cidade/Logradouro logo abaixo.
                            </div>
                        </div>

                        {{-- UF / Cidade / Logradouro (para autocomplete) --}}
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label for="state" class="form-label">UF</label>
                                <input
                                    type="text"
                                    id="state"
                                    name="state"
                                    class="form-control @error('state') is-invalid @enderror"
                                    value="{{ old('state', $editingContact->state ?? '') }}"
                                    maxlength="2"
                                    required
                                >
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-5">
                                <label for="city" class="form-label">Cidade</label>
                                <input
                                    type="text"
                                    id="city"
                                    name="city"
                                    class="form-control @error('city') is-invalid @enderror"
                                    value="{{ old('city', $editingContact->city ?? '') }}"
                                    required
                                >
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-5">
                                <label for="street" class="form-label">Logradouro</label>
                                <input
                                    type="text"
                                    id="street"
                                    name="street"
                                    class="form-control @error('street') is-invalid @enderror"
                                    value="{{ old('street', $editingContact->street ?? '') }}"
                                    required
                                >
                                @error('street')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Digite parte do endereço para ver sugestões.
                                </div>
                            </div>
                        </div>

                        {{-- Sugestões de endereço (autocomplete ViaCEP) --}}
                        <div class="mb-3" id="addressSuggestionsWrapper" style="display:none;">
                            <label class="form-label">Sugestões de endereço</label>
                            <ul class="list-group" id="addressSuggestions"></ul>
                        </div>

                        {{-- Número / Complemento / Bairro --}}
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="number" class="form-label">Número</label>
                                <input
                                    type="text"
                                    id="number"
                                    name="number"
                                    class="form-control @error('number') is-invalid @enderror"
                                    value="{{ old('number', $editingContact->number ?? '') }}"
                                    required
                                >
                                @error('number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="complement" class="form-label">Complemento</label>
                                <input
                                    type="text"
                                    id="complement"
                                    name="complement"
                                    class="form-control @error('complement') is-invalid @enderror"
                                    value="{{ old('complement', $editingContact->complement ?? '') }}"
                                >
                                @error('complement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-5">
                                <label for="neighborhood" class="form-label">Bairro</label>
                                <input
                                    type="text"
                                    id="neighborhood"
                                    name="neighborhood"
                                    class="form-control @error('neighborhood') is-invalid @enderror"
                                    value="{{ old('neighborhood', $editingContact->neighborhood ?? '') }}"
                                    required
                                >
                                @error('neighborhood')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Latitude/Longitude (preenchidas via backend/Google Maps) --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input
                                    type="text"
                                    id="latitude"
                                    name="latitude"
                                    class="form-control @error('latitude') is-invalid @enderror"
                                    value="{{ old('latitude', $editingContact->latitude ?? '') }}"
                                    readonly
                                >
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input
                                    type="text"
                                    id="longitude"
                                    name="longitude"
                                    class="form-control @error('longitude') is-invalid @enderror"
                                    value="{{ old('longitude', $editingContact->longitude ?? '') }}"
                                    readonly
                                >
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-success">
                                {{ $editingContact ? 'Atualizar' : 'Salvar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        {{-- MAPA --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    Localização do Contato
                </div>
                <div class="card-body p-0">
                    <div id="map" style="width: 100%; height: 500px;"></div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    {{-- Google Maps JS (usa a key do config/services.php) --}}
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap"
        async defer>
    </script>

    <script>
        let map;
        let marker = null;

        function initMap() {
            // Centro padrão (ex: Curitiba) se não tiver contato
            const defaultCenter = { lat: -25.4284, lng: -49.2733 };

            map = new google.maps.Map(document.getElementById('map'), {
                center: defaultCenter,
                zoom: 12
            });

           // @if($contacts->count() > 0 && $contacts->first()->latitude && $contacts->first()->longitude)
             //   centerMapOnContact({{ $contacts->first()->latitude }}, {{ $contacts->first()->longitude }});
            //@endif
        }

        function centerMapOnContact(lat, lng) {
            if (!lat || !lng) {
                alert('Contato sem coordenadas cadastradas.');
                return;
            }

            const position = { lat: parseFloat(lat), lng: parseFloat(lng) };

            if (!marker) {
                marker = new google.maps.Marker({
                    position,
                    map: map
                });
            } else {
                marker.setPosition(position);
            }

            map.setCenter(position);
            map.setZoom(16);
        }

        // ------- Autocomplete ViaCEP (UF, Cidade, Logradouro) -------

        const viaCepAutocompleteUrl = "{{ route('via-cep.autocomplete') }}"; 
        // Ajuste o nome da rota pra bater com o que você criou no ViaCepController

        const streetInput = document.getElementById('street');
        const stateInput  = document.getElementById('state');
        const cityInput   = document.getElementById('city');
        const cepInput    = document.getElementById('cep');
        const neighInput  = document.getElementById('neighborhood');
        const numberInput = document.getElementById('number');
        const latInput    = document.getElementById('latitude');
        const lngInput    = document.getElementById('longitude');

        const suggestionsWrapper = document.getElementById('addressSuggestionsWrapper');
        const suggestionsList    = document.getElementById('addressSuggestions');

        let autocompleteTimeout = null;

        function debounceAutocomplete() {
            if (autocompleteTimeout) {
                clearTimeout(autocompleteTimeout);
            }
            autocompleteTimeout = setTimeout(fetchAddressSuggestions, 400);
        }

        async function fetchAddressSuggestions() {
            const uf    = stateInput.value.trim();
            const city  = cityInput.value.trim();
            const query = streetInput.value.trim();

            if (uf.length !== 2 || city.length < 2 || query.length < 3) {
                suggestionsWrapper.style.display = 'none';
                suggestionsList.innerHTML = '';
                return;
            }

            try {
                const url = new URL(viaCepAutocompleteUrl, window.location.origin);
                url.searchParams.set('uf', uf);
                url.searchParams.set('city', city);
                url.searchParams.set('street', query);

                const response = await fetch(url.toString(), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                if (!response.ok) {
                    console.error('Erro ao buscar sugestões ViaCEP');
                    return;
                }

                const data = await response.json();

                // Esperando um array de endereços no formato:
                // [{ cep, street, neighborhood, city, state, latitude, longitude }, ...]
                suggestionsList.innerHTML = '';

                if (!Array.isArray(data) || data.length === 0) {
                    suggestionsWrapper.style.display = 'none';
                    return;
                }

                data.forEach(item => {
                    const li = document.createElement('li');
                    li.classList.add('list-group-item', 'list-group-item-action');
                    li.textContent = `${item.street}, ${item.neighborhood} - ${item.city}/${item.state} (${item.cep})`;
                    li.style.cursor = 'pointer';
                    li.addEventListener('click', () => {
                        applyAddressSuggestion(item);
                    });
                    suggestionsList.appendChild(li);
                });

                suggestionsWrapper.style.display = 'block';
            } catch (e) {
                console.error(e);
            }
        }

        function applyAddressSuggestion(item) {
            cepInput.value       = item.cep || '';
            streetInput.value    = item.street || '';
            neighInput.value     = item.neighborhood || '';
            cityInput.value      = item.city || '';
            stateInput.value     = item.state || '';

            // Coordenadas vindas do backend (ViaCepController chamando Google Maps)
            if (item.latitude && item.longitude) {
                latInput.value = item.latitude;
                lngInput.value = item.longitude;
                centerMapOnContact(item.latitude, item.longitude);
            }

            suggestionsWrapper.style.display = 'none';
            suggestionsList.innerHTML = '';
        }

        streetInput.addEventListener('input', debounceAutocomplete);
        cityInput.addEventListener('input', debounceAutocomplete);
        stateInput.addEventListener('input', debounceAutocomplete);

        // ------- Botão "Buscar endereço (ViaCEP)" por CEP simples -------

        const btnBuscaCep = document.getElementById('btnBuscaCep');
        const viaCepByCepUrl = "{{ route('via-cep.by-cep') }}"; 
        // rota que recebe ?cep= e retorna um único endereço com lat/lng

        btnBuscaCep.addEventListener('click', async () => {
            const cep = cepInput.value.replace(/\D/g, '');
            if (cep.length !== 8) {
                alert('Informe um CEP válido (8 dígitos).');
                return;
            }

            try {
                const url = new URL(viaCepByCepUrl, window.location.origin);
                url.searchParams.set('cep', cep);

                const response = await fetch(url.toString(), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                if (!response.ok) {
                    alert('Erro ao consultar CEP.');
                    return;
                }

                const data = await response.json();
                // Exemplo de retorno esperado:
                // { cep, street, neighborhood, city, state, latitude, longitude }

                if (!data || !data.cep) {
                    alert('CEP não encontrado.');
                    return;
                }

                streetInput.value = data.street || '';
                neighInput.value  = data.neighborhood || '';
                cityInput.value   = data.city || '';
                stateInput.value  = data.state || '';

                if (data.latitude && data.longitude) {
                    latInput.value = data.latitude;
                    lngInput.value = data.longitude;
                    centerMapOnContact(data.latitude, data.longitude);
                }

            } catch (e) {
                console.error(e);
                alert('Erro inesperado ao consultar CEP.');
            }
        });
    </script>
@endpush
