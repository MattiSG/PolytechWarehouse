<?php
	/*
	 * index.php
	 * The index page, including the php pages.
	 */
	 
	// Starts the session
	session_start();
	
	$GLOBALS['PWH_PATH'] = "./";
	require_once($GLOBALS['PWH_PATH'] . 'libpwh/PWHHeader.php');
	require_once('include/util.php');
	// To extract the config from hard--coded to local file
	require_once('config/local.conf.php');
	
	// If he is not logged, the user is redirected to the login page
	if(!isset($_SESSION['id']))
	{
	    if(!isset($_GET['page']) || $_GET['page'] != 'login')
	    {
	        redirect('index.php?page=login');
	    }
	}
?>

<!doctype html>  
<html lang="en-EN">
	<head>
	    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
		
        <title>Polytech'WareHouse</title>
		<link rel="stylesheet" type="text/css" href="css/header.css"/>
		<link rel="stylesheet" type="text/css" href="css/log.css"/>
		<link rel="stylesheet" type="text/css" href="css/content.css"/>
		<link rel="stylesheet" type="text/css" href="css/footer.css"/>
		<link type="text/css" rel="stylesheet" media="all" href="css/calendar.css" />

        <?php INIT_JS(); ?>
        
        <script language="javascript" type="text/javascript" src="js/mootools-core-1.3.2-full-nocompat-yc.js"></script>
        <script language="javascript" type="text/javascript" src="js/mootools-more-1.3.2.1.js"></script>
	</head>
	<body>
		<a href="index.php">
			<header></header>
		</a>
		
		<div id="main" role="main">
		    <?php		    
			     
		        include('include/pages.php');
		            
			    if((isset($_GET['page'])))
			    {
				    $file = $_GET['page'];
				
				    // Authorized page to display
				    if((isset($validPages[$file]))) 
				    { ?>	
				        <?php 
				            include($validPages[$file]);
				            ?> <p id="links"> <?php 
						    // Back to home link
						    if($file != 'teacher_home' && $file != 'student_home' && $file != 'admin_home' && $file != 'login') 
						    { ?>					    
						    <a href="index.php"><img src="<?php echo IMG_PATH(); ?>home.png"/>Retour &agrave; l'espace personnel</a>
						    <a href="index.php?page=<?php echo $_SESSION['previousPage'] . $_SESSION['previousParams']; ?>">
					            <img src="<?php echo IMG_PATH(); ?>arrow_left.png"/>Retour &agrave; la page pr&eacute;c&eacute;dente</a>					
					  <?php }
						    // Logout link, for logged user
						    if(isset($_SESSION['logged'])) 
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
					    <p id="links"><a href="index.php"><img src="<?php echo IMG_PATH(); ?>home.png"/>Retour &agrave; l'espace personnel</a>
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
			        if($_SESSION['type'] == STUDENT_TYPE)
			        {
			            include('include/student/home.php');
			        }
			        else if($_SESSION['type'] == TEACHER_TYPE)
			        {
			            include('include/teacher/home.php');
			        }
			        else if($_SESSION['type'] == ADMIN_TYPE)
			        {	            
				        include('include/admin/home.php');
				    }
				    // Logout link, for logged user
				    if(isset($_SESSION['logged'])) 
				    { ?>
					    <p id="links"><a href="index.php?page=logout"><img src="<?php echo IMG_PATH(); ?>logout.png"/>D&eacute;connecter <?php echo $_SESSION['login'] ?></a></p>
		    <?php	}
			    }?>
		</div>
		
		<footer>
		    <p>
		        <a href="index.php?page=credits">Cr&eacute;dits</a>
		    </p>
	    </footer>
	</body>
</html>
