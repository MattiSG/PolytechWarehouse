<?php
	/*
	 * index.php
	 * The index page, including the php pages.
	 */
	 
	// Starts the session
	session_start();
	
	$GLOBALS['PWH_PATH'] = "./";
	require_once($GLOBALS['PWH_PATH'] . 'libpwh/PWHGlobals.php');
	require_once('include/util.php');
	
	// If he is not logged, the user is redirected to the login page
	if(!isset($_SESSION['login']))
	{
	    if(!isset($_GET['page']) || $_GET['page'] != 'login')
	    {
	        redirect('index.php?page=login');
	    }
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<title>Polytech'WareHouse</title>
		<link rel="stylesheet" type="text/css" href="css/pwh.css"/>
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
							    'login' => 'include/login.php',
							    'logout' => 'include/logout.php',
							    'student_home' => 'include/studentHome.php',
							    'teacher_home' => 'include/teacherHome.php',
							    'teacher_list_subjects' => 'include/teacherListSubjects.php',
							    'teacher_create_subject' => 'include/teacherCreateSubject.php',
							    'teacher_subject_settings' => 'include/teacherSubjectSettings.php',
							    'teacher_list_works' => 'include/teacherListWorks.php',
							    'teacher_create_work' => 'include/teacherCreateWork.php',
							    'teacher_work_settings' => 'include/teacherWorkSettings.php',
							    'teacher_list_groups' => 'include/teacherListGroups.php',
							    'teacher_load_group' => 'include/teacherLoadGroup.php',
							    'teacher_create_group' => 'include/teacherCreateGroup.php',
							    'teacher_group_settings' => 'include/teacherGroupSettings.php',
							    'teacher_erase_database' => 'include/teacherEraseDatabase.php');
							    
							

			    if((isset($_GET['page'])))
			    {
				    $file = $_GET['page'];
				
				    // Authorized page to display
				    if((isset($valid_pages[$file]))) 
				    { ?>	
				        <?php 
				            include($valid_pages[$file]);
				            ?> <p id="links"> <?php 
						    // Back to home link
						    if($file != 'teacher_home' && $file != 'student_home' && $file != 'login') 
						    { ?>					    
						    <a href="index.php"><img src="<?php echo IMG_PATH(); ?>home.png"/>Retour &agrave l'espace personnel</a>
						    <a href="index.php?page=<?php echo $_SESSION['previousPage'] . $_SESSION['previousParams']; ?>">
					            <img src="<?php echo IMG_PATH(); ?>arrow_left.png"/>Retour &agrave la page pr&eacute;c&eacute;dente</a>					
					  <?php }
					        if($file != 'teacher_home' && $file != 'student_home'&& $file != 'login')
					        { ?>
					            
					  <?php }
						    // Logout link, for logged user
						    if(isset($_SESSION['login'])) 
						    { ?>
							    <a href="index.php?page=logout"><img src="<?php echo IMG_PATH(); ?>logout.png"/>D&eacute;connexion</a>
			        <?php	} ?>
						    </p>
				    <?php
				    }
				    // Forbidden page
				    else 
				    { 
                        include('include/forbid.php'); ?>
					    <p id="links"><a href="index.php"><img src="<?php echo IMG_PATH(); ?>home.png"/>Retour &agrave l'espace personnel</a>
					    <?php
						    // Logout link, for logged user
						    if(isset($_SESSION['login'])) 
						    { ?>
							    <a href="index.php?page=logout"><img src="<?php echo IMG_PATH(); ?>logout.png"/>D&eacute;connexion</a>
				    <?php	} ?>
						    </p>
				    <?php		
				    }
			    }
			    // Default page
			    else 
			    { 
			        // TODO: Detection of type of the user to go back to student or teacher home    		        
				    include('include/teacherHome.php');
				    // Logout link, for logged user
				    if(isset($_SESSION['login'])) 
				    { ?>
					    <p id="links"><a href="index.php?page=logout"><img src="<?php echo IMG_PATH(); ?>logout.png"/>D&eacute;connexion</a></p>
		    <?php	}
			    }?>
		</div>
		<div id="footer"><p>Polytech'WareHouse par S&eacute;bastien Mosser & Karim Matrah | Polytech'Nice-Sophia &copy; 2009</p></div>
	</body>
</html>


