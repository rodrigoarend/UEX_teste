<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Cpf;

class UpdateContactReq extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $contatoId = $this->route('contact');
        return [
            'name'   => ['sometimes', 'required', 'string', 'max:255'],
            'cpf'    => ['sometimes', 'required', 'string', 'max:14', new Cpf,
                'unique:contacts,cpf,' . $contatoId . ',id,user_id,' . $this->user()->id],
            'phone'  => ['sometimes', 'nullable', 'string', 'max:20'],
            'email'  => ['sometimes', 'nullable', 'email', 'max:255'],

            'cep'          => ['sometimes', 'required', 'string', 'max:9'],
            'street'       => ['sometimes', 'required', 'string', 'max:255'],
            'number'       => ['sometimes', 'required', 'string', 'max:20'],
            'complement'   => ['sometimes', 'nullable', 'string', 'max:255'],
            'neighborhood' => ['sometimes', 'nullable', 'string', 'max:255'],
            'city'         => ['sometimes', 'required', 'string', 'max:255'],
            'state'        => ['sometimes', 'required', 'string', 'size:2'],

            'latitude'  => ['sometimes', 'nullable', 'numeric'],
            'longitude' => ['sometimes', 'nullable', 'numeric'],
        ];
    }
}
