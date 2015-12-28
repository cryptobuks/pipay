@extends('app')
@section('content')
    
    <script>
        //Payment list control
        Architekt.event.on('ready', function() {
            var Notice = Architekt.module.Widget.Notice;
            var Http = Architekt.module.Http;

            var currentUrl = '{{ Request::url() }}';
            var paymentTable = new Architekt.module.DataTable({
                pagenate: true,
                readOnly: false,
            });
            var hasNext = false;    //check for next page exists
            var pagePer = 15;
            
            //create DataTable component
            paymentTable.setHeaderColumn(['주문번호', '결제시각', '상품명', '상품가격', '결제상태', 'Pi 결제금액']);
            paymentTable.appendTo($('#pi_list'));    //append to body

            //first draw
            paymentTable.render({ animate: true });

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
                        'page': paymentTable.getCurrentPage()
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
                    noMoreData();
                }
                else {
                    //if length of list items are shorter than number of paging per that means no more page.
                    //but if len = pagePer, it can be has next page so be cautious!
                    hasNext = (len < pagePer);

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
            }

            //fetch payment list data from server and update
            function getPaymentDataAndUpdate() {
                paymentTable.resetColumns();

                getPaymentData(function(dataObject) {
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
                getData({
                    url: [currentUrl, id].join("/"),
                    callback: function(dataObject) {
                        if(typeof callback === 'function') callback(dataObject);
                    }
                });
            }

            //update product info on page
            function updateProduct(dataObject) {
                new Notice({
                    text: JSON.stringify(dataObject)
                });
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
                getPaymentDataAndUpdate();
                return false;
            });

            //export as excel
            $('#exportExcel').click(function() {

                return false;
            });



            //get data and update!
            getPaymentDataAndUpdate(paymentTable.getCurrentPage());
        });
    </script>

    <div id="pi_top_space"></div>

    <div id="pi_payment">
        <div id="pi_list" class="pi-container">
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