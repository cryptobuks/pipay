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
            	<a href="#" id="addProduct" class="pi-button pi-theme-success">결제 상품 추가하기</a>
            </div>

            <table class="pi-table">
            	<thead>
            		<tr>
            			<th>상품명</th>
						<th>상품번호</th>
						<th>상품가격</th>
						<th>결제방법</th>
						<th>정산통화</th>
            		</tr>
            	</thead>
            	<tbody>
					<tr>
						<td>야구공</td>
						<td>1234567</td>
						<td>5,000 KRW</td>
						<td>온라인</td>
						<td>KRW</td>
					</tr>
					<tr>
						<td>축구공</td>
						<td>1231323</td>
						<td>10,000 KRW</td>
						<td>온라인</td>
						<td>Pi</td>
					</tr>
            	</tbody>
            </table>
        </div>
    </div>

@endsection