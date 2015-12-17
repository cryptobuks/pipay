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
            	<a href="/product/create" id="addProduct" class="pi-button pi-theme-success">결제 상품 추가하기</a>
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
			@foreach($product as $item)
					<tr>
						<td>{{ $item->item_desc }}</td>
						<td>{{ $item->order_id }}</td>
						<td>{{ $item->amount }}KRW</td>
						<td>
							@if ( $item->usage == 1)
								온라인
							@elseif ( $item->usage == 2)
								기부
							@elseif ( $item->usage == 3)
								오프라인
							@endif
						</td>
						<td>{{ $item->currency }}</td>
					</tr>
			@endforeach
            	</tbody>
            </table>
        </div>
    </div>

	<div class="col-md-12">
		{!! $product->render(); !!}
	</div>

@endsection