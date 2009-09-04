<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page display_delivery");
    
    previousPage('teacher_list_deliveries');
    addPreviousPageParameter('subject_id', $_GET['subject_id']);
    addPreviousPageParameter('work_id', $_GET['work_id']);
    addPreviousPageParameter('delivery_id', $_GET['delivery_id']);    

    if(isset($_GET['subject_id']) && isset($_GET['work_id']) && isset($_GET['delivery_id']))
    {
        try
        {
            $subject = new PWHSubject();
            $subject->Read($_GET['subject_id']);
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());
        }
        
        try
        {
            $work = new PWHWork();
            $work->Read($_GET['work_id']);
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());
        }
        
        try
        {
            $delivery = new PWHDelivery();
            $delivery->Read($_GET['delivery_id']);
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());
        }
    }
    
    $dateTranslator = new PWHDateTranslator();
?>


<fieldset>
	<legend>Rendu de <?php echo $delivery->GetName(); ?></legend>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("#");
        
	    displayErrorReport();
	    displaySuccessReport();
    ?>
   <div class="section">
        <div class="list">
            <ul>
                <li><a href=#><img src="img/group_add.png"/>Composer les groupes de rendus</a></li>
                <li><a href="index.php?page=teacher_download_work&amp;subject_id=<?php echo $subject->GetID(); ?>&amp;work_id=<?php echo $work->GetID(); ?>&amp;delivery_id=<?php echo $delivery->GetID(); ?>"><img src="img/package_go.png">T&eacute;l&eacute;charger l'archive du travail</a></li>
                <li><a href="index.php?page=teacher_download_delivery&amp;subject_id=<?php echo $subject->GetID(); ?>&amp;work_id=<?php echo $work->GetID(); ?>&amp;delivery_id=<?php echo $delivery->GetID(); ?>"><img src="img/package_go.png"/>T&eacute;l&eacute;charger l'archive du rendu</a></li>
            </ul>
        </div>
   </div>
    <h4>R&eacute;capitulatif</h4>
	<div class="section">
        <table class="colored_table underlined_table">
            <tr>
                <td>Mati&egrave;re</td>
                <td><?php echo $subject->GetName(); ?></td>
            </tr>
            <tr>
                <td>Travail</td>
                <td><?php echo $work->GetName(); ?></td>
            </tr>
            <tr>
                <td>Date de rendu</td>
                <td><?php echo $dateTranslator->Html($delivery->GetDeadline(), PWHDateTranslator::DATE_AND_TIME); ?></td>
            </tr>

            <tr>
                <td>Composition des groupes</td>
                <td><?php echo $dateTranslator->Html($delivery->GetGroupCompositionDeadline(), PWHDateTranslator::DATE_AND_TIME); ?></td>
            </tr>
            <tr>
                <td>Format</td>
                <td><?php echo PWHMetaType::GetName($work->GetFormat()); ?></td>
            </tr>
            <tr>
                <td>Membre minimum</td>
                <td><?php echo $work->GetGroupMin(); ?></td>
            </tr>
            <tr>
                <td>Membre maximum</td>
                <td><?php echo $work->GetGroupMax(); ?></td>
            </tr>
            <tr>
                <td>Tol&eacute;rance</td>
                <td><?php echo $work->GetExtraTime(); ?></td>
            </tr>
            <tr>
                <td>Taille</td>
                <td><?php echo $work->GetSize(); ?></td>
            </tr>
        </table>
   </div>
</fieldset>
