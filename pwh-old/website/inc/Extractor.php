<?php


class Extractor
{

  private $course;
  private $promo;
  private $delivery;
  private $groupId;
  
  public function __construct($promoId,$courseId,$groupId,$deliverableId)
  {
    $this->course = new Course($courseId);
    $this->promo = new Promotion($promoId);
    $tmp = $this->course->getDeliverable($deliverableId);
    $this->delivery = new Delivery($tmp[0],$this->promo,$this->course);
    $this->groupId = $groupId;
  }


  public function perform()
  {
    $root = $this->delivery->getBoxName($this->groupId,"");
    if (! is_dir($root))
      mkdir($root,0777,true);
    $tmp = $this->makeArchive($root);
    // echo $tmp;
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="deliveries.zip"'); 
    header('Content-Transfer-Encoding: binary');
    echo file_get_contents($tmp);
  }

  private function makeArchive($dir) {
    $old = getCwd();
    chdir($dir);
    $zip = new ZipArchive();
    $archFile = tempnam(sys_get_temp_dir(),"pwh-");
    exec("zip -r $archFile *");
    chdir($old);
    return $archFile.".zip";
  }


}


?>