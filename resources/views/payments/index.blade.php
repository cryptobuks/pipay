@extends('app')
@section('content')
    
    <script>
        Architekt.event.on('ready', function() {
            var Notice = Architekt.module.Widget.Notice;
            var paymentTable = new Architekt.module.DataTable();    //create DataTable component
            paymentTable.setHeaderColumn(['주문번호', '결제시각', '상품명', '상품가격', '결제상태', 'Pi 결제금액']);
            paymentTable.addColumn( [1, new Date().toGMTString(), '음식물 쓰레기', '1,000,000', '<span class="pi-theme-complete">완료</span>', 100] );   //add items
            paymentTable.addColumn( [2, new Date().toGMTString(), '음식물 쓰레기 MK2', '2,500,000', '<span class="pi-theme-waiting">처리중</span>', 250] );   //add items

            paymentTable.appendTo($('#pi_payment > .pi-container'));    //append to body
            paymentTable.render({ animate: true });      //render the datatable

            //Event handlers
            paymentTable.event.on('itemclick', function(e) {
                var idx = e.clickedIndex;
                var column = e.column;

                new Notice({
                    text: JSON.stringify(column),
                });
            });

            //prev
            paymentTable.event.on('previous', function(e) {
                var page = e.currentPage;

                new Notice({
                    text: 'current page is ' + page
                });
            });

            //next
            paymentTable.event.on('next', function(e) {
                var page = e.currentPage;

                new Notice({
                    text: 'current page is ' + page
                });
            });

            //print items on console
            Architekt.module.Printer.inspect(paymentTable.getColumns());
        });
    </script>

    <div id="pi_top_space"></div>

    <div id="pi_payment">
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

            
        </div>
    </div>

	

@endsection