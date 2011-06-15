<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page create_subject_teachers");
    previousPage('teacher_create_subject_name');
    $failed = false;
        
    // Retrieves the list of teachers sorted, corresponding to the value of the index
    if(isset($_GET['index']) && preg_match("#^[*A-Z]$#", $_GET['index']))
    {
        try
        {
            $teachers = PWHEntity::ListAll('PWHTeacher');     
            $allTeachers = $teachers;
            usort($teachers, "person_comparator");
            $index = new PWHIndex();
            $teachers = $index->FilterPersons($teachers, $_GET['index']);
            
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
        // [FORM] Save the name of the subject into the session
        if(isset($_POST['subjectName']) && isset($_POST['promo']))
        {
            $_SESSION['subject_name'] = stripslashes($_POST['subjectName']);
            $_SESSION['promo'] = $_POST['promo'];
        }
        // Saves the specified name when go back to the previous page
        addPreviousPageParameter('subject_name', $_SESSION['subject_name']);   
        // Saves the specified promo when go back to the previous page
        addPreviousPageParameter('promo', $_SESSION['promo']);
    
        if(!isset($_SESSION['teachers']))
        {
            $_SESSION['teachers'] = array();
            array_push($_SESSION['teachers'], $_SESSION['id']);
            $table = new PWHPersonTable();
            $teachers = $table->FilterPersons($teachers, $_SESSION['teachers']);
            $allTeachers = $table->FilterPersons($allTeachers, $_SESSION['teachers']);
        }
        else
        {
            $table = new PWHPersonTable();
            $teachers = $table->FilterPersons($teachers, $_SESSION['teachers']);
            $allTeachers = $table->FilterPersons($allTeachers, $_SESSION['teachers']);
        }
            
        // [FORM] Save the teachers who have been added to the subject
        if(isset($_GET['action']) && $_GET['action'] == 'add_teachers')
        {
            $insert = array();
            foreach($teachers as $teacher)
            {
                if(isset($_POST[$teacher->GetID()]))
                {
                    array_push($insert, (int)$teacher->GetID());
                }
            }
            $_SESSION['teachers'] = array_merge($_SESSION['teachers'], $insert);
            $table = new PWHPersonTable();
            $teachers = $table->FilterPersons($teachers, $_SESSION['teachers']);
            $allTeachers = $table->FilterPersons($allTeachers, $_SESSION['teachers']);
         }
         
        // Creates a new memo for the user     
        $memo = new PWHSubjectCreationMemo();
        $memo->SetName($_SESSION['subject_name']);
        if(isset($_SESSION['teachers']))
        {
            $memo->SetTeachers($_SESSION['teachers']);
        }
    }
    
    if($failed)
    {
        errorReport("Impossible d'afficher la page demand&eacute;e.");
    }
?>
<section>
	<h2>enseignants responsables - etape 2/3</h2>
	<?php 
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/create_subject_teachers.html', 800, 600);");
        displayErrorReport();
        displaySuccessReport();
        
	    if(!$failed)
	    {
	        echo $memo->Html();
	?>
	<h4>Liste des enseignants disponibles</h4>
	<div class="section">
	    <?php 
	        $index = new PWHIndex();
	        echo $index->Html("index.php?page=teacher_create_subject_teachers", $_GET['index'], true, $allTeachers);
	    ?>
		<form id="person_index" method="post" action="index.php?page=teacher_create_subject_teachers&amp;index=<?php echo $_GET['index']; ?>&amp;action=add_teachers">
	        <?php
	            $table = new PWHPersonTable();
	            echo $table->Html($teachers, "Ajouter +");
	        ?>
	    </form>
   	    <form method="post" action="index.php?page=teacher_create_subject_groups">
            <input onclick="javascript:UserConfirmSubmit();" class="next_form" type="submit" value="Suivant &raquo;"/>
        </form>
	</div>
	<?php } ?>
</section>
<script type="text/javascript" charset="iso-8859-1">
<!--
    function MakeIndex(link, letter)
    {
        var form = document.getElementById("person_index");
        var boxs = form.elements;
        var quit = true;
        for(var i=0; i<boxs.length; i++)
        {
            if(boxs[i].checked)
            {
                if(confirm("Vous n'avez pas valid\351 le formulaire en cliquant sur le bouton \"Ajouter +\" ? Voulez-vous le valider avant de quitter cette page ?"))
                {
                    form.submit();
                    window.location = link + "&index=" + letter;
                    break;
                }
                else
                {
                    window.location = link + "&index=" + letter;
                    break;
                }
           }
        }

        window.location = link + "&index=" + letter;
    }
    
    function UserConfirmSubmit()
    {
        var form = document.getElementById("person_index");
        var boxs = form.elements;
        for(var i=0; i<boxs.length; i++)
        {
            if(boxs[i].checked)
            {
                if(confirm("Vous n'avez pas valid\351 le formulaire en cliquant sur le bouton \"Ajouter +\" ? Voulez-vous le valider avant de passer \340 l'\351tape suivante ?"))
                {
                    form.submit();
                    break;
                }
                else
                {
                    break;
                }
           }
        }
    }
//-->
</script>
<script type="text/javascript" charset="iso-8859-1">
<!--
    function CheckForm(index)
    {
        var form = document.getElementById("person_index");
        var input = document.getElementById("next");
        var boxs = form.elements;
              
        var disabled = true;
        for(var i=0; i<boxs.length; i++)
        {
             if(boxs[i].checked)
             {
                disabled = false;
             }
        }
        input.disabled = disabled;
        
        boxs[index].checked = !boxs[index].checked;
    }
    
    function CheckBox(index)
    {
        var box = document.getElementById("cb_" + index);
        box.checked = !box.checked;
        
        var form = document.getElementById("person_index");
        var input = document.getElementById("next");
        var boxs = form.elements;
              
        var disabled = true;
        for(var i=0; i<boxs.length; i++)
        {
             if(boxs[i].checked)
             {
                disabled = false;
             }
        }
        input.disabled = disabled;
        
        var lines = form.getElementsByTagName("tr");
        index++;
        if(box.checked)
        {
            lines[index].className = "selected";
        }
        else
        {
            lines[index].className = "";
            if(index%2 == 0)
            {
                lines[index].className = "alt";
            }
        }
    } 
    
    var form = document.getElementById("person_index");
    var boxs = form.elements;
    var input = document.getElementById("next");
        
    var disabled = true;
    for(var i=0; i<boxs.length; i++)
    {
         if(boxs[i].checked)
         {
            disabled = false;
         }
    }
    input.disabled = disabled; 
//-->
</script>
