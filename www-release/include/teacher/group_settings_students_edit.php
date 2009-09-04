<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page group_settings_students_edit");    
     
    previousPage('teacher_list_groups');
 
    // Retrieves the list of students sorted, corresponding to the value of the index
    if(isset($_GET['index']))
    {        
        try
        {
            if(isset($_GET['group_id']))
            {
                $group = new PWHGroup();
                $group->Read($_GET['group_id']);
                $students = $group->GetStudents();
                $allStudents = $students;
                usort($students, "person_comparator");
                $index = new PWHIndex();
                $students = $index->FilterPersons($students, $_GET['index']);
            }
            else
            {
                $students = array();
            }
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());
            $students = array();
        }
    }
?>

<fieldset>
	<legend>Edition de profil</legend>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/group_settings_students_edit.html', 800, 550);");
        
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
        <li><a href="index.php?page=teacher_group_settings_students_add&amp;group_id=<?php echo $group->GetID(); ?>&amp;index=A&amp;index_alt=A">Ajout d'&eacute;tudiants</a></li>
        <li><a href="index.php?page=teacher_group_settings_students_remove&amp;group_id=<?php echo $group->GetID(); ?>&amp;index=A">Suppression d'&eacute;tudiants</a></li>
        <li class="active"><a href="index.php?page=teacher_group_settings_students_edit&amp;group_id=<?php echo $group->GetID(); ?>&amp;index=A">Edition des profils</a></li>
      </ul>
    </div>
	<div class="section">
	    <?php 
	        $index = new PWHIndex();
	        echo $index->Html("index.php?page=teacher_group_settings_students_edit&amp;group_id=" . $group->GetID(), $_GET['index'], false, $allStudents);
	    ?>
        <table class="colored_table underlined_table">
	            <tr>
		            <th>Nom</th>
		            <th>Pr&eacute;nom
		            <th>Editer</th>
	            </tr>
	            <?php
	                if(count($students) == 0)
	                { ?>
	                    <tr><td colspan="3">Aucun &eacute;l&eacute;ment disponible</td></tr> 
	          <?php }
	                else
	                {		
	                    $id = 0;           
	                    foreach($students as $student)
                        { 
                             $class = "";
                             if($id%2 == 1)
                             {
                                $class = ' class="alt"';
                             }
                             ?>
                            <tr<?php echo $class; ?> onclick="CheckBox('<?php echo $id; ?>');">
                                <td><?php echo $student->GetLastName(); ?></td>
				                <td><?php echo $student->GetFirstName(); ?></td>
				                <td><a href="index.php?page=teacher_group_settings_student_edit&amp;group_id=<?php echo $group->GetID(); ?>&amp;student_id=<?php echo $student->GetID(); ?>&amp;index=<?php echo $_GET['index']; ?>"><img src="img/user_edit.png"/></a></td>
			                </tr>
			           <?php 
			                $id++;
                       }
                  } ?>
        </table>
	</div>
</fieldset>
			

