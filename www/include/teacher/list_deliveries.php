<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page list_deliveries");
    
    previousPage('teacher_list_works');
    addPreviousPageParameter('subject_id', $_GET['subject_id']);
    addPreviousPageParameter('work_id', $_GET['work_id']);
    
    if(isset($_GET['previous'])) {
        previousPage($_GET['previous']);
    }
	
	if(isset($_GET['group_id'])) {    
    	addPreviousPageParameter('group_id', $_GET['group_id']);
    	$groupid = $_GET['group_id'];
    }

    
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
        if(isset($_GET['action']) && isset($_GET['delivery_id']))
        {
            $delivery = new PWHDelivery();
            $delivery->Read($_GET['delivery_id']);
                    
            if($_GET['action'] == 'delete')
            {
                if($_SESSION['id'] == $work->GetOwnerID() || $_SESSION['id'] == $delivery->GetOwnerID())
                {
                    try
                    {
                        $delivery->Delete();
                        $work->RemoveDeliveries(array($_GET['delivery_id']));
                        $work->Update();
                        $groups = $delivery->GetGroups();
                        foreach($groups as $group)
                        {
                            $students = $group->GetStudents();
                            PWHEvent::Notify($students, STUDENT_TYPE, "Le travail " . $subject->GetName() . "-" . $work->GetName() . " a &eacute;t&eacute; supprim&eacute;");
                        }
                        $teachers = $subject->GetTeachers();
                        PWHEvent::Notify($teachers, TEACHER_TYPE, "Le rendu " . $subject->GetName() . "-" . $work->GetName() . "-" . $delivery->GetName() . " a &eacute;t&eacute; supprim&eacute;");
                        PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Suppression rendu " . $subject->GetName() . "-" . $work->GetName());
                        successReport("Le rendu " . $delivery->GetName() . " a &eacute;t&eacute; supprim&eacute; du travail " . $work->GetName() . ".");
                    }
                    catch(Exception $ex)
                    {
                        PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec suppression rendu");
                        errorReport($ex->getMessage());
                    }
                }
                else
                {
                    PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Echec suppression rendu " . $subject->GetName() . "-" . $work->GetName() . ": non propri&eacute;taire rendu ni travail");
                    errorReport("Echec de suppression du rendu " . $delivery->GetName() . " : vous n'&ecirc;tes pas le propri&eacute;taire de ce rendu ni propri&eacute;taire du travail.");
                }
            }
        }
        
        $deliveries = $work->GetDeliveries();
        $files = $work->GetFiles();
        $dateTranslator = new PWHDateTranslator();
        $workName = mb_strtolower($work->GetName());
    }
    
    if($failed)
    {
        errorReport("Impossible d'afficher la page demand&eacute;e.");
    }
