<?php
//Check if logged in and set SESSION variable accordingly
$loggedIn = empty($_SESSION['loggedin']) ? false : $_SESSION['loggedin'];
//Try to log in to the database, quit if it cannot
require 'pgsql.conf';
$conn = pg_connect("host=$db_host dbname=$db_name user=$db_user password=$db_pass");
if(!$conn) {
	echo "<br/>An error occurred with connecting to the server.<br/>";
	die();
}
//Start session if not already started
if(!isset($_SESSION)) {
	session_start();
}
?>
<div class="navbar navbar-inverse navbar-static-top" >
        <div class="container">
                <a href="https://babbage.cs.missouri.edu/~cs3380s14grp10/Home/home.php" class="navbar-brand"><img src="https://babbage.cs.missouri.edu/~cs3380s14grp10/EmblemImages/Emblem5">Mizzou Gaming</a>
                <!--Collapse Button-->
                <button class="navbar-toggle" data-toggle = "collapse" data-target = ".navHeaderCollapse">
                        <span class ="icon-bar"></span>
                        <span class ="icon-bar"></span>
                        <span class ="icon-bar"></span>
                </button>
                <!--Collapsable header -->
                <div class="collapse navbar-collapse navHeaderCollapse">
                        <ul class="nav navbar-nav navbar-right">
                                <li><a href="https://babbage.cs.missouri.edu/~cs3380s14grp10/Home/home.php" class="glyphicon glyphicon-home"> Home</a></li>
                                <li class = "dropdown">
                                        <a href="#tournaments" class="dropdown-toggle glyphicon glyphicon-tower" data-toggle="dropdown"> Tournaments <b class ="caret"></b></a>
										<ul class="dropdown-menu">
										<li><a href='https://babbage.cs.missouri.edu/~cs3380s14grp10/Tournaments/team_registration.php'>Register Team</a></li>
										<li><a href='https://babbage.cs.missouri.edu/~cs3380s14grp10/Tournaments/fa_registration.php'>Register As Free Agent</a></li>
										<?php
											$query = 'SELECT productname FROM DB_1.product WHERE category = $1 ORDER BY productname';
											$stmt = pg_prepare($conn,"tab",$query) or die( pg_last_error() );
											$result = pg_execute($conn, "tab", array("Tournament")) or die( pg_last_error() );
											
											while ($line=pg_fetch_array($result,null,PGSQL_ASSOC))
											{
												if ( $_SESSION['prevPage'] == 'home' )
													echo"<li><a href='https://babbage.cs.missouri.edu/~cs3380s14grp10/Tournaments/index.php?id=".$line['productname']."'>".$line['productname']."</a></li>\n";
												else
													echo"<li><a href='../Tournaments/index.php?id=".$line['productname']."'>".$line['productname']."</a></li>\n";
											}
										?>
                                        </ul>
								</li>
                                <li><a href="https://babbage.cs.missouri.edu/~cs3380s14grp10/Store/index.php" class="glyphicon glyphicon-tags"> Store</a></li>
                                <li><a href="https://babbage.cs.missouri.edu/~cs3380s14grp10/ShoppingCart/index.php" class="glyphicon glyphicon-shopping-cart"> Cart</a></li>
								<?php
								//Checks if user is logged in, and displays proper headers accordingly
								if($loggedIn) {
									echo "<li><a href='https://babbage.cs.missouri.edu/~cs3380s14grp10/Orders/customer_order.php' class='glyphicon glyphicon-user'> Orders </a></li>";
								}
					

								
								if ($loggedIn )
								{
									echo "<li><a href='https://babbage.cs.missouri.edu/~cs3380s14grp10/Login/logout.php' class='glyphicon glyphicon-user'> Logout</a></li>" ;
								}
								else {
									echo "<li><a href='https://babbage.cs.missouri.edu/~cs3380s14grp10/Login/login.php' class='glyphicon glyphicon-user'> Login</a></li>" ;
								}
								//Close database
								pg_close($conn);
								?>
                        </ul>
                </div>
        </div>
</div>

