@php
    $role_labels = [
        'customer' => 'Selamatkan Makanan',
        'partner'  => 'Mitra Dashboard',
        'agent'    => 'Agen Dashboard',
        'founder'  => 'Founder Dashboard',
    ];

    $current_role = $current_role ?? 'customer';
    $active_menu  = $active_menu ?? null;
@endphp

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">Hungerswitch</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link {{ ($active_menu ?? '') == 'home' ? 'active' : '' }}"
                       href="{{ route('home') }}">
                        Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ ($active_menu ?? '') == 'marketplace' ? 'active' : '' }}"
                       href="{{ route('marketplace') }}">
                        Marketplace
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ ($active_menu ?? '') == 'donasi' ? 'active' : '' }}"
                       href="{{ route('donasi') }}">
                        Donasi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ ($active_menu ?? '') == 'login' ? 'active' : '' }}"
                       href="{{ route('login') }}">
                        Login
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>


<style>
.navbar-nav .nav-link {
    font-weight: 500;
    color: #333;
    margin: 0 0.5rem;
    position: relative;
    transition: color 0.3s ease;
}

.navbar-nav .nav-link::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 3px;
    background: #D9232D;
    transition: width 0.3s ease;
}

.navbar-nav .nav-link:hover {
    color: #D9232D;
}

.navbar-nav .nav-link:hover::after {
    width: 80%;
}

.navbar-nav .nav-link.active {
    color: #D9232D;
}

.navbar-nav .nav-link.active::after {
    width: 80%;
}
</style>