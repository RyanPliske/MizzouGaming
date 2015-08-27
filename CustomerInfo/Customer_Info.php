<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" type="text/css" href="css/Customer_Info.css" />
<title>Customer Information Submission Form</title>
</head>
<body>
<!-- Current homepage format -->
<div id="page">
	<div id="pagetop">
		<h1>Mizzou Gaming</h1>
		<div class="links">
		<ul>
			<li><a href="Customer_Info.php">Contact Us</a></li>
			<li><a href="Customer_Info.php">Articles</a></li>
			<li><a href="Customer_Info.php">Events</a></li>
			<li><a href="Customer_Info.php">Member Login</a></li>
			<li><a href="Customer_Info.php">Home</a></li>
		</ul>
		<span style="color:red; float: right;">*** All above menu choices link back to Customer_Info.php ***</span>
	</div>
</div>
<div id="main">
	<div class="content">
		<div class="main_body">
			
			<!-- Product Submission Form -->
			<div id="form_body">
				<form method="POST" action="process.php">
					<fieldset>
						<!-- Fields used to take in user info -->
						<legend>Customer Information Submission Form</legend>
						<ul>
							<li>
								<label for="firstname">First Name<em>*</em></label>
								<input type="text" name="firstname" id="firstname" required="true" />
								<div style="clear:both; float:none"></div>
							</li>
							<li>
								<label for="lastname">Last Name<em>*</em></label>
								<input type="text" name="lastname" id="lastname" required="true" />
								<div style="clear:both; float:none"></div>
							</li>
							<li>
								<label for="user_email">Email<em>*</em></label><br>
								<input type="text" name="user_email" id="user_email" required="true" />
								<div style="clear:both; float:none"></div>
							</li>
							<li>
								<label for="street">Street<em>*</em></label>
								<textarea name="street" id="street" required="true"></textarea>
								<div style="clear:both; float:none"></div>
							</li>
							<li>
								<label for="city">City<em>*</em></label>
								<input type="text" name="city" id="city" required="true" />
								<div style="clear:both; float:none"></div>
							</li>
							<li>
								<label for="state">State<em>*</em></label>
								<input type="text" name="state" id="state" required="true" />
								<div style="clear:both; float:none"></div>
							</li>
							<li>
								<label for="zip">Zip<em>*</em></label>
								<input type="integer" name="zip" id="zip" required="true" />
								<div style="clear:both; float:none"></div>
							</li>
							<li>
								<label for="phonenumber">Phone Number<em>*</em></label>
								<input type="text" name="phonenumber" id="phonenumber" required="true" />
								<div style="clear:both; float:none"></div>
							</li>
							<li>
								<label for="submit"> </label>
								<input type="submit" name="submit" id="submit" value="Continue" class="button" />
							</li>
							
						</ul>
					</fieldset>
				</form>
			</div>

<!-- Current homepage format -->
		</div>
		<div class="clear">&nbsp;</div>
	</div>
</div>
	
</body>
</html>
