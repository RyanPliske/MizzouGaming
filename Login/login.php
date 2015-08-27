<?php
	// To access $_SESSION, we have to call session_start()
	if (!isset($_SESSION))
	{
		session_start();
	}
	//check login
	$loggedIn = empty($_SESSION['loggedin']) ? false : $_SESSION['loggedin'];
	if ($loggedIn )
	{
		header("Location: https://babbage.cs.missouri.edu/~cs3380s14grp10/Home/home.php");
		exit;
	}
	//check https
	else if ($_SERVER['HTTPS'] == "on") 
	{
		handle_login();
	}
	else 
	{
			header("Location:https://babbage.cs.missouri.edu/~cs3380s14grp10/Login/login.php");
			exit;
	}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset=UTF-8>
<title>Login</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../Bootstrap/css/bootstrap.styles.css" rel="stylesheet">
<style type="text/css">
.center {text-align: center;}	
</style>
</head>
<body>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../Bootstrap/js/bootstrap.min.js"></script>
<?php
        //get the common header from header.php
        set_include_path('..');
        require "header.php";
?>
<form method="POST" action="login.php">
		<center>
		<div id="container-fluid">
			<div class="row">
				<div class="col-xs-0 col-md-3">
				</div>
				<div class="col-xs-12 col-md-6">
					<!-- Takes in user login info -->
					<h4> User Login:</h4>
					Username: <input type="text" name="username" /><br /><br />
					Password: <input type="password" name="password" /><br /><br />
					<input type="submit" name="submit" value="submit" /><br />
				</div>
				<div class="col-xs-0 col-md-3">
				</div>
			</div>
		</div>
		</center>
        </form>

        <div>
                <h3 class="center">OR:</h3>
        </div>
        <div class="center">
                <h4><a href="registration.php">Click Here to Register</a></h2>
        </div>
</form>
<?php

function handle_login()
{
	//Submit Button for User Login
	if( isset($_POST['submit']) )
	{
	//setup connection
	require 'pgsql.conf';
	$conn = pg_connect("host=$db_host dbname=$db_name user=$db_user password=$db_pass");
	if (!$conn) 
	{
	  echo "<br/>An error occurred with the connection to the server.<br/>";
	  die();
	}
		
		//Run variables against dB
		$query = "SELECT salt, hashpass, userType FROM DB_1.customer WHERE username =$1" ;
		$stmt = pg_prepare($conn, "check", $query)  or die( "ERROR:". pg_last_error() );
		$result = pg_execute($conn, "check" ,array(htmlspecialchars($_POST['username'])))  or die( "ERROR:". pg_last_error() );
		$row = pg_fetch_assoc($result);
		$salt = $row['salt'];
		$salty = sha1($salt);
		$salty = trim($salt);
		$password = htmlspecialchars($_POST['password']);
		$localHash = sha1($salty.$password);

		for ($i=0; $i<10000; $i++) //Slow Hashing
		{
			$localHash = sha1($localHash);
		}
		if ($localHash === $row['hashpass'] ) //if user exists then
		{
			//Condition handling:
			if ($row['usertype'] == "admin"){
				$_SESSION['admin_loggedin'] = htmlspecialchars($_POST['username']);
				header("Location: https://babbage.cs.missouri.edu/~cs3380s14grp10/admin.php");
				exit;
			}
			
			if ($_SESSION['prevPage'] === 'cart'){
				$_SESSION['loggedin'] = htmlspecialchars($_POST['username']);
				header("Location: http://babbage.cs.missouri.edu/~cs3380s14grp10/ShoppingCart/index.php");
				exit;
			} 
			else if ($_SESSION['prevPage'] === 'FA'){
				$_SESSION['loggedin'] = htmlspecialchars($_POST['username']);
				header("Location: http://babbage.cs.missouri.edu/~cs3380s14grp10/Tournaments/fa_registration.php");
				exit;
			} 
			else if ($_SESSION['prevPage'] === 'TEAM'){
				$_SESSION['loggedin'] = htmlspecialchars($_POST['username']);
				header("Location: http://babbage.cs.missouri.edu/~cs3380s14grp10/Tournaments/team_registration.php");
				exit;
			} 
			else {	
				$_SESSION['loggedin'] = htmlspecialchars($_POST['username']);
				header("Location: http://babbage.cs.missouri.edu/~cs3380s14grp10/Home/home.php");
				exit;
			}
		}
		else
		{
			//Check to see if login user exists, if not do nothing
			if(pg_num_rows($result) == 0)
			{
			echo "\n<div class ='alert alert-danger alert-dismissable'>";
			echo "\n\t<center>User does not exist please register by clicking Register link below</center>"; 
			echo "\n</div>";
			}
			else
			{
				echo "\n<div class ='alert alert-danger alert-dismissable'>";
				echo"\n\t<center>Incorrect Password</center>";
				echo "\n</div>";
			}
		}
	}
}
?>


</body>
</html>
