<?php 
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page list_groups");
    previousPage("teacher_home");
    $failed = false;
    
    try 
    {
        $groups = PWHEntity::ListAll('PWHGroup');
    }
    catch(Exception $ex)
    {
        $failed = true;
        errorReport($ex->getMessage());
    }
    
    if(!$failed)
    {
        // [FORM] Delete the specified group
        if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['group_id']))
        {
            try 
            {
                $group = new PWHGroup();
                $group->Read($_GET['group_id']);   
                if(!$group->HasSubjects(true))
                {   
                    $group->Delete();
                    PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Suppression groupe " . $group->GetName());
                    successReport("Le groupe " . $group->GetName() . " et ses sous-groupes ont &eacute;t&eacute; supprim&eacute;.");
                }
                else
                {
                    $subjects = $group->GetSubjects(true);
                    $strbuf = "";
                    foreach($subjects as $subject)
                    {
                        $strbuf .= $subject->GetName() . ", ";
                    }
                    $strbuf = substr($strbuf, 0, strlen($strbuf) - 2);
                    PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Echec suppression groupe " . $group->GetName() . ": mati&egrave;res existantes");
                    errorReport("Le groupe " . $group->GetName() . " ne peut pas &ecirc;tre supprim&eacute; car il est utilis&eacute; dans les mati&egrave;res suivantes : " . $strbuf . ".");
                }
            }
            catch(Exception $ex)
            {
                PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec suppression groupe");
                errorReport($ex->getMessage());
            }      
        }
    }
    
    if($failed)
    {
        errorReport("Impossible d'afficher la page demand&eacute;e.");
    }
?>
<section>
    <h2>gestion des groupes</h2>
    <?php
        $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/list_groups.html', 800, 550);");
        
        displaySuccessReport();
        displayErrorReport();
        
        if(!$failed)
        {
    ?>
    <h4>Cr&eacute;ation de groupes</h4>
    <div class="section">
        <div class="list">
            <ul>
                <li>
                    <a href="index.php?page=teacher_create_group_name">
                        <img src="img/group_add.png"/>Cr&eacute;er un nouveau groupe &agrave; partir des groupes existants
                    </a>
                </li>
                <li>
                    <a href="index.php?page=teacher_load_group">
                        <img src="img/page_white_get.png"/>Charger une nouvelle promotion &agrave; partir d'un fichier .promo
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <h4>Promotions et groupes</h4>
    <div class="section">   
        <?php  
            $groupTree = new PWHGroupTree();
            $groupTree->Build(PWHGroupTree::ROOT);
            echo $groupTree->Html(PWHGroupTree::CONFIG_TREE, PWHGroupTree::TEACHER); 
        ?>
    </div>
    <?php } ?>
</section>
<script type="text/javascript">
<!--
    function UserConfirmation(group_id)
    {
        if(confirm('****** ATTENTION ******\n\nLa suppression d\'un groupe est d�finitive ! \nLes �tudiants ne seront pas effac�s du syst�me, mais juste d�saffect� du groupe que vous supprimez.\n\nEtes vous sur de vouloir continuer ? \n\nCliquez sur \'OK\' pour confirmer votre choix, ou sur \'Annuler\' pour ne pas poursuivre.'))
        {
            window.location = "index.php?page=teacher_list_groups&action=delete&group_id=" + group_id;
        }
    }
//-->
</script>
