<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page create_work_name_constraints");
    
    previousPage('teacher_list_works');
    addPreviousPageParameter('group_id', $_GET['group_id']);
    $failed = false;

// TODO is it really useful ?    
/*     // Clean session variables that will be used during the creation of the subject
    if(isset($_SESSION['work_name']))
    {
        unset($_SESSION['work_name']);
    }
    if(isset($_SESSION['extra_time']))
    {
        unset($_SESSION['extra_time']);
    }
    if(isset($_SESSION['size']))
    {
        unset($_SESSION['size']);
    }
    if(isset($_SESSION['files']))
    {
        unset($_SESSION['files']);
    }
    if(isset($_SESSION['number_files']))
    {
        unset($_SESSION['number_files']);
    }
    if(isset($_SESSION['group_min']))
    {
        unset($_SESSION['group_min']);
    }
    if(isset($_SESSION['group_max']))
    {
        unset($_SESSION['group_max']);
    }
    if(isset($_SESSION['link']))
    {
        unset($_SESSION['link']);
    }
    if(isset($_SESSION['level']))
    {
        unset($_SESSION['level']);
    }
    if(isset($_SESSION['subject_id']))
    {
        unset($_SESSION['subject_id']);
    }
*/
    
	// Retrieves the concerned group
    if(isset($_GET['group_id']) && PWHEntity::Valid("PWHGroup", $_GET['group_id']))
    {     
        try
        {
            $group = new PWHGroup();
            $group->Read($_GET['group_id']);
        }
        catch(Exception $ex)
        {
            $failed = true;
            errorReport($ex->getMessage());
        }
    }
    else
    {
        $failed = true;
    }
    
    if(!$failed)
    {
        if(isset($_GET['work_name']) && isset($_GET['extra_time']) && isset($_GET['size'])
             && isset($_GET['group_min']) && isset($_GET['group_max']) && isset($_GET['link']) && isset($_GET['level']))
        {
            $workName = stripslashes($_GET['work_name']);
            $extraTime = $_GET['extra_time'];
            $size = $_GET['size'];
            $groupMin = $_GET['group_min'];
            $groupMax = $_GET['group_max'];
            $link = $_GET['link'];
            $level = $_GET['level'];
            $subject_id = $_GET['subject_id'];
        }
        else
        {
            $workName = "";
            $extraTime = "";
            $size = "";
            $groupMin = "";
            $groupMax = "";
            $link = "";
            $level = "";
            $subject_id = 0;
        }
        
        if(isset($_GET['action']))
        {
            if($_GET['action'] == 'alert_empty')
            {
                errorReport("Vous devez remplir tous les champs obligatoires.");
            }
            else if($_GET['action'] == 'alert_type_req')
            {
                errorReport("Vous devez sp&eacute;cifier des nombres entiers pour le nombre minimum et maximum de membres dans un groupe de rendu.");
            }
            else if($_GET['action'] == 'alert_type_nreq')
            {
                errorReport("Vous devez sp&eacute;cifier des nombres entiers pour la periode de tol&eacute;rance et la taille du rendu.");
            }
            else if($_GET['action'] == 'alert_err')
            {
                errorReport("Vous devez sp&eacute;cifier un nombre de membres minimum inf&eacute;rieur au nombre de membres maximum.");
            }
       }
       
    }
    
    if($failed)
    {
        errorReport("Impossible d'afficher la page demand&eacute;e.");
    }
?>

<section>
	<h2>nom - etape 1/3</h2>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/create_work_name_constraints.html', 800, 600);");
        
        displayErrorReport();
        
