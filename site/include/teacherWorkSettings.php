<?php
    require_once(LIB_PATH() . "PWHWork.php");
    previousPage('teacher_list_works');

    if(isset($_GET['subject_id']) && isset($_GET['work_id']))
    {
        try
        {
            addPreviousPageParameter('subject_id', $_GET['subject_id']);
            addPreviousPageParameter('work_id', $_GET['work_id']);
            $work = new PWHWork(null, null, null, null, null, null);
            $work->Read($_GET['work_id']);
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
            $work->SetName($_POST['workName']);
            $work->SetExtraTime($_POST['extraTime']);
            $work->SetSize($_POST['size']);
            $work->SetFormat($_POST['format']);
            $work->SetGroupMin($_POST['groupMin']);
            $work->SetGroupMax($_POST['groupMax']);       
            $work->Update();
            successReport("Les contraintes du travail " . $work->GetName() . " ont &eacute;t&eacute; mises &agrave; jour");
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());
        }
    }
?>

<fieldset>
	<legend>configuration de <?php echo mb_strtolower($work->GetName()); ?></legend>
	<?php
	    displayErrorReport();
	    displaySuccessReport();
	?>
	<div class="manager">
		<form method="post">
	        <table>
	            <tr>
			        <td class="no_align"><label for="workName">Nom du travail</label></td>
			        <td class="no_align"><input type="text" name="workName" id="workName" size="20" value="<?php echo $work->GetName(); ?>"/></td>
		        </tr>
		        <tr>
			        <td class="no_align"><label for="extraTime">Jour de gr&acirc;ce</label></td>
			        <td class="no_align"><input type="text" name="extraTime" id="extraTime" size="20" value="<?php echo $work->GetExtraTime(); ?>"/></td>
		        </tr>
		        <tr>
			        <td class="no_align"><label for="size">Taille</label></td>
			        <td class="no_align"><input type="text" name="size" id="size" size="20" value="<?php echo $work->GetSize(); ?>"/></td>
		        </tr>
		        <tr>
			        <td class="no_align"><label for="format">Format</label></td>
			        <td class="no_align"><input type="text" name="format" id="format" size="20" value="<?php echo $work->GetFormat(); ?>"/></td>
		        </tr>
		        <tr>
			        <td class="no_align"><label for="groupMin">Nombre minimum d'&eacute;tudiants</label></td>
			        <td class="no_align"><input type="text" name="groupMin" id="groupMin" size="20" value="<?php echo $work->GetGroupMin(); ?>"/></td>
		        </tr>
		        <tr>
			        <td class="no_align"><label for="groupMax">Nombre maximum d'&eacute;tudiants</label></td>
			        <td class="no_align">
			            <input type="text" name="groupMax" id="groupMax" size="20" value="<?php echo $work->GetGroupMax(); ?>"/>
			            <input type="submit" name="apply" value="Appliquer"/>
			        </td>
		        </tr>
	        </table>
        </form>
    </div>
</fieldset>


