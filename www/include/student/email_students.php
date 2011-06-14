<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page student_email_students");
    
    previousPage('student_email_groups');
    $failed = false;
    
    // Retrieves the list of students sorted, corresponding to the value of the index
    if(isset($_GET['index']) && preg_match("#^[*A-Z]$#", $_GET['index']))
    {        
        try
        {
            if(isset($_GET['group_id']))
            {
                $group = new PWHGroup();
                $group->Read($_GET['group_id']);
                $students = $group->GetStudents();
                $allStudents = $students;
                usort($students, "person_comparator");
                $index = new PWHIndex();
                $students = $index->FilterPersons($students, $_GET['index']);
            }
            else
            {
                $failed = true;
            }
        }
        catch(Exception $ex)
        {
            $failed = true;
            errorReport($ex->getMessage());
        }
    }
    else
    {
        $failed = true;
    }
    
    if($failed)
    {
        errorReport("Impossible d'afficher la page demand&eacute;e.");
    }
?>

<section>
	<h2>envoi d'emails</h2>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("#");
        
	    displayErrorReport();
	    displaySuccessReport();
	    if(!$failed)
	    {
	?>
	<h4>Liste des &eacute;tudiants du groupe <?php echo $group->GetName(); ?></h4>
	<div class="section">
	    <?php 
	        $index = new PWHIndex();
	        echo $index->Html("index.php?page=student_email_students&amp;group_id=" . $group->GetID(), $_GET['index'], false, $allStudents);
	    ?>
        <?php
            $table = new PWHEmailTable();
            echo $table->Html($students);
        ?>
	</div>
	<?php } ?>
</section>
			

