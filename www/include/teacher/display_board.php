<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page display_board");
    $failed = false;
    $deliveryName = "???";
    
    if(isset($_GET['previous']))
    {
        previousPage($_GET['previous']);
        if($_GET['previous'] == 'teacher_list_group_deliveries')
        {
            addPreviousPageParameter('group_id', $_GET['group_id']);
        }
        addPreviousPageParameter('subject_id', $_GET['subject_id']);
        addPreviousPageParameter('work_id', $_GET['work_id']);
        addPreviousPageParameter('delivery_id', $_GET['delivery_id']);
    }
    
    if(isset($_SESSION['students']))
    {
        unset($_SESSION['students']);
    }
    
    if(isset($_GET['subject_id']) && isset($_GET['work_id']) && isset($_GET['delivery_id']) && isset($_GET['index']) && preg_match("#^[*A-Z]$#", $_GET['index'])
        && PWHEntity::Valid("PWHSubject", $_GET['subject_id'])
        && PWHEntity::Valid("PWHWork", $_GET['work_id'])
        && PWHEntity::Valid("PWHDelivery", $_GET['delivery_id']))
    {
        try
        {
            $subject = new PWHSubject();
            $subject->Read($_GET['subject_id']);
            $work = new PWHWork();
            $work->Read($_GET['work_id']);
            $delivery = new PWHDelivery();
            $delivery->Read($_GET['delivery_id']);
            $deliverygroups = $delivery->GetDeliverygroups();
            usort($deliverygroups, "entity_comparator");
            $freeStudents = $delivery->GetFreeStudents();
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
        if(isset($_GET['action']) && isset($_GET['deliverygroup_id']))
        {
            if($_GET['action'] == 'delete')
            {
                try
                {
                    $deliverygroup = new PWHDeliverygroup();
                    $deliverygroup->Read($_GET['deliverygroup_id']);
                    $deliverygroup->Delete();
                    
                    $targets = $deliverygroup->GetStudents();
                    PWHEvent::Notify($targets, STUDENT_TYPE, "Votre groupe de rendu dans le travail " . $subject->GetName() . "-" . $work->GetName() . " a &eacute;t&eacute; supprim&eacute;");
                    
                    PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Suppression groupe de rendu " . $deliverygroup->GetName() . " dans le travail " . $subject->GetName() . "-" . $work->GetName());
                    successReport("Le groupe de rendu " . $deliverygroup->GetName() . " a &eacute;t&eacute; supprim&eacute;.");
                }
                catch(Exception $ex)
                {
                    PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec suppression groupe de rendu dans le travail " . $subject->GetName() . "-" . $work->GetName());
                    errorReport($ex->getMessage());
                }
            }
        }
        
        $dateTranslator = new PWHDateTranslator();
        $deliveryName = mb_strtolower($delivery->GetName());
        $deliverygroups = $delivery->GetDeliverygroups();
    }
    
    if($failed)
    {
        errorReport("Impossible d'afficher la page demand&eacute;e.");
    }
?>


<section>
	<h2>etat des rendus de <?php echo $deliveryName; ?></h2>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/display_board.html', 800, 600);");
        
	    displayErrorReport();
	    displaySuccessReport();
	    if(!$failed)
	    {
            $totalStudents = count($freeStudents);
            $totalDelivered = 0;
            $totalLate = 0;
            $totalUndelivered = $totalStudents;
            foreach($deliverygroups as $deliverygroup)
            {
//                $totalStudents += $deliverygroup->CountStudents();
                $totalStudents++;
                if($deliverygroup->GetLastDelivery() == "")
                {
                    $totalUndelivered++;
                }
                else if($deliverygroup->IsExtraTimeUsed())
                {
                    $totalLate++;
                }
                else
                {
                    $totalDelivered++;
                }
            }
    ?>
    <h4>Statistiques</h4>
    <div class="section">
        <table class="summary">
            <tr>
                <td>Etudiants ayant livr&eacute;</td>
                <td><?php echo $totalDelivered; ?> sur <?php echo $totalStudents; ?></td>
            </tr>
            <tr>
                <td>Etudiants ayant livr&eacute; en retard</td>
                <td><?php echo $totalLate; ?> sur <?php echo $totalStudents; ?></td>
            </tr>
            <tr>
                <td>Etudiants n'ayant pas livr&eacute;</td>
                <td><?php echo $totalUndelivered; ?> sur <?php echo $totalStudents; ?></td>
            </tr>
        </table>
   </div>
   <?php
        if($delivery->IsConfigured() && $delivery->IsStillTimeForDelivery(date("Y-m-d H:i:s")))
        { ?>
   <h4>Composition des groupes de rendu</h4>
    <div class="section">
        <div class="list">
            <ul>
                <li><a href="index.php?page=teacher_create_deliverygroup&amp;previous=<?php echo $_GET['previous']; ?>&amp;subject_id=<?php echo $_GET['subject_id']; ?>&amp;work_id=<?php echo $_GET['work_id']; ?>&amp;delivery_id=<?php echo $_GET['delivery_id']; ?><?php if(isset($_GET['group_id'])) { echo "&amp;group_id=" . $_GET['group_id']; } ?>&amp;index=A"><img src="img/group_add.png"/>Composer les groupes de rendus</a></li>
            </ul>
        </div>
   </div>
   <?php } ?>
    <h4>Groupes de rendu existants</h4>
	<div class="section">
	    <?php
	        if(count($deliverygroups) > 0)
	        {
                $strbuf = "";
                foreach($deliverygroups as $deliverygroup)
                {
                    $strbuf .= $deliverygroup->GetEmail();    
                }
                ?>
        <a href="mailto:<?php echo $strbuf; ?>"><img src="img/email.png"/>Email du groupe d'&eacute;tudiants ayant un groupe de rendu</a>
      <?php } ?>
	    <table class="colored_table underlined_table">
	    <tr>
	        <th>Email</th>
	        <th>Membres</th>
	        <th>Cr&eacute;ation</th>
	        <th>Livraison</th>
	        <th>Supprimer</th>
	    </tr>
	    <?php
	        if(count($deliverygroups) == 0)
	        { ?>
	        <tr>
	            <td colspan="5">Il n'y a aucun groupe de rendu</td>
	        </tr>
	        <?php }
	        else
	        { 
	            foreach($deliverygroups as $deliverygroup)
	            { 
	                $students = $deliverygroup->GetStudents();
	                $class = "";
	                if($delivery->IsExtraTimeUsed(date("Y-m-d H:i:s")) && ($deliverygroup->GetLastDelivery() == "" || $deliverygroup->IsExtraTimeUsed()))
	                {
	                    $class = ' class="extra_time_line"';
	                }
	                else if(!$delivery->IsStillTimeForDelivery(date("Y-m-d H:i:s")) && $deliverygroup->IsExtraTimeUsed())
	                {
	                    $class = ' class="extra_time_line"';
	                }
	                else if(!$delivery->IsStillTimeForDelivery(date("Y-m-d H:i:s")) && $deliverygroup->GetLastDelivery() == "")
	                {
	                    $class = ' class="undelivered_line"';
	                }
	                ?>
	                <tr<?php echo $class; ?>>
	                    <td>
	                        <?php 
	                            $strbuf = '<a href="mailto:';
	                            $strbuf .= $deliverygroup->GetEmail();
	                            $strbuf .= '"><img src="img/email.png"/></a>';
	                            echo $strbuf;
	                            ?>
	                    </td>
	                    <td>
	                         <?php
	                            $strbuf = "";
	                            foreach($students as $student)
	                            {
	                                $strbuf .= '<a href="mailto:' . $student->GetEmail() . '">' . $student->GetLastName() . " " . $student->GetFirstName() . "</a>, ";
	                            } 
	                            $strbuf = substr($strbuf, 0, strlen($strbuf) - 2);
	                            echo $strbuf;
	                            ?>
	                    </td>
	                    <td><?php echo $dateTranslator->Html($deliverygroup->GetCreation(), PWHDateTranslator::DATE_AND_TIME); ?></td>
		                <td><?php echo $dateTranslator->Html($deliverygroup->GetLastDelivery(), PWHDateTranslator::DATE_AND_TIME); ?></td>
		                <td>
		                    <?php
		                        if($delivery->IsStillTimeForDelivery(date("Y-m-d H:i:s")))
		                        { ?>
		                    <a href="index.php?page=teacher_display_board&amp;index=<?php echo $_GET['index']; ?>&amp;previous=<?php echo $_GET['previous']; ?>&amp;subject_id=<?php echo $_GET['subject_id']; ?>&amp;work_id=<?php echo $work->GetID(); ?>&amp;delivery_id=<?php echo $delivery->GetID(); ?><?php if($_GET['previous'] == 'teacher_list_group_deliveries'){ echo "&amp;group_id=" . $_GET['group_id']; } ?>&amp;deliverygroup_id=<?php echo $deliverygroup->GetID(); ?>&amp;action=delete">
		                        <img src="img/cross.png">
		                    </a>
		                    <?php }
		                        else
		                        { ?>
		                  <a><img src="img/cross_lock.png"/></a>
		                  <?php } ?>
		                </td> 
	               </tr>
	            
	       <?php }
	       } ?>
        </table>
   </div>
   <h4>El&egrave;ves sans groupe de rendus</h4>
   <div class="section">
        <?php
            if(count($freeStudents) > 0)
            {
                $strbuf = "";
                foreach($freeStudents as $freeStudent)
                {
                    $strbuf .= $freeStudent->GetEmail() . ",";    
                }
                $strbuf = substr($strbuf, 0, strlen($strbuf) - 1);
                ?>
        <a href="mailto:<?php echo $strbuf; ?>"><img src="img/email.png"/>Email du groupe d'&eacute;tudiants sans groupe de rendu</a>
      <?php } 
        $index = new PWHIndex();
        $link = "index.php?page=teacher_display_board&amp;previous=" . $_GET['previous'] . "&amp;subject_id=" . $_GET['subject_id'] . "&amp;work_id=" . $_GET['work_id'] . "&amp;delivery_id=" . $_GET['delivery_id'];
        if(isset($_GET['group_id']))
        {
            $link .= "&amp;group_id=" . $_GET['group_id'];
        }
        echo $index->Html($link, $_GET['index'], false, $freeStudents);
        usort($freeStudents, "person_comparator");
        $freeStudents = $index->FilterPersons($freeStudents, $_GET['index']);
      ?>
        <table class="colored_table underlined_table">
        <tr>
            <th>Email</th>
            <th>Nom</th>
            <th>Pr&eacute;nom</th>
        </tr>
        <?php
            if(count($freeStudents) == 0)
            { ?>          
                <tr><td colspan="3">Il n'y a aucun &eacute;tudiants sans groups de rendu</td></tr>      
      <?php }
            foreach($freeStudents as $freeStudent)
            { ?>
                <tr>
                    <td><a href="mailto:<?php echo $freeStudent->GetEmail(); ?>"><img src="img/email.png"/></a></td>
                    <td><?php echo $freeStudent->GetLastName(); ?></td>
                    <td><?php echo $freeStudent->GetFirstName(); ?></td>
                </tr>
            <?php } ?>
        </table>
   </div>
   <?php } ?>
</section>
