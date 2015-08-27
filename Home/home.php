<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Mizzou Gaming</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="../Orders/css/style.css" />
<link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../Bootstrap/css/bootstrap.styles.css" rel="stylesheet">
<!--for image slider-->
<link href="themes/1/js-image-slider.css" rel="stylesheet" type="text/css" />
<script src="themes/1/js-image-slider.js" type="text/javascript"></script>
<style>        
	#main_box {
		text-align: center;
		border: 5px solid silver;
		width: 580px;
		height: 340px;
	}
	.center {text-align: center;}	
</style>
</head>
<body>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../Bootstrap/js/bootstrap.min.js"></script>
<?php
		if (!isset($_SESSION))
		{
			session_start();
			$_SESSION['prevPage'] = 'home';
		}
        //get the common header from header.php
        set_include_path('..');
	require "header.php";
	echo'<div id="main">';
	echo'<center><div id="pagetitle">Welcome to the Homepage</div></center>';
	echo'</div>';
	// Connect to database.
	include("../secure/database.php");
	$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD);

	// Query database for all products.
	$query = "SELECT DISTINCT ON (imgpath) * FROM DB_1.product ORDER BY imgpath";
	$result = pg_query($query) or die('Query failed: ' . pg_last_error());

	// Check if query was successful.
	if($result) {
        	// Get each product image
		echo '<div id="sliderFrame">';
		echo '<div id="slider">';
        	while($row = pg_fetch_array($result)) {
                	echo '<img src="../Store/' . $row['imgpath'] . '" alt="">';
		}
		echo ' </div>';
	}
	// Close database connection.
	pg_close($conn);

?>

</body>
</html>
