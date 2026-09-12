@props(['document'])
<article class="group flex h-full flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
    <div class="mb-4 flex items-center justify-between gap-3"><span class="rounded-full bg-teal-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-cod-teal">{{ $document->category?->name ?? 'Documento' }}</span><span class="text-xs font-semibold text-slate-400">{{ strtoupper(pathinfo(parse_url($document->public_url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: 'DOC') }}</span></div>
    <h3 class="text-lg font-bold leading-snug text-cod-navy group-hover:text-cod-teal">{{ $document->title }}</h3>
    @if($document->published_at)<time class="mt-3 text-xs text-slate-500">Publicado el {{ $document->published_at->translatedFormat('d \d\e F \d\e Y') }}</time>@endif
    <a href="{{ route('documents.show', $document) }}" class="mt-auto pt-5 text-sm font-bold text-cod-teal">Ver documento →</a>
</article>
