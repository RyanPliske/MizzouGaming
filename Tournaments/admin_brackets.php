<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="PHP Shopping Cart Using Sessions" /> 
<meta name="keywords" content="shopping cart tutorial, shopping cart, php, sessions" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Tournaments</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" type="text/css" href="../ProductForm/css/product_form.css" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../Bootstrap/css/bootstrap.styles.css" rel="stylesheet">
<style>
	.sub_mit{
		text-align: center;
	}	
  .center {text-align: center;}
  table.bracket {
	  border-collapse: collapse;
	  border: none;
	  font: small arial, helvetica, sans-serif;
	}
	td.bracket {
	  vertical-align: middle;
	  width: 10em;
	  margin: 0;
	  padding: 0;
	}
	p.bracket {
	  border-bottom: solid 1px black;
	  margin: 0;
	  padding: 5px 5px 2px 5px;
	}
	.winner{ 
		color:blue;
	}
</style>
</head>
<body>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../Bootstrap/js/bootstrap.min.js"></script>
<!-- Current homepage format -->
<div id="page">
<?php
        //get the common header from header.php
        set_include_path('..');
        require "admin_header.php";
?>

<?php
	//connect to your database here
	include("../secure/database.php");
	$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD);
?>


</head>
<body>
<div id="main">
	<div class="content">
