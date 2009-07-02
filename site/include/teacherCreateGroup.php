<?php 
    require_once(LIB_PATH() . "PWHStudent.php");
    require_once(LIB_PATH() . "PWHGroup.php");
    previousPage('teacher_list_groups');
    
    if(isset($_POST['parentGroup']))
    {
        $parentGroup = $_POST['parentGroup'];       
    }
    else
    {
        $parentGroup = 0;
    }
    
    if(isset($_POST['groupID']) && isset($_POST['groupName']))
    {
        try 
        {
            $insert = array();
            if($_POST['groupID'] == 0)
            {
                $students = PWHStudent::ListStudents();
                foreach($students as $student)
                {
                    if(isset($_POST[$student->GetID()]))
                    {
                        array_push($insert, (int)$student->GetID());
                    }
                }
            }
            else
            {
                $group = new PWHGroup(null);
                $group->Read((int)$_POST['groupID']);
                $ids = $group->GetStudentsIDs();
                foreach($ids as $id)
                {
                    if(isset($_POST[$id]))
                    {
                        array_push($insert, (int)$id);
                        echo "push student " . $id . "<br/>"; 
                    }
                }
            }
            
            $newGroup = new PWHGroup($_POST['groupName']);
            $newGroup->AddStudents($insert);
            $newGroup->Create(true);
            successReport("Le groupe " . $newGroup->GetName() . " a &eacute;t&eacute; cr&eacute;e"); 
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());   
        }
    }
?>

<fieldset>
	<legend>nouveau groupe</legend>
	<?php
	    displayErrorReport();
	    displaySuccessReport();
	?>
	<div class="manager">
	    <form method="post">
	        <table>
	            <tr>
			        <td class="no_align"><label for="parentGroup">Groupe parent</label></td>
			        <td class="no_align">
				        <select name="parentGroup" id="parentGroup">
				            <option value="0">_</option>
				            <?php 
				                $groups = PWHGroup::ListGroups();
	                            foreach($groups as $group)
	                            { ?>
				                <option value="<?php echo $group->GetID(); ?>"><?php echo $group->GetName(); ?></option>
				          <?php } ?>
				        </select>
				        <input type="submit" value="Afficher"/>
			        </td>
		        </tr>
	    </form>
		<form method="post">
		    <input type="hidden" name="groupID" value="<?php echo $parentGroup; ?>"/>
		        <tr>
			        <td class="no_align"><label for="groupName">Nom du groupe</label></td>
			        <td class="no_align"><input type="text" name="groupName" size="20"/></td>
		        </tr>
	        </table>
	        <table>
		        <tr>
			        <th>Nom</th>
			        <th>S&eacute;lection</th>
		        </tr>
			    <?php		            
                $students = PWHStudent::ListStudents();
                if($parentGroup != 0)
                {
                    $group = new PWHGroup(null);
                    $group->Read($parentGroup);
                    
                    foreach($students as $student)
                    { 
                        if($group->StudentExists($student->GetID()))
                        { ?>
                        <tr>
	                        <td><?php echo $student->GetLogin(); ?></td>
	                        <td><input type="checkbox" name="<?php echo $student->GetID(); ?>" 
	                                    id="<?php echo $student->GetID(); ?>"/>
	                       </td>
                        </tr>
                   <?php }
                    }
                }
                else
                {
                    foreach($students as $student)
                    { ?>
                        <tr>
	                        <td><?php echo $student->GetLogin(); ?></td>
	                        <td><input type="checkbox" name="<?php echo $student->GetID(); ?>" 
	                                    id="<?php echo $student->GetID(); ?>"/>
	                       </td>
                        </tr>
              <?php }
                } ?>
		        <tr>
		            <td></td>
		            <td><input type="submit" id="create" value="Cr&eacute;er"/></td>
		        </tr>
	        </table>
	   </form>
	</div>
</fieldset>
			

