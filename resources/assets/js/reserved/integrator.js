Architekt.module.reserv('Integrator', function() {
	return function(tasks, callback) {
		for(var i = 0, len = tasks.length; i < len; i++)
			tasks[i].call(null);

		if(typeof callback === 'function') callback();
	};
});