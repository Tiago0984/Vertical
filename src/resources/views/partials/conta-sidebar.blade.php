<div class="conta-sidebar">
    <div class="conta-sidebar-user">
        <i class="fa fa-user-circle-o"></i>
        <div>
            <strong>{{ auth()->user()->name }}</strong>
            <span>{{ auth()->user()->email }}</span>
        </div>
    </div>
    <ul class="conta-sidebar-nav">
        <li class="{{ request()->routeIs('conta.index') ? 'active' : '' }}">
            <a href="{{ route('conta.index') }}"><i class="fa fa-th-large"></i> Minha Conta</a>
        </li>
        <li class="{{ request()->routeIs('conta.dados-pessoais') ? 'active' : '' }}">
            <a href="{{ route('conta.dados-pessoais') }}"><i class="fa fa-id-card-o"></i> Dados Pessoais</a>
        </li>
        <li class="{{ request()->routeIs('conta.enderecos.*') ? 'active' : '' }}">
            <a href="{{ route('conta.enderecos.index') }}"><i class="fa fa-map-marker"></i> Endereços</a>
        </li>
        <li class="{{ request()->routeIs('conta.pedidos.*') ? 'active' : '' }}">
            <a href="{{ route('conta.pedidos.index') }}"><i class="fa fa-list-alt"></i> Histórico de Pedidos</a>
        </li>
        <li>
            <a href="{{ route('favoritos') }}"><i class="fa fa-heart-o"></i> Lista de Desejos</a>
        </li>
    </ul>
</div>
