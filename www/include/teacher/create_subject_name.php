<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page create_subject_name");
    
    previousPage('teacher_list_subjects');
    addPreviousPageParameter('see', 'less');
    $failed = false;
    
    // Clean session variables that will be used during the creation of the subject
    if(isset($_SESSION['subject_name']))
    {
        unset($_SESSION['subject_name']);
    }
    
    if(isset($_SESSION['promo']))
    {
        unset($_SESSION['promo']);
    }
    
    if(isset($_SESSION['teachers']))
    {
        unset($_SESSION['teachers']);
    }
        
    if(isset($_GET['subject_name']))
    {
        $subjectName = stripslashes($_GET['subject_name']);       
    }
    else
    {
        $subjectName = "";
    }
    
    if(isset($_GET['promo']))
    {
        $promo = $_GET['promo'];
    }
    else
    {
        $promo = -1;
    }
    
    try
    {
        $promos = PWHGroup::GetPromotions();
    }
    catch(Exception $ex)
    {
        $failed = true;
        errorReport($ex->getMessage());
    }
    
    if(!$failed)
    {
        if(count($promos) == 0)
        {
            errorReport("Aucune promotion n'est disponible. Vous ne pouvez pas cr&eacute;er de mati&egrave;re.");
        }
    }
    
    if($failed)
    {
        errorReport("Impossible d'afficher la page demand&eacute;e.");
    }
?>

<fieldset>
	<legend>nom - etape 1/3</legend>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/create_subject_name.html', 800, 550);");
        
        displayErrorReport();

        if(!$failed && count($promos) > 0)
        { ?>
	<div class="section">
		<form method="post" action="index.php?page=teacher_create_subject_teachers&amp;index=A">
	        <div class="input">
                <label for="subjectName">Nom de la mati&egrave;re:</label>
	            <input type="text" id="subject_name" name="subjectName" size="20" value="<?php echo $subjectName; ?>"/>
	        </div>
	        <div class="input">
	            <label for="promo">Promotion:</label>
	            <select name="promo" id="promo">
                    <?php 
                        foreach($promos as $p)
                        { ?>  
                        <option value="<?php echo $p->GetID(); ?>" <?php if($p->GetID() == $promo){ echo 'selected="selected"'; } ?>><?php echo $p->GetName(); ?></option>
                    <?php } ?>
		        </select>
	        </div>
	        <input class="next_form" type="submit" id="next" value="Suivant &raquo;"/>
	   </form>
	</div>
	<?php } ?>
</fieldset>

<script type="text/javascript">
<!--
    function CheckForm()
    {
        var inputName = document.getElementById("subject_name");
        var inputSubmit = document.getElementById("next");
        if(inputName.value == "")
        {
            inputSubmit.disabled = true;
        }
        else
        {
            inputSubmit.disabled = false;
        }
    }
    
    var inputName = document.getElementById("subject_name");
    var inputSubmit = document.getElementById("next");
    inputName.onkeyup = CheckForm;
    if(inputName.value == "")
    {
        inputSubmit.disabled = true;
    }
//-->
</script>

