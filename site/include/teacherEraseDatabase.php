<?php
    previousPage("teacher_home");
    
    if(isset($_GET['action']) && $_GET['action'] == 'erase')
    { 
        if ($db = @sqlite_open(DATABASE_FILE())) 
        { 
            if(@sqlite_query($db, 'DELETE FROM PWH_Student') && @sqlite_query($db, 'DELETE FROM PWH_Group')           
                && @sqlite_query($db, 'DELETE FROM PWH_StudentGroup'))
             {
                successReport("Les groupes et les &eacute;tudiants ont &eacute;t&eacute; supprim&eacute;es"); 
             }
             else
             { 
                successReport("Echec de la suppression des groupes et des &eacute;tudiants"); 
             }
            sqlite_close($db);
        }
        else
        {
            errorReport("Echec de la connection avec la base de donn&eacute;es");
        }
    }
    else if(isset($_GET['action']) && $_GET['action'] == 'erase_all')
    {  
        try
        {
            @unlink(DATABASE_FILE());
            include(LIB_PATH() . "PWHSQLiteSetup.php");
            successReport("La base de donn&eacute;es a &eacute;t&eacute; r&eacute;initialis&eacute;");
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());
        }
    }
?>

<fieldset>
    <legend>database managment</legend>
    <?php
        displayErrorReport();
        displaySuccessReport();
    ?>
    <p>
        Pour supprimer tous les groupes et &eacute;tudiants de la base de donn&eacute;es 
        <a href="index.php?page=teacher_erase_database&amp;action=erase"><img src="<?php echo IMG_PATH(); ?>bullet_go.png"/>cliquez ici</a>.
    </p>
    <p>
        Pour supprimer l'int&eacute;gralit&eacute; de la base de donn&eacute;es et la r&eacute;initialiser
        <a href="index.php?page=teacher_erase_database&amp;action=erase_all"><img src="<?php echo IMG_PATH(); ?>bullet_go.png"/>cliquez ici</a>.
    </p>
</fieldset>
