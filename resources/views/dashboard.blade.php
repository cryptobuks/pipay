@extends('app')
@section('content')
    <script>
<<<<<<< HEAD
        Architekt.event.on('ready', function() {

        });
    </script>

    <div id="pi_top_space"></div>

    <div id="pi_dashboard">
        <div class="pi-container">

        </div>
    </div>

=======

    </script>

	<!-- Main top -->
    <div id="pi_main_top">
        <div class="pi-container">
            <div id="pi_main_top_text">
                <h1>대쉬보드</h1>
            </div>
        </div>

        <img id="pi_main_top_yummy" src="image/yummy.png" />
    </div>

    <!-- How this work -->
    <div id="howThisWorkLayer" class="architekt-widget-background">
        <img src="{{ asset('image/howThisWork.png') }}" />
    </div>



>>>>>>> f2963c5278d4a258b8b533f02e1f008f03631e6c
@endsection