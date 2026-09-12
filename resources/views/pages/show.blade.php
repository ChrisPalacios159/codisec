@extends('layouts.public')

@section('title', $page->title.' | CODISEC Surquillo')
@section('page_heading', $page->title)
@section('page_parent', 'CODISEC')

@section('content')
    <article class="public-content-card content-body">
        {!! $page->content !!}
    </article>

    @if($documents->count())
        <section class="public-content-card public-section">
            <span class="section-kicker">Archivos relacionados</span>
            <h2 class="section-title">Documentos de esta sección</h2>
            <div class="mt-6 grid gap-5 md:grid-cols-2">
                @foreach($documents as $document)
                    <x-document-card :document="$document" />
                @endforeach
            </div>
            <div class="mt-8">{{ $documents->links() }}</div>
        </section>
    @endif
@endsection
