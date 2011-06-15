<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page student_list_deliveries");
    
    previousPage('student_home');
    $failed = false;
    $studentName = "???";
    
    try
    {
        $student = new PWHStudent();
        $student->Read($_SESSION['id']);
        $groups = $student->GetGroups();
        
        $deliveries = array();
        foreach($groups as $group)
        {
            $deliveries = array_merge($deliveries, $group->GetDeliveries(false));
        }
        
        $level = 0;
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
        $inactiveDelivered = array();
        $inactiveUndelivered = array();
        
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
                   array_push($inactiveDelivered, $delivery);
                }
                else
                {
                    array_push($inactiveUndelivered, $delivery);
                }
            }
        }
        
        $id=1;
        $link = '<a class="next_form" id="toggle" href="javascript:toggle();"><img src="img/zoom_in.png"/>Voir les rendus inactifs</a>';
        $studentName = mb_strtolower($student->GetFirstName() . " " . $student->GetLastName());
    }
    catch(Exception $ex)
    {
        $failed = true;
    }
    
    if($failed)
    {
        errorReport("Impossible d'afficher la page demand&eacute;e.");
    }
?>

<section>
	<h2>Travaux de <?php echo $studentName; ?></h2>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/student/help/list_deliveries.html', 800, 550);");
        
	    displayErrorReport();
	    displaySuccessReport();
	    
	    if(!$failed)
	    {
	?>
	<h4>Statistiques</h4>
	<div class="section">
	    <table class="summary">
	    <?php
	        $workLevel = '-';
            if($level == 1)
            {
                $workLevel = $level . " heure"; 
            }
            else if($level > 1)
            {
                $workLevel = $level . " heures";
            }
            $summary = new PWHSummary();
            $summary->SetInfo('Nombre de travaux actifs', count($activeDelivered) + count($activeUndelivered));
            $summary->SetInfo('Nombre de travaux inactifs', count($inactiveDelivered) + count($inactiveUndelivered));
            $summary->SetInfo('Charge de travail approximative', $workLevel);
            echo $summary->HTML();
        ?>
	</div>
	<?php
	    	    
	    $legend = new PWHLegend();
	    $legend->SetType($_SESSION['type']);
	    echo $legend->Html();
	?>
	<h4>Listes des travaux actifs</h4>
    <div class="section">
        <?php
            $activeTable = new PWHActiveDeliveriesTable();
            $activeTable->SetUndelivered($activeUndelivered);
            $activeTable->SetDelivered($activeDelivered);
            echo $activeTable->HTML();
        ?>
    </div>
    <div class="section">
	    <?php echo $link; ?>
	</div>
	<div id="inactive">
        <h4>Listes des travaux inactifs</h4>
        <div class="section">
            <?php
                $inactiveTable = new PWHInactiveDeliveriesTable();
                $inactiveTable->SetUndelivered($inactiveUndelivered);
                $inactiveTable->SetDelivered($inactiveDelivered);
                echo $inactiveTable->HTML();
            ?>
        </div>
    </div>
    <?php } ?>
</section>
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

