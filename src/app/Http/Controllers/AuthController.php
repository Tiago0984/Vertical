<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('site.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [], [
            'email' => 'e-mail',
            'password' => 'senha',
        ]);

        $lembrar = $request->boolean('lembrar');

        if (! Auth::attempt($credentials, $lembrar)) {
            throw ValidationException::withMessages([
                'email' => 'E-mail ou senha incorretos.',
            ]);
        }

        $request->session()->regenerate();
        $request->session()->flash('just_logged_in', true);

        return redirect()->intended(route('home'));
    }

    public function showRegister()
    {
        return view('site.auth.cadastro');
    }

    public function register(Request $request)
    {
        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sobrenome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [], [
            'name' => 'nome',
            'sobrenome' => 'sobrenome',
            'email' => 'e-mail',
            'password' => 'senha',
        ]);

        $user = User::create([
            'name' => $dados['name'],
            'sobrenome' => $dados['sobrenome'],
            'email' => $dados['email'],
            'password' => $dados['password'],
        ]);

        Auth::login($user);

        $request->session()->regenerate();
        $request->session()->flash('just_logged_in', true);

        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
