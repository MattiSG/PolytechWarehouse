<?php 
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page group_settings_student");
    
    previousPage("teacher_list_groups");
      
    // Retrieves the concerned group
    if(isset($_GET['group_id']))
    {
        try
        {
            $group = new PWHGroup();
            $group->Read($_GET['group_id']);
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage);
        }
    }
    
    // [FORM] Adds a student to the group
    if(isset($_POST['studentLogin']) && isset($_POST['studentFirstName']) && isset($_POST['studentLastName']) && isset($_POST['studentEmail']))
    {
        try
        {
            $student = new PWHStudent();
            $student->SetLogin(stripslashes($_POST['studentLogin']));
            $student->SetFirstName(stripslashes($_POST['studentFirstName']));
            $student->SetLastName(stripslashes($_POST['studentLastName']));
            $student->SetEmail(stripslashes($_POST['studentEmail']));
            $student->Create(true);        
            $group->AddStudents(array($student->GetID()));
            $group->Update();
            PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Cr&eacute;ation &eacute;tudiants promo " . $group->GetName());
            successReport("L'&eacute;tudiant " . $student->GetLogin() . " a &eacute;t&eacute; cr&eacute;e et ajout&eacute; au groupe " . $group->GetName() . ".");
        }
        catch(Exception $ex)
        {
            PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Echec cr&eacute;ation &eacute;tudiant promo " . $group->GetName() . ": &eacute;tudiant d&eacute;j&agrave; existant");
            errorReport("L'&eacute;tudiant existe d&eacute;j&agrave;.");
        }
    }
?>

<fieldset>
	<legend>configuration de <?php echo mb_strtolower($group->GetName()); ?></legend>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/group_settings_student.html', 800, 550);");
        
	    displayErrorReport();
	    displaySuccessReport();
	?>
    <div class="tab">
      <ul>
        <li><a href="index.php?page=teacher_group_settings_name&amp;group_id=<?php echo $group->GetID(); ?>">Nom</a></li>
        <?php if($group->GetParentID() == -1)
        { ?>
        <li class="active"><a href="index.php?page=teacher_group_settings_student&amp;group_id=<?php echo $group->GetID(); ?>">Cr&eacute;ation rapide d'&eacute;tudiants</a></li>
        <?php } ?>
        <li><a href="index.php?page=teacher_group_settings_students_add&amp;group_id=<?php echo $group->GetID(); ?>&amp;index=A&amp;index_alt=A">Ajout d'&eacute;tudiants</a></li>
        <li><a href="index.php?page=teacher_group_settings_students_remove&amp;group_id=<?php echo $group->GetID(); ?>&amp;index=A">Suppression d'&eacute;tudiants</a></li>
        <li><a href="index.php?page=teacher_group_settings_students_edit&amp;group_id=<?php echo $group->GetID(); ?>&amp;index=A">Edition des profils</a></li>
      </ul>
    </div>
	<div class="section">
	    <form method="post">
		    <div class="input">
                <label for="studentLogin">Login de l'&eacute;tudiant:</label> <input type="text" id="student_login" name="studentLogin" size="20"/>
		    </div>
		    <div class="input">
		        <label for="studentFirstName">Pr&eacute;nom de l'&eacute;tudiant:</label><input type="text" id="student_firstname" name="studentFirstName" size="20"/>
		    </div>
		    <div class="input">
		        <label for="studentLastName">Nom de l'&eacute;tudiant:</label><input type="text" id="student_lastname" name="studentLastName" size="20"/>
		    </div>
		    <div class="input">
		        <label for="studentEmail">Email de l'&eacute;tudiant:</label><input type="text" id="student_email" name="studentEmail" size="20"/><input type="submit" id="create" value="Cr&eacute;er !"/>
		    </div>
	    </form>
    </div>
</fieldset>

<script type="text/javascript">
<!--
    function CheckForm()
    {
        var inputLogin = document.getElementById("student_login");
        var inputFirstName = document.getElementById("student_firstname");
        var inputLastName = document.getElementById("student_lastname");
        var inputEmail = document.getElementById("student_email");
        var inputSubmit = document.getElementById("create");
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
    var inputSubmit = document.getElementById("create");
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
