<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page delivery_settings");
    
    previousPage('teacher_list_deliveries');
    addPreviousPageParameter('subject_id', $_GET['subject_id']);
    addPreviousPageParameter('work_id', $_GET['work_id']);
    addPreviousPageParameter('delivery_id', $_GET['delivery_id']);

    if(isset($_GET['subject_id']) && isset($_GET['work_id']) && isset($_GET['delivery_id']))
    {
        try
        {
            $subject = new PWHSubject();
            $subject->Read($_GET['subject_id']);
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());
        }
        
        try
        {
            $delivery = new PWHDelivery();
            $delivery->Read($_GET['delivery_id']);
            $teacher = new PWHTeacher();
            $teacher->Read($delivery->GetOwnerID());
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());
        }
        
        try
        {
            $work = new PWHWork();
            $work->Read($_GET['work_id']);
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());
        }
    }
    
    $update = false;
    if(isset($_POST['groupCompositionDeadline_day']) && isset($_POST['groupCompositionDeadline_month']) && isset($_POST['groupCompositionDeadline_year'])
        && isset($_POST['groupCompositionDeadline_hour']) && isset($_POST['groupCompositionDeadline_minute']))
    {
        if(checkdate($_POST['groupCompositionDeadline_month'], $_POST['groupCompositionDeadline_day'], $_POST['groupCompositionDeadline_year'])
            && preg_match("#^[0-9]{2}$#", $_POST['groupCompositionDeadline_hour']) && preg_match("#^[0-9]{2}$#", $_POST['groupCompositionDeadline_minute']))
        {
            try
            {
                $delivery->SetGroupCompositionDeadline($_POST['groupCompositionDeadline_year'] . "-" . $_POST['groupCompositionDeadline_month'] . "-" . $_POST['groupCompositionDeadline_day'] . " " . $_POST['groupCompositionDeadline_hour'] . ":" . $_POST['groupCompositionDeadline_minute'] . ":00");   
                $delivery->Update();
                $update = true;
                successReport("La date de composition des groupes de rendu " . $delivery->GetName() . " a &eacute;t&eacute; mise &agrave; jour.");
                PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Mise &agrave; jour [date groupe] rendu " . $subject->GetName() . "-" . $work->GetName() . "-" . $delivery->GetName());
            }
            catch(Exception $ex)
            {
                errorReport($ex->getMessage());
                PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec mise &agrave; jour [date groupe] rendu");
            }
        }
        else
        {
            errorReport("Tentative d'utilisation d'une date de composition des groupes non valide. Format valide: JJ/MM/AAAA HH:MM.");
            PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Echec mise &agrave; jour [date groupe] rendu " . $subject->GetName() . "-" . $work->GetName() . "-" . $delivery->GetName() . ": format date invalide");
        }
    }
    
    if(isset($_POST['deadline_day']) && isset($_POST['deadline_month']) && isset($_POST['deadline_year'])
        && isset($_POST['deadline_hour']) && isset($_POST['deadline_minute']))
    {
        if(checkdate($_POST['deadline_month'], $_POST['deadline_day'], $_POST['deadline_year'])
            && preg_match("#^[0-9]{2}$#", $_POST['deadline_hour']) && preg_match("#^[0-9]{2}$#", $_POST['deadline_minute']))
        {
            try
            {
                $delivery->SetDeadline($_POST['deadline_year'] . "-" . $_POST['deadline_month'] . "-" . $_POST['deadline_day']  . " " . $_POST['deadline_hour'] . ":" .$_POST['deadline_minute'] . ":00");     
                $delivery->Update();
                $update = true;
                successReport("La date de rendu limite du rendu " . $delivery->GetName() . " a &eacute;t&eacute; mise &agrave; jour.");
                PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Mise &agrave; jour [date rendu] rendu " . $subject->GetName() . "-" . $work->GetName() . "-" . $delivery->GetName());
            }
            catch(Exception $ex)
            {
                errorReport($ex->getMessage());
                PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec mise &agrave; jour [date rendu] rendu");
            }
        }
        else
        {
            PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Echec mise &agrave; jour [date rendu] rendu " . $subject->GetName() . "-" . $work->GetName() . "-" . $delivery->GetName() . ": format date invalide");
            errorReport("Tentative d'utilisation d'une date de rendu non valide. Format valide: JJ/MM/AAAA HH:MM.");    
        }
    }
    
    if($update)
    {
        $teachers = $subject->GetTeachers();
        PWHEvent::Notify($teachers, TEACHER_TYPE, "Les dates du rendu " . $subject->GetName() . "-" . $work->GetName() . "-" . $delivery->GetName() . " ont &eacute;t&eacute; modifi&eacute;es");
           
        if($work->IsPublished())
        {
            $groups = $delivery->GetGroups();
            foreach($groups as $group)
            {
                $students = $group->GetStudents();
                PWHEvent::Notify($students, STUDENT_TYPE, "Les dates du travail " . $subject->GetName() . "-" . $work->GetName() . " ont &eacute;t&eacute; modifi&eacute;es");
            }
        }
    }
   
?>

<fieldset>
	<legend>configuration de <?php echo mb_strtolower($delivery->GetName()); ?></legend>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/delivery_settings.html', 800, 600);");
        
	    displayErrorReport();
	    displaySuccessReport();
	?>
	<h4>Rendu de <?php echo $delivery->GetName(); ?> pour <?php echo $teacher->GetLastName() . " " . $teacher->GetFirstName(); ?></h4>
	<div class="section">
		<form method="post">
            <?php
                if(!$work->IsSimple() && $work->GetGroupMax() > 1)
                { ?>
            <div class="input">
		        <label for="groupCompositionDeadline">Groupes:</label>
		        <?php 
		            $groupCompositionDeadline = new PWHDateSelector();
		            echo $groupCompositionDeadline->Html("groupCompositionDeadline", $delivery->GetGroupCompositionDeadline());
		        ?>
	        </div>
	        <?php } ?>
	        <div class="input">
		        <label for="deadline">Rendu:</label>
	            <?php 
		            $deadline = new PWHDateSelector();
		            echo $deadline->Html("deadline", $delivery->GetDeadline());
		        ?>
	        </div>
	        <input class="next_form" type="submit" name="apply" value="Appliquer !"/>
        </form>
    </div>
</fieldset>


