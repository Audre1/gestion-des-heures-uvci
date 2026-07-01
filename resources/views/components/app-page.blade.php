@props([
    'title',
    'subtitle' => null,
    'section' => null,
    'icon' => null,
])

@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="page-head">
        <div>
            <nav class="breadcrumb">
                <a href="{{ route('dashboard') }}">Accueil</a>
                @if($section)<span class="mx-1 text-muted">›</span><span class="text-muted">{{ $section }}</span>@endif
                <span class="mx-1 text-muted">›</span><span class="active">{{ $title }}</span>
            </nav>
            <h1>@if($icon)<i class="{{ $icon }} text-uvci-green me-2"></i>@endif{{ $title }}</h1>
            @if($subtitle)<p class="subtitle">{{ $subtitle }}</p>@endif
        </div>
        @isset($actions)
            <div class="actions">{{ $actions }}</div>
        @endisset
    </div>

    {{ $slot }}
@endsection
