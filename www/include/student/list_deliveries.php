<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page student_list_deliveries");
    
    previousPage('student_home');
    
    $student = new PWHStudent();
    $student->Read($_SESSION['id']);
    $groups = $student->GetGroups();
    
    $deliveries = array();
    foreach($groups as $group)
    {
        $deliveries = array_merge($deliveries, $group->GetDeliveries(false));
    }
    
    $level;
    $i = 0;
    while($i < count($deliveries))
    {
        $work = new PWHWork();
        $work->Read($deliveries[$i]->GetWorkID());
        if(!$work->IsPublished())
        {
            array_splice($deliveries, $i, 1);
        }
        else
        {
            $level += $work->GetLevel();
            $i++;
        }
    }
    
    $activeDelivered = array();
    $activeUndelivered = array();
    $unactiveDelivered = array();
    $unactiveUndelivered = array();
    
    foreach($deliveries as $delivery)
    {
        if($delivery->IsStillTimeForDelivery(date("Y-m-d H:i:s")))
        {
            if($student->HasDeliverygroup($delivery->GetID()) && $student->GetDeliverygroup($delivery->GetID())->GetLastDelivery() != "")
            {
               array_push($activeDelivered, $delivery);
            }
            else
            {
                array_push($activeUndelivered, $delivery);
            }
        }
        else
        {
            if($student->HasDeliverygroup($delivery->GetID()) && $student->GetDeliverygroup($delivery->GetID())->GetLastDelivery() != "")
            {
               array_push($unactiveDelivered, $delivery);
            }
            else
            {
                array_push($unactiveUndelivered, $delivery);
            }
        }
    }
    
    $id=1;
    $link = '<a class="next_form" id="toggle" href="javascript:toggle();"><img src="img/zoom_in.png"/>Voir les rendus inactifs</a>';
?>

