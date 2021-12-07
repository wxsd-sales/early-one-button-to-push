@extends('layouts.app')

@section('content')
    <login :csrf='"{!! csrf_token() !!}"'
           :email='"{!! session('email') ?? old('email') !!}"'
           :error='{!! $errors->any() ? $errors : "{}" !!}'
           :url='{!! json_encode($url) !!}'>
    </login>
@endsection
