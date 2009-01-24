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
    $courseId = $params[0];
    $groupId = $params[1];
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

}
?>