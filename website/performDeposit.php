<?php

require_once "bootstrap.php";

$promo = (array_key_exists("promotion",$_POST) ? $_POST["promotion"] : false);
$course = (array_key_exists("course",$_POST) ? $_POST["course"] : false);
$group = (array_key_exists("group",$_POST) ? $_POST["group"] : false);
$deliverable = (array_key_exists("deliverable",$_POST) ? $_POST["deliverable"] : false);

if (! ($promo && $course && $group && $deliverable))
  die("Bad usage.");

//echo highlight_string(var_export($_POST));
$deposit = new Deposit($course,$deliverable,$promo,$group);
$proof = $deposit->perform();

includeHeader("Deposit Proof");
?>
  <h1> Delivery Proof </h1>
<?php
  if (count($proof["errors"]) != 0) {
    echo "<h2> Errors !! </h2>\n";
    echo "<p>The following errors occured: \n";
    echo "  <ul>\n";
    foreach($proof["errors"] as $e)
      echo "    <li>$e</li>\n";
    echo "</ul></p>";
  }
?>
<?php
  if (count($proof["warnings"]) != 0) {
    echo "<h2> Warning !! </h2>\n";
    echo "<p>The following warnings occured: \n";
    echo "  <ul>\n";
    foreach($proof["warnings"] as $w)
      echo "    <li>$w</li>\n";
    echo "</ul></p>";
  }
?>
  <h2>Result</h2>
<?php
  if (count($proof["errors"]) == 0)
    echo "<p> Your delivery is stored on the server</p>";
  else
    echo "<p class=\"error\">Due to errors, your delivery is refused.</p>";
?>
<?php includeFooter(); ?>