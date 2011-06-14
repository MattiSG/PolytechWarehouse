<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page home");
    
    $teacher = new PWHTeacher();
    $teacher->Read($_SESSION['id']);
?>

<section>
    <?php
        $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/home.html', 800, 600);");
    ?>
    <h1>Mes matières</h1>
    <dl id="promos">
        <dt><a href="??">CiP1</a></dt>
    	<dd>
    		<ul>
    			<li>4 rendus dont 1 mien</li>
    			<li>Prochain : IntroInternet le 6 octobre</li>
    		</ul>
    	</dd>
    
    	<dt><a href="??">SI3</a></dt>
    	<dd>
    		<ul>
    			<li>6 rendus dont 2 miens</li>
    			<li>Prochain : POO le 12 novembre</li>
    		</ul>
    	</dd>
    	
    	<dt><a href="??">SI4</a></dt>
    	<dd>
    		<ul>
    			<li>5 rendus dont 0 miens</li>
    			<li>Prochain : &#8212;</li>
    		</ul>
    	</dd>
    </dl>
	<p class="add"><a href="index.php?page=teacher_create_subject_name"><img src="<?php echo IMG_PATH(); ?>book_open.png"/>Ajouter une matière</a></p>
	
	<a href="index.php?page=teacher_list_groups_deliveries"><img src="<?php echo IMG_PATH(); ?>package.png"/>Tous mes rendus</a>
	
	<p><a href="index.php?page=teacher_email_groups"><img src="<?php echo IMG_PATH(); ?>email.png"/>Mailing lists</a></p>

	<p>Gérer les <a href="index.php?page=teacher_list_groups"><img src="<?php echo IMG_PATH(); ?>group.png"/>groupes</a> ou les <a href="index.php?page=teacher_list_subjects&amp;see=less"><img src="<?php echo IMG_PATH(); ?>book_open.png"/>mati&egrave;res</a></p>
</section>
<section>
	<h2>&eacute;v&egrave;nements r&eacute;cents</h2>
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
</section>
