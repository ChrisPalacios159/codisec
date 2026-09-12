@extends('layouts.public')

@section('title', 'Contacto | CODISEC Surquillo')
@section('page_heading', 'Contacto')

@section('content')
    <section class="public-content-card">
        <p>Escríbenos para consultas relacionadas con la información pública del CODISEC.</p>
        @if(session('success'))<div class="mt-6 bg-emerald-50 p-4 font-semibold text-emerald-800">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="mt-6 bg-red-50 p-4 text-red-800">Revisa los campos indicados.</div>@endif
        <form method="post" action="{{ route('contact.store') }}" class="mt-8 grid gap-5 sm:grid-cols-2">
            @csrf
            <label class="grid gap-2 text-sm font-bold">Nombre<input name="name" value="{{ old('name') }}" required class="border border-slate-300 px-4 py-3 font-normal focus:border-cod-teal"></label>
            <label class="grid gap-2 text-sm font-bold">Correo<input type="email" name="email" value="{{ old('email') }}" required class="border border-slate-300 px-4 py-3 font-normal focus:border-cod-teal"></label>
            <label class="grid gap-2 text-sm font-bold">Teléfono<input name="phone" value="{{ old('phone') }}" class="border border-slate-300 px-4 py-3 font-normal focus:border-cod-teal"></label>
            <label class="grid gap-2 text-sm font-bold">Asunto<input name="subject" value="{{ old('subject') }}" class="border border-slate-300 px-4 py-3 font-normal focus:border-cod-teal"></label>
            <label class="grid gap-2 text-sm font-bold sm:col-span-2">Mensaje<textarea name="message" rows="6" required class="border border-slate-300 px-4 py-3 font-normal focus:border-cod-teal">{{ old('message') }}</textarea></label>
            <button class="bg-cod-teal px-6 py-3 font-black text-white hover:bg-cod-blue sm:col-span-2">Enviar mensaje</button>
        </form>
    </section>
@endsection
