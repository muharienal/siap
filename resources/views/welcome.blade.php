@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Welcome') }}</div>

                <div class="card-body">
                    <div class="text-center">
                        <h1 class="display-4">Welcome to {{ config('app.name', 'SIAP') }}</h1>
                        <p class="lead">A modern Laravel application with Bootstrap UI</p>
                        
                        @guest
                            <div class="mt-4">
                                <a href="{{ route('login') }}" class="btn btn-primary me-2">Login</a>
                                <a href="{{ route('register') }}" class="btn btn-outline-primary">Register</a>
                            </div>
                        @else
                            <div class="mt-4">
                                <a href="{{ route('home') }}" class="btn btn-primary">Go to Dashboard</a>
                            </div>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection