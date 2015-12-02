<!DOCTYPE html>
<html>
<head>
<title>Pi Payment</title>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>{{ Lang::get('pages.title1') }}</title>
<meta property="og:url" content="https://www.pi-pay.net/" />
<meta property="og:title" content="{{ Lang::get('pages.title1') }}" />
<meta property="og:image" content="https://www.pi-pay.net/images/logo1.png" />
<meta property="og:description" content="{{ Lang::get('pages.sub1') }}" />

<link rel="shortcut icon" href="{{ asset('/images/favicon.ico') }}" type="image/x-icon" />
<link href="{{ asset('assets/css/app.css') }}?noCache={{ date('Y-m-d_h:i:s') }}" rel="stylesheet">
<link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'><!-- Fonts -->
<!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">    -->


</head>
<body>
@if (Session::has('flash_message'))
    <script>
        window.addEventListener('load', function(){
            new Widget.Notice({
                text: '{!! session('flash_message') !!}',
                headText: 'Pi-Payment'
            });
        });
    </script>
@endif

<?php
    $loggedIn = Sentinel::check();
    
    if($loggedIn){
        $user = Sentinel::getUser();
        $username = $user->username ? $user->username : $user->email;
    }
?>

        <div class="container">
        <nav class="navbar xnavbar-fixed-top navbar-inverse" role="navigation">

            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ URL::to('/') }}">Pi Payment</a>
            </div>

            <div class="collapse navbar-collapse navbar-ex1-collapse">

                <ul class="nav navbar-nav">
                    <li{{ Request::is('/') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">Home</a></li>
                    @if ( ! Sentinel::check())
                    <li{{ Request::is('login') ? ' class="active"' : null }}><a href="{{ URL::to('user/login') }}">Login</a></li>
                    <li{{ Request::is('register') ? ' class="active"' : null }}><a href="{{ URL::to('user/register') }}">Register</a></li>
                    @else
                    <li{{ Request::is('dashborad') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">Dashboard</a></li>                    
                    <li{{ Request::is('product') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">Product</a></li>                                   
                    <li{{ Request::is('payment') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">Payment</a></li>                                  
                    <li{{ Request::is('leagder') ? ' class="active"' : null }}><a href="{{ URL::to('/') }}">Leagder</a></li>                                                                           
                    @endif
                </ul>
          <ul class="nav navbar-nav pull-right">
                @if ($user = Sentinel::check())
                    <li{{ Request::is('account') ? ' class="active"' : null }}><a href="{{ URL::to('account') }}">Account
                    @if ( ! Activation::completed($user))
                    <span class="label label-danger">Inactive</span>
                    @endif
                </a></li>
                <li><a href="{{ URL::to('logout') }}">Logout</a></li>
                @endif
          </ul>

        </div>
    </nav>



            @include('errors.message')
            @yield('content')
        </div>

    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.socket.io/socket.io-1.3.5.js"></script>
    <script src="{{ asset('assets/js/all.js') }}?noCache={{ date('Y-m-d_h:i:s') }}"></script>
    <script> 
        /* window.Locale.setLocale('<?= App::getLocale() ?>'); */
    </script>

</body>
</html>            