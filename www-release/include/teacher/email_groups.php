<?php 
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page email_groups");
    
    previousPage("teacher_home");
?>

<fieldset>
    <legend>envoi d'emails</legend>
    <?php
        $help = new PWHHelp();
        echo $help->Html("#");
    ?>
    <h4>Promotions et groupes</h4>
    <div class="section">          
        <?php  
            $groupTree = new PWHGroupTree();
            $groupTree->Build(PWHGroupTree::ROOT);
            echo $groupTree->Html(PWHGroupTree::EMAIL_TREE, PWHGroupTree::TEACHER); 
        ?>
    </div>
</fieldset>

