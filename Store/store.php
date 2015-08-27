<?php
// Connect to database.
include("../secure/database.php");
$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD);
// Print page title.
echo '<div id="pagetitle">Mizzou Gaming Store</div>';
// Query database for products.
echo "\n</br><center><div class='panel panel-primary'><div class='panel-footer'><h5>Merchandise</h5></div></div></center>";
$query = "SELECT * FROM DB_1.product WHERE category='Merchandise' ORDER BY productname ASC ";
$result = pg_query($query) or die('Query failed: ' . pg_last_error());
drawStore($result);
echo '<div class="clear">&nbsp;</div>
	</div>
</div>';
echo '<div id="page">
<div id="main">
	<div class="content">';

// Query database for tournament passes
echo "</br><center><div class='panel panel-primary'><div class='panel-footer'><h5>Team Passes</h5></div></div></center>";
$query = "SELECT * FROM DB_1.product WHERE category='Tournament' ORDER BY productname ASC ";
$result = pg_query($query) or die('Query failed: ' . pg_last_error());
drawStore($result);

//Draw Store
function drawStore($result)
{
	// $count variable is used for determining which CSS to use.
	$count = 0;

	// Check if query was successful.
	if($result) {
		
		// Get each product and list on page.
		while($row = pg_fetch_array($result)) {
			if($count % 2 == 0) {
				// List product on left side of screen.
				echo '<div class="left">';
				echo '<div class="title">' . $row['productname'] . '</div>';
				echo '<a href="' . $row['imgpath'] . '" ><img src="' . $row['imgpath'] . '" class="store" alt="' . $row['imgpath'] . '"></a>';
				echo "<div><center>Original Quantity:	".$row['orig_quantity']."</center></div>";
				echo '<div class="price">Price: $' . $row['price'] . ' - Quantity: ' . $row['quantity'] . '</div>';
				echo '<div class="description">Description: ' . $row['productdesc'] . '</div>';
				
				// This allows the use of $_GET['id'] to get the productid being purchased.
				//If not passed endOfSale date, no endOfSale date, or the quantity is not 0
				if ($row['endOfSale'] > getdate() || empty($row['endOfSale']) || $row['quantity'] != 0)
				{
					//If product is a tournament team pass, then allow for registration to become Free Agent
					if ($row['team_members'] > 1)
					{
						echo" <a href='../Tournaments/fa_registration.php' class='submitbutton2'>Register As Free Agent</a>";
						echo '<a href="../ShoppingCart/index.php?action=add&id=' . $row['productid'] . '" class="submitbutton">Add Team Pass to Cart</a>';
					} else {
						if($row['quantity'] != 0) {
							echo '<a href="../ShoppingCart/index.php?action=add&id=' . $row['productid'] . '" class="submitbutton">Add to Cart</a>';
						} else {
							echo '<a target="_blank" class="submitbutton">Sold Out!</a>';
						}
					}
					echo"</div>";
				//Otherwise, passed the endOfSale date or quantity is 0
				} else {
					echo '<a target="_blank" class="submitbutton">Not Available</a>';
				}	
			} else {
				// List product on right side of screen.
				echo '<div class="right">';
				echo '<div class="title">' . $row['productname'] . '</div>';
				echo '<a href="' . $row['imgpath'] . '" ><img src="' . $row['imgpath'] . '" class="store" alt="' . $row['imgpath'] . '"></a>';
				echo "<div><center>Original Quantity:	".$row['orig_quantity']."</center></div>";
				echo '<div class="price">Price: $' . $row['price'] . ' - Quantity: ' . $row['quantity'] . '</div>';
				echo '<div class="description">Description: ' . $row['productdesc'] . '</div>';
				
				// This allows the use of $_GET['id'] to get the productid being purchased.
				//If not passed endOfSale date, no endOfSale date, or the quantity is not 0
				if ($row['endOfSale'] > getdate() || empty($row['endOfSale']) || $row['quantity'] != 0)
				{
					//If product is a tournament team pass, then allow for registration to become Free Agent
					if ($row['team_members'] > 1)
					{
						echo" <a href='../Tournaments/fa_registration.php' class='submitbutton2'>Register As Free Agent</a>";
						echo '<a href="../ShoppingCart/index.php?action=add&id=' . $row['productid'] . '" class="submitbutton">Add Team Pass to Cart</a>';
					} else {
						if($row['quantity'] != 0) {
							echo '<a href="../ShoppingCart/index.php?action=add&id=' . $row['productid'] . '" class="submitbutton">Add to Cart</a>';
						} else {
							echo '<a target="_blank" class="submitbutton">Sold Out!</a>';
						}
					}
					echo"</div>";
				//Otherwise, passed the endOfSale date or quantity is 0
				} else {
					echo 'Product Not Available';
				}
			}
			// Increment count for detemining CSS.
			$count++;
		}
	}
}
// Close database connection.
pg_close($conn);
?>
