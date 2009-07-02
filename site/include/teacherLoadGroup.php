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
            try
            {
                $group = new PWHGroup($_POST['groupName']);
                $group->ReadFromFile($_FILES['uploadedFile']['tmp_name']);
                $group->Create(true);
                successReport("Le groupe " . $group->GetName() . " a &eacute;t&eacute; charg&eacute;");
            }
            catch(Exception $ex)
            {
                errorReport($ex->getMessage());
            }
        }
        else
        {
            errorReport($fileExtension . " n'est pas une extension valide");
        }      
    }
?>

<fieldset>
    <legend>nouveau groupe</legend>
    <?php
        displayErrorReport();
        displaySuccessReport();
    ?>
    <form method="post" enctype="multipart/form-data">
        <div class="manager">
	        <table>
		        <tr>
			        <td class="no_align"><label for="nom">Nom du groupe</label></td>
			        <td class="no_align"><input type="text" name="groupName" size="30"/></td>
		        </tr>
		     </table>
		</div>
        <div class="text">
            <h4>Charger un fichier .promo</h4>
            <input type="file" name="uploadedFile"/>	    
            <input type="submit" value="Charger"/>         
        </div>
    </form>
</fieldset>

