<?php
  /** Platform Bootstrap
   * @author Sebastian mosser <mosser@polytech.unice.fr>
   * @copyright Polytech'Sophia IaI Team
   * @licence LGPL
   */
session_start();
error_reporting(E_ALL | E_STRICT);

date_default_timezone_set('Europe/Berlin');


// Requiring functions library
require_once "inc/constants.php";
require_once "inc/page.php";

// Very basic class loader
function __autoload($className)
{
  require_once("inc/".$className.".php");
}




// DEBUG PUPROSE
$_SESSION["login"] = "tafani";
$_SESSION["teacher"] = false;
$_SESSION["lastname"] = "TAFANI";
$_SESSION["firstname"] = "Gaetan";
$_SESSION["uid"] = "51";


?>