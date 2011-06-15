<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page list_group_deliveries");
    previousPage('teacher_list_groups_deliveries');
    $failed = false;
    $groupName = "???";
    
    if(isset($_GET['group_id']) && PWHEntity::Valid("PWHGroup", $_GET['group_id']))
    {     
        try
        {
            $group = new PWHGroup();
            $group->Read($_GET['group_id']);
            $deliveries = $group->GetDeliveries(true);
        }
        catch(Exception $ex)
        {
            $failed = true;
            errorReport($ex->getMessage());
        }
    }
    else
    {
        $failed = true;
    }   
    
    if(!$failed)
    {
        $active = array();
        $unactive = array();
        
        foreach($deliveries as $delivery)
        {
            if($delivery->IsStillTimeForDelivery(date("Y-m-d H:i:s")))
            {
                array_push($active, $delivery);
            }
            else
            {
                array_push($unactive, $delivery);
            }
        }
        
        $level = 0;
        $activeSorted = array();
        $checked = array();
        foreach($active as $delivery)
        {
            if(!in_array($delivery->GetID(), $checked))
            {
                $work = new PWHWork();
                $work->Read($delivery->GetWorkID());
                $level += $work->GetLevel();
                $workDeliveries = array();
                $workDeliveries[0] = $work;
                foreach($active as $a)
                {
                    if($a->GetWorkID() == $work->GetID())
                    {
                        array_push($workDeliveries, $a);
                        array_push($checked, $a->GetID());
                    }
                }
                array_push($activeSorted, $workDeliveries);
            }
        }
        
        $unactiveSorted = array();
        $checked = array();
        foreach($unactive as $delivery)
        {
            if(!in_array($delivery->GetID(), $checked))
            {
                $work = new PWHWork();
                $work->Read($delivery->GetWorkID());
                $workDeliveries = array();
                $workDeliveries[0] = $work;
                foreach($unactive as $a)
                {
                    if($a->GetWorkID() == $work->GetID())
                    {
                        array_push($workDeliveries, $a);
                        array_push($checked, $a->GetID());
                    }
                }
                array_push($unactiveSorted, $workDeliveries);
            }
        }
    
        $id=1;
        $link = '<a class="next_form" id="toggle" href="javascript:toggle();"><img src="img/zoom_in.png"/>Voir les rendus inactifs</a>';
        $JSID = 1;
        $groupName = mb_strtolower($group->GetName());
    }
    
    if($failed)
    {
        errorReport("Impossible d'afficher la page demand&eacute;e.");
    }




	include $GLOBALS['EXTERNAL_LIB_DIRNAME'].'calendar.php'; // TODO : Move this include
	
	$month = isset($_GET['m']) ? $_GET['m'] : NULL;
	$year  = isset($_GET['y']) ? $_GET['y'] : NULL;
	
	$params['week_start'] = 1;
	
	$calendar = Calendar::factory($month, $year, $params);
	
	$calendar->standard('today')
		->standard('prev-next');
	
	
	foreach($deliveries as $a_delivery)
	{
		$a_work = new PWHWork();
		$a_work->Read($a_delivery->GetWorkID());
		
	    $a_subject = new PWHSubject();
	    $a_subject->Read($a_work->GetSubjectID());
	
		$event = $calendar->event()
			->condition('timestamp', strtotime($a_delivery->GetDeadline()))
			->title($a_subject->GetName()." / ".$a_work->GetName())
			->output('<a href="./index.php?page=teacher_display_board&previous=teacher_list_group_deliveries&group_id='.$_GET['group_id'].'&subject_id='.$a_subject->GetID().'&work_id='.$a_work->GetID().'&delivery_id='.$a_delivery->GetID().'&index=A">'.$a_subject->GetName()." / ".$a_work->GetName().'</a>');
			
		$calendar->attach($event);
	}

