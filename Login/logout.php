<?php
	if (!isset($_SESSION))
	{
		session_start();
	}
	else{}
	// If the session was propagated using a cookie, remove that cookie
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', 1,
			$params["path"], $params["domain"],
			$params["secure"], $params["httponly"]
		);
	}
	else{}

	// Unset all session variables
	$_SESSION = array();
	// Destroy the session
	session_destroy();
	
	// Redirect to home page
	header("Location: http://babbage.cs.missouri.edu/~cs3380s14grp10/Home/home.php");
	exit;
?>
