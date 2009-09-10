<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page list_subjects");
    previousPage('teacher_home');
    $failed = false;
      
    // Retrieves the list of all subjects or the concerned subjects only
    $toggle = "see=more";
    $link = "[+] Voir toutes les mati&egrave;res";
    if(isset($_GET['see']))
    {
        if($_GET['see'] == "more")
        {
            try
            {
                $subjects = PWHEntity::ListAll('PWHSubject');
                usort($subjects, "entity_comparator");
                $toggle = "see=less";
                $link = "[-] Voir uniquement les mati&egrave;res qui me concernent";
            }
            catch(Exception $ex)
            {
                $failed = true;
                errorReport($ex->getMessage());
            }
        }
        else if($_GET['see'] == "less")
        {
            try
            {
                $teacher = new PWHTeacher();
                $teacher->Read($_SESSION['id']);
                $subjects = $teacher->GetSubjects();
                usort($subjects, "entity_comparator");
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
    }
    else
    {
        $failed = true;
    }
   
    if(!$failed)
    {
        // [FORM] Delete the specified subject 
        if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['subject_id']))
        {
            try 
            {
                $subject = new PWHSubject();
                $subject->Read($_GET['subject_id']);
                
                if($subject->TeacherExists($_SESSION['id']))
                {
                    if(!$subject->HasWorks())
                    {
                        $subject->Delete();
                        $teachers = $subject->GetTeachers();
                        PWHEvent::Notify($teachers, TEACHER_TYPE, "La mati&egrave;re " . $subject->GetName() . " a &eacute;t&eacute; supprim&eacute;e");
                        PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Suppression mati&egrave;re " . $subject->GetName());
                        successReport("La mati&egrave;re " . $subject->GetName() . " a &eacute;t&eacute; supprim&eacute;e.");
                    }
                    else
                    {
                        PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Echec suppression mati&egrave;re " . $subject->GetName() . ": travaux existants");
                        errorReport("La mati&egrave;re ne peut pas &ecirc;tre supprim&eacute;e car elle contient des travaux.");
                    }
                }
                else
                {
                    PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Echec suppression mati&egrave;re " . $subject->GetName() . ": non responsable");
                    errorReport("Echec de suppression de la mati&egrave;re " . $subject->GetName() . " : vous n'appartenez pas aux enseignants responsables de cette mati&egrave;re.");
                }
            }
            catch(Exception $ex)
            {
                errorReport($ex->getMessage());
                PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec suppression mati&egrave;re");
            }
        }
    }
    
    if($failed)
    {
        errorReport("Impossible d'afficher la page demand&eacute;e.");
    }
?>
<fieldset>
	<legend>gestion des mati&egrave;res</legend>
	<?php
        $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/list_subjects.html', 800, 550);");
        
	    displayErrorReport();
	    displaySuccessReport();
	    
	    if(!$failed)
	    {
	?>
	<h4>Cr&eacute;ation de mati&egrave;res</h4>
	<div class="section">
	    <div class="list">
	        <ul>
	            <li><a href="index.php?page=teacher_create_subject_name"><img src="img/book_add.png"/>Cr&eacute;er une nouvelle mati&egrave;re</a></li>
	        </ul>
	    </div>
    </div>
    <h4>Liste des mati&egrave;res disponibles</h4>
    <div class="section">
        <table class="colored_table underlined_table">
            <tr>
	            <th>Nom</th>
	            <th>Configurer</th>
	            <th>Supprimer</th>
            </tr>
            <?php
            if(count($subjects) == 0)
            { ?>
            <tr><td colspan="3">Il n'y a aucune mati&egrave;re disponible</td></tr>
            <tr><td colspan="3"><a href="index.php?page=teacher_list_subjects&amp;<?php echo $toggle; ?>"><?php echo $link; ?></td></tr>
        <?php } 
            else
            { 
                foreach($subjects as $subject)
                { 
                    ?>  
                    <tr <?php if(!$subject->IsConfigured()) { echo 'class="unconfigured_line"'; } ?>>
	                    <td>
	                        <a href="index.php?page=teacher_list_works&amp;subject_id=<?php echo $subject->GetID(); ?>">
	                            <img src="<?php echo IMG_PATH() ?>bullet_go.png"/><?php echo $subject->GetName() . " - " . $subject->GetPromotion()->GetName(); ?>
	                        </a>
	                    </td>
	                    <td>
	                        <a href="index.php?page=teacher_subject_settings_name&amp;subject_id=<?php echo $subject->GetID(); ?>">
	                            <img src="<?php echo IMG_PATH() ?>bullet_wrench.png"/>
	                        </a>
	                    </td>
	                    <td>
	                        <?php
	                        if($subject->TeacherExists($_SESSION['id']))
                            { ?>
                            <a href="javascript:UserConfirmation('<?php echo $_GET['see']; ?>', '<?php echo $subject->GetID(); ?>');"><img src="<?php echo IMG_PATH() ?>cross.png"/>
                            </a>
                            <?php }
                            else
                            { ?>
                                <a><img src="<?php echo IMG_PATH() ?>cross_lock.png"/></a>
                            <?php } ?>
	                    </td>
                    </tr>
            <?php } ?>
                    <tr><td colspan="3"><a href="index.php?page=teacher_list_subjects&amp;<?php echo $toggle; ?>"><?php echo $link; ?></td></tr>
       <?php } ?>
            
        </table>
    </div>
    <?php } ?>
</fieldset>
<script type="text/javascript">
<!--
    function UserConfirmation(see, subject_id)
    {
        if(confirm('Voulez-vous vraiment continuer ?'))
        {
            window.location = "index.php?page=teacher_list_subjects&see=" + see + "&action=delete&subject_id=" + subject_id;
        }
    }
//-->
</script>
