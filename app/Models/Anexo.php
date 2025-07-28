<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anexo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome_arquivo',
        'caminho_arquivo',
        'tipo_mime',
        'tamanho',
        'tipo_anexo',
        'empresa_id',
    ];

    /**
     * Get the empresa that owns the anexo.
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}
