<?php
require_once "bootstrap.php";
redirectIfNotTeacher();


$promo = (array_key_exists("promotion",$_POST) ? $_POST["promotion"] : false);
$course = (array_key_exists("course",$_POST) ? $_POST["course"] : false);
$group = (array_key_exists("group",$_POST) ? $_POST["group"] : false);
$deliverable = (array_key_exists("deliverable",$_POST) ? $_POST["deliverable"] : false);

if ($promo && $course && $group && $deliverable) {
  $extractor = new Extractor($promo,$course,$group,$deliverable);
  echo $extractor->perform();
}


includeHeader("Extraction des dépots");
?>

<h1> Récupérer une livraison</h1>
    <div class="form">
      <fieldset>
	<legend>Choisissez votre livraison</legend>
	<form action="extractDeliveries.php" method="post"  
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
		      onchange="getDeliverable()">
		<option value="">---</option>
              </select>
	    </li>
	    <li>Livraison: 
	      <select disabled="disabled" id="deliverable" name="deliverable" 
		      onchange="getSubmitButton()">
		<option value="">---</option>
              </select>
	    </li>
	  </ul>
	  <div align="center">
	    <input id="send" type="submit" value="Récuperer !" 
		   disabled="disabled"/> 
	    <input type="reset" value="Ré-initialiser" 
		   onclick="resetForm(0);"/> 
	  </div>
	</form>
      </fieldset>
    </div>
  <div id="result" >
  <p> L'ensemble des rendus de la livraisons selectionée sera rassemblées dans une même archive <code>ZIP</code>.</p>
  </div>
<?php includeFooter(); ?>