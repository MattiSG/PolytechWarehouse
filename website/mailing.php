<?php
require_once "bootstrap.php";
includeHeader("Students Group Mailing");
?>
    <h1>Contacter un groupe d'étudiants</h1>
    <div class="form">
      <fieldset>
	<legend>Choix du groupe d'étudiants</legend>
	<form action="#" method="post"  
              enctype="multipart/form-data" id="deposit_form" >
	  <ul>
            <li>Promotion : 
	      <select id="promotion" name="promotion" onchange="getPromoGroups()">
		<option value="">---</option>
<?php
foreach(Promotion::getAvailablePromotions() as $k => $promo){
  echo "		<option value=\"".$k."\">".$promo."</option>\n";
}
?>
	      </select>
	    </li>
	    <li>Groupe : 
	      <select disabled="disabled" id="group" name="group" 
		      onchange="getGroupMail()">
		<option value="">---</option>
              </select>
	    </li>
	  </ul>
	</form>
      </fieldset>
    </div>
    <div id="result">
    </div>
<?php includeFooter(); ?>
