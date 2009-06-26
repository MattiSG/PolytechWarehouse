<?php
	/*
	 * index.php
	 * The index page, including the php pages.
	 */
	 
	// Starts the session
	session_start();
	
	$GLOBALS['PWH_PATH'] = "./";
	
	/*
	 * A simple redirection function to the specified url.
	 */
	function redirect($url)
	{ 
		die('<meta http-equiv="refresh" content="0;URL='.$url.'">'); 
	}
	
	/*
	 * A simple function to the specified the previous page.
	 */
	function previousPage($page)
	{ 
		 $_SESSION['previous_page'] = $page;
	}

	
	
	include($GLOBALS['PWH_PATH'] . 'libpwh/PWHGlobals.php');
	
	
	// If he is not logged, the user is redirected to the login page
	if(!isset($_SESSION['login']))
	{
		if(!isset($_GET['page'])) 
		{
			redirect('index.php?page=login');
		}
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<title>Polytech'WareHouse</title>
		<link rel="stylesheet" type="text/css" href="pwh.css"/>
	</head>
	<body>
		<div id="header"></div>
		<div id="menu">
			<ul>
			</ul>
		</div>
		<div id="content">
		    <?php		    
			    // Array of all the authorized page to display and their name
			    $valid_pages = array(
							    'login' => 'login.php',
							    'logout' => 'logout.php',
							    'student_home' => 'studentHome.php',
							    'teacher_home' => 'teacherHome.php',
							    'teacher_list_groups' => 'teacherListGroups.php',
							    'teacher_load_group' => 'teacherLoadGroup.php',
							    'teacher_group_settings' => 'teacherGroupSettings.php');
							

			    if((isset($_GET['page'])))
			    {
				    $file = $_GET['page'];
				
				    // Authorized page to display
				    if((isset($valid_pages[$file]))) 
				    { ?>	
				        <?php 
				            include($valid_pages[$file]);
						    // Back to home link
						    if($file != 'home') 
						    { ?>
						    <p id="links"><a href="index.php"><img src="<?php echo IMG_PATH(); ?>home.png"/>Back to Home</a>
						
					  <?php }
					        if($file != 'home' && $file != 'login')
					        { ?>
					            <a href="index.php?page=<?php echo $_SESSION['previous_page']; ?>"><img src="<?php echo IMG_PATH(); ?>arrow_left.png"/>Back to previous</a>
					  <?php }
						    // Logout link, for logged user
						    if(isset($_SESSION['login'])) 
						    { ?>
							    <a href="index.php?page=logout"><img src="<?php echo IMG_PATH(); ?>logout.png"/>Logout</a>
			        <?php	} ?>
						    </p>
				    <?php
				    }
				    // Forbidden page
				    else 
				    { 
                        include('forbid.php'); ?>
					    <p id="links"><a href="index.php"><img src="<?php echo IMG_PATH(); ?>home.png"/>Back to home</a>
					    <?php
						    // Logout link, for logged user
						    if(isset($_SESSION['login'])) 
						    { ?>
							    <a href="index.php?page=logout"><img src="<?php echo IMG_PATH(); ?>logout.png"/>Logout</a>
				    <?php	} ?>
						    </p>
				    <?php		
				    }
			    }
			    // Default page
			    else 
			    { 
			        // TODO: Detection of type of the user to go back to student or teacher home    		        
				    include('teacherHome.php');
				    // Logout link, for logged user
				    if(isset($_SESSION['login'])) 
				    { ?>
					    <p id="links"><a href="index.php?page=logout"><img src="<?php echo IMG_PATH(); ?>logout.png"/>Logout</a></p>
		    <?php	}
			    }?>
		</div>
		<div id="footer"><p>Polytech'WareHouse designed by Karim Matrah | Polytech'Nice-Sophia &copy; 2009</p></div>
	</body>
</html>

