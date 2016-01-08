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
                var failed = typeof options.failed === 'function' ? options.failed : function() {};
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
                        failed();
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