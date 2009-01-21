<?php
require_once "inc/page.php";
require_once "inc/promotions.php";

includeHeader("Deposit Form");
?>

  <h1>Deposit Form</h1>
  <fieldset>
    <legend>Delivery Information</legend>
    <form action="performDeposit.php" method="post"  
          enctype="multipart/form-data">
      <input type="hidden" name="MAX_FILE_SIZE" 
             value="<?php echo MAX_FILE_SIZE; ?>" />
      <ul>
        <li>Promotion : <select id="promotion" onchange="getCourses()">
          <option value="">---</option>
<?php
foreach(getAvailablePromotions() as $promo){
  echo "          <option value=\"".$promo."\">".$promo."</option>\n";
}
?>
       </li>
       <li>Course : <select disabled="true" id="course" onchange="getGroups()">
                    </select>
       </li>
  
      </ul>
    </form>
  </fieldset>


<?php 
includeFooter();
?>