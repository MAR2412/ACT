@extends('layouts.base')

@section('styles')
    @yield('app-styles')
    @livewireStyles
@endsection

@section('content')
    @yield('app-content')
@endsection

@section('scripts')
    @yield('app-scripts')
    @livewireScripts
@endsection
