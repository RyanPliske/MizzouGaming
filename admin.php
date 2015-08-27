<!DOCTYPE html>
<html>
<head>
<meta charset=UTF-8>
<title>Mizzou Gaming Admin</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="Bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="Orders/css/style.css" />
<link href="Bootstrap/css/bootstrap.styles.css" rel="stylesheet">
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="Bootstrap/js/bootstrap.min.js"></script>
<style>

.front {
	margin-left: 10px;
	margin-right: 10px;
	background-color: rgba(142,140,140,0.43);
	border-radius: 5px;
	padding-bottom: 5px;
	border: 1px Solid #4f4f4f;
}
h4 {
	padding-top: 5px;
}
</style>
</head>
<body>
<div id="page">
<?php
		if (!isset($_SESSION))
		{
			session_start();
			$_SESSION['prevPage'] = 'home';
		}
	// Get common admin header.
	require "admin_header.php";
?>
<div id="main">
	<div class="content">
		<div id="pagetitle">Welcome to the Admin Homepage</div>
		<div style="margin-left: 20px; margin-right: 20px; text-align: center;">
			<table border="0" style="width: 100%;">
			<tr>
				<td>
				<div class="front">
				<!-- Display Order options -->
				<h4>Manage Orders</h4>
				<p><a href="Orders/">View incomplete orders</a></p>
				<p><a href="Orders/index.php?complete=true">View completed orders</a></p>
				</div>
				</td>
				<td>
				<div class="front">
				<!-- Display Store options -->
				<h4>Store Tools</h4>
				<p><a href="ProductForm/product_form.php">Add new product</a></p>
				<p><a href="Store/edit.php">Edit a product</a></p>				
				<p><a href="Store/remove.php">Remove a product</a></p>
				</div>
				</td>
				<td>
				<div class="front">
				<!-- Display Admin options -->
				<h4>Manage Admins</h4>
				<p><a href="Admin_Registration/admin_registration.php">Register an admin</a></p>
				<p><a href="Admin_Registration/remove_admin.php">Remove an admin</a></p>
				</div>
				</td>
			</tr>
			</table>
		</div>
		<div class="clear">&nbsp;</div>
	</div>
</div>
</div>
</body>
</html>