?>
<section>
	<h2>Rendus du travail : <?php echo $workName; ?></h2>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/list_deliveries.html', 800, 600);");
        
	    displayErrorReport();
	    displaySuccessReport();
	    
	    if(!$failed)
	    {
    ?>
    <h4>R&eacute;capitulatif</h4>
	<div class="section">
        <table class="summary">
            <tr>
                <td>Mati&egrave;re</td>
                <td><?php echo $subject->GetName(); ?></td>
            </tr>
            <tr>
                <td>Travail</td>
                <td><?php echo $work->GetName(); ?></td>
            </tr>
            <tr>
                <td>Membre minimum</td>
                <td><?php echo $work->GetGroupMin(); ?></td>
            </tr>
            <tr>
                <td>Membre maximum</td>
                <td><?php echo $work->GetGroupMax(); ?></td>
            </tr>
            <tr>
                <td>Site web du sujet</td>
                <td>
                    <?php
                        if($work->GetLink() != "")
                        { ?>
                    <a href="<?php echo $work->GetLink(); ?>"><img src="img/world.png"/><?php echo $work->GetLink(); ?></a>
                    <?php } 
                        else 
                        {
                            echo "-";
                        }
                    ?>  
                </td>
            </tr>
            <tr>
                <td>Charge de travail</td>
                <td>
                    <?php 
                        if($work->GetLevel() > 0)
                        {
                            echo $work->GetLevel();
                        }
                        else
                        {
                            echo "-";
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <td>Tol&eacute;rance</td>
                <td>
                    <?php
                        echo $work->GetExtraTime();
                        if($work->GetExtraTime() > 1)
                        {
                            echo " jours";
                        }
                        else
                        {
                            echo " jour";
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <td>Taille</td>
                <td>
                    <?php 
                        if($work->GetSize() > 0)
                        {
                            echo $work->GetSize() . " Mo";
                        }
                        else
                        {
                            echo "-";
                        }
                    ?>
                </td>
            </tr>
            <?php
                foreach($files as $name=>$format)
                { ?>
                <tr>
                    <td>Fichier [<?php echo $name; ?>]</td>
                    <td>Format <?php echo PWHMetaType::GetName($format); ?></td>
                </tr>          
          <?php } ?>
        </table>
    </div>
    <?php
        if(count($deliveries) > 0)
        { ?>
    <h4>Configuration</h4>
    <div class="section">
        <div class="list">
            <ul>
				<li>
					<a href="index.php?page=teacher_work_settings&amp;subject_id=<?php echo $subject->GetID() ?>&amp;work_id=<?php echo $work->GetID() ?>&previous=teacher_list_deliveries<?php echo isset($groupid)?"&group_id=$groupid":""; ?>">
						<img src="<?php echo IMG_PATH() ?>bullet_wrench.png"/>Configuration du travail (nom, taille, ...)
					</a>
				</li>
                <li>
                	<a href="index.php?page=teacher_deliveries_settings&amp;subject_id=<?php echo $subject->GetID(); ?>&amp;work_id=<?php echo $work->GetID(); ?>">
                		<img src="img/bullet_wrench.png"/>Configuration des dates des rendus
                	</a>
                </li>
            </ul>
        </div>
    </div>
    <h4>Dates</h4>
	<div class="section">
        <table class="colored_table underlined_table">
            <tr>
                <th>Responsable</th>
                <?php 
                    if(!$work->IsSimple() && $work->GetGroupMax() > 1)
                    { ?>
                <th>Date de composition des groupes</th>
                <?php } ?>
                <th>Date de rendu</th>
            </tr>
            <?php 
            foreach($deliveries as $delivery)
            { ?>
            <tr>
                <td><?php 
                        $teacher = new PWHTeacher();
                        $teacher->Read($delivery->GetOwnerID());
                        echo $teacher->GetLastName() . " " . $teacher->GetFirstName(); ?></td>
                <?php
                    if(!$work->IsSimple() && $work->GetGroupMax() > 1)
                    { ?>
                <td><?php echo $dateTranslator->Html($delivery->GetGroupCompositionDeadline(), PWHDateTranslator::DATE_AND_TIME); ?></td>
                <?php } ?>
                <td><?php echo $dateTranslator->Html($delivery->GetDeadline(), PWHDateTranslator::DATE_AND_TIME); ?></td>
            </tr>
            <?php } ?>
        </table>
   </div>
   <?php } ?>
    <h4>Liste des rendus</h4>
	<div class="section">   
        <table class="colored_table underlined_table">
            <tr>
                <th>Nom des rendus</th>
	            <th>Examiner</th>
	            <th>Configurer</th>
                <th>T&eacute;l&eacute;charger</th>
	            <th>Supprimer</th>
            </tr>
            <?php
	            if(count($deliveries) == 0)
                { ?>
                    <tr>
                        <td colspan="5">Il n'y a aucun rendu enregistr&eacute; dans le travail <?php echo $work->GetName(); ?></td>
                    </tr>
	      <?php }
	            else
	            { 
                foreach($deliveries as $delivery)
                {                     
                    ?>
                    <tr <?php if(!$delivery->IsConfigured()) { echo 'class="unconfigured_line"'; } ?>>
                        <td>
	                        <?php echo $workName; ?> / <?php echo $delivery->GetName() ?>
	                    </td>
	                    <td>
	                        <a href="index.php?page=teacher_display_board&amp;previous=teacher_list_deliveries&amp;subject_id=<?php echo $_GET['subject_id']; ?>&amp;work_id=<?php echo $work->GetID(); ?>&amp;delivery_id=<?php echo $delivery->GetID(); ?>&amp;index=A">
	                            <img src="<?php echo IMG_PATH() ?>zoom_in.png"/>
	                        </a>
	                    </td>
	                    <td>
	                        <a href="index.php?page=teacher_delivery_settings&amp;subject_id=<?php echo $subject->GetID(); ?>&amp;work_id=<?php echo $work->GetID(); ?>&amp;delivery_id=<?php echo $delivery->GetID(); ?>">
	                            <img src="<?php echo IMG_PATH() ?>bullet_wrench.png"/>
	                        </a>
	                    </td>
	                    <td>
	                        <a href="downloads/index.php?type=delivery&amp;subject_id=<?php echo $subject->GetID(); ?>&amp;work_id=<?php echo $work->GetID(); ?>&amp;delivery_id=<?php echo $delivery->GetID(); ?>">
	                            <img src="img/package_go.png"/>
	                        </a>
	                    </td>
	                    <td>
	                        <?php
	                        if($_SESSION['id'] == $work->GetOwnerID() || $_SESSION['id'] == $delivery->GetOwnerID())
	                        { ?>
	                        <a href="javascript:UserConfirmation('<?php echo $subject->GetID(); ?>', '<?php echo $work->GetID(); ?>', '<?php echo $delivery->GetID(); ?>');">
	                            <img src="<?php echo IMG_PATH() ?>cross.png"/>
	                        </a>
	                        <?php }
	                        else
	                        { ?>
	                            <a><img src="<?php echo IMG_PATH() ?>cross_lock.png"/></a>
	                        <?php } ?>
	                    </td>
                    </tr>
            <?php } 
            } ?>
        </table>
    </div>
    <?php } ?>
</section>
<script type="text/javascript">
<!--
    function UserConfirmation(subject_id, work_id, delivery_id)
    {
        if(confirm('****** ATTENTION ******\n\nLa suppression d\'un rendu est définitive ! \n\nEtes vous sur de vouloir continuer ? \n\nCliquez sur \'OK\' pour confirmer votre choix, ou sur \'Annuler\' pour ne pas poursuivre.'))
        {
            window.location = "index.php?page=teacher_list_deliveries&action=delete&subject_id=" + subject_id + "&work_id=" + work_id + "&delivery_id=" + delivery_id;
        }
    }
//-->
</script>
