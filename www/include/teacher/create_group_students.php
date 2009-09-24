<?php 
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page create_group_students");
    
    previousPage('teacher_create_group_name');
    $failed = false;
    
    // Definition of the parent group
    if(isset($_POST['parentGroup']) && PWHEntity::Valid("PWHGroup", $_POST['parentGroup']) && isset($_POST['groupName']))
    {
        $_SESSION['parent_group'] = (int)$_POST['parentGroup'];
        
        $parent = new PWHGroup();
        $parent->Read($_SESSION['parent_group']);
        if($parent->IsChildUniqueName(stripslashes($_POST['groupName'])))
        {
            $_SESSION['group_name'] = stripslashes($_POST['groupName']);
        }
        else
        {
            redirect("index.php?page=teacher_create_group_name&amp;action=alert_name_used");
        }
        // Saves the specified name when go back to the previous page
        addPreviousPageParameter('parent_group', $_SESSION['parent_group']);
        // Saves the specified name when go back to the previous page
        addPreviousPageParameter('group_name', $_SESSION['group_name']);
    }
       
    // Retrieves the list of students sorted, corresponding to the value of the index
    if(isset($_GET['index']) && preg_match("#^[*A-Z]$#", $_GET['index']))
    {        
        try
        {
            $parentGroup = new PWHGroup();
            $parentGroup->Read($_SESSION['parent_group']);
            $students = $parentGroup->GetStudents();  
            usort($students, "person_comparator");
            $index = new PWHIndex();
            $students = $index->FilterPersons($students, $_GET['index']);
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
    }
    
    if(!$failed)
    {
        if(isset($_SESSION['students']))
        {
            $table = new PWHPersonTable();
            $students = $table->FilterPersons($students, $_SESSION['students']);
        }
        
        // [FORM] Save the teachers who have been added to the subject
        if(isset($_GET['action']))
        {    
            if($_GET['action'] == 'add_students')
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
            }
            else if($_GET['action'] == 'create')
            {
                try
                {
                    $group = new PWHGroup();
                    $group->SetName($_SESSION['group_name']);
                    $group->SetParentID($_SESSION['parent_group']);
                    $group->AddStudents($_SESSION['students']);
                    $group->Create(true);
                    
                    // Destroys session variables
                    unset($_SESSION['group_name']);
                    unset($_SESSION['parent_group']);
                    unset($_SESSION['students']);
                    
                    PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Cr&eacute;ation groupe " . $group->GetName());
                    redirect("index.php?page=teacher_list_groups");
                }
                catch(Exception $ex)
                {
                    errorReport($ex->getMessage());
                    PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec cr&eacute;ation groupe");
                }
            }
        }
         
        // Creates a new memo for the user     
        $memo = new PWHGroupCreationMemo();
        $memo->SetName($_SESSION['group_name']);
        if(isset($_SESSION['parent_group']))
        {
            $memo->SetParent($_SESSION['parent_group']);
        }
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
        
        $allStudents = $parentGroup->GetStudents();
        if(isset($_SESSION['students']))
        {
            $table = new PWHPersonTable();
            $allStudents = $table->FilterPersons($allStudents, $_SESSION['students']);
        }
    }
    
    if($failed)
    {
        errorReport("Impossible d'afficher la page demand&eacute;e.");
    }

?>
<fieldset>
	<legend>&eacute;tudiants - etape 2/2</legend>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/create_group_students.html', 800, 550);");
        
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
	        echo $index->Html("index.php?page=teacher_create_group_students", $_GET['index'], true, $allStudents);
	    ?>
		<form id="person_index" method="post" action="index.php?page=teacher_create_group_students&amp;index=<?php echo $_GET['index']; ?>&amp;action=add_students">
	        <?php
	            $table = new PWHPersonTable();
	            echo $table->Html($students, "Ajouter +");
	        ?>
	   </form>
	   <form id="submit_group" method="post" action="index.php?page=teacher_create_group_students&amp;action=create&amp;index=<?php echo $_GET['index']; ?>">
            <input onclick="javascript:UserConfirmSubmit();" class="next_form" <?php echo $disabled; ?> type="submit" value="Cr&eacute;er !"/>
        </form>
	</div>
	<?php } ?>
</fieldset>
<script type="text/javascript" charset="iso-8859-1">
<!--
    function MakeIndex(link, letter)
    {
        var form = document.getElementById("person_index");
        var boxs = form.elements;
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
                if(confirm("Vous n'avez pas valid\351 le formulaire en cliquant sur le bouton \"Ajouter +\" ? Voulez-vous le valider avant de cr\351er le groupe ?"))
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
