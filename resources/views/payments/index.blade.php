@extends('app')
@section('content')
    
    <script>
        //Payment list control
        Architekt.event.on('ready', function() {
            var Notice = Architekt.module.Widget.Notice;
            var Http = Architekt.module.Http;
            var CustomWidget = Architekt.module.CustomWidget;
            var Formatter = Architekt.module.Formatter;

            var requestUrl = '{{ Request::url() }}';
            var dataTable = new Architekt.module.DataTable({
                pagenate: true,
                readOnly: false,
            });
            var hasNext = false;    //check for next page exists
            var pagePer = parseInt('{{ $pagePer }}');
            var filter = "";
            
            pagePer = isNaN(pagePer) ? 10 : pagePer;    //if pagePer from the server is not a number, use 10 instead.
            var isRefresh = false;                      //if refresh and no more page, no hasNext alert.
            
            //create DataTable component
            dataTable.setHeaderColumn(['주문번호', '결제시각', '상품명', '상품가격', '결제상태', 'Pi 결제금액']);
            dataTable.appendTo($('#pi_list'));    //append to body

            //first draw
            dataTable.render({ animate: true });


            //filter
            var filterDom = $('#pi_payment_filter > div');
            filterDom.click(function() {
                var filterText = $(this).attr('data-filter');

                if(filterText === "all" && filter === "") return;
                else if(filterText === filter) return;

                switch(filterText) {
                    case "confirmed":
                        filterText = filterText;
                        break;
                    default:
                        filterText = "";
                        break;
                }

                //reset page
                dataTable.setPage(1);

                filter = filterText;
                filterDom.removeClass('on');
                $(this).addClass('on');

                $('#refresh').trigger('click'); //force refresh
            });


            //load pure functions for common actions
            @include('dataTable/pure')


            //load adapted functions for common actions
            //filterFunc(object dataColumn): data filtering function for payment
            function filterFunc(dataColumn) {
                var parsedArray = [];

                for(var key in dataColumn) {
                    if(key === 'amount') {
                        var _t = Formatter.currency(dataColumn['amount'], { drop: 1, symbol: '\\' });

                        parsedArray.push(_t);
                    }
                    else if(key === 'pi_amount_received') {
                        var receivedAmount = Formatter.currency(dataColumn['pi_amount_received'], { drop: 1, symbol: false });
                        var amount = Formatter.currency(dataColumn['pi_amount'], { drop: 1, symbol: false });

                        parsedArray.push(receivedAmount + ' / ' + amount);
                    }
                    else if(key === 'status') {
                        var status = dataColumn['status'];
                        var result = '';

                        switch(status) {
                            case 'new':
                                result = '<span class="pi-theme-waiting">대기</span>';
                                break;
                            case 'pending':
                                result = '결제 확인 중';
                                break;
                            case 'confirmed':
                                result = '<span class="pi-theme-complete">결제 완료</span>';
                                break;
                            case 'failed':
                                result = '결제 실패';
                                break;
                            case 'expired':
                                result = '결제 만료';
                                break;
                            case 'refunded':
                                result = '전액 환불';
                                break;
                            case 'refunded_partial':
                                result = '일부 환불';
                                break;
                            default:
                                result = '';
                                break;
                        }

                        parsedArray.push(result);
                    }
                    else {
                        if(parsedArray.length >= 6) break;

                        parsedArray.push(dataColumn[key]);
                    }
                }

                return parsedArray;
            }

            @include('dataTable/adapted')


            /* Event handlers */
            //load common event handlers
            @include('dataTable/events')

            //item click -> show detail
            dataTable.event.on('itemclick', function(e) {
                var idx = e.clickedIndex;
                var column = e.column;

                getProduct(column[0], function(dataObject) {
                    updateProduct(dataObject);
                });
            });


            //get data and update!
            $('#refresh').trigger('click');


            /**************************************************************************************
             *
             *
             *                            details of specific product
             *
             *
             **************************************************************************************/

             //get specified product info
            function getProduct(id, callback) {
                dataTable.lock({
                    loading: true
                });

                getData({
                    url: [requestUrl, id].join("/"),
                    callback: function(dataObject) {
                        if(typeof callback === 'function') callback(dataObject);
                    },
                    complete: function() {
                        dataTable.unlock();
                    }
                });
            }

            //update product info on page
            function updateProduct(dataObject) {
                productDetailWidget.setData(dataObject).render().show();
            }


            /* Product detail widget */
            var productDetailWidget = new CustomWidget({ 
                dom: $('#pi_product_widget'),
                events: {
                    '#refund click': 'refund',
                    '#receipt click': 'receipt'
                },
                formats: { 
                    //print to curreny format
                    currency: function(data, args) {
                        var symbol = args.symbol || 'KRW';
                        return Formatter.currency(data, { symbol: symbol.toUpperCase(), drop: 1 });
                    },
                    //print only if has value
                    printIfHasValue: function(data) {
                        return (!data || data === "") ? "" : data;
                    },
                    statusText: function(data) {
                        var status = data;
                        var result = '';

                        switch(data) {
                            case 'new':
                                result = '대기';
                                break;
                            case 'pending':
                                result = '결제 확인 중';
                                break;
                            case 'confirmed':
                                result = '결제 완료';
                                break;
                            case 'failed':
                                result = '결제 실패';
                                break;
                            case 'expired':
                                result = '결제 만료';
                                break;
                            case 'refunded':
                                result = '전액 환불';
                                break;
                            case 'refunded_partial':
                                result = '일부 환불';
                                break;
                            default:
                                result = '';
                                break;
                        }

                        return result;
                    }
                },
                refund: function(dataObject) {
                    var e = dataObject.originalEvent;
                    e.preventDefault();

                    refundWidget.show({
                        verticalCenter: false
                    });
                },
                receipt: function(dataObject) {
                    var e = dataObject.originalEvent;
                    e.preventDefault();

                    window.open('{{ url('/') }}/receipt/' + dataObject.token);
                },
            });


            /* Refund widget */
            var refundWidget = new CustomWidget({
                dom: $('#pi_refund_widget'),
                events: {
                    'form submit': 'submit',
                },
                submit: function(dataObject) {
                    var e = dataObject.originalEvent;
                    e.preventDefault();

                    new Notice({
                        text: 'Submit!',
                    });

                    refundWidget.hide();
                }
            });
        });
    </script>

    <div id="pi_top_space"></div>

    <div id="pi_payment">
        <div id="pi_list" class="pi-container">
            <div id="pi_payment_filter" class="pi-abstract-nav">
                <div data-filter="all" class="pi-abstract-nav-item on">전부</div>
                <div data-filter="confirmed" class="pi-abstract-nav-item">완료</div>
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

    @include('payments/widgets/productDetail')
    @include('payments/widgets/refund')

@endsection