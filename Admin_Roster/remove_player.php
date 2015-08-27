<?php
//Make sure the page is HTTPS
if ($_SERVER['HTTPS'] != "on")
{
	$url = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
 	header("Location: $url");
	exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset=UTF-8>
<title>Remove Player</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../Bootstrap/css/bootstrap.styles.css" rel="stylesheet">
<style>      
	.center {
		text-align: center;
	}	
	
  </style>
</head>
<body>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../Bootstrap/js/bootstrap.min.js"></script>
<form method="POST" action="remove_player.php">
<?php
	//get the common header from header.php
	set_include_path('..');
	require "admin_header.php";
?>
	<div>
		<h3 class="center">View Tournament Roster:</h3>
	</div>
	<div class="center">
		<div>
			<!--	Choose: Tournament roster to view	 -->	
			<h4 class="center">Choose Tournament:</h4>
				<select type="list" name="tournament">
					<option value="tournament">Tournament</option>
					
				<?php
				//setup connection
				include("../secure/database.php");
				$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD)
					or die( pg_last_error() ); 
				// Get tournament names
				$result = pg_query($conn, "SELECT DISTINCT ON (DB_1.roster.tournament) tournament FROM DB_1.roster"); 
				
				// Load tournament names into our list
				while ($row = pg_fetch_array($result))
				{
				?>
				   <option value="<? echo $row['tournament'];?>"><?echo $row['tournament'];?></option>                                      
					<?
				}
					?>

				</select><br />
			<br />
			<input type="submit" name="view" value="View" /><br /><br />
			<!-- Get player names -->
			<?php
			if( isset($_POST['view']) && $_POST['tournament'] != "tournament")
			{
			?>
				<div>
				<!--	Choose: Player to remove	 -->
					<h4 class="center">Choose a Team to remove:</h4>
					<select type="list" name="team_name">
									<option value="name_not">Team Name</option>
					<?php
					//setup connection
					include("../secure/database.php");
					$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD)  
							or die( pg_last_error() ); 
					// Get  usernames
					$result = pg_query($conn, "SELECT * FROM DB_1.roster"); 
					
					// Load usernames into our list
					while ($row = pg_fetch_array($result))
					{
					?>
					   <option value="<? echo $row['username'];?>"><?echo $row['username'];?></option>                                      
						<?
					}
				
						?>

						</select><br />
				<br />
				<input type="submit" name="submit" value="Remove" /><br /><br />
			<?php
			}
			else if ($_POST['tournament'] == "tournament")
			{
				// Print choose tournament
				echo "\n<div class='container'>\n\t<div class ='alert alert-danger'>";
				echo "<center>Please select a tournament...</center>";
				echo "\n\t</div>\n</div>";
				exit();
			}
			?>
			</div>
	</div>
</form>
       
<?php

// Do on submit click
if( isset($_POST['submit']) )
{
	// check if user is empty
	if (empty($_POST['team_name']) )
	{
		echo "\n<div class='container'>\n\t<div class ='alert alert-danger'>";
		echo "<center>Username is needed</center>";
		echo "\n\t</div>\n</div>";
		exit();
	}	

	if($_POST['team_name'] == "name_not")
	{
		// Print success if result
		echo "\n<div class='container'>\n\t<div class ='alert alert-warning'>";
		echo "<center>Please select an admin to delete!</center>";
		echo "\n\t</div>\n</div>";
		exit();
	}
	//setup connection
	include("../secure/database.php");
	$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD);
	if (!$conn) 
	{
		echo "\n<div class='container'>\n\t<div class ='alert alert-danger'>";
		echo "<center>An Error occurred during connection.</center>";
		echo "\n\t</div>\n</div>";
		exit();
	}
	$user = htmlspecialchars($_POST['team_name']);
	
	//Run username against dB
	$query = "DELETE FROM DB_1.roster WHERE team_name =$1" ;
	
	$stmt = pg_prepare($conn, "remove_admin", $query)  or die( pg_last_error() );
	
	$result = pg_execute($conn, "remove_admin" ,array($user) )  or die( pg_last_error() );

	session_start();
	
	if($result)
	{
		// Print success if result
		echo "\n<div class='container'>\n\t<div class ='alert alert-success'>";
		echo "<center>Delete Successful!</center>";
		echo "\n\t</div>\n</div>";
		exit();
	}
	else
	{
		// Print !success if !result
		echo "\n<div class='container'>\n\t<div class ='alert alert-danger'>";
		echo "<center>Delete Unsuccessful...</center>";
		echo "\n\t</div>\n</div>";
		exit();	
	}
}
?>

</body>
</html>
