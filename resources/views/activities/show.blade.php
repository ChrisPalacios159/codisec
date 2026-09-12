@extends('layouts.public')

@section('title', $activity->title.' | CODISEC Surquillo')
@section('page_heading', 'Actividad')
@section('page_parent', 'Actividades y buenas prácticas')

@section('content')
    <article class="public-content-card overflow-hidden !p-0">
        @if($activity->cover_image_url)<img src="{{ $activity->cover_image_url }}" alt="" class="max-h-[520px] w-full object-cover">@endif
        <div class="p-7 sm:p-9">
            <a href="{{ route('activities.index') }}" class="text-sm font-bold text-cod-teal">← Todas las actividades</a>
            @if($activity->occurred_at)<time class="mt-6 block text-sm font-bold text-cod-teal">{{ $activity->occurred_at->translatedFormat('d \d\e F \d\e Y') }}</time>@endif
            <h1 class="mt-2 text-3xl font-black text-cod-navy">{{ $activity->title }}</h1>
            <div class="content-body mt-7">{!! $activity->content !!}</div>
        </div>
    </article>
@endsection
