<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page create_subject_groups");
    
    previousPage('teacher_create_subject_teachers');
    addPreviousPageParameter('index', 'A');
    $failed = false;
    
    try
    {
        $groups = PWHEntity::ListAll('PWHGroup');
    }
    catch(Exception $ex)
    {
        errorReport($ex->getMessage());
        $failed = false;
    }
    
    if(!$failed)
    {
        // [FORM] Creates a new subject with the specified name, teachers and groups
        if(isset($_GET['action']) && $_GET['action'] == 'add_groups')
        {        
            $insert = array();
            $check = array();
            foreach($groups as $group)
            {
                if(isset($_POST[$group->GetID()]))
                {
                    array_push($insert, $group->GetID());
                    array_push($check, $group);
                }
            }
            
            $wrong = false;
            foreach($check as $group)
            {
                $parents = $group->GetParents();
                foreach($parents as $parent)
                {
                    if(in_array($parent->GetID(), $insert))
                    {
                        $wrong = true;
                    }
                }
            }
            
            if(!$wrong)
            {
                try
                {
                    $subject = new PWHSubject();
                    $subject->SetName($_SESSION['subject_name']);
                    $subject->AddTeachers($_SESSION['teachers']);
                    $subject->AddGroups($insert);
                    $subject->Create(true);
                    $subject->CreateDirectory();
                    
                    $teachers = $subject->GetTeachers();
                    PWHEvent::Notify($teachers, TEACHER_TYPE, "La mati&egrave;re " . $subject->GetName() . " a &eacute;t&eacute; cr&eacute;&eacute;e");
                    PWHLog::Write(PWHLog::WARNING, $_SESSION['login'], "Cr&eacute;ation mati&egrave;re " . $subject->GetName());
                    
                    // Destroys session variables
                    unset($_SESSION['subject_name']);
                    unset($_SESSION['promo']);
                    unset($_SESSION['teachers']);
                    
                    redirect("index.php?page=teacher_list_subjects&amp;see=less");
                }
                catch(Exception $ex)
                {
                    errorReport($ex->getMessage());
                    PWHLog::Write(PWHLog::ERROR, $_SESSION['login'], "Echec cr&eacute;ation mati&egrave;");
                }   
            }
            else
            {
                errorReport("Vous ne pouvez pas choisir en m&ecirc;me temps un groupe et un de ses groupes parents.");
            }
        }
        
        // Creates a new memo for the user     
        $memo = new PWHSubjectCreationMemo();
        $memo->SetName($_SESSION['subject_name']);
        $memo->SetTeachers($_SESSION['teachers']);
    }
    
    if($failed)
    {
        errorReport("Impossible d'afficher la page demand&eacute;e.");
    }
?>

<fieldset>
	<legend>groupes - etape 3/3</legend>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/create_subject_groups.html', 800, 550);");
        
	    displayErrorReport();
	    displaySuccessReport();
	    echo $memo->Html();
	    
	    if(!$failed)
	    {
	?>
	<h4>Affectation de groupes &agrave la mati&egrave;re</h4>
	<div class="section">
	    <form id="group_tree" method="post" action="index.php?page=teacher_create_subject_groups&amp;action=add_groups">
	        <?php
	            $groupTree = new PWHGroupTree();
                $groupTree->Build($_SESSION['promo']);
                echo $groupTree->Html(PWHGroupTree::FORM_TREE, PWHGroupTree::TEACHER);
            ?>
	        <input class="next_form" disabled="disabled" type="submit" id="create" name="create" value="Cr&eacute;er !"/></td>
            </table>
	    </form>	 
	</div>
	<?php } ?>
</fieldset>
<script type="text/javascript" charset="iso-8859-1">
<!--
    function MakeIndex(link, letter)
    {
        var form = document.getElementById("person_index");
        var boxs = form.elements;
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
//-->
</script>
<script type="text/javascript">
<!--
    function CheckForm()
    {
        var form = document.getElementById("group_tree");
        var boxs = form.elements;
        var inputSubmit = document.getElementById("create");
        var empty = true;
        for(var i=0; i<boxs.length && empty; i++)
        {
            if(boxs[i].checked)
            {
                inputSubmit.disabled = false;
                empty = false;
            }
        }
        if(empty)
        {
            inputSubmit.disabled = true;
        }
    }
    
    var form = document.getElementById("group_tree");
    var boxs = form.elements;
    var inputSubmit = document.getElementById("create");
    for(var i=0; i<boxs.length; i++)
    {
        boxs[i].onclick = CheckForm;
    }
//-->
</script>
