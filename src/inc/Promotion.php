<?php
  /** Promotion object binding (business code)
   * @author Sebastian mosser <mosser@polytech.unice.fr>
   * @copyright Polytech'Sophia IaI Team
   * @licence LGPL
   */

class Promotion extends XmlData
{

  public static function getAvailablePromotions()
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

  public function __construct($name)
  {
    $this->load(PROMOTION_ROOT . $name . ".xml");
  }

  public function getStudents()
  {
    return $this->document->xpath("students/student");
  }

  public function getStudentByUid($uid)
  {
    $result = $this->document->xpath("students/student[@uid='$uid']");
    if (count($result) != 1)
      return false;
    else
      return $result[0];
  }

  public function getLabel() { return $this->document["label"]; }

  public function getAvailableCourses()
  {
    return $this->document->xpath("courses/course");
  }

  public function getAvailableGroups()
  {
    $nodeSet = $this->document->xpath("groups/group");
    $result = array();
    foreach($nodeSet as $group)
      $result[] = $group["name"];
    return $result;
  }
  
  public function getStudentsByGroupName($groupName)
  {
    $nodes = $this->document->xpath("groups/group[@name='$groupName']/member");
    $result = array();
    foreach($nodes as $n)
      $result[] = $this->getStudentByUid($n["uid"]);
    return $result;
  }


  public function isMemberOf($userId,$groupId)
  {
    $query = "groups/group[@name='$groupId']/member[@uid='$userId']";
    $nodes = $this->document->xpath($query);
    return 1 == count($nodes);
  }

  public static function pajax_getCourses($promoId)
  {
    $promo = new Promotion($promoId[0]);
    $courses = $promo->getAvailableCourses();
    $result = array();
    foreach($courses as $c)
      $result[] = $c["descriptor"];
    return $result;
  }
  
}

?>