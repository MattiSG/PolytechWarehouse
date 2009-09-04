<?php
	/*
	 * logout.php
	 * Logout the user.
	 */

    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "D&eacute;connexion de l'utilisateur");
	// Destroy the session
	session_unset();
	session_destroy();
	
	// Redirection to the home page
	redirect('index.php');
?>
