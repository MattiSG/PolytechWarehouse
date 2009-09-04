<?php
    $GLOBALS['PWH_PATH'] = "../";
    require_once($GLOBALS['PWH_PATH'] . 'libpwh/PWHHeader.php');
    require_once($GLOBALS['PWH_PATH'] . 'include/util.php');
    	
    if(isset($_GET['type']) && $_GET['type'] == "student")
    {
        if(isset($_GET['student_id']) && PWHEntity::Valid("PWHStudent", $_GET['student_id']) && isset($_GET['action']) && $_GET['action'] == 'show_cal')
        {
            $student = new PWHStudent();
            $student->Read($_GET['student_id']);
            $groups = $student->GetGroups();
            
            header("Content-Type: text/calendar");
            header("Content-Disposition: inline; filename=" . $student->GetLogin() . "-calendar.ics");
            
            echo "BEGIN:VCALENDAR\n";
            echo "VERSION:2.0\n";
            echo "PRODID:-//polytech.unice.fr\n";

            foreach($groups as $group)
            {
                $deliveries = $group->GetDeliveries(true);
                
                foreach($deliveries as $delivery)
                { 
                    $work = new PWHWork();
                    $work->Read($delivery->GetWorkID());
                    if($work->IsPublished())
                    {
                        $subject = new PWHSubject();
                        $subject->Read($work->GetSubjectID());
                        
                        $timestamp = $delivery->GetDeadline();
                        $date = substr($timestamp, 0, 10);
                        $time = substr($timestamp, 11, strlen($timestamp));
                        
                        $date = explode('-', $date);
                        $time = explode(':', $time);
            
                        echo "BEGIN:VEVENT\n";
                        echo "DTSTART:" . $date[0] . $date[1] . $date[2] . "T" . $time[0] . $time[1] . $time[2] . "Z\n";
                        echo "DTSTAMP:" . date("Ymd\THis") . "Z\n";
                        echo "UID:" . md5(uniqid(mt_rand(), true)) . "@polytech.unice.fr\n";
                        echo "CREATED:" . date("Ymd\THis") . "Z\n";
                        echo "DESCRIPTION:Fin du travail " . $subject->GetName() . "-" . $work->GetName() . "\n";
                        echo "LAST-MODIFIED:" . date("Ymd\TH:i:s") . "Z\n";
                        echo "CATEGORY:PROJECTS\n";
                        echo "LOCATION:\n";
                        echo "SEQUENCE:0\n";
                        echo "STATUS:CONFIRMED\n";
                        echo "SUMMARY:Rendu " . $subject->GetName() . "-" . $work->GetName() . "\n";
                        echo "TRANSP:OPAQUE\n";
                        echo "DTEND:" . $date[0] . $date[1] . $date[2] . "T" . $time[0] . $time[1] . $time[2] . "Z\n";
                        echo "END:VEVENT\n";
                        
                        if(!$work->IsSimple() && $work->GetGroupMax() > 1)
                        {
                            $timestamp = $delivery->GetGroupCompositionDeadline();
                            $date = substr($timestamp, 0, 10);
                            $time = substr($timestamp, 11, strlen($timestamp));
                            
                            $date = explode('-', $date);
                            $time = explode(':', $time);
                
                            echo "BEGIN:VEVENT\n";
                            echo "DTSTART:" . $date[0] . $date[1] . $date[2] . "T" . $time[0] . $time[1] . $time[2] . "Z\n";
                            echo "DTSTAMP:" . date("Ymd\THis") . "Z\n";
                            echo "UID:" . md5(uniqid(mt_rand(), true)) . "@polytech.unice.fr\n";
                            echo "CREATED:" . date("Ymd\THis") . "Z\n";
                            echo "DESCRIPTION: Fin de la periode de composition des groupes de rendu du travail " . $subject->GetName() . "-" . $work->GetName() . "\n";
                            echo "LAST-MODIFIED:" . date("Ymd\THis") . "Z\n";
                            echo "CATEGORY:PROJECTS\n";
                            echo "LOCATION:\n";
                            echo "SEQUENCE:0\n";
                            echo "STATUS:CONFIRMED\n";
                            echo "SUMMARY:Rendu " . $subject->GetName() . "-" . $work->GetName() . " (Composition des groupes)\n";
                            echo "TRANSP:OPAQUE\n";
                            echo "DTEND:" . $date[0] . $date[1] . $date[2] . "T" . $time[0] . $time[1] . $time[2] . "Z\n";
                            echo "END:VEVENT\n";
                        }
                    }           
                }
           }
            
           echo "END:VCALENDAR\n";
        }
    }
    else if(isset($_GET['type']) && $_GET['type'] == "group")
    {
        if(isset($_GET['group_id']) && PWHEntity::Valid("PWHGroup", $_GET['group_id']) && isset($_GET['action']) && $_GET['action'] == 'show_cal')
        {
            $group = new PWHGroup();
            $group->Read($_GET['group_id']);
            
            header("Content-Type: text/calendar");
            header("Content-Disposition: inline; filename=" . $group->GetName() . "-calendar.ics");
            
            echo "BEGIN:VCALENDAR\n";
            echo "VERSION:2.0\n";
            echo "PRODID:-//polytech.unice.fr\n";

            $deliveries = $group->GetDeliveries(true);
                
            foreach($deliveries as $delivery)
            { 
                $work = new PWHWork();
                $work->Read($delivery->GetWorkID());
                if($work->IsPublished())
                {
                    $subject = new PWHSubject();
                    $subject->Read($work->GetSubjectID());
                    
                    $timestamp = $delivery->GetDeadline();
                    $date = substr($timestamp, 0, 10);
                    $time = substr($timestamp, 11, strlen($timestamp));
                    
                    $date = explode('-', $date);
                    $time = explode(':', $time);
        
                    echo "BEGIN:VEVENT\n";
                    echo "DTSTART:" . $date[0] . $date[1] . $date[2] . "T" . $time[0] . $time[1] . $time[2] . "Z\n";
                    echo "DTSTAMP:" . date("Ymd\THis") . "Z\n";
                    echo "UID:" . md5(uniqid(mt_rand(), true)) . "@polytech.unice.fr\n";
                    echo "CREATED:" . date("Ymd\THis") . "Z\n";
                    echo "DESCRIPTION:Fin du travail " . $subject->GetName() . "-" . $work->GetName() . "\n";
                    echo "LAST-MODIFIED:" . date("Ymd\TH:i:s") . "Z\n";
                    echo "CATEGORY:PROJECTS\n";
                    echo "LOCATION:\n";
                    echo "SEQUENCE:0\n";
                    echo "STATUS:CONFIRMED\n";
                    echo "SUMMARY:Rendu " . $subject->GetName() . "-" . $work->GetName() . "\n";
                    echo "TRANSP:OPAQUE\n";
                    echo "DTEND:" . $date[0] . $date[1] . $date[2] . "T" . $time[0] . $time[1] . $time[2] . "Z\n";
                    echo "END:VEVENT\n";
                    
                    if(!$work->IsSimple() && $work->GetGroupMax() > 1)
                    {
                        $timestamp = $delivery->GetGroupCompositionDeadline();
                        $date = substr($timestamp, 0, 10);
                        $time = substr($timestamp, 11, strlen($timestamp));
                        
                        $date = explode('-', $date);
                        $time = explode(':', $time);
            
                        echo "BEGIN:VEVENT\n";
                        echo "DTSTART:" . $date[0] . $date[1] . $date[2] . "T" . $time[0] . $time[1] . $time[2] . "Z\n";
                        echo "DTSTAMP:" . date("Ymd\THis") . "Z\n";
                        echo "UID:" . md5(uniqid(mt_rand(), true)) . "@polytech.unice.fr\n";
                        echo "CREATED:" . date("Ymd\THis") . "Z\n";
                        echo "DESCRIPTION: Fin de la periode de composition des groupes de rendu du travail " . $subject->GetName() . "-" . $work->GetName() . "\n";
                        echo "LAST-MODIFIED:" . date("Ymd\THis") . "Z\n";
                        echo "CATEGORY:PROJECTS\n";
                        echo "LOCATION:\n";
                        echo "SEQUENCE:0\n";
                        echo "STATUS:CONFIRMED\n";
                        echo "SUMMARY:Rendu " . $subject->GetName() . "-" . $work->GetName() . " (Composition des groupes)\n";
                        echo "TRANSP:OPAQUE\n";
                        echo "DTEND:" . $date[0] . $date[1] . $date[2] . "T" . $time[0] . $time[1] . $time[2] . "Z\n";
                        echo "END:VEVENT\n";
                    }
                }           
            }
            
           echo "END:VCALENDAR\n";
        }
    }
?>

