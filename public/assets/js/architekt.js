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

    //Media object
    this.media = new function () {
        //Architekt.media.Image()
        this.Image = function (options) {
            var self = this;

            options = typeof options === 'object' ? options : {};
            this.src = typeof options.src !== 'undefined' ? options.src : false;

            //Make events
            this.event = new Architekt.EventEmitter(['load']);

            this.data = new Image();
            this.data.src = this.src;
            this.data.addEventListener('load', function () {
                self.event.fire('load');
            });
        };
        this.Image.prototype.reload = function () {
            var self = this;

            this.data.removeEventListener('load');

            this.data = new Image();
            this.data.src = this.src;
            this.data.addEventListener('load', function () {
                self.event.fire('load');
            });
            return this;
        };
        //Architekt.media.Audio()
        this.Audio = function (options) {
            var self = this;

            options = typeof options === 'object' ? options : {};
            this.src = typeof options.src !== 'undefined' ? options.src : false;
            this.playable = false;

            //Make events
            this.event = new Architekt.EventEmitter(['load','ended']);

            this._loadFired = false;

            this.data = new Audio();
            this.data.src = this.src;
            this.data.addEventListener('canplaythrough', function () {
                this.playable = true;

                if (self._loadFired) return false;
                self._loadFired = true;
                self.event.fire('load');
            });
            this.data.addEventListener('ended', function () {
                self.event.fire('ended');
            });
        };
        this.Audio.prototype.reload = function () {
            var self = this;

            this.data.removeEventListener('canplaythrough');
            this.data.removeEventListener('ended');
            this.playable = false;

            this.data = new Audio();
            this.data.src = this.src;
            this.data.addEventListener('canplaythrough', function () {
                this.playable = true;

                if (self._loadFired) return false;
                self._loadFired = true;
                self.event.fire('load');
            });
            this.data.addEventListener('ended', function () {
                self.event.fire('ended');
            });
            return this;
        }
        this.Audio.prototype.play = function () {
            this.data.play();
            return this;
        };
        this.Audio.prototype.pause = function () {
            this.data.pause();
            return this;
        };
        this.Audio.prototype.stop = function () {
            this.data.currentTime = 0;
            this.data.pause();
            return this;
        };
    };

    //Architekt.EventEmitter(): Create event attributes
    this.EventEmitter = function (events) {
        var self = this;
        this.list = {};
        this.checkOnce = {};

        if (typeof events === 'object' && events.length !== 'undefined')
            for (var i = 0, len = events.length; i < len; i++) {
                var eventName = events[i].toLowerCase();
                if (eventName.substr(0, 2) !== 'on') eventName = 'on' + eventName;

                this.list[eventName] = [];
            }

        //add basic events
        (function addBasicEvents() {
            //Error event for error handling while firing event
            self.list.onerror = [];
        })();

        //normalize event name: if name doen't have 'on', attach it at front.
        function normalize(eventName) {
            eventName = eventName.toLowerCase() || eventName;
            if (eventName.substr(0, 2) !== 'on') eventName = 'on' + eventName;

            return eventName;
        };
                
        //Architekt.EventEmitter.on(string type, function func): Add event
        this.on = function (type, func) {
            type = normalize(type);
            
            var _el = this.list[type];
            if (typeof _el === "undefined")
                throw new Error('Architekt.js: Unknown event ' + type);

            _el.push(func);
            return this;
        };
        //Architekt.EventEmitter.off(string type): Remove events
        this.off = function (type) {
            type = normalize(type);

            var _el = this.list[type];
            if (typeof _el === "undefined")
                throw new Error('Architekt.js: Unknown event ' + type);

            _el = [];
            return this;
        };
        //Architekt.EventEmitter.fire(string type, object eventArgument): Fire event
        this.fire = function (type, eventArgument) {
            type = normalize(type);

            var _el = this.list[type];

            if (typeof _el === "undefined")
                throw new Error('Architekt.js: Unknown event ' + type);

            //fire event
            for (var i = 0, len = _el.length; i < len; i++) {
                try {
                    _el[i](eventArgument);
                }
                catch(error) {
                    if(typeof error === 'object')
                        console.error(error.stack);
                    else
                        console.error(error);

                    //fire error event
                    this.fire('onerror', error);
                }
            }

            return this;
        };
        //Architekt.EventEmitter.trigger(string type, object eventArgument): Alias of EventEmitter.fire
        this.trigger = function(type, eventArgument) {
            return this.fire(type, eventArgument);
        };
        //Architekt.EventEmitter.fireOnce(string type, object eventArgument): Fire event only once
        this.fireOnce = function(type, eventArgument) {
            var self = this;

            type = normalize(type);

            var _el = this.list[type];

            if (typeof _el === "undefined")
                throw new Error('Architekt.js: Unknown event ' + type);

            //if already fired, don't fire events
            if(typeof self.checkOnce[type] !== 'undefined') return this;

            self.checkOnce[type] = true;

            //fire event
            for (var i = 0, len = _el.length; i < len; i++) {
                try {
                    _el[i](eventArgument);
                }
                catch(error) {
                    if(typeof error === 'object')
                        console.error(error.stack);
                    else
                        console.error(error);

                    //fire error event
                    this.fire('onerror', error);
                }
            }

            return this;
        };
        //Architekt.EventEmitter.triggerOnce(string type, object eventArgument): Alias of EventEmitter.fireOnce
        this.triggerOnce = function(type, eventArgument) {
            return this.fireOnce(type, eventArgument);
        };
        //Architekt.EventEmitter.reset(string type): Reset the "fire once" event
        this.reset = function(type) {
            type = normalize(type);

            delete this.list[type];
            return this;
        };
        //Architekt.EventEmitter.register(string eventName): Add new event
        this.register = function(eventName) {
            eventName = normalize(eventName);

            if(typeof this.list[eventName] !== 'undefined')
                throw new Error('Architekt.js: Failed register event ' + eventName + '. Event already exists.');

            this.list[eventName] = [];
            return this;
        };
        //Architekt.EventEmitter.unregister(string eventName): Remove event
        this.unregister = function(eventName) {
            eventName = normalize(eventName);

            if(typeof this.list[eventName] === 'undefined')
                throw new Error('Architekt.js: Failed unregister event ' + eventName + '. Event does not exists.');

            delete this.list[eventName];
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
    var reservedModules = {};
    var moduleList = [];
    var modulesLoaded = 0;
    var modulesMounted = 0;

    //function for wait and load dependency module
    function loadDependencyModule(moduleObject) {
        var deps = moduleObject.deps;
        var loaded = 0;

        //loop and check dependencies are loaded
        //this loop is looping dependency list
        for(var i = 0, len = deps.length; i < len; i++) {
            var dependency = deps[i];

            //this loop is looping loaded modules
            for(var j = 0, lenModules = moduleList.length; j < lenModules; j++) {
                if(dependency === moduleList[j].name) {
                    loaded++;
                    break;
                }
            }
        }

        //if all modules loaded, actually mount
        if(loaded >= deps.length) {
            mountModule(moduleObject);
        }
        //else, wait more 10 ms.
        else {
            setTimeout(function() {
                loadDependencyModule(moduleObject);
            }, 10);
        }
    };
    function mountModule(moduleObject) {
        var name = moduleObject.name;
        var version = moduleObject.version;
        var deps = moduleObject.deps;
        var moduleConstructor = moduleObject.moduleConstructor;

        Architekt.module[name] = new moduleConstructor();

        moduleList.push({
            name: name,
            version: version,
            deps: deps,
        });

        modulesMounted++;

        if(modulesMounted >= modulesLoaded) {
            console.log('Architekt.js: Ready to go.');
            console.log(JSON.stringify(self.info));

            Architekt.event.fire('onready');
        }
    }

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
                    modulesLoaded = 0;
                    callback();
                }
                else {
                    modulesLoaded = _num;

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
                modulesLoaded++;
                
                Architekt.loadScript(url, function () {
                    console.log('Architekt.js: Module mounted ' + url);
                    callback();
                });    
            }
            
            return this;
        },
        //Architekt.module.mount(string moduleName, function moduleConstructor): Mount a module
        //Architekt.module.mount(object moduleInfo, function moduleConstructor): Mount a module with specified info
        mount: function (moduleInfo, moduleConstructor) {
            //if first argument is string, it means it is name of the module.
            if(typeof moduleInfo === 'string') {
                var moduleName = moduleInfo;

                if (typeof this[moduleName] !== 'undefined')
                    throw new Error(moduleName + ' Module already mounted.');

                mountModule({
                    name: moduleName,
                    version: 0,
                    deps: [],
                    moduleConstructor: moduleConstructor,
                });
            }
            //if first argument is object, it contains module info
            else if(typeof moduleInfo === 'object') {
                var moduleName = moduleInfo.name || '';
                var moduleVersion = moduleInfo.version || '';
                var dependencies = moduleInfo.deps || [];

                if (typeof this[moduleName] !== 'undefined')
                    throw new Error(moduleName + ' module already mounted.');

                //is module has dependencies?
                if(dependencies.length > 0) {
                    loadDependencyModule({
                        name: moduleName,
                        version: moduleVersion,
                        deps: dependencies,
                        moduleConstructor: moduleConstructor,
                    });
                }
                //else, just load
                else {
                    mountModule({
                        name: moduleName,
                        version: moduleVersion,
                        deps: dependencies,
                        moduleConstructor: moduleConstructor,
                    });
                }
            }
            else {
                throw new Error('Architekt.js: tried unsupported module mounting. first argument must be string or object.');
            }

            return this;
        },
        //Architekt.module.reserv(string moduleName, function moduleConstructor): Reserv a module
        //Architekt.module.reserv(object moduleInfo, function moduleConstructor): Reserv a module with specified info
        reserv: function(moduleInfo, moduleConstructor) {
            var moduleName;

            if(typeof moduleInfo === 'string') {
                moduleName = moduleInfo;
            }
            else if(typeof moduleInfo === 'object') {
                if(typeof moduleInfo.name !== 'undefined')
                    moduleName = moduleInfo.name;
                else
                    throw new Error('Architekt.js: moduleInfo object must contains module name.');
            }
            else
                throw new Error('Architekt.js: tried unsupported module mounting. first argument must be string or object.');

        	reservedModules[moduleName] = {
                moduleInfo: moduleInfo,
                moduleConstructor: moduleConstructor,
            };
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
            //count how many modules
            for(var key in reservedModules) 
                modulesLoaded++;

			for(var key in reservedModules) {
				Architekt.module.mount(reservedModules[key].moduleInfo, reservedModules[key].moduleConstructor);
			}
		}, 0);
	};

	//Event definitions
    this.event = new this.EventEmitter([ 'onready', 'onresize', 'onscroll', 'onpreparing' ]);
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

    Architekt.event.fire('onresize', e);
}).on(window, 'scroll', function(e) {
    Architekt.event.fire('onscroll', e);
}).on(window, 'error', function(err) {
    var extra = !err.error ? '' : '' + err.error;
    extra += !err.lineno ? '' : ' at ' + err.filename + ' Line ' + err.lineno;

    console.error(extra);
    Architekt.event.fire('onerror', err);

    err.preventDefault();
    err.stopPropagation();
    err.stopImmediatePropagation();
    return false;
});
//# sourceMappingURL=architekt.js.map
