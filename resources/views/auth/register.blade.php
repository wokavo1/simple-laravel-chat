@extends('layouts.app')

@section('content')
<div class="container">
    <form action="{{ route('auth.register') }}" method="POST">
        @csrf

        <div class="d-flex flex-column">
            <h2>Register for an Account</h2>

            <label for="email">Email:</label>
            <input
                type="email"
                name="email"
                value="{{ old('email') }} "
                required>

            <label for="password">Password:</label>
            <input
                type="password"
                name="password"
                required>

            <label for="password">Confirm Password:</label>
            <input
                type="password"
                name="password_confirmation"
                required>

            <button type="submit" class="btn btn-dark mt-4">Register</button>
        </div>

        <!-- validation errors -->
        @if ($errors->any())
            <ul>
                @foreach ($errors->all() as $key => $error)
                    <li class="red">
                        {{ $error }}
                    </li>
                @endforeach
            </ul>
        @endif

    </form>
</div>
@endsection