@extends('layouts.public')

@section('title', $document->title.' | CODISEC Surquillo')
@section('page_heading', 'Documento')
@section('page_parent', $document->category?->name ?? 'Repositorio')

@section('content')
    <article class="public-content-card">
        <a href="{{ route('documents.index') }}" class="text-sm font-bold text-cod-teal">← Volver al repositorio</a>
        <span class="mt-6 block text-xs font-bold uppercase tracking-wide text-cod-teal">{{ $document->category?->name ?? 'Documento' }}</span>
        <h1 class="mt-2 text-3xl font-black leading-tight text-cod-navy">{{ $document->title }}</h1>
        @if($document->published_at)<p class="mt-3 text-sm text-slate-500">Publicado el {{ $document->published_at->translatedFormat('d \d\e F \d\e Y') }}</p>@endif
        @if($document->description)<div class="content-body mt-8">{!! $document->description !!}</div>@endif
        @if($document->public_url)
            <a href="{{ $document->public_url }}" target="_blank" rel="noopener" class="mt-8 inline-flex bg-cod-teal px-6 py-3 font-black text-white hover:bg-cod-blue">Abrir o descargar documento ↗</a>
        @else
            <p class="mt-8 bg-amber-50 p-4 text-amber-900">El archivo no está disponible en este momento.</p>
        @endif
    </article>
@endsection
