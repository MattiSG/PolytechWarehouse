<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page subject_settings_name");
    
    previousPage('teacher_list_subjects');
    addPreviousPageParameter('see', 'less');
    $failed = false;
    $subjectName = "???";
    
    // Retrieves the concerned subject
    if(isset($_GET['subject_id']) && PWHEntity::Valid("PWHSubject", $_GET['subject_id']))
    {
        try
        {
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
        // [FORM] Changes the name of the subject
        if(isset($_POST['subjectName']))
        {
            try
            {
                $oldName = $subject->GetName();
                $subject->SetName(stripslashes($_POST['subjectName']));
                $subject->Update();
                $teachers = $subject->GetTeachers();
                PWHEvent::Notify($teachers, TEACHER_TYPE, "La mati&egrave;re " . $oldName . " a &eacute;t&eacute; renomm&eacute;e en " . $subject->GetName()); 
                PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Mise &agrave; jour [nom] mati&egrave;re " . $subject->GetName());
                successReport("La mati&egrave;re " . $oldName . " a &eacute;t&eacute; renomm&eacute;e en " . $subject->GetName() . "."); 
            }
            catch(Exception $ex)
            {
                PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec mise &agrave; jour [nom] mati&egrave;re");
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
        echo $help->Html("javascript:popup('include/teacher/help/subject_settings_name.html', 800, 600);");
        
	    displayErrorReport();
	    displaySuccessReport();
	    
	    if(!$failed)
	    {
	?>
	<div class="tab">
      <ul>
        <li class="active"><a href="index.php?page=teacher_subject_settings_name&amp;subject_id=<?php echo $subject->GetID(); ?>">Nom</a></li>
        <li><a href="index.php?page=teacher_subject_settings_teachers_add&amp;subject_id=<?php echo $subject->GetID(); ?>&amp;index=A">Ajout d'enseignants</a></li>
        <li><a href="index.php?page=teacher_subject_settings_teachers_remove&amp;subject_id=<?php echo $subject->GetID(); ?>&amp;index=A">Suppression d'enseignants</a></li>
      </ul>
    </div>
	<div class="section">
		<form method="post" action="index.php?page=teacher_subject_settings_name&amp;subject_id=<?php echo $subject->GetID() ?>">
            <div class="input">
                <label for="subjectName">Nom de la mati&egrave;re:</label>
			    <input id="subject_name" type="text" name="subjectName" value="<?php echo $subject->GetName(); ?>" size="20"/><input id="change" type="submit" name="change" value="Changer !"/>
	        </div>
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
<script type="text/javascript">
<!--
    function CheckForm()
    {
        var inputName = document.getElementById("subject_name");
        var inputSubmit = document.getElementById("change");
        if(inputName.value == "")
        {
            inputSubmit.disabled = true;
        }
        else
        {
            inputSubmit.disabled = false;
        }
    }
    
    var inputName = document.getElementById("subject_name");
    var inputSubmit = document.getElementById("change");
    inputName.onkeyup = CheckForm;
    if(inputName.value == "")
    {
        inputSubmit.disabled = true;
    }
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
