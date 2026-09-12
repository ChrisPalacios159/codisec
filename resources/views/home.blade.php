@extends('layouts.public')

@section('title', 'CODISEC Surquillo | Seguridad Ciudadana')

@section('content')
    <section class="home-intro">
        <span class="section-kicker text-teal-200">Comité Distrital de Seguridad Ciudadana</span>
        <h1 class="mt-3">Un Surquillo más seguro, informado y organizado.</h1>
        <p>Consulta planes, actas, evaluaciones, informes y actividades oficiales del CODISEC.</p>
        <div class="home-actions">
            <a href="{{ route('documents.index') }}">Explorar documentos</a>
            <a href="{{ route('activities.index') }}">Ver actividades</a>
        </div>
    </section>

    <section class="public-content-card public-section">
        <span class="section-kicker">Acceso directo</span>
        <h2 class="section-title">Información por sección</h2>
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($categories->take(6) as $category)
                <a href="{{ route('documents.index', ['categoria' => $category->slug]) }}" class="border border-slate-200 bg-slate-50 p-4 no-underline transition hover:border-cod-teal hover:bg-white">
                    <span class="text-xs font-bold uppercase text-cod-teal">{{ $category->documents_count }} documentos</span>
                    <h3 class="mt-1 font-extrabold text-cod-navy">{{ $category->name }}</h3>
                </a>
            @endforeach
        </div>
    </section>

    <section class="public-content-card public-section">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div><span class="section-kicker">Repositorio</span><h2 class="section-title">Documentos recientes</h2></div>
            <a href="{{ route('documents.index') }}" class="font-bold text-cod-teal">Ver todos →</a>
        </div>
        <div class="mt-6 grid gap-5 md:grid-cols-2">
            @forelse($documents as $document)
                <x-document-card :document="$document" />
            @empty
                <p>No hay documentos disponibles.</p>
            @endforelse
        </div>
    </section>

    @if($activities->isNotEmpty())
        <section class="public-content-card public-section">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div><span class="section-kicker">Comunidad</span><h2 class="section-title">Últimas actividades</h2></div>
                <a href="{{ route('activities.index') }}" class="font-bold text-cod-teal">Ver todas →</a>
            </div>
            <div class="mt-6 grid gap-5 md:grid-cols-2">
                @foreach($activities->take(4) as $activity)
                    <article class="border-l-4 border-cod-teal bg-slate-50 p-5">
                        @if($activity->occurred_at)<time class="text-xs font-bold uppercase text-cod-teal">{{ $activity->occurred_at->translatedFormat('d M Y') }}</time>@endif
                        <h3 class="mt-1 text-lg font-extrabold text-cod-navy">{{ $activity->title }}</h3>
                        <p class="mt-2 line-clamp-3 text-sm leading-6">{{ $activity->summary }}</p>
                        <a class="mt-3 inline-block font-bold text-cod-teal" href="{{ route('activities.show', $activity) }}">Leer actividad →</a>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
@endsection
