<?php
  /** Course List object binding (business code)
   * @author Sebastian mosser <mosser@polytech.unice.fr>
   * @copyright Polytech'Sophia IaI Team
   * @licence LGPL
   */


class Course extends XmlData
{
  
  function __construct($name)
  {
    $this->load(COURSES_ROOT . $name . ".xml");
  }

  function getLabel() { return $this->document["label"]; }
  function getLongName() { return (string) $this->document->longname; }

  function getGroups()
  {
    return $this->document->xpath("students/group");
  }

  function getDeliverables()
  {
    return $this->document->xpath("deliverable/delivery");
  }

  function getDeliverable($uid)
  {
    return $this->document->xpath("deliverable/delivery[@uid='$uid']");
  }

  function getDeliverableProducts($dUid)
  {
    $query = "deliverable/delivery[@uid='$dUid']/product/file";
    return $this->document->xpath($query);
  }

  public function toHtml() {
    $result = "<h1>".$this->document->longname. "</h1>\n";
    $result .= "<h2>Informations générales</h2>\n";
    $teachers = new TeachersList();
    $result .= "<ul>\n";
    $result .= "<li> Responsable : " . $teachers->displayByLogin($this->document->responsible)." (<a href=\"mailto:".$this->document->responsible.MAIL_DOMAIN."\">" . $this->document->responsible.MAIL_DOMAIN ."</a>)</li>\n";
    $promo = new Promotion($this->document->students["promotion"]);
    $result .= "<li> Public étudiant : " .$promo->getName(). "\n";
    $result .= "<ul>";
    foreach($this->document->students->group as $g) {
      $result .= "<li>";
      $result .= (string) $g ." : " . $teachers->displayByLogin($g["advisor"])." (<a href=\"mailto:".$g["advisor"].MAIL_DOMAIN."\">" . $g["advisor"].MAIL_DOMAIN ."</a>)";
      $result .= "</li>";
    }
    $result .= "</ul></li>";
    $result .= "</ul>";
    $result .= "<h2>Synthèse des rendus</h2>";
    $result .= "<table>";
    $result .= "<tr><th>Rendu \ Groupe</th>";
    foreach($this->document->students->group as $g) {
      $result .= "<th>".$g."</th>";
    }
    $result .= "</tr>";
    $i = 0;
    foreach($this->document->deliverable->delivery as $d) {
      $class = "class=\"".($i++ % 2 == 0 ? "odd" : "even") ."\"";
      $result .= "<tr $class ><th>".$d->name."</th>";
      foreach ($d->due_date->group as $g) {
	$result .= "<td>".$g."</td>";
      }
      $result .= "</tr>";
    }
    $result .= "</table>";

    $result .= "<h2>Descriptif des rendus</h2><ul>";
    foreach($this->document->deliverable->delivery as $d) {
      $result .= "<li> [".$d["uid"]."] ".$d->name."";
      $result .= "<ul>";
      foreach($d->product->file as $f) {
	$result .= "<li><code>".$f["name"].$f["extension"]."</code></li>";
      }
      $result .= "</ul></li>";
    }
    $result .= "</ul>";
    return $result;
  }

  public static function displayAsHtml($courseId) 
  {
    $course = new Course($courseId);
    return $course->toHtml();
  }

  public static function pajax_getGroups($courseId)
  {
    $course = new Course($courseId[0]);
    $groups = $course->getGroups();
    $teachers = new TeachersList();
    $result = array();
    foreach($groups as $g) {
      $opt =  (string) $g;
      $opt .= " [".$teachers->displayByLogin($g["advisor"])."]";
      $result[(string) $g] = $opt;
    }
    return $result;
  }

  public static function pajax_getDeliverables($params)
  {
    $courseId = $params[1];
    $groupId = $params[0];
    $course = new Course($courseId);
    $result = array();
    foreach($course->getDeliverables() as $d) {
      $opt = $d->name;
      $result[(string)$d["uid"]] = $opt;
    }
    return $result;
  }

  public static function pajax_getProducts($params)
  {
    $courseId = $params[0];
    $deliverableId = $params[1];
    $course = new Course($courseId);
    $result = array();
    foreach($course->getDeliverableProducts($deliverableId) as $p) {
      $result[(string)$p["name"]] = $p["name"] . " [" . $p["extension"] ."]";
    }
    return $result;
  }

  public static function pajax_getCourseDescription($p)
  {
    $c = new Course($p[0]);
    return $c->toHtml();
  }

}
?>