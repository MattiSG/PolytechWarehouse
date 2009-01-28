<?php
require_once "bootstrap.php";
includeHeader("Description d'une matière");
?>
    <h1>Description d'une matière</h1>
    <div class="form">
      <fieldset>
	<legend>Identification de la matièree</legend>
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
		      onchange="getCourseDescription()">
		<option value="">---</option>
              </select>
	    </li>
	  </ul>
	</form>
      </fieldset>
    </div>
    <div id="result">
<?php


$course = (array_key_exists("course",$_GET) ? $_GET["course"] : false);
if ($course )
  echo Course::displayAsHtml($course);
?>
    </div>
<?php includeFooter(); ?>
