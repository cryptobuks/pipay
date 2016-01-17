Architekt.module.reserv('AsyncService', function () {
	var serviceStarted = false;
	var services = [];              //XMLHttpRequest container
	var serviceHandlers = [];       //Actual setInterval handlers
	var serviceId = 0;              //Unique ID for services
	
	var serviceGroups = [];
	var serviceGroupId = 0;
    
    //asyncService.ServiceGroup(groupName: as string, { activated: as boolean, once: as boolean }) : Create a Service Group
	function ServiceGroup(groupName, options) {
		options = typeof options === 'object' ? options : {};
		this.groupName = typeof groupName !== 'undefined' ? groupName : serviceGroupId;
		this._activated = typeof options.activated !== 'undefined' ? !!options.activated : false;
		this.once = typeof options.once !== 'undefined' ? !!options.once : false;
		
		this.groupId = serviceGroupId++;
		serviceGroups.push(this);
	};
	ServiceGroup.prototype.activate = function() {
		this._activated = true;
		return this;
	};
	ServiceGroup.prototype.deactivate = function() {
		this._activated = false;
		return this;
	};
	ServiceGroup.prototype.activated = function() {
		return this._activated;
	};
	
	//asyncService.XMLHttpRequest({ activated: as boolean, duration: as integer, xhttpOptions: as object, callback: as function, errorCallback: as function, serviceGroups: as Array }) : Create XMLHttpRequest Service.
	function XMLHttpRequest(options) {
		options = typeof options === 'object' ? options : {};
		this.serviceName = typeof options.serviceName !== 'undefined' ? options.serviceName : serviceId;
		this.activated = typeof options.activated !== 'undefined' ? !!options.activated : true;
		this.duration = typeof options.duration !== 'undefined' ? parseInt(options.duration) : (1000 * 60 * 5);    //default = 5min
		this.callback = typeof options.callback === 'function' ? options.callback : function() {};
		this.errorCallback = typeof options.errorCallback === 'function' ? options.errorCallback : function() {};
		this.xhttpOptions = typeof options.xhttpOptions === 'object' ? options.xhttpOptions : {};
		this.serviceGroups = typeof options.serviceGroups === 'object' ? options.serviceGroups : false;
		this.log = typeof options.log !== 'undefined' ? !!options.log : false;
		
		if(!this.serviceGroups) {
			console.error('asyncService: Service requires ServiceGroup.');
			return false;
		}
		
		this.xhttpOptions.method !== 'undefined' ? this.xhttpOptions.method : 'post';
		this.xhttpOptions.dataType !== 'undefined' ? this.xhttpOptions.dataType : 'json';
		
		this.serviceId = serviceId++;
		this.serviceType = 'XMLHttpRequest';
		services.push(this);
	};
	//XMLHttpRequest.execute: Execute service
	XMLHttpRequest.prototype.execute = function() {
		var self = this;
		var xhttpOptions = this.xhttpOptions;
		var headers = xhttpOptions.headers || {};
		var url = xhttpOptions.url || '';
		var data = xhttpOptions.data || {};
		var method = xhttpOptions.method;
		var dataType = xhttpOptions.dataType;
		
		ajaxRequest({
			headers: headers,
			url: url,
			method: method,
			data: data,
			dataType: dataType,
			success: function(data) {
				self.callback(data);
			},
			error: function(responseText, status) {
				self.errorCallback(responseText, status, self);
			}
		});
	};
	
	//asyncService.ApplicationService({ duration: as integer, procedure: as function, serviceGroups: as Array }) : Create Application Service.
	function ApplicationService (options) {
		options = typeof options === 'object' ? options : {};
		this.serviceName = typeof options.serviceName !== 'undefined' ? options.serviceName : serviceId;
		this.duration = typeof options.duration !== 'undefined' ? parseInt(options.duration) : (1000 * 60 * 5);    //default = 5min
		this.procedure = typeof options.procedure === 'function' ? options.procedure : function() {};
		this.serviceGroups = typeof options.serviceGroups === 'object' ? options.serviceGroups : false;
		this.log = typeof options.log !== 'undefined' ? !!options.log : false;
		
		if(!this.serviceGroups) {
			console.error('asyncService: Service requires ServiceGroup.');
			return false;
		}

		this.serviceId = serviceId++;
		this.serviceType = 'applicationservice';
		services.push(this);
	};
	//ApplicationService.execute: Execute service
	ApplicationService.prototype.execute = function() {
		this.procedure();
	};
	
	//asyncService.start() : Start the services
	function start() {
		serviceStarted = true;
		console.log('asyncService: Service started at ' + new Date().toString());
		
		for(var i = 0, len = services.length; i < len; i++) {
			var service = services[i];
			
			(function(service) {
				if(typeof service.serviceType === 'undefined') return;
				
				if(typeof service.serviceName === 'number')
					console.log('[Service - ' + service.serviceType + '] Service' + service.serviceId + ' registered.');
				else
					console.log('[Service - ' + service.serviceType + '] ' + service.serviceName + ' registered.');
					
				//JavaScript timer functions does not provide first start and looping.
				//So make a function that you wanna do some tasks,
				//and execute it immediately, and send to the timer function.
				function _do_service () {
					//Check service groups!
					var halt = false;
					var serviceGroups = service.serviceGroups;
					
					for(var j = 0, sgLen = serviceGroups.length; j < sgLen; j++) {
						if(!serviceGroups[j].activated()) {
							halt = true;
							break;
						}
					}
						
					if(halt) return;
					
					if(service.log) {
						var serviceName = typeof service.serviceName === 'number' ? 'Service' + service.serviceId : service.serviceName;
						console.log('[Service - ' + service.serviceType + '] ' + serviceName + ' is working at ' + new Date().toString());	
					}
					
					service.execute();
				}
				
				//Execute immediately!
				_do_service();
				serviceHandlers[i] = setInterval(function() {
					_do_service();
				}, service.duration);
			})(service);
		}
	};
	//AsyncService.stop() : Stop all services
	function stop() {
		console.log('asyncService: Stop all services...');
		
		for(var i = 0, len = services.length; i < len; i++) {
			var service = services[i];
			
			if(typeof service.serviceName === 'number')
				console.log('[Service - ' + service.serviceType + '] Service' + service.serviceId + ' stopped.');
			else
				console.log('[Service - ' + service.serviceType + '] ' + service.serviceName + ' stopped.');
			
			clearInterval(serviceHandlers[i]);
		}
		
		serviceStarted = false;
		console.log('asyncService: All Services stopped at ' + new Date().toString());
	};
	//AsyncService.getServiceById(serviceId) : Get the service by specified service ID
	function getServiceById(serviceId) {
		return services[serviceId];
	};
	
	//Accessible properties
    return {
        ServiceGroup: ServiceGroup,
		XMLHttpRequest: XMLHttpRequest,
		ApplicationService: ApplicationService,
		start: start,
		stop: stop,
		getServiceById: getServiceById,
    };
});