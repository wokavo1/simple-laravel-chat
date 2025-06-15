<!doctype html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    @vite(['resources/css/external/bootstrap.min.css', 'resources/js/app.js'])
</head>

<body>
    <header>
        <div class="container mt-5 mb-5 d-flex flex-row align-items-center">
            <a class="btn btn-dark m-1"
                href="{{ route('index') }}">Main Page</a>
            @guest
            <a class="btn btn-dark m-1"
                href="{{ route('auth.login.page') }}"> Login </a>
            <a class="btn btn-dark m-1"
                href="{{ route('auth.register.page') }}"> Register </a>
            @endguest

            @auth
            <div style="justify-self: flex-end;">
                Hello {{ Auth::user()->name }}
            </div>
            <form action="{{ route('auth.logout') }}" method="POST"
                class="mb-0">
                @csrf
                <button class="btn btn-dark m-1">
                    Logout
                </button>
            </form>
            @endauth
        </div>
    </header>
    <main id="app">@yield('content')</main>

    @yield('script')
</body>

</html>