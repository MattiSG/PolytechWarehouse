<fieldset>
	<legend>&eacute;v&egrave;nements</legend>
	<div class="list">
	    <ul>
	    </ul>
	</div>
</fieldset>
<fieldset>
    <legend>espace personnel de <?php echo mb_strtolower($_SESSION['login']); ?></legend>
	<div class="list">
        <ul>
            <li><a href="index.php?page=teacher_list_subjects"><img src="<?php echo IMG_PATH(); ?>book_open.png"/>Afficher les mati&egrave;res</a></li>
            <li><a href="index.php?page=teacher_list_groups"><img src="<?php echo IMG_PATH(); ?>group.png"/>Afficher les groupes</a></li>
            <li><a href="index.php?page=teacher_erase_database"><img src="<?php echo IMG_PATH(); ?>database_delete.png"/>Gestionnaire de base de donn&eacute;es</a></li>
        </ul>
    </div>
</fieldset>

