@extends('app')
@section('content')

    <script>
        Architekt.event.on('ready', function() {

        });
    </script>

    <div id="pi_top_space"></div>

    <div id="pi_product">
        <div class="pi-container">
            <div class="pi-button-container">
            	<a href="/tool/generate/button" id="generateButton" class="pi-button pi-theme-success">결제버튼 만들기</a>
            </div>

            <div class="pi-button-container">
            	<a href="/tool/generate/link" id="generateLink" class="pi-button pi-theme-success">결제 링크 만들기</a>
            </div>

        </div>
    </div>

@endsection