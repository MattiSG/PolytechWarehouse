<?php
abstract class XmlData
{
  protected $document;

  protected function load($fileName) 
  { 
    $this->document = simplexml_load_file(getcwd() . $fileName);
  }

}

?>