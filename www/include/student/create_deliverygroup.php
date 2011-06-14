<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page student_create_deliverygroup");
     
    previousPage('student_display_delivery');
    addPreviousPageParameter('subject_id', $_GET['subject_id']);
    addPreviousPageParameter('work_id', $_GET['work_id']);
    addPreviousPageParameter('delivery_id', $_GET['delivery_id']);
    
    $failed = false;
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
    
    if(isset($_GET['subject_id']) && isset($_GET['delivery_id']) && isset($_GET['work_id']) && isset($_GET['index']) && preg_match("#^[*A-Z]$#", $_GET['index'])
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
                PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Acc&egrave;s page student_create_deliverygroup sur rendu inattendu");
            }
            
            $work = new PWHWork();
            $work->Read($_GET['work_id']);
            $subject = new PWHSubject();
            $subject->Read($_GET['subject_id']);
            
            $students = $delivery->GetFreeStudents();
            $allStudents = $students;
            usort($students, "person_comparator");
            $index = new PWHIndex();
            $students = $index->FilterPersons($students, $_GET['index']);
           
            $i=0;
            while($i < count($students))
            {
                if($students[$i]->GetID() == $student->GetID())
                {
                    array_splice($students, $i, 1);
                    break;
                }
                $i++;
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
        PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Acc&egrave;s page student_create_deliverygroup avec param&egrave;tres URL absents ou corrompus");
    }
    
    if(!$failed)
    {
        if(isset($_GET['action']) && $_GET['action'] == 'create')
        {
            if($work->IsSimple() || $work->GetGroupMax() == 1 || $delivery->IsStillTimeForGroupComposition(date("Y-m-d H:i:s")))
            {
                try
                {
                    $update = false;
                    if(!$student->HasDeliverygroup($delivery->GetID()))
                    {
                        $deliverygroup = new PWHDeliverygroup();
                        $deliverygroup->SetDeliveryID($delivery->GetID());
                        $deliverygroup->AddStudents(array($_SESSION['id']));
                    }
                    else
                    { 
                        $deliverygroup = $student->GetDeliverygroup($delivery->GetID());
                        $update = true;
                    }
                    
                    $insert = array();
                    foreach($students as $s)
                    {
                        if(isset($_POST[$s->GetID()]))
                        {
                            array_push($insert, (int)$s->GetID());
                        }
                    }
                    
                    
                    if(count($insert) + count($deliverygroup->GetStudents()) <= $work->GetGroupMax())
                    {       
                        $deliverygroup->AddStudents($insert);    
                        if($update)
                        {
                            successReport("Le groupe de rendu a &eacute;t&eacute; mis &agrave jour.");
                            $deliverygroup->Update();
                        }
                        else
                        {
                            $deliverygroup->SetCreation(date("Y-m-d H:i:s"));
                            $deliverygroup->Create(true);
                            $deliverygroup->CreateDirectory();
                            successReport("Le groupe de rendu a &eacute;t&eacute; cr&eacute;&eacute;.");
                        }
                        
                        $persons = array();
                        foreach($insert as $id)
                        {
                            $person = new PWHStudent();
                            $person->Read($id);
                            array_push($persons, $person);
                        }

                        foreach($persons as $person)
                        {
                            $strbuf .= $person->GetLastName() . " " . $person->GetFirstName() . ", ";
                        }
                        $strbuf = substr($strbuf, 0, strlen($strbuf) - 2);
                        
                        $targets = $deliverygroup->GetStudents();
                        $table = new PWHPersonTable();
                        $targets = $table->FilterPersons($targets, array($_SESSION['id']));
                        
                        if(count($persons) == 1)
                        {
                            PWHEvent::Notify($targets, STUDENT_TYPE, $strbuf . " a rejoint un groupe de rendu dans le travail " . $subject->GetName() . "-" . $work->GetName());
                            PWHEvent::Notify(array($student), STUDENT_TYPE, $strbuf . " a rejoint votre groupe de rendu dans le travail " . $subject->GetName() . "-" . $work->GetName());
                        }
                        else
                        {
                            PWHEvent::Notify($targets, STUDENT_TYPE, $strbuf . " ont rejoint un groupe de rendu dans le travail " . $subject->GetName() . "-" . $work->GetName());
                            PWHEvent::Notify(array($student), STUDENT_TYPE, $strbuf . " ont rejoint votre groupe de rendu dans le travail " . $subject->GetName() . "-" . $work->GetName());
                        }
                        
                        PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Cr&eacute;ation groupe de rendu");
                        
                        $table = new PWHPersonTable();
	                    $students = $table->FilterPersons($students, $insert);
	                    $allStudents = $table->FilterPersons($allStudents, $insert);
                    }
                    else
                    {
                        $left = $work->GetGroupMax() - count($deliverygroup->GetStudents());
                        if($left == 1)
                        {
                            errorReport("Vous ne pouvez choisir que 1 membre.");
                        }
                        else
                        {
                            errorReport("Vous ne pouvez choisir que " . $left . " membres.");
                        }
                        PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Echec cr&eacute;ation groupe de rendu: trop de membres");
                    }
                }
                catch(Exception $ex)
                {
                    errorReport($ex->getMessage());
                }
            }
            else
            {
                errorReport("La date de composition des groupes est d&eacute;pass&eacute;e.");
                PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Echec cr&eacute;ation groupe de rendu: date de composition d&eacute;pass&eacute;e");
            }
        }
    
        if($student->HasDeliverygroup($delivery->GetID()))
        {
            $deliverygroup = $student->GetDeliverygroup($delivery->GetID());
            $left = $work->GetGroupMax() - count($deliverygroup->GetStudents());
        }
        else
        {  
            $left = $work->GetGroupMax() - 1;
        }
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
	<h2>&eacute;tudiants disponibles</h2>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/student/help/create_deliverygroup.html', 800, 550);");
        
        displayErrorReport();
	    displaySuccessReport();
	        
        if(!$failed)
        {     
    ?>
	<h4 id="counter">Liste des &eacute;tudiants disponibles - encore <?php echo $left; ?> <?php if($left > 1) { echo "membres"; } else { echo "membre"; } ?> pour compl&eacute;ter le groupe</h4>
	<div class="section">
	    <?php
	        $index = new PWHIndex();
	        echo $index->Html("index.php?page=student_create_deliverygroup&amp;subject_id=" . $_GET['subject_id'] . "&amp;work_id=" . $_GET['work_id'] . "&amp;delivery_id=" . $_GET['delivery_id'], $_GET['index'], true, $allStudents);
	    ?>
		<form id="person_index" method="post" action="index.php?page=student_create_deliverygroup&amp;action=create&amp;subject_id=<?echo $_GET['subject_id']; ?>&amp;work_id=<?php echo $_GET['work_id']; ?>&amp;delivery_id=<?php echo $_GET['delivery_id']; ?>&amp;index=<?php echo $_GET['index']; ?>">
		    <input type="hidden" name="action" id="action" value="create"/>
	        <?php
	            $table = new PWHPersonTable();
	            echo $table->Html($students, "Ajouter +");
	        ?>
	   </form>
	</div>
	<?php } ?>
</section>

<script type="text/javascript" charset="iso-8859-1">
<!--
    var left = <?php echo $left; ?>;
    
    var form = document.getElementById("person_index");
    var boxs = form.elements;
        
    function CheckForm()
    {
        var form = document.getElementById("person_index");
        var input = document.getElementById("next");
        var lines = form.getElementsByTagName("tr");
        var boxs = form.elements;
        
        var disabled = true;
        for(var i=0; i<boxs.length; i++)
        {
             var index = i;
             if(boxs[i].checked)
             {   
                disabled = false;
                lines[index].className = "selected";
             }
             else
             {
                if(index < lines.length-1)
                {
                    lines[index].className = "alt";
                }
                if(i%2 == 1)
                {
                    lines[index].className = "";
                }
             }
        }
        input.disabled = disabled; 
        
        var cpt = 0;
        for(var i=0; i<boxs.length; i++)
        {
             if(boxs[i].checked)
             {
                cpt++;
             }
        }
        var counter = document.getElementById("counter");
        var sum = left - cpt;
        if(sum > 1)
        {
            counter.innerHTML = "Liste des &eacute;tudiants disponibles - encore " + sum + " membres pour compl&eacute;ter le groupe";
        }
        else
        {
            counter.innerHTML = "Liste des &eacute;tudiants disponibles - encore " + sum + " membre pour compl&eacute;ter le groupe";
        }
        
        var save = input.disabled;
        if(sum == 0)
        {
            for(var i=0; i<boxs.length; i++)
            {
                 if(!boxs[i].checked)
                 {
                    boxs[i].disabled = true;
                 }
            }
        }
        else if(sum > 0)
        {
            for(var i=0; i<boxs.length; i++)
            {
                 if(!boxs[i].checked)
                 {
                    boxs[i].disabled = false;
                 }
            }
        }
        input.disabled = save;
    }
    
    var input = document.getElementById("next");
    
    for(var i=0; i<boxs.length; i++)
    {
        boxs[i].onclick = CheckForm;
    }
    
    var disabled = true;
    for(var i=0; i<boxs.length; i++)
    {
         if(boxs[i].checked)
         {
            disabled = false;
         }
    }
    input.disabled = disabled;
    
    var counter = document.getElementById("counter");
    var save = input.disabled;
    if(left == 0)
    {
        for(var i=0; i<boxs.length; i++)
        {
             if(!boxs[i].checked)
             {
                boxs[i].disabled = true;
             }
        }
    }
    else if(left > 0)
    {
        for(var i=0; i<boxs.length; i++)
        {
             if(!boxs[i].checked)
             {
                boxs[i].disabled = false;
             }
        }
    }
    input.disabled = save;
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
//-->
</script>
