<?php
    PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Acc&egrave;s page create_group_name");
    
    previousPage('teacher_list_groups');;
    
    // Clean session variables that will be used during the creation of the subject
    if(isset($_SESSION['group_name']))
    {
        unset($_SESSION['group_name']);
    }
    if(isset($_SESSION['students']))
    {
        unset($_SESSION['students']);
    }
    if(isset($_SESSION['parent_group']))
    {
        unset($_SESSION['parent_group']);
    }       
        
    if(isset($_GET['group_name']))
    {
        $groupName = stripslashes($_GET['group_name']);
    }
    else
    {
        $groupName = "";
    }
    
    if(isset($_GET['parent_group']))
    {
        $parentGroup = $_GET['parent_group'];
    }
    else
    {
        $parentGroup = -1;
    }
    
    
    // Retrieves list of all groups
    try
    {
        $promos = PWHGroup::GetPromotions();     
    }
    catch(Exception $ex)
    {
        errorReport($ex->getMessage());
        $promos = array();
    }
    
    if(count($promos) == 0)
    {
        errorReport("Il n'y aucun groupe disponible. Vous ne pouvez pas cr&eacute;er de sous groupes.");
    }
    
    if(isset($_GET['action']))
    {
        if($_GET['action'] == 'alert_name_used')
        {
            errorReport("Un groupe portant ce nom a d&eacute;j&agrave; &eacute;t&eacute; cr&eacute;&eacute; dans ce groupe parent");
        }
   }
?>

<fieldset>
	<legend>nom - etape 1/2</legend>
	<?php
	    $help = new PWHHelp();
        echo $help->Html("javascript:popup('include/teacher/help/create_group_name.html', 800, 550);");
        
        displayErrorReport();
    
        if(count($promos) > 0)
        {
    ?>
	<div class="section">
		<form method="post" action="index.php?page=teacher_create_group_students&amp;index=A">
            <div class="input">
                <label for="groupName">Nom du groupe:</label><input id="group_name" type="text" name="groupName" size="20" value="<?php echo $groupName; ?>"/>
	        </div>
	        <div class="input">
	            <label for="parentGroup">Groupe parent:</label>
		        <select name="parentGroup" id="parentGroup">
		            <?php 
                        foreach($promos as $promo)
                        { ?>  
		                <option class="promo" value="<?php echo $promo->GetID();?>" <?php if($promo->GetID() == $parentGroup){ echo 'selected="selected"'; } ?>><?php echo $promo->GetName(); ?></option>
		          <?php 
		                $family = PWHGroup::GetFamily($promo->GetID());
		                foreach($family as $group)
		                { ?>
		                <option value="<?php echo $group->GetID();?>" <?php if($group->GetID() == $parentGroup){ echo 'selected="selected"'; } ?>><?php echo "&nbsp;&nbsp;" . $group->GetName(); ?></option>   
		                <?php }
		                } ?>
		        </select>
		    </div>
		    <input class="next_form" type="submit" id="next" value="Suivant &raquo;"/>
	   </form>
	</div>
	<?php } ?>
</fieldset>

<script type="text/javascript">
<!--
    function CheckForm()
    {
        var inputName = document.getElementById("group_name");
        var inputSubmit = document.getElementById("next");
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
    var inputSubmit = document.getElementById("next");
    inputName.onkeyup = CheckForm;
    if(inputName.value == "")
    {
        inputSubmit.disabled = true;
    }
//-->
</script>
