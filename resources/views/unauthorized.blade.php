@extends('layouts.app')

@section('content')
<div class="container d-flex flex-column">
    <div>
        Authorization required to access this page
    </div>
    <div class="d-flex flex-row">
        <a class="btn btn-dark m-1"
            href="{{ route('auth.login.page') }}"> Login </a>
        <a class="btn btn-dark m-1"
            href="{{ route('auth.register.page') }}"> Register </a>
    </div>
</div>
@endsection