<?php
require_once "bootstrap.php";
includeHeader("Deliverable Status");
?>
    <h1>Statut des livraisons</h1>
    <div class="form">
      <fieldset>
	<legend>Identification du groupe</legend>
	<form action="#" method="post"  
              enctype="multipart/form-data" id="deposit_form" >
	  <ul>
            <li>Promotion : 
	      <select id="promotion" name="promotion" onchange="getCourses()">
		<option value="">---</option>
<?php
foreach(Promotion::getAvailablePromotions() as $k => $promo){
  echo "		<option value=\"".$k."\">".$promo."</option>\n";
}
?>
	      </select>
	    </li>
	    <li>Cours : 
	      <select disabled="disabled" id="course" name="course" 
		      onchange="getGroups()">
		<option value="">---</option>
              </select>
	    </li>
	    <li>Groupe : 
	      <select disabled="disabled" id="group" name="group" 
		      onchange="getDeposits()">
		<option value="">---</option>
              </select>
	    </li>
	  </ul>
	</form>
      </fieldset>
    </div>
    <div id="result">
<?php
$promo = (array_key_exists("promo",$_GET) ? $_GET["promo"] : false);
$course = (array_key_exists("course",$_GET) ? $_GET["course"] : false);
$group = (array_key_exists("group",$_GET) ? $_GET["group"] : false);
if ($promo &&  $course && $group)
  echo Delivery::getDeliverablesStatusAsHTML($promo,$course,$group);
?>
    </div>
<?php includeFooter(); ?>
