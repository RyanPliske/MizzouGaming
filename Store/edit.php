<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" type="text/css" href="css/form.css" />
<title>Edit Product</title>
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

<?php
        //get the common header from header.php
        set_include_path('..');
        require "admin_header.php";
?>
<div id="page">
	<div id="main">
		<div class="content">
			<div id="pagetitle">Edit Product</div>
			
			<?php
			
			// Connect to database.
			include("../secure/database.php");
			$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD);
			
			// Check if form has been submitted.
			if(isset($_POST['submit'])){
				// Get product by ID.
				$query = "SELECT * FROM DB_1.product WHERE productid = $1";
				// Query for product.
				$result = pg_prepare($conn, "edit_value", $query) or die('Could not connect PREPARE: ' . pg_last_error());
				$result = pg_execute($conn, "edit_value", array($_POST['product'])) or die('Could not connect EXECUTE: ' . pg_last_error());
				// Edit product information.
				if($result) {
					$row = pg_fetch_assoc($result);
					//Display the information to be edited		
					echo '<div id="form_body">';
					echo '<form method="POST" action="edit.php" enctype="multipart/form-data">';
					echo '<fieldset>';
					echo '<input type="hidden" name="productID" value="' . $_POST['product'] . '" />';
					echo '<ul>';
					echo '<li>';
					echo '<label for="productName">Product Name</label>';
					echo '<input type="text" name="productName" id="productName" value="' . $row['productname'] . '" />';
					echo '<div style="clear:both; float:none"></div>';
					echo '</li>';
					echo '<li>';
					echo '<label for="productDesc">Product Description</label><br>';
					echo '<textarea name="productDesc" id="productDesc">' . $row['productdesc'] . '</textarea>';
					echo '<div style="clear:both; float:none"></div>';
					echo '</li>';
					echo '<li>';
					echo '<label for="price">Price (without $)</label>';
					echo '<input type="number" min="0.00" step=".01" name="price" id="price" value="' . $row['price'] . '" />';
					echo '<div style="clear:both; float:none"></div>';
					echo '</li>';
					echo '<li>';
					echo '<label for="quantity">Quantity</label>';
					echo '<input type="number" min="0" step="1" name="quantity" id="quantity" value="' . $row['quantity'] . '" />';
					echo '<div style="clear:both; float:none"></div>';
					echo '</li>';
					echo '<li>';
					echo '<label for="endOfSale">End of Sale</label>';
					echo '<input type="date" name="endOfSale" id="endOfSale" value="' . $row['endofsale'] . '" />';
					echo '<div style="clear:both; float:none"></div>';
					echo '</li>';
					echo '<li>';
					echo '<label for="category">Product Category</label>';
					echo '<select name="category" id="category">';
					if($row['category'] == "Merchandise") {
					echo '<option value="Tournament">Tournament</option>';
					echo '<option value="Merchandise" selected>Merchandise</option>';
					} else {
					echo '<option value="Tournament" selected>Tournament</option>';
					echo '<option value="Merchandise">Merchandise</option>';
					}
					echo '</select>';
					echo '<div style="clear:both; float:none"></div>';
					echo '</li>';
					echo '<li>';
					echo '<label for="type">Tournament Type</label>';
					echo '<select name="type" id="type">';
					echo '<option value=""></option>';
					switch($row['team_members']) {
						case 1:
							echo '<option value="1" selected>1v1</option>';
							echo '<option value="2">2v2</option>';
							echo '<option value="3">3v3</option>';
							echo '<option value="4">4v4</option>';
							echo '<option value="5">5v5</option>';
							break;
						case 2:
							echo '<option value="1">1v1</option>';
							echo '<option value="2" selected>2v2</option>';
							echo '<option value="3">3v3</option>';
							echo '<option value="4">4v4</option>';
							echo '<option value="5">5v5</option>';
							break;
						break;
						case 3:
							echo '<option value="1">1v1</option>';
							echo '<option value="2">2v2</option>';
							echo '<option value="3" selected>3v3</option>';
							echo '<option value="4">4v4</option>';
							echo '<option value="5">5v5</option>';
							break;						
						break;
						case 4:
							echo '<option value="1">1v1</option>';
							echo '<option value="2">2v2</option>';
							echo '<option value="3">3v3</option>';
							echo '<option value="4" selected>4v4</option>';
							echo '<option value="5">5v5</option>';
							break;						
						break;
						case 5:
							echo '<option value="1">1v1</option>';
							echo '<option value="2">2v2</option>';
							echo '<option value="3">3v3</option>';
							echo '<option value="4">4v4</option>';
							echo '<option value="5" selected>5v5</option>';
							break;						
						break;
					}
					echo '</select>';
					echo '<div style="clear:both; float:none"></div>';
					echo '<li>';
					echo '<label for="submit"></label>';
					echo '<input type="submit" name="submit2" id="submit" value="Submit" class="button" />';
					echo '</li>';
					echo '</ul>';
					echo '</fieldset>';
					echo '</form>';
					echo '</div>';
				}
			//Fields will be updated
			} else if(isset($_POST['submit2'])) {
				echo '<p>Updating the following fields:</p>';

				$product_id = $_POST['productID'];
				//Depending on what field has been updated, create a query and update the information	
				if($_POST['productName']) {
					echo '<p>Product Name: ' . $_POST['productName'] . '</p>';
					$query = "UPDATE DB_1.product SET productname = $1 WHERE productid = $2";
					$result = pg_prepare($conn, "edit_name", $query) or die('Could not connect PREPARE: ' . pg_last_error());
					$result = pg_execute($conn, "edit_name", array($_POST['productName'], $product_id)) or die('Could not connect EXECUTE: ' . pg_last_error());
					if(!$result) {
						echo '<p>Error: ' . pg_last_error() . '</p>';
					}
				}
				if($_POST['productDesc']) {
					echo '<p>Product Description: ' . $_POST['productDesc'] . '</p>';
					$query = "UPDATE DB_1.product SET productdesc = $1 WHERE productid = $2";
					$result = pg_prepare($conn, "edit_desc", $query) or die('Could not connect PREPARE: ' . pg_last_error());
					$result = pg_execute($conn, "edit_desc", array($_POST['productDesc'], $product_id)) or die('Could not connect EXECUTE: ' . pg_last_error());
					if(!$result) {
						echo '<p>Error: ' . pg_last_error() . '</p>';
					}
				}
				if($_POST['price']) {
					echo '<p>Product Price: ' . $_POST['price'] . '</p>';
					$query = "UPDATE DB_1.product SET price = $1 WHERE productid = $2";
					$result = pg_prepare($conn, "edit_price", $query) or die('Could not connect PREPARE: ' . pg_last_error());
					$result = pg_execute($conn, "edit_price", array($_POST['price'], $product_id)) or die('Could not connect EXECUTE: ' . pg_last_error());
					if(!$result) {
						echo '<p>Error: ' . pg_last_error() . '</p>';
					}
				}
				if($_POST['quantity']) {
					echo '<p>Product Quantity: ' . $_POST['quantity'] . '</p>';
					$query = "UPDATE DB_1.product SET quantity = $1 WHERE productid = $2";
					$result = pg_prepare($conn, "edit_quantity", $query) or die('Could not connect PREPARE: ' . pg_last_error());
					$result = pg_execute($conn, "edit_quantity", array($_POST['quantity'], $product_id)) or die('Could not connect EXECUTE: ' . pg_last_error());
					if(!$result) {
						echo '<p>Error: ' . pg_last_error() . '</p>';
					}
				}
				if($_POST['endOfSale']) {
					echo '<p>Product End of Sale: ' . $_POST['endOfSale'] . '</p>';
					$query = "UPDATE DB_1.product SET endofsale = $1 WHERE productid = $2";
					$result = pg_prepare($conn, "edit_endofsale", $query) or die('Could not connect PREPARE: ' . pg_last_error());
					$result = pg_execute($conn, "edit_endofsale", array($_POST['endOfSale'], $product_id)) or die('Could not connect EXECUTE: ' . pg_last_error());
					if(!$result) {
						echo '<p>Error: ' . pg_last_error() . '</p>';
					}
				}
				if($_POST['category']) {
					echo '<p>Product Category: ' . $_POST['category'] . '</p>';
					$query = "UPDATE DB_1.product SET category = $1 WHERE productid = $2";
					$result = pg_prepare($conn, "edit_category", $query) or die('Could not connect PREPARE: ' . pg_last_error());
					$result = pg_execute($conn, "edit_category", array($_POST['category'], $product_id)) or die('Could not connect EXECUTE: ' . pg_last_error());
					if(!$result) {
						echo '<p>Error: ' . pg_last_error() . '</p>';
					}
				}
				if($_POST['type']) {
					echo '<p>Product Type: ' . $_POST['type'] . '</p>';
					$query = "UPDATE DB_1.product SET team_members = $1 WHERE productid = $2";
					$result = pg_prepare($conn, "edit_type", $query);
					$result = pg_execute($conn, "edit_type", array($_POST['type'], $product_id));
					if(!$result) {
						echo '<p>Error: ' . pg_last_error() . '</p>';
					}
				}
			} else {
			?>
			<div id="form_body">	
				<!-- product page code -->
				<form method="POST" action="edit.php">	
					<fieldset>
						<ul>
							<li>
							<label for="product">Product Name</label>
							<select name="product">
								<?php
								
								// Query database for all products.
								$query = "SELECT * FROM DB_1.product WHERE quantity > 0";
								$result = pg_query($query) or die('Query failed: ' . pg_last_error());
								
								// Check if query was successful.
								if($result) {
								    // Get each product and list as option.
								    while($row = pg_fetch_array($result)) {
										echo "\t<option value=\"".$row['productid']."\" >".$row['productname']."</option>\n";
									}
								}
								?>
							</select>
							<div style="clear:both; float:none"></div>
							</li>
							<li>
								<input type="submit" name="submit" id="submit" value="submit" class="button" />
							</li>
						</ul>
					</fieldset>
				</form>
			</div>
			<?php } ?>

			<!-- Current homepage format -->
			<div class="clear">&nbsp;</div>
		</div>
	</div>
</div>
</body>
</html>
<!-- End Current homepage format -->
