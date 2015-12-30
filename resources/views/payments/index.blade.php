@extends('app')
@section('content')
    
    <script>
        //Payment list control
        Architekt.event.on('ready', function() {
            var Notice = Architekt.module.Widget.Notice;
            var Http = Architekt.module.Http;
            var CustomWidget = Architekt.module.CustomWidget;

            var currentUrl = '{{ Request::url() }}';
            var paymentTable = new Architekt.module.DataTable({
                pagenate: true,
                readOnly: false,
            });
            var hasNext = false;    //check for next page exists
            var pagePer = parseInt('{{ $pagePer }}');
            var filter = "";
            
            pagePer = isNaN(pagePer) ? 10 : pagePer;    //if pagePer from the server is not a number, use 10 instead.
            var isRefresh = false;                      //if refresh and no more page, no hasNext alert.
            
            //create DataTable component
            paymentTable.setHeaderColumn(['주문번호', '결제시각', '상품명', '상품가격', '결제상태', 'Pi 결제금액']);
            paymentTable.appendTo($('#pi_list'));    //append to body

            //first draw
            paymentTable.render({ animate: true });


            //filter
            var filterDom = $('#pi_payment_filter > div');
            filterDom.click(function() {
                var _f = $(this).attr('data-filter');

                if(_f === "all" && filter === "") return;
                else if(_f === filter) return;

                switch(_f) {
                    case "confirmed":
                        _f = _f;
                        break;
                    default:
                        _f = "";
                        break;
                }

                filter = _f;
                filterDom.removeClass('on');
                $(this).addClass('on');

                $('#refresh').trigger('click'); //force refresh
            });


            /**************************************************************************************
             *
             *
             *                                  pure functions
             *
             *
             **************************************************************************************/

            //HTTP get data
            var _isFetching = false;
            function getData(options) {
                if(_isFetching) return;

                options = typeof options === 'object' ? options : {};
                var url = typeof options.url !== 'undefined' ? options.url : '';
                var callback = typeof options.callback === 'function' ? options.callback : function() {};
                var complete = typeof options.complete === 'function' ? options.complete : function() {};
                var data = typeof options.data === 'object' ? options.data : {};

                _isFetching = true;

                Http.get({
                    url: url,
                    data: data,
                    success: function(dataObject) {
                        callback(dataObject);
                    },
                    error: function(text, status, error) {
                        new Notice({
                            text: JSON.stringify(error),
                        });
                    },
                    complete: function() {
                        _isFetching = false;
                        complete();
                    }
                });    
            }

            //no more data?
            function noMoreData() {
                new Notice({
                    text: '마지막 페이지입니다.'
                });
            }


            /**************************************************************************************
             *
             *
             *                              payment list functions
             *
             *
             **************************************************************************************/

            //get payment data list
            function getPaymentData(callback) {
                getData({
                    url: currentUrl,
                    data: {
                        'page': paymentTable.getCurrentPage(),
                        'pagePer': pagePer,
                        'filter': filter,
                    },
                    callback: function(dataObject) {
                        if(typeof callback === 'function') callback(dataObject);
                    }
                });
            }

            //update DataTable
            function updatePaymentTable(dataObject) {
                var len = dataObject.length;

                //0 item? nah, no more page.
                if(len === 0) {
                    hasNext = false;
                    paymentTable.setPage(paymentTable.getCurrentPage() - 1);    //decrease page numb

                    if(!isRefresh) noMoreData();    //if the refresh, don't show up.
                }
                else {
                    paymentTable.resetColumns();

                    //if length of list items are shorter than number of paging per that means no more page.
                    //but if len = pagePer, it can be has next page so be cautious!

                    hasNext = (len >= pagePer);

                    for(var i = 0; i < len; i++) {
                        var data = dataObject[i];
                        var parsedArray = [];

                        for(var key in data) {
                            if(key === 'pi_amount_received') {
                                parsedArray.push(data['pi_amount_received'] + ' / ' + data['pi_amount']);
                                break;
                            }

                            parsedArray.push(data[key]);
                        }

                        paymentTable.addColumn(parsedArray);
                    }    

                    //on update only has data
                    paymentTable.render({ animate: true, updateHeader: false });
                }

                isRefresh = false;
            }

            //fetch payment list data from server and update
            function getPaymentDataAndUpdate() {
                paymentTable.lock({
                    loading: true
                });

                getPaymentData(function(dataObject) {
                    paymentTable.unlock();
                    updatePaymentTable(dataObject);
                });
            }


            /**************************************************************************************
             *
             *
             *                            details of specific product
             *
             *
             **************************************************************************************/

             //get specified product info
            function getProduct(id, callback) {
                paymentTable.lock({
                    loading: true
                });

                getData({
                    url: [currentUrl, id].join("/"),
                    callback: function(dataObject) {
                        if(typeof callback === 'function') callback(dataObject);
                    },
                    complete: function() {
                        paymentTable.unlock();
                    }
                });
            }

            //update product info on page
            function updateProduct(dataObject) {
                productDetailWidget.setData(dataObject).render().show();
            }


            /* Event handlers */
            //item click -> show detai;
            paymentTable.event.on('itemclick', function(e) {
                var idx = e.clickedIndex;
                var column = e.column;

                getProduct(column[0], function(dataObject) {
                    updateProduct(dataObject);
                });
            });

            //prev
            paymentTable.event.on('previous', function(e) {
                var page = e.currentPage;

                if(page === 1) {
                    new Notice({
                        text: '첫 번째 페이지입니다.'
                    });
                }
                else {
                    paymentTable.setPage(--page);
                    getPaymentDataAndUpdate();
                }
            });

            //next
            paymentTable.event.on('next', function(e) {
                var page = e.currentPage;

                if(!hasNext) {
                    noMoreData();
                }
                else {
                    paymentTable.setPage(++page);
                    getPaymentDataAndUpdate();                    
                }
            });

            //refresh
            $('#refresh').click(function() {
                isRefresh = true;
                getPaymentDataAndUpdate();
                return false;
            });

            //export as excel
            $('#exportExcel').click(function() {
                new Notice({
                    text: '준비 중입니다.'
                });
                return false;
            });


            //get data and update!
            $('#refresh').trigger('click');


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
                        if(isNaN(data)) return '';

                        args = args || {};
                        symbol = args.symbol || 'KRW';   //default KRW

                        data = parseFloat(data).toFixed(1);
                        return [data, ' ', symbol.toUpperCase()].join('');
                    },
                    //print only if has value
                    printIfHasValue: function(data) {
                        return (!data || data === "") ? "" : data;
                    }
                },
                refund: function(dataObject) {
                    
                },
                receipt: function(dataObject) {
                    window.open('{{ url('/') }}/receipt/' + dataObject.token);
                },
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

@endsection