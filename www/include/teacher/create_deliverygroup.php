<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page create_deliverygroup");
    
    previousPage('teacher_display_board');
    addPreviousPageParameter('subject_id', $_GET['subject_id']);
    addPreviousPageParameter('work_id', $_GET['work_id']);
    addPreviousPageParameter('delivery_id', $_GET['delivery_id']);
    addPreviousPageParameter('previous', $_GET['previous']);
    addPreviousPageParameter('index', 'A');
    $failed = false;
    
    if(isset($_GET['group_id']))
    {
        addPreviousPageParameter('group_id', $_GET['group_id']);
    }
    
    
    if(isset($_GET['subject_id']) && isset($_GET['work_id']) && isset($_GET['index']) && preg_match("#^[*A-Z]$#", $_GET['index'])
        && PWHEntity::Valid("PWHSubject", $_GET['subject_id'])
        && PWHEntity::Valid("PWHWork", $_GET['work_id']))
    {
        try
        {
            $delivery = new PWHDelivery();
            $delivery->Read($_GET['delivery_id']);
            $work = new PWHWork();
            $work->Read($_GET['work_id']);
            $subject = new PWHSubject();
            $subject->Read($_GET['subject_id']);
            $students = $delivery->GetFreeStudents();
            $allStudents = $students;
            usort($students, "person_comparator");
            $index = new PWHIndex();
            $students = $index->FilterPersons($students, $_GET['index']);     
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
        if(isset($_SESSION['students']))
        {
            $table = new PWHPersonTable();
            $students = $table->FilterPersons($students, $_SESSION['students']);
        }
        
        // [FORM] Save the teachers who have been added to the subject
        if(isset($_GET['action']) && $_GET['action'] == 'add_students')
        {
            if(!isset($_SESSION['students']))
            {
                $_SESSION['students'] = array();
            }

            $insert = array();
            foreach($students as $student)
            {
                if(isset($_POST[$student->GetID()]))
                {
                    array_push($insert, (int)$student->GetID());
                }
            }
            $_SESSION['students'] = array_merge($_SESSION['students'], $insert);
            $table = new PWHPersonTable();
            $students = $table->FilterPersons($students, $_SESSION['students']);
            $allStudents = $table->FilterPersons($allStudents, $_SESSION['students']);
         }
        
        if(isset($_GET['action']) && $_GET['action'] == 'create')
        {
            try
            {    
                $deliverygroup = new PWHDeliverygroup();
                $deliverygroup->SetDeliveryID($delivery->GetID());
                $deliverygroup->SetSuper(true);
                $deliverygroup->SetCreation(date("Y-m-d H:i:s"));
                $deliverygroup->AddStudents($_SESSION['students']);
                $deliverygroup->Create(true);
                $deliverygroup->CreateDirectory();    
                
                $table = new PWHPersonTable();
                $students = $table->FilterPersons($students, $_SESSION['students']);
                $allStudents = $table->FilterPersons($allStudents, $_SESSION['students']);
                
                $targets = $deliverygroup->GetStudents();
                $strbuf = "";
                foreach($targets as $target)
                {
                    $strbuf .= $target->GetLastName() . " " . $target->GetFirstName() . ", ";
                }
                $strbuf = substr($strbuf, 0, strlen($strbuf) - 2);
                
                if(count($targets) == 1)
                {
                    PWHEvent::Notify($targets, STUDENT_TYPE, $strbuf . " a &eacute;t&eacute; assign&eacute; &agrave; un groupe de rendu dans le travail " . $subject->GetName() . "-" . $work->GetName());
                }
                else
                {
                    PWHEvent::Notify($targets, STUDENT_TYPE, $strbuf . " ont &eacute;t&eacute; assign&eacute;s &agrave; un groupe de rendu dans le travail " . $subject->GetName() . "-" . $work->GetName());
                }
                
                PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Cr&eacute;ation groupe de rendu " . $deliverygroup->GetName() . " dans le travail " . $subject->GetName() . "-" . $work->GetName());
                successReport("Le groupe de rendu a &eacute;t&eacute; cr&eacute;&eacute;.");
                
                unset($_SESSION['students']);
            }
            catch(Exception $ex)
            {
                PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec cr&eacute;ation groupe de rendu dans le travail " . $subject->GetName() . "-" . $work->GetName());
                errorReport($ex->getMessage());
            }
        }
        
        // Creates a new memo for the user     
        $memo = new PWHDeliverygroupCreationMemo();
        if(isset($_SESSION['students']))
        {
            $memo->SetStudents($_SESSION['students']);
        }
        
        // Permit the creation of the group
        $disabled = "";
        if(!isset($_SESSION['students']))
        {
            $disabled = 'disabled="disabled"';
        }
    }
    
    if($failed)
    {
        errorReport("Impossible d'afficher la page demand&eacute;e.");
    }
?>
<fieldset>
	<legend>&eacute;tudiants disponibles</legend>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/create_deliverygroup.html', 800, 600);");
        
	    displayErrorReport();
	    displaySuccessReport();
	    
	    if(!$failed)
	    {
	        echo $memo->Html();
	?>
	<h4>Liste des &eacute;tudiants disponibles</h4>
	<div class="section">
	    <?php
	        $index = new PWHIndex();
	        $link = "index.php?page=teacher_create_deliverygroup&amp;previous=" . $_GET['previous'] . "&amp;subject_id=" . $_GET['subject_id'] . "&amp;work_id=" . $_GET['work_id'] . "&amp;delivery_id=" . $_GET['delivery_id'];
	        if(isset($_GET['group_id']))
	        {
	            $link .= "&amp;group_id=" . $_GET['group_id'];
	        }
	        echo $index->Html($link, $_GET['index'], true, $allStudents);
	    ?>
		<form id="person_index" method="post" action="index.php?page=teacher_create_deliverygroup&amp;previous=<?php echo $_GET['previous']; ?>&amp;action=add_students&amp;subject_id=<?php echo $_GET['subject_id']; ?>&amp;work_id=<?php echo $_GET['work_id']; ?>&amp;delivery_id=<?php echo $_GET['delivery_id']; ?><?php if(isset($_GET['group_id'])) { echo "&amp;group_id=" . $_GET['group_id']; } ?>&amp;index=<?php echo $_GET['index']; ?>">
	        <?php
	            $table = new PWHPersonTable();
	            echo $table->Html($students, "Ajouter +");
	        ?>
	   </form>
	   <form method="post" action="index.php?page=teacher_create_deliverygroup&amp;previous=<?php echo $_GET['previous']; ?>&amp;action=create&amp;subject_id=<?php echo $_GET['subject_id']; ?>&amp;work_id=<?php echo $_GET['work_id']; ?>&amp;delivery_id=<?php echo $_GET['delivery_id']; ?><?php if(isset($_GET['group_id'])) { echo "&amp;group_id=" . $_GET['group_id']; } ?>&amp;index=<?php echo $_GET['index']; ?>">
	        <div class="input">
	            <input onclick="javascript:UserConfirmSubmit();" class="next_form" <?php echo $disabled; ?> type="submit" value="Cr&eacute;er !"/>
	        </div>
	    </form>
	</div>
	<?php } ?>
</fieldset>

<script type="text/javascript" charset="iso-8859-1">
<!--
    function CheckForm(index)
    {
        var form = document.getElementById("person_index");
        var input = document.getElementById("next");
        var boxs = form.elements;
              
        var disabled = true;
        for(var i=0; i<boxs.length; i++)
        {
             if(boxs[i].checked)
             {
                disabled = false;
             }
        }
        input.disabled = disabled;
        
        boxs[index].checked = !boxs[index].checked;
    }
    
    function CheckBox(index)
    {
        var box = document.getElementById("cb_" + index);
        box.checked = !box.checked;
        
        var form = document.getElementById("person_index");
        var input = document.getElementById("next");
        var boxs = form.elements;
              
        var disabled = true;
        for(var i=0; i<boxs.length; i++)
        {
             if(boxs[i].checked)
             {
                disabled = false;
             }
        }
        input.disabled = disabled;
        
        var lines = form.getElementsByTagName("tr");
        index++;
        if(box.checked)
        {
            lines[index].className = "selected";
        }
        else
        {
            lines[index].className = "";
            if(index%2 == 0)
            {
                lines[index].className = "alt";
            }
        }
    } 
    
    var form = document.getElementById("person_index");
    var boxs = form.elements;
    var input = document.getElementById("next");
        
    var disabled = true;
    for(var i=0; i<boxs.length; i++)
    {
         if(boxs[i].checked)
         {
            disabled = false;
         }
    }
    input.disabled = disabled; 
//-->
</script>
<script type="text/javascript" charset="iso-8859-1">
<!--
    function MakeIndex(link, letter)
    {
        var form = document.getElementById("person_index");
        var boxs = form.elements;
        var quit = true;
        for(var i=0; i<boxs.length; i++)
        {
            if(boxs[i].checked)
            {
                if(confirm("Vous n'avez pas valid\351 le formulaire en cliquant sur le bouton \"Ajouter +\" ? Voulez-vous le valider avant de quitter cette page ?"))
                {
                    form.submit();
                    window.location = link + "&index=" + letter;
                    break;
                }
                else
                {
                    window.location = link + "&index=" + letter;
                    break;
                }
           }
        }

        window.location = link + "&index=" + letter;
    }
    
    function UserConfirmSubmit()
    {
        var form = document.getElementById("person_index");
        var boxs = form.elements;
        for(var i=0; i<boxs.length; i++)
        {
            if(boxs[i].checked)
            {
                if(confirm("Vous n'avez pas valid\351 le formulaire en cliquant sur le bouton \"Ajouter +\" ? Voulez-vous le valider avant de cr\351er le groupe de rendu ?"))
                {
                    form.submit();
                    break;
                }
                else
                {
                    break;
                }
           }
        }
    }
//-->
</script>
