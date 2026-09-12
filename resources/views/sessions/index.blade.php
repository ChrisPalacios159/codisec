@extends('layouts.public')

@section('title', 'Sesiones | CODISEC Surquillo')
@section('page_heading', 'Sesiones del CODISEC')

@section('content')
    <section class="public-content-card">
        <p class="mb-6">Actas, acuerdos y documentos de las reuniones del comité.</p>
        <div class="grid gap-3">
            @forelse($sessions as $session)
                <article class="flex flex-col gap-4 border border-slate-200 bg-slate-50 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <div><span class="text-xs font-bold uppercase text-cod-teal">{{ data_get($session->metadata, 'year', 'Sesión') }}</span><h2 class="mt-1 font-extrabold text-cod-navy">{{ $session->title }}</h2></div>
                    @if($session->external_url || $session->document_path)<a href="{{ $session->external_url ?: asset('storage/'.$session->document_path) }}" target="_blank" rel="noopener" class="shrink-0 bg-cod-teal px-4 py-2 text-center text-sm font-bold text-white">Ver acta ↗</a>@endif
                </article>
            @empty
                <p>No hay sesiones disponibles.</p>
            @endforelse
        </div>
        <div class="mt-8">{{ $sessions->links() }}</div>
    </section>
@endsection
