<?php     
    previousPage('admin_database_management');
    
    if(isset($_FILES['uploadedFile']) && $_FILES['uploadedFile']['error'] == 0)
    {
        $fileInfo = pathinfo($_FILES['uploadedFile']['name']);
        $fileExtension = $fileInfo['extension'];
        $validExtensions = array('ens', 'ENS');
        if (in_array($fileExtension, $validExtensions))
        {
            $errors = array();
            $errors[0] = array();
            $errors[1] = array();
            // ajouter .* pour nom prénom
            $accents = "éèêëàâäïîûùüöôç'";
            if($file = @file($_FILES['uploadedFile']['tmp_name']))
            {
                $i = 0;
                foreach($file as $line)
                {
                    $i++;
                    $line = rtrim($line);
                    if(preg_match("#[a-zA-Z0-9]+;[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4};[-" . $accents . "a-zA-Z0-9 ]+;[-" . $accents . "a-zA-Z0-9 ]+;#", $line))
                    {
                        try 
                        {
                            $info = explode(';', $line);
                            $teacher = new PWHTeacher();
                            $teacher->SetLogin($info[0]);
                            $teacher->SetFirstName($info[3]);
                            $teacher->SetLastName($info[2]);
                            $teacher->SetEmail($info[1]);
                            $teacher->Create(true);
                        }
                        catch(PWHQueryException $ex)
                        {
                            array_push($errors[0], $info[3] . " " . $info[2]);
                        }
                        catch(PWHIOException $ex)
                        {
                            errorReport($ex->getMessage());
                            break;
                        }
                    }
                    else
                    {
                        array_push($errors[1], "L" . $i . " : " . $line);
                    }                  
                }
                
                if(count($errors[0]) > 0)
                {
                    $strbuf = "";
                    foreach($errors[0] as $error)
                    {
                        $strbuf .= $error . ", ";
                    }
                    $strbuf = substr($strbuf, 0, strlen($strbuf) - 2);
                    errorReport("Les enseignants suivants n'ont pas &eacute;t&eacute; cr&eacute;&eacute;s car ils existent d&eacute;j&agrave;: " . $strbuf . ".");
                }
                
                if(count($errors[1]) > 0)
                {
                    $strbuf = "";
                    foreach($errors[1] as $error)
                    {
                        $strbuf .= "<p>" . $error . "</p>";
                    }
                    $strbuf = substr($strbuf, 0, strlen($strbuf) - 2);
                    errorReport("Erreur de lecture aux lignes suivantes du fichier .ENS: " . $strbuf . ".");
                }
                
                successReport("Le fichier " . $_FILES['uploadedFile']['name'] . " a &eacute;t&eacute; charg&eacute;.");
            }
            else
            {
                errorReport("Echec de l'ouverture du fichier .ENS.");
            }
        }
        else
        {
            errorReport($fileExtension . " n'est pas une extension valide.");
        }      
    }

?>

<fieldset>
    <legend>enseignants</legend>
    <?php
        $help = new PWHHelp();
        echo $help->Html("#");
        
        displayErrorReport();
        displaySuccessReport();
    ?>
    <h4>Chargement d'enseignants</h4>
    <div class="section">
        <form method="post" enctype="multipart/form-data">
            <div class="input">
                <label>Fichier .ENS:</label>
                <input type="file" name="uploadedFile"/>	    
                <input type="submit" value="Charger !"/>         
            </div>
       </form>
   </div>
</fieldset>
