<?php
    $GLOBALS['PWH_PATH'] = "../";
    require_once($GLOBALS['PWH_PATH'] . 'libpwh/PWHHeader.php');
    require_once($GLOBALS['PWH_PATH'] . 'include/util.php');

    
    function MakeIndex($delivery, $path, $enableWork)
    {
        $oldPath = getcwd();
        chdir($path);
        $index = sys_get_temp_dir() . "/index-" . $delivery->GetName() . ".html";
        $file = fopen($index, "w");
        
        $work = new PWHWork();
        $work->Read($delivery->GetWorkID());
        $subject = new PWHSubject();
        $subject->Read($work->GetSubjectID());
                    
        $deliverygroups = $delivery->GetDeliverygroups();
        $freeStudents = $delivery->GetFreeStudents();
    
        $dateTranslator = new PWHDateTranslator();
        
        fprintf($file, '<html><head><title>Compte rendu de ' . $subject->GetName() . "-" . $work->GetName() . "-" . $delivery->GetName() . '</title><link rel="stylesheet" type="text/css" href="css/pwh.css"/></head><body>');
        fprintf($file, '<h4>Informations</h4>');
        fprintf($file, '<div class="section"><p>');
        fprintf($file, 'Ce fichier a &eacute;t&eacute; g&eacute;n&eacute;r&eacute; par la plateforme de d&eacute;p&ocirc;t le ' . $dateTranslator->Html(date("Y-m-d H:i:s"), PWHDateTranslator::DATE_AND_TIME) . " pour le rendu " . $subject->GetName() . "-" . $work->GetName() . "-" . $delivery->GetName() . ".");
        fprintf($file, '</p></div>');
        fprintf($file, "<h4>Groupes de rendu existants</h4>");
	    fprintf($file, '<div class="section">');
        if(count($deliverygroups) > 0)
        {
            $strbuf = "";
            foreach($deliverygroups as $deliverygroup)
            {
                $strbuf .= $deliverygroup->GetEmail();    
            }
                fprintf($file, '<a href="mailto:'. $strbuf . '"><img src="img/email.png"/>Email du groupe d\'&eacute;tudiants ayant un groupe de rendu</a>');
        }
	    
	    fprintf($file, '<table class="colored_table underlined_table">');
	    fprintf($file, '<tr><th>Archive</th><th>Email</th><th>Membres</th><th>Cr&eacute;ation</th><th>Livraison</th></tr>');
        if(count($deliverygroups) == 0)
        {
            fprintf($file, '<tr><td colspan="5">Il n\'y a aucun groupe de rendu</td></tr>');
        }
        else
        { 
            foreach($deliverygroups as $deliverygroup)
            { 
                $students = $deliverygroup->GetStudents();
                $class = "";
                if($delivery->IsExtraTimeUsed(date("Y-m-d H:i:s")) && ($deliverygroup->GetLastDelivery() == "" || $deliverygroup->IsExtraTimeUsed()))
                {
                    $class = ' class="extra_time_line"';
                }
                else if(!$delivery->IsStillTimeForDelivery(date("Y-m-d H:i:s")) && $deliverygroup->IsExtraTimeUsed())
                {
                    $class = ' class="extra_time_line"';
                }
                else if(!$delivery->IsStillTimeForDelivery(date("Y-m-d H:i:s")) && $deliverygroup->GetLastDelivery() == "")
                {
                    $class = ' class="undelivered_line"';
                }
                
                fprintf($file, '<tr' . $class . '>');
                if($enableWork)
                {
                    fprintf($file,'<td><a href="' . $work->GetID() . "/" . $delivery->GetName() . '/' . $deliverygroup->GetName() . '"><img src="img/package_go.png"/></a></td>');
                }
                else
                {
                    fprintf($file,'<td><a href="' . $delivery->GetName() . '/' . $deliverygroup->GetName() . '"><img src="img/package_go.png"/></a></td>');
                }
                fprintf($file, '<td><a href="mailto:' . $deliverygroup->GetEmail() . '"><img src="img/email.png"/></a></td>');

                fprintf($file, '<td>');
                $strbuf = "";
                foreach($students as $student)
                {
                    $strbuf .= '<a href="mailto:' . $student->GetEmail() . '">' . $student->GetLastName() . " " . $student->GetFirstName() . "</a>, ";
                } 
                $strbuf = substr($strbuf, 0, strlen($strbuf) - 2);
                fprintf($file, $strbuf . '</td>');
                fprintf($file, '<td>' . $dateTranslator->Html($deliverygroup->GetCreation(), PWHDateTranslator::DATE_AND_TIME) . '</td>');
	            fprintf($file, '<td>' . $dateTranslator->Html($deliverygroup->GetLastDelivery(), PWHDateTranslator::DATE_AND_TIME) . '</td>');
            }
        }
        fprintf($file, '</table></div>');
        
        fprintf($file, '<h4>El&egrave;ves sans groupe de rendus</h4>');
        fprintf($file, ' <div class="section">');
  
        if(count($freeStudents) > 0)
        {
            $strbuf = "";
            foreach($freeStudents as $freeStudent)
            {
                $strbuf .= $freeStudent->GetEmail() . ";";    
            }
            $strbuf = substr($strbuf, 0, strlen($strbuf) - 1);
                
            fprintf($file, '<a href="mailto:' . $strbuf . '"><img src="img/email.png"/>Email du groupe d\'&eacute;tudiants sans groupe de rendu</a>');
        } 

        usort($freeStudents, "person_comparator");
        
        fprintf($file, '<table class="colored_table underlined_table"><tr><th>Email</th><th>Nom</th><th>Pr&eacute;nom</th></tr>');
        if(count($freeStudents) == 0)
        {         
            fprintf($file, '<tr><td colspan="3">Il n\'y a aucun &eacute;tudiants sans groupe de rendu</td></tr>');    
        }
        else
        {
            foreach($freeStudents as $freeStudent)
            {
                fprintf($file, '<tr><td><a href="mailto:' . $freeStudent->GetEmail(). '"><img src="img/email.png"/></a></td>');
                fprintf($file, '<td>' . $freeStudent->GetLastName() . '</td>');
                fprintf($file, '<td>' . $freeStudent->GetFirstName() . '</td></tr>');
            }
        }
        fprintf($file, '</table></div></body></html>');
        
        fclose($file);
        chdir($oldPath);
        return $index;
    }
    
    function MakeCSV($delivery, $path)
    {
        $oldPath = getcwd();
        chdir($path);
        $csv = sys_get_temp_dir() . "/tableur-" . $delivery->GetName() . ".csv";
        $file = fopen($csv, "w");
                    
        $deliverygroups = $delivery->GetDeliverygroups();
        $freeStudents = $delivery->GetFreeStudents();
        usort($freeStudents, "person_comparator");
            
        fprintf($file, "numero_groupe;nom;prénom;date_rendu;note;observation;\n");

        $numGroup = 1;
        foreach($deliverygroups as $deliverygroup)
        {
            $students = $deliverygroup->GetStudents();
            usort($students, "person_comparator");
            foreach($students as $student)
            {
                $strbuf = $numGroup++ . ";" ;
                $strbuf .= $student->GetLastName() . ";";
                $strbuf .= $student->GetFirstName() . ";";
                $strbuf .= $deliverygroup->GetLastDelivery() . ";";
                $strbuf .= ";;\n";
                fprintf($file, $strbuf);
            }
        }
        
        foreach($freeStudents as $freeStudent)
        {
            $strbuf = ";";
            $strbuf .= $freeStudent->GetLastName() . ";";
            $strbuf .= $freeStudent->GetFirstName() . ";";
            $strbuf .= ";;;\n";
            fprintf($file, $strbuf);
        }
        
        fclose($file);
        chdir($oldPath);
        return $csv;
    }
    
    /*function MakeCSV($delivery, $path)
    {
        $oldPath = getcwd();
        chdir($path);
        $csv = sys_get_temp_dir() . "/tableur-" . $delivery->GetName() . ".csv";
        $file = fopen($csv, "w");
                    
        $groups = $delivery->GetGroups();
        $students = array();
        foreach($groups as $group)
        {
            $students = array_merge($students, $group->GetStudents());
        }
        usort($students, "person_comparator");
            
        fprintf($file, "numero_groupe;nom;prénom;date_rendu;note;observation;\n");

        foreach($students as $student)
        { 
            $id = "";
            $date = "";
            if($student->HasDeliverygroup($delivery->GetID()))
            {
                $deliverygroup = $student->GetDeliverygroup($delivery->GetID());
                $id = $deliverygroup->GetID();
                $date = $deliverygroup->GetLastDelivery();
            }
           
            $strbuf = $id . ";" ;
            $strbuf .= $student->GetLastName() . ";";
            $strbuf .= $student->GetFirstName() . ";";
            $strbuf .= $ddate . ";";
            $strbuf .= ";;\n";
            fprintf($file, $strbuf);
        }
        
        fclose($file);
        chdir($oldPath);
        return $csv;
    }*/
    
    if(isset($_GET['type']) && $_GET['type'] == 'work')
    {    
        if(isset($_GET['subject_id']) && isset($_GET['work_id']))
        {
            try
            {
                $subject = new PWHSubject();
                $subject->Read($_GET['subject_id']);
            }
            catch(Exception $ex)
            {
                die();
            }
            
            try
            {
                $work = new PWHWork();
                $work->Read($_GET['work_id']);
            }
            catch(Exception $ex)
            {
                die();
            }
        
            $oldPath = getcwd();
            $deliveries = $work->GetDeliveries();
            chdir($subject->GetPath());
            $filename = tempnam(sys_get_temp_dir(), "pwh-") . ".zip";
           
            exec("zip -r " . $filename . " " . $work->GetID());
            foreach($deliveries as $delivery)
            {
                $index = MakeIndex($delivery, $oldPath, true);
                $csv = MakeCSV($delivery, $oldPath);
                exec("zip -j -u " . $filename . " " . $index);
                exec("zip -j -u " . $filename . " " . $csv);
            }
            chdir($oldPath);
            
            exec("zip -r -u " . $filename . " css/");
            exec("zip -r -u " . $filename . " img/");
            
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachement; filename="'. str_replace(" ", "_", $subject->GetName()) . "-" . str_replace(" ", "_", $work->GetName()) . '.zip"');
            header('Content-Transfer-Encoding: binary');
            echo file_get_contents($filename);
        }
    }
    else if(isset($_GET['type']) && $_GET['type'] == 'delivery')
    {
        if(isset($_GET['subject_id']) && isset($_GET['work_id']) && isset($_GET['delivery_id']))
        {
            try
            {
                $subject = new PWHSubject();
                $subject->Read($_GET['subject_id']);
            }
            catch(Exception $ex)
            {
                die();
            }
            
            try
            {
                $work = new PWHWork();
                $work->Read($_GET['work_id']);
            }
            catch(Exception $ex)
            {
                die();
            }
            
            try
            {
                $delivery = new PWHDelivery();
                $delivery->Read($_GET['delivery_id']);
            }
            catch(Exception $ex)
            {
                die();
            }        
        
            $oldPath = getcwd();
            chdir($work->GetPath());
            $filename = tempnam(sys_get_temp_dir(), "pwh-") . ".zip";
            $index = MakeIndex($delivery, $oldPath, false);
            $csv = MakeCSV($delivery, $oldPath);
            exec("zip -r " . $filename . " " . $delivery->GetName());
            exec("zip -j -u " . $filename . " " . $index);
            exec("zip -j -u " . $filename . " " . $csv);
            chdir($oldPath);
            
            exec("zip -r -u " . $filename . " css/");
            exec("zip -r -u " . $filename . " img/");
            
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachement; filename="'. str_replace(" ", "_", $subject->GetName()) . "-" . str_replace(" ", "_", $work->GetName()) . "-" . str_replace(" ", "_", $delivery->GetName()) . '.zip"');
            header('Content-Transfer-Encoding: binary');
            echo file_get_contents($filename);
        }
    }
    else if(isset($_GET['type']) && $_GET['type'] == 'deliverygroup')
    {
        if(isset($_GET['delivery_id']) && isset($_GET['id']))
        {
            try
            {
                $delivery = new PWHDelivery();
                $delivery->Read($_GET['delivery_id']);
            }
            catch(Exception $ex)
            {
                die();
            }
            
            try
            {
                $deliverygroup = new PWHDeliverygroup();
                $deliverygroup->Read($_GET['id']);
            }
            catch(Exception $ex)
            {
                die();
            }
            
            $groupName =  $deliverygroup->GetName();
            $oldPath = getcwd();
            chdir($delivery->GetPath());
            $filename = tempnam(sys_get_temp_dir(), "pwh-") . ".zip";
            exec("zip -r " . $filename . " " . $groupName);
            chdir($oldPath);
            
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachement; filename="'. $groupName . '.zip"');
            header('Content-Transfer-Encoding: binary');
            echo file_get_contents($filename);
        }
    }
    else if(isset($_GET['type']) && $_GET['type'] == 'bd_sqlite')
    {  
        $oldPath = getcwd();
        chdir("../");
        $filename = tempnam(sys_get_temp_dir(), "pwh-") . ".zip";
        exec("zip -r " . $filename . " database/ uploads/");
        chdir($oldPath);
        
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachement; filename="pwh-bd-sqlite-backup.zip"');
        header('Content-Transfer-Encoding: binary');
        echo file_get_contents($filename);
    }
    else if(isset($_GET['type']) && $_GET['type'] == 'log_sqlite')
    {  
        $oldPath = getcwd();
        chdir("../");
        $filename = tempnam(sys_get_temp_dir(), "pwh-") . ".zip";
        exec("zip -r " . $filename . " log/");
        chdir($oldPath);
        
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachement; filename="pwh-log-sqlite-backup.zip"');
        header('Content-Transfer-Encoding: binary');
        echo file_get_contents($filename);
    }
?>
