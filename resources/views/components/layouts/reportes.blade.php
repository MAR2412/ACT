@extends('layouts.base')
@section('content')
    <x-banner />

    <div class="min-h-screen bg-stone-100 dark:bg-stone-900">
        <!-- Page Content -->
        <main class="dark:bg-stone-900">
            <div class="">
                <div class=" mx-auto">
                    {{ $slot }}
                </div>
            </div>
        </main>

    </div>
@endsection
