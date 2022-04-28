<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta name="description" content="{{ config('app.description') }}">
    <meta name="keywords" content="{{ implode(",", config('app.keywords', [])) }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} | Webex by Cisco</title>

    <!-- Preconnects -->
    {{--    <link rel="preconnect" href="">--}}

    <!-- Fonts -->
    {{-- <link rel="dns-prefetch" href="//fonts.gstatic.com"> --}}
    {{-- <link href="" rel="stylesheet"> --}}

    <!-- Styles -->
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">

    <style>
        @if(config('app.debug') && env('SHOW_OUTLINES'))
        * {
            outline: lightgrey dashed 1px;
        }

        .container {
            outline: lightgreen dashed 4px;
        }

        .columns {
            outline: lightskyblue dashed 3px;
        }

        .column {
            outline: lightblue dashed 2px;
        }
        @endif

        @if(env('PARTNER_LOGO_FILENAME'))
        .navbar-brand:before {
            content: '';
            display: block;
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            opacity: 0.2;
            background-image: url({{asset('/images/' . env('PARTNER_LOGO_FILENAME'))}});
            background-size: contain;
            background-repeat: no-repeat;
            background-position: left center;
        }
        @endif

        @yield('css')
    </style>

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{asset('/images/favicons/apple-touch-icon.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset('/images/favicons/favicon-32x32.png')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('/images/favicons/favicon-16x16.png')}}">
    <link rel="manifest" href="{{asset('/images/favicons/site.webmanifest')}}">
    <link rel="mask-icon" href="{{asset('/images/favicons/safari-pinned-tab.svg')}}" color="#5bbad5">
    <link rel="shortcut icon" href="{{asset('/images/favicons/favicon.ico')}}">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-config" content="{{asset('/images/favicons/browserconfig.xml')}}">
    <meta name="theme-color" content="#ffffff">
</head>
<body class="has-navbar-fixed-top">
<nav class="navbar is-fixed-top" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a class="navbar-item" href="{{ url('/') }}">
            <strong>{{ config('app.name', 'Laravel') }}</strong>
        </a>

        <a role="button" id="main-burger" class="navbar-burger burger" data-target="navbarSupportedContent"
           aria-controls="navbarSupportedContent"
           aria-expanded="false"
           aria-label="{{ __('Toggle navigation') }}"
        >
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </a>
    </div>

    <div id="navbarSupportedContent" class="navbar-menu">
        <!-- Left Side Of Navbar -->
        <div class="navbar-start">
        </div>
    @if(!isset($exception))
        <!-- Right Side Of Navbar -->
            <div class="navbar-end">
                <!-- Authentication Links -->
                <div class="navbar-item">
                    @guest
                        @if (Route::has('setup'))
                            <a class="button is-link is-fullwidth is-rounded {{ Request::is('setup') ? 'is-active' : '' }}"
                               href="{{ route('setup') }}"
                            >
                                <span class="icon"><i class="mdi mdi-cog"> </i></span>
                                <span>{{ __('Setup') }}</span>
                            </a>
                        @endif
                    @endguest
                    @auth
                        <div class="navbar-item has-dropdown is-hoverable">
                            <a id="navbarDropdown" class="navbar-link is-size-5" href="#" data-toggle="dropdown"
                               aria-haspopup="true" aria-expanded="false" v-pre
                            >
                                <b>{{ Auth::user()->email }}</b>
                            </a>
                            <div class="navbar-dropdown">
                                @if(Route::has('dashboard'))
                                    <a class="navbar-item is-size-6" href="{{ route('dashboard') }}">
                                        <span class="icon"><i class="mdi mdi-view-dashboard"> </i></span>
                                        <span>{{ __('Dashboard') }}</span>
                                    </a>
                                @endif
                                @if(Route::has('home'))
                                    <a class="navbar-item  is-size-6" href="{{ route('home') }}">
                                        <span class="icon"><i class="mdi mdi-home"> </i></span>
                                        <span>{{ __('Home') }}</span>
                                    </a>
                                @endif
                                <hr class="navbar-divider">
                                <a class="navbar-item  is-size-6 has-text-danger" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                >
                                    <span class="icon"><i class="mdi mdi-logout"> </i></span>
                                    <span>{{ __('Logout') }}</span>
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;"
                                >
                                    @csrf
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        @endif
    </div>
</nav>

<noscript id="javascript-warning" class="hero is-danger is-bold">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                <span class="icon">
                  <i class="mdi mdi-alert"></i>
                </span>
                <span>Javascript is disabled.</span>
            </h1>
            <h2 class="subtitle">
                This site requires Javascript for its core functionality.
                Please enable Javascript in browser settings and reload this page.
            </h2>
        </div>
    </div>
</noscript>

<section id="cookies-warning" class="hero is-danger is-bold is-hidden">
    <div class="hero-body">
        <div class="container">
            <h1 class="title">
                    <span class="icon">
                      <i class="mdi mdi-alert"></i>
                    </span>
                <span>Cookies are disabled.</span>
            </h1>
            <h2 class="subtitle">
                This site requires cookies for its core functionality.
                Please enable cookies in browser settings and reload this page.
            </h2>
        </div>
    </div>
</section>

<main id="app">
    @yield('content')
</main>

<footer class="footer has-background-white">
    <div class="content has-text-centered">
        <p>
            <strong>{{ config('app.name', 'Laravel') }}</strong>
            by <a href="https://github.com/wxsd-sales">WXSD-Sales</a>.<br>
            &copy; {{ date('Y') }} Webex by Cisco
        </p>
    </div>
</footer>

<!-- Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const mainMenu = document.getElementById('main-burger')
        // Check if there are any navbar burgers
        mainMenu.addEventListener('click', () => {
            // Get the target from the "data-target" attribute
            const target = document.getElementById(mainMenu.dataset.target)
            // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
            mainMenu.classList.toggle('is-active')
            target.classList.toggle('is-active')
        })

        // https://github.com/Modernizr/Modernizr/blob/master/feature-detects/cookies.js
        function hasCookiesDisabled () {
            // Quick test if browser has cookieEnabled host property
            if (navigator.cookieEnabled) return false
            // Create cookie
            document.cookie = 'cookietest=1'
            const isCookieSet = document.cookie.indexOf('cookietest=') !== -1
            // Delete cookie
            document.cookie = 'cookietest=1; expires=Thu, 01-Jan-1970 00:00:01 GMT'
            return !isCookieSet
        }

        if (hasCookiesDisabled()) {
            console.log('Cookies are disabled.')
            document.getElementById('cookies-warning').classList.remove('is-hidden')
            document.getElementById('app').classList.add('is-invisible')
        }
    })
</script>
@if(config('app.mix-manifest')['/js/manifest.js'] ?? null && config('app.mix-manifest')['/js/vendor.js'] ?? null)
    <script src="{{ mix('/js/manifest.js') }}" defer></script>
    <script src="{{ mix('/js/vendor.js') }}" defer></script>
@endif
<script src="{{ mix('/js/app.js') }}" defer></script>
@yield('js')
</body>
</html>
