<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmpresaRequest;
use App\Models\Empresa;
use App\Models\Endereco;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class EmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $empresas = Empresa::with(['endereco', 'anexos'])
            ->withCount('anexos')
            ->orderBy('razao_social')
            ->get();

        return Inertia::render('Empresas/Index', [
            'empresas' => $empresas->map(function ($empresa) {
                return [
                    'id' => $empresa->id,
                    'cnpj' => $empresa->cnpj,
                    'razao_social' => $empresa->razao_social,
                    'nome_fantasia' => $empresa->nome_fantasia,
                    'email' => $empresa->email,
                    'telefone' => $empresa->telefone,
                    'ramo_atividade' => $empresa->ramo_atividade,
                    'endereco' => $empresa->endereco,
                    'anexos_count' => $empresa->anexos_count,
                ];
            })
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Empresas/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // First validate basic required fields
        $request->validate([
            'tipo_documento' => 'required|in:CNPJ,CPF',
        ]);

        // Build validation rules based on document type
        $rules = [
            'tipo_documento' => 'required|in:CNPJ,CPF',
            'cnpj' => 'required|string|min:11|max:18|unique:empresas,cnpj',
            'nome_fantasia' => 'nullable|string|max:255',
            'email' => 'required|email:rfc,dns|max:150',
            'telefone' => 'required|string|min:14|max:15', // Brazilian phone format
            'ramo_atividade' => 'required|string|max:100',
            // Endereco fields - all required
            'endereco.cep' => 'required|string|size:9', // Format: 12345-678
            'endereco.logradouro' => 'required|string|min:5|max:255',
            'endereco.numero' => 'required|string|min:1|max:10',
            'endereco.complemento' => 'nullable|string|max:100',
            'endereco.bairro' => 'required|string|min:2|max:100',
            'endereco.cidade' => 'required|string|min:2|max:100',
            'endereco.estado' => 'required|string|size:2',
        ];

        // Conditional validation for razão social based on document type
        if ($request->input('tipo_documento') === 'CNPJ') {
            $rules['razao_social'] = 'required|string|min:3|max:255';
        } else {
            $rules['razao_social'] = 'required|string|min:1|max:255';
        }

        $messages = [
            'tipo_documento.required' => 'O tipo de documento é obrigatório.',
            'tipo_documento.in' => 'O tipo de documento deve ser CNPJ ou CPF.',
            'cnpj.required' => 'O documento é obrigatório.',
            'cnpj.unique' => 'Este documento já está cadastrado.',
            'razao_social.required' => 'A Razão Social é obrigatória.',
            'razao_social.min' => $request->input('tipo_documento') === 'CNPJ' 
                ? 'Para CNPJ, a razão social deve ter pelo menos 3 caracteres.' 
                : 'A razão social é obrigatória.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ter um formato válido.',
            'telefone.required' => 'O telefone é obrigatório.',
            'telefone.min' => 'O telefone deve ter o formato (11) 99999-9999.',
            'endereco.cep.required' => 'O CEP é obrigatório.',
            'endereco.cep.size' => 'O CEP deve ter o formato 12345-678.',
            'endereco.logradouro.required' => 'O logradouro é obrigatório.',
            'endereco.numero.required' => 'O número é obrigatório.',
            'endereco.bairro.required' => 'O bairro é obrigatório.',
            'endereco.cidade.required' => 'A cidade é obrigatória.',
            'endereco.estado.required' => 'O estado é obrigatório.',
            'endereco.estado.size' => 'O estado deve ter exatamente 2 caracteres (ex: SP).',
        ];

        $validated = $request->validate($rules, $messages);

        try {
            DB::beginTransaction();

            // Create endereco first
            $endereco = Endereco::create([
                'cep' => $validated['endereco']['cep'],
                'logradouro' => $validated['endereco']['logradouro'],
                'numero' => $validated['endereco']['numero'],
                'complemento' => $validated['endereco']['complemento'],
                'bairro' => $validated['endereco']['bairro'],
                'cidade' => $validated['endereco']['cidade'],
                'estado' => $validated['endereco']['estado'],
            ]);

            // Create empresa
            Empresa::create([
                'tipo_documento' => $validated['tipo_documento'],
                'cnpj' => $validated['cnpj'],
                'razao_social' => $validated['razao_social'],
                'nome_fantasia' => $validated['nome_fantasia'],
                'email' => $validated['email'],
                'telefone' => $validated['telefone'],
                'ramo_atividade' => $validated['ramo_atividade'],
                'endereco_id' => $endereco->id,
            ]);

            DB::commit();

            return redirect()->route('empresas.index')
                ->with('success', 'Empresa cadastrada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erro ao cadastrar empresa: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Empresa $empresa): Response
    {
        $empresa->load(['endereco', 'anexos']);

        return Inertia::render('Empresas/Show', [
            'empresa' => $empresa
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Empresa $empresa): Response
    {
        $empresa->load('endereco');

        return Inertia::render('Empresas/Edit', [
            'empresa' => $empresa
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Empresa $empresa): RedirectResponse
    {
        $validated = $request->validate([
            'cnpj' => 'required|string|min:14|max:18|unique:empresas,cnpj,' . $empresa->id,
            'razao_social' => 'required|string|min:3|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'email' => 'required|email|max:150',
            'telefone' => 'required|string|min:10|max:15',
            'ramo_atividade' => 'required|string|max:100',
            // Endereco fields
            'endereco.cep' => 'required|string|min:8|max:9',
            'endereco.logradouro' => 'required|string|min:5|max:255',
            'endereco.numero' => 'required|string|min:1|max:10',
            'endereco.complemento' => 'nullable|string|max:100',
            'endereco.bairro' => 'required|string|min:2|max:100',
            'endereco.cidade' => 'required|string|min:2|max:100',
            'endereco.estado' => 'required|string|size:2',
        ]);

        try {
            DB::beginTransaction();

            // Update endereco
            $empresa->endereco->update([
                'cep' => $validated['endereco']['cep'],
                'logradouro' => $validated['endereco']['logradouro'],
                'numero' => $validated['endereco']['numero'],
                'complemento' => $validated['endereco']['complemento'],
                'bairro' => $validated['endereco']['bairro'],
                'cidade' => $validated['endereco']['cidade'],
                'estado' => $validated['endereco']['estado'],
            ]);

            // Update empresa
            $empresa->update([
                'cnpj' => $validated['cnpj'],
                'razao_social' => $validated['razao_social'],
                'nome_fantasia' => $validated['nome_fantasia'],
                'email' => $validated['email'],
                'telefone' => $validated['telefone'],
                'ramo_atividade' => $validated['ramo_atividade'],
            ]);

            DB::commit();

            return redirect()->route('empresas.index')
                ->with('Empresa atualizada');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erro ao atualizar empresa: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Empresa $empresa): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Load the endereco to delete it after empresa deletion
            $empresa->load('endereco');
            $endereco = $empresa->endereco;

            // Delete empresa first (this will cascade delete anexos)
            $empresa->delete();

            // Delete endereco explicitly if it exists
            if ($endereco) {
                $endereco->delete();
            }

            DB::commit();

            return redirect()->route('empresas.index')
                ->with('success', 'Empresa excluída com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erro ao excluir empresa: ' . $e->getMessage());
        }
    }
}
