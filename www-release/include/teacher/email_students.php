<?php 
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page email_students");
    previousPage('teacher_email_groups');

    // Retrieves the list of students sorted, corresponding to the value of the index
    if(isset($_GET['index']))
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
                $students = array();
            }
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());
            $students = array();
        }
    }
?>

<fieldset>
	<legend>envoi d'emails</legend>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("#");
        
	    displayErrorReport();
	    displaySuccessReport();
	?>
	<h4>Liste des &eacute;tudiants du groupe <?php echo $group->GetName(); ?></h4>
	<div class="section">
	    <?php 
	        $index = new PWHIndex();
	        echo $index->Html("index.php?page=teacher_email_students&amp;group_id=" . $group->GetID(), $_GET['index'], false, $allStudents);
	    ?>
        <?php
            $table = new PWHEmailTable();
            echo $table->Html($students);
        ?>
	</div>
</fieldset>
			

