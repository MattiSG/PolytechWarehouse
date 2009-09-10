<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page list_works");
    
    previousPage('teacher_list_subjects');
    addPreviousPageParameter('see', 'less');
    $failed = false;
    $subjectName = "???";
    
    // Retrieves the concerned subject and its works
    if(isset($_GET['subject_id']) && PWHEntity::Valid("PWHSubject", $_GET['subject_id']))
    {
        try
        {
            $subject = new PWHSubject();
            $subject->Read($_GET['subject_id']);
            $teachers = $subject->GetTeachers();
            $groups = $subject->GetGroups();
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
        if(isset($_GET['action']) && isset($_GET['work_id']) && PWHEntity::Valid("PWHWork", $_GET['work_id']))
        {     
            $work = new PWHWork();
            $work->Read($_GET['work_id']);
            
            if($_GET['action'] == 'delete')
            {
                if($_SESSION['id'] == $work->GetOwnerID())
                {
                    try
                    {   
                        $work->Delete();
                        $subject->RemoveWorks(array($_GET['work_id']));
                        $subject->Update();
                        $deliveries = $work->GetDeliveries();
                        foreach($deliveries as $delivery)
                        {
                            $groups = $delivery->GetGroups();
                            foreach($groups as $group)
                            {
                                $students = $group->GetStudents();
                                PWHEvent::Notify($students, STUDENT_TYPE, "Le travail " . $subject->GetName() . "-" . $work->GetName() . " a &eacute;t&eacute; supprim&eacute;");
                            }       
                        }
                        $teachers = $subject->GetTeachers();
                        PWHEvent::Notify($teachers, TEACHER_TYPE, "Le travail " . $subject->GetName() . "-" . $work->GetName() . " a &eacute;t&eacute; supprim&eacute;");
                        PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Suppression travail " . $subject->GetName() . "-" . $work->GetName());
                        successReport("Le travail " . $work->GetName() . " a &eacute;t&eacute; supprim&eacute; de la mati&egrave;re " . $subject->GetName() . ".");    
                    }
                    catch(Exception $ex)
                    {
                        PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec suppression travail");
                        errorReport($ex->getMessage());
                    }
                }
                else
                {
                    PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Echec suppression travail " . $subject->GetName() . "-" . $work->GetName() . ": non propri&eacute;taire");
                    errorReport("Echec de suppression du travail " . $work->GetName() . " : vous n'&ecirc;tes pas le propri&eacute;taire de ce travail.");
                }
            }
            else if($_GET['action'] == 'publish')
            {
                $configured = true;
                $deliveries = $work->GetDeliveries();
                foreach($deliveries as $delivery)
                {
                    if(!$delivery->IsConfigured())
                    {
                        $configured = false;
                    }
                }
                
                if($configured)
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
                    successReport("Le travail " . $work->GetName() . " a &eacute;t&eacute; publi&eacute;.");
                }
                else
                {
                    PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Echec publication travail " . $subject->GetName() . "-" . $work->GetName() . ": rendus non configur&eacute;s");
                    errorReport("Le travail " . $work->GetName() . " ne peut pas &ecirc;tre publi&eacute; car les dates de rendu ne sont pas encore configur&eacute;es.");
                }
            }
        }
        
        $works = $subject->GetWorks();
        $link = "<a class=\"next_form\" href=\"javascript:popup('include/teacher/subject_board.php?subject_id=" . $subject->GetID() . "', 1024, 768);\"><img src=\"img/zoom_in.png\"/>Voir le tableau de bord de la mati&egrave;re</a>";
        $subjectName = mb_strtolower($subject->GetName());
    }
    
    if($failed)
    {
        errorReport("Impossible d'afficher la page demand&eacute;e.");
    }
