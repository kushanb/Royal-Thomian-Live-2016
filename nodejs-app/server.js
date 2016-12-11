var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var fs = require('fs');
var mysql = require('mysql');

app.get('/', function(req, res){
  res.send('<h1>Royal Thomian Live</h1>');
});

http.listen(3000, function(){
  console.log('listening on *:3000');
});

var db = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    database: 'royalthomian'
});
 
db.connect(function(err){
    if (err) console.log(err)
});


io.on('loadscores',function(mobile){
	
});


io.on('connection', function(socket){
	
	 socket.on('send update', function(data){
		 
		 if(data.authcode == "roytholive##Suresh"){
			 
			 if(data.datatype == 'mainscore'){
				 
				updatemainscore();
				console.log("Sent Main Scores");
				
			 } else if(data.datatype == 'batting'){
				 
				updatebatting();
				console.log("Sent Batting Info");
				
			 } else if(data.datatype == 'bowling'){
				 
				updatebowling();
				console.log("Sent Bowling Info");
				
			 } else if(data.datatype == 'commentary'){
				 
				updatecommentary();
				console.log("Sent Commentary");
				
			 } else {
				 
				console.log("Invalid Data request with datatype: "+data.datatype); 
				
			 }
			 
		 } else {
			 
			console.log("Data Authentication Failed with authcode: "+data.authcode); 
			
		 }
		 
	 });
	
});


var updatecommentary = function(){
	var query = db.query("SELECT* FROM commentary ORDER BY ID DESC LIMIT 5;");
	var commentary = [];
	
	query.on("error",function(error){
		
		console.log(error);
		
	}).on("result",function(data){
	
		commentary.push(data);
		
	}).on("end",function(data){
		
		io.emit('commentary', commentary);
					
	});
};

var updatebowling = function(){
	var query = db.query("SELECT* FROM bowling WHERE current_bowl='1'");
	var bowler = [];
	
	query.on("error",function(error){
		
		console.log(error);
		
	}).on("result",function(data){
	
		bowler.push(data);
		
	}).on("end",function(data){
		
		io.emit('bowling', bowler);
					
	});
};

var updatebatting = function(){
	var query = db.query("SELECT* FROM batting WHERE current_bat='1'");
	var batsman = [];
	var batsman_name = [];
	var finalised_batsman = [];
	
	query.on("error",function(error){
		
		console.log(error);
		
	}).on("result",function(data){
	
		batsman.push(data);
		
	}).on("end",function(data){
		
		io.emit('batting', batsman);
					
	});
};

function merge_options(obj1,obj2){
    var obj3 = {};
    for (var attrname in obj1) { obj3[attrname] = obj1[attrname]; }
    for (var attrname in obj2) { obj3[attrname] = obj2[attrname]; }
    return obj3;
}

var updatemainscore =  function(){
	var query = db.query("SELECT* FROM mainscore");
	var mainscore = [];
	
	query.on("error",function(error){
		
		console.log(error);
		
	}).on("result",function(data){
		
		mainscore.push(data);
		
	}).on("end",function(data){
			
		io.emit('mainscore', mainscore[0]);
		
	});	
};