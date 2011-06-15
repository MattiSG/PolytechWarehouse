<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page history");
    
    previousPage("teacher_home");
     
    $student = new PWHStudent();
    $student->Read($_SESSION['id']);
?>
<section>
	<h2>historique des &eacute;v&egrave;nements</h2>
	<?php 
	    $help = new PWHHelp();
        echo $help->Html("#");
    ?>
	<div class="section">
        <?php 
            $list = new PWHEventList();
            $list->SetPersonID($student->GetID());
            $list->SetPersonType($_SESSION['type']);
            $list->SetSize(PWHEvent::ALL_EVENTS);
            echo $list->Html(); 
        ?>
	</div>
</section>

