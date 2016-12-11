<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

include("connection.php");


if($_GET["request"] == "mainscore"){
	
	$query = mysqli_query($sql_connect,"SELECT* FROM mainscore");
	$data = mysqli_fetch_array($query);
	
	$json = json_encode($data);
	echo $json;
	
} else if($_GET["request"] == "batting"){
	
	$query = mysqli_query($sql_connect,"SELECT* FROM batting WHERE current_bat='1'");

	$batting_array = array();
	
	while($data = mysqli_fetch_array($query)){
		 $batting_array[] = $data;
	}
	
	$json = json_encode($batting_array);
	echo $json;
	
} else if($_GET["request"] == "bowling"){
	
	$query = mysqli_query($sql_connect,"SELECT* FROM bowling WHERE current_bowl='1'");
	
	$bowlers_array = array();
	
	while($data = mysqli_fetch_array($query)){
		$bowlers_array[] = $data;
	}
	
	$json = json_encode($bowlers_array);
	echo $json;
	
} else if($_GET["request"] == "scorecard"){
	
	$scorecard = array();
	
	$inning = $_GET["inning"];
	
	$valid_innings = array("1","2","3","4","odi1","odi2");
	
	if(in_array($inning,$valid_innings)){
		
		$batting_query = mysqli_query($sql_connect,"SELECT* FROM batting WHERE inning='$inning'");
		$bowling_query = mysqli_query($sql_connect,"SELECT* FROM bowling WHERE inning='$inning'");
		
		while($batting_data = mysqli_fetch_array($batting_query)){
			$scorecard["batting"][] = $batting_data;
		}
		
		while($bowling_data = mysqli_fetch_array($bowling_query)){
			$scorecard["bowling"][] = $bowling_data;
		}
		
		$json = json_encode($scorecard);
		echo $json;
		
	} else {
		echo "invalid inning";	
	}
	
	
} else if($_GET["request"] == "commentary"){
	
	$commentary = array();
	
	$query = mysqli_query($sql_connect,"SELECT* FROM commentary ORDER BY ID DESC LIMIT 5;");
	
	while($comment_data = mysqli_fetch_array($query)){
			$commentary[] = $comment_data;
	}
	
	$json = json_encode($commentary);
	echo $json;
		
} else if($_GET["request"] == "photos"){
	
	$photos = array();
	
	$query = mysqli_query($sql_connect,"SELECT* FROM photos ORDER BY ID DESC LIMIT 5;");
	
	while($photo_data = mysqli_fetch_array($query)){
			$photos[] = $photo_data;
	}
	
	$json = json_encode($photos);
	echo $json;
	
} else {
	
	echo "Invalid request!";
	
}


?>