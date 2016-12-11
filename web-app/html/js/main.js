//Initialization
function init() {
	document.addEventListener("deviceready", onDeviceReady, false);
}

function onDeviceReady() {
	//Hide Splash Screen
	navigator.splashscreen.hide();
	
	//Device ready
	if (device.platform == "Android") {
		initPushwooshAndroid();
    } else if (device.platform == "Win32NT") {
		initPushwooshWindowsPhone();
    } else if(device.platform == "iOS"){
		initPushwooshIOS();
	}
}

function initPushwooshWindowsPhone(){
    var pushNotification = cordova.require("com.pushwoosh.plugins.pushwoosh.PushNotification");
 
    document.addEventListener('push-notification', function(event) {
		var notification = event.notification;
		alert(JSON.stringify(notification));
    });
 
    pushNotification.onDeviceReady({ appid: "818B1-2A125", serviceName: "" });
 
    pushNotification.registerDevice(
        function(status) {
            var pushToken = status;
            console.warn('push token: ' + pushToken);
        },
        function(status) {
            console.warn(JSON.stringify(['failed to register ', status]));
        }
    );
}

function initPushwooshAndroid(){
    var pushNotification = cordova.require("com.pushwoosh.plugins.pushwoosh.PushNotification");
 
    document.addEventListener('push-notification', function(event) {
        var title = event.notification.title;
        var userData = event.notification.userdata;
                                 
        if(typeof(userData) != "undefined") {
            console.warn('user data: ' + JSON.stringify(userData));
        }
                                     
        alert(title);
    });
 
    pushNotification.onDeviceReady({ projectid: "1017698848496", pw_appid : "818B1-2A125" });
 
    pushNotification.registerDevice(
        function(status) {
            var pushToken = status;
            console.warn('push token: ' + pushToken);
        },
        function(status) {
            console.warn(JSON.stringify(['failed to register ', status]));
        }
    );
}

function initPushwooshIOS() {
    var pushNotification = cordova.require("com.pushwoosh.plugins.pushwoosh.PushNotification");
 
    document.addEventListener('push-notification', function(event) {
          var notification = event.notification;
          alert(notification.aps.alert);
          pushNotification.setApplicationIconBadgeNumber(0);
    });
 
    pushNotification.onDeviceReady({pw_appid:"818B1-2A125"});
     
    pushNotification.registerDevice(
        function(status) {
            var deviceToken = status['deviceToken'];
            console.warn('registerDevice: ' + deviceToken);
        },
        function(status) {
            console.warn('failed to register : ' + JSON.stringify(status));
            alert(JSON.stringify(['failed to register ', status]));
        }
    );
     
    pushNotification.setApplicationIconBadgeNumber(0);
}

//Load Headers and Panel
$(document).one('pagebeforecreate', function() {
    $("#mypanel").panel();

    var header = '<header data-role="header" data-position="fixed" class="stc"> <table width="100%"> <tr> <td align="left" valign="top"> <a id="togglepanel" href="#mypanel" onclick="javascript:void();"> <img height="43px" src="img/menu.png"/> </a> </td><td align="right" style="color:#fff;"><img height="75px" src="img/crest.png" class="bat_crest"/> </td></tr><tr> <td align="center" colspan="2"> <div class="scorecontainer"> <h1><span class="bat_score">-</span>/<span class="bat_wickets">-</span></h1> <h2><span class="match">n/a</span></h2> <h2><span class="overs">0.0</span> Overs | RR: <span class="run_rate">0.0</span></h2> </div></td></tr><tr> <td align="left"><a href="#live" class="livelink"><span class="live">LIVE</span></a> </td><td align="right"> <h2 style="display: inline-block;font-size: 40px;"><span class="bowl_score">00</span>/<span class="bowl_wickets">0</span></h2><img src="img/royal.png" height="38px" class="bowl_crest"/> </td></tr></table> </header>';
   	$('div[data-role="page"]').prepend(header);
});

