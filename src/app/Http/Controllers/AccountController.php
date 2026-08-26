<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return view('site.conta.index', [
            'ultimoPedido' => $user->orders()->latest()->first(),
            'totalPedidos' => $user->orders()->count(),
            'totalEnderecos' => $user->addresses()->count(),
            'totalFavoritos' => $user->favorites()->count(),
        ]);
    }

    public function dadosPessoais(Request $request)
    {
        return view('site.conta.dados-pessoais', ['user' => $request->user()]);
    }

    public function atualizarDadosPessoais(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sobrenome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'telefone' => ['nullable', 'string', 'max:20'],
            'data_nascimento' => ['nullable', 'date'],
            'newsletter_email' => ['nullable', 'boolean'],
            'newsletter_sms' => ['nullable', 'boolean'],
        ], [], [
            'name' => 'nome',
        ]);

        $data['newsletter_email'] = $request->boolean('newsletter_email');
        $data['newsletter_sms'] = $request->boolean('newsletter_sms');

        $user->update($data);

        return back()->with('status', 'Dados atualizados com sucesso.');
    }

    public function atualizarSenha(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'senha_atual' => ['required'],
            'senha' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($data['senha_atual'], $user->password)) {
            return back()->withErrors(['senha_atual' => 'Senha atual incorreta.']);
        }

        $user->update(['password' => $data['senha']]);

        return back()->with('status', 'Senha alterada com sucesso.');
    }
}
