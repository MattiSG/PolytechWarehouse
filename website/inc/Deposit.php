<?php

class Deposit
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
    $tmp = $this->course->getDeliverable($uid);
    $this->delivery = new Delivery($tmp[0],$this->promo,$this->course);
    $this->groupId = $groupId;
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
    $user = $this->promo->getStudentByLogin($_SESSION["login"]);
    if (! $user) {
		$this->errors[] = "Utilisateur ".$_SESSION["login"]." inconnu !";
		return false; 
    }
      
    if ($this->promo->isMemberOf($user["uid"],$this->groupId))
      return true;
    $e = "L'utilisateur <em>" . $user->lastname . " " . $user->firstname;
    $e .= "</em> de login <code>".$_SESSION["login"]."</code>";
    $e .= " n'appartient pas au groupe <strong>".$this->groupId."</strong>";
    $e .= " de la promotion <strong>" . $this->promo->getName()."</strong>";
    $this->errors[] = $e;
    return false;
  }

  private function validateDate()
  {
    $date = $this->delivery->getDueDate($this->groupId);
    $due = strtotime((string) $date);
    if (($due - time()) > 0)
      return true;
    $limit = date("d/m/Y à H:i:s",strtotime($date[0]));
    
    $e = "<strong>Trop tard !</strong> La date limite de dépôt était fixée au <strong>" . $limit."</strong>";
    $e .= " Voyez directement avec votre encadrant les conséquences de votre retard.";
    $this->errors[] = $e;
    return  false;
  }

  private function storeProducts()
  {
    $this->prepareBox();
    $root = $this->delivery->getBoxName($this->groupId,$_SESSION["login"]);
    foreach($this->products as $name => $p){
      $fileName = $this->delivery->getProductFileName($name);
      move_uploaded_file($p['tmp_name'],$root."/".$fileName);
    }
  }



  private function prepareBox()
  {
    $root = $this->delivery->getBoxName($this->groupId,$_SESSION["login"]);
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
    $root = $this->delivery->getBoxName($this->groupId,$_SESSION["login"]);
    foreach($this->products as $name => $real){
      if (! is_array($real)) {
	$w = "Erreur de chargement ! Le fichier <code>$name</code> n'a pas";
	$w .= " été correctement chargé. Explications :  <code>$real</code>";
	$this->errors[] = $w;
	continue;
      }
      $tmp = $this->delivery->getProductFile($name);
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
	return "votre fichier n'est pas un fichier téléchargé ... étrange ...";
      }
    case UPLOAD_ERR_INI_SIZE:  
    case UPLOAD_ERR_FORM_SIZE: 
      return "Fichier trop lourd (".(MAX_FILE_SIZE / 1024 / 1024)."Mo max)";
    case UPLOAD_ERR_PARTIAL:
      return "Le fichier a été chargé partiellement.";
    case UPLOAD_ERR_NO_FILE:
      return "Aucun fichier fourni par l'utilisateur";
    case UPLOAD_ERR_NO_TMP_DIR:
    case UPLOAD_ERR_CANT_WRITE:
    case UPLOAD_ERR_EXTENSION:
      return "Mauvaise configuration du serveur. Contactez l'administrateur.";
    default:
      return "Cas d'erreur non prévu par la plate forme ... vraiment étrange ...";
    }
  }

}


?>