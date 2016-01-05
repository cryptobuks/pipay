/****************************************************************************************************
 *
 *      Architekt.module.Client: Client resource provider
 *
 ****************************************************************************************************/

Architekt.module.reserv('Client', function(options) {
	return {
		domain: document.URL,
		host: location.host,
		hostName: location.hostname,
		href: location.href,
		origin: location.origin,
		path: location.path,
		protocol: location.protocol,
		url: (location.protocol + "//" + location.host),
		createUrl: function(sub) {
			var temp = [];
			temp.push(this.url);
			temp.push(sub);

			return temp.join("/");
		}
	};
});