//Toggle Panel on swipe
jQuery(window).on("swiperight", function(event) {
    togglepanel();
});

//Show the side menu
function togglepanel(){
	$("#mypanel").panel("open");
}

//Data sources
var SocketServerURL = "http://192.168.1.2:3000";
var ScoreServerURL = "http://192.168.1.2/roytholive1/getdata.php";
var CricketTeamURL = "http://192.168.1.2/roytholive1/team/";
var streamurl = "";
var socket = io.connect(SocketServerURL);

//Initialization variables
var isMainScoreLoaded = false;
var isBattingLoaded = false;
var isLoadedBowling = false;
var isCommentaryLoaded = false;
var isTeamLoaded = false;
var isTwitterLoaded = false;
var isScorecardLoaded = false;
var selected_id = 1;
var activeinning = null;

$(document).on("pagechange", function(e) {
    var activepage = $('.ui-page-active').attr('id');
	
    if (activepage == "teams") {
        loadteams();
    } else if (activepage == "photos") {
        loadphotogallery();
    } else if(activepage == "twitter"){
		if(isTwitterLoaded == false){
			var twitter = document.getElementById('tweetframe');
			twitter.src = "twitter.html";
			$('#tweetframe').load(this,function(){
				$('#twitterloader').fadeOut(100);
				isTwitterLoaded = true;	
			});
		}
	} else if(activepage == "live"){
		$('#live iframe').attr('src', streamurl);
	
		$('#streamframe').load(this, function() {
			$('#streamloader').fadeOut(100);
			$('#live .footer').hide();
		});
	} else if(activepage == "scorecard"){
		loadscorecard(selected_id);
	}
});

$(document).ready(function(e) {
	
	//Load by Dafault
	loadscores();
	loadbatting();
	loadbowling();
	loadcommentary();
	
	$('.scorecard_link').click(function(e){
		selected_id = $(this).attr('id');
		loadscorecard(selected_id);
	});
		
	//Initialize fancybox for Photo Gallery
	$(".fancybox").fancybox();
	
	//Live Stream frame size: 16:9
	var doc_height = $(document).height();
	var stream_height = (doc_height * 9) / 16;
	$('#streamframe').height(stream_height);
	
});

// Demo		
$(function(){
    $('#photos .ui-content').xpull({
    	'callback':function(){
        	loadphotogallery();
       	}
    });	
	
	$('#scorecard .ui-content').xpull({
    	'callback':function(){
        	loadscorecard(selected_id);
       	}
    });		
});

//Load Cricket Teams
function loadteams(){
	//Load STC Team
	if(isTeamLoaded == false){
		$.ajax({
				url: CricketTeamURL+"stc.php",
				type: 'GET',
				crossDomain: true,
				dataType: 'html',
				success: function(data){
					$('#stc').html(data);
					$('#teams_loader').css('display','none');
				},
				error: function(){
					setTimeout(function(){
						loadteams();
					}, 1000);
				}
		});
		
		//Load RC Team
		$.ajax({
				url: CricketTeamURL+"rc.php",
				type: 'GET',
				crossDomain: true,
				dataType: 'html',
				success: function(data){
					$('#royal').html(data);
				},
				error: function(){
					setTimeout(function(){
						loadteams();
					}, 1000);
				}
		});
		
		isTeamLoaded = true;
	}
}

//Load Photo Gappery
function loadphotogallery(){
	$.ajax({
			url: ScoreServerURL,
			type: 'GET',
			data: {
				request: "photos"
			},
			crossDomain: true,
			dataType: 'json',
			success: function(data){
				
				var photo_html = "";
	
				$.each(data,function(key,photo){
					'<div style="background-image:url(8);"></div>'
					photo_html += "<li><a href='"+photo.url+"' class='fancybox photothumb'><div style='background-image:url("+photo.url+");' class='photofeedimg'></div></a></li>";
					$('.photofeedimg').load();
				});
					
				$('#photos ul').html(photo_html);
				$('#photofeed_loader').css('display','none');
				
			},
			error: function(){
				setTimeout(function(){
                    loadphotogallery();
                }, 1000);
			}
	});
}

