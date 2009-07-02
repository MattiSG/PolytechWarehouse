<?php
    require_once(LIB_PATH() . "PWHSubject.php");
    previousPage('teacher_list_subjects');

    if(isset($_POST['subjectName']))
    {
        try
        {
            $subject = new PWHSubject($_POST['subjectName']);
            $subject->Create(true);
            successReport("La mati&egrave;re " . $subject->GetName() . " a &eacute;t&eacute; cr&eacute&eacute;e");
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());
        }
    }

?>

<fieldset>
	<legend>nouvelle mati&egrave;re</legend>
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
			            <input type="text" name="subjectName" size="20"/>
			            <input type="submit" name="create" value="Cr&eacute;er"/>
			        </td>
		        </tr>
	        </table>
	   </form>
	</div>
</fieldset>

