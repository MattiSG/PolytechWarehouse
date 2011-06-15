<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page list_groups_deliveries");
    
    previousPage("teacher_home");
?>

<section>
    <h2>Etats des rendus</h2>
    <?php
        $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/list_groups_deliveries.html', 800, 550);");
    ?>
    <h4>Promotions et groupes</h4>
    <div class="section">          
        <?php  
            $groupTree = new PWHGroupTree();
            $groupTree->Build(PWHGroupTree::ROOT);
            echo $groupTree->Html(PWHGroupTree::DELIVERY_TREE, PWHGroupTree::TEACHER); 
        ?>
    </div>
</section>
