<?php
    require_once(LIB_PATH() . "PWHWork.php");
    require_once(LIB_PATH() . "PWHSubject.php");
    previousPage('teacher_list_works'); 

    if(isset($_GET['subject_id']))
    {
        addPreviousPageParameter('subject_id', isset($_GET['subject_id']));
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
    
    if(isset($_POST['workName']) && isset($_POST['extraTime']) && isset($_POST['size'])
         && isset($_POST['format']) && isset($_POST['groupMin']) && isset($_POST['groupMax']))
    {
        try
        {
            $work = new PWHWork($_POST['workName'], $_POST['extraTime'], $_POST['size'], 
                                    $_POST['format'], $_POST['groupMin'], $_POST['groupMax']);
            $work->Create(true);
            $subject->AddWorks(array($work->GetID()));
            $subject->Update();
            successReport("Le travail " . $work->GetName() . " a &eacute;t&eacute; cr&eacute;e dans la mati&egrave;re " . $subject->GetName());
        }
        catch(Exception $ex)
        {
             errorReport($ex->getMessage());
        }
    }
?>

<fieldset>
	<legend>nouveau travail</legend>
	<?php
	    displayErrorReport();
	    displaySuccessReport();
	?>
	<div class="manager">
		<form method="post">
	        <table>
		        <tr>
			        <td class="no_align"><label for="workName">Nom du travail</label></td>
			        <td class="no_align"><input type="text" id="workName" name="workName" size="20"/></td>
		        </tr>
		        <tr>
			        <td class="no_align"><label for="extraTime">Jour de gr&acirc;ce</label></td>
			        <td class="no_align"><input type="text" id="extraTime" name="extraTime" size="20"/></td>
		        </tr>
		        <tr>
			        <td class="no_align"><label for="size">Taille</label></td>
			        <td class="no_align"><input type="text" id="size" name="size" size="20"/></td>
		        </tr>
		        <tr>
			        <td class="no_align"><label for="format">Format</label></td>
			        <td class="no_align"><input type="text" id="format" name="format" size="20"/></td>
		        </tr>
		        <tr>
			        <td class="no_align"><label for="groupMin">Nombre minimum d'&eacute;tudiants</label></td>
			        <td class="no_align"><input type="text" id="groupMin" name="groupMin" size="20"/></td>
		        </tr>
		        <tr>
			        <td class="no_align"><label for="groupMax">Nombre maximum d'&eacute;tudiants</label></td>
			        <td class="no_align">
			            <input type="text" id="groupMax" name="groupMax" size="20"/>
			            <input type="submit" name="create" value="Cr&eacute;er"/>
			        </td>
		        </tr>
	        </table>
	    </form>
    <div>
</fieldset>
