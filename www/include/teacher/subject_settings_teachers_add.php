<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page subject_settings_teachers_add");
    
    previousPage('teacher_list_subjects');
    addPreviousPageParameter('see', 'less');
    $failed = false;
    $subjectName = "???";
    
    // Retrieves the list of all teachers
    try
    {
        $teachers = PWHEntity::ListAll('PWHTeacher');
        $allTeachers = $teachers;
        usort($teachers, "person_comparator");
        $index = new PWHIndex();
        $teachers = $index->FilterPersons($teachers, $_GET['index']);
    }
    catch(Exception $ex)
    {
        errorReport($ex->getMessage());
        $failed = true;
    }
    
    // Retrieves the concerned subject
    if(isset($_GET['subject_id']) && PWHEntity::Valid("PWHGroup", $_GET['subject_id']))
    {
        try
        {
            $subject = new PWHSubject();
            $subject->Read($_GET['subject_id']);
            $existingTeachers = $subject->GetTeachers();
            
            $table = new PWHPersonTable();
            foreach($existingTeachers as $existingTeacher)
            {
                $allTeachers = $table->FilterPersons($allTeachers, array($existingTeacher->GetID()));
            }
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
        // [FORM] Adds teachers responsible of the subject
        if(isset($_GET['action']))
        {              
            try
            {
                if($_GET['action'] == 'add')
                {
                    $insert = array();
                    foreach($teachers as $teacher)
                    {
                        if(isset($_POST[$teacher->GetID()]))
                        {
                            array_push($insert, (int)$teacher->GetID());
                        }
                    }
                    $persons = array();
                    foreach($insert as $id)
                    {
                        $person = new PWHTeacher();
                        $person->Read($id);
                        array_push($persons, $person);
                    }
                    
                    $targets = $subject->GetTeachers();
                    $subject->AddTeachers($insert);
                    $subject->Update();
                    PWHEvent::Notify($targets, TEACHER_TYPE, "L'ensemble des enseignants responsables de la mati&egrave;re " . $subject->GetName() . " a &eacute;t&eacute; modifi&eacute;");
                    PWHEvent::Notify($persons, TEACHER_TYPE, "Vous avez &eacute;t&eacute; ajout&eacute; &agrave; la mati&egrave;re " . $subject->GetName());
                
                    if(count($insert) > 1)
                    {
                        successReport("Les enseignants ont &eacute;t&eacute; ajout&eacute;s &agrave; la mati&egrave;re " . $subject->GetName() . ".");
                    }
                    else
                    {
                        successReport("L'enseignant a &eacute;t&eacute; ajout&eacute; &agrave; la mati&egrave;re " . $subject->GetName() . ".");
                    }
                    PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Mise &agrave; jour ajout [enseignants] mati&egrave;re " . $subject->GetName());
                }
            }
            catch(Exception $ex)
            {
                PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec mise &agrave; jour ajout [enseignants] mati&egrave;re");
                errorReport($ex->getMessage());
            }     
        }
        
        $existingTeachers = $subject->GetTeachers();
        usort($existingTeachers, "person_comparator");
        $link = '<a class="next_form" id="toggle" href="javascript:toggle();"><img src="img/zoom_in.png"/>Voir les enseignants d&eacute;j&agrave; responsables</a>';
        $subjectName = mb_strtolower($subject->GetName());
    }
    
    if($failed)
    {
        errorReport("Impossible d'afficher la page demand&eacute;e.");
    }  
?>
<fieldset>
	<legend>configuration de <?php echo $subjectName; ?></legend>
	
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/subject_settings_teachers_add.html', 800, 600);");
        
	   displayErrorReport();
	   displaySuccessReport();
	   
	   if(!$failed)
	   {
	?>
	<div class="tab">
      <ul>
        <li><a href="index.php?page=teacher_subject_settings_name&amp;subject_id=<?php echo $subject->GetID(); ?>">Nom</a></li>
        <li class="active"><a href="index.php?page=teacher_subject_settings_teachers_add&amp;subject_id=<?php echo $subject->GetID(); ?>&amp;index=A">Ajout d'enseignants</a></li>
        <li><a href="index.php?page=teacher_subject_settings_teachers_remove&amp;subject_id=<?php echo $subject->GetID(); ?>&amp;index=A">Suppression d'enseignants</a></li>
      </ul>
    </div>
	<div class="section">
	    <?php
	        $index = new PWHIndex();
	        echo $index->Html("index.php?page=teacher_subject_settings_teachers_add&amp;subject_id=" . $subject->GetID(), $_GET['index'], true, $allTeachers);
	    ?>
	    <form id="person_index" method="post" action="index.php?page=teacher_subject_settings_teachers_add&amp;subject_id=<?php echo $subject->GetID(); ?>&amp;index=<?php echo $_GET['index']; ?>&amp;action=add">
	         <table class="colored_table underlined_table">
	            <tr>
		            <th>Nom</th>
		            <th>Pr&eacute;nom
		            <th>S&eacute;lection</th>
	            </tr>
	            <?php
	                if(count($teachers) == 0)
	                { ?>
	                    <tr><td colspan="3">Aucun &eacute;l&eacute;ment disponible</td></tr> 
	          <?php }
	                else
	                {		
	                    $found = false;     
	                    $id = 0;       
	                    foreach($teachers as $teacher)
                        {
                             $class = "";
                             if($id%2 == 1)
                             {
                                $class = ' class="alt"';
                             }
                             if(!$subject->TeacherExists($teacher->GetID()))
                             { 
                                $found = true;
                             ?>
                            <tr<?php echo $class; ?> onclick="CheckBox('<?php echo $id; ?>');">
                                <td><?php echo $teacher->GetLastName(); ?></td>
				                <td><?php echo $teacher->GetFirstName(); ?></td>
				                <td><input onclick="CheckForm('<?php echo $id; ?>');" type="checkbox" id="cb_<?php echo $id; ?>" name="<?php echo $teacher->GetID(); ?>" id="<?php echo $teacher->GetID(); ?>"/></td>
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
	    <h4>Liste des enseignants d&eacute;j&agrave; responsables</h4>
	    <div class="section">
	        <table class="colored_table underlined_table">
	                <tr>
		                <th>Nom</th>
		                <th>Pr&eacute;nom</th>
	                </tr>
	                <?php
	                    if(count($existingTeachers) == 0)
	                    { ?>
	                        <tr><td colspan="3">Aucun &eacute;l&eacute;ment disponible</td></tr> 
	              <?php }
	                    else
	                    {		
	                        $id = 0;           
	                        foreach($existingTeachers as $existingTeacher)
                            { 
                                 $class = "";
                                 if($id%2 == 1)
                                 {
                                    $class = ' class="alt"';
                                 }
                                 ?>
                                <tr<?php echo $class; ?> onclick="CheckBox('<?php echo $id; ?>');">
                                    <td><?php echo $existingTeacher->GetLastName(); ?></td>
				                    <td><?php echo $existingTeacher->GetFirstName(); ?></td>
			                    </tr>
			               <?php 
			                    $id++;
                           }
                      } ?>
            </table>
        </div>
    </div>
    <?php } ?>
</fieldset>
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
            link.innerHTML = '<img src="img/zoom_in.png"/>Voir les enseignants d&eacute;j&agrave; responsables';
        }
        else
        {
            divInactive.style.display = "";
            link.innerHTML = '<img src="img/zoom_out.png"/>Masquer les enseignants d&eacute;j&agrave; responsables';
        }
    }
    
    document.getElementById("present").style.display = "none";
</script>
