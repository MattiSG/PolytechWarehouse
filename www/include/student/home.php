<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page student_home");
    
    $student = new PWHStudent();
    $student->Read($_SESSION['id']);
?>
<fieldset>
	<legend>&eacute;v&egrave;nements r&eacute;cents</legend>
	<?php 
	    $history = new PWHHistory();
        echo $history->Html("index.php?page=student_history");
    ?>
	<div class="section">
        <?php 
            $list = new PWHEventList();
            $list->SetPersonID($student->GetID());
            $list->SetPersonType($_SESSION['type']);
            $list->SetSize(4);
            echo $list->Html(); 
        ?>
	</div>
</fieldset>
<fieldset>
    <legend>espace personnel de <?php echo mb_strtolower($student->GetFirstName() . " " . $student->GetLastName()); ?></legend>
	<?php 
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/student/help/home.html', 800, 550);");
    ?>
    <h4>Menu principal</h4>
	<div class="section">    
	    <div class="list">
            <ul>
                <li><a href="index.php?page=student_list_deliveries"><img src="img/package_go.png"/>Gestion des travaux</a></li>
                <li><a href="export/index.php?type=student&amp;student_id=<?php echo $_SESSION['id']; ?>&amp;action=show_cal"><img src="img/calendar.png"/>Export du planning des rendus au format agenda iCalendar</a></li>
                <li><a href="index.php?page=student_email_groups"><img src="img/email.png"/>Mailing List</a></li>
            </ul>
        </div>
   </div>
</fieldset>
