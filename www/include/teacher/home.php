<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page home");
    
    try {
		$teacher = new PWHTeacher();
		$teacher->Read($_SESSION['id']);
		$subjects = $teacher->GetSubjects();
		
		$promos = array();
		foreach($subjects as $subject)
			$promos[$subject->GetPromotion()->GetID()] = $subject->GetPromotion();
				
		usort($promos, "entity_comparator");
    } catch(Exception $ex) {
        $failed = true;
        errorReport($ex->getMessage());
    }
?>

<section>
    <?php
        $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/home.html', 800, 600);");
    ?>
    <h1>Mes matières</h1>
    
    <?php
    	if(count($promos) == 0) {
    		echo '<p>Aucune promo concern&eacute;e</p>';
    	} else {
    ?>
     
    <table id="promos">
    	<?php
        	foreach($promos as $promo) {
        ?>
        		<tr>
	    		    <th>
	    		    	<a href="index.php?page=teacher_list_group_deliveries&amp;group_id=<?php echo $promo->GetID(); ?>">
	                    	<?php echo $promo->GetName(); ?>
	                    </a>
	    		    </th>
	    		    <?php
    					$all_deliveries = $promo->GetDeliveries(true);
    					
    					$active_deliveries = array();
    					$mine_deliveries = array();
    					$next_deliveries = false;
   						
  						foreach ($all_deliveries as $delivery) {
    						if ($delivery->IsStillTimeForDelivery(date("Y-m-d H:i:s")))
		   						$active_deliveries[] = $delivery;
						}
						
						foreach ($active_deliveries as $delivery) {
							$work = new PWHWork();
			                $work->Read($delivery->GetWorkID());
							$subject = new PWHSubject();
	                    	$subject->Read($work->GetSubjectID());
	                    	
							if ($subject->TeacherExists($teacher->GetID()))
								$mine_deliveries[] = $delivery;
						}
						
						$subjects = $promo->GetSubjects(true);
						echo '<td>';
						if (count($subjects) >= 1)
							echo $subjects[0]->GetName();
						$i = 1;
						while ($i < 3 && $i < count($subjects))
							echo ', '.$subjects[$i++]->GetName();
						if (count($subjects) >= 3)
							echo ', ...';
						echo '</td>';
						
    					echo "<td>".count($active_deliveries)." rendus dont ".count($mine_deliveries)." mien</td>";
    				?>
		    	</tr>
		<?php
        	}
       	}
        ?>
    </table>
    
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
