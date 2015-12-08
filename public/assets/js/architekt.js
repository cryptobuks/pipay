if (typeof String.prototype.utf8Encode == 'undefined') {
    String.prototype.utf8Encode = function () {
        return unescape(encodeURIComponent(this));
    };
}
if (typeof String.prototype.utf8Decode == 'undefined') {
    String.prototype.utf8Decode = function () {
        try {
            return decodeURIComponent(escape(this));
        } catch (e) {
            return this; // invalid UTF-8? return as-is
        }
    };
}

//Crossbrowsing event handler
var events = {
	on: function(target, type, func) {
		if(window.addEventListener) {
			target.addEventListener(type, func);
		}
		else {
			type = type.toLowerCase();
			if(type.substr(0,2) !== 'on') type = 'on' + type;
			target.attachEvent(type, func);
		}

        return this;
	},
	off: function(target, type) {
		if(window.removeEventHandler) {
			target.removeEventHandler(type);
		}
		else {
			type = type.toLowerCase();
			if(type.substr(0,2) !== 'on') type = 'on' + type;
			target.detachEvent(type);
		}

        return this;
	}
};

//Architekt 1.0 Lite: based Non-UIComponent, Modular Framework
window.Architekt = new function ArchitektConstructor() {
	var self = this;

	//Framework info
    this.info = {
        version: '1.0 Lite',
    };

	//Device object: contain device info
    this.device = {
        width: 0,
        height: 0,
    };
    //Object object: helpers for object
    this.object = {
        extend: function (source) {
            var obj = Object.create(source);
            return obj;
        },
        link: function(baseObject, derivedObject, paramsObject) {
            baseObject.call(derivedObject, paramsObject);
            return this;
        },
    };
    //Array object: helpers for array
    this.array = {
        clone: function (arr) {
            return arr.slice(0);
        },
    };

    //Architekt.EventEmitter(): Create event attributes
    this.EventEmitter = function (events) {
        this.list = {};

        if (typeof events === 'object' && events.length !== 'undefined')
            for (var i = 0, len = events.length; i < len; i++) {
                var eventName = events[i].toLowerCase();
                if (eventName.substr(0, 2) !== 'on') eventName = 'on' + eventName;

                this.list[eventName] = [];
            }
                
        //Architekt.EventEmitter.on(string type, function func): Add event
        this.on = function (type, func) {
            type = type.toLowerCase();
            if (type.substr(0, 2) !== 'on') type = 'on' + type;
            
            var _el = this.list[type];
            if (typeof _el === "undefined")
                throw new Error('Architekt.js: Unknown event ' + type);

            _el.push(func);
            return this;
        };
        //Architekt.EventEmitter.off(string type): Remove events
        this.off = function (type) {
            type = type.toLowerCase();
            if (type.substr(0, 2) !== 'on') type = 'on' + type;

            var _el = this.list[type];
            if (typeof _el === "undefined")
                throw new Error('Architekt.js: Unknown event ' + type);

            _el = [];
            return this;
        };
        //Architekt.EventEmitter.fire(string type, object eventArgument): Fire event
        this.fire = function (type, eventArgument) {
            type = type.toLowerCase();
            if (type.substr(0, 2) !== 'on') type = 'on' + type;

            var _el = this.list[type];
            if (typeof _el === "undefined")
                throw new Error('Architekt.js: Unknown event ' + type);

            for (var i = 0, len = _el.length; i < len; i++) _el[i](eventArgument);
            return this;
        };
    };

    //Architekt.loadScript(string url, function callback): Load JavaScript Async
    this.loadScript = function(url, callback) {
        var _h = document.getElementsByTagName('head')[0];
        var _s = document.createElement('script');
        _s.type = 'text/javascript';
        _s.src = url;

        _s.addEventListener('readystatechange', callback);
        _s.addEventListener('load', callback);
        _s.addEventListener('error', function () {
            throw new Error('Failed to load script: ' + url);
        });

        _h.appendChild(_s);
        return this;
    };
    
    //Architekt.module.loadCSS(string url, function callback): Load a stylesheet
    //Architekt.module.loadCSS(array url, function callback): Load stylesheets
    this.loadCSS = function(url, callback) {
        callback = typeof callback === 'function' ? callback : function () { };

        function _loadCSS(url, callback) {
            var _h = document.getElementsByTagName('head')[0];
            var _s = document.createElement('link');
            _s.setAttribute('rel', 'stylesheet');
            _s.setAttribute('href', url);

            _s.addEventListener('readystatechange', callback);
            _s.addEventListener('load', callback);
            _s.addEventListener('error', function () {
                throw new Error('Failed to load css: ' + url);
            });

            _h.appendChild(_s);
        }

        var _loaded = 0, _num = 0;

        if (typeof url === 'object' && typeof url.length !== 'undefined') {
            _loaded = 0;
            _num = url.length;

            if (_num === 0) {
                callback();
            }
            else {
                for (var i = 0; i < _num; i++) {
                    _loadCSS(url[i], function () {
                        console.log('Architekt.js: Stylesheet loaded ' + url[_loaded]);

                        if (++_loaded >= _num) {
                            callback();
                        }
                    });
                }
            }
        }
        else {
            _loadCSS(url, function () {
                console.log('Architekt.js: Stylesheet mounted ' + url);
                callback();
            });
        }
        
        return this;
    };

    //Modular
    var reserved = {};
    this.module = {
        //Architekt.module.load(string url, function callback): Load a module
        //Architekt.module.load(array url, function callback): Load modules
        load: function(url, callback) {
            callback = typeof callback === 'function' ? callback : function () { };

            var _loaded = 0, _num = 0;
            
            if (typeof url === 'object' && typeof url.length !== 'undefined') {
                _loaded = 0;
                _num = url.length;
                
                if (_num === 0) {
                    callback();
                }
                else {
                    for (var i = 0; i < _num; i++) {
                        Architekt.loadScript(url[i], function () {
                            console.log('Architekt.js: Module mounted ' + url[_loaded]);

                            if (++_loaded >= _num) {
                                callback();
                            }
                        });
                    }
                }
            }
            else {
                Architekt.loadScript(url, function () {
                    console.log('Architekt.js: Module mounted ' + url);
                    callback();
                });    
            }
            
            return this;
        },
        //Architekt.module.mount(string moduleName, function moduleConstructor): Mount a module
        mount: function (moduleName, moduleConstructor) {
            if (typeof this[moduleName] !== 'undefined') throw new Error(moduleName + ' Module already mounted.');
            this[moduleName] = new moduleConstructor();
            return this;
        },
        //Architekt.module.reserv(string moduleName, function moduleConstructor): Reserv a module
        //Note that reserved modules are loaded after onmodulesready
        reserv: function(moduleName, moduleConstructor) {
        	if (typeof reserved[moduleName] !== 'undefined') throw new Error(moduleName + ' Module already reserved.');
        	reserved[moduleName] = moduleConstructor;
        	return this;
        },
    };


    //Architekt.setGPUAcceleration(domElement target): Add GPU Acceleration(only works with Webkit based browser)
    this.setGPUAcceleration = function (target) {
        target.style.transform = 'translateZ(0)';
        target.style.webkitTransform = 'translateZ(0);'
        target.style.msTransform = 'translateZ(0);'
        target.style.MozTransform = 'translateZ(0);'
        target.style.OTransform = 'translateZ(0);'
        
        //Repeating twice: There is a f**king but with just adding style. I spend lots of time to solve this, but gave up. F**k it.
        target.style.transform = 'translateZ(0)';
        target.style.webkitTransform = 'translateZ(0);'
        target.style.msTransform = 'translateZ(0);'
        target.style.MozTransform = 'translateZ(0);'
        target.style.OTransform = 'translateZ(0);'
        return this;
    };

	//Architekt.init(): Init Architekt
	this.init = function() {
		//Load reservated modules
		setTimeout(function() {
			for(var moduleName in reserved) {
				Architekt.module.mount(moduleName, reserved[moduleName]);
			}

            console.log('Architekt.js: Ready to go.');
            console.log(JSON.stringify(self.info));

			Architekt.event.fire('onready');
		}, 0);
	};

	//Event definitions
    this.event = new this.EventEmitter([ 'onready', 'onresize', 'onscroll', 'onmodulesloaded', 'onpreparing' ]);
};

//Make Architekt load when window loaded
events.on(window, 'load', function() {
    Architekt.device.width = window.innerWidth;
    Architekt.device.height = window.innerHeight;

    Architekt.event.fire('onpreparing');
	Architekt.init();
}).on(window, 'resize', function(e) {
    Architekt.device.width = window.innerWidth;
    Architekt.device.height = window.innerHeight;

    Architekt.event.fire('resize', e);
}).on(window, 'scroll', function(e) {
    Architekt.event.fire('scroll', e);
});
//# sourceMappingURL=architekt.js.map
