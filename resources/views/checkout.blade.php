<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    
    <meta property="og:url" content="{{ url('/') }}" />
    <meta property="og:title" content="{{ Lang::get('pages.title1') }}" />
    <meta property="og:image" content="{{ url('/') }}images/logo1.png" />
    <meta property="og:description" content="{{ Lang::get('pages.sub1') }}" />

    <title>{{ Lang::get('pages.title1') }}</title>
    <link rel="shortcut icon" href="{{ asset('/images/favicon.ico') }}" type="image/x-icon" />
    <link href="{{ asset('assets/css/app.css') }}?noCache={{ date('Y-m-d_h:i:s') }}" rel="stylesheet" />
    <link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css' /><!-- Fonts -->
    <script src="{{ asset('assets/js/architekt.js') }}?noCache={{ date('Y-m-d_h:i:s') }}"></script>
</head>
<body>

    @yield('content')


    <!-- load depencies -->
    <script src="{{ asset('assets/js/depend.js') }}"></script>
    <!-- load modules -->
    <script src="{{ asset('assets/js/architekt_modules.js') }}?noCache={{ date('Y-m-d_h:i:s') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}?noCache={{ date('Y-m-d_h:i:s') }}"></script>
    @include('synchronizer/sync_locale')

</body>
</html>