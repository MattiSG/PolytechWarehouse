<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page edit_teacher");
    
    previousPage("admin_edit_teachers");
    addPreviousPageParameter("index", $_GET['index']);
      
    // Retrieves the concerned student
    if(isset($_GET['teacher_id']))
    {
        try
        {
            $teacher = new PWHTeacher();
            $teacher->Read($_GET['teacher_id']);
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage);
        }
    }
    
    // [FORM]  Update a teacher
    if(isset($_POST['teacherLogin']) && isset($_POST['teacherFirstName']) && isset($_POST['teacherLastName']) && isset($_POST['teacherEmail']))
    {
        try
        {
            $teacher->SetLogin(stripslashes($_POST['teacherLogin']));
            $teacher->SetFirstName(stripslashes($_POST['teacherFirstName']));
            $teacher->SetLastName(stripslashes($_POST['teacherLastName']));
            $teacher->SetEmail(stripslashes($_POST['teacherEmail']));
            $teacher->Update();      
            successReport("L'enseignant " . $teacher->GetLogin() . " a &eacute;t&eacute; mis &agrave; jour.");
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());
        }
    }
?>

<fieldset>
	<legend>Profil de <?php echo $teacher->GetLastname() . " " . $teacher->GetFirstName(); ?></legend>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("#");
        
	    displayErrorReport();
	    displaySuccessReport();
	?>
    <h4>Informations sur l'enseignant</h4>
	<div class="section">
	    <form method="post">
		    <div class="input">
                <label for="teacherLogin">Login de l'enseignant:</label> <input type="text" id="teacher_login" name="teacherLogin" size="20" value="<?php echo $teacher->GetLogin(); ?>" />
		    </div>
		    <div class="input">
		        <label for="teacherFirstName">Pr&eacute;nom de l'enseignant:</label><input type="text" id="teacher_firstname" name="teacherFirstName" size="20" value="<?php echo $teacher->GetFirstName(); ?>"/>
		    </div>
		    <div class="input">
		        <label for="teacherLastName">Nom de l'enseignant:</label><input type="text" id="teacher_lastname" name="teacherLastName" size="20" value="<?php echo $teacher->GetLastName(); ?>"/>
		    </div>
		    <div class="input">
		        <label for="teacherEmail">Email de l'enseignant:</label><input type="text" id="teacher_email" name="teacherEmail" size="20" value="<?php echo $teacher->GetEmail(); ?>"/><input type="submit" id="apply" value="Appliquer !"/>
		    </div>
	    </form>
    </div>
</fieldset>

<script type="text/javascript">
<!--
    function CheckForm()
    {
        var inputLogin = document.getElementById("teacher_login");
        var inputFirstName = document.getElementById("teacher_firstname");
        var inputLastName = document.getElementById("teacher_lastname");
        var inputEmail = document.getElementById("teacher_email");
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
    
    var inputLogin = document.getElementById("teacher_login");
    var inputFirstName = document.getElementById("teacher_firstname");
    var inputLastName = document.getElementById("teacher_lastname");
    var inputEmail = document.getElementById("teacher_email");
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
