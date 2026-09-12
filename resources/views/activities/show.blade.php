@extends('layouts.public')
@section('title', $activity->title.' | CODISEC Surquillo')
@section('content')
<div class="mx-auto max-w-5xl px-4 py-14 sm:px-6 lg:px-8"><a href="{{ route('activities.index') }}" class="text-sm font-bold text-cod-teal">← Todas las actividades</a><article class="mt-6 overflow-hidden rounded-2xl bg-white shadow-sm">@if($activity->cover_image)<img src="{{ $activity->cover_image }}" alt="" class="max-h-[540px] w-full object-cover">@endif<div class="p-7 sm:p-10">@if($activity->occurred_at)<time class="text-sm font-bold text-cod-teal">{{ $activity->occurred_at->translatedFormat('d \d\e F \d\e Y') }}</time>@endif<h1 class="mt-3 text-3xl font-black text-cod-navy sm:text-4xl">{{ $activity->title }}</h1><div class="content-body mt-8">{!! $activity->content !!}</div></div></article></div>
@endsection
