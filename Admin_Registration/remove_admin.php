<?php
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
<title>Remove Admin</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="css/form.css" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../Bootstrap/css/bootstrap.styles.css" rel="stylesheet">
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../Bootstrap/js/bootstrap.min.js"></script>
</head>
<body>

<?php
//get the common header from header.php
set_include_path('..');
require "admin_header.php";
// Check if user is the "super" admin
if ($_SESSION['admin_loggedin'] != 'admin' )
{
		// Print FAILURE: if $_SESSION['admin_loggedin'] != 'admin'
		echo "<div class ='alert alert-danger'>";
		echo "<center>Failure: You do not have permission to delete admins!</center>";
		echo "</div>";
		exit();
}
?>
<div id="main">
	<div class="content">
		<div id="pagetitle">Remove Admin:</div>
		
			<div id="form_body">
			<form method="POST" action="remove_admin.php">
				<fieldset>		
					<ul>
						<li>
							
							<label>Username: </label>
							<select type="list" name="user">
								<option value="">Select:</option>
								<?php
								//setup connection
								include("../secure/database.php");
								$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD);            
								// Get admin usernames
								$result = pg_query($conn, "SELECT * FROM DB_1.customer WHERE username != 'admin' AND userType='admin' "); 
								
								// Load usernames into our list
								while ($row = pg_fetch_array($result)) { ?>
								<option value="<?php echo $row['username']; ?>"><?php echo $row['username']; ?></option>                                      
								<?php } ?>
							</select>
							<div style="clear:both; float:none"></div>
						</li>
						<li>
							<input class="button" type="submit" name="submit" value="Remove" />

							<input class="button2" type="submit" name="reload" value="Refresh Page" />
						</li>
					</ul>
				</fieldset>
			</form>
		</div>
       

<?php
if( isset($_POST['reload']) )
{
	header("Location: https://babbage.cs.missouri.edu/~cs3380s14grp10/Admin_Registration/remove_admin.php");
}
// Do on submit click
if( isset($_POST['submit']) )
{
	// Check if username was selected
	if($_POST['user'] == "" || empty($_POST['user']))
	{
		// Print success if result
		echo "<div class ='alert alert-warning'>";
		echo "<center>Please select an admin to delete!</center>";
		echo "</div>";
		
		exit();
	}
	//setup connection
	include("../secure/database.php");
	$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD);
	if (!$conn) 
	{
		echo "<div class ='alert alert-danger'>";
		echo "<center>Error: </center>" . pg_last_error();
		echo "</div>";
		exit();
	}
	$user = htmlspecialchars($_POST['user']);
	
	//Run username against dB
	$query = "DELETE FROM DB_1.customer WHERE username = $1" ;
	
	$stmt = pg_prepare($conn, "remove_admin", $query) or die( pg_last_error() );
	
	$result = pg_execute($conn, "remove_admin", array($user)) or die( pg_last_error() );
	
	if($result)
	{
		// Print success if result
		echo "<div class ='alert alert-success'>";
		echo "<center>Success: " . $user . " has been removed!</center>";
		echo "</div>";
		exit();
	} else {
		// Print !success if !result
		echo "<div class ='alert alert-danger'>";
		echo "<center>Failure: " . $user . " was not deleted!</center>";
		echo "</div>";
		exit();	
	}
}
?>
	</div>
</div>
</body>
</html>
