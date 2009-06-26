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

includeHeader("Preuve de dépôt");
?>
  <h1> Preuve de dépôt </h1>
<?php
  if (count($proof["errors"]) != 0) {
    echo "<h2> Erreurs !! </h2>\n";
    echo "<p>Les erreurs suivantes ont empéchées le dépot de votre livraison: \n";
    echo "  <ul>\n";
    foreach($proof["errors"] as $e)
      echo "    <li>$e</li>\n";
    echo "</ul></p>";
  }
?>
<?php
  if (count($proof["warnings"]) != 0) {
    echo "<h2> Attention !! </h2>\n";
    echo "<p>Le système détecte les incohérences suivantes : \n";
    echo "  <ul>\n";
    foreach($proof["warnings"] as $w)
      echo "    <li>$w</li>\n";
    echo "</ul></p>";
  }
?>
  <h2>Result</h2>
<?php
  if (count($proof["errors"]) == 0)
    echo "<p> Votre dépôt a bien été pris en compte par la plate forme.</p>";
  else
    echo "<p class=\"error\">En raison des erreurs précédentes, votre dépot est refusé.</p>";
?>
<?php includeFooter(); ?>