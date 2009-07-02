<?php
	/*
	 * logout.php
	 * Logout the user.
	 */

	// Destroy the session
	session_unset();
	session_destroy();
	
	// Redirection to the home page
	redirect('index.php');
?>
