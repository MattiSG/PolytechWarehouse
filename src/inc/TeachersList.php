<?php
  /** Teachers List object binding (business code)
   * @author Sebastian mosser <mosser@polytech.unice.fr>
   * @copyright Polytech'Sophia IaI Team
   * @licence LGPL
   */

class TeachersList extends XmlData
{

  function __construct()
  {
    $this->load(TEACHERS_LIST);
  }

  function getByLogin($login)
  {
    $result = $this->document->xpath("teacher[@login='$login']");
    if (count($result) != 1)
      return null;
    else
      return $result[0];
  }

  function displayByLogin($login)
  {
    $t = $this->getByLogin($login);
    if (null !== $t)
      return $t->firstname ." ".$t->lastname;
    else
      return "??";
  }
  
}

?>