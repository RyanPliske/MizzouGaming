<?php
//Check if SESSION is set, otherwise set it
if (!isset($_SESSION))
{
	session_start();
}
//Check login information, if not redirect to login page
$loggedIn = empty($_SESSION['loggedin']) ? false : $_SESSION['loggedin'];
if(!$loggedIn)
 {
	header("Location: https://babbage.cs.missouri.edu/~cs3380s14grp10/Login/login.php");
	exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<title>Purchase Receipt</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../Bootstrap/css/bootstrap.styles.css" rel="stylesheet">
<link rel="stylesheet" media="all" href="/css/style.css" type="text/css" />
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
        require "header.php";
    ?>

	<div id="main">
		<div class="content">
			<div id="pagetitle">Purchase Page</div>
			
			<?php
			// Database connection
			include("../secure/database.php");
			$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD);
			
			$num_purchased = 0;
			$check_num = 0;
			$tournament_purchased = 0;
			
			//If cart has something in it display header to our table
			if($_SESSION['cart']) {
				// Go through each item in the shopping cart, get product_id and quantity for each.
				foreach($_SESSION['cart'] as $product_id => $quantity) {
					// Query database for matching item.				
					$query = 'SELECT * FROM DB_1.product WHERE productid = ' . $product_id;
					$result = pg_query($query);
					// If a row is found, print data.
					while($product = pg_fetch_assoc($result)) {
						
						//If it's a tournament, select to see if user is in the tournament already 
						if ($product['category'] == 'Tournament'){
							if ($quantity != 1){
								$quantity = 1;
								echo "\n<div class ='alert alert-danger alert-dismissable'>";
                                                                echo"\n\t<center>Quantity of tournament puchase changed to one</center>";
                                                                echo "\n</div>";
							}
							$check_num++;
							$check = "SELECT * FROM DB_1.purchase_order WHERE cID IN (SELECT cID FROM DB_1.customer WHERE username = $1) AND productID = $2";
							$tournCheck = pg_prepare($conn, "check_tourn" . $check_num, $check)  or die('Could not connect: ' . pg_last_error());
							$tournCheck = pg_execute($conn, "check_tourn" . $check_num, array($_SESSION['loggedin'], $product['productid'])) or die('Could not connect: ' . pg_last_error());

							$num = pg_num_rows($tournCheck);

							if ($num != 0){
								//Means there is one value in table with same productID, meaning they are already in this tournament
								echo "\n<div class ='alert alert-danger alert-dismissable'>";
								echo"\n\t<center>You were not able to purchase " . $product['productname'] . ", you are already in this tournament!</center>";
								echo "\n</div>";
								unset($_SESSION['cart'][$product_id]);
								break;
							}
							$tournament_purchased++;
						}
						$num_purchased++;
						
						$cost = $quantity * $product['price'];
						
						//get the customer ID
						$query = 'SELECT cid FROM DB_1.customer WHERE username = \'' . $_SESSION['loggedin'] . '\'';
						$result = pg_query($query) or die('Query failed: ' . pg_last_error());
						$customer = pg_fetch_assoc($result);
						$today = date('m-d-Y');
						//add order into purchase_order
						$query = 'INSERT INTO DB_1.purchase_order VALUES (DEFAULT, $1, $2, $3, FALSE, $4)';
						
						$result = pg_prepare($conn, "insert_order" . $num_purchased, $query) or die('Could not connect: ' . pg_last_error());
						$result = pg_execute($conn, "insert_order" . $num_purchased, array($customer['cid'], $today, $product['productid'], $cost)) or die('Could not connect: ' . pg_last_error());
						//Update quantity
						$new_quantity = ($product['quantity'] - $quantity);
	
						//If there's still quantity, update it
						if($new_quantity >= 0) {	
							$query = 'UPDATE DB_1.product SET quantity = $1 WHERE productid = $2';
							$result = pg_prepare($conn, "update_quantity" . $num_purchased, $query) or die('Could not connect: ' . pg_last_error());
							$result = pg_execute($conn, "update_quantity" . $num_purchased, array($new_quantity, $product_id)) or die('Could not connect: ' . pg_last_error());
							
							echo '<div class ="alert alert-success alert-dismissable">';
							echo '<center>Purchased ' . $product['productname'] . ' for $' . $cost . ' ($' . $product['price'] . ' x ' . $quantity . ')</center>';
							echo '</div>';

							echo '<div class="clear">&nbsp;</div>';
						//No quantity left, so do not allow for a purchase
						} else {
							echo "\n<div class ='alert alert-danger alert-dismissable'>";
							echo"\n\t<center>Could not purchase".$quantity." ".$product['productname'].", only ".$product['quantity']." available</center>";
							echo "\n</div>";
							exit();
						}
						unset($_SESSION['cart'][$product_id]);
					}
				}
				echo '<hr>';
				empty_cart($num_purchased, $tournament_purchased);
			} else {
				echo "\n<div class ='alert alert-danger alert-dismissable'>";
				echo"\n\t<center>Error: Can't complete purchase, there are no items in shopping cart!</center>";
				echo "\n</div>";
			}
			echo '<center><a class="center" href="../Store/">Continue Shopping</a></center>';
			
			function empty_cart($num_purchased, $tournament_purchased) {
				// This empties the shopping cart upon completing the transaction.
				unset($_SESSION['cart']);
				//if cart was purchased then display success message
				if($num_purchased > 0) {
					echo "\n<div class ='alert alert-success alert-dismissable'>";
					echo"\n\t<center>Success! Please Print this page for your records.</center>";
					echo "\n</div>";
				}
				//if a tournament pass is purchased display link to team_registration page
				if($tournament_purchased > 0)
				{
					echo "\n<div class ='alert alert-warning alert-dismissable'>";
					echo '<center><a href="http://babbage.cs.missouri.edu/~cs3380s14grp10/Tournaments/team_registration.php">Continue to Team Registration</a></center>';
					echo"\n\t<center>If you don't have a complete team, you can register your team later!</center>";
					echo "\n</div>";
				}
			}
			
			pg_close($conn);
			?>
			
			<!-- Current homepage format -->
			<div class="clear">&nbsp;</div>
		</div>
	</div>
</div>
</body>
</html>
<!-- End Current homepage format -->
