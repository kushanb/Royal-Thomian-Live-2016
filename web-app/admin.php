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
                    <?php
echo $privilege;
?> | <a href="logout.php">Logout</a></p>
            </div>
        </div>
    </nav>

<div class="container">

<?php
if($privilege == "bowling"){
	header("Location: bowlers.php");
} else if($privilege == "batting"){
	header("Location: batting.php");
} else if($privilege == "commentator"){
	header("Location: commentary.php");
} else if($privilege == "admin"){
?>

<h1>Hello, Suresh!</h1>
<p>Please Choose Your Decision.</p>

<ul>
	<li><a href="batting.php">Update Batting</a></li>
    <li><a href="bowlers.php">Update Bowlers</a></li>
    <li><a href="commentary.php">Update Commentaries</a></li>
    <li><a href="photos.php">Update Photo Gallery</a></li>
    <li><a href="livescore.php">Update Main Score</a></li>
</ul>

<?php	
}
?>
        
</div>

</body>

</html>