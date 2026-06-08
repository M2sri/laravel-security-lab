@extends('layouts.app')

@section('title', 'IDOR Lab Login')
@section('nav_label', 'Login navigation')

@section('navigation')
    <a href="/">Home</a>
    <a href="/labs/idor">IDOR Lab</a>
    <a href="/labs/mass-assignment">Mass Assignment Lab</a>
@endsection

@section('content')
    <main class="lab-page narrow-page">
        <h1>IDOR Lab Login</h1>
        <p class="lab-lede">Use a lab customer account to compare vulnerable and secure invoice access.</p>

        @if ($errors->any())
            <p class="error">{{ $errors->first() }}</p>
        @endif

        <form class="form-panel" method="POST" action="/login">
            @csrf

            <label>
                Email
                <input name="email" type="email" value="{{ old('email') }}" required autofocus>
            </label>

            <label>
                Password
                <input name="password" type="password" required>
            </label>

            <button type="submit">Log in</button>
        </form>

        <p class="spaced-link"><a href="/labs/idor">Back to the lab page</a></p>
    </main>
@endsection
