<?php
	// To access $_SESSION, we have to call session_start()
	if (!isset($_SESSION))
	{
		session_start();
	}
	//check login
	$loggedIn = empty($_SESSION['loggedin']) ? false : $_SESSION['loggedin'];
	$_SESSION['prevPage'] = 'TEAM';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="PHP FA Using Sessions" /> 
<meta name="keywords" content="fa, free agent, php, sessions" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Team Registration</title>
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
			
			<div id="pagetitle">Team Registration</div>
			<!-- Product Submission Form -->
			<?php if(!$loggedIn) 
			{ 
				echo "\n<div class ='alert alert-danger alert-dismissable'>";
				echo'<center><p class="empty">You must <a href="../Login/login.php">Login</a> or <a href="../Login/registration.php">Create User Account</a> first!</p></center>';
				echo "\n</div>";
			}
			else
			{
					//Figure out if they've purchased a tournament pass
					require 'pgsql.conf';
					$conn = pg_connect("host=$db_host dbname=$db_name user=$db_user password=$db_pass");
					if (!$conn)
					{
						echo "<br/>An error occurred with connecting to the server.<br/>";
						die();
					}
					//Find their cid
					$query = 'SELECT cid FROM DB_1.customer WHERE username = $1';
					$stmt = pg_prepare($conn,"find_cid",$query) or die( pg_last_error() );
					$result = pg_execute($conn, "find_cid", array($_SESSION['loggedin'])) or die( pg_last_error() );
					while ($line=pg_fetch_array($result,null,PGSQL_ASSOC))
					{
						$cid = $line['cid'];
					}
					//Using their cid, Run through the purchase order to see if they're purchased a tournament pass
					$query = 'SELECT DISTINCT ON (productid) * FROM DB_1.product AS tourny INNER JOIN DB_1.purchase_order AS receipt USING (productid) WHERE category = $1 AND cid = $2';
					$stmt = pg_prepare($conn,"tourny",$query) or die( pg_last_error() );
					$result = pg_execute($conn, "tourny", array("Tournament",$cid)) or die( pg_last_error() );
					//Check to see if anything is returned, if not display error message
					if (pg_num_rows($result) < 1)
					{
						echo "\n<div class ='alert alert-danger alert-dismissable'>";
						echo'<center>You have not purchased a team pass. Passes are on sale in the <a href="../Store/index.php">Store</a></center>';
						echo "\n</div>";
					}
					else //If yes which tournament(s)
					{
						echo '
						<div id="form_body">
							<form method="POST" action="team_registration.php" enctype="multipart/form-data">
								<fieldset> 
								<ul>
									<li>
									<label for="type">Tournament<em>*</em> </label>
									<select name="type" id="type"> 
									<option value="" > </option>\n';
						//display appropriate dropdown list			
						while ($line=pg_fetch_array($result,null,PGSQL_ASSOC)) 
						{
							echo"<option value='".$line['productname']."'>".$line['productname']."</option>\n";
						}
						echo 	'</select>
									<div style="clear:both; float:none"></div>
									</br>
									</li>
									<li>
									<label for="submit"> </label>
									<input type="Submit" name="Submit" id="Submit" value="Submit" class="button" />
									</li>
								</ul>	
							</fieldset>
							</form>
							</div></br>'; //Close first Form
						//if not selected, display message to submit first form
						if(empty($_POST['type']))
						{
							echo "\n</br>\n<div class ='alert alert-info alert-dismissable'>";
							echo'<center><p class="empty">Welcome! Which Tournament are you registering for?</p></center>';
							echo "\n</div>";
						}
						//When selected, spawn appropriate gamertag fields (4 if 4v4, 1 if 1v1), 
						else
						{
						$_SESSION['tourny'] = $_POST['type'];
						//Using their choice spawn appropriate number of gamertag fields
						$query = 'SELECT team_members FROM DB_1.product WHERE productname = $1';
						$stmt = pg_prepare($conn,"choice",$query) or die( pg_last_error() );
						$result = pg_execute($conn, "choice", array($_POST['type'])) or die( pg_last_error() );
						while ($line=pg_fetch_array($result,null,PGSQL_ASSOC)) 
						{
							$choice = $line['team_members'];
							$_SESSION['choice'] = $choice;
						}
						echo'
							<div id="pagetitle">'.$_POST['type'].' Registration</div>
							<div id="form_body">
								<form method="POST" action="team_registration.php" enctype="multipart/form-data">
								<fieldset> 
								<ul>
								<li>
								<label for="gt">Gamertag<em>*</em></label>';
							for ($k=0; $k < $choice; $k++)
							{
								echo '<input type="text" name="gt'.$k.'" id="gt'.$k.'" required="true" />';
							}
							echo'
										<div style="clear:both; float:none"></div>
									</li>
									</br>
									<li>';
								if ($choice > 1)
								{
									echo '
									<label for="gt">Team Name<em>*</em></label>
									<input type ="text" name="team" id="team" require="true"/>
									<div style="clear:both; float:none"></div>';
								}
								else
								{
								}
									echo'
									</br>
									<label for="submit"> </label>
									<input type="Submit" name="Register" id="Register" value="Register" class="button" />
									</li>
								</ul>	
							</fieldset>
								</form>
							</div>
						</div>
						<div class="clear">&nbsp;</div>
					</div>
				</div>
				</body>
				</html>';
						}
					}
			}
	//once second form submitted
	if( isset($_POST['Register']) )
	{
		//Check if user has registered for that tournament before.
		$user = htmlspecialchars($_SESSION['loggedin'] );
		$query = 'SELECT roster_id FROM DB_1.roster WHERE team_captain_username = $1 AND tournament = $2';
		$stmt = pg_prepare($conn, 'check', $query) or die( pg_last_error() );
		$result = pg_execute($conn, 'check', array($user, $tourny) ) or die( pg_last_error() );
		
		//If user hasn't registered, allow registration
		if (pg_num_rows($result) < 1000)
		{
			// declare variables for array to be sent to db
			for ($k=0; $k < $_SESSION['choice']; $k++)
			{
				$string = "gt".$k;
				$gamertag[$k] = htmlspecialchars($_POST[$string]);
			}	

			//insert into db
			$query = 'INSERT INTO DB_1.roster VALUES (DEFAULT,$1,$2,$3,$4,$5,$6,$7)';
			$stmt = pg_prepare($conn, 'insert', $query) or die ( "Prepare failed: ".pg_last_error() );
			$result = pg_execute($conn, 'insert', array($_SESSION['tourny'], $user, $gamertag[0], $gamertag[1], $gamertag[2], $gamertag[3], $_POST['team'] ) ) or die ( "Execute failed: ".pg_last_error() );
			
			//alert if insertion is succes
			if ($result)
			{
				echo "\n<div class ='alert alert-success alert-dismissable'>";
				echo'<center>Thank you for registering!</center>';
				echo "\n</div>";
			}
			else
			{
			}
		}
		//Else display error
		else
		{
			echo "\n<div class ='alert alert-danger alert-dismissable'>";
			echo'<center>You have already registered for that tournament.</center>';
			echo "\n</div>";
		}
		pg_close($conn);
	}
?>