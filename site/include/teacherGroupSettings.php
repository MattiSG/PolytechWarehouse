<?php 
    require_once(LIB_PATH() . "PWHStudent.php");
    require_once(LIB_PATH() . "PWHGroup.php");
    previousPage("teacher_list_groups");
    
    if(isset($_GET['group_id']))
    {
        try
        {
            $group = new PWHGroup(null);
            $group->Read($_GET['group_id']);
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage);
        }
    }
    
    if(isset($_POST['studentLogin']) && isset($_POST['studentEmail']))
    {
        try
        {
            $student = new PWHStudent($_POST['studentLogin'], $_POST['studentEmail']);
            $student->Create(true);        
            $group->AddStudents(array($student->GetID()));
            $group->Update();
            successReport("L'&eacute;tudiant " . $student->GetLogin() . 
                            " a &eacute;t&eacute; cr&eacute;e et ajout&eacute; au groupe " . $group->GetName());
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());
        }
    }
    
    if(isset($_POST['groupName']))
    {       
        try
        {
            $oldName = $group->GetName();
            $group->SetName($_POST['groupName']);
            $group->Update();
            successReport("Le groupe " . $oldName . " a &eacute;t&eacute; renomm&eacute; en " . $group->GetName());
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());
        }
    }
    

    if(isset($_GET['action']) && isset($_GET['student_id']))
    {   
             
        try
        {
            if($_GET['action'] == 'remove')
            {
                $student = new PWHStudent(null, null);
                $student->Read($_GET['student_id']);
                $group->RemoveStudents(array((int)$_GET['student_id']));
                $group->Update();
                successReport("L'&eacute;tudiant " . $student->GetLogin() . " a &eacute;t&eacute; supprim&eacute; du groupe " . $group->GetName());
            }
            else if($_GET['action'] == 'add')
            {
                $student = new PWHStudent(null, null);
                $student->Read($_GET['student_id']);
                $group->AddStudents(array((int)$_GET['student_id']));
                $group->Update();
                successReport("L'&eacute;tudiant " . $student->GetLogin() . " a &eacute;t&eacute; ajout&eacute; au groupe " . $group->GetName());
            }
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());
        }    
        
    }
?>

<fieldset>
	<legend>configuration de <?php echo mb_strtolower($group->GetName()); ?></legend>
	<?php
	    displayErrorReport();
	    displaySuccessReport();
	?>
	<div class="manager">
	    <form method="post">
		    <table>
		        <tr>
			        <td class="no_align"><label for="studentLogin">Identifiant de l'&eacute;tudiant</label></td>
			        <td class="no_align"><input type="text" id="studentLogin" name="studentLogin" size="20"/></td>
		        </tr>
		        <tr>
			        <td class="no_align"><label for="studentEmail">Email de l'&eacute;tudiant</label></td>
			        <td class="no_align"><input type="text" id="studentEmail" name="studentEmail" size="20"/><input type="submit" value="Ajouter"/></td>
		        </tr>
	    </form>
		        <tr>
		            <form method="post">
			            <td class="no_align"><label for="groupName">Nom du groupe</label></td>
			            <td class="no_align">
			                <input type="text" id="groupName" name="groupName" size="20" value="<?php echo $group->GetName(); ?>"/><input type="submit" value="Changer"/>
			           </td>
			       </form>
		        </tr>
	        </table>
    </div>
</fieldset>

<?php $students = PWHStudent::ListStudents(); ?>

<fieldset>
    <legend>&eacute;tudiants de <?php echo mb_strtolower($group->GetName()); ?></legend>
	<div class="manager">
	    <form method="post">
	        <table>
		        <tr>
			        <th>Nom</th>
			        <th>Supprimer</th>
		        </tr>
		        <?php		            
	                foreach($students as $student)
	                { 
	                     if($group->StudentExists($student->GetID()))
	                     { ?>
	                    <tr>
					        <td><?php echo $student->GetLogin(); ?></td>
					        <td>
					            <a href="index.php?page=teacher_group_settings&amp;action=remove&amp;group_id=<?php echo $group->GetID() ?>&amp;student_id=<?php echo $student->GetID() ?>">
		                        <img src="<?php echo IMG_PATH() ?>remove.png"/>
		                    </a>
					       </td>
				        </tr>
				   <?php }     
                   } ?>
	        </table>
	   </form>
	</div>
</fieldset>
<fieldset>
    <legend>autres &eacute;tudiants</legend>
	<div class="manager">
	    <form method="post">
	        <table>
		        <tr>
			        <th>Nom</th>
			        <th>Ajouter</th>
		        </tr>
		        <?php		            
		            foreach($students as $student)
	                { 
	                     if(!$group->StudentExists($student->GetID()))
	                     { ?>
	                    <tr>
					        <td><?php echo $student->GetLogin(); ?></td>
					        <td>
					            <a href="index.php?page=teacher_group_settings&amp;action=add&amp;group_id=<?php echo $group->GetID() ?>&amp;student_id=<?php echo $student->GetID() ?>">
		                        <img src="<?php echo IMG_PATH() ?>add.png"/>
		                    </a>
					       </td>
				        </tr>
				   <?php }     
                   } ?>
	        </table>
	   </form>
	</div>
</fieldset>

