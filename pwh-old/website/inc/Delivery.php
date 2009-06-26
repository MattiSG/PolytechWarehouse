<?php
  /** Delivery object binding (business code)
   * @author Sebastian mosser <mosser@polytech.unice.fr>
   * @copyright Polytech'Sophia IaI Team
   * @licence LGPL
   */

class Delivery extends XmlData
{

  private $promo;
  private $course;

  public function __construct($xmlElement,$promo,$course)
  {
    $this->document = $xmlElement;
    $this->promo = $promo;
    $this->course = $course;
  }

  public function getUid() { return $this->document["uid"]; }
  public function  getName() { return $this->document->name; }


  public function getDueDate($groupId)
  {
    $date = $this->document->xpath("due_date/group[@uid='".$groupId."']");
    return $date[0];
  }

  public function getProductFileName($name) 
  { 
    $tmp = $this->document->xpath("product/file[@name='$name']");
    return (string) $tmp["0"]["name"] . (string) $tmp[0]["extension"];
  }

  public function getProductFile($name) 
  { 
    return $this->document->xpath("product/file[@name='$name']");
  }

  public function getBoxName($group,$login)
  {
    $root = getcwd() . BOXES . "/" . $this->promo->getLabel() . "/" ;
    $root .= $this->course->getLabel() ."/" . $this->getUid(). "/";
    $root .= $group ."/" . $login;
    return $root;
  }

  public function hasDelivered($group,$login)
  {
    $box = $this->getBoxName($group,$login);
    if (! is_dir($box)) 
      return false;
    foreach($this->document->product->file as $f) {
      if (! file_exists($box."/".$f["name"].$f["extension"]))
	return false;
    }
    return true;
  }

  public function isOutOfDate($groupId)
  {
    $expected = $this->getDueDate($groupId);
    return (strtotime((string) $expected) - time() < 0);
  }

  public function getDeliveryStatus($login,$groupId)
  {
    if ($this->hasDelivered($groupId,$login)) {
      $stamp = filemtime($this->getBoxName($groupId,$login));
      $day = date("d/m/Y",$stamp);
      $hour = date("H:i:s",$stamp);
      $value = "<span class=\"day\">$day</span> <span class=\"hour\">$hour</span>";
      return $value;
    }
    else {
      if ($this->isOutOfDate($groupId))
	return TOO_LATE;
      else
	return STILL_DELIVERABLE;
    }
  }


  public static function getDeliverablesStatus($promoId,$courseId,$gId)
  {
    $promo = new Promotion($promoId);
    $course = new Course($courseId);
    
    $students =  array();
    foreach($promo->getStudentsByGroupName($gId) as $s) {
      $students[(string)$s["uid"]] = $s;
    }

    $deliverables = array();
    foreach($course->getDeliverables() as $d) {
      $deliverables[(string) $d["uid"]] = new Delivery($d,$promo,$course);
    }

    $matrix = array();
    foreach($students as $sUid => $s) {
      $matrix[$sUid] = array();
      foreach($deliverables as $dUid => $d)
	$matrix[$sUid][$dUid] = $d->getDeliveryStatus($s["login"],$gId);
    }
    $result = array();
    $result["students"] = $students;
    $result["deliverables"] = $deliverables;
    $result["matrix"] = $matrix;
    return $result;
  }

  public static function getDeliverablesStatusAsHTML($promoId,$courseId,$gId)
  {
    $result = self::getDeliverablesStatus($promoId,$courseId,$gId);
    $header = "<table>";
    $header .= "<tr><th>Nom</th><th>Prénom</th>";
    foreach($result["deliverables"] as $d)
      $header .= "<th>".$d->getName()."</th>";
    $header .= "</tr>\n";
    $content = "";
    $matrix = $result["matrix"];
    $i = 0;
    foreach($result["students"] as $s){
      $class = "class=\"".($i++ % 2 == 0 ? "odd" : "even")."\"";
      $content .= "<tr $class ><td><a href=\"mailto:".$s["login"].MAIL_DOMAIN."\">";
      $content .= $s->lastname."</a></td>";
      $content .= "<td>".$s->firstname."</td>";
      $sUid = (string) $s["uid"];
      foreach($result["deliverables"] as $d) {
	$dUid =  (string) $d->getUid();
	$status = $matrix[$sUid][$dUid];
	switch($status){
	case TOO_LATE:
	  $content .= "<td class=\"ko\">-</td>";
	  break;
	case STILL_DELIVERABLE:
	  $content .= "<td class=\"empty\">?</td>";
	  break;
	default: 
	  $content .= "<td class=\"ok\">$status</td>";
	  break;
	}
      }
      $content .= "</tr>\n";
    }
    return $header.$content."</table>\n";
  }

  public static function pajax_getDeliverablesStatus($p)
  {
    return self::getDeliverablesStatusAsHTML($p[2],$p[1],$p[0]);
  }


}
?>