?>
<fieldset>
	<legend>travaux de <?php echo $subjectName; ?></legend>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/list_works.html', 800, 600);");
        
	    displayErrorReport();
	    displaySuccessReport();
	    
	    if(!$failed)
	    {
    ?>
    <h4>R&eacute;capitulatif</h4>
    <div class="section">
        <table class="summary">
            <tr>
                <td>Responsables</td>
                <td>
                    <?php
                        $strbuf = "";
                        foreach($teachers as $teacher)
                        {
                            $strbuf .= $teacher->GetLastName() . " " . $teacher->GetFirstName() . ", ";
                        }
                        $strbuf = substr($strbuf, 0, strlen($strbuf) - 2);
                        echo $strbuf;
                    ?>
               </td>
            </tr>
            <tr>
                <td>Groupes</td>
                <td>
                    <?php
                        $strbuf = "";
                        foreach($groups as $group)
                        {
                            $strbuf .= $group->GetName() . ", ";
                        }
                        $strbuf = substr($strbuf, 0, strlen($strbuf) - 2);
                        echo $strbuf;
                    ?>
                </td>
            </tr>
        </table>
    </div>
    <h4>Cr&eacute;ation de travaux</h4>
    <div class="section">
	    <div class="list">
	        <ul>
	            <li><a href="index.php?page=teacher_create_work_name_constraints&amp;subject_id=<?php echo $subject->GetID(); ?>">
	            <img src="img/package_add.png"/>Cr&eacute;er un nouveau travail</a></li>
	        </ul>
	    </div>
	</div>
	<h4>Liste des travaux disponibles</h4>
	<div class="section">
        <table class="colored_table underlined_table">
            <tr>
	            <th>Nom</th>
	            <th>Type</th>
	            <th>Configurer</th>
	            <th>Publier</th>
	            <th>T&eacute;l&eacute;charger</th>
	            <th>Supprimer</th>
            </tr>
            <?php
	        if(count($works) == 0)
	        { ?>
	            <tr>
	                <td colspan="6">Il n'y a aucun travail enregistr&eacute; dans la mati&egrave;re <?php echo $subject->GetName(); ?></td>
	            </tr>
	  <?php }
	        else
	        {
                foreach($works as $work)
                { 
                    ?>
                    <tr<?php if(!$work->IsPublished()) { echo ' class="unpublished_line"'; } ?>>
	                    <td>
	                        <a href="index.php?page=teacher_list_deliveries&amp;subject_id=<?php echo $subject->GetID() ?>&amp;work_id=<?php echo $work->GetID() ?>">
	                            <img src="<?php echo IMG_PATH() ?>bullet_go.png"/><?php echo $work->GetName() ?>
	                        </a>
	                    </td>
	                    <td>
	                        <?php
	                            if($work->IsSimple())
	                            {
	                                echo "Express";
	                            }
	                            else
	                            {
	                                echo "Normal";
	                            }
	                        ?>
	                    </td>
	                    <td>
	                        <a href="index.php?page=teacher_work_settings&amp;subject_id=<?php echo $subject->GetID() ?>&amp;work_id=<?php echo $work->GetID() ?>">
	                            <img src="<?php echo IMG_PATH() ?>bullet_wrench.png"/>
	                        </a>
	                    </td>
	                    <td>
	                        <?php
	                            if(!$work->IsPublished())
	                            { ?>
	                        <a href="index.php?page=teacher_list_works&amp;action=publish&amp;subject_id=<?php echo $subject->GetID() ?>&amp;work_id=<?php echo $work->GetID() ?>">
	                            <img src="img/unpublished.png"/>
	                            <?php }
	                            else
	                            { ?>
	                                <a><img src="img/published.png"/></a>
	                            <?php } ?>
	                    </td>
	                    <td>
	                        <a href="downloads/index.php?type=work&amp;subject_id=<?php echo $subject->GetID(); ?>&amp;work_id=<?php echo$work->GetID(); ?>">
	                            <img src="img/package_go.png">
	                        </a>
	                    </td>
	                    <td>
	                        <?php
	                            if($_SESSION['id'] == $work->GetOwnerID())
	                            { ?>
	                        <a href="javascript:UserConfirmation('<?php echo $subject->GetID() ?>', '<?php echo $work->GetID() ?>');">
	                            <img src="<?php echo IMG_PATH() ?>cross.png"/>
	                        </a>
	                        <?php }
	                            else
	                            { ?>
	                                <a><img src="<?php echo IMG_PATH() ?>cross_lock.png"/></a>
	                            <?php } ?>
	                    </td>
                    </tr>
             <? }
             } ?>
        </table>
    </div>
    <div class="section">
	    <?php echo $link; ?>
	</div>
	<?php } ?>
</fieldset>
<script type="text/javascript">
<!--
    function UserConfirmation(subject_id, work_id)
    {
        if(confirm('****** ATTENTION ******\n\nLa suppression d\'un travail est définitive ! \n\nEtes vous sur de vouloir continuer ? \n\nCliquez sur \'OK\' pour confirmer votre choix, ou sur \'Annuler\' pour ne pas poursuivre.'))
        {
            window.location = "index.php?page=teacher_list_works&action=delete&subject_id=" + subject_id + "&work_id=" + work_id;
        }
    }
//-->
</script>
