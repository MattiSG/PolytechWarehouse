<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page create_work_files");
    
    previousPage('teacher_create_work_name_constraints');
    addPreviousPageParameter('subject_id', $_GET['subject_id']);
    $failed = false;
    
    // Retrieves the concerned subject
    if(isset($_GET['subject_id']) && PWHEntity::Valid("PWHSubject", $_GET['subject_id']))
    {     
        try
        {
            $subject = new PWHSubject(null);
            $subject->Read($_GET['subject_id']);
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
    
    // Clean sessions variables
    if(isset($_SESSION['files']))
    {
        unset($_SESSION['files']);
    }
    if(isset($_SESSION['number_files']))
    {
        unset($_SESSION['number_files']);
    }
    
    if(!$failed)
    {
        // Sets the number of files for the work
        $numberFiles = 1;
        if(isset($_GET['number_files']))
        {
            if(!preg_match("#^[0-9]+$#", $_GET['number_files']))
            {
                errorReport("Vous devez sp&eacute;cifier des nombres entiers pour le nombre de fichiers");
            }
            else
            {
                $numberFiles = $_GET['number_files'];
            }
        }
        
        
        
        if(isset($_GET['action']))
        {
            if($_GET['action'] == 'alert_empty')
            {
                errorReport("Vous devez remplir tous les champs obligatoires");
            }
        }
        
        
        // [FORM] Save the name of the work into the session
        if(isset($_POST['workName']) && isset($_POST['extraTime']) && isset($_POST['size'])
            && isset($_POST['groupMin']) && isset($_POST['groupMax']) && isset($_POST['link']) && isset($_POST['level']) && isset($_POST['simple']))
        {
            if($_POST['workName'] == "" || $_POST['groupMin'] == "" || $_POST['groupMax'] == "")
            {
                redirect("index.php?page=teacher_create_work_name_constraints&amp;subject_id=" . $subject->GetID() . "&amp;action=alert_empty");
            }
            else if(!preg_match("#^[0-9]+$#", $_POST['groupMin']) || !preg_match("#^[0-9]+$#", $_POST['groupMax']))
            {
                redirect("index.php?page=teacher_create_work_name_constraints&amp;subject_id=" . $subject->GetID() . "&amp;action=alert_type_req");
            }
            else if($_POST['groupMin'] > $_POST['groupMax'])
            {
                redirect("index.php?page=teacher_create_work_name_constraints&amp;subject_id=" . $subject->GetID() . "&amp;action=alert_err");
            }
            else if(($_POST['extraTime'] != "" && !preg_match("#^[0-9]+$#", $_POST['extraTime']))
                     || ($_POST['size'] != "" && !preg_match("#^[0-9]+$#", $_POST['size']))
                     || ($_POST['level'] != "" && $_POST['level'] != "-" && !preg_match("#^[0-9]+$#", $_POST['level'])))
            {
                redirect("index.php?page=teacher_create_work_name_constraints&amp;subject_id=" . $subject->GetID() . "&amp;action=alert_type_nreq");  
            }
            else
            {
                $_SESSION['work_name'] = stripslashes($_POST['workName']);
                $_SESSION['group_min'] = $_POST['groupMin'];
                $_SESSION['group_max'] = $_POST['groupMax'];
                $_SESSION['link'] = $_POST['link'];
                $_SESSION['level'] = $_POST['level'];
                $_SESSION['extra_time'] = $_POST['extraTime'];
                $_SESSION['size'] = $_POST['size'];
                
                if($_SESSION['extra_time'] == "")
                {
                    $_SESSION['extra_time'] = 0;
                } 
                
                if($_SESSION['size'] == "")
                {
                    $_SESSION['size'] = 0;
                }
                
                if($_SESSION['level'] == "")
                {
                    $_SESSION['level'] = 0;
                }
                
                if($_POST['simple'] == "true")
                {
                    $_SESSION['simple'] = true;
                }
                else
                {
                    $_SESSION['simple'] = false;
                }
            }
        }
        
        // Saves the specified name when go back to the previous page
        addPreviousPageParameter('work_name', $_SESSION['work_name']);
        addPreviousPageParameter('extra_time', $_SESSION['extra_time']);
        addPreviousPageParameter('size', $_SESSION['size']);
        addPreviousPageParameter('group_min', $_SESSION['group_min']);
        addPreviousPageParameter('group_max', $_SESSION['group_max']);
        addPreviousPageParameter('link', $_SESSION['link']);
        addPreviousPageParameter('level', $_SESSION['level']);
    }
    
    if($failed)
    {
        errorReport("Impossible d'afficher la page demand&eacute;e.");
    }
?>

<fieldset>
	<legend>fichiers - etape 2/3</legend>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/create_work_files.html', 800, 600);");
        
        displayErrorReport();
        
        if(!$failed)
        {
    ?>
	<form method="post" action="index.php?page=teacher_create_work_assocs&amp;subject_id=<?php echo $_GET['subject_id']; ?>">
        <?php
	        for($i=1; $i<=$numberFiles; $i++)
	        { ?>
	            <h4>Configuration du fichier <?php echo $i; ?></h4>
	            <div class="section">
	                <div class="input">
	                    <label>Nom (*):</label>
	                    <input id="file_name<?php echo $i; ?>" type="text" name="fileName<?php echo $i; ?>"/>
	                    <select name="fileFormat<?php echo $i; ?>">
                        <?php
                            $metatypes = PWHMetaType::GetMetaTypes();
                            foreach($metatypes as $metatype)
                            { ?>
                                <option <?php if(preg_match("#^[0-9]+$#", $metatype['id'])) { echo 'class="metatype" '; } ?>value="<?php echo $metatype['id']; ?>"><?php if(preg_match("#^[0-9]+-[0-9]+$#", $metatype['id'])) { echo "&nbsp;&nbsp;&nbsp;&nbsp;"; } echo PWHMetaType::GetName($metatype['id']); ?></option>          
                      <?php } ?>
                        </select>
                    </div>
                </div>
	     <?php } ?>
	     <div class="section">
	         <div class="input">
                <a href="index.php?page=teacher_create_work_files&amp;subject_id=<?php echo $_GET['subject_id']; ?>&amp;number_files=<?php echo $numberFiles+1; ?>"><img src="img/plus.png"/>Ajouter un fichier</a>
                <?php if($numberFiles > 1)
                { ?>
               <a href="index.php?page=teacher_create_work_files&amp;subject_id=<?php echo $_GET['subject_id']; ?>&amp;number_files=<?php echo $numberFiles-1; ?>"><img src="img/minus.png"/>Supprimer un fichier</a> 
                <?php } ?>
            <div>
         </div>
	     <div class="section">
	        <input type="hidden" name="numberFiles" value="<?php echo $numberFiles; ?>"/>
            <input class="next_form" type="submit" id="next" value="Suivant &raquo;"/>
         </div>
    </form>
    <?php } ?>
</fieldset>

<script type="text/javascript">
<!--
    var numberFiles = <?php echo $numberFiles; ?>;
    var inputNames = Array();
    for(var i=1; i<=numberFiles; i++)
    {
        inputNames[i] = document.getElementById("file_name" + i);
    }
    
    var inputSubmit = document.getElementById("next");
    var empty = false;
    for(var i=1; i<=numberFiles; i++)
    {
        if(inputNames[i].value == "")
        {
            empty = true;
        }
    }
    inputSubmit.disabled = empty;
    
    function CheckForm()
    {
        var inputSubmit = document.getElementById("next");
        var empty = false;
        for(var i=1; i<=numberFiles; i++)
        {
            if(inputNames[i].value == "")
            {
                empty = true;
            }
        }
        inputSubmit.disabled = empty;
    }
    
    for(var i=1; i<=numberFiles; i++)
    {
        inputNames[i].onkeyup = CheckForm;
    }
//-->
</script>
