@extends('user.layouts.app')
@section('content')
    <main>
        <section class="section-5 pt-3 pb-3 mb-3 bg-white">
            <div class="container">
                <div class="light-font">
                    <ol class="breadcrumb primary-color mb-0">
                        <li class="breadcrumb-item"><a class="white-text" href="{{ route('user.index') }}">Home</a></li>
                        <li class="breadcrumb-item active">Thank You</li>
                    </ol>
                </div>
            </div>
        </section>
        <section class="container">
            @if (session('success'))
                <x-alert type="success" :message="session('success')" />
            @elseif(session('error'))
                <x-alert type="danger" :message="session('error')" />
            @endif
            <div class="col-md-12 text-center">
                <h1>Thank You For Your Purchase</h1>
                <h3>Your order has been successfully placed. Your order ID is: {{ $id }}</h3>
                <p>Thank you for shopping with us. We're thrilled to have you on board.</p>
            </div>
        </section>
    </main>
@endsection
