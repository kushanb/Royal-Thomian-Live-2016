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
if($privilege == "commentator" || $privilege == "admin"){
?>    
    
<div class="col-md-6">
	<h1>Photos Updating</h1>
	<div class="panel panel-default">
      <!-- Default panel contents -->
      <div class="panel-heading">Please type the URL</div>
      <div class="panel-body">
        <form action="#" method="post">
        <p>
        <label for="text">Type Here...</label>
        </p>
        <p>
        <textarea name="text" class="form-control"></textarea>
        </p>
        <p><input type="submit" name="comment" value="Post Photo" class="btn btn-primary"></p>
        </form>  
      </div>
    </div>
   
</div>
    
    
    <div class="col-md-6">
    
		<h2>Recently Published Photos</h2>
        
        <ul class="list-group">
			<?php
            
            include("connection.php");
			
            $get_comments_query = mysqli_query($sql_connect,"SELECT* FROM photos ORDER BY ID DESC LIMIT 4;");
            
            while($data = mysqli_fetch_array($get_comments_query)){
            ?>
                <li class="list-group-item col-sm-6"><img src="<?php echo $data['url'] ?>" height="150px"/><br/><small style="text-transform:uppercase;"><?php echo $data['datetime'] ?> | BY, <?php echo $data['author'] ?></small><a href="photos.php?edit=<?php echo $data['id'] ?>" class="pull-right">EDIT</a></li>
            <?php } ?>
        </ul>
    
    </div> 

<?php
	if(isset($_POST["comment"])){
		$text = $_POST["text"];
		
		if(!empty($text)){
			
			$text = mysqli_real_escape_string($sql_connect,$text);
			$sql_query = mysqli_query($sql_connect,"INSERT INTO `photos` (`id`, `url`, `datetime`, `author`) VALUES (NULL, '$text', NOW(), '$privilege');");
			//done
			header("Location: photos.php");
			exit();	
			
		} else {
			echo '<div class="alert alert-danger" role="alert">URL cannot be empty</div>';	
		}
	}
	
	if(isset($_GET['edit'])){
		
		include("connection.php");
		$id = (int)$_GET["edit"];
		
		//check for existing comments
		$comment_check_query = mysqli_query($sql_connect,"SELECT* FROM photos WHERE id='$id'");
		$rows = mysqli_num_rows($comment_check_query);
		$updata = mysqli_fetch_array($comment_check_query);
		
		if($rows == 1){
		?>
        
        <div class="col-md-6">
 		<div class="panel panel-default">
          <!-- Default panel contents -->
          <div class="panel-heading">Edit Your Comment!</div>
          <div class="panel-body">
            <form action="#" method="post">
            <p>
            <label for="text">Type Here...</label>
            </p>
            <p>
            <textarea name="updatedtext" class="form-control"><?php echo $updata['url'] ?></textarea>
            </p>
            <p><input type="submit" name="update" value="Update Comment" class="btn btn-primary"> <input type="submit" name="delete" value="Delete" class="btn btn-danger"></p>
            </form>  
          </div>
        </div>
        </div>
           
        <?php
		
			if(isset($_POST['update'])){
				
				$id = $_GET["edit"];
				$text = $_POST["updatedtext"];
				
				if(!empty($text)){
					
					mysqli_query($sql_connect,"UPDATE photos SET url='$text' WHERE id='$id';");
					header("Location: photos.php");
					exit();
					
				} else {
					echo '<div class="alert alert-danger" role="alert">URL cannot be empty</div>';	
				}
				
			} else if(isset($_POST['delete'])){
				
				$id = $_GET["edit"];
				
				mysqli_query($sql_connect,"DELETE FROM photos WHERE id='$id';");
				header("Location: photos.php");
				exit();
				
			}
		
		} else {
			echo "Photo does not exist!";	
		}
		
	}

} else {
	header("Location: admin.php");	
}
?>    
    
</div>

<style>
	body {
		background-image:url(img/background.jpg);
		background-size:cover;
		background-attachment:fixed;
	}
	</style>
    
</body>

</html>