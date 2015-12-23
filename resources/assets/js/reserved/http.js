/****************************************************************************************************
 *
 *      Architekt.module.Http: Asynchronous HTTP request module
 *
 ****************************************************************************************************/
Architekt.module.reserv('Http', function(options) {
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
		var after = typeof data.after === "function" ? data.after : function () { };
		
		console.log('********** Sending XMLHttpRequest **********');
		console.log(method.toUpperCase() + ' ' + url);
		console.log('header: ' + JSON.stringify(headers) + ', data: ' + JSON.stringify(dataObject));

		$.ajax({
			timeout: 20000, //maximum 20seconds to timeout
			url: url,
			data: dataObject,
			headers: headers,
			'type': method,
			dataType: dataType,
			success: function (result) {
				console.log('AJAX Scucess');
				console.log(method.toUpperCase() + ' ' + url);
				console.log(JSON.stringify(result));
				suc(result);
			},
			error: function (response, status, error) {
				var responseText = false;
				if(typeof response.responseText !== 'undefined') {
					responseText = JSON.parse(response.responseText);
				}
				else {
					responseText = { message: 'undefined' };
				}
				
				//Check timeout
				if(status === 'timeout') responseText.error = 'timeout';
				
				console.error('Error occured while AJAX requesting.');
				console.log(method.toUpperCase() + ' ' + url);
				console.log('Sent Header: ' + JSON.stringify(headers));
				console.log('Sent Data:' + JSON.stringify(dataObject));
				console.log('Response: ' + JSON.stringify(response));
				console.log('Code: ' + response.status);
				console.log('Message: ' + response.responseText);
				console.log('Error: ' + error);
				err(responseText, response.status)
			},
			complete: function () {
				_ajax_work = false;
				console.log('********** REQUEST OVER **********');
				comp();
				after();
			}
		});
		
		//Give notice if processing take too long
		setTimeout(function() {
			if(_ajax_work) {
				console.log('Process taking so long');
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