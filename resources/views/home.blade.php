@extends('layouts.app')

@section('content')
{{--Debug Info--}}
<div class="columns is-centered is-mobile my-1 py-6">
    <div class="column is-four-fifths">

        <div class="box mb-4 pb-4">
            <h1 class="title">
                {{ __('Home') }}
            </h1>
            <div class="card-content">
                <div class="content">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>

        @if(config('app.env') === 'local' && config('app.debug') === true)
            <div class="box my-4">
                <h1 class="title">
                    $_SERVER
                </h1>
                <div class="card-content">
                    <div class="content">
                        <pre><code>{{ json_encode($_SERVER,
                                        JSON_PRETTY_PRINT |
                                        JSON_UNESCAPED_UNICODE |
                                        JSON_UNESCAPED_SLASHES |
                                        JSON_NUMERIC_CHECK) }}</code></pre>
                    </div>
                </div>
            </div>
        @endif

        <div class="box my-4">
            <h1 class="title">
                Vue Component
            </h1>
            <div class="card-content">
                <div class="content">
                    <example-component
                            :auth-user-name='"{{ Auth::user()->name }}"'
                            :auth-user-id='{!! Auth::user()->getAuthIdentifier() !!}'>
                    </example-component>
{{--                    <div class="control">--}}
{{--                        <label>--}}
{{--                                <textarea class="textarea" placeholder="XSRF-TOKEN"--}}
{{--                                          readonly>{{ Cookie::get('XSRF-TOKEN') }}</textarea>--}}
{{--                        </label>--}}
{{--                    </div>--}}
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
