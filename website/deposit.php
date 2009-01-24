<?php
require_once "bootstrap.php";
includeHeader("Deposit Form");
?>
    <h1>Aire de dépôt</h1>
    <div class="form">
      <fieldset>
	<legend>Choisissez votre livraison</legend>
	<form action="performDeposit.php" method="post"  
              enctype="multipart/form-data" id="deposit_form" >
	  <input type="hidden" name="MAX_FILE_SIZE" 
		 value="<?php echo MAX_FILE_SIZE; ?>" />
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
		      onchange="getProducts()">
		<option value="">---</option>
              </select>
	    </li>
	    <li> Dépot : 
              <ul id="products"> <li style="display: none;"> </li></ul>
	    </li>
	  </ul>
	  <div align="center">
	    <input id="send" type="submit" value="Déposer !" 
		   disabled="disabled"/> 
	    <input type="reset" value="Ré-initialiser" 
		   onclick="resetForm(0);"/> 
	  </div>
	</form>
      </fieldset>
    </div>
<?php includeFooter(); ?>
