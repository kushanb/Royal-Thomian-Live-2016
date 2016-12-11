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
if ($privilege == "batting" || $privilege == "admin") {
?>
    
    
<div class="col-md-8">
            <ul class="nav nav-pills">
              <li role="presentation"><a href="batting.php?inning=1">Inning 1</a></li>
              <li role="presentation"><a href="batting.php?inning=2">Inning 2</a></li>
              <li role="presentation"><a href="batting.php?inning=3">Inning 3</a></li>
              <li role="presentation"><a href="batting.php?inning=4">Inning 4</a></li>
              
              <li role="presentation"><a href="batting.php?inning=odi1">One day - 1st</a></li>
              <li role="presentation"><a href="batting.php?inning=odi2">One day - 2nd</a></li>
            </ul>
        </div>
            
    <?php
    $inning        = "";
    $valid_innings = array(
        "1",
        "2",
        "3",
        "4",
        "odi1",
        "odi2"
    );
    
    if (!empty($_GET['inning'])) {
        
        $inning = $_GET['inning'];
        
        if (in_array($inning, $valid_innings)) {
            
            include("connection.php");
            $inning      = mysqli_real_escape_string($sql_connect, $inning);
            
            $check_if_loaded_query = mysqli_query($sql_connect, "SELECT* FROM batting WHERE inning='$inning';");
            $check_if_loaded_rows  = mysqli_num_rows($check_if_loaded_query);
            
            if ($check_if_loaded_rows == 0) {
?> 
       
       <div class="col-md-4">
        	<div class="panel panel-default">
              <!-- Default panel contents -->
              <div class="panel-heading">Load Players</div>
            
              <div class="panel-body">
              	<form action="#" method="post">
                    <select class="form-control" name="team_select">
                        <option value="rc">Royal</option>
                        <option value="stc">S. Thomas</option>
                     </select>
                     <input type="submit" name="loadteam" value="Load Players" class="btn btn-primary" style="margin-top:10px;"/>
                 </form>
              </div>
              
            </div>
        </div>
        
        <?php
                if (isset($_POST['loadteam'])) {
                    $teams = array(
                        "rc",
                        "stc"
                    );
                    
                    $selected_team = mysqli_real_escape_string($sql_connect, $_POST["team_select"]);
                    
                    if (in_array($selected_team, $teams)) {
                        
                        $sql_query = mysqli_query($sql_connect, "SELECT* FROM player_profile WHERE team='$selected_team';");
                        
                        //start while
                        while ($sql_data = mysqli_fetch_array($sql_query)) {
                            
                            $player_id = (int) $sql_data['id'];
							$db_player_name = $sql_data["player_name"];
                            
                            //insert to batting table
                            $insert_batting = mysqli_query($sql_connect, "INSERT INTO `batting` (`id`, `player_id`, `player_name`, `inning`, `R`, `B`, `4s`, `6s`, `current_bat`) VALUES (NULL, '$player_id', '$db_player_name', '$inning', '0', '0', '0', '0', '0');");
                            
                        }
                        //end while
                        header("Location: batting.php?inning=" . $inning);
                        exit();
                        
                    } else {
                        echo "Invalid Team!";
                    }
                    
                }
                
            }
?>
        
        <div class="clearfix"></div>
        
        <?php
            
            $check_if_there_rows_query = mysqli_query($sql_connect, "SELECT* FROM batting WHERE inning='$inning';");
            $check_if_there_rows_rows  = mysqli_num_rows($check_if_there_rows_query);
            
            if ($check_if_there_rows_rows > 0) {
                
                if (isset($_POST["updatescores"])) {
                    
                    $reset_checks_query = mysqli_query($sql_connect, "UPDATE `batting` SET `current_bat` = '0';");
                    
                    foreach ($_POST['currentbatting'] as $input_player_id) {
                        $input_player_id    = (int) $input_player_id;
						
                        //check whether theres an existing player with the checkbox ID
                        $check_player_query = mysqli_query($sql_connect, "SELECT * FROM player_profile WHERE id='$input_player_id';");
                        $check_player_rows  = mysqli_num_rows($check_player_query);
                        
                        if ($check_player_rows == 1) {
                            $input_r     = $_POST['runs' . $input_player_id];
                            $input_b     = $_POST['bowls' . $input_player_id];
                            $input_fours = $_POST['fours' . $input_player_id];
                            $input_sixes = $_POST['sixes' . $input_player_id];
                            
                            if ($input_r != "" || $input_b != "" || $input_fours != "" || $input_sixes != "") {
                                
                                //Update database
                                $update_score_query = mysqli_query($sql_connect, "UPDATE `batting` SET `R` = '$input_r', `B` = '$input_b', `4s` = '$input_fours', `6s` = '$input_sixes', `current_bat` = '1' WHERE `player_id` = '$input_player_id' AND `inning` = '$inning';");
                                
                            } else {
                                echo "Please enter all the values!";
                            }
                        } else {
                            echo "Please don't change the HTML Codes!!";
                        }
                        
                    }
                    header("Location: batting.php?inning=" . $inning);
                    exit();
                }
                
?>
        
        <form action="#" method="post">
        
        <h1 style="display:inline-block;">Batsman Updating</h1>
        <input type="submit" name="updatescores" class="btn btn-primary pull-right" value="Save Changes" />
        
        <div class="panel panel-default">
          <!-- Default panel contents -->
          <div class="panel-heading">Batsman Updating</div>
        
          <table width="100%" class="table">
        	<thead>
            	<tr>
                	<th>Current batting</th>
                	<th>Name</th>
                    <th>Runs</th>
                    <th>Bowls</th>
                    <th>Fours</th>
                    <th>Sixes</th>
                </tr>
                
            </thead>
            <tbody>
            	<?php
                while ($get_table_data = mysqli_fetch_array($check_if_there_rows_query)) {
                    
                    $table_player_id = (int) $get_table_data['player_id'];
                    
                    $get_player_name_query = mysqli_query($sql_connect, "SELECT* FROM player_profile WHERE id='$table_player_id'");
                    $player_info           = mysqli_fetch_array($get_player_name_query);
                    $player_name           = $player_info["player_name"];
				?>
            	<tr>
                
                <?php
                    if ($get_table_data['current_bat'] == 1) {
                        echo '<td><input checked name="currentbatting[]" value="' . $get_table_data['player_id'] . '" type="checkbox" class="currentbatting"/></td>';
                    } else {
                        echo '<td><input name="currentbatting[]" value="' . $get_table_data['player_id'] . '" type="checkbox" class="currentbatting"/></td>';
                    }
?>
                   
                   <td><?php echo $player_name; ?></td>
                   <td><input type="number" class="form-control" name="runs<?php echo $get_table_data['player_id'];?>" value="<?php echo $get_table_data["R"];?>"/></td>
                   <td><input type="number" class="form-control" name="bowls<?php echo $get_table_data['player_id'];?>" value="<?php echo $get_table_data["B"];
?>"/></td>
                   <td><input type="number" class="form-control" name="fours<?php echo $get_table_data['player_id'];?>" value="<?php echo $get_table_data["4s"];?>"/></td>
                   <td><input type="number" class="form-control" name="sixes<?php echo $get_table_data['player_id'];?>" value="<?php echo $get_table_data["6s"];?>"/></td>
                 </tr>
                 
                 <?php } ?>
            </tbody>
        </table>
        </div>
        <input type="submit" name="updatescores" class="btn btn-primary pull-right" value="Save Changes" style="margin-bottom:15px;" />
        </form>
                  
        <?php
            }
            
        } else {
            echo "Invalid Inning";
        }
    } else {
        echo "Please select an Inning";
    }
} else {
	echo "You do not have Permission!";	
}
?>

        
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script>
	var limit = 2;
	var selected = $("[type='checkbox']:checked").length;
		
	$("[type='checkbox']:checked").closest("tr").toggleClass("highlight", this.checked);	
	
	$('input.currentbatting').on('change', function(evt) {
		
	  $(this).closest("tr").toggleClass("highlight", this.checked);
		
	  if($(this).is(':checked') == true){
		 selected++; 
	  } else {
		 selected--; 
	  }	
	  
	  if(selected == limit){  
		$('input.currentbatting:not(:checked)').attr("disabled", true);
		  
	  } else {
		 $('input.currentbatting:not(:checked)').attr("disabled", false);
	  }
	  
	});
	
	if(selected == limit){  
		$('input.currentbatting:not(:checked)').attr("disabled", true);
		  
	} else {
		$('input.currentbatting:not(:checked)').attr("disabled", false);
	}
	</script>

	<script src="https://cdn.socket.io/socket.io-1.4.3.js"></script>
	<script>
    
    var socket = io.connect("http://192.168.1.3:3000");
    
    $(document).ready(function(e) {
        var data = {authcode:"roytholive##Suresh",datatype:"batting"};
        socket.emit('send update', data);
    });
    
    </script> 

	<style>
	body {
		background-image:url(img/background.jpg);
		background-size:cover;
		background-attachment:fixed;
	}
	.alert {
		display:inline-block;
	}
	tr.highlight {
		background:#8E9CFF;
		color:#fff;
	}
	</style>

</body>

</html>