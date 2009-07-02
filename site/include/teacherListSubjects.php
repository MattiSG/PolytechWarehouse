<?php
    require_once(LIB_PATH() . "PWHSubject.php");
    previousPage('teacher_home');
    
    if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['subject_id']))
    {
        try 
        {
            $subject = new PWHSubject(null);
            $subject->Read($_GET['subject_id']);
            $subject->Delete();
            successReport("La mati&egrave;re " . $subject->GetName() . " a &eacute;t&eacute; supprim&eacute;e");
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());
        }
    }
    
    try
    {
        $subjects = PWHSubject::ListSubjects();
    }
    catch(Exception $ex)
    {
        errorReport($ex->getMessage());
        $subjects = array();
    }
?>

<fieldset>
	<legend>mati&egrave;res</legend>
	<?php
	    displayErrorReport();
	    displaySuccessReport();
	?>
	<div class="list">
	    <ul>
	        <li><a href="index.php?page=teacher_create_subject"><img src="img/book_add.png"/>Cr&eacute;er une nouvelle mati&egrave;re</a></li>
	    </ul>
	<div class="manager">
        <table>
            <tr>
	            <th>Nom</th>
	            <th>Configurer</th>
	            <th>Supprimer</th>
            </tr>
            <?php
                foreach($subjects as $subject)
                { 
                    ?>  
                    <tr>
	                    <td>
	                        <a href="index.php?page=teacher_list_works&amp;subject_id=<?php echo $subject->GetID(); ?>">
	                            <img src="<?php echo IMG_PATH() ?>bullet_go.png"/><?php echo $subject->GetName() ?>
	                        </a>
	                    </td>
	                    <td>
	                        <a href="index.php?page=teacher_subject_settings&amp;subject_id=<?php echo $subject->GetID(); ?>">
	                            <img src="<?php echo IMG_PATH() ?>bullet_wrench.png"/>
	                        </a>
	                    </td>
	                    <td>
	                        <a href="index.php?page=teacher_list_subjects&amp;action=delete&amp;subject_id=<?php echo $subject->GetID(); ?>">
	                            <img src="<?php echo IMG_PATH() ?>cross.png"/>
	                        </a>
	                    </td>
                    </tr>
            <? } ?>
        </table>
    </div>
</fieldset>

