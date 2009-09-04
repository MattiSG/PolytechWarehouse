<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page subject_settings_teachers_remove");
    
    previousPage('teacher_list_subjects');
    addPreviousPageParameter('see', 'less');
     
    // Retrieves the concerned subject
    if(isset($_GET['subject_id']))
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
    }
    
        // Retrieves the list of all teachers
    try
    {
        $teachers = $subject->GetTeachers();
        $allTeachers = $teachers;
        usort($teachers, "person_comparator");
        $index = new PWHIndex();
        $teachers = $index->FilterPersons($teachers, $_GET['index']);
    }
    catch(Exception $ex)
    {
        errorReport($ex->getMessage());
        $teachers = array();
    }
    
    // [LINK ACTION] Adds teachers responsible of the subject
    if(isset($_GET['action']))
    {              
        try
        {
            if($_GET['action'] == 'remove')
            {
                $remove = array();
                foreach($teachers as $teacher)
                {
                    if(isset($_POST[$teacher->GetID()]))
                    {
                        array_push($remove, (int)$teacher->GetID());
                    }
                }
                
                $persons = array();
                foreach($remove as $id)
                {
                    $person = new PWHTeacher();
                    $person->Read($id);
                    array_push($persons, $person);
                }
                
                $subject->RemoveTeachers($remove);
                $subject->Update();
                $targets = $subject->GetTeachers();
                PWHEvent::Notify($targets, TEACHER_TYPE, "L'ensemble des enseignants responsables de la mati&egrave;re " . $subject->GetName() . " a &eacute;t&eacute; modifi&eacute;");
                PWHEvent::Notify($persons, TEACHER_TYPE, "Vous avez &eacute;t&eacute; supprim&eacute; de la mati&egrave;re " . $subject->GetName());
                $table = new PWHPersonTable();
                $teachers = $table->FilterPersons($teachers, $remove);
                $allTeachers = $table->FilterPersons($allTeachers, $remove);
                if(count($remove) > 1)
                {
                    successReport("Les enseignants ont &eacute;t&eacute; supprim&eacute;s de la mati&egrave;re " . $subject->GetName() . ".");
                }
                else
                {
                    successReport("L'enseignant a &eacute;t&eacute; supprim&eacute;s de la mati&egrave;re " . $subject->GetName() . ".");
                }
                PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Mise &agrave; jour suppression [enseignants] mati&egrave;re " . $subject->GetName());
            }
        }
        catch(Exception $ex)
        {
            PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec mise &agrave; jour suppression [enseignants] mati&egrave;re");
            errorReport($ex->getMessage());
        }     
    }
?>

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

<fieldset>
	<legend>configuration de <?php echo mb_strtolower($subject->GetName()); ?></legend>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/subject_settings_teachers_remove.html', 800, 600);");
        
	   displayErrorReport();
	   displaySuccessReport();
	?>
	<div class="tab">
      <ul>
        <li><a href="index.php?page=teacher_subject_settings_name&amp;subject_id=<?php echo $subject->GetID(); ?>">Nom</a></li>
        <li><a href="index.php?page=teacher_subject_settings_teachers_add&amp;subject_id=<?php echo $subject->GetID(); ?>&amp;index=A">Ajout d'enseignants</a></li>
        <li class="active"><a href="index.php?page=teacher_subject_settings_teachers_remove&amp;subject_id=<?php echo $subject->GetID(); ?>&amp;index=A">Suppression d'enseignants</a></li>
      </ul>
    </div>
	<div class="section">
	    <?php
	        $index = new PWHIndex();
	        echo $index->Html("index.php?page=teacher_subject_settings_teachers_remove&amp;subject_id=" . $subject->GetID(), $_GET['index'], true, $allTeachers);
	    ?>
	    <form id="person_index" method="post" action="index.php?page=teacher_subject_settings_teachers_remove&amp;subject_id=<?php echo $subject->GetID(); ?>&amp;index=<?php echo $_GET['index']; ?>&amp;action=remove">
	        <?php
                $table = new PWHPersonTable();
                echo $table->Html($teachers, "Supprimer -");
	        ?>
	   </form>
	</div>
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
