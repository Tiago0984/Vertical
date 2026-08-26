<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        return view('site.conta.enderecos.index', [
            'enderecos' => $request->user()->addresses()->orderByDesc('is_padrao')->latest()->get(),
        ]);
    }

    public function create()
    {
        return view('site.conta.enderecos.form', ['endereco' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validarEndereco($request);

        $endereco = $request->user()->addresses()->create($data);

        if ($request->boolean('is_padrao')) {
            $this->definirComoPadrao($request->user(), $endereco);
        }

        return redirect()->route('conta.enderecos.index')->with('status', 'Endereço adicionado com sucesso.');
    }

    public function edit(Request $request, Address $endereco)
    {
        abort_unless($endereco->user_id === $request->user()->id, 403);

        return view('site.conta.enderecos.form', ['endereco' => $endereco]);
    }

    public function update(Request $request, Address $endereco)
    {
        abort_unless($endereco->user_id === $request->user()->id, 403);

        $data = $this->validarEndereco($request);
        $endereco->update($data);

        if ($request->boolean('is_padrao')) {
            $this->definirComoPadrao($request->user(), $endereco);
        }

        return redirect()->route('conta.enderecos.index')->with('status', 'Endereço atualizado com sucesso.');
    }

    public function destroy(Request $request, Address $endereco)
    {
        abort_unless($endereco->user_id === $request->user()->id, 403);

        $endereco->delete();

        return back()->with('status', 'Endereço removido.');
    }

    public function definirPadrao(Request $request, Address $endereco)
    {
        abort_unless($endereco->user_id === $request->user()->id, 403);

        $this->definirComoPadrao($request->user(), $endereco);

        return back()->with('status', 'Endereço definido como padrão.');
    }

    private function definirComoPadrao($user, Address $endereco): void
    {
        $user->addresses()->where('id', '!=', $endereco->id)->update(['is_padrao' => false]);
        $endereco->update(['is_padrao' => true]);
    }

    private function validarEndereco(Request $request): array
    {
        return $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'sobrenome' => ['required', 'string', 'max:255'],
            'cep' => ['required', 'string', 'max:9'],
            'endereco' => ['required', 'string', 'max:255'],
            'numero' => ['required', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:255'],
            'bairro' => ['required', 'string', 'max:255'],
            'cidade' => ['required', 'string', 'max:255'],
            'estado' => ['required', 'string', 'size:2'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'referencia' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
