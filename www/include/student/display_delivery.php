<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page student_display_delivery");
     
    previousPage('student_list_deliveries');   
    $failed = false;
    $workName = "???";
    
    try
    {
        $student = new PWHStudent();
        $student->Read($_SESSION['id']);
    }
    catch(Exception $ex)
    {
        errorReport($ex->getMessage());
        $failed = true;
    }
     
    if(isset($_GET['subject_id']) && isset($_GET['work_id']) && isset($_GET['delivery_id'])
        && PWHEntity::Valid("PWHSubject", $_GET['subject_id']) 
        && PWHEntity::Valid("PWHWork", $_GET['work_id']) 
        && PWHEntity::Valid("PWHDelivery", $_GET['delivery_id']))
    {
        try
        {
            $subject = new PWHSubject();
            $subject->Read($_GET['subject_id']);
        }
        catch(Exception $ex)
        {
            $failed = true;
        }
        
        try
        {
            $work = new PWHWork();
            $work->Read($_GET['work_id']);
        }
        catch(Exception $ex)
        {
            $failed = true;
        }
        
        try
        {
            $exist = false;
            $delivery = new PWHDelivery();
            $delivery->Read($_GET['delivery_id']);
            $groups = $delivery->GetGroups();
            foreach($groups as $group)
            {
                if($group->StudentExists($student->GetID()))
                {
                    $exist = true;
                }
            }
            
            if(!$exist)
            {
                PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Acc&egrave;s page display_delivery sur rendu inattendu");
            }
        }
        catch(Exception $ex)
        {
            $failed = true;
        }
    }
    else
    {
        PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Acc&egrave;s page display_delivery avec param&egrave;tres URL absents ou corrompus");
        $failed = true;
    }   
    
    if(!$failed)
    {
        $files = $work->GetFiles();
        foreach($files as $name=>$format)
        {
            if(isset($_FILES[$name]))
            {
                if($_FILES[$name]['error'] == 0)
                {
                    $fileInfo = pathinfo($_FILES[$name]['name']);
                    $fileName = (string)basename($_FILES[$name]['name']);
                    $patterns = PWHMetaType::GetPatterns($format);
                    
                    $validFormat = false;
                    foreach($patterns as $pattern)
                    {    
                        if(preg_match($pattern, $fileName))
                        {
                            $validFormat = true;
                        }
                    }
                    
                    if($delivery->IsStillTimeForDelivery(date("Y-m-d H:i:s")))
                    {
                        if($validFormat)
                        {
                            if($work->GetSize() == 0 || $work->GetSize() > 0 && $_FILES[$name]['size'] <= $work->GetSize() * 1024 * 1024)
                            {
                                if(!$student->HasDeliverygroup($delivery->GetID()))
                                {
                                    $deliverygroup = new PWHDeliverygroup();
                                    $deliverygroup->SetDeliveryID($delivery->GetID());
                                    $deliverygroup->SetCreation(date("Y-m-d H:i:s"));
                                    $deliverygroup->AddStudents(array($student->GetID()));
                                    $deliverygroup->Create(true);
                                    $deliverygroup->CreateDirectory();
                                }
                                else
                                {
                                    $deliverygroup = $student->GetDeliverygroup($delivery->GetID());
                                }
                                
                                $directory = $deliverygroup->GetPath();
                                exec("rm -rf " . $directory . $subject->GetName() . "-" . $work->GetName() . "-" . $deliverygroup->GetName() . "-" . $name . "*");
                                move_uploaded_file($_FILES[$name]['tmp_name'], $directory . $subject->GetName() . "-" . $work->GetName() . "-" . $deliverygroup->GetName() . "-" . $name . "." . $fileInfo['extension']);
                                $deliverygroup->SetLastDelivery(date("Y-m-d H:i:s"));
                                if($delivery->IsExtraTimeUsed(date("Y-m-d H:i:s")))
                                {
                                    $deliverygroup->SetExtraTimeUsed(true);
                                }
                                $deliverygroup->Update();
                                
                                $targets = $deliverygroup->GetStudents();
                                PWHEvent::Notify($targets, STUDENT_TYPE, $student->GetLastName() . " " . $student->GetFirstName() . " a effectu&eacute; une livraison dans le travail " . $subject->GetName() . "-" . $work->GetName());
                
                                successReport("Le rendu a &eacute;t&eacute; livr&eacute;.");
                                PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Envoi du rendu " . $work->GetName() . "-" . $delivery->GetName());
                            }
                            else
                            {
                                errorReport("Le rendu d&eacute;passe la taille maximale autoris&eacute;e.");
                                PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Echec de l'envoi du rendu " . $work->GetName() . "-" . $delivery->GetName() . ": taille non respect&eacute;e");
                            }
                        }
                        else
                        {
                            errorReport(basename($_FILES[$name]['name']) . " n'est pas un fichier au format valide.");
                            PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Echec de l'envoi du rendu " . $work->GetName() . "-" . $delivery->GetName() . ": format " . $fileInfo['extension'] . " invalide");
                        }    
                    }
                    else
                    {
                        errorReport("La date de rendu est d&eacute;pass&eacute;e."); 
                        PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Echec de l'envoi du rendu " . $work->GetName() . "-" . $delivery->GetName() . ": date de rendu d&eacute;pass&eacute;e");   
                    }
                }
                else
                {
                    switch($_FILES[$name]['error'])
                    {
                        case UPLOAD_ERR_NO_FILE:
                            errorReport("Erreur lors de l'envoi du fichier: fichier manquant.");
                            PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec de l'envoi du fichier: fichier manquant");
                            break;
                        case UPLOAD_ERR_INI_SIZE: case UPLOAD_ERR_FORM_SIZE:
                            errorReport("Erreur lors de l'envoi du fichier: fichier trop volumineux.");
                            PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec de l'envoi du fichier: fichier trop volumineux");
                            break;
                        case UPLOAD_ERR_PARTIAL:
                            errorReport("Erreur lors de l'envoi du fichier: transfert partiel.");
                            PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec de l'envoi du fichier: transfert partiel");
                            break;
                        default:
                            errorReport("Erreur lors de l'envoi du fichier.");
                            PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec de l'envoi du fichier.");
                            break;
                    }
                }
            }
        }
        
        $dateTranslator = new PWHDateTranslator();
        $memos = array();
        if($work->IsSimple() && $work->GetGroupMax() > 1)
        {
            $memo = new PWHMemo();
            $memo->SetText("Ce rendu exige la cr&eacute;ation de groupes de rendu qui ne sont pas modifiables une fois cr&eacute;&eacute;s. Fa&icirc;tes attention quant au choix des membres de votre groupe.");
            array_push($memos, $memo);
        }
        
        if(!$work->IsSimple() && $work->GetGroupMax() > 1 && !$delivery->IsStillTimeForGroupComposition(date("Y-m-d H:i:s")))
        {
            $memo = new PWHMemo();
            $memo->SetText("La date de composition des groupes est d&eacute;pass&eacute;e. Vous ne pouvez plus cr&eacute;er ou modifier votre groupe.");
            array_push($memos, $memo);
        }
        
        if(!$work->IsSimple() && $work->GetGroupMax() > 1 && $delivery->IsStillTimeForGroupComposition(date("Y-m-d H:i:s")))
        {
            $memo = new PWHMemo();
            $memo->SetText("La date limite de composition des groupes n'est pas atteinte. Vous pouvez composer et modifier votre groupe mais vous ne pouvez pas encore effectuer de livraison.");
            array_push($memos, $memo);
        }
        
        if($delivery->IsExtraTimeUsed(date("Y-m-d H:i:s")))
        {
            $memo = new PWHMemo();
            $memo->SetText("La date de rendu est d&eacute;pass&eacute;e. Vous pouvez cependant effectuer des livraisons mais elles seront consid&eacute;r&eacute;es comme non-conformes &agrave la date de rendu.");
            array_push($memos, $memo);
        }
        
        if(!$delivery->IsStillTimeForDelivery(date("Y-m-d H:i:s")))
        {
            $memo = new PWHMemo();
            $memo->SetText("La date de rendu est d&eacute;pass&eacute;e. Vous ne pouvez plus effectuer de livraison.");
            array_push($memos, $memo);
        }
        
        $teacher = new PWHTeacher();
        $teacher->Read($delivery->GetOwnerID());
        $workName = mb_strtolower($subject->GetName() . " / " . $work->GetName());
    }
    
    if($failed)
    {
        errorReport("Impossible d'afficher la page demand&eacute;e.");
    }
    else if(!$failed && !$exist)
    {      
        errorReport("Vous n'&ecirc;tes pas concern&eacute; par ce rendu.");
    }
