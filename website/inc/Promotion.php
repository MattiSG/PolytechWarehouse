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
	$aPromo = new Promotion($tmp[0]);
	$result[$tmp[0]] = $aPromo->getName();
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
    $ans = $this->document->xpath("students/student[@uid='$uid']");
    if (count($ans) != 1)
      return false;
    return $ans[0];
  }

  public function getStudentByLogin($login)
  {
    $result = $this->document->xpath("students/student[@login='$login']");
    if (count($result) != 1)
      return false;
    else
      return $result[0];
  }

  public function getLabel() { return $this->document["label"]; }
  public function getName() { return $this->document->name; }

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
    foreach($courses as $c) {
      $cInst = new Course($c["descriptor"]);
      $result[(string)$c["descriptor"]] = $cInst->getLongName();
    }
    return $result;
  }

  public static function pajax_getGroups($promoId)
  {
    $promo = new Promotion($promoId[0]);
    return  $promo->getAvailableGroups();
  }
  
  public static function pajax_getGroupAsHTML($p)
  {
    $promo = new Promotion($p[0]);
    $members = $promo->getStudentsByGroupName($p[1]);
    $tmp = array();
    foreach($members as $s){
      $row = "<tr><td>".$s["uid"]."</td><td>".$s["login"]."</td>";
      $row .= "<td>".$s->lastname."</td><td>".$s->firstname."</td>";
      $row .= "<td><a href=\"mailto:".$s["login"].MAIL_DOMAIN."\">".$s["login"].MAIL_DOMAIN."</a></td>";
      $row .= "</tr>\n";
      $key = "".$s->lastname.$s->firstname;
      $tmp[$key] = $row;
    }
    ksort($tmp);
    $result = "<table>\n";
    $result .= "<tr><th>UID</th><th>login</th><th>Nom</th><th>Prénom</th><th>email</th></tr>\n";
    foreach($tmp as $row)
      $result .= $row;
    $result .= "</table>";
    return $result;
  }

  public static function pajax_getGroupMembersMail($p)
  {
    $promo = new Promotion($p[0]);
    $members = $promo->getStudentsByGroupName($p[1]);
    $result = array();
    foreach($members as $m)
      $result[] = $m["login"].MAIL_DOMAIN;
    return $result;
  }

}

?>