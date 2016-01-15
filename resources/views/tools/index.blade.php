@extends('app')
@section('content')

    <div id="pi_top_space"></div>

    <div id="pi_product">
        <div class="pi-container">
            <div id="tool_buttons">
            	<a href="/tool/generate/button">
                    <h1>결제버튼 만들기</h1>
                    <p>웹 사이트에 파이결제 버튼을 추가할 수 있습니다.</p>
                    <img src="{{ asset('image/generate_button.png') }}" />
                </a>
            
            	<a href="/tool/generate/link">
                    <h1>결제 링크 만들기</h1>
                    <p>결제 요청시 링크를 이메일, SNS, 문자로 보낼 수 있습니다.</p>
                    <img src="{{ asset('image/generate_link.png') }}" />
                </a>
            </div>
        </div>
    </div>

@endsection