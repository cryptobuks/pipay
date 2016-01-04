			/**************************************************************************************
             *
             *
             *                              adapted functions
             *
             *
             **************************************************************************************/

            //get table data
            var filter = filter || '';
            
            //get table data with Http module
            function getTableData(callback) {
                getData({
                    url: requestUrl,
                    data: {
                        'page': dataTable.getCurrentPage(),
                        'pagePer': pagePer,
                        'filter': filter || '',
                    },
                    callback: function(dataObject) {
                        if(typeof callback === 'function') callback(dataObject);
                    }
                });
            }

            //update DataTable
            function updateDataTable(dataObject) {
                var len = dataObject.length;

                //0 item? nah, no more page.
                if(len === 0) {
                    hasNext = false;
                    dataTable.setPage(dataTable.getCurrentPage() - 1);    //decrease page numb

                    if(!isRefresh) noMoreData();    //if the refresh, don't show up.
                }
                else {
                    dataTable.resetColumns();

                    //if length of list items are shorter than number of paging per that means no more page.
                    //but if len = pagePer, it can be has next page so be cautious!

                    hasNext = (len >= pagePer);

                    for(var i = 0; i < len; i++) {
                        var result = [];

                        if(typeof filterFunc === 'function')    //has filter function?
                            result = filterFunc(dataObject[i]);
                        else
                            result = dataObject[i];

                        dataTable.addColumn(result);
                    }    

                    //on update only has data
                    dataTable.render({ animate: true, updateHeader: false });
                }

                isRefresh = false;
            }

            //fetch payment list data from server and update
            function getTableDataAndUpdate() {
                dataTable.lock({
                    loading: true
                });

                getTableData(function(dataObject) {
                    dataTable.unlock();
                    updateDataTable(dataObject);
                });
            }