<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page load_group");
    
    previousPage("teacher_list_groups");
    
    if(isset($_POST['groupName']) && isset($_FILES['uploadedFile']))
    {
        if($_FILES['uploadedFile']['error'] == 0)
        {
            $fileInfo = pathinfo($_FILES['uploadedFile']['name']);
            $fileExtension = $fileInfo['extension'];
            $validExtensions = array('promo', 'PROMO');
            if (in_array($fileExtension, $validExtensions))
            {
                try
                {
                    $group = new PWHGroup();
                    $group->SetName($_POST['groupName']);
                    $errors = $group->ReadFromFile($_FILES['uploadedFile']['tmp_name']);
                    $group->Create(true);
                    
                    if(count($errors[0]) > 0)
                    {
                        $strbuf = "";
                        foreach($errors[0] as $error)
                        {
                            $strbuf .= $error . ", ";
                        }
                        $strbuf = substr($strbuf, 0, strlen($strbuf) - 2);
                        errorReport("Les &eacute;tudiants suivants n'ont pas &eacute;t&eacute; cr&eacute;&eacute;s ni ajout&eacute;s au groupe car ils existent d&eacute;j&agrave;: " . $strbuf . ".");
                        PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Fichier .promo: &eacute;tudiants d&eacute;j&agrave; pr&eacute;sents");
                    }
                    
                    if(count($errors[1]) > 0)
                    {
                        $strbuf = "";
                        foreach($errors[1] as $error)
                        {
                            $strbuf .= "<p>" . $error . "</p>";
                        }
                        $strbuf = substr($strbuf, 0, strlen($strbuf) - 2);
                        errorReport("Erreur de lecture aux lignes suivantes du fichier .PROMO: " . $strbuf);
                        PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Fichier .promo: erreurs de lecture");
                    }
                    
                    $teachers = PWHEntity::ListAll("PWHTeacher");
                    PWHEvent::Notify($teachers, TEACHER_TYPE, "La promotion " . $group->GetName() . " a &eacute;t&eacute; charg&eacute;e dans le d&eacute;p&ocirc;t");
                    PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Chargement promotion " . $group->GetName());
                    successReport("Le groupe " . $group->GetName() . " a &eacute;t&eacute; cr&eacute;&eacute;.");
                    
                }
                catch(Exception $ex)
                {
                    errorReport($ex->getMessage());
                    PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec chargement promotion");
                }
            }
            else
            {
                errorReport($fileExtension . " n'est pas une extension valide.");
                PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Echec de l'envoi du fichier .promo: format " . $fileExtension . " invalide");
            }      
        }
        else
        {
            switch($_FILES['uploadedFile']['error'])
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
?>

<section>
    <h2>promotion</h2>
    <?php
        $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/load_group.html', 800, 600);");
        
        displayErrorReport();
        displaySuccessReport();
    ?>
    <form method="post" enctype="multipart/form-data">
        <h4>Chargement de promotion</h4>
        <div class="section">
            <div class="input">
                <label for="nom">Nom de la promotion:</label><input type="text" id="group_name" name="groupName" size="20"/>
		     </div>
            <div class="input">
                <label for="nom">Fichier .PROMO:</label><input type="file" name="uploadedFile"/><input type="submit" id="load" value="Charger !"/>         
            </div>
		</div>
    </form>
</section>

<script type="text/javascript">
<!--
    function CheckForm()
    {
        var inputName = document.getElementById("group_name");
        var inputSubmit = document.getElementById("load");
        if(inputName.value == "")
        {
            inputSubmit.disabled = true;
        }
        else
        {
            inputSubmit.disabled = false;
        }
    }
    
    var inputName = document.getElementById("group_name");
    var inputSubmit = document.getElementById("load");
    inputName.onkeyup = CheckForm;
    if(inputName.value == "")
    {
        inputSubmit.disabled = true;
    }
//-->
</script>
