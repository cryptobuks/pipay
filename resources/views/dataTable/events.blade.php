            /* Event handlers */
            
            //prev
            dataTable.event.on('previous', function(e) {
                var page = e.currentPage;

                if(page === 1) {
                    new Notice({
                        text: '첫 번째 페이지입니다.'
                    });
                }
                else {
                    dataTable.setPage(--page);
                    getTableDataAndUpdate();
                }
            });

            //next
            dataTable.event.on('next', function(e) {
                var page = e.currentPage;

                if(!hasNext) {
                    noMoreData();
                }
                else {
                    dataTable.setPage(++page);
                    getTableDataAndUpdate();                    
                }
            });


            //refresh
            $('#refresh').click(function() {
                isRefresh = true;
                getTableDataAndUpdate();
                return false;
            });

            //export as excel
            $('#exportExcel').click(function() {
                window.open(Client.createUrl('payment/export'));
                return false;
            });