?>
<section>

	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/list_group_deliveries.html', 800, 550);");
        
	    displayErrorReport();
	    displaySuccessReport();
	    
	    if(!$failed)
	    {
	?>


	<h2>Travaux du groupe <?php echo $groupName; ?></h2>
	
		<div class="section">
	    <table class="summary">
            <tr>
                <td>Nombre de travaux actifs</td>
                <td><?php echo count($activeSorted); ?></td>
            </tr>
            <tr>
                <td>Nombre de travaux inactifs</td>
                <td><?php echo count($unactiveSorted); ?></td>
            </tr>
            <tr>
                <td>Charge de travail approximative</td>
                <td><?php 
                        if($level == 0)
                        {
                            echo "-";
                        }
                        else if($level == 1)
                        {
                            echo $level . " heure"; 
                        }
                        else if($level > 1)
                        {
                            echo $level . " heures";
                        }
                     ?>
                </td>
            </tr>
        </table>
	</div>
	<?php
	    $legend = new PWHLegend();
	    $legend->SetType($_SESSION['type']);
	    echo $legend->Html();
	?>

	
		<article>
			<h4>Cliquez sur un jour pour ajouter un rendu :</h4>
			<table class="calendar" id="calendar">
				<thead>
					<tr class="navigation">
						<th class="prev-month"><a href="<?php echo htmlspecialchars($calendar->prev_month_url()) ?>"><?php echo $calendar->prev_month() ?></a></th>
						<th colspan="5" class="current-month"><?php echo $calendar->month() ?></th>
						<th class="next-month"><a href="<?php echo htmlspecialchars($calendar->next_month_url()) ?>"><?php echo $calendar->next_month() ?></a></th>
					</tr>
					<tr class="weekdays">
						<?php foreach ($calendar->days() as $day): ?>
							<th><?php echo $day ?></th>
						<?php endforeach ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($calendar->weeks() as $week): ?>
						<tr>
							<?php foreach ($week as $day): ?>
								<?php
								list($number, $current, $data) = $day;
								
								$classes = array();
								$output  = '';
								
								if (is_array($data))
								{
									$classes = $data['classes'];
									$title   = $data['title'];
									$output  = empty($data['output']) ? '' : '<ul class="output"><li>'.implode('</li><li>', $data['output']).'</li></ul>';
								}
								?>
								<td class="day <?php echo implode(' ', $classes) ?>">
									<span class="date" title="<?php echo implode(' / ', $title) ?>"><?php echo $number ?></span>
									<div class="day-content">
										<?php echo $output ?>
									</div>
								</td>
							<?php endforeach ?>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
		</article>

	<h4>Liste des rendus actifs</h4>
    <div class="section">
        <table id="active" class="colored_table underlined_table">
	        <tr>
		        <th>Nom</th>
		        <th>Groupe</th>
		        <th>Rendu</th>
		        <th>Extra</th>
	        </tr>
	        <?php
	            if(count($activeSorted) == 0)
	            { ?>
	                <tr><td colspan="4">Il n'y a aucun rendu actif pour le groupe <?php echo $group->GetName(); ?></td></tr>
	            <?php
	            }
	            else
	            {
	                foreach($activeSorted as $active) {
	                    $work = $active[0];
	                    
	                    $subject = new PWHSubject();
	                    $subject->Read($work->GetSubjectID());
	                    
	                    $class = ' class="active_work"';
	                    if (!$work->IsPublished()) {
	                        $class = ' class="unpublished_line"';
	                    } else if($work->IsExtraTimeUsed(date("Y-m-d H:i:s"))) {
                            $class = ' class="extra_time_line"';
                        }
                        ?>
                        <tr id="work_<?php echo $JSID; ?>"<?php echo $class; ?>>
	                        <td><a href="javascript:ShowHideDeliveries('<?php echo $JSID; ?>', '<?php echo $work->GetID();?>')"><img id="img-<?php echo $work->GetID(); ?>" src="img/bullet_arrow_right.png"/><?php echo $subject->GetName().' / '.$work->GetName(); ?></a></td>
	                        <td colspan="3"></td>
	                    </tr>
	                    <?php 
	                    $JSID++;
	                    $i = 1;
	                    while($i < count($active))
	                    {
	                        $currentDate = date('Y-m-d H:i:s');
	                        
                            $deliveryDaysLeft = dateDiff($currentDate, $active[$i]->GetDeadline());
                            
                            $groupDaysLeft = 0;
                            if(!$work->IsSimple() && $work->GetGroupMax() > 1)
                            {
                                $groupDaysLeft = dateDiff($currentDate, $active[$i]->GetGroupCompositionDeadline());
                            }
                            
                            $extraTimeLeft = 0;
                            if($work->GetExtraTime() > 0)
                            {
                                if($active[$i]->IsExtraTimeUsed($currentDate))
                                {
                                    $extraTimeLeft = $deliveryDaysLeft + $work->GetExtraTime() * 86400;
                                }
                                else
                                {
                                    $extraTimeLeft = $work->GetExtraTime() * 86400;
                                }
                            }
	                
	                        $class = ' class="active_delivery"';
	                        if(!$work->IsPublished())
                            {
                                $class = ' class="unpublished_delivery_line"';
                            }
                            else if($active[$i]->IsExtraTimeUsed(date("Y-m-d H:i:s")))
                            {
                                $class = ' class="extra_time_delivery_line"';
                            }
                            ?>
	                        <tr id="<?php echo $work->GetID() . "-" . $delivery->GetID(); ?>"<?php echo $class; ?>>
		                        <td>
		                            <a href="index.php?page=teacher_display_board&amp;previous=teacher_list_group_deliveries&amp;group_id=<?php echo $group->GetID(); ?>&amp;subject_id=<?php echo $subject->GetID(); ?>&amp;work_id=<?php echo $work->GetID(); ?>&amp;delivery_id=<?php echo $active[$i]->GetID(); ?>&amp;index=A">
		                                <img src="img/bullet_go.png"/><?php echo $active[$i]->GetName(); ?>
		                            </a>
		                        </td>	       
		                        <td id="group_days_left<?php echo $id; ?>"><?php if($groupDaysLeft < 0) { echo 0; } else { echo $groupDaysLeft; } ?></td>     
		                        <td id="delivery_days_left<?php echo $id; ?>"><?php if($deliveryDaysLeft < 0) { echo 0; } else { echo $deliveryDaysLeft; } ?></td>
		                        <td id="extra_time_left<?php echo $id; ?>"><?php echo $extraTimeLeft; ?></td>
	                        </tr>    
	                               
	                        <?php
	                        $id++;
	                        $i++;
	                    }
                    }
                }  ?>
        </table>
    </div>
    <div class="section">
	    <?php echo $link; ?>
	</div>
	<div id="inactive">
        <h4>Listes des rendus inactif</h4>
        <div class="section">
            <table class="colored_table underlined_table">
	            <tr>
		            <th>Nom</th>
		            <th>Date de fin</th> 
	            </tr>
	            <?php
	            if(count($unactiveSorted) == 0)
	            { ?>
	                <tr><td colspan="4">Il n'y a aucun rendu inactif pour le groupe <?php echo $group->GetName(); ?></td></tr>
	            <?php
	            }
	            else
	            {
	                $dateTranslator = new PWHDateTranslator();
	                foreach($unactiveSorted as $unactive)
	                {
	                    $work = $unactive[0];
	                    
	                    $subject = new PWHSubject();
	                    $subject->Read($work->GetSubjectID());
	                    
	                    $class = ' class="unactive_work"';
                        ?>
                        <tr id="work_<?php echo $JSID; ?>"<?php echo $class; ?>>
	                        <td><a href="javascript:ShowHideDeliveries('<?php echo $JSID; ?>', '<?php echo $work->GetID();?>')"><img id="img-<?php echo $work->GetID(); ?>" src="img/bullet_arrow_right.png"/><?php echo $subject->GetName().' / '.$work->GetName(); ?></a></td>
	                        <td colspan="3"></td>
	                    </tr>
	                    <?php 
	                    $JSID++;
	                    $i = 1;
	                    while($i < count($unactive))
	                    {	                
                            $class = ' class="delivered_locked_line"';
                            ?>
	                        <tr id="<?php echo $work->GetID() . "-" . $delivery->GetID(); ?><?php echo $class; ?>">
		                        <td>
		                            <a href="index.php?page=teacher_display_board&amp;previous=teacher_list_group_deliveries&amp;group_id=<?php echo $group->GetID(); ?>&amp;subject_id=<?php echo $subject->GetID(); ?>&amp;work_id=<?php echo $work->GetID(); ?>&amp;delivery_id=<?php echo $delivery->GetID(); ?>&amp;index=A">
		                                <img src="img/bullet_go.png"/><?php echo $delivery->GetName(); ?>
		                            </a>
		                        </td>
		                        <td><?php echo $dateTranslator->Html($delivery->GetDeadline(), PWHDateTranslator::DATE_AND_TIME); ?></td>
	                        </tr>     
	                        <?php
	                        $i++;
	                    }
                    }
                }  ?>
            </table>
        </div>
    </div>
    <?php } ?>
