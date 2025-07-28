<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmpresaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'RazaoSocial' => 'required|string|min:3|max:255', // Min 3, Max 255 characters
            'cnpj' => 'required|string|min:14|max:18|unique:empresas,cnpj', // CNPJ format
            'email' => 'nullable|email|max:150',
            'telefone' => 'nullable|string|min:10|max:15', // Min 10 digits for phone
        ];
    }

    public function messages()
    {
        return [
            'RazaoSocial.min' => 'A Razão Social deve ter pelo menos 3 caracteres.',
            'RazaoSocial.max' => 'A Razão Social não pode ter mais que 255 caracteres.',
            'cnpj.min' => 'O CNPJ deve ter pelo menos 14 caracteres.',
            'telefone.min' => 'O telefone deve ter pelo menos 10 dígitos.',
        ];
    }
}
