@extends('app')
@section('content')
    
    <script>
        Architekt.event.on('ready', function() {

        });
    </script>

    <div id="pi_top_space"></div>
	
	<div id="pi_ledger">
        <div class="pi-container">
        	<div id="pi_ledger_total">
        		<h1>잔액: <span class="pi-theme-complete">5,000 KRW</span> | <span class="pi-theme-waiting">5Pi</span></h1>
        		<p>* 원화 정산은 결제일로부터 영업일 기간 내 2일 이내로 처리되며 거래소 내부 KRW로 충전됩니다.</p>
        		<p>* 파이 정산은 결제 후 2시간 이내에 처리됩니다.</p>
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
                        <th>날짜</th>
                        <th>입금액</th>
                        <th>출금액</th>
                        <th>수수료</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    	<td>2015-10-11</td>
                    	<td></td>
                    	<td>-4,950 KRW</td>
                    	<td>-50 KRW</td>
                    </tr>
                    <tr>
                    	<td>2015-10-12</td>
                    	<td>5,000 KRW</td>
                    	<td></td>
                    	<td></td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>

	

@endsection