@extends('layouts.public')

@section('title', 'CODISEC Surquillo | Seguridad Ciudadana')

@section('sidebar_after')
    <div class="sidebar-widget sidebar-status-widget">
        <p>No hay documentos en este momento.</p>
    </div>
@endsection

@section('content')
    <h1 class="sr-only">Un Surquillo más seguro, informado y organizado.</h1>

    <article class="home-featured">
        <img
            src="{{ asset('storage/legacy/2016/07/cuatro-2.jpg') }}"
            alt="Autoridades y representantes de seguridad ciudadana reunidos en la plaza de Surquillo"
            class="home-featured-image"
        >

        <div class="home-featured-body">
            <p>Aquí podrás ver toda la información brindada por el Comité Distrital de Seguridad Ciudadana hacia los habitantes del distrito de Surquillo.</p>
            <time datetime="2022-01-14" class="home-featured-date">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" />
                    <path d="M12 7v5l3 2" />
                </svg>
                14 Enero, 2022
            </time>
        </div>
    </article>

    <div class="home-content-spacer" aria-hidden="true"></div>
@endsection
