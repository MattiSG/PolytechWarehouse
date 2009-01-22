<?php
require_once "bootstrap.php";
includeHeader("Deposit Form");
?>

  <h1>Deposit Form</h1>
  <fieldset>
    <legend>Delivery Information</legend>
    <form action="performDeposit.php" method="post"  
          enctype="multipart/form-data" id="deposit_form" onload="resetForm(0)">
      <input type="hidden" name="MAX_FILE_SIZE" 
             value="<?php echo MAX_FILE_SIZE; ?>" />
      <ul>
        <li>Promotion: <select id="promotion" name="promotion" onchange="getCourses()">
          <option value="">---</option>
<?php
foreach(Promotion::getAvailablePromotions() as $promo){
  echo "          <option value=\"".$promo."\">".$promo."</option>\n";
}
?>
       </li>
       <li>Course: <select disabled="true" id="course" name="course" onchange="getGroups()">
          <option value="">---</option>
                    </select>
       </li>
       <li>Group: <select disabled="true" id="group" name="group" onchange="getDeliverable()">
          <option value="">---</option>
                    </select>
       </li>
       <li>Deliverable: <select disabled="true" id="deliverable" name="deliverable" onchange="getProducts()">
          <option value="">---</option>
                    </select>
      </li>
      <li> Delivery : 
        <ul id="products">
        </ul>
      </li>
    </ul>
  <div align="center">
  <input id="send" type="submit" value="Upload My Delivery !" disabled="true"/> 
  <input type="reset" onclick="resetForm(0);"/> 
  </div>
  </form>
  </fieldset>


<?php 
includeFooter();
?>