<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <script type="text/javascript">
            window.api_url = "{{ config('app.url') }}/api";
        </script>

        <script src="{{ mix('js/app.js') }}" defer></script>

        <link href="{{ mix('css/app.css') }}" rel="stylesheet">

        {{-- ... Tracking would go here ... --}}
    </head>
    <body>
        @if(\App::environment() == 'local' && request()->has('grid'))
            <div style="text-align: center; position:fixed; left:0; right:0; top:0; z-index:999; color:white">
                <div class="d-none d-xxl-block" style="background-color:red">XXL</div>
                <div class="d-none d-xl-block d-xxl-none" style="background-color:#ff5050">XL</div>
                <div class="d-none d-lg-block d-xl-none" style="background-color:#ff6600">LG</div>
                <div class="d-none d-md-block d-lg-none" style="background-color:#ff9933">MD</div>
                <div class="d-none d-sm-block d-md-none" style="background-color:#ffcc00">SM</div>
                <div class="d-xs-block d-sm-none" style="background-color:#ffff00">XS</div>
            </div>
        @endif

        @yield('content')
    </body>
</html>
