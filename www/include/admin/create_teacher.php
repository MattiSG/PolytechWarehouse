<?php
    previousPage("admin_database_management");
      
    
    // [FORM] Adds a teacher to the group
    if(isset($_POST['teacherLogin']) && isset($_POST['teacherFirstName']) && isset($_POST['teacherLastName']) && isset($_POST['teacherEmail']))
    {
        if(!preg_match("#^[a-zA-Z0-9]+$#", $_POST['teacherLogin']))
        {
            errorReport("Echec cr&eacute;ation de l'enseigant: le login ne doit comporter que des chiffres et des lettres");  
        }
        else if(!preg_match("#^[-éèêëàâäïîûùüöôç'a-zA-Z0-9 ]+$#", $_POST['teacherFirstName']))
        {
            errorReport("Echec cr&eacute;ation de l'enseigant: le pr&eacute;nom ne doit comporter des lettres, espaces, apostrophes ou tir&eacute;s");  
        }
        else if(!preg_match("#^[-éèêëàâäïîûùüöôç'a-zA-Z0-9 ]+$#", $_POST['teacherLastName']))
        {
            errorReport("Echec cr&eacute;ation de l'enseigant: le nom ne doit comporter des lettres, espaces, apostrophes ou tir&eacute;s");  
        }
        else if(!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST['teacherEmail']))
        {
            errorReport("Echec cr&eacute;ation de l'enseigant: l'email est invalide");  
        }
        else
        {
            try
            {
                $teacher = new PWHTeacher();
                $teacher->SetLogin(strtolower(stripslashes($_POST['teacherLogin'])));
                $teacher->SetFirstName(stripslashes($_POST['teacherFirstName']));
                $teacher->SetLastName(stripslashes($_POST['teacherLastName']));
                $teacher->SetEmail(stripslashes($_POST['teacherEmail']));
                $teacher->Create(true);        
                successReport("L'enseignant " . $teacher->GetLogin() . " a &eacute;t&eacute; cr&eacute;e.");
            }
            catch(Exception $ex)
            {
                errorReport("L'enseignant existe d&eacute;j&agrave;.");
            }
        }
    }
?>

<fieldset>
	<legend>Creation express d'enseignants</legend>
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
                <label for="teacherLogin">Login de l'enseignant:</label> <input type="text" id="teacher_login" name="teacherLogin" size="20"/>
		    </div>
		    <div class="input">
		        <label for="teacherFirstName">Pr&eacute;nom de l'enseignant:</label><input type="text" id="teacher_firstname" name="teacherFirstName" size="20"/>
		    </div>
		    <div class="input">
		        <label for="teacherLastName">Nom de l'enseignant:</label><input type="text" id="teacher_lastname" name="teacherLastName" size="20"/>
		    </div>
		    <div class="input">
		        <label for="teacherEmail">Email de l'enseignant:</label><input type="text" id="teacher_email" name="teacherEmail" size="20"/><input type="submit" id="create" value="Cr&eacute;er !"/>
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
    
    var inputLogin = document.getElementById("teacher_login");
    var inputFirstName = document.getElementById("teacher_firstname");
    var inputLastName = document.getElementById("teacher_lastname");
    var inputEmail = document.getElementById("teacher_email");
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
