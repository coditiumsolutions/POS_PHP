<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Home') - Coditium POS</title>
    <meta name="theme-color" content="#0f766e">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    @stack('styles')
</head>
<body>
@php
    use App\Services\NavigationCatalog;
    use Illuminate\Support\Facades\Route as RouteFacade;

    $user = Auth::user();
    $navKey = NavigationCatalog::resolveCurrentKey();
    $currentArea = NavigationCatalog::findArea($navKey);
    $topAreas = NavigationCatalog::topAreas();
@endphp

<div class="pos-shell">
    <div class="pos-header-bars no-print">
        <header class="pos-topbar-primary">
            <a class="pos-brand" href="{{ route('nav.clear') }}">
                <span>Coditium POS</span>
            </a>
            <div class="pos-user">
                <div class="pos-session-meta" title="Signed-in user session">
                    <span class="pos-session-item"><span class="pos-session-label">User</span> {{ $user->user_name ?? $user->full_name ?? '—' }}</span>
                    <span class="pos-session-item"><span class="pos-session-label">Terminal</span> {{ $user->terminal_name ?? '—' }}</span>
                    <span class="pos-session-item"><span class="pos-session-label">Branch</span> {{ $user->branch ?? '—' }}</span>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
        </header>

        <header class="pos-topbar-secondary">
            <nav class="pos-top-nav">
                <a class="pos-top-link {{ ($navKey === 'Dashboard') ? 'active' : '' }}"
                   href="{{ route('nav.clear') }}">Home</a>
                @foreach($topAreas as $area)
                    @php
                        $areaActive = $area['key'] === 'AllModules'
                            ? \App\Services\NavigationCatalog::isUnderAllModules($navKey)
                            : strcasecmp($navKey, $area['key']) === 0;
                    @endphp
                    @if($area['key'] === 'AllModules')
                        <div class="pos-top-dropdown {{ $areaActive ? 'active' : '' }}" data-top-dropdown>
                            <div class="pos-top-dropdown-trigger">
                                <a class="pos-top-link {{ $areaActive ? 'active' : '' }}"
                                   href="{{ route('nav.select', ['key' => $area['key']]) }}">{{ $area['title'] }}</a>
                                <button type="button" class="pos-top-twistee" aria-expanded="false"
                                        aria-label="Expand {{ $area['title'] }}" data-top-twistee>
                                    <i class="bi bi-caret-right-fill" aria-hidden="true"></i>
                                </button>
                            </div>
                            <div class="pos-top-dropdown-menu" hidden>
                                @foreach($area['links'] as $link)
                                    @if(!empty($link['route']))
                                        <a class="pos-top-dropdown-item"
                                           href="{{ route('nav.select', ['key' => 'AllModules', 'to' => $link['route']]) }}">{{ $link['title'] }}</a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a class="pos-top-link {{ $areaActive ? 'active' : '' }}"
                           href="{{ route('nav.select', ['key' => $area['key']]) }}">{{ $area['title'] }}</a>
                    @endif
                @endforeach
            </nav>
        </header>
        <div class="pos-gold-bar" aria-hidden="true"></div>
    </div>

    <div class="pos-body">
        <aside class="pos-sidebar" id="posSidebar">
            <div class="pos-sidebar-toggle-row">
                <button type="button" class="pos-sidebar-toggle" id="posSidebarToggle"
                        aria-label="Collapse sidebar" title="Collapse sidebar" aria-expanded="true">
                    <i class="bi bi-list" aria-hidden="true"></i>
                </button>
            </div>
            <nav class="pos-nav" id="posSidebarNav">
                <div class="pos-nav-label">{{ $currentArea['title'] ?? 'Dashboard' }}</div>

                @foreach(($currentArea['links'] ?? []) as $link)
                    @if(!empty($link['children']))
                        @php
                            $groupActive = collect($link['children'])->contains(function ($child) {
                                return !empty($child['route']) && request()->routeIs($child['route']);
                            });
                            $groupGold = strcasecmp($link['title'] ?? '', 'Categories') === 0;
                        @endphp
                        <div class="pos-nav-group open {{ $groupActive ? 'active' : '' }}" data-side-nav-group>
                            <button type="button"
                                    class="pos-nav-link pos-nav-group-toggle {{ $groupGold ? 'pos-nav-gold' : '' }}"
                                    aria-expanded="true" data-side-nav-twistee>
                                @if(!empty($link['icon']))
                                    <i class="bi {{ $link['icon'] }}" aria-hidden="true"></i>
                                @endif
                                <span>{{ $link['title'] }}</span>
                                <i class="bi bi-caret-right-fill pos-nav-group-caret" aria-hidden="true"></i>
                            </button>
                            <div class="pos-nav-group-menu">
                                @foreach($link['children'] as $child)
                                    @if(empty($child['route']))
                                        @continue
                                    @endif
                                    <a class="pos-nav-link pos-nav-sublink {{ request()->routeIs($child['route']) ? 'active' : '' }}"
                                       href="{{ route($child['route']) }}">
                                        <span>{{ $child['title'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @elseif(!empty($link['route']))
                        <a class="pos-nav-link {{ request()->routeIs($link['route']) ? 'active' : '' }}"
                           href="{{ route($link['route']) }}">
                            <i class="bi {{ $link['icon'] ?? 'bi-circle' }}" aria-hidden="true"></i>
                            <span>{{ $link['title'] }}</span>
                        </a>
                    @endif
                @endforeach

                <div class="pos-nav-permanent">
                    @if(RouteFacade::has('modules.index'))
                        <a class="pos-nav-link pos-nav-accent {{ request()->routeIs('modules.*') ? 'active' : '' }}"
                           href="{{ route('modules.index') }}">
                            <i class="bi bi-grid-3x3-gap" aria-hidden="true"></i>
                            <span>Modules</span>
                        </a>
                    @endif
                    @if(RouteFacade::has('setup.index'))
                        <a class="pos-nav-link pos-nav-accent {{ request()->routeIs('setup.*') ? 'active' : '' }}"
                           href="{{ route('setup.index') }}">
                            <i class="bi bi-gear" aria-hidden="true"></i>
                            <span>Setup</span>
                        </a>
                    @endif
                </div>
            </nav>
        </aside>
        <div class="pos-backdrop" id="posBackdrop"></div>

        <div class="pos-main">
            <main class="pos-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show no-print" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show no-print" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="{{ asset('js/site.js') }}"></script>
@stack('scripts')
</body>
</html>
