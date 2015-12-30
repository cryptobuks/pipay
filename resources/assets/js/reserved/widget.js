/* Widget Module */
Architekt.module.reserv('Widget', function(options) {
	var body = $('body');

	var defaultText = {
		ok: 'Ok',
		confirm: 'Confirm',
		close: 'Close',
		cancel: 'Cancel',
	};

	//widgetBase constructor
	function widgetBase(options) {
		options = typeof options === 'object' ? options : {};
		this.text = typeof options.text !== 'undefined' ? options.text : '';
		this.controlObject = null;
		this.callback = typeof options.callback === 'function' ? options.callback : function() {};
		this.noCallback = typeof options.noCallback === 'function' ? options.noCallback : function() {};
		this.okText = typeof options.okText !== 'undefined' ? options.okText : defaultText.ok;
		this.confirmText = typeof options.confirmText !== 'undefined' ? options.confirmText : defaultText.confirm;
		this.closeText = typeof options.closeText !== 'undefined' ? options.closeText : defaultText.close;
		this.cancelText = typeof options.cancelText !== 'undefined' ? options.cancelText : defaultText.cancel;

		this.destruct = function() {
			var co = this.controlObject;

			co.fadeOut(200, function() {
				co.remove();
				co = null;
			});

			return this;	
		};
	}

	//Architekt.module.Widget.Notice(): Create a Notice widget
	function Notice(options) {
		Architekt.object.link(widgetBase, this, options);	//Link widgetBase with argument options

		var self = this;

		this.controlObject = $('<div></div>').addClass('architekt-widget-background');

		var container = $('<div></div>').addClass('architekt-widget-container').appendTo(this.controlObject);	
		var textObject = $('<p></p>').text(this.text).appendTo(container);
		var buttonContainer = $('<div></div>').addClass('architekt-widget-buttonContainer').appendTo(container);

		$('<button></button>').addClass('architekt-widget-button architekt-theme-confirm').click(function() {
			self.callback();
			self.destruct();
		}).text(this.okText).appendTo(buttonContainer);

		//Append to body
		this.controlObject.appendTo(body);

		//Fancy scale up animation
		setTimeout(function() {
			container.addClass('on');
		}, 25);
	}

	//Architekt.module.Widget.Confirm(): Create a Confirm widget
	function Confirm(options) {
		Architekt.object.link(widgetBase, this, options);	//Link widgetBase with argument options

		var self = this;

		this.controlObject = $('<div></div>').addClass('architekt-widget-background');

		var container = $('<div></div>').addClass('architekt-widget-container').appendTo(this.controlObject);	
		var textObject = $('<p></p>').text(this.text).appendTo(container);
		var buttonContainer = $('<div></div>').addClass('architekt-widget-buttonContainer').appendTo(container);

		$('<button></button>').addClass('architekt-widget-button').click(function() {
			self.noCallback();
			self.destruct();
		}).text(this.closeText).appendTo(buttonContainer);

		$('<button></button>').addClass('architekt-widget-button architekt-theme-confirm').click(function() {
			self.callback();
			self.destruct();
		}).text(this.confirmText).appendTo(buttonContainer);

		//Append to body
		this.controlObject.appendTo(body);

		//Fancy scale up animation
		setTimeout(function() {
			container.addClass('on');
		}, 25);
	}

	return {
		Notice: Notice,
		Confirm: Confirm,
		setDefaultText: function(newTexts) {
			if(typeof newTexts.ok !== 'undefined') defaultText.ok = newTexts.ok;
			if(typeof newTexts.confirm !== 'undefined') defaultText.confirm = newTexts.confirm;
			if(typeof newTexts.close !== 'undefined') defaultText.close = newTexts.close;
			if(typeof newTexts.cancel !== 'undefined') defaultText.cancel = newTexts.cancel;
		}
	};
});