<?php
    $_SESSION['errorMessages'] = array();
    $_SESSION['successMessages'] = array();
    $_SESSION['previousPage'] = '';
    $_SESSION['previousParams'] = '';
    
    /*
	 * A simple redirection function to the specified url.
	 */
	function redirect($url)
	{ 
		die('<meta http-equiv="refresh" content="0;URL=' . $url . '">'); 
	}
	
	/*
	 * A simple function to the specified the previous page.
	 */
	function previousPage($page)
	{ 
		 $_SESSION['previousPage'] = $page;
	}
	
	/*
	 * A simple function to the specified the previous page parameters.
	 */
	function addPreviousPageParameter($param, $value)
	{
	     $_SESSION['previousParams'] .= "&amp;" . $param . '=' . $value;    
	}
		 
	/*
	 * A simple function to add a new error message
	 */
	function errorReport($msg)
	{ 
		array_push($_SESSION['errorMessages'], $msg); 
	}
	
	/*
	 * A simple function to add a new error message
	 */
	function successReport($msg)
	{ 
		array_push($_SESSION['successMessages'], $msg); 
	}
	
	/*
	 * A simple function to display error messages
	 */
	function displayErrorReport()
	{ 
		foreach($_SESSION['errorMessages'] as $msg)
		{
		     echo '<div class="failed"><div class="up"><div></div></div><p><img src="' . IMG_PATH() . 'cancel.png"/>' . $msg . 
		            '</p><div class="down"><div></div></div></div>';
		} 
	}
	
	/*
	 * A simple function to display success messages
	 */
	function displaySuccessReport()
	{ 
		foreach($_SESSION['successMessages'] as $msg)
		{
		     echo '<div class="success"><div class="up"><div></div></div><p><img src="' . IMG_PATH() . 'accept.png"/>' . $msg . 
		            '</p><div class="down"><div></div></div></div>';
		} 
	}
	
?>
