<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<title>Order Management</title>
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
		<?php
		if(!$_GET['complete']) { include "new_orders.php"; }
		else { include "finished_orders.php"; }
		?>
		<!-- Current homepage format -->
		<div class="clear">&nbsp;</div>
	</div>
</div>
</div>	
</body>
</html>
<!-- End Current homepage format -->
