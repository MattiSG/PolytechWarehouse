<?php

class Deposit extends XmlData
{
  
  private $groupId;
  private $course;
  private $promo;
  private $products;
  private $errors;
  private $warnings;

  public function __construct($courseId,$uid,$promoId,$groupId)
  {
    $this->course = new Course($courseId);
    $this->promo = new Promotion($promoId);
    $this->groupId = $groupId;
    $tmp = $this->course->getDeliverable($uid);
    $this->document = $tmp[0];
    $this->products = $this->fillProducts();
    $this->errors = array();
    $this->warnings = array();
  }

  public function perform()
  {
    if ($this->validateUser() && $this->validateDate()){
      $this->storeProducts();
      $this->checkProducts();
    }
    return $this->generateProof();
  }


  private function validateUser()
  {
    $userId = $_SESSION["uid"];
    if ($this->promo->isMemberOf($userId,$this->groupId))
      return true;
    $e = "Student " . $_SESSION["firstname"] . " " . $_SESSION["lastname"];
    $e .= "(<code>#$userId / ".$_SESSION["login"]."</code>)";
    $e .= "is not a member of group <code>".$this->groupId."</code>";
    $this->errors[] = $e;
    return false;
  }

  private function validateDate()
  {
    $date = $this->document->xpath("due_date/group[@uid='".$this->groupId."']");
    $due = strtotime((string)$date[0]);
    if (($due - time()) > 0)
      return true;
    $e = "Too late !! Your delivery was expected for " . $date[0];
    $this->errors[] = $e;
    return  false;
  }

  private function storeProducts()
  {
    $this->prepareBox();
    $root = $this->getBoxName();
    foreach($this->products as $name => $p){
      $tmp = $this->document->xpath("product/file[@name='$name']");
      $ext = $tmp[0]["extension"];
      move_uploaded_file($p['tmp_name'],$root."/".$name . $ext);
    }

  }

  private function getBoxName()
  {
    $root = getcwd() . BOXES . "/" . $this->promo->getLabel() . "/" ;
    $root .= $this->course->getLabel() ."/" . $this->document["uid"] . "/";
    $root .= $_SESSION["login"];
    return $root;
  }

  private function prepareBox()
  {
    $root = $this->getBoxName();
    if ( ! is_dir($root))
      mkdir($root,0777,true);
    else {
      $files = scandir($root);
      foreach($files as $f){
	if (is_file($root."/".$f))
	  unlink($root."/".$f);
      }
    }
    return $root; 
  }

  
  private function checkProducts()
  {
    $root = $this->getBoxName();
    foreach($this->products as $name => $real){
      if (! is_array($real)) {
	$w = "Upload Trouble ! The file <code>$name</code> wasn't ";
	$w .= " successfully uploaded. The reason is <code>$real</code>";
	$this->errors[] = $w;
	continue;
      }
      $tmp = $this->document->xpath("product/file[@name='$name']");
      $expected = $tmp[0];
      $ext = str_replace(".","\.",(string) $expected["extension"]);
      if (! ereg("[:word:]*".$ext."$",$real["name"])){
	$w = "Extension mismatch ! You provide as <code>".$name."</code>";
	$w .= " a file named <code>";
	$w .= $real["name"] . "</code> but you were supposed to upload a";
	$w .= " file using the <code>".$expected["extension"]."</code>";
	$w .= " extension";
	$this->warnings[] = $w;
      }
    }
  }

  private function generateProof()
  {
    $result = array();
    $result["errors"] = $this->errors;
    $result["warnings"] = $this->warnings;
    return $result;
  }

  private function fillProducts()
  {
    $result = array();
    foreach($_FILES as $k => $v)
      $result[$k] = $this->extractFile($k);
    return $result;
  }

  private function extractFile($inputName)
  {
    switch($_FILES[$inputName]['error']){
    case UPLOAD_ERR_OK:
      if (is_uploaded_file($_FILES[$inputName]['tmp_name'])) {
	return $_FILES[$inputName];
      } else {
	return "The file doesn't seem to be an uploaded file ... weird ...";
      }
    case UPLOAD_ERR_INI_SIZE:  
    case UPLOAD_ERR_FORM_SIZE: 
      return "File size exceed the maximum allowed by the system !";
    case UPLOAD_ERR_PARTIAL:
      return "The file was partially uploaded due to a transport problem";
    case UPLOAD_ERR_NO_FILE:
      return "No file to upload !";
    case UPLOAD_ERR_NO_TMP_DIR:
    case UPLOAD_ERR_CANT_WRITE:
    case UPLOAD_ERR_EXTENSION:
      return "Server misconfiguration ... contact the administrator";
    default:
      return "Unexpected error case ... really weird, ins't it ?";
    }
  }

}


?>