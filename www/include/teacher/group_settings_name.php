<?php 
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page group_settings_name");
    
    previousPage("teacher_list_groups");
      
    // Retrieves the concerned group
    if(isset($_GET['group_id']))
    {
        try
        {
            $group = new PWHGroup();
            $group->Read($_GET['group_id']);
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage);
        }
    }
    
    // [FORM] Changes the name of the group
    if(isset($_POST['groupName']))
    {       
        try
        {
            $oldName = $group->GetName();
            $group->SetName(stripslashes($_POST['groupName']));
            $group->Update();
            PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Mise &agrave; jour [nom] groupe " . $group->GetName());
            successReport("Le groupe " . $oldName . " a &eacute;t&eacute; renomm&eacute; en " . $group->GetName() . ".");
        }
        catch(Exception $ex)
        {
             PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec mise &agrave; jour [nom] groupe");
            errorReport($ex->getMessage());
        }
    }
    
    $existingStudents = $group->GetStudents();
    usort($existingStudents, "person_comparator");
    $link = '<a class="next_form" id="toggle" href="javascript:toggle();"><img src="img/zoom_in.png"/>Voir les &eacute;tudiants d&eacute;j&agrave; pr&eacute;sents</a>';
?>

<fieldset>
	<legend>configuration de <?php echo mb_strtolower($group->GetName()); ?></legend>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/group_settings_name.html', 800, 550);");
        
	    displayErrorReport();
	    displaySuccessReport();
	?>
	<div class="tab">
      <ul>
        <li class="active"><a href="index.php?page=teacher_group_settings_name&amp;group_id=<?php echo $group->GetID(); ?>">Nom</a></li>
        <?php if($group->GetParentID() == -1)
        { ?>
        <li><a href="index.php?page=teacher_group_settings_student&amp;group_id=<?php echo $group->GetID(); ?>">Cr&eacute;ation rapide d'&eacute;tudiants</a></li>
        <?php } ?>
        <li><a href="index.php?page=teacher_group_settings_students_add&amp;group_id=<?php echo $group->GetID(); ?>&amp;index=A&amp;index_alt=A">Ajout d'&eacute;tudiants</a></li>
        <li><a href="index.php?page=teacher_group_settings_students_remove&amp;group_id=<?php echo $group->GetID(); ?>&amp;index=A">Suppression d'&eacute;tudiants</a></li>
        <li><a href="index.php?page=teacher_group_settings_students_edit&amp;group_id=<?php echo $group->GetID(); ?>&amp;index=A">Edition des profils</a></li>
      </ul>
    </div>
	<div class="section">
		<form method="post">            <div class="input">            
                <label for="groupName">Nom du groupe:</label>
                <input type="text" id="group_name" name="groupName" size="20" value="<?php echo $group->GetName(); ?>"/><input id="change" type="submit" value="Changer !"/>
	        </div>
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

<script type="text/javascript">
<!--
    function CheckForm()
    {
        var inputName = document.getElementById("group_name");
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
    
    var inputName = document.getElementById("group_name");
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
