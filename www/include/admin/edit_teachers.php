<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page edit_teachers");
    
    previousPage('admin_database_management');

          
    // Retrieves the list of teachers sorted, corresponding to the value of the index
    if(isset($_GET['index']))
    {        
        try
        {
            $teachers = PWHEntity::ListAll("PWHTeacher");
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
    }
?>

<fieldset>
	<legend>Edition de profil</legend>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("#");
        
	    displayErrorReport();
	    displaySuccessReport();
	?>
	<h4>Liste des enseignants disponibles</h4>
	<div class="section">
	    <?php 
	        $index = new PWHIndex();
	        echo $index->Html("index.php?page=admin_edit_teachers", $_GET['index'], false, $allTeachers);
	    ?>
        <table class="colored_table underlined_table">
	            <tr>
		            <th>Nom</th>
		            <th>Pr&eacute;nom
		            <th>Editer</th>
	            </tr>
	            <?php
	                if(count($teachers) == 0)
	                { ?>
	                    <tr><td colspan="3">Aucun &eacute;l&eacute;ment disponible</td></tr> 
	          <?php }
	                else
	                {		
	                    $id = 0;           
	                    foreach($teachers as $teacher)
                        { 
                             $class = "";
                             if($id%2 == 1)
                             {
                                $class = ' class="alt"';
                             }
                             ?>
                            <tr<?php echo $class; ?> onclick="CheckBox('<?php echo $id; ?>');">
                                <td><?php echo $teacher->GetLastName(); ?></td>
				                <td><?php echo $teacher->GetFirstName(); ?></td>
				                <td><a href="index.php?page=admin_edit_teacher&amp;teacher_id=<?php echo $teacher->GetID(); ?>&amp;index=<?php echo $_GET['index']; ?>"><img src="img/user_edit.png"/></a></td>
			                </tr>
			           <?php 
			                $id++;
                       }
                  } ?>
        </table>
	</div>
</fieldset>
			

