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
    
    if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['work_id']))
    {
        try
        {
            $subject->RemoveWorks(array($_GET['work_id']));
            $subject->Update();   
            $work = new PWHWork(null, null, null, null, null, null);
            $work->Read($_GET['work_id']);
            $work->Delete();
            successReport("Le travail " . $work->GetName() . " a &eacute;t&eacute; supprim&eacute; de la mati&egrave;re " . $subject->GetName());
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());
        }
    }
?>

<fieldset>
	<legend>travaux de <?php echo mb_strtolower($subject->GetName()); ?></legend>
	<?php
	    displayErrorReport();
	    displaySuccessReport();
    ?>
	<div class="list">
	    <ul>
	        <li><a href="index.php?page=teacher_create_work&amp;subject_id=<?php echo $subject->GetID() ?>">
	        <img src="img/package_add.png"/>Cr&eacute;er un nouveau travail</a></li>
	    </ul>
	<div class="manager">
        <table>
            <tr>
	            <th>Nom</th>
	            <th>Configurer</th>
	            <th>Supprimer</th>
            </tr>
            <?php
                $ids = $subject->GetWorksIDs();
                foreach($ids as $id)
                { 
                    $work = new PWHWork(null, null, null, null, null, null);
                    $work->Read($id);
                    ?>
                    <tr>
	                    <td>
	                        <a href="index.php?page=teacher_list_deliveries&amp;subject_id=<?php echo $subject->GetID() ?>&amp;work_id=<?php echo $work->GetID() ?>">
	                            <img src="<?php echo IMG_PATH() ?>bullet_go.png"/><?php echo $work->GetName() ?>
	                        </a>
	                    </td>
	                    <td>
	                        <a href="index.php?page=teacher_work_settings&amp;subject_id=<?php echo $subject->GetID() ?>&amp;work_id=<?php echo $work->GetID() ?>">
	                            <img src="<?php echo IMG_PATH() ?>bullet_wrench.png"/>
	                        </a>
	                    </td>
	                    <td>
	                        <a href="index.php?page=teacher_list_works&amp;action=delete&amp;subject_id=<?php echo $subject->GetID() ?>&amp;work_id=<?php echo $work->GetID() ?>">
	                            <img src="<?php echo IMG_PATH() ?>cross.png"/>
	                        </a>
	                    </td>
                    </tr>
            <? } ?>
        </table>
    </div>
</fieldset>

