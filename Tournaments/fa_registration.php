<?php
	// To access $_SESSION, we have to call session_start()
	if (!isset($_SESSION))
	{
		session_start();
	}
	//check login
	$loggedIn = empty($_SESSION['loggedin']) ? false : $_SESSION['loggedin'];
	$_SESSION['prevPage'] = 'FA';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="PHP FA Using Sessions" /> 
<meta name="keywords" content="fa, free agent, php, sessions" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Free Agent Registration</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" type="text/css" href="css/product_form.css" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../Bootstrap/css/bootstrap.styles.css" rel="stylesheet">
<style>
	.sub_mit{
		text-align: center;
	}	
  .center {text-align: center;}
</style>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../Bootstrap/js/bootstrap.min.js"></script>
</head>
<!-- Current homepage format -->
<body>
<div id="page">
<?php
        //get the common header from header.php
        set_include_path('..');
        require "header.php";
?>
	<div id="main">
		<div class="content">
			
			<div id="pagetitle">Free Agent Submission Form</div>
			<!-- Product Submission Form -->
			<?php if(!$loggedIn) 
			{ 
				echo "\n<div class ='alert alert-danger alert-dismissable'>";
				echo'<center><p class="empty">You must <a href="../Login/login.php">Login</a> or <a href="../Login/registration.php">Create User Account</a> first!</p></center>';
				echo "\n</div>";
			}
			else
			{
			echo '
			<div id="form_body">
				<form method="POST" action="fa_registration.php" enctype="multipart/form-data">
					<fieldset>
						<ul>
							<li>
								<label for="gt">Gamertag<em>*</em></label>
								<input type="text" name="gt" id="gt" required="true" />
								<div style="clear:both; float:none"></div>
							</li>
							<li>
								<label for="desc">Give a Description of Yourself:<em>*</em></label><br>
								<textarea name="desc" id="desc" required="true"></textarea>
								<div style="clear:both; float:none"></div>
							</li>
							</br>
							<li>
								<label for="category">How do you want to be contacted by gamers?<em>*</em></label>
								<select name="category" id="category" required="true">
									<option value="Email">Email</option>
									<option value="Phone">Phone</option>
								</select>
								<div style="clear:both; float:none"></div>
							</li>
							<li>
								<label for="type">Tournament </label>
								<select name="type" id="type"> ';

							require 'pgsql.conf';
							$conn = pg_connect("host=$db_host dbname=$db_name user=$db_user password=$db_pass");
							if (!$conn)
							{
								echo "<br/>An error occurred with connecting to the server.<br/>";
								die();
							}
							$query = 'SELECT productname FROM DB_1.product WHERE category = $1 AND team_members > 1 ORDER BY productname';
							$stmt = pg_prepare($conn,"tab",$query) or die( pg_last_error() );
							$result = pg_execute($conn, "tab", array("Tournament")) or die( pg_last_error() );
							while ($line=pg_fetch_array($result,null,PGSQL_ASSOC))
							{
								echo"<option value='".$line['productname']."'>".$line['productname']."</option>\n";
							}
							echo '
								</select>
								<div style="clear:both; float:none"></div>
								</br>
								<label for="submit"> </label>
								<input type="Submit" name="Submit" id="Submit" value="Submit" class="button" />
							</li>
						</ul>	
					</fieldset>';
			}
			?>
				</form>
			</div>
			</div>
		<div class="clear">&nbsp;</div>
	</div>
</div>
</body>
</html>

<?php
	//once submitted
	if( isset($_POST['Submit']) )
	{
		//Check if user has registered for that tournmaent before.
		$user = htmlspecialchars($_SESSION['loggedin'] );
		$tourny = $_POST['type'];
		$query = 'SELECT * FROM DB_1.free_agents WHERE username = $1 AND tournament = $2';
		$stmt = pg_prepare($conn, 'check', $query) or die( pg_last_error() );
		$result = pg_execute($conn, 'check', array($user, $tourny) ) or die( pg_last_error() );

		if (pg_num_rows($result) == 0)
		{
			// declare variables for array to be sent to db
			//easy variables first
			$gamertag = htmlspecialchars($_POST['gt']);
			$desc = htmlspecialchars($_POST['desc']);
			$gamertag = htmlspecialchars($_POST['gt']);
			//variables we need from our database
			$query = 'SELECT cid, user_email, phonenumber FROM DB_1.customer WHERE username = $1';
			$stmt = pg_prepare($conn, 'declare', $query) or die( pg_last_error() );
			$result = pg_execute($conn, 'declare', array($user) ) or die( pg_last_error() );
			while ($line = pg_fetch_array($result, NULL, PGSQL_ASSOC) )
			{
				if ($_POST['category'] == 'Email')
				{
					$contact = $line['user_email'];
				}
				else
				{
					$contact = $line['phonenumber'];
				}
				$cid = $line['cid'];
			}
			//insert into db
			$query = 'INSERT INTO DB_1.free_agents VALUES (DEFAULT,$1,$2,$3,$4,$5, $6)';
			$stmt = pg_prepare($conn, 'insert', $query) or die ( "Prepare failed: ".pg_last_error() );
			$result = pg_execute($conn, 'insert', array($cid, $gamertag, $user, $contact, $tourny, $desc ) ) or die ( "Execute failed: ".pg_last_error() );
			
			//alert if insertion is succes
			if ($result)
			{
				echo "\n<div class ='alert alert-success alert-dismissable'>";
				echo'<center>Thank you for registering! Hope you find a team!</center>';
				echo "\n</div>";
			}
			else	//alert if insertion is failure
			{
				echo "\n<div class ='alert alert-danger alert-dismissable'>";
				echo'<center>You have already registered for that tournament.</center>';
				echo "\n</div>";
			}
		}
		else
		{
			echo "\n<div class ='alert alert-danger alert-dismissable'>";
			echo'<center>You have already registered for that tournament.</center>';
			echo "\n</div>";
		}
		pg_close($conn);
	}
?>
