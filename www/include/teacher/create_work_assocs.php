<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page create_work_assocs");
    
    previousPage('teacher_create_work_files');
    addPreviousPageParameter('subject_id', $_GET['subject_id']);
        
    // Retrieves the concerned subject
    if(isset($_GET['subject_id']))
    {     
        try
        {
            $subject = new PWHSubject(null);
            $subject->Read($_GET['subject_id']);
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());
        }
    }
    
    // Retrieves the list of concerned groups
    try
    {
        $groups = $subject->GetGroups();
    }
    catch(Exception $ex)
    {
        errorReport($ex->getMessage());
        $groups = array();
    }
    
    // Retrieves the list of concerned teachers
    try
    {
        $teachers = $subject->GetTeachers();
        usort($teachers, "person_comparator");
    }
    catch(Exception $ex)
    {
        errorReport($ex->getMessage());
        $teachers = array();
    }
    
    // Sets file configurations
    if(isset($_POST['numberFiles']))
    {        
        for($i=1; $i<=$_POST['numberFiles']; $i++)
        {
            if($_POST['fileName' . $i] == "")
            {
                redirect("index.php?page=teacher_create_work_files&amp;subject_id=" . $subject->GetID() . "&amp;action=alert_empty");
            }
        }
        
        $_SESSION['number_files'] = $_POST['numberFiles'];       
        $_SESSION['files'] = array();
        for($i=1; $i<=$_POST['numberFiles']; $i++)
        {
            $_SESSION['files'][stripslashes($_POST['fileName' . $i])] = $_POST['fileFormat' . $i];
        }
    }
    
    // Creates the work   
    if(isset($_GET['action']) && $_GET['action'] == 'create')
    {
        try
        {
            $work = new PWHWork();
            $work->SetName($_SESSION['work_name']);
            $work->SetExtraTime($_SESSION['extra_time']);
            $work->SetSize($_SESSION['size']);
            $work->SetGroupMin($_SESSION['group_min']);
            $work->SetGroupMax($_SESSION['group_max']);
            if(substr($_SESSION['link'], 0, 7) == "http://" || $_SESSION['link'] == "")
            {
                $work->SetLink($_SESSION['link']);
            }
            else if($_SESSION['link'] != "")
            {
                $work->SetLink("http://" . $_SESSION['link']);
            }
            $work->SetLevel($_SESSION['level']);
            $work->AddFiles($_SESSION['files']);
            if($_SESSION['simple'])
            {
                $work->SetSimple(true);
            }
            $work->SetSubjectID($subject->GetID());
            $teachers = $subject->GetTeachers();
            if($subject->TeacherExists($_SESSION['id']))
            {      
                $work->SetOwnerID($_SESSION['id']);
            }
            else
            {
                $work->SetOwnerID($teachers[0]);
            }
            $work->Create(true);
            $work->CreateDirectory();       
            
            PWHEvent::Notify($teachers, TEACHER_TYPE, "Le travail " . $subject->GetName() . "-" . $work->GetName() . " a &eacute;t&eacute; cr&eacute;&eacute;e");
        
            $subject->AddWorks(array($work->GetID()));
            $subject->Update();
            
            foreach($groups as $group)
            {
                $teacher = new PWHTeacher();
                $teacher->Read($_POST['teacher' . $group->GetID()]);
                $delivery = new PWHDelivery();
                $delivery->SetName($group->GetName() . "-" . $teacher->GetLastName());
                $delivery->SetOwnerID($teacher->GetID());
                $delivery->SetWorkID($work->GetID());
                $delivery->AddGroups(array($group->GetID()));
                
                $delivery->Create(true);
                $delivery->CreateDirectory();
                PWHEvent::Notify(array($teacher), TEACHER_TYPE, "Vous avez &eacute;t&eacute; design&eacute; responsable du rendu " . $subject->GetName() . "-" . $work->GetName() . "-" . $delivery->GetName());
                $work->AddDeliveries(array($delivery->GetID()));
            }
            $work->Update();
            
            // Destroys session variables
            unset($_SESSION['work_name']);
            unset($_SESSION['extra_time']);
            unset($_SESSION['size']);
            unset($_SESSION['files']);
            unset($_SESSION['number_files']);
            unset($_SESSION['group_min']);
            unset($_SESSION['group_max']);
            unset($_SESSION['link']);
            unset($_SESSION['level']);
            
            PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Cr&eacute;ation travail " . $subject->GetName() . "-" . $work->GetName());
            redirect("index.php?page=teacher_deliveries_settings&amp;subject_id=" . $subject->GetID() . "&amp;work_id=" . $work->GetID());
        }
        catch(Exception $ex)
        {
             errorReport($ex->getMessage());
             PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec cr&eacute;ation travail");
        }    
     }
     
    // Creates a new memo for the user   
    $memo = new PWHWorkCreationMemo();
    $memo->SetName($_SESSION['work_name']);
    $memo->SetExtraTime($_SESSION['extra_time']);
    $memo->SetSize($_SESSION['size']);
    $memo->SetGroupMin($_SESSION['group_min']);
    $memo->SetGroupMax($_SESSION['group_max']);
    $memo->SetFiles($_SESSION['files']);
?>

<fieldset>
	<legend>responsables - etape 3/3</legend>
	
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/create_work_assocs.html', 800, 600);");
        
	    displayErrorReport();
	    displaySuccessReport();
	    echo $memo->Html();
	?>
	<h4>Affectation des groupes aux enseignants</h4>
	<div class="section">
		<form method="post" action="index.php?page=teacher_create_work_assocs&amp;subject_id=<?php echo $_GET['subject_id']; ?>&amp;n_assoc=<?php echo $nAssoc; ?>&amp;action=create">
	        <table class="colored_table underlined_table">
		        <tr>
			        <th>Groupe</th>
			        <th>Enseignant</th>
		        </tr>
		        <?php
		            foreach($groups as $group)
		            { ?>
                <tr>
	                <td><?php echo $group->GetName(); ?></td>
	                <td>
		                <select name="teacher<?php echo $group->GetID(); ?>" id="teacher<?php echo $group->GetID(); ?>">
			                 <?php
                                foreach($teachers as $teacher)
                                { ?>
		                        <option value="<?php echo $teacher->GetID(); ?>"><?php echo $teacher->GetLastName() . " " . $teacher->GetFirstName(); ?></option>
		                  <?php } ?>
		                </select>
	                </td>
                </tr>
                <?php } ?>
                <tr class="submit_line">
                    <td></td>
                    <td><input type="submit" name="create" value="Cr&eacute;er !"/></td>
                </tr>
            </table>
	    </form>	    
    </div>
</fieldset>