<?php
	//Get tournament id from the URL 
	$tournament_name = $_GET['id'];	 
	//Display Page Title
	echo '<div id="pagetitle">'.$_GET['id'].'</div>';
	//Display Image
	$query = 'SELECT * FROM DB_1.product WHERE productname = $1';
	$stmt = pg_prepare($conn, 'img', $query) or die("prepare failed: ".pg_last_error() );
	$result = pg_execute($conn, 'img', array($tournament_name) ) or die ("execute failed: ".pg_last_error() );
	while($line = pg_fetch_array($result, NULL, PGSQL_ASSOC) )
		{
				echo '<center><img src="../Store/'.$line['imgpath'].'"></center>';
				$teammates = $line['team_members'];
		}

	//Reset Session
	$username = $_SESSION['admin_loggedin'];
	$_SESSION = array();
	$_SESSION['admin_loggedin'] = $username;
	
	//Check team members to see if it's a team game or not
	if ($teammates != 1) //Then it's a team game
	{
	//---------------------------------Check for Advances--------------------------------------------------------------------------------------------------------------------------------
	if ( isset($_POST['adv'] ) || isset($_POST['undo'] ) )
	{
		$query = 'SELECT DISTINCT ON (team_name) team_name, wins, seed FROM DB_1.roster WHERE tournament = $1';
		$stmt = pg_prepare($conn, 'changes', $query) or die("prepare failed: ".pg_last_error() );
		$result = pg_execute($conn, 'changes', array($tournament_name) ) or die ("execute failed: ".pg_last_error() );
		while($line = pg_fetch_array($result, NULL, PGSQL_ASSOC) )
		{
			//Check if any team needs to be advanced--------------------------------------------------------
			if ($_POST['adv']==$line['team_name'])
			{
				//Increase wins for this team (May need to use array)
				$wins = $line['wins'];
				if($wins <= 2) {
					$addWin = $wins + 1;
				} else {
					$addWin = 3;
				}
				
				//Insert this update into the Database
				$query = 'UPDATE DB_1.roster SET wins = $1 WHERE tournament = $2 AND team_name = $3';
				$stmt = pg_prepare($conn, 'advance', $query) or die("prepare failed: ".pg_last_error() );
				$result = pg_execute($conn, 'advance', array($addWin, $tournament_name, $line['team_name']) ) or die ("execute failed: ".pg_last_error() );
			}

			//Check if any bracket advances need to be undone---------------------------------------------
			if ($_POST['undo']==$line['team_name'])
			{
				//Decrease wins for this team
				$wins = $line['wins'];
				if($wins >= 1) {
					$subWin = $wins - 1;
				} else {
					$subWin = 0;
				}
				
				//Insert this update into the Database
				$query = 'UPDATE DB_1.roster SET wins = $1 WHERE tournament = $2 AND team_name = $3';
				$stmt = pg_prepare($conn, 'undo_advance', $query) or die("prepare failed: ".pg_last_error() );
				$result = pg_execute($conn, 'undo_advance', array($subWin, $tournament_name, $line['team_name']) ) or die ("execute failed: ".pg_last_error() );
			}
		}
	}
	//End Check for Advances--------------------------------------------------------------------------------------------------------------------------------
	//---------------------------------Get Teams from Database--------------------------------------------------------------------------------------------------------------------------------
		$query = 'SELECT DISTINCT ON (team_name) seed, team_name,  player1, player2, player3, player4, wins FROM DB_1.roster WHERE tournament = $1';
		//$query = 'SELECT DISTINCT ON (seed) seed, team_name,  player1, player2, player3, player4, wins FROM DB_1.roster WHERE tournament = $1';
		$stmt = pg_prepare($conn, 'teams', $query) or die("prepare failed: ".pg_last_error() );
		$result = pg_execute($conn, 'teams', array($tournament_name) ) or die ("execute failed: ".pg_last_error() );
		$numRows = pg_num_rows($result);
		
		//If No one has registered for the Event
		if($numRows == 0 )
		{
			echo "\n</br><div class ='alert alert-warning alert-dismissable'>";
			echo'<center>No Teams have registered for this Tournament.</center>';
			echo "\n</div>";
		}
		else
		{
			//Display Table of Teams*****************************************************************************************************
			echo "\n</br><center><div class='panel panel-primary'><div class='panel-footer'><h5>Teams</h5></div></div></center>";
			echo"\n<center><table class='table table-responsive table-striped'>\n\t<tr>\n ";
			//build header row using pg_field name iterated over pg_num_fields
			$i = pg_num_fields($result);
			//pg_field_name
			for($j=0;$j<$i;$j++) 
			{
				echo "\t\t<td class='center'><strong>".pg_field_name($result,$j)."</strong></td>\n";	
			}
			echo "\t</tr>\n\t<tr>\n";
			//Give Seeds------------------------------------------------------------------------------------------------------------------------------------------------------------------
			$counter = 1;
			while($line = pg_fetch_array($result, NULL, PGSQL_ASSOC) )
			{
				//Display Team Rows with Data
				foreach ($line as $col_value)
				{
					echo "\t\t<td class='center'>$col_value</td>\n";
				}
				echo "\n</tr>\n";
				
					switch($counter)
					{
						case 1:
										//Give 1 seed
										$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_name = $3';
										$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
										$final = pg_execute($conn, $counter, array(1, $tournament_name, $line['team_name']) ) or die ("execute failed: ".pg_last_error() );
										//Load Data into Session Array
										$_SESSION['1_team_name'] = $line['team_name'];
										$_SESSION['1_wins'] = $line['wins'];
										break;
						case 2:		//If less than 8 teams Give 4 seed
										if ($numRows < 8)
										{
											$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_name = $3';
											$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
											$final = pg_execute($conn, $counter, array(4, $tournament_name, $line['team_name']) ) or die ("execute failed: ".pg_last_error() );
											//Load Data into Session Array
											$_SESSION['4_team_name'] = $line['team_name'];
											$_SESSION['4_wins'] = $line['wins'];
										}
										else
										{
											//Else there are 8 teams so Give 8 seed
											$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_name = $3';
											$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
											$final = pg_execute($conn, $counter, array(8, $tournament_name, $line['team_name']) ) or die ("execute failed: ".pg_last_error() );
											//Load Data into Session Array
											$_SESSION['8_team_name'] = $line['team_name'];
											$_SESSION['8_wins'] = $line['wins'];
										}
										break;
						case 3:
										if ($numRows < 8)
										{
											$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_name = $3';
											$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
											$final = pg_execute($conn, $counter, array(5, $tournament_name, $line['team_name']) ) or die ("execute failed: ".pg_last_error() );
											//Load Data into Session Array
											$_SESSION['5_team_name'] = $line['team_name'];
											$_SESSION['5_wins'] = $line['wins'];
										}
										else
										{
											$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_name = $3';
											$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
											$final = pg_execute($conn, $counter, array(4, $tournament_name, $line['team_name']) ) or die ("execute failed: ".pg_last_error() );
											//Load Data into Session Array
											$_SESSION['4_team_name'] = $line['team_name'];
											$_SESSION['4_wins'] = $line['wins'];
										}
										break;
						case 4:
										if ($numRows < 8)
										{
											$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_name = $3';
											$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
											$final = pg_execute($conn, $counter, array(3, $tournament_name, $line['team_name']) ) or die ("execute failed: ".pg_last_error() );
											//Load Data into Session Array
											$_SESSION['3_team_name'] = $line['team_name'];
											$_SESSION['3_wins'] = $line['wins'];
										}
										else
										{
											$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_name = $3';
											$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
											$final = pg_execute($conn, $counter, array(5, $tournament_name, $line['team_name']) ) or die ("execute failed: ".pg_last_error() );
											//Load Data into Session Array
											$_SESSION['5_team_name'] = $line['team_name'];
											$_SESSION['5_wins'] = $line['wins'];
										}
										break;
						case 5:
										if ($numRows < 8)
										{
											$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_name = $3';
											$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
											$final = pg_execute($conn, $counter, array(6, $tournament_name, $line['team_name']) ) or die ("execute failed: ".pg_last_error() );
											//Load Data into Session Array
											$_SESSION['6_team_name'] = $line['team_name'];
											$_SESSION['6_wins'] = $line['wins'];
										}
										else
										{
											$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_name = $3';
											$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
											$final = pg_execute($conn, $counter, array(3, $tournament_name, $line['team_name']) ) or die ("execute failed: ".pg_last_error() );
											//Load Data into Session Array
											$_SESSION['3_team_name'] = $line['team_name'];
											$_SESSION['3_wins'] = $line['wins'];
										}
										break;
						case 6:
										if ($numRows < 8)
										{
											$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_name = $3';
											$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
											$final = pg_execute($conn, $counter, array(2, $tournament_name, $line['team_name']) ) or die ("execute failed: ".pg_last_error() );
											//Load Data into Session Array
											$_SESSION['2_team_name'] = $line['team_name'];
											$_SESSION['2_wins'] = $line['wins'];
										}
										else
										{
											$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_name = $3';
											$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
											$final = pg_execute($conn, $counter, array(6, $tournament_name, $line['team_name']) ) or die ("execute failed: ".pg_last_error() );
											//Load Data into Session Array
											$_SESSION['6_team_name'] = $line['team_name'];
											$_SESSION['6_wins'] = $line['wins'];
										}
										break;
						case 7:
										if ($numRows < 8)
										{
											$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_name = $3';
											$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
											$final = pg_execute($conn, $counter, array(7, $tournament_name, $line['team_name']) ) or die ("execute failed: ".pg_last_error() );
											//Load Data into Session Array
											$_SESSION['7_team_name'] = $line['team_name'];
											$_SESSION['7_wins'] = $line['wins'];
										}
										else
										{
											$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_name = $3';
											$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
											$final = pg_execute($conn, $counter, array(2, $tournament_name, $line['team_name']) ) or die ("execute failed: ".pg_last_error() );
											//Load Data into Session Array
											$_SESSION['2_team_name'] = $line['team_name'];
											$_SESSION['2_wins'] = $line['wins'];
										}
										break;
						case 8:
										$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_name = $3';
										$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
										$final = pg_execute($conn, $counter, array(7, $tournament_name, $line['team_name']) ) or die ("execute failed: ".pg_last_error() );
										//Load Data into Session Array
										$_SESSION['8_team_name'] = $line['team_name'];
										$_SESSION['8_wins'] = $line['wins'];
										break;
					}
				//increase Counter
				$counter++;
			}
			//End Seeds----------------------------------------------------------------------------------------------------------------------
			echo "\n</table></center>";
			//End of Display Table of Teams*******************************************************************************************************
			
			//first randomize the order, Right now i'm ordering by name (Random Enough)
			$query = 'SELECT DISTINCT ON (team_name) team_name, wins FROM DB_1.roster WHERE tournament = $1 ORDER BY team_name';
			$stmt = pg_prepare($conn, 'team_bracket', $query) or die("prepare failed: ".pg_last_error() );
			$result = pg_execute($conn, 'team_bracket', array($tournament_name) ) or die ("execute failed: ".pg_last_error() );
			$numRows = pg_num_rows($result);
			//If less than eight teams, display eight team bracket
			/*NOTE: i'm only writing error checking for teams upwards of 6 teams, if less than six, then  bracket won't generate*/
			if ($numRows >= 6 && $numRows <= 8)
			{
				//Display 8 Team Bracket****************************************************************************************************************
				echo "\n</br><center><div class='panel panel-primary'><div class='panel-footer'><h5>Bracket</h5></div></div></center>";
				//Form is within the Table
				echo '<form method="POST" action="admin_brackets.php?id='.$tournament_name.'" enctype="multipart/form-data">';
				echo "\n<table class='bracket'>\n";
				$rowCount = 1;
				while($line = pg_fetch_array($result, NULL, PGSQL_ASSOC) )
				{
					//Row One
					if ($rowCount == 1)
					{
						echo "\n<tr>";
						echo "\n\t<td class='bracket'><p class='bracket'>1. ".$line['team_name']."</p></td>";
						//if less than 8 teams (7teams)
						if ($numRows < 8)
						{
							//This team advances immediately and gets a free "win" for ROUND 1
							//NOTE this value will be used to check wins over other teams and be inserted into the Db
							echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>1. ".$line['team_name']."</p></td>";
							$_SESSION['winner1v8'] = 1;
						}
						else
						//The bracket is full, no need to give a bye
						{
							//IF seed 1 beats seed 8
							//Since this is the 1st seed's row, we have to get 8th seed's wins and name (Loaded from Session Array)
							if ($line['wins'] < $_SESSION['8_wins'])
							{
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>8. ".$_SESSION['8_team_name']."</p></td>";
								$_SESSION['winner1v8'] = 8;
							}
							else if ($line['wins'] > $_SESSION['8_wins'])
							{
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>1. ".$line['team_name']."</p></td>";
								$_SESSION['winner1v8'] = 1;
							}
							else
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket'>winner</p></td>";
						}
						//IF seed 1 vs seed 8 beats winner of seed4 vs seed5
							if ( ($_SESSION['1_wins'] + $_SESSION['8_wins'])	> ($_SESSION['4_wins'] + $_SESSION['5_wins']) && ($_SESSION['4_wins'] + $_SESSION['5_wins']) != 0)
							{
								if ($_SESSION['winner1v8'] == 1)
								{
									echo "\n\t<td class='bracket' rowspan='4'><p class='bracket winner'>1. ".$_SESSION['1_team_name']."</p></td>";
									$_SESSION['winner18v45']  = 1;
								}
								else
								{
									echo "\n\t<td class='bracket' rowspan='4'><p class='bracket winner'>8. ".$_SESSION['8_team_name']."</p></td>";
									$_SESSION['winner18v45']  = 8;
								}
							}
							else 	if ( ($_SESSION['1_wins'] + $_SESSION['8_wins'])	< ($_SESSION['4_wins'] + $_SESSION['5_wins']) && ($_SESSION['4_wins'] + $_SESSION['5_wins']) != 0)
							{
								if ($_SESSION['4_wins'] > $_SESSION['5_wins'])
								{
									echo "\n\t<td class='bracket' rowspan='4'><p class='bracket winner'>4. ".$_SESSION['4_team_name']."</p></td>";
									$_SESSION['winner18v45']  = 4;
								}
								else
								{
									echo "\n\t<td class='bracket' rowspan='4'><p class='bracket winner'>5. ".$_SESSION['5_team_name']."</p></td>";
									$_SESSION['winner18v45']  = 5;
								}
							}
							else
								echo "\n\t<td class='bracket' rowspan='4'><p class='bracket'>winner</p></td>";
						//IF top half beats winner of bottom half of bracket then they are the Tournament Winner!
							if ( ($_SESSION['1_wins'] + $_SESSION['8_wins']	+ $_SESSION['4_wins'] + $_SESSION['5_wins']) > ($_SESSION['3_wins'] + $_SESSION['6_wins']+ $_SESSION['2_wins'] + $_SESSION['7_wins']) && ($_SESSION['3_wins'] + $_SESSION['6_wins']+$_SESSION['2_wins'] + $_SESSION['7_wins'])  > 2)
							{
								if ($_SESSION['winner18v45'] == 1)
								{
									echo "\n\t<td class='bracket' rowspan='8'><p class='bracket winner'>1. ".$_SESSION['1_team_name']."</p></td>";
								}
								else if ($_SESSION['winner18v45'] == 8)
								{
									echo "\n\t<td class='bracket' rowspan='8'><p class='bracket winner'>8. ".$_SESSION['8_team_name']."</p></td>";
								}
								else if ($_SESSION['winner18v45'] == 4)
								{
									echo "\n\t<td class='bracket' rowspan='8'><p class='bracket winner'>4. ".$_SESSION['4_team_name']."</p></td>";
								}
								else if ($_SESSION['winner18v45'] == 5)
								{
									echo "\n\t<td class='bracket' rowspan='8'><p class='bracket winner'>5. ".$_SESSION['5_team_name']."</p></td>";
								}
							}
							else if ( ($_SESSION['1_wins'] + $_SESSION['8_wins']	+ $_SESSION['4_wins'] + $_SESSION['5_wins']) < ($_SESSION['3_wins'] + $_SESSION['6_wins']+ $_SESSION['2_wins'] + $_SESSION['7_wins']) && ($_SESSION['1_wins'] + $_SESSION['8_wins']+$_SESSION['4_wins'] + $_SESSION['5_wins'])  > 2)
							{
								if ($_SESSION['3_wins'] > ($_SESSION['6_wins'] +$_SESSION['2_wins'] +$_SESSION['7_wins'] ) )
								{
									echo "\n\t<td class='bracket' rowspan='8'><p class='bracket winner'>3. ".$_SESSION['3_team_name']."</p></td>";
								}
								else if ($_SESSION['6_wins'] > ($_SESSION['3_wins'] +$_SESSION['2_wins'] +$_SESSION['7_wins'] ) )
								{
									echo "\n\t<td class='bracket' rowspan='8'><p class='bracket winner'>6. ".$_SESSION['6_team_name']."</p></td>";
								}
								else if ($_SESSION['2_wins'] > ($_SESSION['6_wins'] +$_SESSION['3_wins'] +$_SESSION['7_wins'] ) )
								{
									echo "\n\t<td class='bracket' rowspan='8'><p class='bracket winner'>2. ".$_SESSION['2_team_name']."</p></td>";
								}
								else if ($_SESSION['7_wins'] > ($_SESSION['6_wins'] +$_SESSION['2_wins'] +$_SESSION['3_wins'] ) )
								{
									echo "\n\t<td class='bracket' rowspan='8'><p class='bracket winner'>7. ".$_SESSION['7_team_name']."</p></td>";
								}
							}
							else
								echo "\n\t<td class='bracket' rowspan='8'><p class='bracket'>winner</p></td>";
						echo "\n</tr>";
						$rowCount++;
						continue;
					}
					//Row Two
					if ($rowCount == 2)
					{
						echo "\n<tr>";
						if ($numRows < 8)
						{
							//Display This Row
							echo "\n\t<td class='bracket'><p class='bracket'>8.  bye  </p></td>";
							echo "\n<tr>";
							//And Display Next Row
							echo "\n\t<td class='bracket'><p class='bracket'>4. ".$line['team_name']."</p></td>";
							//Check for a winner of seed 4 vs seed5
							//Since this is the 4th seed's row, we have to get 5th seed's wins and name (Loaded from Session Array)
							if ($line['wins'] < $_SESSION['5_wins'])
							{
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>5. ".$_SESSION['5_team_name']."</p></td>";
								$_SESSION['winner4v5'] = 5;
							}
							else if ($line['wins'] > $_SESSION['5_wins'])
							{
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>4. ".$line['team_name']."</p></td>";
								$_SESSION['winner4v5'] = 4;
							}
							else
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket'>winner</p></td>";
							echo "\n</tr>";
						}
						else
						{
							//Just Display This Row
							echo "\n\t<td class='bracket'><p class='bracket'>8. ".$line['team_name']."</p></td>";
						}
						echo "\n</tr>";
						$rowCount++;
						continue;
					}
					//Row Three
					if ($rowCount == 3)
					{
						if ($numRows < 8)
						{
							//Display Next Row
							echo "\n<tr>";
							echo "\n\t<td class='bracket'><p class='bracket'>5. ".$line['team_name']."</p></td>";
							echo "\n</tr>";
						}
						else
						{
							//Display this Row
							echo "\n\t<td class='bracket'><p class='bracket'>4. ".$line['team_name']."</p></td>";
							//Check for a winner of seed 4 vs seed5
							//Since this is the 4th seed's row, we have to get 5th seed's wins and name (Loaded from Session Array)
							if ($_SESSION['4_wins'] < $_SESSION['5_wins'])
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>5. ".$_SESSION['5_team_name']."</p></td>";
							else if ($_SESSION['4_wins'] > $_SESSION['5_wins'])
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>4. ".$line['team_name']."</p></td>";
							else
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket'>winner</p></td>";
							echo "\n</tr>";
						}
						$rowCount++;
						continue;
					}
					//Row Four
					if ($rowCount == 4)
					{
						if ($numRows < 8)
						{
							//Display Next Row
							echo "\n<tr>";
							echo "\n\t<td class='bracket'><p class='bracket'>3. ".$line['team_name']."</p></td>";
							//Check for winner of 3 vs 6
							if ($_SESSION['3_wins'] < $_SESSION['6_wins'])
							{
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>6. ".$_SESSION['6_team_name']."</p></td>";
								$_SESSION['winner3v6'] = 6;
							}
							else if ($_SESSION['3_wins'] > $_SESSION['6_wins'])
							{
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>3. ".$_SESSION['3_team_name']."</p></td>";
								$_SESSION['winner3v6']  = 3;
							}
							else
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket'>winner</p></td>";
							//Check for winner of winners round 2 (might just do a sum)
							if ( ($_SESSION['3_wins'] + $_SESSION['6_wins'])	> ($_SESSION['2_wins'] + $_SESSION['7_wins']) && ($_SESSION['2_wins'] + $_SESSION['7_wins']) != 0)
							{
								if ($_SESSION['winner3v6'] == 3)
								{
									echo "\n\t<td class='bracket' rowspan='4'><p class='bracket winner'>3. ".$_SESSION['3_team_name']."</p></td>";
									$_SESSION['winner36v27']  = 3;
								}
								else
								{
									echo "\n\t<td class='bracket' rowspan='4'><p class='bracket winner'>6. ".$_SESSION['6_team_name']."</p></td>";
									$_SESSION['winner36v27']  = 6;
								}
							}
							else 	if ( ($_SESSION['3_wins'] + $_SESSION['6_wins'])	< ($_SESSION['2_wins'] + $_SESSION['7_wins']) && ($_SESSION['3_wins'] + $_SESSION['6_wins']) != 0)
							{
								if ($_SESSION['winner2v7'] == 7)
								{
									echo "\n\t<td class='bracket' rowspan='4'><p class='bracket winner'>7. ".$_SESSION['7_team_name']."</p></td>";
									$_SESSION['winner36v27']  = 7;
								}
								else
								{
									echo "\n\t<td class='bracket' rowspan='4'><p class='bracket winner'>2. ".$_SESSION['2_team_name']."</p></td>";
									$_SESSION['winner36v27']  = 2;
								}
							}
							else
								echo "\n\t<td class='bracket' rowspan='4'><p class='bracket'>winner</p></td>";
							echo "\n</tr>";
						}
						else
						{
							//Display This Row
							echo "\n<tr>";
							echo "\n\t<td class='bracket'><p class='bracket'>5. ".$line['team_name']."</p></td>";
							echo "\n</tr>";
						}
						$rowCount++;
						continue;
					}
					//Row Five
					if ($rowCount == 5)
					{
						if ($numRows < 8)
						{
						//Display Next Row
							echo "\n<tr>";
							echo "\n\t<td class='bracket'><p class='bracket'>6. ".$line['team_name']."</p></td>";
							echo "\n</tr>";
						}
						else
						{
							//Display This Row
							echo "\n<tr>";
							echo "\n\t<td class='bracket'><p class='bracket'>3. ".$line['team_name']."</p></td>";
							//Check for Winners of 3 vs 6
							if ($_SESSION['3_wins'] < $_SESSION['6_wins'])
							{
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>6. ".$_SESSION['6_team_name']."</p></td>";
								$_SESSION['winner3v6'] = 6;
							}
							else if ($_SESSION['3_wins'] > $_SESSION['6_wins'])
							{
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>3. ".$_SESSION['3_team_name']."</p></td>";
								$_SESSION['winner3v6']  = 3;
							}
							else
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket'>winner</p></td>";
							//Check for winner of winners round 2 (might just do a sum)
							if ( ($_SESSION['3_wins'] + $_SESSION['6_wins'])	> ($_SESSION['2_wins'] + $_SESSION['7_wins']) && ($_SESSION['2_wins'] + $_SESSION['7_wins']) != 0)
							{
								if ($_SESSION['winner3v6'] == 3)
								{
									echo "\n\t<td class='bracket' rowspan='4'><p class='bracket winner'>3. ".$_SESSION['3_team_name']."</p></td>";
									$_SESSION['winner36v27']  = 3;
								}
								else
								{
									echo "\n\t<td class='bracket' rowspan='4'><p class='bracket winner'>6. ".$_SESSION['6_team_name']."</p></td>";
									$_SESSION['winner36v27']  = 6;
								}
							}
							
							else if ( ($_SESSION['3_wins'] + $_SESSION['6_wins'])	< ($_SESSION['2_wins'] + $_SESSION['7_wins']) && ($_SESSION['3_wins'] + $_SESSION['6_wins']) != 0)
							{
								if ($_SESSION['2_wins'] < $_SESSION['7_wins'] && ($_SESSION['2_wins'] + $_SESSION['7_wins']) > 1 )
								{
									echo "\n\t<td class='bracket' rowspan='4'><p class='bracket winner'>7. ".$_SESSION['7_team_name']."</p></td>";
									$_SESSION['winner36v27']  = 7;
								}
								else if ($_SESSION['2_wins'] > $_SESSION['7_wins'] && ($_SESSION['2_wins'] + $_SESSION['7_wins']) > 1 )
								{
									echo "\n\t<td class='bracket' rowspan='4'><p class='bracket winner'>2. ".$_SESSION['2_team_name']."</p></td>";
									$_SESSION['winner36v27']  = 2;
								}
							}
							/*else
								echo "\n\t<td class='bracket' rowspan='4'><p class='bracket'>WINNER</p></td>";*/
							echo "\n</tr>";
						}
						$rowCount++;
						continue;
					}
					//Row Six
					if ($rowCount == 6)
					{
						//If there are more than seven teams let it pass
						if ($numRows < 8)
						{
							//Display Next Row
							echo "\n<tr>";
							echo "\n\t<td class='bracket'><p class='bracket'>2. ".$line['team_name']."</p></td>";
							echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>2. ".$line['team_name']."</p></td>";
							echo "\n</tr>";
							if ($numRows == 6)
							{
								//If number of rows is 6, then last seed becomes a bye
								echo "\n<tr>";
								echo "\n\t<td class='bracket'><p class='bracket'>7. bye</p></td>";
								echo "\n</tr>";
							}
						}
						else
						{
							//Display Next Row
							echo "\n<tr>";
							echo "\n\t<td class='bracket'><p class='bracket'>6. ".$line['team_name']."</p></td>";
							//Check for winners of 2 v 7
							//Since this is the 2nd seed's row, we have to get 7th seed's wins and name (Loaded from Session Array)
							if ($line['wins'] < $_SESSION['7_wins'] && ($line['wins'] + $_SESSION['7_wins']) > 1 )
							{
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>7. ".$_SESSION['7_team_name']."</p></td>";
								$_SESSION['winner2v7'] = 7;
							}
							else if ($line['wins'] > $_SESSION['7_wins'] && ($line['wins'] + $_SESSION['7_wins']) > 1 )
							{
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>2. ".$line['team_name']."</p></td>";
								$_SESSION['winner2v7'] = 2;
							}
							else
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket'>winner</p></td>";
							echo "\n</tr>";

						}
						$rowCount++;
						continue;
					}
					//Row Seven
					if ($rowCount == 7)
					{
						if ($numRows < 8)
						{
							//Display Next Row 
							echo "\n<tr>";
							echo "\n\t<td class='bracket'><p class='bracket'>7. ".$line['team_name']."</p></td>";
							echo "\n</tr>";
						}
						else
						{
							//Display This Row
							echo "\n<tr>";
							echo "\n\t<td class='bracket'><p class='bracket'>2. ".$line['team_name']."</p></td>";
							//Check for winners of 2 v 7
							//Since this is the 2nd seed's row, we have to get 7th seed's wins and name (Loaded from Session Array)
							if ($line['wins'] < $_SESSION['7_wins'] && ($line['wins'] + $_SESSION['7_wins']) > 1 )
							{
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>7. ".$_SESSION['7_team_name']."</p></td>";
								$_SESSION['winner2v7'] = 7;
							}
							else if ($line['wins'] > $_SESSION['7_wins'] && ($line['wins'] + $_SESSION['7_wins']) > 1 )
							{
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>2. ".$line['team_name']."</p></td>";
								$_SESSION['winner2v7'] = 2;
							}
							else
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket'>winner</p></td>";
							echo "\n</tr>";
						}
						$rowCount++;
						continue;
					}
					//Row Eight
					if ($rowCount == 8)
					{
						echo "\n<tr>";
						echo "\n\t<td class='bracket'><p class='bracket'>7. ".$line['team_name']."</p></td>";
						echo "\n</tr>";
					}
				}
				//Footer of Table
				echo "\n</form>";
				echo "\n</table></br></br>";
				

				//Begin Form for advancing teams and undoing advancing ----------------------------------------------------------------------------------------
	echo '<div id="form_body" class="col-xs-6 col-md-6">
			<form method="POST" action="admin_brackets.php?id='.$tournament_name.'" enctype="multipart/form-data">
			<fieldset>
				<ul>
					<li>
				<label for="type">Advance a team one spot in the bracket </label>
										<select name="adv" id="adv"> ';

					$query = 'SELECT DISTINCT ON (team_name) team_name FROM DB_1.roster WHERE tournament = $1 ORDER BY team_name';
					$stmt = pg_prepare($conn,"advanced",$query) or die( pg_last_error() );
					$result = pg_execute($conn, "advanced", array($tournament_name)) or die( pg_last_error() );
					while ($line=pg_fetch_array($result,null,PGSQL_ASSOC))
					{
						echo"<option value='".$line['team_name']."'>".$line['team_name']."</option>\n";
					}
							echo '
								</select>
								<div style="clear:both; float:none"></div>
								</br>
								<label for="submit"> </label>
								<input type="Submit" name="Submit" id="Submit" value="Advance" class="button" />
							</li>
						</ul>	
					</fieldset>
					</form>
					</div>';
	echo '<div id="form_body" class="col-xs-6 col-md-6">
			<form method="POST" action="admin_brackets.php?id='.$tournament_name.'" enctype="multipart/form-data">
			<fieldset>
				<ul>
					<li>
				<label for="type">If you\'ve made a mistake, Undo: </label>
										<select name="undo" id="undo"> ';

					$query = 'SELECT DISTINCT ON (team_name) team_name FROM DB_1.roster WHERE tournament = $1 ORDER BY team_name';
					$stmt = pg_prepare($conn,"undo_advanced",$query) or die( pg_last_error() );
					$result = pg_execute($conn, "undo_advanced", array($tournament_name)) or die( pg_last_error() );
					while ($line=pg_fetch_array($result,null,PGSQL_ASSOC))
					{
						echo"<option value='".$line['team_name']."'>".$line['team_name']."</option>\n";
					}
							echo '
								</select>
								<div style="clear:both; float:none"></div>
								</br>
								<label for="submit"> </label>
								<input type="Submit" name="Submit" id="Submit" value="Undo" class="button" />
							</li>
						</ul>	
					</fieldset>
					</form>
					</div>					
					<div class="clear">&nbsp;</div>';
	//End Form----------------------------------------------------------------------------------------------------------------------------------------------------------
			}
			//End of Display 8 Team Bracket****************************************************************************************************************
			//Else not enough teams to generate bracket
			else
			{
				echo "\n</br><div class ='alert alert-danger alert-dismissable'>";
				echo'<center>Not Enough Teams have registered for this Tournament.</center>';
				echo "\n</div>";
			}
		}
		
	}
	//Else it's not a team game
	else
	{
		//---------------------------------Check for Advances--------------------------------------------------------------------------------------------------------------------------------
		if ( isset($_POST['adv'] ) || isset($_POST['undo'] ) )
		{
			$query = 'SELECT DISTINCT ON (team_captain_username) team_captain_username, wins, seed FROM DB_1.roster WHERE tournament = $1';
			$stmt = pg_prepare($conn, 'changes', $query) or die("prepare failed: ".pg_last_error() );
			$result = pg_execute($conn, 'changes', array($tournament_name) ) or die ("execute failed: ".pg_last_error() );
			while($line = pg_fetch_array($result, NULL, PGSQL_ASSOC) )
			{
				//Check if any team needs to be advanced--------------------------------------------------------
				if ($_POST['adv']==$line['team_captain_username'])
				{
					//Increase wins for this team (May need to use array)
					$wins = $line['wins'];
					if($wins <= 2) {
						$addWin = $wins + 1;
					} else {
						$addWin = 3;
					}
					
					//Insert this update into the Database
					$query = 'UPDATE DB_1.roster SET wins = $1 WHERE tournament = $2 AND team_captain_username = $3';
					$stmt = pg_prepare($conn, 'advance', $query) or die("prepare failed: ".pg_last_error() );
					$result = pg_execute($conn, 'advance', array($addWin, $tournament_name, $line['team_captain_username']) ) or die ("execute failed: ".pg_last_error() );
				}

				//Check if any bracket advances need to be undone---------------------------------------------
				if ($_POST['undo']==$line['team_captain_username'])
				{
					//Decrease wins for this team
					$wins = $line['wins'];
					if($wins >= 1) {
						$subWin = $wins - 1;
					} else {
						$subWin = 0;
					}
					
					//Insert this update into the Database
					$query = 'UPDATE DB_1.roster SET wins = $1 WHERE tournament = $2 AND team_captain_username = $3';
					$stmt = pg_prepare($conn, 'undo_advance', $query) or die("prepare failed: ".pg_last_error() );
					$result = pg_execute($conn, 'undo_advance', array($subWin, $tournament_name, $line['team_captain_username']) ) or die ("execute failed: ".pg_last_error() );
				}
			}
		}
		//End Check for Advances--------------------------------------------------------------------------------------------------------------------------------
		//---------------------------------Get Players from Database--------------------------------------------------------------------------------------------------------------------------
		$query = 'SELECT DISTINCT ON (team_captain_username) seed, player1 AS player, team_name AS team_sponsor, wins FROM DB_1.roster WHERE tournament = $1';
		$stmt = pg_prepare($conn, 'players', $query) or die("prepare failed: ".pg_last_error() );
		$result = pg_execute($conn, 'players', array($tournament_name) ) or die ("execute failed: ".pg_last_error() );
		$numRows = pg_num_rows($result);
	
		//If No one has registered for the Event
		if($numRows == 0 )
		{
			echo "\n</br><div class ='alert alert-warning alert-dismissable'>";
			echo'<center>No Teams have registered for this Tournament.</center>';
			echo "\n</div>";
		}
		else
		{
			//Display Table of Players**********************************************************************************************
			echo "\n</br><center><div class='panel panel-primary'><div class='panel-footer'><h5>Players</h5></div></div></center>";
			echo"\n<center><table class='table table-responsive table-striped'>\n\t<tr>\n ";
			//build header row using pg_field name iterated over pg_num_fields
			$i = pg_num_fields($result);
			//pg_field_name
			for($j=0;$j<$i;$j++) 
			{
				echo "\t\t<td class='center'><strong>".pg_field_name($result,$j)."</strong></td>\n";	
			}
			echo "\t</tr>\n\t<tr>\n";
			//Give Seeds------------------------------------------------------------------------------------------------------------------------------------------------------------------
			$counter = 1;
			while($line = pg_fetch_array($result, NULL, PGSQL_ASSOC) )
			{
				foreach ($line as $col_value)
				{
					echo "\t\t<td class='center'>$col_value</td>\n";
				}
				echo "\n</tr>\n";
				
					switch($counter)
					{
						case 1:
										//Give 1 seed
										$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_captain_username = $3';
										$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
										$final = pg_execute($conn, $counter, array(1, $tournament_name, $line['player']) ) or die ("execute failed: ".pg_last_error() );
										//Load Data into Session Array
										$_SESSION['1_team_name'] = $line['player'];
										$_SESSION['1_wins'] = $line['wins'];
										break;
						case 2:		//If less than 8 teams Give 4 seed
										if ($numRows < 8)
										{
											$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_captain_username = $3';
											$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
											$final = pg_execute($conn, $counter, array(4, $tournament_name, $line['player']) ) or die ("execute failed: ".pg_last_error() );
											//Load Data into Session Array
											$_SESSION['4_team_name'] = $line['player'];
											$_SESSION['4_wins'] = $line['wins'];
										}
										else
										{
											//Else there are 8 teams so Give 8 seed
											$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_captain_username = $3';
											$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
											$final = pg_execute($conn, $counter, array(8, $tournament_name, $line['player']) ) or die ("execute failed: ".pg_last_error() );
											//Load Data into Session Array
											$_SESSION['8_team_name'] = $line['player'];
											$_SESSION['8_wins'] = $line['wins'];
										}
										break;
						case 3:
										if ($numRows < 8)
										{
											$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_captain_username = $3';
											$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
											$final = pg_execute($conn, $counter, array(5, $tournament_name, $line['player']) ) or die ("execute failed: ".pg_last_error() );
											//Load Data into Session Array
											$_SESSION['5_team_name'] = $line['player'];
											$_SESSION['5_wins'] = $line['wins'];
										}
										else
										{
											$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_captain_username = $3';
											$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
											$final = pg_execute($conn, $counter, array(4, $tournament_name, $line['player']) ) or die ("execute failed: ".pg_last_error() );
											//Load Data into Session Array
											$_SESSION['4_team_name'] = $line['player'];
											$_SESSION['4_wins'] = $line['wins'];
										}
										break;
						case 4:
										if ($numRows < 8)
										{
											$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_captain_username = $3';
											$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
											$final = pg_execute($conn, $counter, array(3, $tournament_name, $line['player']) ) or die ("execute failed: ".pg_last_error() );
											//Load Data into Session Array
											$_SESSION['3_team_name'] = $line['player'];
											$_SESSION['3_wins'] = $line['wins'];
										}
										else
										{
											$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_captain_username = $3';
											$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
											$final = pg_execute($conn, $counter, array(5, $tournament_name, $line['player']) ) or die ("execute failed: ".pg_last_error() );
											//Load Data into Session Array
											$_SESSION['5_team_name'] = $line['player'];
											$_SESSION['5_wins'] = $line['wins'];
										}
										break;
						case 5:
										if ($numRows < 8)
										{
											$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_captain_username = $3';
											$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
											$final = pg_execute($conn, $counter, array(6, $tournament_name, $line['player']) ) or die ("execute failed: ".pg_last_error() );
											//Load Data into Session Array
											$_SESSION['6_team_name'] = $line['player'];
											$_SESSION['6_wins'] = $line['wins'];
										}
										else
										{
											$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_captain_username = $3';
											$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
											$final = pg_execute($conn, $counter, array(3, $tournament_name, $line['player']) ) or die ("execute failed: ".pg_last_error() );
											//Load Data into Session Array
											$_SESSION['3_team_name'] = $line['player'];
											$_SESSION['3_wins'] = $line['wins'];
										}
										break;
						case 6:
										if ($numRows < 8)
										{
											$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_captain_username = $3';
											$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
											$final = pg_execute($conn, $counter, array(2, $tournament_name, $line['player']) ) or die ("execute failed: ".pg_last_error() );
											//Load Data into Session Array
											$_SESSION['2_team_name'] = $line['player'];
											$_SESSION['2_wins'] = $line['wins'];
										}
										else
										{
											$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_captain_username = $3';
											$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
											$final = pg_execute($conn, $counter, array(6, $tournament_name, $line['player']) ) or die ("execute failed: ".pg_last_error() );
											//Load Data into Session Array
											$_SESSION['6_team_name'] = $line['player'];
											$_SESSION['6_wins'] = $line['wins'];
										}
										break;
						case 7:
										if ($numRows < 8)
										{
											$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_captain_username = $3';
											$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
											$final = pg_execute($conn, $counter, array(7, $tournament_name, $line['player']) ) or die ("execute failed: ".pg_last_error() );
											//Load Data into Session Array
											$_SESSION['7_team_name'] = $line['player'];
											$_SESSION['7_wins'] = $line['wins'];
										}
										else
										{
											$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_captain_username = $3';
											$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
											$final = pg_execute($conn, $counter, array(2, $tournament_name, $line['player']) ) or die ("execute failed: ".pg_last_error() );
											//Load Data into Session Array
											$_SESSION['2_team_name'] = $line['player'];
											$_SESSION['2_wins'] = $line['wins'];
										}
										break;
						case 8:
										$query = 'UPDATE DB_1.roster SET seed = $1 WHERE tournament = $2 AND team_captain_username = $3';
										$stmt = pg_prepare($conn, $counter, $query) or die("prepare failed: ".pg_last_error() );
										$final = pg_execute($conn, $counter, array(7, $tournament_name, $line['player']) ) or die ("execute failed: ".pg_last_error() );
										//Load Data into Session Array
										$_SESSION['8_team_name'] = $line['player'];
										$_SESSION['8_wins'] = $line['wins'];
										break;
					}
				//increase Counter
				$counter++;
			}
			echo "\n</table></center></br>"; 
			//End Table of Players ***************************************************************************************************
			
			//first randomize the order, Right now i'm ordering by name (Random Enough)
			$query = 'SELECT DISTINCT ON (team_captain_username) team_captain_username, wins FROM DB_1.roster WHERE tournament = $1 ORDER BY team_captain_username';
			$stmt = pg_prepare($conn, 'team_bracket', $query) or die("prepare failed: ".pg_last_error() );
			$result = pg_execute($conn, 'team_bracket', array($tournament_name) ) or die ("execute failed: ".pg_last_error() );
		
			$numRows = pg_num_rows($result);
			//If less than eight teams, display eight team bracket
			/*NOTE: i'm only writing error checking for teams upwards of 6 teams, if less than six, then  bracket won't generate*/
			
			if ($numRows >= 6 && $numRows <= 8)
			{
				//Display 8 Team Bracket****************************************************************************************************************
				echo "\n</br><center><div class='panel panel-primary'><div class='panel-footer'><h5>Bracket</h5></div></div></center>";
				//Form is within the Table
				echo '<form method="POST" action="admin_brackets.php?id='.$tournament_name.'" enctype="multipart/form-data">';
				echo "\n<table class='bracket'>\n";
				$rowCount = 1;
				while($line = pg_fetch_array($result, NULL, PGSQL_ASSOC) )
				{
					//Row One
					if ($rowCount == 1)
					{
						echo "\n<tr>";
						echo "\n\t<td class='bracket'><p class='bracket'>1. ".$line['team_captain_username']."</p></td>";
						//if less than 8 teams (7teams)
						if ($numRows < 8)
						{
							//This team advances immediately and gets a free "win" for ROUND 1
							//NOTE this value will be used to check wins over other teams and be inserted into the Db
							echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>1. ".$line['team_captain_username']."</p></td>";
							$_SESSION['winner1v8'] = 1;
						}
						else
						//The bracket is full, no need to give a bye
						{
							//IF seed 1 beats seed 8
							//Since this is the 1st seed's row, we have to get 8th seed's wins and name (Loaded from Session Array)
							if ($line['wins'] < $_SESSION['8_wins'])
							{
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>8. ".$_SESSION['8_team_name']."</p></td>";
								$_SESSION['winner1v8'] = 8;
							}
							else if ($line['wins'] > $_SESSION['8_wins'])
							{
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>1. ".$line['team_captain_username']."</p></td>";
								$_SESSION['winner1v8'] = 1;
							}
							else
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket'>winner</p></td>";
						}
						//IF seed 1 vs seed 8 beats winner of seed4 vs seed5
							if ( ($_SESSION['1_wins'] + $_SESSION['8_wins'])	> ($_SESSION['4_wins'] + $_SESSION['5_wins']) && ($_SESSION['4_wins'] + $_SESSION['5_wins']) != 0)
							{
								if ($_SESSION['winner1v8'] == 1)
								{
									echo "\n\t<td class='bracket' rowspan='4'><p class='bracket winner'>1. ".$_SESSION['1_team_name']."</p></td>";
									$_SESSION['winner18v45']  = 1;
								}
								else
								{
									echo "\n\t<td class='bracket' rowspan='4'><p class='bracket winner'>8. ".$_SESSION['8_team_name']."</p></td>";
									$_SESSION['winner18v45']  = 8;
								}
							}
							else 	if ( ($_SESSION['1_wins'] + $_SESSION['8_wins'])	< ($_SESSION['4_wins'] + $_SESSION['5_wins']) && ($_SESSION['4_wins'] + $_SESSION['5_wins']) != 0)
							{
								if ($_SESSION['4_wins'] > $_SESSION['5_wins'])
								{
									echo "\n\t<td class='bracket' rowspan='4'><p class='bracket winner'>4. ".$_SESSION['4_team_name']."</p></td>";
									$_SESSION['winner18v45']  = 4;
								}
								else
								{
									echo "\n\t<td class='bracket' rowspan='4'><p class='bracket winner'>5. ".$_SESSION['5_team_name']."</p></td>";
									$_SESSION['winner18v45']  = 5;
								}
							}
							else
								echo "\n\t<td class='bracket' rowspan='4'><p class='bracket'>winner</p></td>";
						//IF top half beats winner of bottom half of bracket then they are the Tournament Winner!
							if ( ($_SESSION['1_wins'] + $_SESSION['8_wins']	+ $_SESSION['4_wins'] + $_SESSION['5_wins']) > ($_SESSION['3_wins'] + $_SESSION['6_wins']+ $_SESSION['2_wins'] + $_SESSION['7_wins']) && ($_SESSION['3_wins'] + $_SESSION['6_wins']+$_SESSION['2_wins'] + $_SESSION['7_wins'])  > 2)
							{
								if ($_SESSION['winner18v45'] == 1)
								{
									echo "\n\t<td class='bracket' rowspan='8'><p class='bracket winner'>1. ".$_SESSION['1_team_name']."</p></td>";
								}
								else if ($_SESSION['winner18v45'] == 8)
								{
									echo "\n\t<td class='bracket' rowspan='8'><p class='bracket winner'>8. ".$_SESSION['8_team_name']."</p></td>";
								}
								else if ($_SESSION['winner18v45'] == 4)
								{
									echo "\n\t<td class='bracket' rowspan='8'><p class='bracket winner'>4. ".$_SESSION['4_team_name']."</p></td>";
								}
								else if ($_SESSION['winner18v45'] == 5)
								{
									echo "\n\t<td class='bracket' rowspan='8'><p class='bracket winner'>5. ".$_SESSION['5_team_name']."</p></td>";
								}
							}
							else if ( ($_SESSION['1_wins'] + $_SESSION['8_wins']	+ $_SESSION['4_wins'] + $_SESSION['5_wins']) < ($_SESSION['3_wins'] + $_SESSION['6_wins']+ $_SESSION['2_wins'] + $_SESSION['7_wins']) && ($_SESSION['1_wins'] + $_SESSION['8_wins']+$_SESSION['4_wins'] + $_SESSION['5_wins'])  > 2)
							{
								if ($_SESSION['3_wins'] > ($_SESSION['6_wins'] +$_SESSION['2_wins'] +$_SESSION['7_wins'] ) )
								{
									echo "\n\t<td class='bracket' rowspan='8'><p class='bracket winner'>3. ".$_SESSION['3_team_name']."</p></td>";
								}
								else if ($_SESSION['6_wins'] > ($_SESSION['3_wins'] +$_SESSION['2_wins'] +$_SESSION['7_wins'] ) )
								{
									echo "\n\t<td class='bracket' rowspan='8'><p class='bracket winner'>6. ".$_SESSION['6_team_name']."</p></td>";
								}
								else if ($_SESSION['2_wins'] > ($_SESSION['6_wins'] +$_SESSION['3_wins'] +$_SESSION['7_wins'] ) )
								{
									echo "\n\t<td class='bracket' rowspan='8'><p class='bracket winner'>2. ".$_SESSION['2_team_name']."</p></td>";
								}
								else if ($_SESSION['7_wins'] > ($_SESSION['6_wins'] +$_SESSION['2_wins'] +$_SESSION['3_wins'] ) )
								{
									echo "\n\t<td class='bracket' rowspan='8'><p class='bracket winner'>7. ".$_SESSION['7_team_name']."</p></td>";
								}
							}
							else
								echo "\n\t<td class='bracket' rowspan='8'><p class='bracket'>winner</p></td>";
						echo "\n</tr>";
						$rowCount++;
						continue;
					}
					//Row Two
					if ($rowCount == 2)
					{
						echo "\n<tr>";
						if ($numRows < 8)
						{
							//Display This Row
							echo "\n\t<td class='bracket'><p class='bracket'>8.  bye  </p></td>";
							echo "\n<tr>";
							//And Display Next Row
							echo "\n\t<td class='bracket'><p class='bracket'>4. ".$line['team_captain_username']."</p></td>";
							//Check for a winner of seed 4 vs seed5
							//Since this is the 4th seed's row, we have to get 5th seed's wins and name (Loaded from Session Array)
							if ($line['wins'] < $_SESSION['5_wins'])
							{
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>5. ".$_SESSION['5_team_name']."</p></td>";
								$_SESSION['winner4v5'] = 5;
							}
							else if ($line['wins'] > $_SESSION['5_wins'])
							{
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>4. ".$line['team_captain_username']."</p></td>";
								$_SESSION['winner4v5'] = 4;
							}
							else
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket'>winner</p></td>";
							echo "\n</tr>";
						}
						else
						{
							//Just Display This Row
							echo "\n\t<td class='bracket'><p class='bracket'>8. ".$line['team_captain_username']."</p></td>";
						}
						echo "\n</tr>";
						$rowCount++;
						continue;
					}
					//Row Three
					if ($rowCount == 3)
					{
						if ($numRows < 8)
						{
							//Display Next Row
							echo "\n<tr>";
							echo "\n\t<td class='bracket'><p class='bracket'>5. ".$line['team_captain_username']."</p></td>";
							echo "\n</tr>";
						}
						else
						{
							//Display this Row
							echo "\n\t<td class='bracket'><p class='bracket'>4. ".$line['team_captain_username']."</p></td>";
							//Check for a winner of seed 4 vs seed5
							//Since this is the 4th seed's row, we have to get 5th seed's wins and name (Loaded from Session Array)
							if ($_SESSION['4_wins'] < $_SESSION['5_wins'])
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>5. ".$_SESSION['5_team_name']."</p></td>";
							else if ($_SESSION['4_wins'] > $_SESSION['5_wins'])
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>4. ".$line['team_captain_username']."</p></td>";
							else
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket'>winner</p></td>";
							echo "\n</tr>";
						}
						$rowCount++;
						continue;
					}
					//Row Four
					if ($rowCount == 4)
					{
						if ($numRows < 8)
						{
							//Display Next Row
							echo "\n<tr>";
							echo "\n\t<td class='bracket'><p class='bracket'>3. ".$line['team_captain_username']."</p></td>";
							//Check for winner of 3 vs 6
							if ($_SESSION['3_wins'] < $_SESSION['6_wins'])
							{
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>6. ".$_SESSION['6_team_name']."</p></td>";
								$_SESSION['winner3v6'] = 6;
							}
							else if ($_SESSION['3_wins'] > $_SESSION['6_wins'])
							{
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>3. ".$_SESSION['3_team_name']."</p></td>";
								$_SESSION['winner3v6']  = 3;
							}
							else
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket'>winner</p></td>";
							//Check for winner of winners round 2 (might just do a sum)
							if ( ($_SESSION['3_wins'] + $_SESSION['6_wins'])	> ($_SESSION['2_wins'] + $_SESSION['7_wins']) && ($_SESSION['2_wins'] + $_SESSION['7_wins']) != 0)
							{
								if ($_SESSION['winner3v6'] == 3)
								{
									echo "\n\t<td class='bracket' rowspan='4'><p class='bracket winner'>3. ".$_SESSION['3_team_name']."</p></td>";
									$_SESSION['winner36v27']  = 3;
								}
								else
								{
									echo "\n\t<td class='bracket' rowspan='4'><p class='bracket winner'>6. ".$_SESSION['6_team_name']."</p></td>";
									$_SESSION['winner36v27']  = 6;
								}
							}
							else 	if ( ($_SESSION['3_wins'] + $_SESSION['6_wins'])	< ($_SESSION['2_wins'] + $_SESSION['7_wins']) && ($_SESSION['3_wins'] + $_SESSION['6_wins']) != 0)
							{
								if ($_SESSION['winner2v7'] == 7)
								{
									echo "\n\t<td class='bracket' rowspan='4'><p class='bracket winner'>7. ".$_SESSION['7_team_name']."</p></td>";
									$_SESSION['winner36v27']  = 7;
								}
								else
								{
									echo "\n\t<td class='bracket' rowspan='4'><p class='bracket winner'>2. ".$_SESSION['2_team_name']."</p></td>";
									$_SESSION['winner36v27']  = 2;
								}
							}
							else
								echo "\n\t<td class='bracket' rowspan='4'><p class='bracket'>winner</p></td>";
							echo "\n</tr>";
						}
						else
						{
							//Display This Row
							echo "\n<tr>";
							echo "\n\t<td class='bracket'><p class='bracket'>5. ".$line['team_captain_username']."</p></td>";
							echo "\n</tr>";
						}
						$rowCount++;
						continue;
					}
					//Row Five
					if ($rowCount == 5)
					{
						if ($numRows < 8)
						{
						//Display Next Row
							echo "\n<tr>";
							echo "\n\t<td class='bracket'><p class='bracket'>6. ".$line['team_captain_username']."</p></td>";
							echo "\n</tr>";
						}
						else
						{
							//Display This Row
							echo "\n<tr>";
							echo "\n\t<td class='bracket'><p class='bracket'>3. ".$line['team_captain_username']."</p></td>";
							//Check for Winners of 3 vs 6
							if ($_SESSION['3_wins'] < $_SESSION['6_wins'])
							{
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>6. ".$_SESSION['6_team_name']."</p></td>";
								$_SESSION['winner3v6'] = 6;
							}
							else if ($_SESSION['3_wins'] > $_SESSION['6_wins'])
							{
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>3. ".$_SESSION['3_team_name']."</p></td>";
								$_SESSION['winner3v6']  = 3;
							}
							else
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket'>winner</p></td>";
							//Check for winner of winners round 2 (might just do a sum)
							if ( ($_SESSION['3_wins'] + $_SESSION['6_wins'])	> ($_SESSION['2_wins'] + $_SESSION['7_wins']) && ($_SESSION['2_wins'] + $_SESSION['7_wins']) != 0)
							{
								if ($_SESSION['winner3v6'] == 3)
								{
									echo "\n\t<td class='bracket' rowspan='4'><p class='bracket winner'>3. ".$_SESSION['3_team_name']."</p></td>";
									$_SESSION['winner36v27']  = 3;
								}
								else
								{
									echo "\n\t<td class='bracket' rowspan='4'><p class='bracket winner'>6. ".$_SESSION['6_team_name']."</p></td>";
									$_SESSION['winner36v27']  = 6;
								}
							}
							
							else 	if ( ($_SESSION['3_wins'] + $_SESSION['6_wins'])	< ($_SESSION['2_wins'] + $_SESSION['7_wins']) && ($_SESSION['3_wins'] + $_SESSION['6_wins']) != 0)
							{
								if ($_SESSION['2_wins'] < $_SESSION['7_wins']) 
								{
									echo "\n\t<td class='bracket' rowspan='4'><p class='bracket winner'>7. ".$_SESSION['7_team_name']."</p></td>";
									$_SESSION['winner36v27']  = 7;
								}
								else
								{
									echo "\n\t<td class='bracket' rowspan='4'><p class='bracket winner'>2. ".$_SESSION['2_team_name']."</p></td>";
									$_SESSION['winner36v27']  = 2;
								}
							}
							else
								echo "\n\t<td class='bracket' rowspan='4'><p class='bracket'>winner</p></td>";
							echo "\n</tr>";
						}
						$rowCount++;
						continue;
					}
					//Row Six
					if ($rowCount == 6)
					{
						//If there are more than seven teams let it pass
						if ($numRows < 7)
						{
							//Display Next Row
							echo "\n<tr>";
							echo "\n\t<td class='bracket'><p class='bracket'>2. ".$line['team_captain_username']."</p></td>";
							echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>2. ".$line['team_captain_username']."</p></td>";
							echo "\n</tr>";
							if ($numRows == 6)
							{
								//If number of rows is 6, then last seed becomes a bye
								echo "\n<tr>";
								echo "\n\t<td class='bracket'><p class='bracket'>7. bye</p></td>";
								echo "\n</tr>";
							}
						}
						else
						{
							//Display Next Row
							echo "\n<tr>";
							echo "\n\t<td class='bracket'><p class='bracket'>2. ".$line['team_captain_username']."</p></td>";
							//Check for winners of 2 v 7
							//Since this is the 2nd seed's row, we have to get 7th seed's wins and name (Loaded from Session Array)
							if ($line['wins'] < $_SESSION['7_wins'] && ($line['wins'] + $_SESSION['7_wins']) > 1 )
							{
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>7. ".$_SESSION['7_team_name']."</p></td>";
								$_SESSION['winner2v7'] = 7;
							}
							if ($line['wins'] > $_SESSION['7_wins'] && ($line['wins'] + $_SESSION['7_wins']) > 1 )
							{
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>2. ".$line['team_captain_username']."</p></td>";
								$_SESSION['winner2v7'] = 2;
							}
							else
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket'>winner</p></td>";
							echo "\n</tr>";

						}
						$rowCount++;
						continue;
					}
					//Row Seven
					if ($rowCount == 7)
					{
						if ($numRows < 8)
						{
							//Display Next Row 
							echo "\n<tr>";
							echo "\n\t<td class='bracket'><p class='bracket'>7. ".$line['team_captain_username']."</p></td>";
							echo "\n</tr>";
						}
						else
						{
							//Display This Row
							echo "\n<tr>";
							echo "\n\t<td class='bracket'><p class='bracket'>2. ".$line['team_captain_username']."</p></td>";
							//Check for winners of 2 v 7
							//Since this is the 2nd seed's row, we have to get 7th seed's wins and name (Loaded from Session Array)
							if ($line['wins'] < $_SESSION['7_wins'])
							{
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>7. ".$_SESSION['7_team_name']."</p></td>";
								$_SESSION['winner2v7'] = 7;
							}
							else if ($line['wins'] > $_SESSION['7_wins'])
							{
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket winner'>2. ".$line['team_captain_username']."</p></td>";
								$_SESSION['winner2v7'] = 2;
							}
							else
								echo "\n\t<td class='bracket' rowspan='2'><p class='bracket'>winner</p></td>";
							echo "\n</tr>";
						}
						$rowCount++;
						continue;
					}
					//Row Eight
					if ($rowCount == 8)
					{
						echo "\n<tr>";
						echo "\n\t<td class='bracket'><p class='bracket'>7. ".$line['team_captain_username']."</p></td>";
						echo "\n</tr>";
					}
				}
				//Footer of Table
				echo "\n</form>";
				echo "\n</table></br></br>";
			
			
			
			
			
				//Begin Form for advancing teams and undoing advancing ----------------------------------------------------------------------------------------
	echo '<div id="form_body" class="col-xs-6 col-md-6">
			<form method="POST" action="admin_brackets.php?id='.$tournament_name.'" enctype="multipart/form-data">
			<fieldset>
				<ul>
					<li>
				<label for="type">Advance a team one spot in the bracket </label>
										<select name="adv" id="adv"> ';

					$query = 'SELECT DISTINCT ON (team_captain_username) team_captain_username FROM DB_1.roster WHERE tournament = $1 ORDER BY team_captain_username';
					$stmt = pg_prepare($conn,"advanced",$query) or die( pg_last_error() );
					$result = pg_execute($conn, "advanced", array($tournament_name)) or die( pg_last_error() );
					while ($line=pg_fetch_array($result,null,PGSQL_ASSOC))
					{
						echo"<option value='".$line['team_captain_username']."'>".$line['team_captain_username']."</option>\n";
					}
							echo '
								</select>
								<div style="clear:both; float:none"></div>
								</br>
								<label for="submit"> </label>
								<input type="Submit" name="Submit" id="Submit" value="Advance" class="button" />
							</li>
						</ul>	
					</fieldset>
					</form>
					</div>';
	echo '<div id="form_body" class="col-xs-6 col-md-6">
			<form method="POST" action="admin_brackets.php?id='.$tournament_name.'" enctype="multipart/form-data">
			<fieldset>
				<ul>
					<li>
				<label for="type">If you\'ve made a mistake, Undo: </label>
										<select name="undo" id="undo"> ';

					$query = 'SELECT DISTINCT ON (team_captain_username) team_captain_username FROM DB_1.roster WHERE tournament = $1 ORDER BY team_captain_username';
					$stmt = pg_prepare($conn,"undo_advanced",$query) or die( pg_last_error() );
					$result = pg_execute($conn, "undo_advanced", array($tournament_name)) or die( pg_last_error() );
					while ($line=pg_fetch_array($result,null,PGSQL_ASSOC))
					{
						echo"<option value='".$line['team_captain_username']."'>".$line['team_captain_username']."</option>\n";
					}
							echo '
								</select>
								<div style="clear:both; float:none"></div>
								</br>
								<label for="submit"> </label>
								<input type="Submit" name="Submit" id="Submit" value="Undo" class="button" />
							</li>
						</ul>	
					</fieldset>
					</form>
					</div>					
					<div class="clear">&nbsp;</div>';
	//End Form----------------------------------------------------------------------------------------------------------------------------------------------------------
			}//Else not enough players
			else
			{
				echo "\n</br><div class ='alert alert-danger alert-dismissable'>";
				echo'<center>Not Enough Teams have registered for this Tournament.</center>';
				echo "\n</div>";
			}
		}//End Final Else Statement
	}//End Players Section
	echo '<div class="clear">&nbsp;</div>';
	

	pg_close($conn);
?>

				</div> <!--End Content------------------------------------------------------------------------------------------------------------------------------------------------------->
			</div> <!--End Page------------------------------------------------------------------------------------------------------------------------------------------------------------------------>
</body>
</html>

