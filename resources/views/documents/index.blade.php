@extends('layouts.public')

@section('title', 'Documentos | CODISEC Surquillo')
@section('page_heading', 'Repositorio documental')

@section('content')
    <section class="public-content-card">
        <p class="mb-6 text-base">Planes, evaluaciones, actas, informes y normativa de seguridad ciudadana.</p>
        <form class="grid gap-3 border border-slate-200 bg-slate-50 p-4 sm:grid-cols-[1fr_230px_auto]">
            <input name="q" value="{{ request('q') }}" placeholder="Buscar por título..." class="border border-slate-300 bg-white px-4 py-3 outline-none focus:border-cod-teal">
            <select name="categoria" class="border border-slate-300 bg-white px-4 py-3">
                <option value="">Todas las categorías</option>
                @foreach($categories as $category)
                    <option value="{{ $category->slug }}" @selected(request('categoria') === $category->slug)>{{ $category->name }}</option>
                @endforeach
            </select>
            <button class="bg-cod-teal px-6 py-3 font-bold text-white hover:bg-cod-blue">Buscar</button>
        </form>

        <div class="mt-7 grid gap-5 md:grid-cols-2">
            @forelse($documents as $document)
                <x-document-card :document="$document" />
            @empty
                <p class="border border-slate-200 bg-slate-50 p-6 md:col-span-2">No se encontraron documentos con esos criterios.</p>
            @endforelse
        </div>
        <div class="mt-9">{{ $documents->links() }}</div>
    </section>
@endsection
