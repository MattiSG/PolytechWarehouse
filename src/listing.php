<?php
require_once "inc/promotions.php";
require_once "inc/page.php";

if (array_key_exists("promo",$_GET))
  $promo = loadPromotion($_GET["promo"]);
else
  die("bad arguments, you bad guy");

if (array_key_exists("group",$_GET))
  $groups = array(0 => $_GET["group"]);
else
  $groups = getAvailableGroups($promo);

function displayGroup($promotion,$groupName)
{
  $result = "  <table>\n";
  $result .= "    <tr><th>#</th><th>Lastname</th><th>Firstname</th><th>login</th></tr>\n";
  foreach(getStudentsByGroupName($promotion,$groupName) as $stud){
    $result .= "    <tr><th>".$stud["uid"]."</th><td>".$stud->lastname."</td>";
    $result .= "<td>".$stud->firstname."</td><td>".$stud["login"]."</td></tr>";
    $result .= "\n";
  }
  $result .= "</table>";
  return $result;
}

includeHeader("Listing");
echo "  <h1>".$_GET["promo"] . " Listing</h1>\n";
foreach ($groups as $g){
  echo "  <h2>".$g."</h2>\n";
  echo displayGroup($promo,$g)."\n";
}
includeFooter();
?>
