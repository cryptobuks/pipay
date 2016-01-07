//require modules
var express = require('express');
var app     = express();
var http    = require('http').Server(app);
var socket  = require('socket.io')(http);
var Redis   = require('ioredis');
var redis   = new Redis();

//configuration
var port = process.env.PORT || 8800;

app.use(function(req, res, next) {
  res.header('Access-Control-Allow-Origin', '*');
  res.header('Access-Control-Allow-Methods', 'GET, OPTIONS');
  res.header('Access-Control-Allow-Headers', 'Content-Type');
  return next();
});

redis.psubscribe('*', function(err, count) {
    //
});

//message event listener
redis.on('pmessage', function(subscribed, channel, message){
	console.log('Message received: ' + message);
	message = JSON.parse(message);
	socket.emit(channel + ':' + message.event, message.data);
});

//server listener
http.listen(port, function(){
	console.log('Server running on port: ' + port);
});