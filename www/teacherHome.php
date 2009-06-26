<fieldset>
	<legend>events</legend>
	<div class="list">
	    <ul>
	    </ul>
	</div>
</fieldset>
<fieldset>
    <legend><?php echo $_SESSION['login']; ?>'s home</legend>
	<div class="list">
        <ul>
            <li><a href="index.php?page=teacher_list_subjects"><img src="<?php echo IMG_PATH(); ?>book_open.png"/>Display subjects</a></li>
            <li><a href="index.php?page=teacher_list_groups"><img src="<?php echo IMG_PATH(); ?>group.png"/>Display groups</a></li>
        </ul>
    </div>
</fieldset>

