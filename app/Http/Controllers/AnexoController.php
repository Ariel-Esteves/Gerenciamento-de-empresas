<?php

namespace App\Http\Controllers;

use App\Models\Anexo;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class anexoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Anexo::with('empresa');

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('descricao', 'like', "%{$search}%")
                  ->orWhere('nome_arquivo', 'like', "%{$search}%")
                  ->orWhereHas('empresa', function($empresaQuery) use ($search) {
                      $empresaQuery->where('razao_social', 'like', "%{$search}%")
                                   ->orWhere('nome_fantasia', 'like', "%{$search}%");
                  });
            });
        }

        // Apply tipo filter
        if ($request->has('tipo') && !empty($request->tipo)) {
            $query->where('tipo_anexo', $request->tipo);
        }

        // Apply empresa filter
        if ($request->has('empresa_id') && !empty($request->empresa_id)) {
            $query->where('empresa_id', $request->empresa_id);
        }

        $anexos = $query->orderBy('created_at', 'desc')
                       ->paginate(10)
                       ->appends($request->query());

        // Get all empresas for filter dropdown
        $empresas = Empresa::select('id', 'razao_social', 'nome_fantasia')
            ->orderBy('razao_social')
            ->get();

        return Inertia::render('Anexos/Index', [
            'anexos' => $anexos,
            'filters' => $request->only(['search', 'tipo', 'empresa_id']),
            'empresas' => $empresas
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $empresas = Empresa::select('id', 'razao_social', 'nome_fantasia')
            ->orderBy('razao_social')
            ->get();

        return Inertia::render('Anexos/Create', [
            'empresas' => $empresas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'arquivo' => 'required|file|max:10240', // 10MB max
            'empresa_id' => 'required|exists:empresas,id',
            'tipo_anexo' => 'required|in:contrato,documento,imagem,outro',
            'descricao' => 'nullable|string|max:255'
        ], [
            'arquivo.required' => 'Por favor, selecione um arquivo.',
            'arquivo.max' => 'O arquivo não pode ser maior que 10MB.',
            'empresa_id.required' => 'Por favor, selecione uma empresa.',
            'empresa_id.exists' => 'A empresa selecionada não existe.',
            'tipo_anexo.required' => 'Por favor, selecione o tipo do anexo.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $file = $request->file('arquivo');
            $empresa = Empresa::findOrFail($request->empresa_id);
            
            // Generate unique filename
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Store file in storage/app/public/anexos/{empresa_id}/
            $filePath = $file->storeAs(
                'anexos/' . $empresa->id, 
                $filename, 
                'public'
            );

            // Create anexo record
            Anexo::create([
                'nome_arquivo' => $file->getClientOriginalName(),
                'caminho_arquivo' => $filePath,
                'tipo_mime' => $file->getMimeType(),
                'tamanho' => $file->getSize(),
                'tipo_anexo' => $request->tipo_anexo,
                'empresa_id' => $request->empresa_id,
            ]);

            return redirect()->route('anexos.index')
                ->with('success', 'Anexo enviado com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao enviar arquivo: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Anexo $anexo): Response
    {
        $anexo->load('empresa.endereco');

        return Inertia::render('Anexos/Show', [
            'anexo' => $anexo
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Anexo $anexo): Response
    {
        $empresas = Empresa::select('id', 'razao_social', 'nome_fantasia')
            ->orderBy('razao_social')
            ->get();

        return Inertia::render('Anexos/Edit', [
            'anexo' => $anexo,
            'empresas' => $empresas
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Anexo $anexo): RedirectResponse
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'arquivo' => 'nullable|file|max:10240', // Optional file upload
            'empresa_id' => 'required|exists:empresas,id',
            'tipo_anexo' => 'required|in:contrato,documento,imagem,outro',
        ], [
            'arquivo.max' => 'O arquivo não pode ser maior que 10MB.',
            'empresa_id.required' => 'Por favor, selecione uma empresa.',
            'tipo_anexo.required' => 'Por favor, selecione o tipo do anexo.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $updateData = [
                'empresa_id' => $request->empresa_id,
                'tipo_anexo' => $request->tipo_anexo,
            ];

            // If new file is uploaded, replace the old one
            if ($request->hasFile('arquivo')) {
                // Delete old file
                if (Storage::disk('public')->exists($anexo->caminho_arquivo)) {
                    Storage::disk('public')->delete($anexo->caminho_arquivo);
                }

                $file = $request->file('arquivo');
                $empresa = Empresa::findOrFail($request->empresa_id);
                
                // Generate unique filename
                $filename = time() . '_' . $file->getClientOriginalName();
                
                // Store new file
                $filePath = $file->storeAs(
                    'anexos/' . $empresa->id, 
                    $filename, 
                    'public'
                );

                $updateData = array_merge($updateData, [
                    'nome_arquivo' => $file->getClientOriginalName(),
                    'caminho_arquivo' => $filePath,
                    'tipo_mime' => $file->getMimeType(),
                    'tamanho' => $file->getSize(),
                ]);
            }

            $anexo->update($updateData);

            return redirect()->route('anexos.index')
                ->with('success', 'Anexo atualizado com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar anexo: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Anexo $anexo): RedirectResponse
    {
        try {
            // Delete file from storage
            if (Storage::disk('public')->exists($anexo->caminho_arquivo)) {
                Storage::disk('public')->delete($anexo->caminho_arquivo);
            }

            // Delete record from database
            $anexo->delete();

            return redirect()->route('anexos.index')
                ->with('success', 'Anexo excluído com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erro ao excluir anexo: ' . $e->getMessage());
        }
    }

    /**
     * Download the specified anexo file.
     */
    public function download(Anexo $anexo)
    {
        if (!Storage::disk('public')->exists($anexo->caminho_arquivo)) {
            return redirect()->back()
                ->with('error', 'Arquivo não encontrado.');
        }

        return Storage::disk('public')->download(
            $anexo->caminho_arquivo, 
            $anexo->nome_arquivo
        );
    }

    /**
     * Get anexos by empresa (API endpoint).
     */
    public function byEmpresa(Empresa $empresa)
    {
        $anexos = $empresa->anexos()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'anexos' => $anexos
        ]);
    }
}
