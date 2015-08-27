<!DOCTYPE html>
<html>
<head>
<meta charset=UTF-8>
<title>Registration</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../Bootstrap/css/bootstrap.styles.css" rel="stylesheet">
<style>        .center {text-align: center;}	</style>
</head>
<body>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../Bootstrap/js/bootstrap.min.js"></script>
<form method="POST" action="registration.php">
<?php
	//get the common header from header.php
	set_include_path('..');
	require "header.php";
?>
        <div>
                <h3 class="center">Please Register:</h3>
        </div>
        <div>
                <div class="center">
			<!-- Fields to take in information from user -->
			<input type="hidden" name="register" value="register" />
                        <h4 class="center">Login Information:</h4>
			Username: <input type="text" name="user" /><br /><br />
                        Password: <input type="password" name="pass1" /><br /><br />
                        Re-Enter Password: <input type="password" name="pass2" /><br /><br />
                        <br />
			<h4 class="center">Contact Information:</h4>
			First Name: <input type="text" name="fname" /><br /><br />
                        Last Name: <input type="text" name="lname" /><br /><br />
                        Email Address: <input type="text" name="email" /><br /><br />
                        Phone Number: <input type="text" name="phone" /><br /><br />
			<br />
			<h4 class="center">Address Information:</h4>
			Street Address: <input type="text" name="street" /><br /><br />
                        City: <input type="text" name="city" /><br /><br />
                        State: <input type="text" name="state" /><br /><br />
                        Zip: <input type="text" name="zip" /><br /><br />                        
                        <input type="submit" name="submit" value="submit" /><br />
                </div>
        </form>
		<p><center><a href='login.php'>Return to Login</a></center></p>
        </div>

<?php
//Makes sure HTTPS is being used
if ($_SERVER['HTTPS'] != "on")
{
	$url = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
 	header("Location: $url");
	exit;
}
	
require 'pgsql.conf';
if( isset($_POST['submit']) )
{
	//Error checking for login information
	if (empty($_POST['user']) )
	{
		echo "\n<div class='container'>\n\t<div class ='alert alert-danger'>";
		echo "<center>Username is needed</center>";
		echo "\n\t</div>\n</div>";
		exit();
	}	
	else if (empty($_POST['pass1']) || empty($_POST['pass2']) )
	{
		echo "\n<div class='container'>\n\t<div class ='alert alert-danger'>";
		echo "<center>Both passwords are needed</center>";
		echo "\n\t</div>\n</div>";
		exit();
	}
	else if ($_POST['pass1'] != $_POST['pass2']){
		echo "\n<div class='container'>\n\t<div class ='alert alert-danger'>";
		echo "<center>Both passwords should match</center>";
		echo "\n\t</div>\n</div>";
		exit();
	}
		//Error checking contact information
		else if (empty($_POST['fname']) || empty($_POST['lname']) || empty($_POST['email']) || empty($_POST['phone']) )
        {
                echo "\n<div class='container'>\n\t<div class ='alert alert-danger'>";
                echo "<center>All contact information is needed</center>";
                echo "\n\t</div>\n</div>";
                exit();
        }
		else if (!is_numeric($_POST['phone']))
        {
                echo "\n<div class='container'>\n\t<div class ='alert alert-danger'>";
                echo "<center>Phone number must be an integer</center>";
                echo "\n\t</div>\n</div>";
                exit();
        }
		//Error checking address information
		else if (empty($_POST['street']) || empty($_POST['city']) || empty($_POST['state']) || empty($_POST['zip']))
        {
                echo "\n<div class='container'>\n\t<div class ='alert alert-danger'>";
                echo "<center>All address information is needed</center>";
                echo "\n\t</div>\n</div>";
                exit();
        }
		else if (strlen($_POST['state']) != 2)
        {
                echo "\n<div class='container'>\n\t<div class ='alert alert-danger'>";
                echo "<center>State should be inserted as 2 character value</center>";
				echo "<center>Example: \"Missouri\" should be \"MO\"</center>";
                echo "\n\t</div>\n</div>";
                exit();
		}
		else if (!is_numeric($_POST['zip']))
        {
                echo "\n<div class='container'>\n\t<div class ='alert alert-danger'>";
                echo "<center>Zip code must be an integer</center>";
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
	$user = htmlspecialchars($_POST['user']);
	
	//Run username against dB
	$query = "SELECT * FROM DB_1.customer WHERE username =$1" ;
	$stmt = pg_prepare($conn, "lab8", $query)  or die( pg_last_error() );
	$result = pg_execute($conn, "lab8" ,array($user) )  or die( pg_last_error() );
	//Check to see if login user exists, if not do nothing
	if(pg_num_rows($result) == 1)
	{
		echo "\n<div class='container'>\n\t<div class ='alert alert-danger'>";
		echo "<center>User already exists. Please register a different Username.</center>";
		echo "\n\t</div>\n</div>";
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

	//Insert user into the database
	$query = "INSERT INTO DB_1.customer (username,salt,hashpass,lastName,firstName,user_email,street,city,state,zip,phoneNumber, userType) VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11, $12)";
	
	$state = pg_prepare($conn,"insert",$query) or die( "Error:". pg_last_error() );
	$queryInsert = pg_execute($conn,"insert",array($user,$salt,$passHashed,htmlspecialchars($_POST['lname']), htmlspecialchars($_POST['fname']),htmlspecialchars($_POST['email']),htmlspecialchars($_POST['street']),htmlspecialchars($_POST['city']),htmlspecialchars($_POST['state']),htmlspecialchars($_POST['zip']),htmlspecialchars($_POST['phone']), 'customer'  ) )  or die( "Error:". pg_last_error() );
	
	session_start();

	// Instead of setting a cookie, we'll set a key/value pair in $_SESSION
	$_SESSION['loggedin'] = $username;
	//Condition handling: if user was previously on the cart, redirect them to it
	if ($_SESSION['prevPage'] === 'cart'){
		header("Location: http://babbage.cs.missouri.edu/~cs3380s14grp10/ShoppingCart/index.php");
	} 
	else if ($_SESSION['prevPage'] === 'FA'){
		header("Location: http://babbage.cs.missouri.edu/~cs3380s14grp10/Tournaments/fa_registration.php");
	} 
	else if ($_SESSION['prevPage'] === 'TEAM'){
		header("Location: http://babbage.cs.missouri.edu/~cs3380s14grp10/Tournaments/team_registration.php");
	} 
	else {	
		echo "\n<div class='container'>\n\t<div class ='alert alert-success'>";
		echo "<center>Thanks for registering!</center>";
		echo "\n\t</div>\n</div>";
	}
	exit;
}
?>

</body>
</html>
