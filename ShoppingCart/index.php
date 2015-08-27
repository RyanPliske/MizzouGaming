<?php
session_start();
$loggedIn = empty($_SESSION['loggedin']) ? false : $_SESSION['loggedin'];

$_SESSION['prevPage'] = 'cart';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="PHP Shopping Cart Using Sessions" /> 
<meta name="keywords" content="shopping cart tutorial, shopping cart, php, sessions" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Shopping Cart</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<link rel="stylesheet" type="text/css" href="css/style.css" />

<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../Bootstrap/css/bootstrap.styles.css" rel="stylesheet">
<style>
	.sub_mit{
		text-align: center;
	}	
  .center {text-align: center;}
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
        require "header.php";
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
		<div id="pagetitle">Shopping Cart</div>
			<?php if(!$loggedIn) { 
			echo '<p class="empty">You must <a href="../Login/login.php">Login</a> or <a href="../Login/registration.php">Register</a> to purchase!</p>';
			} ?>
<?php

	$product_id = $_GET['id'];	 //the product id from the URL 
	$action 	= $_GET['action']; //the action from the URL 

	//if there is an product_id and that product_id doesn't exist display an error message
	if($product_id && !productExists($product_id)) {
		die("Error. Product Doesn't Exist");
	}

	switch($action) {	//decide what to do	
	
		case "add":
			$query = "SELECT * FROM DB_1.product WHERE productid = " . $product_id;
			$result = pg_query($query);
			$product = pg_fetch_array($result);
			$product_quantity = $product['quantity'];
			$product_name = $product['productname'];
			if($_SESSION['cart'][$product_id] < $product_quantity) {
				$_SESSION['cart'][$product_id]++; //add one to the quantity of the product with id $product_id
			} else {
				echo '<p class="empty">Cannot add another ' . $product_name . ', only ' . $product_quantity . ' available.</p>';
			}
		break;
		
		case "remove":
			$_SESSION['cart'][$product_id]--; //remove one from the quantity of the product with id $product_id 
			if($_SESSION['cart'][$product_id] == 0) unset($_SESSION['cart'][$product_id]); //if the quantity is zero, remove it completely (using the 'unset' function) - otherwise is will show zero, then -1, -2 etc when the user keeps removing items. 
		break;
		
		case "empty":
			unset($_SESSION['cart']); //unset the whole cart, i.e. empty the cart. 
		break;
	
	}	

	if($_SESSION['cart']) {	//if the cart isn't empty
		//show the cart
		
		//echo "<table border=\"1\" bordercolor=\"676767\" id=\"cart\">";	//format the cart using a HTML table
		echo "<table class='table table-responsive table-striped' id=\"cart\">";
			echo "<tr>";
				echo '<td class="heading">Product</td>';
				echo '<td class="heading">Quantity</td>';
				echo '<td class="heading">Price</td>';
			echo "</tr>";
			//iterate through the cart, the $product_id is the key and $quantity is the value
			foreach($_SESSION['cart'] as $product_id => $quantity) {	
				
				//get the name, description and price from the database - this will depend on your database implementation.
				//use sprintf to make sure that $product_id is inserted into the query as a number - to prevent SQL injection
				$query = "SELECT productname, productdesc, price FROM DB_1.product WHERE productid = " . $product_id; 
					
				$result = pg_query($query);
					
				//Only display the row if there is a product (though there should always be as we have already checked)
				if(pg_num_rows($result) > 0) {
				
					list($name, $description, $price) = pg_fetch_row($result);
				
					$line_cost = $price * $quantity;		//work out the line cost
					$total = $total + $line_cost;			//add to the total cost
				
					echo "<tr>";
						//show this information in table cells
						echo "<td>$name</td>";
						//along with a 'remove' link next to the quantity - which links to this page, but with an action of remove, and the id of the current product
						echo "<td><a href=\"$_SERVER[PHP_SELF]?action=add&id=$product_id\">+</a> $quantity <a href=\"$_SERVER[PHP_SELF]?action=remove&id=$product_id\">-</a></td>";
						echo "<td>$$line_cost</td>";
					
					echo "</tr>";
					
				}
			
			}
			
			//show the total
			echo "<tr>";
				echo "<td></td>";
				echo "<td>Total</td>";
				echo "<td>$$total</td>";
			echo "</tr>";
			
			//show the empty cart link - which links to this page, but with an action of empty. A simple bit of javascript in the onlick event of the link asks the user for confirmation
			echo "<tr>";
				echo "<td colspan=\"1\"><a href=\"$_SERVER[PHP_SELF]?action=empty\" onclick=\"return confirm('Are you sure?');\">Empty Cart</a></td>";
				if(!$loggedIn) {
					echo "<td colspan=\"2\"><a href=\"../Login/login.php\">Login</a> to Purchase</td>";
				} else {
					echo "<td colspan=\"2\"><a href=\"purchase.php\">Purchase</a></td>";
				}
			echo "</tr>";		
		echo "</table>";
		
		
	
	}else{
		//otherwise tell the user they have no items in their cart
		echo "<p class=\"empty\">You have no items in your shopping cart.</p>";
		
	}
	
	//function to check if a product exists
	function productExists($product_id) {
			//use sprintf to make sure that $product_id is inserted into the query as a number - to prevent SQL injection
			$query = "SELECT * FROM DB_1.product WHERE productid = " . $product_id;	
			return pg_num_rows(pg_query($query)) > 0;
	}
?>

<center><a class="center" href="../Store/">Continue Shopping</a></center>

		<!-- Current homepage format -->
		<div class="clear">&nbsp;</div>
	</div>
</div>
	
</body>
</html>
<!-- End Current homepage format -->
