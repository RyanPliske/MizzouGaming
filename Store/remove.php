<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" type="text/css" href="css/form.css" />
<title>Remove Product</title>
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
			<div id="pagetitle">Remove Product</div>
			<div id="form_body">	
				<!-- product page code -->
				<?php
				echo'
				<form method="POST" action="remove.php" enctype="multipart/form-data">	
					<fieldset>
						<ul>
							<li>
							<label for="product">Product Name</label>
							<select name="product" id="product">
							<option value="" > </option>\n';

								// Connect to database.
								include("../secure/database.php");
								$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD);
								
								// Query database for all products.
								$query = "SELECT * FROM DB_1.product WHERE quantity > 0";
								$result = pg_query($query) or die('Query failed: ' . pg_last_error());
								
								// Check if query was successful.
								if($result) {
								        // Get each product and list as option.
								        while($row = pg_fetch_array($result)) {
										echo "\t<option value ='".$row['productname']."'>".$row['productname']."</option>\n";
									}
								}

							echo'
							</select>
							<div style="clear:both; float:none"></div>
							</li>
							<li>
								<input type="submit" name="submit" id="submit" value="remove" class="button" />
							</li>
						</ul>
						</form>
						</fieldset>
						</div>';
				//No product has been selected to be deleted
				if(empty($_POST['product']))
				{
					echo "\n</br>\n<div class ='alert alert-info alert-dismissable'>";
					echo'<center><p class="empty">Welcome! Which Product would you like to delete?</p></center>';
					echo "\n</div>";
				}
				//Product has been selected to be deleted
				else
				{
					$_SESSION['delete'] = $_POST['product'];
					//Display are you sure?
					echo '<form method="POST" action="remove.php">';
					echo "\n</br><div class ='alert alert-danger alert-dismissable'>";
					echo'<center>Are you sure? Action cannot be undone. <input type="submit" name ="No" value ="No"/>	<input type="submit" name = "Yes" value ="Yes"/></center>';
					echo "\n</div>";
				}
				if(isset($_POST['Yes'])){
					{
						// Connect to database.
						include("../secure/database.php");
						$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD);
						// Create deletion statement useing cID being passed from option form.
						$query = "DELETE FROM DB_1.Product WHERE productname = $1";
						$result = pg_prepare($conn, "delete_value", $query) or die('Could not connect: ' . pg_last_error());
						$result = pg_execute($conn, "delete_value", array($_SESSION['delete'] )) or die('Could not connect: ' . pg_last_error());;
						//Print that the deletion was successful
						echo "\n</br><div class ='alert alert-success alert-dismissable'>";
						echo'<center>'.$_SESSION['delete'].' was successfully deleted</center>';
						echo "\n</div>";
					}
				}
				else if(isset($_POST['No'])){
						//Print that the deletion was successful
						echo "\n</br><div class ='alert alert-success alert-dismissable'>";
						echo'<center>'.$_SESSION['delete'].' was NOT deleted</center>';
						echo "\n</div>";
				}
			?>
			</form>
			<!-- Current homepage format -->
			<div class="clear">&nbsp;</div>
		</div>
	</div>
</div>
</body>
</html>
<!-- End Current homepage format -->