</section>
<script type="text/javascript">
<!--
    var numberDeliveries = <?php echo $id-1; ?>;
    var counterDeliveries = new Array();
    var counterGroups = new Array();
    var counterExtraTime = new Array();
    
    for(var i=1; i<=numberDeliveries; i++)
    {
        counterDeliveries[i] = document.getElementById("delivery_days_left".concat(i)).innerHTML;
        document.getElementById("delivery_days_left".concat(i)).innerHTML = dateHMS(counterDeliveries[i]);
        counterGroups[i] = document.getElementById("group_days_left".concat(i)).innerHTML;
        document.getElementById("group_days_left".concat(i)).innerHTML = dateHMS(counterGroups[i]);
        counterExtraTime[i] = document.getElementById("extra_time_left".concat(i)).innerHTML;
        document.getElementById("extra_time_left".concat(i)).innerHTML = dateHMS(counterExtraTime[i]);
    }
    
    function time()
    {
        for(var i=1; i <= numberDeliveries; i++)
        {
            var counter = document.getElementById("delivery_days_left".concat(i));
            counterDeliveries[i] -= 1;
            if(counterDeliveries[i] < 0)
            {
                counterDeliveries[i] = 0;
            }
            counter.innerHTML = dateHMS(counterDeliveries[i]);
            
            counter = document.getElementById("group_days_left".concat(i));
            counterGroups[i] -= 1;
            if(counterGroups[i] < 0)
            {
                counterGroups[i] = 0;
            }
            counter.innerHTML = dateHMS(counterGroups[i]);
            
            if(counterDeliveries[i] == 0)
            {
                counter = document.getElementById("extra_time_left".concat(i));
                counterExtraTime[i] -= 1;
                if(counterExtraTime[i] < 0)
                {
                    counterExtraTime[i] = 0;
                }
                counter.innerHTML = dateHMS(counterExtraTime[i]);
            } 
        }
    }
    
    function dateHMS(time) 
    {
        if(time == 0)
        {
            return "-";
        }
        
        var strbuf = new String();
        var temp = time % 3600;
        var hours = ( time - temp ) / 3600;
        var temp2 = hours % 24;
        var days = (hours - temp2) / 24;
        hours = temp2;
        var seconds = temp % 60 ;
        var minutes = ( temp - seconds ) / 60;
        
        if(days > 0)
        {
            strbuf += days;
            strbuf += "j ";
        }
        
        if(days > 0 || (days == 0 && hours > 0))
        {
            if(hours < 10)
            {
                 strbuf += "0";
            }
            strbuf += hours;
            strbuf += "h ";
        }
        
        if((days > 0 || (days == 0 && minutes > 0)) || (hours > 0 || (hours == 0 && minutes > 0)))
        {
            if(minutes < 10)
            {
                 strbuf += "0";
            }
            strbuf += minutes;
            strbuf += "m ";
        }
        
        if(seconds < 10)
        {
             strbuf += "0";
        }
        strbuf += seconds;
        strbuf += "s";
        
        return strbuf;
    }
    
    window.setInterval("time()", 1000); 
