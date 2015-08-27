<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<title>Mizzou Gaming Store</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../Bootstrap/css/bootstrap.styles.css" rel="stylesheet">

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

<div id="main">
	<div class="content">
		<div id="pagetitle">Order Confirmation</div>
		<?php
		//connect to database
		include("../secure/database.php");
		$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD) or die("<p>Failed to connect to DB: </p>" . pg_last_error());

		if(isset($_POST['submit'])) {
			//Update database with new order
			$query = 'UPDATE DB_1.purchase_order SET ordercomplete = TRUE WHERE orderid = $1';

			$result = pg_prepare($conn, "complete_order", $query) or die('Could not connect: ' . pg_last_error());
			$result = pg_execute($conn, "complete_order", array($_POST['orderid'])) or die('Could not connect: ' . pg_last_error());
			
			if($result) {
				echo '<div class ="alert alert-success alert-dismissable center">Order successfully confirmed!</div>';
			} else {
				echo '<div class ="alert alert-danger alert-dismissable center">Failed to confirm order. Query Error: ' . pg_last_error() . '</div>';
			}
			
		} else {
			echo '<div class ="alert alert-danger alert-dismissable center">No button was pressed!<br>Select the Order # from the list.</div>';
		}
		?>
		<center><a class="center" href="index.php">Return to Incomplete Orders</a></center>
		<!-- Current homepage format -->
		<div class="clear">&nbsp;</div>
	</div>
</div>
	
</body>
</html>
<!-- End Current homepage format -->
