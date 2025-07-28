<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_documento',
        'cnpj',
        'razao_social',
        'nome_fantasia',
        'email',
        'telefone',
        'ramo_atividade',
        'endereco_id',
    ];

    /**
     * Get the endereco that belongs to the empresa.
     */
    public function endereco(): BelongsTo
    {
        return $this->belongsTo(Endereco::class);
    }

    /**
     * Get all anexos for the empresa.
     */
    public function anexos(): HasMany
    {
        return $this->hasMany(Anexo::class);
    }

    /**
     * Validation rules for the model
     */
    public static function validationRules()
    {
        return [
            'cnpj' => 'required|string|min:14|max:18|unique:empresas,cnpj',
            'razao_social' => 'required|string|min:3|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'email' => 'required|email|max:150',
            'telefone' => 'required|string|min:10|max:15',
            'ramo_atividade' => 'required|string|max:100',
        ];
    }
}
