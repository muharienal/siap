@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-12">
                            <h4>{{ __('You are logged in!') }}</h4>
                            <p class="text-muted">Welcome back, {{ Auth::user()->name }}!</p>
                            
                            <div class="mt-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Quick Actions</h5>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <a href="#" class="btn btn-outline-primary w-100">
                                                    Profile Settings
                                                </a>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <a href="/dashboard" class="btn btn-outline-success w-100">
                                                    View Dashboard
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
