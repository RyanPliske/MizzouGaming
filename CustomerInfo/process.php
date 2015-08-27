<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" type="text/css" href="css/process.css" />
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

<!-- Handle Form Submission -->
<?php


// Connect to database...

include("../secure/database.php");
$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD);
if($conn) {
echo "<p>Successfully connected to DB</p>";
} else {
echo "<p>Failed to connect to DB</p>";
}


// Get our form variables
$lastname		 = $_POST['LastName'];
$firstname		 = $_POST['FirstName'];
$user_email		 = $_POST['user_email'];
$street			 = $_POST['street'];
$city			 = $_POST['city'];
$state 			 = $_POST['state'];
$zip 			 = $_POST['zip'];
$phonenumber	 = $_POST['phonenumber'];
// Should check for valid input here.

	// Make sure $lastname is safe for insert
	$lastname = htmlspecialchars($_POST['lastname']);
	// Make sure $firstname is safe for insert
	$firstname = htmlspecialchars($_POST['firstname']);
	// Make sure $user_email is a safe email
	$user_email = htmlspecialchars($_POST['user_email']);
	// Make sure $street is safe
	$street = htmlspecialchars($_POST['street']);
	// Make sure $city is safe
	$city = htmlspecialchars($_POST['city']);
	// Make sure $state is safe
	$state = htmlspecialchars($_POST['state']);
	// Make sure $zip is a safe integer
	$zip = htmlspecialchars($_POST['zip']);
	// Make sure $phonenumber is a safe phone number
	$phonenumber = htmlspecialchars($_POST['phonenumber']);
	
// Check if the good parts of the form are good to go.
if(TRUE){

	// Success, let user know.
	echo "<div id='process'><fieldset><legend>Success:</legend>";
	echo "<p>Customer Information has been submitted.</p>";
	echo "<p><ul><li>First Name: "  . $firstname;
	echo "</li><li>Last Name: " 	. $lastname;
	echo "</li><li>Email: " 		. $user_email;
	echo "</li><li>Street: " 		. $street;
	echo "</li><li>City: " 			. $city;
	echo "</li><li>State: " 		. $state;
	echo "</li><li>Zip: " 			. $zip;
	echo "</li><li>Phone number: "  . $phonenumber;
	echo "</ul></p>";
	// Link to return to product form submission page (or where ever we want to send them)
	echo "<p><a href='Customer_Info.php'>Return to Product Form Submission Page.</a></p></fieldset></div>";
	
	// Create a query and submit it to the database here.
	$query  = "INSERT INTO Database_Ver_1.customer ";
	$query .= "VALUES (DEFAULT, $1, $2, $3, $4, $5, $6, $7, $8)";
	
	// Do query here...
	$result = pg_prepare($conn, "query_setup","INSERT INTO Database_Ver_1.customer VALUES (DEFAULT, $1, $2, $3, $4, $5, $6, $7, $8, 't')") or die ('PG_Prepare could not connect: ' .pg_last_error());
	$result = pg_execute($conn, "query_setup", array("$lastname", "$firstname", "$user_email", "$street", "$city", "$state", "$zip", "$phonenumber")) or die ('PG_Execute could not connect: ' .pg_last_error());
	
	
} else {
	// Failure, let user know of the error.
	echo "<div id='process'><fieldset><legend>Error</legend>";
	echo "<p>Unable to submit the new product.</p><p>Make sure that you filled in all of the required fields correctly.</p>";
	// Link to return to product form submission page (or where ever we want to send them)
	echo "<p><a href='Customer_Info.php'>Return to Product Form Submission Page.</a></p></fieldset></div>";
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
