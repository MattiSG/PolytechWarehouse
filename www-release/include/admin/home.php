<?php
     PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page home");
?>
<fieldset>
    <legend>espace personnel administrateur</legend>
    <?php
        $help = new PWHHelp();
        echo $help->Html("#");
    ?>
    <h4>Menu principal</h4>
	<div class="section">   
	    <div class="list">
            <ul>
                <li><a href="index.php?page=admin_database_management"><img src="<?php echo IMG_PATH(); ?>database.png"/>Gestion de la base de donn&eacute;es</a></li>
                <li><a href="index.php?page=admin_log_management"><img src="<?php echo IMG_PATH(); ?>page_edit.png"/>Gestion du fichier des logs</a></li>
            </ul>
        </div>
   </div>
</fieldset>

