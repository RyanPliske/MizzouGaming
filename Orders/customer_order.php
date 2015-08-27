<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" type="text/css" href="css/product_form.css" />
<title>Mizzou Gaming</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../Bootstrap/css/bootstrap.styles.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../Orders/css/style.css" />
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../Bootstrap/js/bootstrap.min.js"></script>
<style>
        #main_box {
                text-align: center;
                border: 5px solid silver;
                width: 580px;
                height: 340px;
        }
        .center {text-align: center;}
</style>
</head>
<body>
<?php
if (!isset($_SESSION))
{
		session_start();
		$_SESSION['prevPage'] = 'home';
}
//get the common header from header.php
require "../header.php";

// Database connection
include("../secure/database.php");
$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD);

echo'<div id="main">';
echo'<center><div id="pagetitle">Your purchased items:</div></center>';

//Get order information from database for incomplete orders
 $query = "SELECT product.productname, purchase_order.price, purchase_order.ordercomplete, purchase_order.dateofpurchase FROM DB_1.purchase_order INNER JOIN DB_1.product AS product USING (productid) WHERE cID IN (SELECT cID FROM DB_1.customer WHERE username = $1) AND ordercomplete = FALSE";
 $result = pg_prepare($conn, "false_complete", $query)  or die('Could not connect: ' . pg_last_error());
 $result = pg_execute($conn, "false_complete", array($_SESSION['loggedin'])) or die('Could not connect: ' . pg_last_error());

//Check if query was successful
if($result)
 {
	//if no rows, no incomplete items
	if(pg_num_rows($result) == 0)
	{
		echo "\n<div class ='alert alert-info alert-dismissable'>";
		echo "\n\t<center>You have not purchased any items.</center>"; 
		echo "\n</div>";
	}
	else
	{
		//Display order information
		echo "<table class='table table-responsive table-striped'>\n";
		$numFields = pg_num_fields($result);
		 echo "\t<tr>\n";
		for ($i=0;$i < $numFields; ++$i){
			$fieldName = pg_field_name($result, $i);
			if ($fieldName == 'ordercomplete')
				echo "\t\t<th>Order Status</th>\n";
			else
				echo "\t\t<th>$fieldName</th>\n";
		}
		echo "\t</tr>\n";

		 while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
			echo "\t<tr>\n";
			foreach ($row as $col_value){
				if ($col_value == 'f' )
					echo "\t\t<td>processing</td>\n";
				else
					echo "\t\t<td>$col_value</td>\n";
			}
			echo "\t</tr>\n";
		}
		echo "</table>\n";
	}
} else {
        echo 'Query failed: ' . pg_last_error();
}
        echo "<br />";
//Get order information from database for complete orders
 $query = "SELECT product.productname, purchase_order.price, purchase_order.ordercomplete, purchase_order.dateofpurchase FROM DB_1.purchase_order INNER JOIN DB_1.product AS product USING (productid) WHERE cID IN (SELECT cID FROM DB_1.customer WHERE username = $1) AND ordercomplete = TRUE";
 $result = pg_prepare($conn, "true_complete", $query)  or die('Could not connect: ' . pg_last_error());
 $result = pg_execute($conn, "true_complete", array($_SESSION['loggedin'])) or die('Could not connect: ' . pg_last_error());

// Check if query was successful.
if($result) 
{

        echo'<center><div id="pagetitle">Items that have been shipped to you:</div></center>';
		//If no items then display items have not been shipped yet.
		if(pg_num_rows($result) == 0)
			{
			echo "\n<div class ='alert alert-info alert-dismissable'>";
			echo "\n\t<center>Items have not been shipped yet.</center>"; 
			echo "\n</div>";
			}
		//Otherwise display information on orders that have been completed
		else
		{
		echo "<table class='table table-responsive table-striped'>\n";
        $numFields = pg_num_fields($result);
         echo "\t<tr>\n";
        for ($i=0;$i < $numFields; ++$i){
                $fieldName = pg_field_name($result, $i);
                if ($fieldName == 'ordercomplete')
					echo "\t\t<th>Order Status</th>\n";
				else
					echo "\t\t<th>$fieldName</th>\n";
        }
        echo "\t</tr>\n";

         while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
                echo "\t<tr>\n";
                foreach ($row as $col_value){
                    if ($col_value == 't' )
						echo "\t\t<td>complete</td>\n";
					else
						echo "\t\t<td>$col_value</td>\n";
                }
                echo "\t</tr>\n";
        }
        echo "</table>\n";
	}
} else {
        echo 'Query failed: ' . pg_last_error();
}



// Close database connection.
pg_close($conn);
?>

