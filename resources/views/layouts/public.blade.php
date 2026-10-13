<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $siteSettings->get('site_tagline', 'Información pública del Comité Distrital de Seguridad Ciudadana de Surquillo') }}">
    <link rel="icon" href="{{ asset('storage/legacy/2016/07/favicon1.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <title>@yield('title', $siteSettings->get('site_name', 'CODISEC Surquillo'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="public-site min-h-screen antialiased">
    @php
        $homeNavigation = $navigation->first(fn ($item) => $item->url === '/' || mb_strtolower(trim($item->label)) === 'inicio');
        $submenuNavigation = $homeNavigation
            ? $navigation->where('parent_id', $homeNavigation->id)
            : $navigation->filter(fn ($item) => in_array($item->url, ['/seccion/codisec', '/seccion/normas-legales'], true));
        $mainNavigation = $navigation
            ->whereNull('parent_id')
            ->reject(fn ($item) => $item->url === '/' || mb_strtolower(trim($item->label)) === 'inicio');
        $secondaryNavigation = collect([
            ['label' => 'Boletines Estadísticos', 'url' => '/seccion/boletines-estadisticos', 'target' => '_self'],
            ['label' => 'Informes Trimestrales de Seguimiento', 'url' => '/seccion/informes-trimestrales-de-seguimiento', 'target' => '_self'],
            ['label' => 'Informes Trimestrales de Cumplimiento', 'url' => '/seccion/informes-trimestrales-de-cumplimiento', 'target' => '_self'],
        ]);
        $sectionNavigation = $navigation
            ->reject(fn ($item) => $item->url === '/' || mb_strtolower(trim($item->label)) === 'inicio')
            ->map(fn ($item) => ['label' => $item->label, 'url' => $item->url, 'target' => $item->target])
            ->concat($secondaryNavigation)
            ->unique('url')
            ->values();
    @endphp

    <header class="public-header">
        <div class="public-header-inner">
            <a href="{{ route('home') }}" class="public-logo" aria-label="{{ $siteSettings->get('municipality', 'Municipalidad Distrital de Surquillo') }} - {{ $siteSettings->get('site_name', 'CODISEC Surquillo') }}">
                <img src="{{ asset('storage/legacy/2016/07/mdslogo111c1.png') }}" alt="{{ $siteSettings->get('municipality', 'Municipalidad Distrital de Surquillo') }}">
            </a>

            <div class="public-tools">
                <a href="{{ url('/admin') }}" class="maintenance-link">Acceso de mantenimiento</a>
                <form action="{{ route('documents.index') }}" method="get" class="public-search" role="search">
                    <label class="sr-only" for="site-search">Buscar documentos</label>
                    <input id="site-search" name="q" value="{{ request('q') }}" placeholder="Buscar...">
                    <button type="submit" aria-label="Buscar">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                    </button>
                </form>
            </div>

            <button data-menu-button class="public-menu-button" aria-expanded="false" aria-label="Abrir menú">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <span>Menú</span>
            </button>

            <nav class="public-navigation" aria-label="Navegación principal">
                <ul>
                    <li class="has-submenu">
                        <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home')])>Inicio <span aria-hidden="true">⌄</span></a>
                        @if($submenuNavigation->isNotEmpty())
                            <ul class="public-submenu">
                                @foreach($submenuNavigation as $item)
                                    <li><a href="{{ $item->url }}" target="{{ $item->target }}">{{ $item->label }}</a></li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                    @foreach($mainNavigation as $item)
                        <li><a href="{{ $item->url }}" target="{{ $item->target }}" @class(['active' => request()->getPathInfo() === $item->url])>{{ $item->label }}</a></li>
                    @endforeach
                </ul>
            </nav>
        </div>

        <nav data-mobile-menu class="public-mobile-navigation hidden" aria-label="Navegación móvil">
            <a href="{{ route('home') }}">Inicio</a>
            @foreach($sectionNavigation as $item)
                <a href="{{ $item['url'] }}" target="{{ $item['target'] }}">{{ $item['label'] }}</a>
            @endforeach
            <a href="{{ route('documents.index') }}">Documentos</a>
            <a href="{{ route('contact.create') }}">Contacto</a>
        </nav>
    </header>

    <main class="public-core">
        <div class="public-container public-layout">
            <aside class="public-sidebar" aria-label="Menú de secciones">
                <div class="sidebar-widget">
                    <h2><span class="sidebar-menu-icon" aria-hidden="true">☰</span> Inicio</h2>
                    <nav>
                        @foreach($sectionNavigation as $item)
                            <a href="{{ $item['url'] }}" target="{{ $item['target'] }}" @class(['active' => request()->getPathInfo() === $item['url'], 'sidebar-child' => $item['url'] === '/seccion/normas-legales'])>{{ $item['label'] }}</a>
                        @endforeach
                    </nav>
                </div>
                @yield('sidebar_after')
            </aside>

            <div class="public-main">
                @hasSection('page_heading')
                    <header class="public-page-heading">
                        <h1>@yield('page_heading')</h1>
                        <div class="heading-rule"></div>
                        <nav aria-label="Migas de pan">
                            <a href="{{ route('home') }}">Inicio</a>
                            @hasSection('page_parent')<span>/</span><span>@yield('page_parent')</span>@endif
                            <span>/</span><strong>@yield('page_heading')</strong>
                        </nav>
                    </header>
                @endif
                @yield('content')
            </div>
        </div>
    </main>

    <footer class="public-footer">
        <div class="public-container footer-columns">
            <section class="footer-about">
                <h2>{{ $siteSettings->get('site_name', 'CODISEC Surquillo') }}</h2>
                <p>El Comité Distrital de Seguridad Ciudadana (CODISEC) es una instancia de diálogo, coordinación y elaboración de políticas, planes, programas, directivas y actividades vinculadas a la seguridad ciudadana en el ámbito distrital.</p>
            </section>

            <section>
                <h2 class="footer-heading">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M7 3H4a1 1 0 0 0-1 1c0 9.39 7.61 17 17 17a1 1 0 0 0 1-1v-3l-4-2-2 2c-3.5-1.5-6.5-4.5-8-8l2-2-2-4Z" /></svg>
                    Teléfonos
                </h2>
                <dl class="footer-phones">
                    <div><dt>Central Telefónica<br>Municipalidad de Surquillo</dt><dd>(01) 241-0413</dd></div>
                    <div><dt>Seguridad Ciudadana</dt><dd>(01) 510-2062</dd></div>
                    <div><dt>Serenazgo de Surquillo</dt><dd>*7000</dd></div>
                </dl>
            </section>

            <section>
                <h2 class="footer-heading">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="9" /><path d="M15.5 9.5a4 4 0 1 0 0 5V8.7 3.3 0 1 0 2.1 5.9" /></svg>
                    Únete a nuestra lista de correo
                </h2>
                <p>Recibe las últimas noticias sobre seguridad ciudadana de nuestro amado distrito.</p>
                <form action="{{ route('contact.create') }}" method="get" class="footer-subscribe">
                    <label for="footer-email" class="sr-only">Tu correo electrónico</label>
                    <input id="footer-email" name="email" type="email" placeholder="Tu correo electrónico">
                    <button type="submit" aria-label="Ir a contacto">›</button>
                </form>
            </section>

            <section>
                <h2 class="footer-heading">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="1" /><path d="m4 7 8 6 8-6" /></svg>
                    Dirección de la Municipalidad
                </h2>
                <address>Jr. Manuel Iribarren 155<br>Surquillo<br>Lima 34</address>
                <p>Central Telefónica: 241-0413</p>
            </section>
        </div>

        <div class="footer-bottom">
            <div class="public-container">
                <p>© {{ date('Y') }} CODISEC</p>
                <nav aria-label="Enlaces del pie"><a href="{{ route('documents.index') }}">Documentos</a><a href="{{ route('contact.create') }}">Contacto</a><a href="{{ url('/admin') }}">Mantenimiento</a></nav>
            </div>
        </div>
    </footer>
</body>
</html>
