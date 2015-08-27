<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--<link rel="stylesheet" type="text/css" href="css/style.css" />-->
<link rel="stylesheet" type="text/css" href="css/process.css" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<title>Product Submission Processor</title>
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
<center>
<div id="page">
<?php
        //get the common header from header.php
        set_include_path('..');
        require "admin_header.php";
?>
<div id="main">
	<div class="content">
		

<!-- Handle Form Submission -->
<?php


// Connect to database...

include("../secure/database.php");
$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD);
if(!$conn) {
	echo "<p>Failed to connect to DB</p>";
}

//Get POST information
$productName = $_POST['productName'];
$productDesc = $_POST['productDesc'];
$price = $_POST['price'];
$category = $_POST['category'];
$quantity = $_POST['quantity'];
$endOfSale = $_POST['endOfSale'];
$pic = "img/" . $_FILES["file"]["name"];
$type = $_POST['type'];

// Should check for valid input here.

	// Make sure $productName is safe for insert
	$productName = htmlspecialchars($_POST['productName']);
	
	// Make sure $productDesc is safe for insert
	$productDesc = htmlspecialchars($_POST['productDesc']);
	
	//Make sure picture doesn't exist already
	$query ="SELECT * FROM DB_1.product WHERE imgpath = $1";
	$result = pg_prepare($conn, 'check_img', $query) or die( "ERROR:". pg_last_error() );
	$result = pg_execute($conn, 'check_img', array($pic) ) or die( "ERROR:". pg_last_error() );
	if ( pg_num_rows($result) != 0)
	{
			echo "\n<div class ='alert alert-danger alert-dismissable'>";
			echo "\n\t<center>Image already exists. Please rename the image.</center>"; 
			echo "\n</div>";
			// Link to return to product form submission page (or where ever we want to send them)
			echo "<center><p><a href='product_form.php'>Return to Product Form Submission Page.</a></p></fieldset></div>";

	}
	else
	{
			// Make sure $price is a monetary value (fix if it begins with 0? example input: 002.00 but not 0.00 )
			// *** MISSING ***
			
		// Check if the good parts of the form are good to go.
		if($productName && $productDesc && $price >= 0 && $category) {
			// Success, let user know.
			echo '<div id="pagetitle">Product Submission Summary - Success</div>';
			echo "<div id='process'><fieldset>";
			echo "<p>A new product has been submitted.</p>";
			echo "<p><ul><li>Product Name: " . $productName;
			echo "</li><li>Product Description: " . $productDesc;
			echo "</li><li>Product Price: $" . $price;
			echo "</li><li>Product Category: " . $category;
			echo "</li><li>Quantity: " . $quantity;
			echo "</li><li>End of Sale: " . $endOfSale . "</li></ul></p>";
			// Link to return to product form submission page (or where ever we want to send them)
			echo "<p><a href='product_form.php'>Return to Product Form Submission Page.</a></p>";
			
			// Create a query and submit it to the database here.
			$query = "INSERT INTO DB_1.product ";//(productid, productname, productdesc, price, category, quantity, endofsale) 
			$query .= "VALUES (DEFAULT, $1, $2, $3, $4, $5, $6, $7,$8,$9)";
			
			// Do query here...
			$result = pg_prepare($conn, "query_setup","INSERT INTO DB_1.product VALUES (DEFAULT, $1, $2, $3, $4, $5, $6, $7,$8,$9)") or die ('PG_Prepare could not connect: ' .pg_last_error());
			$result = pg_execute($conn, "query_setup", array("$productName", "$productDesc", "$price", "$category", "$quantity", "$endOfSale", "$pic", "$type", "$quantity")) or die ('PG_Execute could not connect: ' .pg_last_error());
			
			
		} else {
			// Failure, let user know of the error.
			echo '<div id="pagetitle">Product Submission Summary - Failure</div>';
			echo "<div id='process'><fieldset>";
			echo "<p>Unable to submit the new product.</p><p>Make sure that you filled in all of the required fields correctly.</p>";
			// Link to return to product form submission page (or where ever we want to send them)
			echo "<p><a href='product_form.php'>Return to Product Form Submission Page.</a></p></fieldset></div>";
		}
		//Image storage and error checking
		$allowedExts = array("gif", "jpeg", "jpg", "png");
		$temp = explode(".", $_FILES["file"]["name"]);
		$extension = end($temp);
		if ((($_FILES["file"]["type"] == "image/gif")
		|| ($_FILES["file"]["type"] == "image/jpeg")
		|| ($_FILES["file"]["type"] == "image/jpg")
		|| ($_FILES["file"]["type"] == "image/pjpeg")
		|| ($_FILES["file"]["type"] == "image/x-png")
		|| ($_FILES["file"]["type"] == "image/png"))
		&& ($_FILES["file"]["size"] < 200000000)
		&& in_array($extension, $allowedExts))
		  {
			  if ($_FILES["file"]["error"] > 0)
				{
				echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
				}
			  else
				{
				echo "Upload: " . $_FILES["file"]["name"] . "<br>";
				echo "Type: " . $_FILES["file"]["type"] . "<br>";
				echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
				echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";

				if (file_exists("img/" . $_FILES["file"]["name"]))
				  {
				  echo $_FILES["file"]["name"] . " already exists. ";
				  }
				else
				  {
				  move_uploaded_file($_FILES["file"]["tmp_name"],
				  "img/" . $_FILES["file"]["name"]);
				  echo "Stored in: " . "img/" . $_FILES["file"]["name"];
			  }
			}
			echo "</fieldset></div>";
		  }
		else
		  {
		  echo "Invalid file";
		  }
	}
pg_close($conn);
?>

<!-- Current homepage format -->
		</div>
		<div class="clear">&nbsp;</div>
	</div>
</div>
</body>
</html>
