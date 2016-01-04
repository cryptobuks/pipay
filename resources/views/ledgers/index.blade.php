@extends('app')
@section('content')
    
    <script>
        //Ledger list control
        Architekt.event.on('ready', function() {
            var Notice = Architekt.module.Widget.Notice;
            var Http = Architekt.module.Http;

            var requestUrl = '{{ Request::url() }}';
            var dataTable = new Architekt.module.DataTable({
                pagenate: true,
                readOnly: true,
            });
            var hasNext = false;    //check for next page exists
            var pagePer = parseInt('{{ $pagePer }}');

            pagePer = isNaN(pagePer) ? 10 : pagePer;    //if pagePer from the server is not a number, use 10 instead.
            var isRefresh = false;                      //if refresh and no more page, no hasNext alert.

            //create DataTable component
            dataTable.setHeaderColumn(['날짜', '입금', '출금', '수수료']);
            dataTable.appendTo($('#pi_list'));    //append to body

            //first draw
            dataTable.render({ animate: true });


            //load pure functions for common actions
            @include('dataTable/pure')


            //load adapted functions for common actions
            //filterFunc(object dataColumn): data filtering function for payment
            function filterFunc(dataColumn) {
                var parsedArray = [];

                for(var key in dataColumn) {
                    if(key === 'pi_amount_received') {
                        parsedArray.push(dataColumn['pi_amount_received'] + ' / ' + dataColumn['pi_amount']);
                        break;
                    }

                    parsedArray.push(dataColumn[key]);
                }

                return parsedArray;
            }
            @include('dataTable/adapted')


            //load common event handlers
            @include('dataTable/events')


            //get data and update!
            $('#refresh').trigger('click');
        });
    </script>

    <div id="pi_top_space"></div>

	<div id="pi_ledger">
        <div id="pi_list" class="pi-container">
        	<div id="pi_ledger_total">
        		<h1>잔액: <span class="pi-theme-complete">{{ $AccountJson[0]->KRW }} KRW</span> | <span class="pi-theme-waiting">{{ $AccountJson[0]->PI }} Pi</span></h1>
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
        </div>
    </div>

	

@endsection