//        if(!$failed && $subject->CountTeachers() > 0) FIXME : Why check for teachers ?
        if(!$failed)
        {
    ?>    
    <h4>Contraintes du travail</h4>
    
    <?php
    	$promo = $group->GetPromotion();
    ?>
    <p>Actuellement s&eacute;l&eacute;ctionn&eacute; : <?php echo $group->GetName(); ?></p>
    	<?php 
    		if ($group->GetID() != $promo->GetID())
    			echo '<p>Changer pour la promo : <a href="index.php?page=teacher_create_work_name_constraints&group_id='.$promo->GetID().'&m=6&y=2011&d=9">'.$promo->GetName().'</a></p>';
    	?>
    <p>Changer pour un sous groupe : 
    	<?php
    		
         	foreach(PWHGroup::GetChildrenOf($promo->GetID()) as $g) {
         		if ($group->GetID() == $g->GetID())
	            	echo $g->GetName();
	            else
    				echo '<a href="index.php?page=teacher_create_work_name_constraints&group_id='.$g->GetID().'&m=6&y=2011&d=9">'.$g->GetName().'</a>';
        	}
        ?>
    </p>
    
	<div class="section">
		<form method="post" action="index.php?page=teacher_create_work_files&amp;group_id=<?php echo $_GET['group_id']; ?>">
			<div class="input">
	    		Mati&egrave;re : 
	    		<select name="subject_id" id="subject_id">
	    		     <?php
	                 	foreach($group->GetPromotion()->GetSubjects(true) as $sub) {
	                 		if ($subject_id == $sub->GetID())
		    	            	echo '<option value="'.$sub->GetID().'" selected="selected">';
		    	            else
								echo '<option value="'.$sub->GetID().'">';
		    	           	?>
	    	            		<?php echo $sub->GetName(); ?>
	    	            	</option>
	    	         <?php } ?>
	    	    </select>
		    </div>

			<div class="input">
                <label for="workName">Nom du travail (*):</label>
	            <input type="text" id="work_name" name="workName" size="20" value="<?php echo $workName; ?>"/>
	        </div>
	        <div class="input">
                <label for="groupMin">Membre minimum (*):</label>
	            <input type="text" id="group_min" name="groupMin" size="20" value="<?php echo $groupMin; ?>"/>
	        </div>
	        <div class="input">
                <label for="groupMax">Membre maximum (*):</label>
	            <input type="text" id="group_max" name="groupMax" size="20" value="<?php echo $groupMax; ?>"/>
	        </div>
	        <div class="input">
                <label for="link">Site web du sujet:</label>
	            <input type="text" id="link" name="link" size="20" value="<?php echo $link; ?>"/>
	        </div>
	        <div class="input">
                <label for="level">Charge de travail (en hrs) (*):</label>
	            <input type="text" id="level" name="level" size="20" value="<?php echo $level; ?>"/>
	        </div>
	        <div class="input">
                <label for="extraTime">Tol&eacute;rance (en jrs):</label>
	            <input type="text" id="extra_time" name="extraTime" size="20" value="<?php echo $extraTime; ?>"/>
	        </div>
	        <div class="input">
                <label for="size">Taille (en Mo):</label>
	            <input type="text" id="size" name="size" size="20" value="<?php echo $size; ?>"/>
	        </div>
	        <div class="input">
                <label for="workName">Rendu express:</label>
	            <input type="radio" value="true" name="simple" checked="checked"/> Oui
	            <input type="radio" value="false" name="simple"/> Non
	        </div>
	        <input class="next_form" type="submit" id="next" value="Suivant &raquo;"/>
	   </form>
	</div>
	<?php } ?>
</section>

<script type="text/javascript">
<!--
    function CheckForm()
    {
        var inputName = document.getElementById("work_name");
        var inputGroupMin = document.getElementById("group_min");
        var inputGroupMax = document.getElementById("group_max");
		var inputLevel = document.getElementById("level");
        
        var inputSubmit = document.getElementById("next");
        if(inputName.value == "" || inputGroupMin.value == "" || inputGroupMax.value == "" || inputLevel.value == "")
        {
            inputSubmit.disabled = true;
        }
        else
        {
            inputSubmit.disabled = false;
        }
    }
    
    var inputName = document.getElementById("work_name");
    var inputGroupMin = document.getElementById("group_min");
    var inputGroupMax = document.getElementById("group_max");
	var inputLevel = document.getElementById("level");
    		
    var inputSubmit = document.getElementById("next");
    if(inputName.value == "" || inputGroupMin.value == "" || inputGroupMax.value == "" || inputLevel.value == "")
    {
        inputSubmit.disabled = true;
    }
    inputName.onkeyup = CheckForm;
    inputGroupMin.onkeyup = CheckForm;
    inputGroupMax.onkeyup = CheckForm;
	inputLevel.onkeyup = CheckForm;
//-->
</script>
