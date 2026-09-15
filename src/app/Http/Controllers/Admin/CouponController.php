<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index()
    {
        return view('admin.cupons.index', [
            'cupons' => Coupon::orderByDesc('id')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.cupons.create', ['cupom' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validarCupom($request);
        $data['ativo'] = $request->boolean('ativo');

        Coupon::create($data);

        return redirect()->route('admin.cupons.index')->with('status', 'Cupom criado com sucesso.');
    }

    public function edit(Coupon $cupom)
    {
        return view('admin.cupons.edit', ['cupom' => $cupom]);
    }

    public function update(Request $request, Coupon $cupom)
    {
        $data = $this->validarCupom($request, $cupom);
        $data['ativo'] = $request->boolean('ativo');

        // codigo não pode mudar depois de usado -- o histórico dos pedidos
        // antigos guarda o CÓDIGO como string congelada (orders.cupom), e
        // trocar o que esse código significa quebraria essa rastreabilidade.
        // O campo já vem readonly no form; isso é o cinto de segurança do
        // lado do servidor pra quem tentar contornar via POST direto.
        if ($cupom->usos > 0) {
            $data['codigo'] = $cupom->codigo;
        }

        $cupom->update($data);

        return redirect()->route('admin.cupons.index')->with('status', 'Cupom atualizado com sucesso.');
    }

    public function toggleAtivo(Coupon $cupom)
    {
        $cupom->update(['ativo' => ! $cupom->ativo]);

        return back()->with('status', $cupom->ativo ? 'Cupom ativado.' : 'Cupom desativado.');
    }

    public function destroy(Coupon $cupom)
    {
        // Excluir só com usos = 0 -- um cupom já usado some do desempenho
        // por cupom (fase 3) se for apagado; desativar preserva o vínculo
        // com os pedidos que já o usaram.
        if ($cupom->usos > 0) {
            return back()->withErrors([
                'cupom' => 'Cupons já usados não podem ser excluídos -- desative em vez de apagar, senão o desempenho por cupom perde o vínculo com os pedidos antigos.',
            ]);
        }

        $cupom->delete();

        return back()->with('status', 'Cupom removido.');
    }

    private function validarCupom(Request $request, ?Coupon $cupom = null): array
    {
        // Normaliza ANTES de checar unicidade -- não depender da collation
        // do banco ser case-insensitive pra pegar 'abc' colidindo com 'ABC'.
        // O model normaliza de novo ao salvar; isso aqui é só pra a
        // validação de unicidade enxergar a forma final.
        if ($request->filled('codigo')) {
            $request->merge(['codigo' => Str::upper(trim($request->input('codigo')))]);
        }

        return $request->validate([
            'codigo' => [
                'required', 'string', 'max:50',
                Rule::unique('coupons', 'codigo')->ignore($cupom?->id),
            ],
            'tipo' => ['required', Rule::in(Coupon::TIPOS)],
            'valor' => [
                'required', 'numeric', 'min:0.01',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('tipo') === Coupon::TIPO_PERCENTUAL && ((float) $value < 1 || (float) $value > 100)) {
                        $fail('Para cupom percentual, o valor deve ser entre 1 e 100.');
                    }
                },
            ],
            'inicio_em' => ['nullable', 'date'],
            'fim_em' => ['nullable', 'date', 'after_or_equal:inicio_em'],
            'minimo_compra' => ['nullable', 'numeric', 'min:0'],
            'uso_maximo' => ['nullable', 'integer', 'min:1'],
        ]);
    }
}