//Load Commentary from Database and Socket.io (Realtime Push)
function loadcommentary(){
	
	if(isCommentaryLoaded == false){
		
		$.ajax({
			url: ScoreServerURL,
			type: 'GET',
			data: {
				request: "commentary"
			},
			crossDomain: true,
			dataType: 'json',
			success: function(data){
				if(data != ""){
					updatecommentary(data);
				}
				isCommentaryLoaded = true;
				loadcommentary();
			},
			error: function(){
				setTimeout(function(){
                    loadcommentary();
                }, 1000);
			}
		});
		
	} else {

		socket.on('commentary', function(data){
			if(data != ""){
				updatecommentary(data);
			}
		});
		
	}
}

//Update commentary
function updatecommentary(data){
	var comment_html = "";
	
	$.each(data,function(key,comment){
		comment_html += "<li class='ui-li-static ui-body-inherit'><h3>"+comment.text+"</h3><p>"+comment.datetime+"</p></li>";
	});
	
	$('#commentaryarea p').text(data[0].text);
	
	$('#commentary ul').html(comment_html);
	$('#commentary_loader').css('display','none');
}

//Load full scorecard
function loadscorecard(inning){
	
	$('.scorecard_link').removeClass('ui-btn-active');
	$('#'+inning).addClass('ui-btn-active');
	
	//update scorecard
	var scorecard_batting_html = "<tr><th width='40%'>Batsman</td><th width='15%'>R</td><th width='15%'>B</td><th width='15%'>4s</td><th width='15%'>6s</td><tr>";
	var scorecard_bowling_html = "<tr><th width='40%'>Bowler</td><th width='15%'>O</td><th width='15%'>M</td><th width='15%'>R</td><th width='15%'>W</td><tr>";
				
	$('.scorecard_batting_table').html(scorecard_batting_html);
	$('.scorecard_bowling_table').html(scorecard_bowling_html);
	
	$('#scorecard_batting_loader').css('display','inline');
	$('#scorecard_bowling_loader').css('display','inline');
	
	$.ajax({
			url: ScoreServerURL,
			type: 'GET',
			data: {
				request: "scorecard",
				inning: inning
			},
			crossDomain: true,
			dataType: 'json',
			success: function(data){
				
				$('#scorecard_batting_loader').css('display','none');
				$('#scorecard_bowling_loader').css('display','none');
				
				$.each(data.batting,function(key,batsman){
					scorecard_batting_html += "<tr><td>"+batsman.player_name+"</td><td>"+batsman.R+"</td><td>"+batsman.B+"</td><td>"+batsman["4s"]+"</td><td>"+batsman["6s"]+"</td></tr>";	
				});
				
				$('.scorecard_batting_table').html(scorecard_batting_html);
				
				$.each(data.bowling,function(key,bowler){
					scorecard_bowling_html += "<tr><td>"+bowler.player_name+"</td><td>"+bowler.O+"</td><td>"+bowler.M+"</td><td>"+bowler.R+"</td><td>"+bowler.W+"</td></tr>";	
				});
				
				$('.scorecard_bowling_table').html(scorecard_bowling_html);
				//done loading scorecard
			},
			error: function(){
				setTimeout(function(){
                    loadscorecard();
                }, 1000);
			}
	});
}

//Load active bowlers in the homepage
function loadbowling(){
	if(isLoadedBowling == false){
		
		$.ajax({
			url: ScoreServerURL,
			type: 'GET',
			data: {
				request: "bowling"
			},
			crossDomain: true,
			dataType: 'json',
			success: function(data){
				if(data != ""){
					updatebowling(data);
				}
				isLoadedBowling = true;
				loadbowling();
			},
			error: function(){
				setTimeout(function(){
                    loadbowling();
                }, 1000);
			}
		});
		
	} else {
		socket.on('bowling', function(data){
			if(data != ""){
				updatebowling(data);
			}
		});
	}
}

