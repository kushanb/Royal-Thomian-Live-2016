<?php

session_start();

if (empty($_SESSION['login'])) {
    header("Location: index.php");
    exit();
} else {
    $privilege = $_SESSION["login"];
}

?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Royal Thomian Live | Administration</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <p class="navbar-text">Welcome,
                    <?php echo $privilege; ?> | <a href="logout.php">Logout</a></p>
            </div>
        </div>
    </nav>

<div class="container">

<?php
if($privilege == "bowling" || $privilege == "admin"){
	
	include("connection.php");
	
	$load_score_query = mysqli_query($sql_connect,"SELECT* FROM mainscore");
	$data = mysqli_fetch_array($load_score_query);
	
	$runrate = $data["total"] / $data["overs"];
	
?>

<div class="col-md-5">
<form action="#" method="post" id="mainscoreform">
<p>Inning:</p>
<p>
<select class="form-control" name="inning">
	<?php
	switch($data["inning"]){
		case "1":
			echo '<option value="non">None</option>
				  <option value="1" selected>1st inning</option>
			      <option value="2">2nd inning</option>
				  <option value="3">3rd inning</option>
				  <option value="4">4th inning</option>
				  <option value="odi1">One Day 1</option>
				  <option value="odi2">Ode Day 2</option>';
			break;
		case "2":
			echo '<option value="non">None</option>
			      <option value="1">1st inning</option>
			      <option value="2" selected>2nd inning</option>
				  <option value="3">3rd inning</option>
				  <option value="4">4th inning</option>
				  <option value="odi1">One Day 1</option>
				  <option value="odi2">Ode Day 2</option>';
			break;
		case "3":
			echo '<option value="non">None</option>
			      <option value="1">1st inning</option>
			      <option value="2">2nd inning</option>
				  <option value="3" selected>3rd inning</option>
				  <option value="4">4th inning</option>
				  <option value="odi1">One Day 1</option>
				  <option value="odi2">Ode Day 2</option>';
			break;
		case "4":
			echo '<option value="non">None</option>
			      <option value="1">1st inning</option>
			      <option value="2">2nd inning</option>
				  <option value="3">3rd inning</option>
				  <option value="4" selected>4th inning</option>
				  <option value="odi1">One Day 1</option>
				  <option value="odi2">Ode Day 2</option>';
			break;
		case "odi1":
			echo '<option value="non">None</option>
			      <option value="1">1st inning</option>
			      <option value="2">2nd inning</option>
				  <option value="3">3rd inning</option>
				  <option value="4">4th inning</option>
				  <option value="odi1" selected>One Day 1</option>
				  <option value="odi2">Ode Day 2</option>';
			break;
		case "odi2":
			echo '<option value="non">None</option>
			      <option value="1">1st inning</option>
			      <option value="2">2nd inning</option>
				  <option value="3">3rd inning</option>
				  <option value="4">4th inning</option>
				  <option value="odi1">One Day 1</option>
				  <option value="odi2" selected>Ode Day 2</option>';
			break;	
		default:
			echo '<option selected value="non">None</option>
			      <option value="1">1st inning</option>
			      <option value="2">2nd inning</option>
				  <option value="3">3rd inning</option>
				  <option value="4">4th inning</option>
				  <option value="odi1">One Day 1</option>
				  <option value="odi2">Ode Day 2</option>';
			break;				
	}
	?>

</select>
</p>
<p>Batting</p>
<p><select class="form-control" name="batting">

<?php
	switch($data["batting"]){
		case "rc":
			echo '<option value="non">None</option>
				  <option value="rc" selected>Royal</option>
			      <option value="stc">S. Thomas</option>';
			break;
		case "stc":
			echo '<option value="non">None</option>
				  <option value="rc">Royal</option>
			      <option value="stc" selected>S. Thomas</option>';
			break;	
		default:
			echo '<option selected value="non">None</option>
				  <option value="rc">Royal</option>
			      <option value="stc">S. Thomas</option>';
			break;			
	}
	?>

</select></p>
<p>Is Live?</p>
<p>
<?php
if($data["islive"] == 1){
	echo '<input checked type="checkbox" name="islive"/>';
} else {
	echo '<input type="checkbox" name="islive"/>';
}
?>
</p>
<p>Total:</p>
<p><input type="number" name="total" class="form-control" value="<?php echo $data["total"]; ?>"/></p>
<p>Wickets:</p>
<p><input type="number" name="wickets" class="form-control" value="<?php echo $data["wickets"]; ?>"/></p>
<p>Overs:</p>
<p><input type="text" name="overs" class="form-control" value="<?php echo $data["overs"]; ?>"/></p>
<p>Run Rate:</p>
<p><input type="text" disabled class="form-control" value="<?php echo $runrate; ?>"/></p>

<p>Bowling Team Total:</p>
<p><input type="text" name="bt_total" class="form-control" value="<?php echo $data["bowlingteam_total"]; ?>"/></p>

<p>Bowling Team Wickets:</p>
<p><input type="text" name="bt_wickets" class="form-control" value="<?php echo $data["bowlingteam_wickets"]; ?>"/></p>


<p><input type="submit" value="Update Live Info" class="btn btn-primary" name="update"/></p>
</form>

<?php

if(isset($_POST['update'])){
	$inning = $_POST["inning"];
	$batting = $_POST["batting"];
	
	if(isset($_POST['islive'])){
		$islive = 1;
	} else {
		$islive = 0;	
	}
	
	$total = $_POST["total"];
	$wickets = $_POST["wickets"];
	
	$bt_total = $_POST["bt_total"];
	$bt_wickets = $_POST["bt_wickets"];
	
	$overs = $_POST["overs"];
	
	if($total != "" || $wickets != "" || $overs != "" || $bt_total != "" || $bt_wickets != ""){
		
		$valid_innings = array("non","1","2","3","4","odi1","odi2");
		
		if(in_array($inning,$valid_innings)){
			
			$total = (int)$total;
			$wickets = (int)$wickets;
			$inning = mysqli_real_escape_string($sql_connect,$inning);
			$overs = mysqli_real_escape_string($sql_connect,$overs);
			$batting = mysqli_real_escape_string($sql_connect,$batting);
			$islive = (int)$islive;
			
			echo "<h1>".$islive."</h1>";
			
			$bt_wickets = (int)$bt_wickets;
			$bt_total = (int)$bt_total;
			
			mysqli_query($sql_connect,"UPDATE `mainscore` SET `inning` = '$inning', `total` = '$total', `wickets` = '$wickets', `overs` = '$overs', `bowlingteam_total` = '$bt_total', `bowlingteam_wickets` = '$bt_wickets', `islive`='$islive', `batting`='$batting';");
			
			header("Location: livescore.php");
			exit();
			
		} else {
			echo "Invalid Inning!";	
		}
		
	} else {
		echo "Please enter all the values";	
	}
}

} else {
	header("Location: admin.php");	
}
?>    
</div> 
    
    
</div>

<style>
	body {
		background-image:url(img/background.jpg);
		background-size:cover;
		background-attachment:fixed;
	}
	</style>
  
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>  
<script src="https://cdn.socket.io/socket.io-1.4.3.js"></script>
<script>

var socket = io.connect("http://192.168.1.3:3000");

$(document).ready(function(e) {
	var data = {authcode:"roytholive##Suresh",datatype:"mainscore"};
    socket.emit('send update', data);
});

</script>   
    
</body>

</html>