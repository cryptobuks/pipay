@extends('app')
@section('content')
	<!-- Main top -->
    <div id="pi_main_top">
        <div class="pi-container">
            <div id="pi_main_top_text">
                <h1>파이 결제를 시작하고<br />쇼핑몰 매출이 증가하였습니다.</h1>
                <a href="#" class="pi-button pi-theme-point">파이 결제 받기</a>
                <p>파이 페이먼트는 어떻게 동작하나요?</p>
            </div>
        </div>

        <img id="pi_main_top_yummy" src="image/yummy.png" />
    </div>

    <!-- Feature overview -->
    <div id="pi_main_feature_overview">
        <div class="pi-container">
            <div class="pi_main_feature_item">
                <img src="image/feature_icon_1.png" />
                <p>수수료 제로</p>
            </div>
            <div class="pi_main_feature_item">
                <img src="image/feature_icon_3.png" />
                <p>글로벌 결제</p>
            </div>
            <div class="pi_main_feature_item">
                <img src="image/feature_icon_2.png" />
                <p>실시간 판매 및 정산</p>
            </div>
            <div class="pi_main_feature_item">
                <img src="image/feature_icon_4.png" />
                <p>간편한 설치와 결제</p>
            </div>
        </div>
    </div>

    <!-- Feature: Low fee -->
    <div id="pi_main_feature_1" class="pi_main_feature">
        <div class="pi-container">
            <div class="pi_feature_texts">
                <h1>수수료 제로</h1>
                <p>
                    신용카드, 계좌이체 등 기존의 수수료가 부담스러우셨나요?<br />
                    파이 결제시 수수료 제로, 원화 환전시 1% 수수료.<br />
                    가입비도 연회비도 없습니다.
                </p>    
            </div>
            
            <img src="image/feature_big_1.png" />
        </div>
    </div>

    <!-- Feature: Global trade -->
    <div id="pi_main_feature_2" class="pi_main_feature alt">
        <div class="pi-container">
            <div class="pi_feature_texts">
                <h1>글로벌 결제</h1>
                <p>
                    파이결제는 쉽고 빠른 해외결제를 가능하게 해줍니다.<br />
                    파이 페이먼트와 함께 여러분의 비즈니스 기회를 전세계적으로 넓혀보세요. 
                </p>
            </div>
            
            <img src="image/feature_big_3.png" />
        </div>
    </div>

    <!-- Feature: Realtime trade -->
    <div id="pi_main_feature_3" class="pi_main_feature">
        <div class="pi-container">
            <div class="pi_feature_texts">
                <h1>실시간 판매 및 정산</h1>
                <p>
                    결제상황 조회 및 파이정산이 실시간으로 처리됩니다.<br />
                    원화로 정산을 받는 경우에도 내일 바로 가능합니다. 
                </p>
            </div>
            
            
            <img src="image/feature_big_2.png" />
        </div>
    </div>

    <!-- Feature: Easy to use -->
    <div id="pi_main_feature_4" class="pi_main_feature alt">
        <div class="pi-container">
            <div class="pi_feature_texts">
                <h1>간편한 설치와 결제</h1>
                <p>
                    간단한 상품 정보를 입력하면 복잡한 설치과정없이 바로 파이결제를 받을 수 있습니다.<br />
                    여러분의 고객에게도 쉬운 결제를 경험하게 해주세요. 
                </p>
            </div>
            
            <img src="image/feature_big_4.png" />
        </div>
    </div>

    <!-- Bottom -->
    <div id="pi_main_bottom">
        <div class="pi-container">
            <a href="#" class="pi-button pi-theme-proceed">파이 결제받기</a>
        </div>
    </div>

@endsection