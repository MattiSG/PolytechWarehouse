<?php
    require_once(LIB_PATH() . "PWHSubject.php");
    previousPage('teacher_list_subjects');

    if(isset($_GET['subject_id']))
    {
        try
        {
            $subject = new PWHSubject(null);
            $subject->Read($_GET['subject_id']);
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());
        }
    }
    
    if(isset($_POST['subjectName']))
    {
        try
        {
            $oldName = $subject->GetName();
            $subject->SetName($_POST['subjectName']);
            $subject->Update();
            successReport("La mati&egrave;re " . $oldName . " a &eacute;t&eacute; renomm&eacute;e en " . $subject->GetName()); 
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());
        }
    }
?>

<fieldset>
	<legend>configuration de <?php echo mb_strtolower($subject->GetName()); ?></legend>
	<?php
	    displayErrorReport();
	    displaySuccessReport();
	?>
	<div class="manager">
		<form method="post">
	        <table>
		        <tr>
			        <td class="no_align"><label for="subjectName">Nom de la mati&egrave;re</label></td>
			        <td class="no_align">
			            <input type="text" name="subjectName" value="<?php echo $subject->GetName(); ?>" size="20"/>
			            <input type="submit" name="change" value="Changer"/>
			        </td>
		        </tr>
	        </table>
	   </form>
	</div>
</fieldset>

