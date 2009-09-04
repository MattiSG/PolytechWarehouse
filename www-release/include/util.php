<?php
    $_SESSION['errorMessages'] = array();
    $_SESSION['successMessages'] = array();
    $_SESSION['previousPage'] = '';
    $_SESSION['previousParams'] = '';
    
    ini_set("upload_max_filesize", "20971520");
    
    /*
     * PHP5 class autoloading
     */
    function __autoload($className)
    {
        require_once(LIB_PATH() . $className . '.php');
    }
    
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
	
	/*
	 * A simple function that calculates the number of days between two dates (format YYYY-MM-DD)
	 */
	function dateDiff($begin, $end)
	{
	    $begin = explode(" ", $begin);
	    $beginDate = explode("-", $begin[0]);
	    $beginTime = explode(":", $begin[1]);
	    
	    $end = explode(" ", $end);
	    $endDate = explode("-", $end[0]);
	    $endTime = explode(":", $end[1]);
	    
	    $diff = mktime($endTime[0], $endTime[1], $endTime[2], $endDate[1], $endDate[2], $endDate[0]) 
	            - mktime($beginTime[0], $beginTime[1], $beginTime[2], $beginDate[1], $beginDate[2], $beginDate[0]);
	    return $diff;
	}

    /*
     * A comparator function between two persons
     */
    function person_comparator($person1, $person2)
    {
        return strcmp($person1->GetLastName(), $person2->GetLastName());
    }
    
    /*
     * A comparator function between two entities
     */
    function entity_comparator($entity1, $entity2)
    {
        return strcmp($entity1->GetName(), $entity2->GetName());
    }
        
    /*
	 * Initiates the Show/Hide javascript function.
	 */
	function INIT_JS() 
	{
		echo '<script type="text/javascript">';
			echo 'function showhide(node, id){';
				echo 'if (document.getElementById){';
					echo 'obj = document.getElementById(id);';
					echo 'if (obj.style.display == "none"){';
						echo 'obj.style.display = "";';
						echo 'obj = document.getElementById(node);';
						echo 'obj.src="' . IMG_PATH(). 'plus.png";';
					echo '} else {';
						echo 'obj.style.display = "none";';
						echo 'obj = document.getElementById(node);';
						echo 'obj.src="' . IMG_PATH() . 'minus.png";';
					echo '}';
				echo '}';
			echo '}';
			
			echo 'function popup(page, width, height) {';
                echo 'var top=(screen.height-height)/2;';
                echo 'var left=(screen.width-width)/2;';
                echo 'window.open(page,"","top="+top+",left="+left+",width="+width+",height="+height+",toolbar=no, menubar=no, scrollbars=yes, resizable=no, location=no, directories=no, status=no");';
           echo '}';
		echo '</script>';
	}
?>
