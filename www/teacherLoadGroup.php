<?php
    require_once(LIB_PATH() . "PWHGroup.php");
    previousPage("teacher_list_groups");
    
    if (isset($_POST['groupName']) && isset($_FILES['uploadedFile']) && $_FILES['uploadedFile']['error'] == 0)
    {
        $fileInfo = pathinfo($_FILES['uploadedFile']['name']);
        $fileExtension = $fileInfo['extension'];
        $validExtensions = array('promo', 'PROMO');
        if (in_array($fileExtension, $validExtensions))
        {
            $group = new PWHGroup($_POST['groupName']);
            $group->ReadFromFile($_FILES['uploadedFile']['tmp_name']);
            $group->Create(true);
        }
    }
?>

<fieldset>
    <legend>new group</legend>
    <form method="post" enctype="multipart/form-data">
        <div class="manager">
	        <table>
		        <tr>
			        <td class="no_align"><label for="nom">Group's name</label></td>
			        <td class="no_align"><input type="text" name="groupName" size="30"/></td>
		        </tr>
		     </table>
		</div>
        <div class="text">
            <h4>Load a .promo file</h4>
            <input type="file" name="uploadedFile"/>	    
            <input type="submit" value="Load"/>         
        </div>
    </form>
</fieldset>

