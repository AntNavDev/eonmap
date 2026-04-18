@extends('layouts.app')

@section('content')
    <livewire:occurrence-browser />
@endsection

@push('scripts')
    @vite('resources/js/browse.js')
@endpush