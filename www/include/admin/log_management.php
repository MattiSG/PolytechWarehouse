<?php
    
    previousPage("admin_home");
    
    
    if(isset($_GET['action']) && $_GET['action'] == 'erase_all')
    {  
        try
        {
            @unlink(LOG_FILE());
            include(LIB_PATH() . "PWHLogSetup.php");
            successReport("Le fichier des logs a &eacute;t&eacute; r&eacute;initialis&eacute;.");
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());
        }
    }
    
?>

<script type="text/javascript">
<!--
    function UserConfirmation()
    {
        if(confirm('****** ATTENTION ******\n\nLa suppression du fichier de log est définitive ! \n\nEtes vous sur de vouloir continuer ? \n\nCliquez sur \'OK\' pour confirmer votre choix, ou sur \'Annuler\' pour ne pas poursuivre.'))
        {
            window.location = "index.php?page=admin_log_management&action=erase_all";
        }
    }
//-->
</script>

<section>
    <h2>gestion des logs</h2>
    <?php
        $help = new PWHHelp();
        echo $help->Html("#");
        
        displayErrorReport();
        displaySuccessReport();
    ?>
    <h4>Gestion du fichier SQLite</h4>
    <div class="section">
        <div class="list">
            <ul>
                <li><a href="downloads/index.php?type=log_sqlite"><img src="<?php echo IMG_PATH(); ?>page_save.png"/>
                    T&eacute;l&eacute;charger une copie du fichier .sqlite</a>
                </li>
            </ul>
            <ul>
                <li><a href="javascript:UserConfirmation();"><img src="<?php echo IMG_PATH(); ?>page_refresh.png"/>
                    Supprimer l'int&eacute;gralit&eacute; des logs et r&eacute;initialiser le fichier .sqlite</a>
                </li>
            </ul>
        </div>
    </div>
</section>
