<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<title>Mizzou Gaming Store</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="../Bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../Bootstrap/css/bootstrap.styles.css" rel="stylesheet">

</head>
<body>
<?php
if(!isset($_SESSION)) {
	session_start();
}
?>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="../Bootstrap/js/bootstrap.min.js"></script>
<!-- Current homepage format -->

<?php
        //get the common header from header.php
        set_include_path('..');
        require "header.php";
?>
<div id="page">
<div id="main">
	<div class="content">
		<!-- Insert store data from database -->
		<?php require_once "store.php";?>		
					
		<!-- Current homepage format -->
		<div class="clear">&nbsp;</div>
	</div>
</div>
	
</body>
</html>
<!-- End Current homepage format -->
