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

            <form id="agreementFrm" name="agreementFrm" class="pi-button-container pi-button-centralize" method="POST" action="{{ url('/user/agreement' ) }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                
                <button id="agreementBtnSubmit" type="submit" class="pi-button pi-theme-form">&nbsp; &nbsp; &nbsp; 동의 &nbsp; &nbsp; &nbsp; </button>
            </form>
        </div>
    </div>
<<<<<<< HEAD
=======
</form>

>>>>>>> e8d2f890cdf8fcb3f5a56f75fe5826d707f3e9aa
@endsection