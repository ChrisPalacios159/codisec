@extends('layouts.public')

@section('title', 'Actividades | CODISEC Surquillo')
@section('page_heading', 'Actividades y buenas prácticas')

@section('content')
    <section class="public-content-card">
        <p class="mb-7">Acciones del comité y trabajo conjunto por la seguridad de Surquillo.</p>
        <div class="grid gap-5 md:grid-cols-2">
            @forelse($activities as $activity)
                <article class="overflow-hidden border border-slate-200 bg-slate-50">
                    @if($activity->cover_image)<img src="{{ $activity->cover_image }}" alt="" class="h-48 w-full object-cover" loading="lazy">@else<div class="h-2 bg-cod-teal"></div>@endif
                    <div class="p-5">
                        @if($activity->occurred_at)<time class="text-xs font-bold text-cod-teal">{{ $activity->occurred_at->translatedFormat('d \d\e F \d\e Y') }}</time>@endif
                        <h2 class="mt-2 text-xl font-extrabold text-cod-navy">{{ $activity->title }}</h2>
                        <p class="mt-3 line-clamp-4 text-sm leading-6">{{ $activity->summary }}</p>
                        <a href="{{ route('activities.show', $activity) }}" class="mt-4 inline-block font-bold text-cod-teal">Leer más →</a>
                    </div>
                </article>
            @empty
                <p>No hay actividades publicadas.</p>
            @endforelse
        </div>
        <div class="mt-8">{{ $activities->links() }}</div>
    </section>
@endsection
