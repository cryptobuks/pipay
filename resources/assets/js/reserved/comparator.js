Architekt.module.reserv('Comparator', function(options) {
	var results = [];
	var texts = [];
	var startTime = null;
	var endTime = null;

	return {
		//Architekt.module.Comparator.start(): Begins measuring
		start: function() {
			results = [];
			texts = [];
			startTime = new Date();

			results.push(0);
			texts.push('Begins');
			console.log('Architekt.module.Comparator: Start perfomance measuring at ' + startTime.toGMTString());

			return this;
		},
		//Architekt.module.Comparator.stop(): Stops measuring
		stop: function() {
			endTime = new Date();

			results.push(endTime.getTime() - startTime.getTime());
			texts.push('Ends');
			console.log('Architekt.module.Comparator: Stop perfomance measuring at ' + startTime.toGMTString());

			return this.result();
		},
		//Architekt.module.Comparator.check(): Add checkpoint
		check: function(text) {
			var currentTime = new Date();

			text = typeof text !== 'undefined' ? text : 'Checkpoint';

			results.push(currentTime.getTime() - startTime.getTime());
			texts.push(text);
			console.log('Architekt.module.Comparator: Add checkpoint at ' + startTime.toGMTString());

			return this;
		},
		//Architekt.module.Comparator.result(): Get result
		result: function() {
			var resultObject = [];
			for(var i = 0, len = results.length; i < len; i++)
				resultObject.push({
					time: results[i],
					message: texts[i]
				});

			return resultObject;
		},
	};
});