?>
<fieldset>
	<legend><?php echo 'travail ' . $workName; ?></legend>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/student/help/display_delivery.html', 800, 550);");
        
        displayErrorReport();
	    displaySuccessReport();   
    if(!$failed && $exist)
    {     
	    foreach($memos as $memo)
	    {
	        echo $memo->Html();
	    }
    ?>
    <h4>Gestion du groupe de rendu</h4>
    <div class="section">
        <div class="list">
            <ul>
                <?php
                    if((($work->IsSimple() && $delivery->IsStillTimeForDelivery(date("Y-m-d H:i:s")))
                         || (!$work->IsSimple() && $work->GetGroupMax() > 1 && $delivery->IsStillTimeForGroupComposition(date("Y-m-d H:i:s"))))
                         && 
                            ((!$student->HasDeliverygroup($delivery->GetID()) && $work->GetGroupMax() > 1) || 
                            ($student->HasDeliverygroup($delivery->GetID()) 
                                && count($student->GetDeliverygroup($delivery->GetID())->GetStudents()) < $work->GetGroupMax()
                                && !$student->GetDeliverygroup($delivery->GetID())->IsSuper())))
                    { ?>
                <li><a href="index.php?page=student_create_deliverygroup&amp;subject_id=<?php echo $subject->GetID(); ?>&amp;work_id=<?php echo $work->GetID(); ?>&amp;delivery_id=<?php echo $delivery->GetID(); ?>&amp;index=A"><img src="img/group_add"/>Composer un groupe de rendu</a></li>
                <?php
                    }
                    if($student->HasDeliverygroup($delivery->GetID()) && $work->GetGroupMax() > 1)  
                    {
                    ?>
                <li><a href="index.php?page=student_display_deliverygroup&amp;subject_id=<?php echo $subject->GetID(); ?>&amp;work_id=<?php echo $work->GetID(); ?>&amp;delivery_id=<?php echo $delivery->GetID(); ?>"><img src="img/group_go"/>Voir les informations sur le groupe de rendu</a><li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <h4>R&eacute;capitulatif</h4>
	<div class="section">
        <table class="summary">
            <tr>
                <td>Responsable</td>
                <td><a href="mailto:<?php echo $teacher->GetEmail(); ?>"><img src="img/email.png"/><?php echo $teacher->GetLastName() . " " . $teacher->GetFirstName(); ?></a></td>
            </tr>
            <tr>
                <td>Site web du sujet</td>
                <td>
                    <?php
                        if($work->GetLink() != "")
                        { ?>
                    <a href="<?php echo $work->GetLink(); ?>"><img src="img/world.png"/><?php echo $work->GetLink(); ?></a>
                    <?php } 
                        else 
                        {
                            echo "-";
                        }
                    ?>  
                </td>
            </tr>
            <tr>
                <td>Mati&egrave;re</td>
                <td><?php echo $subject->GetName(); ?></td>
            </tr>
            <tr>
                <td>Travail</td>
                <td><?php echo $work->GetName(); ?></td>
            </tr>
            <tr>
                <td>Date de rendu</td>
                <td><?php echo $dateTranslator->Html($delivery->GetDeadline(), PWHDateTranslator::DATE_AND_TIME); ?></td>
            </tr>
            <?php
                if(!$work->IsSimple() && $work->GetGroupMax() > 1)
                { ?>
            <tr>
                <td>Composition des groupes</td>
                <td><?php echo $dateTranslator->Html($delivery->GetGroupCompositionDeadline(), PWHDateTranslator::DATE_AND_TIME); ?></td>
            </tr>
            <?php } ?>
            <tr>
                <td>Membre minimum</td>
                <td><?php echo $work->GetGroupMin(); ?></td>
            </tr>
            <tr>
                <td>Membre maximum</td>
                <td><?php echo $work->GetGroupMax(); ?></td>
            </tr>
            <tr>
                <td>Charge de travail</td>
                <td>
                    <?php 
                        if($work->GetLevel() > 0)
                        {
                            echo $work->GetLevel();
                        }
                        else
                        {
                            echo "-";
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <td>Tol&eacute;rance</td>
                <td>
                    <?php
                        echo $work->GetExtraTime();
                        if($work->GetExtraTime() > 1)
                        {
                            echo " jours";
                        }
                        else
                        {
                            echo " jour";
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <td>Taille</td>
                <td>
                    <?php 
                        if($work->GetSize() > 0)
                        {
                            echo $work->GetSize() . " Mo";
                        }
                        else
                        {
                            echo "-";
                        }
                    ?>
                </td>
            </tr>
            <?php
                foreach($files as $name=>$format)
                { ?>
                <tr>
                    <td>Fichier [<?php echo $name; ?>]</td>
                    <td>Format <?php echo PWHMetaType::GetName($format); ?></td>
                </tr>          
          <?php } ?>
        </table>
   </div>
   <?php
        if($student->HasDeliverygroup($delivery->GetID()) && $student->GetDeliverygroup($delivery->GetID())->GetLastDelivery() != "")
        {  
            $deliverygroup = $student->GetDeliverygroup($delivery->GetID());
            $id = $deliverygroup->GetID();
        ?>
       <h4>T&eacute;l&eacute;chargement</h4>
       <div class="section">
            <a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>/downloads/index.php?type=deliverygroup&amp;delivery_id=<?php echo $delivery->GetID(); ?>&amp;id=<?php echo $id; ?>"><img src="img/package_go.png"/>T&eacute;l&eacute;charger le dernier rendu effectu&eacute;</a>
        </div>
        <?php 
        }
        if(($work->IsSimple()
                    || $work->GetGroupMax() == 1
                    || !$delivery->IsStillTimeForGroupComposition(date("Y-m-d H:i:s"))) && $delivery->IsStillTimeForDelivery(date("Y-m-d H:i:s")) && ($work->GetGroupMin() == 1 || ($student->HasDeliverygroup($delivery->GetID()) && (count($student->GetDeliverygroup($delivery->GetID())->GetStudents()) >= $work->GetGroupMin()
                                                                                                                                                                                                                                        || $student->GetDeliverygroup($delivery->GetID())->IsSuper()))))
        { 
            foreach($files as $name=>$format)
            { ?>
            <h4>Livraison du fichier <?php echo $name . " - format " . PWHMetaType::GetName($format); ?></h4>
            <div class="section">
                <form method="post" enctype="multipart/form-data">
                    <div class="input">
                        <label>Fichier:</label>
                        <input type="hidden" name="MAX_FILE_SIZE" value="20971520" />
                        <input type="file" name="<?php echo $name; ?>" name="delivery"/>
                        <input type="submit" value="Livrer !"/>
                    </div>
                </form>
            </div>
        <?php } ?>
    <?php } 
    } ?>
</fieldset>
