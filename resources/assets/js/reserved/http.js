/****************************************************************************************************
 *
 *      Architekt.module.Http: Asynchronous HTTP request module
 *
 ****************************************************************************************************/
Architekt.module.reserv('Http', function(options) {
	var log = function() {};
	var warn = function() {};
	var error = function() {};

	Architekt.event.on('ready', function() {
		log = Architekt.module.Printer.log;
		warn = Architekt.module.Printer.warn;
		error = Architekt.module.Printer.error;
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
		
		log('Architekt.module.Http: send HTTP request...');
		log(method.toUpperCase() + ' ' + url);
		log('header: ' + JSON.stringify(headers) + ', data: ' + JSON.stringify(dataObject));

		$.ajax({
			timeout: timeout,
			url: url,
			data: dataObject,
			headers: headers,
			'type': method,
			dataType: dataType,
			success: function (result) {
				log('Architekt.module.Http: Http sent success.');
				log(method.toUpperCase() + ' ' + url);
				log(JSON.stringify(result));
				suc(result);
			},
			error: function (response, status, error) {
				error('Architekt.module.Http: error detected');

				response = response || {};
				status = status || {};


				var responseText = false;
				if(typeof response.responseText !== 'undefined') {
					responseText = JSON.parse(response.responseText);
				}
				else {
					responseText = { message: 'undefined' };
				}
				
				//Check timeout
				if(status === 'timeout') responseText.error = 'timeout';


				log(method.toUpperCase() + ' ' + url);
				log('Sent Header: ' + JSON.stringify(headers));
				log('Sent Data:' + JSON.stringify(dataObject));
				log('Response: ' + JSON.stringify(response));

				var statusCode = parseInt(error.status);

				if(statusCode >= 400 && statusCode < 500) {
					responseText.error = 'clientError';
					response.status = statusCode;
				}
				else {
					log('Code: ' + response.status);
					log('Message: ' + response.responseText);
					log('Error: ' + error);	
				}
				
				err(responseText, response.status)
			},
			complete: function () {
				_ajax_work = false;
				log('Architekt.module.Http: request over.');
				comp();
				after();
			}
		});
		
		//Give notice if processing take too long
		setTimeout(function() {
			if(_ajax_work) {
				log('Architekt.module.Http: request taking too long ( >=5000ms )');
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