//-->
</script>
<script type="text/javascript">
<!--
    function toggle()
    {
        
        var link = document.getElementById("toggle");
        var divInactive = document.getElementById("inactive");
        
        if(divInactive.style.display == "")
        {
            divInactive.style.display = "none";        
            link.innerHTML = '<img src="img/zoom_in.png"/>Voir les rendus inactifs';
        }
        else
        {
            divInactive.style.display = "";
            link.innerHTML = '<img src="img/zoom_out.png"/>Masquer les rendus inactifs';
        }
    }
    
    document.getElementById("inactive").style.display = "none";
</script>
<script type="text/javascript">
<!--
    var numberWorks = <?php echo $JSID-1 ?>;
    var workVisible = new Array();
    for(var i=1; i<=numberWorks; i++)
    {
        workVisible[i] = false;
    }
    
    function ShowHideDeliveries(js_id, work_id)
    {
        var table = document.getElementById("active");
        var children = document.getElementsByTagName("tr");
        workVisible[js_id] = !workVisible[js_id];
        
        for(var i = 0; i < children.length; i++)
        {
            if(children[i].hasAttribute("id"))
            {
                var id = children[i].getAttribute("id");
                var reg = new RegExp("^"+work_id+"-");
                if(reg.test(id))
                {
                    if(workVisible[js_id])
                    {
                        children[i].style.display = "";
                        document.getElementById("img-" + work_id).src = "img/bullet_arrow_down.png";
                    }
                    else
                    {
                        children[i].style.display = "none";
                        document.getElementById("img-" + work_id).src = "img/bullet_arrow_right.png";
                    }
                }
            }
        }
    }
    
    var children = document.getElementsByTagName("tr");
        
    for(var i = 0; i < children.length; i++)
    {
        if(children[i].hasAttribute("id"))
        {
            var id = children[i].getAttribute("id");
            var reg = new RegExp("^[0-9]*\-");
            if(reg.test(id))
            {
                children[i].style.display = "none";
            }
        }
    }
</script>
