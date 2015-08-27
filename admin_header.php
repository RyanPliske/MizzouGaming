<?php
	if (!isset($_SESSION))
	{
		session_start();
	}
	//check login

	$admin_loggedIn = empty($_SESSION['admin_loggedin']) ? false : $_SESSION['admin_loggedin'];
	if (!$admin_loggedIn || !$_SERVER['HTTPS'] == "on" ) {
		header("Location: https://babbage.cs.missouri.edu/~cs3380s14grp10/Home/home.php");
		exit;
	}

?>
<div class="navbar navbar-inverse navbar-static-top" >
        <div class="container">
                <div class="navbar-brand"><img src="https://babbage.cs.missouri.edu/~cs3380s14grp10/EmblemImages/Emblem5">Mizzou Gaming</div>
                <!--Collapse Button-->
                <button class="navbar-toggle" data-toggle = "collapse" data-target = ".navHeaderCollapse">
                        <span class ="icon-bar"></span>
                        <span class ="icon-bar"></span>
                        <span class ="icon-bar"></span>
                </button>
                <!--Collapsable header -->
                <div class="collapse navbar-collapse navHeaderCollapse">
                        <ul class="nav navbar-nav navbar-right">
                                <li><a href="https://babbage.cs.missouri.edu/~cs3380s14grp10/admin.php" class="glyphicon glyphicon-home"> Home</a></li>
                                <!--Dynamically load created tournaments into dropdown list-->
                                <li class = "dropdown">
                                        <a href="#tournaments" class="dropdown-toggle glyphicon glyphicon-edit" data-toggle="dropdown"> Tournaments <b class ="caret"></b></a>
                                        <ul class="dropdown-menu">
										<?php
											require 'pgsql.conf';
											$conn = pg_connect("host=$db_host dbname=$db_name user=$db_user password=$db_pass");
											if (!$conn)
											{
												echo "<br/>An error occurred with connecting to the server.<br/>";
												die();
											}
											$queryZ = 'SELECT productname FROM DB_1.product WHERE category = $1 ORDER BY productname';
											$stmtZ = pg_prepare($conn,"tab",$queryZ) or die( pg_last_error() );
											$resultZ = pg_execute($conn, "tab", array("Tournament")) or die( pg_last_error() );
											
											while ($line=pg_fetch_array($resultZ,null,PGSQL_ASSOC))
											{
												if ( $_SESSION['prevPage'] == 'home' )
													echo"<li><a href='https://babbage.cs.missouri.edu/~cs3380s14grp10/Tournaments/admin_brackets.php?id=".$line['productname']."'>".$line['productname']."</a></li>\n";
												else
													echo"<li><a href='../Tournaments/admin_brackets.php?id=".$line['productname']."'>".$line['productname']."</a></li>\n";
											}
											pg_close($conn);
										?>
                                        </ul>
								</li>
                                <!--END Loading-->
								<li class = "dropdown">
                                        <a href="#" class="dropdown-toggle glyphicon glyphicon-wrench" data-toggle="dropdown"> Store <b class ="caret"></b></a>
                                        <ul class="dropdown-menu">
											<li><a href="https://babbage.cs.missouri.edu/~cs3380s14grp10/ProductForm/product_form.php">Add Product</a></li>
											<li><a href="https://babbage.cs.missouri.edu/~cs3380s14grp10/Store/edit.php">Edit Product</a></li>
											<li><a href="https://babbage.cs.missouri.edu/~cs3380s14grp10/Store/remove.php">Remove Product</a></li>
                                        </ul>
								</li>
								<li class = "dropdown">
										<?php
											require 'pgsql.conf';
											$conn = pg_connect("host=$db_host dbname=$db_name user=$db_user password=$db_pass");
											if (!$conn)
											{
												echo "<br/>An error occurred with connecting to the server.<br/>";
												die();
											}
											$query = 'SELECT * FROM DB_1.purchase_order WHERE ordercomplete = FALSE';
											$result = pg_query($query);
											$orders = pg_num_rows($result);
											
											if($orders > 0) {
												echo '<a href="#" class="dropdown-toggle glyphicon glyphicon-list-alt" data-toggle="dropdown"> Orders (' . $orders . ') <b class ="caret"></b></a>';
											} else {
												echo '<a href="#" class="dropdown-toggle glyphicon glyphicon-list-alt" data-toggle="dropdown"> Orders <b class ="caret"></b></a>';
											}
											pg_close($conn);
										?>
                                        <ul class="dropdown-menu">
											<li><a href="https://babbage.cs.missouri.edu/~cs3380s14grp10/Orders/">Incomplete Orders <?php if($orders > 0) { echo '(' . $orders . ')'; } ?></a></li>
											<li><a href="https://babbage.cs.missouri.edu/~cs3380s14grp10/Orders/index.php?complete=true">Complete Orders</a></li>
                                        </ul>
								</li>
								<li class = "dropdown">
                                        <a href="#" class="dropdown-toggle glyphicon glyphicon-fire" data-toggle="dropdown"> Accounts <b class ="caret"></b></a>
                                        <ul class="dropdown-menu">
											<li><a href="http://babbage.cs.missouri.edu/~cs3380s14grp10/Admin_Registration/admin_registration.php">Register New Admin</a></li>
											<li><a href="http://babbage.cs.missouri.edu/~cs3380s14grp10/Admin_Registration/remove_admin.php">Remove Admin</a></li>
                                        </ul>
								</li>
								<li><a href='https://babbage.cs.missouri.edu/~cs3380s14grp10/Login/logout.php' class='glyphicon glyphicon-user'> Logout</a></li>
                        </ul>
                </div>
        </div>
</div>

