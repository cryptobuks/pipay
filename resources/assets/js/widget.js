var Widget = {};
var workingWidgets = 0;

$(document).ready(function () {
    var body = $('body');

    window.Widget = new function () {
        this.Notice = function (option) {
            var option = typeof option === 'object' ? option : {};
            var text = typeof option.text !== 'undefined' ? option.text : '';
            var headText = typeof option.headText !== 'undefined' ? option.headText : 'Pi-Pay';
            var buttonText = typeof option.buttonText !== 'undefined' ? option.buttonText : Locale.getString('buttonOk');
            var error = typeof option.error !== "undefined" ? !!option.error : false;
            var callback = typeof option.callback === "function" ? option.callback : function () { };
            
            var wrap = $('<div></div>').addClass('pi_widget_wrap');
            var controlObject = $('<div></div>').addClass('pi_widget').appendTo(wrap);

            var top = $('<div></div>').addClass('pi_widget_top').appendTo(controlObject);
            $('<h1></h1>').text(headText).appendTo(top);
            var buttonClose = $('<div></div>').addClass('pi_widget_top_close').text('×').click(function () {
                wrap.remove();
                wrap = null;
            }).appendTo(top);

            var content = $('<div></div>').addClass('pi_widget_content').appendTo(controlObject);
            $('<p></p>').text(text).appendTo(content);

            var bottom = $('<div></div>').addClass('pi_widget_bottom').appendTo(controlObject);
            $('<button></button>').text(buttonText).click(function(){
                buttonClose.trigger('click');
                callback();
            }).appendTo(bottom);

            wrap.appendTo(body);

            setTimeout(function () {
                controlObject.addClass('pi_widget_effect');
            }, 25);

            if (error) {
                var wrap = wrap;
                var bg = ['rgba(2,117,189,0.5)', 'rgba(189, 40, 2, 0.5)'];
                var bgSel = 0;
                var count = 0;
                var duration = 50;

                (function errorAnimation() {
                    wrap.css('background-color', bg[bgSel]);
                    bgSel = +!bgSel;

                    if (count++ < 10) {
                        setTimeout(errorAnimation, duration);
                    }
                })();
            }
        };
        this.Confirm = function (option) {
            var option = typeof option === 'object' ? option : {};
            var text = typeof option.text !== 'undefined' ? option.text : '';
            var headText = typeof option.headText !== 'undefined' ? option.headText : 'Pi-Pay';
            var buttonText = typeof option.buttonText !== 'undefined' ? option.buttonText : Locale.getString('buttonOk');
            var buttonCancelText = typeof option.buttonCancelText !== "undefined" ? option.buttonCancelText : Locale.getString('buttonCancel');
            var error = typeof option.error !== "undefined" ? !!option.error : false;
            var callback = typeof option.callback === "function" ? option.callback : function () { };
            var noCallback = typeof option.noCallback === "function" ? option.noCallback : function () { };
            
            var wrap = $('<div></div>').addClass('pi_widget_wrap');
            var controlObject = $('<div></div>').addClass('pi_widget').appendTo(wrap);

            var top = $('<div></div>').addClass('pi_widget_top').appendTo(controlObject);
            $('<h1></h1>').text(headText).appendTo(top);
            var buttonClose = $('<div></div>').addClass('pi_widget_top_close').text('×').click(function () {
                wrap.remove();
                wrap = null;
            }).appendTo(top);

            var content = $('<div></div>').addClass('pi_widget_content').appendTo(controlObject);
            $('<p></p>').text(text).appendTo(content);

            var bottom = $('<div></div>').addClass('pi_widget_bottom').appendTo(controlObject);
            $('<button></button>').text(buttonText).click(function () {
                buttonClose.trigger('click');
                callback();
            }).appendTo(bottom);
            $('<button></button>').addClass('pi_widget_button_cancel').text(buttonCancelText).click(function () {
                buttonClose.trigger('click');
                noCallback();
            }).appendTo(bottom);

            wrap.appendTo(body);

            setTimeout(function () {
                controlObject.addClass('pi_widget_effect');
            }, 25);
        };
    };
});