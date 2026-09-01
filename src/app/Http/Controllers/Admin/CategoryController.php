<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\SlugGenerator;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categorias.index', [
            'categorias' => Category::withCount('products')->orderBy('nome')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.categorias.create', ['categoria' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validarCategoria($request);
        $data['slug'] = SlugGenerator::unique($data['nome'], Category::class);

        Category::create($data);

        return redirect()->route('admin.categorias.index')->with('status', 'Categoria criada com sucesso.');
    }

    public function edit(Category $categoria)
    {
        return view('admin.categorias.edit', ['categoria' => $categoria]);
    }

    public function update(Request $request, Category $categoria)
    {
        $data = $this->validarCategoria($request);
        $data['slug'] = SlugGenerator::unique($data['nome'], Category::class, $categoria->id);

        $categoria->update($data);

        return redirect()->route('admin.categorias.index')->with('status', 'Categoria atualizada com sucesso.');
    }

    public function destroy(Category $categoria)
    {
        $categoria->delete();

        return back()->with('status', 'Categoria removida.');
    }

    private function validarCategoria(Request $request): array
    {
        return $request->validate([
            'nome' => ['required', 'string', 'max:255'],
        ]);
    }
}
