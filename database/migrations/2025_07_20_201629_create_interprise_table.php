<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('enderecos', function (Blueprint $table) {
            $table->id();
            $table->string('cep', 9); // 8 digits + 1 hyphen (12345-678)
            $table->string('logradouro', 255);
            $table->string('numero', 10);
            $table->string('complemento', 100)->nullable();
            $table->string('bairro', 100);
            $table->string('cidade', 100);
            $table->string('estado', 2); // SP, RJ, MG, etc.
            $table->timestamps();
        });

        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('cnpj', 18)->unique();
            $table->string('razao_social', 255);
            $table->string('nome_fantasia', 255)->nullable();
            $table->string('email', 150);
            $table->string('telefone', 15);
            $table->string('ramo_atividade', 100);
            $table->foreignId('endereco_id')->constrained('enderecos')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('anexos', function (Blueprint $table) {
            $table->id();
            $table->string('nome_arquivo', 255);
            $table->string('caminho_arquivo', 500);
            $table->string('tipo_mime', 100)->nullable();
            $table->integer('tamanho')->nullable();
            $table->enum('tipo_anexo', ['contrato', 'documento', 'imagem', 'outro'])->default('documento');
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anexos'); // Drop first because it has foreign key
        Schema::dropIfExists('empresas');
        Schema::dropIfExists('enderecos');
    }
};
