<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page student_display_deliverygroup");
    
    previousPage('student_display_delivery');
    addPreviousPageParameter('subject_id', $_GET['subject_id']);
    addPreviousPageParameter('work_id', $_GET['work_id']);
    addPreviousPageParameter('delivery_id', $_GET['delivery_id']);   

    $failed = false;
    $studentName = "???";
    
    try
    {
        $student = new PWHStudent();
        $student->Read($_SESSION['id']);
    }
    catch(Exception $ex)
    {
        errorReport($ex->getMessage());
        $failed = true;
    }
     
    if(isset($_GET['subject_id']) && isset($_GET['delivery_id']) && isset($_GET['work_id'])
        && PWHEntity::Valid("PWHSubject", $_GET['subject_id']) 
        && PWHEntity::Valid("PWHWork", $_GET['work_id']) 
        && PWHEntity::Valid("PWHDelivery", $_GET['delivery_id']))
    {        
        try
        {
            $exist = false;
            $delivery = new PWHDelivery();
            $delivery->Read($_GET['delivery_id']);
            $groups = $delivery->GetGroups();
            foreach($groups as $group)
            {
                if($group->StudentExists($student->GetID()))
                {
                    $exist = true;
                }
            }
            
            if(!$exist)
            {
                PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Acc&egrave;s page student_display_deliverygroup sur rendu inattendu");
            }
            
            $work = new PWHWork();
            $work->Read($_GET['work_id']);
            $subject = new PWHSubject();
            $subject->Read($_GET['subject_id']);
            $deliverygroup = $student->GetDeliverygroup($delivery->GetID());
            $deliverygroups = $delivery->GetDeliverygroups();
            
            $i = 0;
            while($i < count($deliverygroups))
            {
                if($deliverygroups[$i]->GetID() == $deliverygroup->GetID())
                {
                    array_splice($deliverygroups, $i, 1);
                }
                else
                {
                    $i++;
                }
            }
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());
            $failed = true;
        }
    }
    else
    {
        $failed = true;
        PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Acc&egrave;s page student_display_deliverygroup avec param&egrave;tres URL absents ou corrompus");
    }
    
    if(!$failed)
    {
        if(isset($_GET['action']) && $_GET['action'] == 'leave')
        {
            if($delivery->IsStillTimeForGroupComposition(date("Y-m-d H:i:s")))
            {
                try 
                {
                    $targets = $deliverygroup->GetStudents();
                    $table = new PWHPersonTable();
                    $targets = $table->FilterPersons($targets, array($student->GetID()));
                    $deliverygroup->RemoveStudents(array($student->GetID()));
                    $deliverygroup->Update();
                    PWHEvent::Notify($targets, STUDENT_TYPE, $student->GetLastName() . " " . $student->GetFirstName() . " a quitt&eacute; votre groupe de rendu dans le travail " . $subject->GetName() . "-" . $work->GetName());
                    PWHEvent::Notify(array($student), STUDENT_TYPE, $student->GetLastName() . " " . $student->GetFirstName() . " a quitt&eacute; son groupe de rendu dans le travail " . $subject->GetName() . "-" . $work->GetName());
                    if(!$deliverygroup->HasStudents())
                    {
                        $deliverygroup->Delete();
                    }
                    
                    successReport("Vous avez quitt&eacute; le groupe de rendu.");
                    PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Abandon groupe de rendu");
                }
                catch(Exception $ex)
                {
                    errorReport($ex->getMessage());
                }
            }
            else
            {
                errorReport("La date de composition des groupes est d&eacute;pass&eacute;e.");  
                PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Echec abandon groupe de rendu: date composition d&eacute;pass&eacute;e");
            }
        }
        
        $dateTranslator = new PWHDateTranslator();
        
            
        $memos = array();
        if($deliverygroup != null && $deliverygroup->IsSuper())
        {
            $memo = new PWHMemo();
            $memo->SetText("Ce groupe de rendu a &eacute;t&eacute; cr&eacute;e par un enseignant. Vous ne pouvez pas quitter ce groupe sans l'intervention d'un responsable.");
            array_push($memos, $memo);
        }
        $studentName = mb_strtolower($student->GetFirstName() . " " . $student->GetLastName());
    }
    
    if($failed)
    {
        errorReport("Impossible d'afficher la page demand&eacute;e.");
    }
    else if(!$failed && !$exist)
    {      
        errorReport("Vous n'&ecirc;tes pas concern&eacute; par ce rendu.");
    }
?>


<section>
	<h2>groupe de rendu de <?php echo $studentName; ?></h2>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/student/help/display_deliverygroup.html', 800, 550);");
    
        displayErrorReport();
	    displaySuccessReport();
	        
        if(!$failed && $deliverygroup != null)
        {
	        foreach($memos as $memo)
	        {
	            echo $memo->Html();
	        }
    ?>
    <div class="section">
        <div class="list">
            <ul>
                <?php 
                if(!$deliverygroup->IsSuper() && !$work->IsSimple() && $delivery->IsStillTimeForGroupComposition(date("Y-m-d H:i:s")) && $deliverygroup->StudentExists($student->GetID()))
                { ?>
                <li><a href="index.php?page=student_display_deliverygroup&amp;action=leave&amp;subject_id=<?php echo $_GET['subject_id']; ?>&amp;work_id=<?php echo $_GET['work_id']; ?>&amp;delivery_id=<?php echo $delivery->GetID(); ?>&amp;deliverygroup_id=<?php echo $deliverygroup->GetID(); ?>"><img src="img/group_delete"/>Quitter le groupe de rendu</a></li>
                <?php } ?>
            </ul>
        </div>
   </div>
    <h4>Listes des membres du groupe de rendu</h4>
	<div class="section">
		<table class="colored_table underlined_table">
		    <tr>
		        <th>Email</th>
		        <th>Membres</th>
		        <th>Cr&eacute;ation</th>
		        <th>Livraison</th>
		    </tr>
		    <tr>
		        <td><a href="mailto:<?php echo $deliverygroup->GetEmail(); ?>"><img src="img/email.png"/></a></td>
		        <td><?php
		            $students = $deliverygroup->GetStudents();
		            $strbuf = "";
		            foreach($students as $s)
		            {
		                $strbuf .= $s->GetLastName() . " " . $s->GetFirstName() . ", ";
		            } 
		            $strbuf = substr($strbuf, 0, strlen($strbuf) - 2);
		            echo $strbuf;
		            ?>
		         </td>
		         <td><?php echo $dateTranslator->Html($deliverygroup->GetCreation(), PWHDateTranslator::DATE_AND_TIME); ?></td>
		         <td><?php echo $dateTranslator->Html($deliverygroup->GetLastDelivery(), PWHDateTranslator::DATE_AND_TIME); ?></td>
		    </tr>
	    </table>
	</div>
   <h4>Autres groupes de rendu</h4>
	<div class="section">
	    <table class="colored_table underlined_table">
	    <tr>
	        <th>Email</th>
	        <th>Membres</th>
	        <th>Cr&eacute;ation</th>
	        <th>Livraison</th>
	    </tr>
	    <?php
	        if(count($deliverygroups) == 0)
	        { ?>
	        <tr>
	            <td colspan="5">Il n'y a aucun autre groupe de rendu</td>
	        </tr>
	        <?php }
	        else
	        { 
	            foreach($deliverygroups as $deliverygroup)
	            { 
	                $students = $deliverygroup->GetStudents();
	                ?>
	                <tr>
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
	               </tr>
	            
	       <?php }
	       } ?>
        </table>
   </div>
   <?php } ?>
</section>
