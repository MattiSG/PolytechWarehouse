<?php 
    require_once(LIB_PATH() . "PWHGroup.php"); 
    previousPage("teacher_home");
    
    if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['group_id']))
    {
        try 
        {
            $group = new PWHGroup(null);
            $group->Read($_GET['group_id']);      
            $group->Delete();
            successReport("Le groupe " . $group->GetName() . " a &eacute;t&eacute; supprim&eacute;");
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());
        }      
    }
    
    try 
    {
        $groups = PWHGroup::ListGroups();
    } 
    catch(Exception $ex)
    {
        errorReport($ex->getMessage());
        $groups = array();
    }
?>

<fieldset>
    <legend>groupes</legend>
    <?php
        displaySuccessReport();
        displayErrorReport();
    ?>
    <div class="list">
        <ul>
            <li>
                <a href="index.php?page=teacher_load_group">
                    <img src="img/page_white_get.png"/>Charger un nouveau groupe &agrave; partir d'un fichier .promo
                </a>
            </li>
            <li>
                <a href="index.php?page=teacher_create_group">
                    <img src="img/group_add.png"/>Cr&eacute;er un nouveau groupe &agrave; partir des groupes existants
                </a>
            </li>
        </ul>
    <div class="manager">
        <table>
            <tr>
	            <th>Nom</th>
	            <th>Configurer</th>
	            <th>Supprimer</th>
            </tr>
            <?php
                foreach($groups as $group)
                { ?>
                    <tr>
	                    <td>
	                        <a href="index.php?page=teacher_list_group_deliveries&amp;group_id=<?php echo $group->GetID() ?>">
	                            <img src="<?php echo IMG_PATH() ?>bullet_go.png"/><?php echo $group->GetName() ?>
	                        </a>
	                    </td>
	                    <td>
	                        <a href="index.php?page=teacher_group_settings&amp;group_id=<?php echo $group->GetID() ?>">
	                            <img src="<?php echo IMG_PATH() ?>bullet_wrench.png"/>
	                        </a>
	                    </td>
	                    <td>
	                        <a href="index.php?page=teacher_list_groups&amp;action=delete&amp;group_id=<?php echo $group->GetID() ?>">
	                            <img src="<?php echo IMG_PATH() ?>cross.png"/>
	                        </a>
	                    </td>
                    </tr>
            <? } ?>
        </table>
    </div>
</fieldset>

