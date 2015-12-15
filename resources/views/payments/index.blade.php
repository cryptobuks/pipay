@extends('app')
@section('content')
    
    <script>
        Architekt.event.on('ready', function() {

        });
    </script>

    <div id="pi_top_space"></div>

    <div id="pi_product">
        <div class="pi-container">
            <div class="pi-abstract-nav">
                <div class="pi-abstract-nav-item on">전부</div>
                <div class="pi-abstract-nav-item">완료</div>
            </div>

            <div class="pi-button-container">
                <a href="#" id="refresh" class="pi-button pi-theme-form">
                    <div class="sprite-refresh"></div>
                    <p>새로고침</p>
                </a>
                <a href="#" id="exportExcel" class="pi-button pi-theme-form">
                    <div class="sprite-disk"></div>
                    <p>내보내기</p>
                </a>
            </div>

            <table class="pi-table">
            	<thead>
                    <tr>
                        <th>주문번호</th>
                        <th>결제시각</th>
                        <th>상품명</th>
                        <th>상품가격</th>
                        <th>결제상태</th>
                        <th>Pi 결제금액</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>33812</td>
                        <td>2015-10-11</td>
                        <td>야구공</td>
                        <td>5,000 KRW</td>
                        <td><span class="pi-text pi-theme-complete">완료</span></td>
                        <td>V 0.5 / 0.5</td>
                    </tr>
                    <tr>
                        <td>33819</td>
                        <td>2015-10-12</td>
                        <td>축구공</td>
                        <td>15,000 KRW</td>
                        <td><span class="pi-text pi-theme-waiting">대기</span></td>
                        <td>V 1.5 / 1.5</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

	

@endsection