<fieldset>
	<legend>rendus de <?php echo mb_strtolower($student->GetFirstName() . " " . $student->GetLastName()); ?></legend>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/student/help/list_deliveries.html', 800, 550);");
        
	    displayErrorReport();
	    displaySuccessReport();
	?>
	<h4>Statistiques</h4>
	<div class="section">
	    <table class="summary">
            <tr>
                <td>Nombre de travaux actifs</td>
                <td><?php echo count($activeDelivered) + count($activeUndelivered); ?></td>
            </tr>
            <tr>
                <td>Nombre de travaux inactifs</td>
                <td><?php echo count($unactiveDelivered) + count($unactiveUndelivered); ?></td>
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
	<h4>Listes des rendus actifs</h4>
    <div class="section">
        <table class="colored_table underlined_table">
	        <tr>
		        <th>Nom</th>
		        <th>Groupe</th> 
		        <th>Rendu</th>
		        <th>Extra</th>
	        </tr>
	        <?php
	            if(count($activeDelivered) + count($activeUndelivered) == 0)
	            { ?>
	                <tr><td colspan="4">Il n'y a aucun rendu actif</td></tr>
	            <?php
	            }
	            else 
	            {   
	                foreach($activeUndelivered as $delivery)
	                { 
                        $work = new PWHWork();
                        $work->Read($delivery->GetWorkID());
                        $subject = new PWHSubject();
                        $subject->Read($work->GetSubjectID());
                        
                        $currentDate = date('Y-m-d H:i:s');
                        
                        $deliveryDaysLeft = dateDiff($currentDate, $delivery->GetDeadline());
                        if($work->IsSimple() || $work->GetGroupMax() == 1)
                        {
                            $groupDaysLeft = 0;
                        }
                        else
                        {
                            $groupDaysLeft = dateDiff($currentDate, $delivery->GetGroupCompositionDeadline());
                        }
                        
                        $extraTimeLeft = 0;
                        if($work->GetExtraTime() > 0)
                        {
                            if($delivery->IsExtraTimeUsed($currentDate))
                            {
                                $extraTimeLeft = $deliveryDaysLeft + $work->GetExtraTime() * 86400;
                            }
                            else
                            {
                                $extraTimeLeft = $work->GetExtraTime() * 86400;
                            }
                        }
                        
                        $class = ' class="active_work"';
                        if($delivery->IsExtraTimeUsed(date("Y-m-d H:i:s")))
                        {
                            $class = ' class="extra_time_line"';
                        } 
                    ?>
	                <tr<?php echo $class; ?>>
		                <td>
		                    <a href="index.php?page=student_display_delivery&amp;subject_id=<?php echo $subject->GetID(); ?>&amp;work_id=<?php echo $work->GetID(); ?>&amp;delivery_id=<?php echo $delivery->GetID(); ?>">
		                        <img src="img/bullet_go.png"/><?php echo $subject->GetName().' / '.$work->GetName(); ?>
		                    </a>
		                </td>
		                <td id="group_days_left<?php echo $id; ?>"><?php if($groupDaysLeft < 0) { echo 0; } else { echo $groupDaysLeft; } ?></td>
		                <td id="delivery_days_left<?php echo $id; ?>"><?php if($deliveryDaysLeft < 0) { echo 0; } else { echo $deliveryDaysLeft; } ?></td>
	                    <td id="extra_time_left<?php echo $id; ?>"><?php echo $extraTimeLeft; ?></td>
	                </tr>           
               <?php 
                        $id++;
                    }
                    
                    foreach($activeDelivered as $delivery)
	                { 
                        $work = new PWHWork();
                        $work->Read($delivery->GetWorkID());
                        $subject = new PWHSubject();
                        $subject->Read($work->GetSubjectID());
                        
                        $currentDate = date('Y-m-d H:i:s');
                        
                        $deliveryDaysLeft = dateDiff($currentDate, $delivery->GetDeadline());
                        if($work->IsSimple() || $work->GetGroupMax() == 1)
                        {
                            $groupDaysLeft = 0;
                        }
                        else
                        {
                            $groupDaysLeft = dateDiff($currentDate, $delivery->GetGroupCompositionDeadline());
                        }
                        
                        $extraTimeLeft = 0;
                        if($work->GetExtraTime() > 0)
                        {
                            if($delivery->IsExtraTimeUsed($currentDate))
                            {
                                $extraTimeLeft = $deliveryDaysLeft + $work->GetExtraTime() * 86400;
                            }
                            else
                            {
                                $extraTimeLeft = $work->GetExtraTime() * 86400;
                            }
                        }
                    ?>
	                <tr class="delivered_line">
		                <td>
		                    <a href="index.php?page=student_display_delivery&amp;subject_id=<?php echo $subject->GetID(); ?>&amp;work_id=<?php echo $work->GetID(); ?>&amp;delivery_id=<?php echo $delivery->GetID(); ?>">
		                        <img src="img/bullet_go.png"/><?php echo $subject->GetName().' / '.$work->GetName(); ?>
		                    </a>
		                </td>
		                <td id="group_days_left<?php echo $id; ?>"><?php if($groupDaysLeft < 0) { echo 0; } else { echo $groupDaysLeft; } ?></td>
		                <td id="delivery_days_left<?php echo $id; ?>"><?php if($deliveryDaysLeft < 0) { echo 0; } else { echo $deliveryDaysLeft; } ?></td>
	                    <td id="extra_time_left<?php echo $id; ?>"><?php echo $extraTimeLeft; ?></td>
	                </tr>           
               <?php 
                        $id++;
                    }
	           } ?>
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
	                if(count($unactiveDelivered) + count($unactiveUndelivered) == 0)
	                { ?>
	                    <tr><td colspan="4">Il n'y a aucun rendu inactif</td></tr>
	                <?php
	                }
	                else 
	                {   
	                    $dateTranslator = new PWHDateTranslator();
	                    
	                    foreach($unactiveUndelivered as $delivery)
	                    { 
                            $work = new PWHWork();
                            $work->Read($delivery->GetWorkID());
                            $subject = new PWHSubject();
                            $subject->Read($work->GetSubjectID());
                        ?>
	                    <tr class="undelivered_line">
		                    <td>
		                        <a href="index.php?page=student_display_delivery&amp;subject_id=<?php echo $subject->GetID(); ?>&amp;work_id=<?php echo $work->GetID(); ?>&amp;delivery_id=<?php echo $delivery->GetID(); ?>">
		                            <img src="img/bullet_go.png"/><?php echo $subject->GetName().' / '.$work->GetName(); ?>
		                        </a>
		                    </td>
		                    <td><?php echo $dateTranslator->Html($delivery->GetDeadline(), PWHDateTranslator::DATE_AND_TIME); ?></td>
	                    </tr>           
                   <?php 
                            $id++;
                        }
                        
                        foreach($unactiveDelivered as $delivery)
	                    { 
                            $work = new PWHWork();
                            $work->Read($delivery->GetWorkID());
                            $subject = new PWHSubject();
                            $subject->Read($work->GetSubjectID());
                        ?>
	                    <tr class="unactive_work">
		                    <td>
		                        <a href="index.php?page=student_display_delivery&amp;subject_id=<?php echo $subject->GetID(); ?>&amp;work_id=<?php echo $work->GetID(); ?>&amp;delivery_id=<?php echo $delivery->GetID(); ?>">
		                            <img src="img/bullet_go.png"/><?php echo $subject->GetName().' / '.$work->GetName(); ?>
		                        </a>
		                    </td>
		                    <td><?php echo $dateTranslator->Html($delivery->GetDeadline(), PWHDateTranslator::DATE_AND_TIME); ?></td>
	                    </tr>           
                   <?php 
                            $id++;
                        }
	               } ?>
            </table>
        </div>
    </div>
</fieldset>
<script type="text/javascript">
<!--
    var numberDeliveries = <?php echo count($activeDelivered) + count($activeUndelivered); ?>;
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
            if(counterDeliveries[i] <= 0)
            {
                counterDeliveries[i] = 0;
            }
            counter.innerHTML = dateHMS(counterDeliveries[i]);
            
            counter = document.getElementById("group_days_left".concat(i));
            counterGroups[i] -= 1;
            if(counterGroups[i] <= 0)
            {
                counterGroups[i] = 0;
            }
            counter.innerHTML = dateHMS(counterGroups[i]);
            
            if(counterDeliveries[i] <= 0)
            {
                counter = document.getElementById("extra_time_left".concat(i));
                counterExtraTime[i] -= 1;
                if(counterExtraTime[i] <= 0)
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

