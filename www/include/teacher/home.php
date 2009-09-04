<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page home");
    
    $teacher = new PWHTeacher();
    $teacher->Read($_SESSION['id']);
?>

<fieldset>
	<legend>&eacute;v&egrave;nements r&eacute;cents</legend>
	<?php 
	    $history = new PWHHistory();
        echo $history->Html("index.php?page=teacher_history");
    ?>
	<div class="section">
        <?php 
            $list = new PWHEventList();
            $list->SetPersonID($teacher->GetID());
            $list->SetPersonType($_SESSION['type']);
            $list->SetSize(4);
            echo $list->Html(); 
        ?>
	</div>
</fieldset>
<fieldset>
    <legend>espace personnel de <?php echo mb_strtolower($teacher->GetFirstName() . " " . $teacher->GetLastName()); ?></legend>
    <?php
        $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/home.html', 800, 600);");
    ?>
    <div class="section">
        <p>
            Bienvenue sur le site de Polytech'WareHouse. Vous &ecirc;tes actuellement sur votre espace personel. Pour plus d'informations, veuillez consulter 
            l'aide en ligne.
        </p>
    </div>
    <h4>Menu principal</h4>
	<div class="section">
	    <div class="list">
            <ul>
                <li><a href="index.php?page=teacher_list_subjects&amp;see=less"><img src="<?php echo IMG_PATH(); ?>book_open.png"/>Gestion des mati&egrave;res</a></li>
                <li><a href="index.php?page=teacher_list_groups_deliveries"><img src="<?php echo IMG_PATH(); ?>package.png"/>Tableau de bord des rendus</a></li>
                <li><a href="index.php?page=teacher_list_groups"><img src="<?php echo IMG_PATH(); ?>group.png"/>Gestion des promotions</a></li>
                <li><a href="index.php?page=teacher_email_groups"><img src="<?php echo IMG_PATH(); ?>email.png"/>Mailing lists</a></li>
            </ul>
        </div>
   </div>
</fieldset>

