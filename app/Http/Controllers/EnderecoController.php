<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use Illuminate\Http\Request;

class EnderecoController extends Controller
{
    public function index()
    {
        $enderecos = Endereco::all();
        return response()->json($enderecos);
    }

    public function create()
    {
        // Return a view for creating a new endereco (if using Blade)
        return view('enderecos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rua' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            // Add other fields and validation rules as needed
        ]);

        $endereco = Endereco::create($validated);
        return response()->json($endereco, 201);
    }

    public function show($id)
    {
        $endereco = Endereco::findOrFail($id);
        return response()->json($endereco);
    }

    public function edit($id)
    {
        $endereco = Endereco::findOrFail($id);
        // Return a view for editing (if using Blade)
        return view('enderecos.edit', compact('endereco'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'rua' => 'sometimes|required|string|max:255',
            'cidade' => 'sometimes|required|string|max:255',
            // Add other fields and validation rules as needed
        ]);

        $endereco = Endereco::findOrFail($id);
        $endereco->update($validated);

        return response()->json($endereco);
    }

    public function destroy($id)
    {
        $endereco = Endereco::findOrFail($id);
        $endereco->delete();

        return response()->json(null, 204);
    }
}
