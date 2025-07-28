<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Endereco extends Model
{
    use HasFactory;

    protected $fillable = [
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
    ];

    /**
     * Get all empresas that have this endereco.
     */
    public function empresas(): HasMany
    {
        return $this->hasMany(Empresa::class);
    }

    /**
     * Validation rules for the model
     */
    public static function validationRules()
    {
        return [
            'cep' => 'required|string|min:8|max:9|regex:/^\d{5}-?\d{3}$/', // CEP format
            'logradouro' => 'required|string|min:5|max:255',
            'numero' => 'required|string|min:1|max:10',
            'complemento' => 'nullable|string|min:2|max:100',
            'bairro' => 'required|string|min:2|max:100',
            'cidade' => 'required|string|min:2|max:100',
            'estado' => 'required|string|size:2', // Exactly 2 characters
        ];
    }

    /**
     * Custom validation messages
     */
    public static function validationMessages()
    {
        return [
            'cep.min' => 'O CEP deve ter pelo menos 8 dígitos.',
            'cep.regex' => 'O CEP deve estar no formato 12345-678 ou 12345678.',
            'logradouro.min' => 'O logradouro deve ter pelo menos 5 caracteres.',
            'bairro.min' => 'O bairro deve ter pelo menos 2 caracteres.',
            'cidade.min' => 'A cidade deve ter pelo menos 2 caracteres.',
            'estado.size' => 'O estado deve ter exatamente 2 caracteres (ex: SP).',
        ];
    }
}
