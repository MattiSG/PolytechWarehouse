<?php
  /**
   */
require_once "inc/constants.php";

function getAvailablePromotions()
{
  $dir = getcwd() . PROMOTION_ROOT;
  $files = scandir($dir);
  $result = array();
  foreach($files as $f) {
    $filename = $dir . "/".$f;
    if (is_file($filename) && ereg("[:word:]*\.xml$",$filename)){
      $tmp = explode(".",$f);
      $result[] = $tmp[0];
    }
  }
  return $result; 
}

function loadPromotion($name)
{
  $filename = getcwd() . PROMOTION_ROOT . $name . ".xml";
  return simplexml_load_file($filename);
}

function getStudents($promotion)
{
  return $promotion->xpath("students/student");
}

function getStudentByUid($promotion,$uid)
{
  $result = $promotion->xpath("students/student[@uid='$uid']");
  if (count($result) != 1)
    return false;
  else
    return $result[0];
}

function getAvailableCourses($promotion)
{
  return $promotion->xpath("courses/course");
}

function getAvailableGroups($promotion)
{
  $nodeSet = $promotion->xpath("groups/group");
  $result = array();
  foreach($nodeSet as $group)
    $result[] = $group["name"];
  return $result;
}

function getStudentsByGroupName($promotion,$groupName)
{
  $nodes = $promotion->xpath("groups/group[@name='$groupName']/member");
  $result = array();
  foreach($nodes as $n)
    $result[] = getStudentByUid($promotion,$n["uid"]);
  return $result;
}

?>