<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page deliveries_settings");
    
    previousPage('teacher_list_deliveries');
    addPreviousPageParameter('subject_id', $_GET['subject_id']);
    addPreviousPageParameter('work_id', $_GET['work_id']);
    $failed = false;
    
    if(isset($_GET['subject_id']) && isset($_GET['work_id'])
        && PWHEntity::Valid("PWHSubject", $_GET['subject_id'])
        && PWHEntity::Valid("PWHWork", $_GET['work_id']))
    { 
        try
        {
            $subject = new PWHSubject();
            $subject->Read($_GET['subject_id']);
            $work = new PWHWork();
            $work->Read($_GET['work_id']);
            $deliveries = $work->GetDeliveries();
        }
        catch(Exception $ex)
        {
            $failed = false;
            errorReport($ex->getMessage());
        }
    }
    else
    {
        $failed = false;
    }
    
    if(!$failed)
    {
        if(isset($_GET['action']))
        {
            if($_GET['action'] == 'set_all_date')
            {
                $dateFailed = false;
                foreach($deliveries as $delivery)
                {
                    if(!$work->IsSimple() && $work->GetGroupMax() > 1)
                    {
                        if(checkdate($_POST['groupCompositionDeadline_month'], $_POST['groupCompositionDeadline_day'], $_POST['groupCompositionDeadline_year'])
                            && preg_match("#^[0-9]{2}$#", $_POST['groupCompositionDeadline_hour']) && preg_match("#^[0-9]{2}$#", $_POST['groupCompositionDeadline_minute']))
                        {
                            try
                            {
                                $delivery->SetGroupCompositionDeadline($_POST['groupCompositionDeadline_year'] . "-" . $_POST['groupCompositionDeadline_month'] . "-" . $_POST['groupCompositionDeadline_day'] . " " . $_POST['groupCompositionDeadline_hour'] . ":" . $_POST['groupCompositionDeadline_minute'] . ":00");   
                                $delivery->Update();
                            }
                            catch(Exception $ex)
                            {
                                errorReport($ex->getMessage());
                                PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec mise &agrave; jour [date groupe] rendu");
                                $dateFailed = true;
                                break;
                            }
                        }
                        else
                        {
                            errorReport("Tentative d'utilisation d'une date de composition des groupes non valide. Format valide: JJ/MM/AAAA HH:MM.");
                            PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Echec mise &agrave; jour [date groupe] rendu " . $subject->GetName() . "-" . $work->GetName() . "-" . $delivery->GetName() . ": format date invalide");
                            $dateFailed = true;
                            break;
                        }
                    }
                    
                    if(checkdate($_POST['deadline_month'], $_POST['deadline_day'], $_POST['deadline_year'])
                        && preg_match("#^[0-9]{2}$#", $_POST['deadline_hour']) && preg_match("#^[0-9]{2}$#", $_POST['deadline_minute']))
                    {
                        try
                        {
                            $delivery->SetDeadline($_POST['deadline_year'] . "-" . $_POST['deadline_month'] . "-" . $_POST['deadline_day'] . " " . $_POST['deadline_hour'] . ":" . $_POST['deadline_minute'] . ":00");   
                            $delivery->Update();
                        }
                        catch(Exception $ex)
                        {
                            errorReport($ex->getMessage());
                            PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec mise &agrave; jour [date rendu] rendu");
                            $dateFailed = true;
                            break;
                        }
                    }
                    else
                    {
                        errorReport("Tentative d'utilisation d'une date de rendu non valide. Format valide: JJ/MM/AAAA HH:MM.");
                        PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Echec mise &agrave; jour [date renu] rendu " . $subject->GetName() . "-" . $work->GetName() . "-" . $delivery->GetName() . ": format date invalide");
                        $dateFailed = true;
                        break;
                    }
                    
                    $teachers = $subject->GetTeachers();
                    PWHEvent::Notify($teachers, TEACHER_TYPE, "Les dates du rendu " . $subject->GetName() . "-" . $work->GetName() . "-" . $delivery->GetName() . " ont &eacute;t&eacute; modifi&eacute;es");
                     PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Mise &agrave; jour [dates] rendu " . $subject->GetName() . "-" . $work->GetName() . "-" . $delivery->GetName());
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
                if(!$dateFailed)
                {
                    $work->SetPublished(true);
                    $work->Update();
                    foreach($deliveries as $delivery)
                    {
                        $groups = $delivery->GetGroups();
                        foreach($groups as $group)
                        {
                            $students = $group->GetStudents();
                            PWHEvent::Notify($students, STUDENT_TYPE, "Le travail " . $subject->GetName() . "-" . $work->GetName() . " a &eacute;t&eacute; ajout&eacute;");
                        }       
                    }
                    $teachers = $subject->GetTeachers();
                    PWHEvent::Notify($teachers, TEACHER_TYPE, "Le travail " . $subject->GetName() . "-" . $work->GetName() . " a &eacute;t&eacute; publi&eacute; aupr&egrave;s des &eacute;tudiants");
                    PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Publication travail " . $subject->GetName() . "-" . $work->GetName());
                    redirect("index.php?page=teacher_list_deliveries&amp;subject_id=" . $subject->GetID() . "&amp;work_id=" . $work->GetID());
                }
                else if(!$failed)
                {
                    redirect("index.php?page=teacher_list_deliveries&amp;subject_id=" . $subject->GetID() . "&amp;work_id=" . $work->GetID());
                }
            }
            else if($_GET['action'] == 'set_date')
            {
               $dateFailed = false;
               foreach($deliveries as $delivery)
               {
                    if(!$work->IsSimple() && $work->GetGroupMax() > 1)
                    {
                        if(checkdate($_POST['groupCompositionDeadline' . $delivery->GetID() . '_month'], $_POST['groupCompositionDeadline' . $delivery->GetID() . '_day'], $_POST['groupCompositionDeadline' . $delivery->GetID() . '_year'])
                            && preg_match("#^[0-9]{2}$#", $_POST['groupCompositionDeadline' . $delivery->GetID() . '_hour']) && preg_match("#^[0-9]{2}$#", $_POST['groupCompositionDeadline' . $delivery->GetID() . '_minute']))
                        {
                            try
                            {
                                $delivery->SetGroupCompositionDeadline($_POST['groupCompositionDeadline' . $delivery->GetID() . '_year'] . "-" . $_POST['groupCompositionDeadline' . $delivery->GetID() . '_month'] . "-" . $_POST['groupCompositionDeadline' . $delivery->GetID() . '_day'] . " " . $_POST['groupCompositionDeadline' . $delivery->GetID() . '_hour'] . ":" . $_POST['groupCompositionDeadline' . $delivery->GetID() . '_minute'] . ":00");   
                                $delivery->Update();
                            }
                            catch(Exception $ex)
                            {
                                errorReport($ex->getMessage());
                                PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec mise &agrave; jour [date groupe] rendu");
                                $dateFailed = true;
                            }
                        }
                        else
                        {
                            errorReport("Tentative d'utilisation d'une date de composition des groupes non valide pour le rendu " . $delivery->GetName() . ". Format valide: JJ/MM/AAAA HH:MM.");
                            PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Echec mise &agrave; jour [date groupe] rendu " . $subject->GetName() . "-" . $work->GetName() . "-" . $delivery->GetName() . ": format date invalide");
                            $dateFailed = true;
                        }
                    }
                    
                    if(checkdate($_POST['deadline' . $delivery->GetID() . '_month'], $_POST['deadline' . $delivery->GetID() . '_day'], $_POST['deadline' . $delivery->GetID() . '_year'])
                        && preg_match("#^[0-9]{2}$#", $_POST['deadline' . $delivery->GetID() . '_hour']) && preg_match("#^[0-9]{2}$#", $_POST['deadline' . $delivery->GetID() . '_minute']))
                    {
                        try
                        {
                            $delivery->SetDeadline($_POST['deadline' . $delivery->GetID() . '_year'] . "-" . $_POST['deadline' . $delivery->GetID() . '_month'] . "-" . $_POST['deadline' . $delivery->GetID() . '_day'] . " " . $_POST['deadline' . $delivery->GetID() . '_hour'] . ":" . $_POST['deadline' . $delivery->GetID() . '_minute'] . ":00");   
                            $delivery->Update();
                        }
                        catch(Exception $ex)
                        {
                            errorReport($ex->getMessage());
                            PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec mise &agrave; jour [date rendu] rendu");
                            $dateFailed = true;
                        }
                    }
                    else
                    {
                        errorReport("Tentative d'utilisation d'une date de rendu non valide pour le rendu " . $delivery->GetName() . ". Format valide: JJ/MM/AAAA HH:MM.");
                        PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Echec mise &agrave; jour [date rendu] rendu " . $subject->GetName() . "-" . $work->GetName() . "-" . $delivery->GetName() . ": format date invalide");
                        $dateFailed = true;
                    }
                    
                    $teachers = $subject->GetTeachers();
                    PWHEvent::Notify($teachers, TEACHER_TYPE, "Les dates du rendu " . $subject->GetName() . "-" . $work->GetName() . "-" . $delivery->GetName() . " ont &eacute;t&eacute; modifi&eacute;es");
                     PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Mise &agrave; jour [dates] rendu " . $subject->GetName() . "-" . $work->GetName() . "-" . $delivery->GetName());
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
               if(!$dateFailed)
               {
                    $work->SetPublished(true);
                    $work->Update();
                    foreach($deliveries as $delivery)
                    {
                        $groups = $delivery->GetGroups();
                        foreach($groups as $group)
                        {
                            $students = $group->GetStudents();
                            PWHEvent::Notify($students, STUDENT_TYPE, "Le travail " . $subject->GetName() . "-" . $work->GetName() . " a &eacute;t&eacute; ajout&eacute;");
                        }       
                    }
                    $teachers = $subject->GetTeachers();
                    PWHEvent::Notify($teachers, TEACHER_TYPE, "Le travail " . $subject->GetName() . "-" . $work->GetName() . " a &eacute;t&eacute; publi&eacute; aupr&egrave;s des &eacute;tudiants");
                    PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Publication travail " . $subject->GetName() . "-" . $work->GetName());
                    redirect("index.php?page=teacher_list_deliveries&amp;subject_id=" . $subject->GetID() . "&amp;work_id=" . $work->GetID());
               }
               else if(!$failed)
               {
                    redirect("index.php?page=teacher_list_deliveries&amp;subject_id=" . $subject->GetID() . "&amp;work_id=" . $work->GetID());
               }
            }
        }
        
        $link = "";
        $selector = "";
        $same = true;
        $i = 0;
        while($i < count($deliveries) - 1)
        {
            if($work->IsSimple() || $work->GetGroupMax == 1)
            {
                if($deliveries[$i]->GetDeadline() != $deliveries[$i+1]->GetDeadline())
                {    
                    $same = false;
                    break;
                }
            }
            else
            {
                if($deliveries[$i]->GetGroupCompositionDeadline() != $deliveries[$i+1]->GetGroupCompositionDeadline() || $deliveries[$i]->GetDeadline() != $deliveries[$i+1]->GetDeadline())
                {    
                    $same = false;
                    break;
                }
            }
            $i++;
        }
        if($same)
        {
            $link = '<a class="next_form" id="toggle" href="javascript:toggle();"><img src="img/zoom_in.png"/>Configuration avanc&eacute;e</a>';
            $selector = "true";
        }
        else
        {
            $link = '<a class="next_form" id="toggle" href="javascript:toggle();"><img src="img/zoom_out.png"/>Configuration g&eacute;n&eacute;rale</a>';
            $selector = "false";
        }
    }
    
    if($failed)
    {
        errorReport("Impossible d'afficher la page demand&eacute;e.");
    }
?>

<section>
	<h2>configuration des rendus</h2>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/deliveries_settings.html', 800, 600);");
        
	    displayErrorReport();
	    displaySuccessReport();
	?>
	<?php if(!$failed && count($deliveries) > 0)
	{ ?>
	<div class="section">
	    <?php echo $link; ?>
	</div> 
	<div id="all">
	    <form method="post" action="index.php?page=teacher_deliveries_settings&amp;action=set_all_date&amp;subject_id=<?php echo $_GET['subject_id']; ?>&amp;work_id=<?php echo $_GET['work_id']; ?>">
	        <h4>Affection de dates communes &agrave; tous les rendus</h4>
	        <div class="section">
            <?php if(!$work->IsSimple() && $work->GetGroupMax() > 1)
            { ?>
                <div class="input">
                    <label>Groupes:</label>
                    <?php 
                        $groupCompositionDeadline = new PWHDateSelector();
                        if($same)
                        {
                            echo $groupCompositionDeadline->Html("groupCompositionDeadline", $deliveries[0]->GetGroupCompositionDeadline());
                        }
                        else
                        {
                            echo $groupCompositionDeadline->Html("groupCompositionDeadline", "");
                        }
                    ?>
                </div>
        <?php } ?>
                <div class="input">
                    <label>Rendu:</label>
                    <?php 
                        $deadline = new PWHDateSelector();
                        if($same)
                        {
                            echo $deadline->Html("deadline", $deliveries[0]->GetDeadline());
                        }
                        else
                        {
                            echo $deadline->Html("deadline", "");
                        }
                    ?>
                </div>
            </div>
            <div class="section">
                <input class="next_form" type="submit" name="apply" value="Appliquer !"/>
            </div>
	    </form>
	</div>
	<div id="specific">
	    <form method="post" action="index.php?page=teacher_deliveries_settings&amp;action=set_date&amp;subject_id=<?php echo $_GET['subject_id']; ?>&amp;work_id=<?php echo $_GET['work_id']; ?>">
            <?php
                foreach($deliveries as $delivery)
                { 
                    $teacher = new PWHTeacher();
                    $teacher->Read($delivery->GetOwnerID());
                    ?>
                    <h4><?php echo "Rendu " . $delivery->GetName(); ?> pour <?php echo $teacher->GetLastName() . " " . $teacher->GetFirstName(); ?></h4>
                    <div class="section">
                    <?php if(!$work->IsSimple() && $work->GetGroupMax() > 1)
                    { ?>
                        <div class="input">
		                    <label>Groupes:</label>
		                    <?php 
		                        $groupCompositionDeadline = new PWHDateSelector();
		                        echo $groupCompositionDeadline->Html("groupCompositionDeadline" . $delivery->GetID(), $delivery->GetGroupCompositionDeadline());
		                    ?>
	                    </div>
	          <?php } ?>
	                    <div class="input">
		                    <label>Rendu:</label>
	                        <?php 
		                        $deadline = new PWHDateSelector();
		                        echo $deadline->Html("deadline" . $delivery->GetID(), $delivery->GetDeadline());
		                    ?>
	                    </div>
	                </div>
	    <?php } ?>
	            <div class="section">
	                <input class="next_form" type="submit" name="apply" value="Appliquer !"/>
	            </div>
        </form>
    </div>
<?php } ?>
</section>
<script type="text/javascript">
<!--
    var selector = <?php echo $selector; ?>;
    
    function toggle()
    {
        
        var link = document.getElementById("toggle");
        var divAll = document.getElementById("all");
        var divSpecific = document.getElementById("specific");
        
        if(selector)
        {
            divSpecific.style.display = "";
            divAll.style.display = "none";     
            selector = false;       
            link.innerHTML = '<img src="img/zoom_out.png"/>Configuration g&eacute;n&eacute;rale';
        }
        else
        {
            divSpecific.style.display = "none";
            divAll.style.display = "";
            selector = true;
            link.innerHTML = '<img src="img/zoom_in.png"/>Configuration avanc&eacute;e';
        }
    }
    
    <?php if($same)
    { ?>
        var divSpecific = document.getElementById("specific");
        divSpecific.style.display = "none";
    <?php }
    else
    { ?>
        var divAll = document.getElementById("all");
        divAll.style.display = "none";
    <?php } ?>
//-->
</script>

