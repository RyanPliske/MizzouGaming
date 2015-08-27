<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--<link rel="stylesheet" type="text/css" href="css/style.css" />-->
<link rel="stylesheet" type="text/css" href="css/product_form.css" />
<link rel="stylesheet" type="text/css" href="css/style.css" />

<title>Product Submission Form</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../Bootstrap/css/bootstrap.styles.css" rel="stylesheet">
<style>
	.sub_mit{
		text-align: center;
	}
</style>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../Bootstrap/js/bootstrap.min.js"></script>
</head>
<body>
<div id="page">
<?php
//get the common header from header.php
set_include_path('..');
require "admin_header.php";
?>
	<div id="main">
		<div class="content">
			
			<div id="pagetitle">New Product Submission Form</div>
			<!-- Product Submission Form -->
			<div id="form_body">
				<form method="POST" action="process.php" enctype="multipart/form-data">
					<fieldset>
						<!-- Product information fields -->
						<ul>
							<li>
								<label for="productName">Product Name<em>*</em></label>
								<input type="text" name="productName" id="productName" required="true" />
								<div style="clear:both; float:none"></div>
							</li>
							<li>
								<label for="productDesc">Product Description<em>*</em></label><br>
								<textarea name="productDesc" id="productDesc" required="true"></textarea>
								<div style="clear:both; float:none"></div>
							</li>
							<li>
								<label for="price">Price (without $)<em>*</em></label>
								<input type="number" min="0.00" step=".01" name="price" id="price" placeholder="0.00" required="true" />
								<div style="clear:both; float:none"></div>
							</li>
							<li>
								<label for="quantity">Quantity<em>*</em></label>
								<input type="number" min="0" step="1" name="quantity" id="quantity" placeholder="0" />
								<div style="clear:both; float:none"></div>
							</li>
							<li>
								<label for="endOfSale">End of Sale<em>*</em></label>
								<input type="date" name="endOfSale" id="endOfSale" />
								<div style="clear:both; float:none"></div>
							</li>
							<li>
								<label for="category">Product Category<em>*</em></label>
								<select name="category" id="category" required="true">
									<option value="Tournament">Tournament</option>
									<option value="Merchandise">Merchandise</option>
								</select>
								<div style="clear:both; float:none"></div>
							</li>
							<li>
								<label for="type">Tournament Type</label>
								<select name="type" id="type">
									<option selected value="DEFAULT"></option>
									<option value="1">1v1</option>
									<option value="2">2v2</option>
									<option value="3">3v3</option>
									<option value="4">4v4</option>
									<option value="5">5v5</option>
								</select>
								<div style="clear:both; float:none"></div>
							</li>
							<li>
								<label for="file">Image<em style="color: red;">*</em></label>
								<input type="file" name="file" id="file" required="true" style="float: right; border: 0;"><br>
							</li>
							<li>
								<label for="submit"> </label>
								<input type="submit" name="submit" id="submit" value="Submit" class="button" />
							</li>
						</ul>
					</fieldset>
				</form>
			</div>

		</div>
		<div class="clear">&nbsp;</div>
	</div>
</div>

</body>
</html>
