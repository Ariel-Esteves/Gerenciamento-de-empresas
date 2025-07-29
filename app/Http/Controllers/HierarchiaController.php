<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Inertia\Inertia;
use Inertia\Response;

class HierarchiaController extends Controller
{
    /**
     * Display hierarchical view of companies by activity branch
     */
    public function index(): Response
    {
        // Buscar todas as empresas com seus endereços
        $empresas = Empresa::with('endereco')->get();
        
        // Organizar empresas por hierarquia de ramo de atividade
        $hierarchy = $this->organizeCompaniesHierarchy($empresas);
        
        return Inertia::render('Hierarquia/Index', [
            'hierarchy' => $hierarchy,
            'totalEmpresas' => $empresas->count(),
            'statistics' => $this->calculateStatistics($empresas)
        ]);
    }

    /**
     * Organize companies into hierarchical structure based on activity branch
     */
    private function organizeCompaniesHierarchy($empresas)
    {
        $hierarchy = [];

        foreach ($empresas as $empresa) {
            // Dividir o ramo de atividade em níveis usando " > " como separador
            $parts = explode(' > ', $empresa->ramo_atividade);
            
            // Se não tem separador, usar como nível único
            if (count($parts) === 1) {
                $mainCategory = trim($parts[0]);
                $subCategory = 'Geral';
            } else {
                $mainCategory = trim($parts[0]);
                $subCategory = trim($parts[1]);
            }

            // Inicializar categoria principal se não existir
            if (!isset($hierarchy[$mainCategory])) {
                $hierarchy[$mainCategory] = [
                    'name' => $mainCategory,
                    'subcategories' => [],
                    'totalCompanies' => 0,
                    'companies' => []
                ];
            }

            // Inicializar subcategoria se não existir
            if (!isset($hierarchy[$mainCategory]['subcategories'][$subCategory])) {
                $hierarchy[$mainCategory]['subcategories'][$subCategory] = [
                    'name' => $subCategory,
                    'companies' => [],
                    'totalCompanies' => 0
                ];
            }

            // Adicionar empresa à subcategoria
            $hierarchy[$mainCategory]['subcategories'][$subCategory]['companies'][] = [
                'id' => $empresa->id,
                'razao_social' => $empresa->razao_social,
                'nome_fantasia' => $empresa->nome_fantasia,
                'cnpj' => $empresa->cnpj,
                'email' => $empresa->email,
                'telefone' => $empresa->telefone,
                'cidade' => $empresa->endereco->cidade ?? 'N/A',
                'estado' => $empresa->endereco->estado ?? 'N/A'
            ];

            // Incrementar contadores
            $hierarchy[$mainCategory]['subcategories'][$subCategory]['totalCompanies']++;
            $hierarchy[$mainCategory]['totalCompanies']++;
        }

        // Ordenar por nome
        ksort($hierarchy);
        foreach ($hierarchy as &$category) {
            ksort($category['subcategories']);
        }

        return $hierarchy;
    }

    /**
     * Calculate statistics for the hierarchical view
     */
    private function calculateStatistics($empresas)
    {
        $states = [];
        $categories = [];
        $totalAnexos = 0;

        foreach ($empresas as $empresa) {
            // Estados
            $estado = $empresa->endereco->estado ?? 'N/A';
            $states[$estado] = ($states[$estado] ?? 0) + 1;

            // Categorias principais
            $parts = explode(' > ', $empresa->ramo_atividade);
            $mainCategory = trim($parts[0]);
            $categories[$mainCategory] = ($categories[$mainCategory] ?? 0) + 1;

            // Total de anexos (assumindo que existe uma relação)
            $totalAnexos += $empresa->anexos()->count();
        }

        return [
            'totalStates' => count($states),
            'topStates' => collect($states)->sortDesc()->take(5)->toArray(),
            'totalCategories' => count($categories),
            'topCategories' => collect($categories)->sortDesc()->take(5)->toArray(),
            'totalAnexos' => $totalAnexos
        ];
    }
}
