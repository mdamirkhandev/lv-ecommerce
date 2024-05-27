@extends('user.layouts.app')
@section('content')
    <main>
        <section class="section-5 pt-3 pb-3 mb-3 bg-white">
            <div class="container">
                <div class="light-font">
                    <ol class="breadcrumb primary-color mb-0">
                        <li class="breadcrumb-item"><a class="white-text" href="{{ route('user.index') }}">Home</a></li>
                        <li class="breadcrumb-item">Login</li>
                    </ol>
                </div>
            </div>
        </section>

        <section class=" section-10">
            <div class="container">
                <div class="login-form">
                    @if (session('success'))
                        <x-alert type="success" :message="session('success')" />
                    @elseif(session('error'))
                        <x-alert type="danger" :message="session('error')" />
                    @endif
                    <form action="{{ route('user.login-process') }}" method="post">
                        @csrf
                        <h4 class="modal-title">Login to Your Account</h4>
                        <div class="form-group">
                            <input type="text" name="email" class="form-control @error('email') is-invalid @enderror"
                                placeholder="Email" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" placeholder="Password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group small">
                            <a href="#" class="forgot-link">Forgot Password?</a>
                        </div>
                        <input type="submit" class="btn btn-dark btn-block btn-lg" value="Login">
                    </form>
                    <div class="text-center small">Don't have an account? <a href="register.php">Sign up</a></div>
                </div>
            </div>
        </section>
    </main>
@endsection