//Update active bowlers
function updatebowling(data){
	$('#bowler1_name').text(data[0].player_name);
	$('#b1_0').text(data[0].O);
	$('#b1_M').text(data[0].M);
	$('#b1_R').text(data[0].R);
	$('#b1_W').text(data[0].W);
	$('#bowler2_name').text(data[1].player_name);
	$('#b2_0').text(data[1].O);
	$('#b2_M').text(data[1].M);
	$('#b2_R').text(data[1].R);
	$('#b2_W').text(data[1].W);
}

//Load active batsmen in Home
function loadbatting(){
	if(isBattingLoaded == false){
		
		$.ajax({
			url: ScoreServerURL,
			type: 'GET',
			data: {
				request: "batting"
			},
			crossDomain: true,
			dataType: 'json',
			success: function(data){
				if(data != ""){
					updatebatting(data);
				}
				isBattingLoaded = true;
				loadbatting();
			},
			error: function(){
				setTimeout(function(){
                    loadbatting();
                }, 1000);
			}
		});
		
	} else {
		
		socket.on('batting', function(data){
			if(data != ""){
				updatebatting(data);
			}
		});
	}
}

//Update active batsmen
function updatebatting(data){
	$('#batsman1_name').text(data[0].player_name);
	$('#batsman2_name').text(data[1].player_name);
	$('#batsman1_score').text(data[0].R);
	$('#batsman2_score').text(data[1].R);
}

//Load live scores for Header
function loadscores(){
	if(isMainScoreLoaded == false){
		//first time score load from database
		$.ajax({
			url: ScoreServerURL,
			type: 'GET',
			data: {
				request: "mainscore"
			},
			crossDomain: true,
			dataType: 'json',
			success: function(data){
				if(data != ""){
					updatevalues(data);
				}
				isMainScoreLoaded = true;
				loadscores();
			},
			error: function(){
				setTimeout(function(){
                    loadscores();
                }, 1000);
			}
		});
	} else {
		socket.on('mainscore', function(data){
			if(data != ""){
				updatevalues(data);
			}
		});
	}
}

//Filter data and Set main score values
function updatevalues(data){
	var runrate = data.total / data.overs;
	runrate = Math.round(runrate * 100) / 100;
	
	$('.bat_score').text(data.total);
	$('.bat_wickets').text(data.wickets);
	$('.match').text(data.inning);
	$('.overs').text(data.overs);
	$('.run_rate').text(runrate);
	$('.bowl_score').text(data.bowlingteam_total);
	$('.bowl_wickets').text(data.bowlingteam_wickets);
	
	if(data.islive == 0){
		$('.livelink').css('display','none');
	} else {
		$('.livelink').css('display','inline');
	}
	
	switch(data.inning){
		case "1":
			$('.match').text("1st Inning");
			break;	
		case "2":
			$('.match').text("2nd Inning");
			break;
		case "3":
			$('.match').text("3rd Inning");
			break;	
		case "4":
			$('.match').text("4th Inning");
			break;
		case "odi1":
			$('.match').text("One Day - 1st Inning");
			break;
		case "odi2":
			$('.match').text("One Day - 2nd Inning");
			break;
		case "non":
			$('.match').text("n/a");
			break;
		default:
			$('.match').text("n/a");
			break;
	}
	
	if(data.batting == "stc"){
		$('.bat_crest').attr('src','img/crest.png');
		$('.bowl_crest').attr('src','img/royal.png');
		$('header').addClass('stc');
		$('header').removeClass('rc');
	} else if(data.batting == "rc") {
		$('.bat_crest').attr('src','img/royal.png');
		$('.bowl_crest').attr('src','img/crest.png');
		$('header').addClass('rc');
		$('header').removeClass('stc');
	} else {
		$('.bat_crest').attr('src','img/crest.png');
		$('.bowl_crest').attr('src','img/royal.png');
		$('header').addClass('stc');
		$('header').removeClass('rc');
	}
}