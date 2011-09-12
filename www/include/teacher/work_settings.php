<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page work_settings");
    
    previousPage('teacher_list_works');
    if(isset($_GET['previous'])) {
        previousPage($_GET['previous']);
        addPreviousPageParameter('group_id', $_GET['group_id']);
    }
    addPreviousPageParameter('subject_id', $_GET['subject_id']);
    addPreviousPageParameter('work_id', $_GET['work_id']);
    $failed = false;
    $workName = "???";

    if(isset($_GET['subject_id']) && isset($_GET['work_id'])
        && PWHEntity::Valid("PWHSubject", $_GET['subject_id'])
        && PWHEntity::Valid("PWHWork", $_GET['work_id']))
    {
        try
        {
            $work = new PWHWork();
            $work->Read($_GET['work_id']);
            $subject = new PWHSubject();
            $subject->Read($_GET['subject_id']);
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
        if(isset($_POST['workName']) && isset($_POST['extraTime']) && isset($_POST['size'])
            && isset($_POST['groupMin']) && isset($_POST['groupMax']) && isset($_POST['link']) && isset($_POST['level']))
        {
            
            if($_POST['workName'] == "" || $_POST['groupMin'] == "" || $_POST['groupMax'] == "")
            {
                errorReport("Vous devez remplir tous les champs obligatoires.");
            }
            else if(!preg_match("#^[0-9]+$#", $_POST['groupMin']) || !preg_match("#^[0-9]+$#", $_POST['groupMax']))
            {
                errorReport("Vous devez sp&eacute;cifier des nombres entiers pour le nombre minimum et maximum de membres dans un groupe de rendu.");
            }
            else if($_POST['groupMin'] > $_POST['groupMax'])
            {
                errorReport("Vous devez sp&eacute;cifier un nombre de membres minimum inf&eacute;rieur au nombre de membres maximum.");
            }
            else if(($_POST['extraTime'] != "" && !preg_match("#^[0-9]+$#", $_POST['extraTime']))
                     || ($_POST['size'] != "" && $_POST['size'] != "-" && !preg_match("#^[0-9]+$#", $_POST['size']))
                     || ($_POST['level'] != "" && $_POST['level'] != "-" && !preg_match("#^[0-9]+$#", $_POST['level'])))
            {
                errorReport("Vous devez sp&eacute;cifier des nombres entiers pour la periode de tol&eacute;rance et la taille du rendu.");  
            }
            else
            {
                try
                {
                    $work->SetName(stripslashes($_POST['workName']));
                    
                    $work->SetGroupMin($_POST['groupMin']);
                    $work->SetGroupMax($_POST['groupMax']);
                    if(substr($_POST['link'], 0, 7) == "http://" || $_POST['link'] == "" || $_POST['link'] == "-")
                    {
                        $work->SetLink($_POST['link']);
                    }
                    else if($_SESSION['link'] != "")
                    {
                        $work->SetLink("http://" . $_POST['link']);
                    }
                    $work->SetExtraTime($_POST['extraTime']);
                    $work->SetSize($_POST['size']);
                    $work->SetLevel($_POST['level']);    
                    
                    
                    if($_POST['extraTime'] == "")
                    {
                        $work->SetExtraTime(0);
                    }
                    
                    if($_POST['size'] == "" || $_POST['size'] == "-")
                    {
                        $work->SetSize(0);
                    }
                    
                    if($_POST['level'] == "" || $_POST['level'] == "-")
                    {
                        $work->SetLevel(0);
                    }
                    
                    if($_POST['link'] == "-")
                    {
                        $work->SetLink("");
                    }
                    
                    $work->Update();
                    
                    $deliveries = $work->GetDeliveries();
                    foreach($deliveries as $delivery)
                    {
                        $groups = $delivery->GetGroups();
                        foreach($groups as $group)
                        {
                            $students = $group->GetStudents();
                            PWHEvent::Notify($students, STUDENT_TYPE, "Les contraintes du travail " . $subject->GetName() . "-" . $work->GetName() . " ont &eacute;t&eacute; modifi&eacute;es");
                        }       
                    }
                    $teachers = $subject->GetTeachers();
                    PWHEvent::Notify($teachers, TEACHER_TYPE, "Les contraintes du travail " . $subject->GetName() . "-" . $work->GetName() . " ont &eacute;t&eacute; modifi&eacute;es");
                    PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Mise &agrave; jour travail " . $subject->GetName() . "-" . $work->GetName());
                    successReport("Les contraintes du travail " . $work->GetName() . " ont &eacute;t&eacute; mises &agrave; jour.");
                }
                catch(Exception $ex)
                {
                    PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec mise &agrave; jour travail");
                    errorReport($ex->getMessage());
                }
            }
        }
        
        $workName = mb_strtolower($work->GetName());
    }
    
    if($failed)
    {
        errorReport("Impossible d'afficher la page demand&eacute;e.");
    }
?>

<section>
	<h2>configuration de <?php echo $workName; ?></h2>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/work_settings.html', 800, 600);");
        
	    displayErrorReport();
	    displaySuccessReport();
	    
	    if(!$failed)
	    {
	?>
	<div class="section">
		<form method="post">
            <div class="input">
                <label for="workName">Nom du travail(*):</label>
			    <input type="text" name="workName" id="work_name" size="20" value="<?php echo $work->GetName(); ?>"/>
		    </div>
		    <div class="input">
			        <label for="groupMin">Membre minimum(*):</label>
			        <input type="text" name="groupMin" id="group_min" size="20" value="<?php echo $work->GetGroupMin(); ?>"/>
		    </div>
		    <div class="input">
			        <label for="groupMax">Membre maximum(*):</label>
		            <input type="text" name="groupMax" id="group_max" size="20" value="<?php echo $work->GetGroupMax(); ?>"/>
		    </div>
		    <div class="input">
                <label for="link">Site web du sujet:</label>
	            <input type="text" id="link" name="link" size="20" value="<?php if($work->GetLink() == "") { echo "-"; } else { echo $work->GetLink(); } ?>"/>
	        </div>
	        <div class="input">
                <label for="level">Charge de travail (en hrs):</label>
	            <input type="text" id="level" name="level" size="20" value="<?php if($work->GetLevel() == 0) { echo "-"; } else { echo $work->GetLevel(); } ?>"/>
	        </div>
		    <div class="input">
		        <label for="extraTime">Tol&eacute;rance (en jrs):</label>
		        <input type="text" name="extraTime" id="extra_time" size="20" value="<?php echo $work->GetExtraTime(); ?>"/>
		    </div>
		    <div class="input">
		        <label for="size">Taille (en Mo):</label>
		        <input type="text" name="size" id="size" size="20" value="<?php if($work->GetSize() == 0) { echo "-"; } else { echo $work->GetSize(); } ?>"/>
		        <input type="submit" id="apply" name="apply" value="Appliquer !"/>
		    </div>
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
        var inputSubmit = document.getElementById("apply");
        if(inputName.value == "" || inputGroupMin.value == "" || inputGroupMax.value == "")
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
    var inputSubmit = document.getElementById("apply");
    if(inputName.value == "" || inputGroupMin.value == "" || inputGroupMax.value == "")
    {
        inputSubmit.disabled = true;
    }
    inputName.onkeyup = CheckForm;
    inputGroupMin.onkeyup = CheckForm;
    inputGroupMax.onkeyup = CheckForm;
//-->
</script>
