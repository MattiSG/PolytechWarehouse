<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page group_settings_students_add");
    
    previousPage("teacher_list_groups");
        
    // Retrieves the concerned group
    if(isset($_GET['group_id']))
    {
        try
        {
            $group = new PWHGroup();
            $group->Read($_GET['group_id']);
            $groups = $group->GetParents();
            $existingStudents = $group->GetStudents();
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage);
        }
    }
    
    // Definition of the parent group
    if(isset($_POST['parentGroup']))
    {
        $parentGroup = (int)$_POST['parentGroup'];
        
    }
    else if(isset($_GET['parent_id']))
    {
        $parentGroup = (int)$_GET['parent_id'];
    }
    else
    {
        $parentGroup = $group->GetParentID();
    }
    
    // Retrieves the list of students of the concerned group and the list of all students
    if(isset($_GET['index']))
    {
        try
        {
            if($parentGroup > 0)
            {
                $parent = new PWHGroup();
                $parent->Read($parentGroup);
                $students = $parent->GetStudents(); 
            }
            else
            {
                $students = PWHEntity::ListAll('PWHStudent');
            }
            $allStudents = $students;
            $table = new PWHPersonTable();
            /*foreach($existingStudents as $existingStudent)
            {
                if($group->StudentExists($existingStudent->GetID()))
                {
                    $allStudents = $table->FilterPersons($allStudents, array($existingStudent->GetID()));
                }
            }*/
            usort($students, "person_comparator");
            $index = new PWHIndex();
            $students = $index->FilterPersons($students, $_GET['index']);
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());
        }
    }
    
    // [LINK ACTION] Add or remove a student from the group
    if(isset($_GET['action']) && $_GET['action'] == 'add')
    {          
        try
        {
            $insert = array();
            foreach($students as $student)
            {
                if(isset($_POST[$student->GetID()]))
                {
                    array_push($insert, (int)$student->GetID());
                }
            }
            $group->AddStudents($insert);
            $group->Update();
            $table = new PWHPersonTable();
            $students = $table->FilterPersons($students, $insert);
            $allStudents = $table->FilterPersons($allStudents, $insert);
            if(count($insert) > 1)
            {
                successReport("Les &eacute;tudiants ont &eacute;t&eacute;s ajout&eacute; au groupe " . $group->GetName() . ".");
            }
            else
            {
                successReport("L'&eacute;tudiant a &eacute;t&eacute; ajout&eacute; au groupe " . $group->GetName() . ".");
            }
            PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Mise &agrave; jour ajout [&eacute;tudiants] groupe " . $group->GetName());
        }
        catch(Exception $ex)
        {
            PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec mise &agrave; jour ajout [&eacute;tudiants] groupe");
            errorReport($ex->getMessage());
        }            
    }
    
    $existingStudents = $group->GetStudents();
    usort($existingStudents, "person_comparator");
    $link = '<a class="next_form" id="toggle" href="javascript:toggle();"><img src="img/zoom_in.png"/>Voir les &eacute;tudiants d&eacute;j&agrave; pr&eacute;sents</a>';
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
	<legend>configuration de <?php echo mb_strtolower($group->GetName()); ?></legend>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/group_settings_students_add.html', 800, 550);");
        
	    displayErrorReport();
	    displaySuccessReport();
	?>
    <div class="tab">
      <ul>
        <li><a href="index.php?page=teacher_group_settings_name&amp;group_id=<?php echo $group->GetID(); ?>">Nom</a></li>
        <?php if($group->GetParentID() == -1)
        { ?>
        <li><a href="index.php?page=teacher_group_settings_student&amp;group_id=<?php echo $group->GetID(); ?>">Cr&eacute;ation rapide d'&eacute;tudiants</a></li>
        <?php } ?>
        <li class="active"><a href="index.php?page=teacher_group_settings_students_add&amp;group_id=<?php echo $group->GetID(); ?>&amp;index=A">Ajout d'&eacute;tudiants</a></li>
        <li><a href="index.php?page=teacher_group_settings_students_remove&amp;group_id=<?php echo $group->GetID(); ?>&amp;index=A">Suppression d'&eacute;tudiants</a></li>
        <li><a href="index.php?page=teacher_group_settings_students_edit&amp;group_id=<?php echo $group->GetID(); ?>&amp;index=A">Edition des profils</a></li>
      </ul>
    </div>
	<div class="section">
	    <form method="post">
	        <div class="input">
	            <label for="parentGroup">Etudiants de:</label>
		        <select name="parentGroup" id="parentGroup">
		            <?php 
                        foreach($groups as $g)
                        { ?>  
		                <option value="<?php echo $g->GetID(); ?>" <?php if($g->GetID() == $parentGroup) { echo 'selected="selected"'; } ?>><?php echo $g->GetName(); ?></option>
		          <?php } ?>
		        </select>
				<input type="submit" value="Choisir"/>
		    </div>
	    </form>
	    <?php 
	        $index = new PWHIndex();
	        echo $index->Html("index.php?page=teacher_group_settings_students_add&amp;parent_id=" . $parentGroup . "&amp;group_id=" . $group->GetID(), $_GET['index'], true, $allStudents);
	    ?>
	    <form id="person_index" method="post" action="index.php?page=teacher_group_settings_students_add&amp;parent_id=<?php echo $parentGroup ?>&amp;group_id=<?php echo $group->GetID(); ?>&amp;index=<?php echo $_GET['index']; ?>&amp;action=add">
            <table class="colored_table underlined_table">
	            <tr>
		            <th>Nom</th>
		            <th>Pr&eacute;nom
		            <th>S&eacute;lection</th>
	            </tr>
	            <?php
	                if(count($students) == 0)
	                { ?>
	                    <tr><td colspan="3">Aucun &eacute;l&eacute;ment disponible</td></tr> 
	          <?php }
	                else
	                {		
	                    $found = false;
	                    $id = 0;           
	                    foreach($students as $student)
                        { 
                             $class = "";
                             if($id%2 == 1)
                             {
                                $class = ' class="alt"';
                             }
                             if(!$group->StudentExists($student->GetID()))
                             { 
                                $found = true;
                             ?>
                            <tr<?php echo $class; ?> onclick="CheckBox('<?php echo $id; ?>');">
                                <td><?php echo $student->GetLastName(); ?></td>
				                <td><?php echo $student->GetFirstName(); ?></td>
				                <td><input onclick="CheckForm('<?php echo $id; ?>');" type="checkbox" id="cb_<?php echo $id; ?>" name="<?php echo $student->GetID(); ?>" id="<?php echo $student->GetID(); ?>"/></td>
			                </tr>
			           <?php 
			                    $id++;
			                }     
                            
                       }
                       if($found)
                       { ?>
                       <tr class="submit_line">
                            <td></td>
                            <td></td>
                            <td><input type="submit" id="add" value="Ajouter +"/></td>
                        </tr> 
                 <?php }
                       else
                       { ?>
                       <tr><td colspan="3">Aucun &eacute;l&eacute;ment disponible</td></tr> 
                      <?php } 
                    } ?>
            </table>
        </form>
	</div>
	<div class="section">
	    <?php echo $link; ?>
	</div>
	<div id="present">
	    <h4>Liste des &eacute;tudiants d&eacute;j&agrave; pr&eacute;sents</h4>
	    <div class="section">
	        <table class="colored_table underlined_table">
	                <tr>
		                <th>Nom</th>
		                <th>Pr&eacute;nom</th>
	                </tr>
	                <?php
	                    if(count($existingStudents) == 0)
	                    { ?>
	                        <tr><td colspan="3">Aucun &eacute;l&eacute;ment disponible</td></tr> 
	              <?php }
	                    else
	                    {		
	                        $id = 0;           
	                        foreach($existingStudents as $existingStudent)
                            { 
                                 $class = "";
                                 if($id%2 == 1)
                                 {
                                    $class = ' class="alt"';
                                 }
                                 ?>
                                <tr<?php echo $class; ?> onclick="CheckBox('<?php echo $id; ?>');">
                                    <td><?php echo $existingStudent->GetLastName(); ?></td>
				                    <td><?php echo $existingStudent->GetFirstName(); ?></td>
			                    </tr>
			               <?php 
			                    $id++;
                           }
                      } ?>
            </table>
        </div>
    </div>
</fieldset>

<script type="text/javascript" charset="iso-8859-1">
<!--
    function CheckForm(index)
    {
        var form = document.getElementById("person_index");
        var input = document.getElementById("add");
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
        var input = document.getElementById("add");
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
    var input = document.getElementById("add");
        
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
<script type="text/javascript">
<!--
    function toggle()
    {
        
        var link = document.getElementById("toggle");
        var divInactive = document.getElementById("present");
        
        if(divInactive.style.display == "")
        {
            divInactive.style.display = "none";        
            link.innerHTML = '<img src="img/zoom_in.png"/>Voir les &eacute;tudiants d&eacute;j&agrave; pr&eacute;sents';
        }
        else
        {
            divInactive.style.display = "";
            link.innerHTML = '<img src="img/zoom_out.png"/>Masquer les &eacute;tudiants d&eacute;j&agrave; pr&eacute;sents';
        }
    }
    
    document.getElementById("present").style.display = "none";
</script>
