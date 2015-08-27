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
<title>Admin Registration</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../Bootstrap/css/bootstrap.styles.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="css/form.css" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
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
		echo "<center>Failure: You do not have permission to register admins!</center>";
		echo "</div>";
		exit();
}
?>
<div id="main">
	<div class="content">
		<div id="pagetitle">Register Admin:</div>
		
		<div id="form_body">
			<form method="POST" action="admin_registration.php">
				<fieldset>
					<ul>
						<!-- Fields for information to register admin -->
						<input type="hidden" name="register" value="register" />
						<li>
							<label>Username:</label><input type="text" name="user" required="true" />
							<div style="clear:both; float:none"></div>
						</li>
						<li>
							<label>First Name:</label><input type="text" name="first" required="true" />
							<div style="clear:both; float:none"></div>
						</li>
						<li>
							<label>Last Name:</label><input type="text" name="last" required="true" />
							<div style="clear:both; float:none"></div>
						</li>
						<li>
							<label>Email:</label><input type="text" name="admin_email" required="true" />
							<div style="clear:both; float:none"></div>
						</li>
						<li>
							<label>Password:</label><input type="password" name="pass1" required="true" />
							<div style="clear:both; float:none"></div>
						</li>
						<li>
							<label>Re-Enter Password:</label><input type="password" name="pass2" required="true" />
							<div style="clear:both; float:none"></div>
						</li>
						<li>
							<input class="button" type="submit" name="submit" value="Submit" />
						</li>
					</ul>
				</fieldset>
			</form>
		</div>

<?php
// Do on submit click
if( isset($_POST['submit']) )
{
	// check if user is empty
	if (empty($_POST['user']) )
	{
		echo "<div class ='alert alert-danger'>";
		echo "<center>Username is required!</center>";
		echo "</div>";
		exit();
	}	
	// check if pass1 and pass2 are equal
	else if ($_POST['pass1'] != $_POST['pass2']){
		echo "<div class ='alert alert-danger'>";
		echo "<center>Passwords did not match!</center>";
		echo "</div>";
		exit();
	}
	//check if pass1 and pass2 are empty
	else if (empty($_POST['pass1']) || empty($_POST['pass2']) )
	{
		echo "<div class ='alert alert-danger'>";
		echo "<center>Password cannot be blank!</center>";
		echo "</div>";
		exit();
	}
	// check if admin_email is empty
	else if (empty($_POST['admin_email']) )
	{
		echo "<div class ='alert alert-danger'>";
		echo "<center>Email is required!</center>";
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
	$query = "SELECT * FROM DB_1.customer WHERE username =$1" ;
	$stmt = pg_prepare($conn, "name_check", $query)  or die( pg_last_error() );
	$result = pg_execute($conn, "name_check" ,array($user) )  or die( pg_last_error() );
	//Check to see if login user exists, if not do nothing
	if(pg_num_rows($result) == 1)
	{
		echo "<div class ='alert alert-danger'>";
		echo "<center>Error: Username taken. Please choose a unique name!</center>";
		echo "</div>";
		exit();
	}
	
	mt_srand(); //Seed number generator
	$salt = mt_rand(); //generate salt
	$salt = sha1($salt);
	$pass = htmlspecialchars($_POST['pass1']);
	$passHashed = sha1($salt.$pass);

	for ($i=0; $i<10000; $i++) //Slow Hashing
	{
		$passHashed = sha1($passHashed);
	}

	//Insert user as admin into the database
	$query = "INSERT INTO DB_1.customer (username, salt, hashpass, user_email, firstname, lastname, userType) VALUES ($1,$2,$3,$4, $5, $6, $7)";
	
	$state = pg_prepare($conn,"insert",$query) or die( "Error:". pg_last_error() );
	$queryInsert = pg_execute($conn,"insert",array($user, $salt, $passHashed, $_POST['admin_email'], $_POST['first'], $_POST['last'], 'admin'))  or die( "Error:". pg_last_error() );
	
	//Notify user was added successfully
	echo "<div class ='alert alert-success'>";
	echo "<center>Success: " . $user . " has been registered as an admin!</center>";
	echo "</div>";
	exit();
} ?>

	</div>
</div>
</body>
</html>

