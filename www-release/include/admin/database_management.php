<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page database_management");
    
    previousPage("admin_home");
    
    
    if(isset($_GET['action']) && $_GET['action'] == 'erase_all')
    {  
        try
        {
            @unlink(DATABASE_FILE());
            exec("rm -rf " . getcwd() . "/uploads/*");
            include(LIB_PATH() . "PWHSQLiteSetup.php");
            successReport("La base de donn&eacute;es a &eacute;t&eacute; r&eacute;initialis&eacute;.");
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
        if(confirm('Voulez-vous vraiment continuer ?'))
        {
            window.location = "index.php?page=admin_database_management&action=erase_all";
        }
    }
//-->
</script>

<fieldset>
    <legend>gestion de la base de donn&eacute;es</legend>
    <?php
        $help = new PWHHelp();
        echo $help->Html("#");
        
        displayErrorReport();
        displaySuccessReport();
    ?>
    <h4>Gestion des enseignants</h4>
    <div class="section">
        <div class="list">
            <ul>
                <li>
                    <a href="index.php?page=admin_load_teachers"><img src="<?php echo IMG_PATH(); ?>page_white_get.png"/>
                        Charger des nouveaux enseignants &agrave; partir d'un fichier .ens</a>
                </li>
                <li>
                    <a href="index.php?page=admin_create_teacher"><img src="<?php echo IMG_PATH(); ?>user_add.png"/>
                        Cr&eacute;er rapidement un enseignant</a>
                </li>
                <li>
                    <a href="index.php?page=admin_edit_teachers&amp;index=A"><img src="<?php echo IMG_PATH(); ?>user_edit.png"/>
                        Editer les profils des enseignants</a>
                </li>
            </ul>
        </div>
    </div>
    <h4>Gestion du fichier SQLite</h4>
    <div class="section">
        <div class="list">
            <ul>
                <li><a href="downloads/index.php?type=bd_sqlite"><img src="<?php echo IMG_PATH(); ?>database_save.png"/>
                    T&eacute;l&eacute;charger une copie du fichier .sqlite et du contenu du d&eacute;p&ocirc;t</a>
                </li>
            </ul>
            <ul>
                <li><a href="javascript:UserConfirmation();"><img src="<?php echo IMG_PATH(); ?>database_refresh.png"/>
                    Supprimer l'int&eacute;gralit&eacute; de la base de donn&eacute;es et la r&eacute;initialiser</a>
                </li>
            </ul>
        </div>
    </div>
</fieldset>
