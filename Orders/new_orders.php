<div id="pagetitle">Incomplete Orders</div>
<?php
// Connect to database.
include("../secure/database.php");
$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD);

// Query database for all products.
$query = "SELECT * FROM DB_1.purchase_order WHERE ordercomplete = FALSE";
$result = pg_query($query);

// Check if query was successful.
if($result) {
	
	// Array to hold results from database query.
	$orders = array();
	
	// Put each row into array.
	while ($row = pg_fetch_array($result)) {
		$orders[] = $row;
	}

	// Get each order and list on page.
	foreach ($orders as $order) {
	
		// Get product info.
		$query = "SELECT * FROM DB_1.product WHERE productid = " . $order['productid'];
		$result = pg_query($query) or die('Query failed: ' . pg_last_error());
		$product = pg_fetch_array($result);
		
		// Get customer info.
		$query = "SELECT * FROM DB_1.customer WHERE cid = " . $order['cid'];
		$result = pg_query($query) or die('Query failed: ' . pg_last_error());		
		$customer = pg_fetch_array($result);
		$purchase_quantity = ( $order['price'] / $product['price'] );
		
		// List order on page.
		echo '<div class="order">';
		echo '<div class="top">';
		echo '<form method="POST" action="confirm_order.php" enctype="multipart/form-data">';
		echo '<input type="hidden" name="orderid" value="' . $order['orderid'] . '" />';
		echo '<input type="submit" name="submit" value="Order #' . $order['orderid'] . '" class="confirm_order" />';
		echo '</form>';
		echo '<form method="POST" action="cancel_order.php" enctype="multipart/form-data">';
		echo '<input type="hidden" name="orderid" value="' . $order['orderid'] . '" />';
		echo '<input type="submit" name="submit" value="Cancel" class="cancel_order" />';
		echo '</form>';	
		echo '<div class="title">' . $product['productname'] . ' (Quantity: ' . $purchase_quantity . ')</div>';
		echo '</div>';
		echo '<div class="clear">&nbsp;</div>';
		echo '<div class="infoleft">';
		echo '<p class="heading">Order by ' . strtoupper($customer['username']) . ' on ' . $order['dateofpurchase'] . '</p>';
		echo '<p class="heading">Email: ' . $customer['user_email'] . '</p>';
		echo '<p class="heading">Phone: ' . $customer['phonenumber'] . '</p>';
		echo '</div>';
		echo '<div class="inforight">';
		echo '<p class="heading">Address:</p><address>' . $customer['firstname'] . ' ' . $customer['lastname'] . '<br>' . $customer['street'] . '<br>' . $customer['city'] . ', ' . $customer['state'] . ' ' . $customer['zip'] . '<address>';
		echo '</div></div>';
	}
} else {
	echo 'Query failed: ' . pg_last_error();
}
// Close database connection.
pg_close($conn);
?>
