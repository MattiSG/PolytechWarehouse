<?php 
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page group_settings_students_remove");
    
    previousPage("teacher_list_groups");
    $failed = false;
    $groupName = "???";
    
    // Retrieves the concerned group and students
    if(isset($_GET['group_id']) && PWHEntity::Valid("PWHGroup", $_GET['group_id']) && isset($_GET['index']) && preg_match("#^[*A-Z]$#", $_GET['index']))
    {
        try
        {
            $group = new PWHGroup();
            $group->Read($_GET['group_id']);
            $students = $group->GetStudents();
            $allStudents = $students;
            usort($students, "person_comparator");
            $index = new PWHIndex();
            $students = $index->FilterPersons($students, $_GET['index']);
        }
        catch(Exception $ex)
        {
            $failed = true;
            errorReport($ex->getMessage);
        }
    }
    else
    {
        $failed = true;
    }
    
    if(!$failed)
    {
        // [FORM] Remove students from the group
        if(isset($_GET['action']))
        {          
            try
            {
                if($_GET['action'] == 'remove')
                {
                    $remove = array();
                    foreach($students as $student)
                    {
                        if(isset($_POST[$student->GetID()]))
                        {
                            array_push($remove, (int)$student->GetID());
                        }
                    }
                    $group->RemoveStudents($remove);
                    $group->Update();
                    $table = new PWHPersonTable();
                    $students = $table->FilterPersons($students, $remove);
                    $allStudents = $table->FilterPersons($allStudents, $remove);
                    if(count($remove) > 1)
                    {
                        successReport("Les &eacute;tudiants ont &eacute;t&eacute; supprim&eacute;s du groupe " . $group->GetName() . ".");
                    }
                    else
                    {
                        successReport("L'&eacute;tudiant a &eacute;t&eacute; supprim&eacute; du groupe " . $group->GetName() . ".");
                    }
                    PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Mise &agrave; jour suppression [&eacute;tudiants] groupe " . $group->GetName());
                }
            }
            catch(Exception $ex)
            {
                PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Echec mise &agrave; jour suppression [&eacute;tudiants] groupe");
                errorReport($ex->getMessage());
            }    
            
        }
        
        $groupName = mb_strtolower($group->GetName());
    }
    
    if($failed)
    {
        errorReport("Impossible d'afficher la page demand&eacute;e.");
    }

?>
<section>
	<h2>configuration de <?php echo $groupName; ?></h2>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/group_settings_students_remove.html', 800, 550);");
        
	    displayErrorReport();
	    displaySuccessReport();
	    
	    if(!$failed)
	    {
	?>
    <div class="tab">
      <ul>
        <li><a href="index.php?page=teacher_group_settings_name&amp;group_id=<?php echo $group->GetID(); ?>">Nom</a></li>
        <?php if($group->GetParentID() == -1)
        { ?>
        <li><a href="index.php?page=teacher_group_settings_student&amp;group_id=<?php echo $group->GetID(); ?>">Cr&eacute;ation rapide d'&eacute;tudiants</a></li>
        <?php } ?>
        <li><a href="index.php?page=teacher_group_settings_students_add&amp;group_id=<?php echo $group->GetID(); ?>&amp;index=A&amp;index_alt=A">Ajout d'&eacute;tudiants</a></li>
        <li class="active"><a href="index.php?page=teacher_group_settings_students_remove&amp;group_id=<?php echo $group->GetID(); ?>&amp;index=A">Suppression d'&eacute;tudiants</a></li>
        <li><a href="index.php?page=teacher_group_settings_students_edit&amp;group_id=<?php echo $group->GetID(); ?>&amp;index=A">Edition des profils</a></li>
      </ul>
    </div>
	<div class="section">
	    <?php 
	        $index = new PWHIndex();
	        echo $index->Html("index.php?page=teacher_group_settings_students_remove&amp;group_id=" . $group->GetID(), $_GET['index'], true, $allStudents);
	    ?>
	    <form id="person_index" method="post" action="index.php?page=teacher_group_settings_students_remove&amp;group_id=<?php echo $group->GetID(); ?>&amp;index=<?php echo $_GET['index']; ?>&amp;action=remove">
            <?php
                $table = new PWHPersonTable();
                echo $table->Html($students, "Supprimer -");
	        ?>
	    </form>
	</div>
	<?php } ?>
</section>
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
