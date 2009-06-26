<?php 
    require_once(LIB_PATH() . "PWHStudent.php");
    require_once(LIB_PATH() . "PWHGroup.php");
    previousPage("teacher_list_groups");
    
    $group = new PWHGroup(null);
    $group->Read($_GET['id']);
    
    if(isset($_POST['studentLogin']) && isset($_POST['studentEmail']))
    {
        $student = new PWHStudent($_POST['studentLogin'], $_POST['studentEmail']);
        $student->Create(true);
        
        $group->AddStudents(array($student->GetID()));
        $group->Update();
    }
    
    if(isset($_POST['groupName']))
    {       
        $group->SetName($_POST['groupName']);
        $group->Update();
    }
?>

<fieldset>
	<legend>new student</legend>
	<div class="manager">
	    <form method="post">
		    <table>
		        <tr>
			        <td class="no_align"><label for="nom">Student's login</label></td>
			        <td class="no_align"><input type="text" name="studentLogin" size="20"/></td>
		        </tr>
		        <tr>
			        <td class="no_align"><label for="nom">Student's email</label></td>
			        <td class="no_align"><input type="text" name="studentEmail" size="20"/><input type="submit" value="Add"/></td>
		        </tr>
	        </table>
	    </form>
	</div>
</fieldset>
<fieldset>
    <legend>group</legend>
	<div class="manager">
		<form method="post">
	        <table>
		        <tr>
			        <td class="no_align"><label for="nom">Group's name</label></td>
			        <td class="no_align">
			            <input type="text" name="groupName" size="20" value="<?php echo $group->GetName(); ?>"/>
			            <input type="submit" value="Change"/>
			       </td>
		        </tr>
	        </table>
	    </form>
	    <form method="post">
	        <table>
		        <tr>
			        <th>Name</th>
			        <th>S&eacute;lection</th>
		        </tr>
		        <?php		            
		            $students = PWHStudent::ListStudents();
	                foreach($students as $student)
	                { ?>
	                    <tr>
					        <td><?php echo $student->GetLogin(); ?></td>
					        <td><input type="checkbox" name="<?php echo $student->GetID(); ?>" 
					                    id="<?php echo $student->GetID(); ?>" 
					                    <?php if($group->StudentExists($student->GetID())) { echo '"checked=checked"'; } ?>/>
					       </td>
				        </tr>
                        
                <? } ?>
                <tr>
		            <td colspan="2"><input type="submit" value="Apply"/></td>
			   </tr>
	        </table>
	   </form>
	</div>
</fieldset>

