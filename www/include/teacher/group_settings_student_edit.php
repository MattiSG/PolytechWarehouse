<?php 
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page group_settings_student_edit");
    
    previousPage("teacher_group_settings_students_edit");
    addPreviousPageParameter("group_id", $_GET['group_id']);
    addPreviousPageParameter("index", $_GET['index']);
    
    $failed = false;
    $studentName = "???";
      
    // Retrieves the concerned student
    if(isset($_GET['student_id']))
    {
        try
        {
            $student = new PWHStudent();
            $student->Read($_GET['student_id']);
        }
        catch(Exception $ex)
        {
            $failed = true;
            errorReport($ex->getMessage);
        }
    }
    else
    {
        $failed = true;
    }
    
    if(!$failed)
    {
        // [FORM] Update a student
        if(isset($_POST['studentLogin']) && isset($_POST['studentFirstName']) && isset($_POST['studentLastName']) && isset($_POST['studentEmail']))
        {
            if(!preg_match("#^[a-zA-Z0-9]+$#", $_POST['studentLogin']))
            {
                errorReport("Echec de la mise &agrave; jour du profil: le login ne doit comporter que des chiffres et des lettres");  
            }
            else if(!preg_match("#^[-éèêëàâäïîûùüöôç'a-zA-Z0-9 ]+$#", $_POST['studentFirstName']))
            {
                errorReport("Echec de la mise &agrave; jour du profil: le pr&eacute;nom ne doit comporter des lettres, espaces, apostrophes ou tir&eacute;s");  
            }
            else if(!preg_match("#^[-éèêëàâäïîûùüöôç'a-zA-Z0-9 ]+$#", $_POST['studentLastName']))
            {
                errorReport("Echec de la mise &agrave; jour du profil: le nom ne doit comporter des lettres, espaces, apostrophes ou tir&eacute;s");  
            }
            else if(!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST['studentEmail']))
            {
                errorReport("Echec de la mise &agrave; jour du profil: l'email est invalide");  
            }
            else
            {
                try
                {
                    $student->SetLogin(strtolower(stripslashes($_POST['studentLogin'])));
                    $student->SetFirstName(stripslashes($_POST['studentFirstName']));
                    $student->SetLastName(stripslashes($_POST['studentLastName']));
                    $student->SetEmail(stripslashes($_POST['studentEmail']));
                    $student->Update();
                    PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Mise &agrave; jour profil &eacute;tudiant " . $student->GetLogin());
                    successReport("Le profil de l'&eacute;tudiant a &eacute;t&eacute; mis &agrave; jour."); 
                }
                catch(Exception $ex)
                {
                    PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec mise &agrave; jour profil &eacute;tudiant");
                    errorReport($ex->getMessage());
                }
            }
        }
        
        $studentName = mb_strtolower($student->GetLastname() . " " . $student->GetFirstName());
    }
    
    if($failed)
    {
        errorReport("Impossible d'afficher la page demand&eacute;e.");
    }
?>

<section>
	<h2>Profil de <?php echo $studentName; ?></h2>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/group_settings_student_edit.html', 800, 600);");
        
	    displayErrorReport();
	    displaySuccessReport();
	    
	    if(!$failed)
	    {
	?>
	<h4>Informations sur l'&eacute;tudiant</h4>
	<div class="section">
	    <form method="post">
		    <div class="input">
                <label for="studentLogin">Login de l'&eacute;tudiant:</label><input type="text" id="student_login" name="studentLogin" size="20" value="<?php echo $student->GetLogin(); ?>" />
		    </div>
		    <div class="input">
		        <label for="studentFirstName">Pr&eacute;nom de l'&eacute;tudiant:</label><input type="text" id="student_firstname" name="studentFirstName" size="20" value="<?php echo $student->GetFirstName(); ?>" />
		    </div>
		    <div class="input">
		        <label for="studentLastName">Nom de l'&eacute;tudiant:</label><input type="text" id="student_lastname" name="studentLastName" size="20" value="<?php echo $student->GetLastName(); ?>" />
		    </div>
		    <div class="input">
		        <label for="studentEmail">Email de l'&eacute;tudiant:</label><input type="text" id="student_email" name="studentEmail" size="20" value="<?php echo $student->GetEmail(); ?>" /><input type="submit" id="apply" value="Appliquer !"/>
		    </div>
	    </form>
    </div>
    <?php } ?>
</section>

<script type="text/javascript">
<!--
    function CheckForm()
    {
        var inputLogin = document.getElementById("student_login");
        var inputFirstName = document.getElementById("student_firstname");
        var inputLastName = document.getElementById("student_lastname");
        var inputEmail = document.getElementById("student_email");
        var inputSubmit = document.getElementById("apply");
        if(inputLogin.value == "" || inputFirstName.value == "" || inputLastName.value == "" || inputEmail.value == "")
        {
            inputSubmit.disabled = true;
        }
        else
        {
            inputSubmit.disabled = false;
        }
    }
    
    var inputLogin = document.getElementById("student_login");
    var inputFirstName = document.getElementById("student_firstname");
    var inputLastName = document.getElementById("student_lastname");
    var inputEmail = document.getElementById("student_email");
    var inputSubmit = document.getElementById("apply");
    inputLogin.onkeyup = CheckForm;
    inputFirstName.onkeyup = CheckForm;
    inputLastName.onkeyup = CheckForm;
    inputEmail.onkeyup = CheckForm;
    if(inputLogin.value == "" || inputFirstName.value == "" || inputLastName.value == "" || inputEmail.value == "")
    {
        inputSubmit.disabled = true;
    }
//-->
</script>
