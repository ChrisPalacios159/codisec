@extends('layouts.public')
@section('title', $page->title.' | CODISEC Surquillo')
@section('content')
<header class="bg-cod-blue text-white"><div class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8"><a href="{{ route('home') }}" class="text-sm text-blue-200">Inicio</a><h1 class="mt-3 text-4xl font-black">{{ $page->title }}</h1>@if($page->excerpt)<p class="mt-4 max-w-3xl text-blue-100">{{ $page->excerpt }}</p>@endif</div></header>
<div class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8"><article class="content-body rounded-2xl bg-white p-6 shadow-sm sm:p-10">{!! $page->content !!}</article>@if($documents->count())<section class="mt-12"><h2 class="text-2xl font-black text-cod-navy">Documentos de esta sección</h2><div class="mt-6 grid gap-5 md:grid-cols-2">@foreach($documents as $document)<x-document-card :document="$document" />@endforeach</div><div class="mt-8">{{ $documents->links() }}</div></section>@endif</div>
@endsection
