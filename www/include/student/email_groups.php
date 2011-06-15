<?php 
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page student_email_groups");
    
    previousPage("student_home");
?>

<section>
    <h2>envoi d'emails</h2>
    <?php
	    $help = new PWHHelp();
        echo $help->Html("#");
        
	    displayErrorReport();
	    displaySuccessReport();
	?>
    <h4>Promotions et groupes</h4>
    <div class="section">          
        <?php  
            $groupTree = new PWHGroupTree();
            $groupTree->Build(PWHGroupTree::ROOT);
            echo $groupTree->Html(PWHGroupTree::EMAIL_TREE, PWHGroupTree::STUDENT);
        ?>
    </div>
</section>

