@extends('app')
@section('content')
    <script>
        Architekt.event.on('ready', function() {
            var h = Architekt.device.height;
            h -= 80;    //gnb
            h -= 140;   //footer
            h -= 28;    //title size(h1)
            h -= 128;    //make some more spaces(include top and bottom spaces, title spaces from the gnb)
            $('.pi_agreements_content').css('height', h + 'px');
        });
    </script>

    <div id="pi_top_space"></div>
<form id="agreementFrm" name="agreementFrm" class="container" method="POST" action="{{ url('/user/agreement' ) }}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <div id="pi_terms">
        <div class="pi-container">
            @include('agreement_content')

            <div class="pi-button-container pi-button-centralize">
                <button id="agreementBtnSubmit" type="submit" class="pi-button pi-theme-form">동의</button>
            </div>
            
        </div>
    </div>
</form>

@endsection