<x-filament-widgets::widget class="fi-wi-site-sections-overview">
    <div class="grid gap-6">
        <section class="codisec-dashboard-hero overflow-hidden rounded-3xl px-6 py-7 text-white sm:px-8 sm:py-9">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-cyan-50">
                        <span class="size-2 rounded-full bg-amber-300"></span>
                        Gestión del sitio público
                    </div>
                    <h1 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">Una sección, un módulo de contenido</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-cyan-50 sm:text-base">La organización replica las diez secciones del menú público. Entra al módulo que deseas actualizar y el contenido se guardará en su área correspondiente.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <span class="rounded-full bg-white/15 px-4 py-2 text-sm font-semibold ring-1 ring-white/25">10 módulos</span>
                    <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-teal-800 shadow-sm transition hover:-translate-y-0.5 hover:bg-cyan-50">
                        Ver sitio público
                        <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" class="size-4" />
                    </a>
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-cyan-100 bg-white p-4 shadow-sm sm:p-6">
            <div class="flex flex-col gap-2 border-b border-cyan-100 pb-5 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-teal-600">Igual que en el menú público</p>
                    <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Secciones del sitio</h2>
                </div>
                <p class="max-w-xl text-sm leading-6 text-slate-500">Usa “Administrar” para revisar lo existente o “Agregar contenido” para publicar nueva información dentro de ese módulo.</p>
            </div>

            <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($sections as $section)
                    <article
                        data-content-module="{{ $section['slug'] }}"
                        data-tone="{{ $section['accent'] }}"
                        class="codisec-module-card group flex min-h-64 flex-col overflow-hidden rounded-2xl border bg-white p-5 transition duration-200 hover:-translate-y-1 hover:shadow-lg"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <span class="codisec-module-icon grid size-11 place-items-center rounded-xl">
                                    <x-filament::icon :icon="$section['icon']" class="size-5" />
                                </span>
                                <span class="text-xs font-bold tracking-[0.16em] text-slate-400">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <span class="rounded-full bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600 ring-1 ring-slate-200">
                                {{ $section['count'] }} {{ $section['countLabel'] }}
                            </span>
                        </div>

                        <h3 class="mt-5 text-lg font-bold leading-6 text-slate-900">{{ $section['title'] }}</h3>
                        <p class="mt-2 grow text-sm leading-6 text-slate-500">{{ $section['description'] }}</p>

                        <div class="mt-5 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">
                            <a href="{{ $section['manageUrl'] }}" class="codisec-module-primary-action inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-bold transition">
                                <x-filament::icon icon="heroicon-o-pencil-square" class="size-4" />
                                Administrar
                            </a>
                            @if ($section['createUrl'])
                                <a href="{{ $section['createUrl'] }}" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">
                                    <x-filament::icon icon="heroicon-o-plus" class="size-4" />
                                    Agregar contenido
                                </a>
                            @endif
                            @if ($section['publicUrl'])
                                <a href="{{ $section['publicUrl'] }}" target="_blank" class="ml-auto grid size-9 place-items-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" aria-label="Ver {{ $section['title'] }} en el sitio público">
                                    <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" class="size-4" />
                                </a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
</x-filament-widgets::widget>
