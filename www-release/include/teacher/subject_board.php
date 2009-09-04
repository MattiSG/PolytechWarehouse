<?php
    $GLOBALS['PWH_PATH'] = "../../";
	require_once($GLOBALS['PWH_PATH'] . 'libpwh/PWHHeader.php');
	require_once($GLOBALS['PWH_PATH'] . 'include/util.php');
	
    // Retrieves the concerned subject and its works
    if(isset($_GET['subject_id']))
    {
        try
        {
            $subject = new PWHSubject();
            $subject->Read($_GET['subject_id']);
            $groups = $subject->GetGroups();
            $works = $subject->GetWorks();
        }
        catch(Exception $ex)
        {
            errorReport($ex->getMessage());
        }
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
	    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
		<link rel="stylesheet" type="text/css" href="../../css/content.css"/>
        <title>Polytech'WareHouse</title>
	</head>
	<body>
        <div id="content_alt">
            <fieldset>
                <legend><?php echo $subject->GetName(); ?></legend>
                <h4>Tableau de bord</h4>
                <div class="section">
                    <table class="colored_table underlined_table">
                        <tr>
                            <th></th>
                            <?php
                                foreach($works as $work)
                                { ?>
                            <th><?php echo $work->GetName(); ?></th>
                            <?php } ?>
                        </tr>
                        <?php
                            $students = array();
                            foreach($groups as $group)
                            {
                                $students = array_merge($students, $group->GetStudents());
                            }
                            usort($students, "person_comparator");
                            if(count($students) == 0)
                            { ?>
                            <tr><td colspan="<?php echo count($works) + 1; ?>">Il n'y a aucun &eacute;tudiant disponible</td></tr>
                            <?php }
                            else
                            { 
                                foreach($students as $student)
                                {
                                ?>
                                    <tr>
                                        <td><?php echo $student->GetLastName() . " " . $student->GetFirstName(); ?></td>
                                        <?php
                                            foreach($works as $work)
                                            {
                                                $delivery = $student->GetDelivery($work->GetID());
                                                $class = ' class="undelivered_line"';
                                                if($student->HasDeliverygroup($delivery->GetID()))
                                                {
                                                    $deliverygroup = $student->GetDeliverygroup($delivery->GetID());
                                                    if($deliverygroup->GetLastDelivery() != "")
                                                    {
                                                        $class = ' class="delivered_line"';
                                                    }
                                                }
                                                ?>
                                                <td<?php echo $class; ?>></td>          
                                      <?php } ?>
                                    </tr>
                            <?php } 
                            } ?>
                    </table>
                </div>
            </fieldset>
        </div>
    </body>
</html>
