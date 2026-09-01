<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Support\SlugGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $produtos = Product::with('category')
            ->when($request->filled('busca'), fn ($query) => $query->where('nome', 'like', '%'.$request->input('busca').'%'))
            ->when($request->filled('categoria_id'), fn ($query) => $query->where('category_id', $request->input('categoria_id')))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.produtos.index', [
            'produtos' => $produtos,
            'categorias' => Category::orderBy('nome')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.produtos.create', [
            'produto' => null,
            'categorias' => Category::orderBy('nome')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validarProduto($request);
        $data['slug'] = SlugGenerator::unique($data['nome'], Product::class);
        $data['imagem'] = $this->armazenarImagem($request);

        Product::create($data);

        return redirect()->route('admin.produtos.index')->with('status', 'Produto criado com sucesso.');
    }

    public function edit(Product $produto)
    {
        return view('admin.produtos.edit', [
            'produto' => $produto,
            'categorias' => Category::orderBy('nome')->get(),
        ]);
    }

    public function update(Request $request, Product $produto)
    {
        $data = $this->validarProduto($request, $produto->id);
        $data['slug'] = SlugGenerator::unique($data['nome'], Product::class, $produto->id);

        if ($request->hasFile('imagem')) {
            $this->apagarImagem($produto->imagem);
            $data['imagem'] = $this->armazenarImagem($request);
        }

        $produto->update($data);

        return redirect()->route('admin.produtos.index')->with('status', 'Produto atualizado com sucesso.');
    }

    public function destroy(Product $produto)
    {
        $favoritos = $produto->favorites()->count();
        $carrinhos = $produto->cartItems()->count();

        $this->apagarImagem($produto->imagem);
        $produto->delete();

        $aviso = 'Produto removido.';
        if ($favoritos > 0 || $carrinhos > 0) {
            $aviso .= " Ele também foi removido de {$favoritos} lista(s) de favoritos e {$carrinhos} carrinho(s) ativos.";
        }

        return back()->with('status', $aviso);
    }

    private function validarProduto(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'preco' => ['required', 'numeric', 'min:0'],
            'preco_promocional' => [
                Rule::requiredIf($request->boolean('is_promocao')),
                'nullable', 'numeric', 'min:0', 'lt:preco',
            ],
            'imagem' => [$ignoreId ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $data['is_novo'] = $request->boolean('is_novo');
        $data['is_promocao'] = $request->boolean('is_promocao');

        return $data;
    }

    private function armazenarImagem(Request $request): string
    {
        $arquivo = $request->file('imagem');
        $nomeArquivo = Str::slug($request->input('nome')).'-'.Str::random(8).'.'.$arquivo->getClientOriginalExtension();

        $arquivo->move(public_path('vertical/images'), $nomeArquivo);

        return 'vertical/images/'.$nomeArquivo;
    }

    private function apagarImagem(?string $caminhoRelativo): void
    {
        if (! $caminhoRelativo) {
            return;
        }

        $caminhoAbsoluto = public_path($caminhoRelativo);

        if (File::exists($caminhoAbsoluto)) {
            File::delete($caminhoAbsoluto);
        }
    }
}
