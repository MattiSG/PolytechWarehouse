<?php
   /** Polytech WareHouse AJAX framework (aka paf) server-side
   * @author Sebastian mosser <mosser@polytech.unice.fr>
   * @copyright Polytech'Sophia IaI Team
   * @licence LGPL
   */
require "bootstrap.php";
//error_reporting(0);
class Pajax {

  public static function run() 
  {
		
    $request = (array_key_exists("request",$_POST) ? $_POST["request"] : null);
    $request = stripslashes(urldecode($request));
    $request = iconv("ISO-8859-1","UTF-8",$request);
    echo self::handle($request);
  }

  private static function handle($request) 
  {
    if (null == $request)
      return;
    $request    = simplexml_load_string($request);

    $className  = ((string) $request->className);
    $methodName = ((string) $request->methodName);

    $parameters = array();
    foreach ($request->params->param as $p)
      $parameters[] = ((string) $p);
    $answer     = self::invoke($className,$methodName,$parameters);
		
    if (is_array($answer))
      $answer = self::displayArray($answer);
    header("Content-Type: text/xml; charset=UTF-8");
    return "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n".$answer;
  }


  private static function displayArray($a) 
  {
    $result = "<array>";
    foreach($a as $k => $v)
      $result .= "<item key=\"$k\" value=\"$v\" />";
    $result .= "</array>";
    return $result;
  
  }

  private static function invoke($className, $methodName, $parameters) 
  {
    try {
      $method = new ReflectionMethod($className,"pajax_".$methodName);
      $ans = $method->invoke(NULL,$parameters);
      return $ans;
    }
    catch(Exception $e) {
      $ans = "<p> <center> <span class=\"error\">";
      $ans .= " Une erreur est survenue sur le serveur lors du ";
      $ans .= "traitement de la demande </span></center></p><br/>";
      $ans .= nl2br($e->__toString());
      return $ans;
    }
  }
}
Pajax::run();
?>