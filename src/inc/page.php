<?php
  /**
   */

require_once "inc/constants.php";

function includeHeader($title)
{
  $str = file_get_contents(getcwd().HEADER);
  echo str_replace("%TITLE%",$title,$str);
}

function includeFooter()
{
  echo file_get_contents(getcwd().FOOTER);
}
?>