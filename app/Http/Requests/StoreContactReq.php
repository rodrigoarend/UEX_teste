<?

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use app\Rules\Cpf;

class StoreContactReq extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // ja é autorizado pelo middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'   => ['required', 'string', 'max:255'],
            'cpf'    => ['required', 'string', 'max:14', new Cpf, 'unique:contacts,cpf,NULL,id,user_id,' . $this->user()->id],
            'phone'  => ['nullable', 'string', 'max:20'],
            'email'  => ['nullable', 'email', 'max:255'],

            'cep'          => ['required', 'string', 'max:9'],
            'street'       => ['required', 'string', 'max:255'],
            'number'       => ['required', 'string', 'max:20'],
            'complement'   => ['nullable', 'string', 'max:255'],
            'neighborhood' => ['nullable', 'string', 'max:255'],
            'city'         => ['required', 'string', 'max:255'],
            'state'        => ['required', 'string', 'size:2'],

            'latitude'  => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ];
    }
}
