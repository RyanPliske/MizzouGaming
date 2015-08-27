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
		include("../secure/database.php");
		$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD) or die("<p>Failed to connect to DB: </p>" . pg_last_error());

		if(isset($_POST['submit'])) {
			//Get order from database
			$query = 'SELECT * FROM DB_1.purchase_order WHERE orderid = $1';
			$result = pg_prepare($conn, "get_order_info", $query) or die('Could not connect: ' . pg_last_error());
			$result = pg_execute($conn, "get_order_info", array($_POST['orderid'])) or die('Could not connect: ' . pg_last_error());
			
			$order = pg_fetch_assoc($result);
			
			if($result) {
				//Get product from database
				$query = 'SELECT * FROM DB_1.product WHERE productid = $1';
				$result = pg_prepare($conn, "get_product_info", $query) or die('Could not connect: ' . pg_last_error());
				$result = pg_execute($conn, "get_product_info", array($order['productid'])) or die('Could not connect: ' . pg_last_error());			
				
				$product = pg_fetch_assoc($result);
				
				$quantity = $order['price'] / $product['price'];
				$quantity = $quantity + $product['quantity'];
				
				//Update quantity in database
				$query = 'UPDATE DB_1.product SET quantity = $1 WHERE productid = $2';
				$result = pg_prepare($conn, "update_quantity" . $num_purchased, $query) or die('Could not connect: ' . pg_last_error());
				$result = pg_execute($conn, "update_quantity" . $num_purchased, array($quantity, $product['productid'])) or die('Could not connect: ' . pg_last_error());
				
				//Delete order from database		
				$query = 'DELETE FROM DB_1.purchase_order WHERE orderid = $1';
				$result = pg_prepare($conn, "cancel_order", $query) or die('Could not connect: ' . pg_last_error());
				$result = pg_execute($conn, "cancel_order", array($_POST['orderid'])) or die('Could not connect: ' . pg_last_error());
				
				//Error checking if successful
				if($result) {
					echo '<div class ="alert alert-success alert-dismissable center">Order successfully cancelled!</div>';
				} else {
					echo '<div class ="alert alert-danger alert-dismissable center">Failed to cancel order. Query Error: ' . pg_last_error() . '</div>';
				}
			}
		} else {
			echo '<div class ="alert alert-danger alert-dismissable center">An order cancel button was not pressed!</div>';
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
