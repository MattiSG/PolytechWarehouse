<?php 
    require_once(LIB_PATH() . "PWHGroup.php"); 
    previousPage("teacher_home");
?>

<fieldset>
<legend>groups</legend>
<div class="list">
    <ul>
        <li>
            <a href="index.php?page=teacher_load_group">
                <img src="img/page_white_get.png"/>Load a group using a .promo file
            </a>
        </li>
        <li>
            <a href="index.php?page=teacher_group_creation">
                <img src="img/group_add.png"/>Create a new group using existing groups
            </a>
        </li>
        <li><a href=#><img src="img/database_delete.png"/>Erase all students and groups</a></li>
    </ul>
<div class="manager">
	<form method="post">
        <table>
	        <tr>
		        <th>Name</th>
		        <th>Action</th>
		        <th>Delete</th>
	        </tr>
	        <?php
	            $groups = PWHGroup::ListGroups();
	            foreach($groups as $group)
	            { ?>
	                <tr>
		                <td>
		                    <a href="index.php?page=teacher_list_group_deliveries&amp;id=<?php echo $group->GetID() ?>">
		                        <img src="<?php echo IMG_PATH() ?>bullet_go.png"/><?php echo $group->GetName() ?>
		                    </a>
		                </td>
		                <td>
		                    <a href="index.php?page=teacher_group_settings&amp;id=<?php echo $group->GetID() ?>">
		                        <img src="<?php echo IMG_PATH() ?>bullet_wrench.png"/> Settings
		                    </a>
		                </td>
		                <td><input type="checkbox" name="<?php echo $group->GetID() ?>" id="<?php echo $group->GetID() ?>"/></td>
	                </tr>
            <? } ?>
	        <tr>
	            <td colspan="3"><input type="submit" id="delete" value="Delete"/></td>
	        </tr>
        </table>
    </form>
</div>
</fieldset>

