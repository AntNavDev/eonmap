@extends('layouts.app')

@section('content')
    <livewire:fossil-map />
@endsection

@push('scripts')
    @vite('resources/js/map.js')
@endpush