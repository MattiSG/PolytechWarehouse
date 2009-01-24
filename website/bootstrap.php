<?php
  /** Platform Bootstrap
   * @author Sebastian mosser <mosser@polytech.unice.fr>
   * @copyright Polytech'Sophia IaI Team
   * @licence LGPL
   */
session_start();
error_reporting(E_ALL | E_STRICT);
redirectIfUnauthorized();

date_default_timezone_set('Europe/Berlin');


// Requiring functions library
require_once "inc/constants.php";
require_once "inc/page.php";

// Very basic class loader
function __autoload($className)
{
  require_once("inc/".$className.".php");
}


function performLogin($login,$password)
{
  if (! Ldap::checkPassword($login,$password))
    return false;

  $_SESSION["connected"] = true;
  $_SESSION["login"] =  $login;
  $_SESSION["isTeacher"] = false;
  $tlist = new TeachersList();
  if ($tlist->getByLogin($login))
    $_SESSION["isTeacher"] = true;
  return true;
}

function isConnected()
{
  if(array_key_exists("connected",$_SESSION) && $_SESSION["connected"] === true)
    return true;
  else
    return false;
}

function performLogout()
{
  foreach($_SESSION as $k => $v)
    unset($_SESSION[$k]);  
}

function redirectIfUnauthorized()
{
  if (! isConnected() && basename($_SERVER["SCRIPT_FILENAME"]) != "login.php")
    header('Location: login.php?error=unauthorized');
}

function redirectIfNotTeacher() 
{
  if (! $_SESSION["isTeacher"])
    header('Location: login.php?error=teacher-only');
}

?>