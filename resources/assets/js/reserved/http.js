/****************************************************************************************************
 *
 *      Architekt.module.Http: Asynchronous HTTP request module
 *
 ****************************************************************************************************/
Architekt.module.reserv('Http', function(options) {
	var printLog = function() {};
	var printWarn = function() {};
	var printError = function() {};

	Architekt.event.on('ready', function() {
		printLog = Architekt.module.Printer.log;
		printWarn = Architekt.module.Printer.warn;
		printError = Architekt.module.Printer.error;
	});

	//AJAX REQUEST function
	//Requres console.js
	//ajaxRequest({ dataObject, headers, dataType, url, success, error, complete, after })
	function request(data) {
		if (typeof data !== 'object' || typeof data.url === 'undefined') return false;
		
		var _ajax_work = true;

		var dataObject = typeof data.data === 'object' ? data.data : {};
		var headers = typeof data.headers !== 'undefined' ? data.headers : {};
		var method = typeof data.method !== 'undefined' ? data.method : 'post';
		var dataType = typeof data.dataType !== 'undefined' ? data.dataType : 'json';
		var url = typeof data.url !== 'undefined' ? data.url : '';
		var suc = typeof data.success === "function" ? data.success : function () { };
		var err = typeof data.error === "function" ? data.error : function () { };
		var comp = typeof data.complete === "function" ? data.complete : function () { };
		var delayed = typeof data.delayed === 'function' ? data.delayed : function() { };
		var after = typeof data.after === "function" ? data.after : function () { };
		var timeout = typeof data.timeOut !== 'undefined' ? +data.timeOut : 10000;	//10sec
		
		printLog('Architekt.module.Http: send HTTP request...');
		printLog(method.toUpperCase() + ' ' + url);
		printLog('header: ' + JSON.stringify(headers) + ', data: ' + JSON.stringify(dataObject));

		$.ajax({
			timeout: timeout,
			url: url,
			data: dataObject,
			headers: headers,
			'type': method,
			dataType: dataType,
			success: function (result) {
				printLog('Architekt.module.Http: Http sent success.');
				printLog(method.toUpperCase() + ' ' + url);
				printLog(JSON.stringify(result));
				suc(result);
			},
			error: function (errorObject, textStatus, errorThrown) {
				printError('Architekt.module.Http: error detected');

				textStatus = textStatus || "";
				errorText = errorThrown || "";


				var responseObject = {};	//object for actual throw into the error callback

				responseObject.message = textStatus;
				responseObject.description = errorThrown;
				responseObject.statusCode = errorObject.status;
				responseObject.response = errorObject.responseJSON || {};


				//print variables for debug
				printLog(method.toUpperCase() + ' ' + url);
				printLog(responseObject.statusCode + ' ' + errorText);
				printLog('Sent Header: ' + JSON.stringify(headers));
				printLog('Sent Data: ' + JSON.stringify(dataObject));
				printLog('Status: ' + JSON.stringify(textStatus));
				printLog('Reponse: ' + JSON.stringify(responseObject));
				
				err(responseObject);
			},
			complete: function () {
				_ajax_work = false;
				printLog('Architekt.module.Http: request over.');
				comp();
				after();
			}
		});
		
		//Give notice if processing take too long
		setTimeout(function() {
			if(_ajax_work) {
				printLog('Architekt.module.Http: request taking too long ( >=5000ms )');
				delayed();	//call delayed callback
			}
		}, 5000);
	}

	function get(data) {
		data.method = 'get';
		request(data);
	}

	function post(data) {
		data.method = 'post';
		request(data);
	}


	return {
		//Architekt.module.Http.request(): Request HTTP
		request: request,
		//Architekt.module.Http.get(): Request GET
		get: get,
		//Architekt.module.Http.post(): Request POST
		post: post,
	};
});