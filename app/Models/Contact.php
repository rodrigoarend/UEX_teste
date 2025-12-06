<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'contacts';

    // Campos da tabela contatos permitidos para cadastro/edição
    protected $fillable = [
        'user_id',
        'name',
        'cpf',
        'phone',
        'cep',
        'state',
        'city',
        'district',
        'street',
        'number',
        'complement',
        'latitude',
        'longitude'
    ];

    protected $casts = [
        'latitude'  => 'double',
        'longitude' => 'double',
    ];

    /**
     * Relacionamento: um contato pertence a um usuário
     */
 
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Validação de CPF
     */
    public static function validateCPF($cpf)
    {   //elimina caractares nao numerais
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        // valida tamanho e caracteres
        if (strlen($cpf) !== 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        //calculo do numeral de validacao
        for ($t = 9; $t < 11; $t++) {
            $d = 0;

            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }

            $d = ((10 * $d) % 11) % 10;

            if ($cpf[$t] != $d) {
                return false;
            }
        }

        return true;
    }
}