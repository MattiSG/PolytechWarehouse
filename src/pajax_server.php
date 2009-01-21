<?php
  /**
   */
require "bridge.php";

class Pajax {

  public static function run() {
		
    $request = $_POST["request"];
    $request = stripslashes(urldecode($request));
    $request = iconv("ISO-8859-1","UTF-8",$request);
    echo self::handle($request);
  }

  private static function handle($request) {
    $request    = simplexml_load_string($request);

    $className  = ((string) $request->className);
    $methodName = ((string) $request->methodName);

    $parameters = array();
    foreach ($request->params->param as $p)
      $parameters[] = ((string) $p);
    $answer     = self::invoke($className,$methodName,$parameters);
		
    if (is_array($answer))
      return self::displayArray($answer);
			
    return $answer;
  }


  private static function displayArray($a) {
    $title = "<h3> Array2Html Answer Translation </h3>";
    $tmp = print_r($a,True);
    $tmp = str_replace(" ","&nbsp;",$tmp);
    return $title.nl2br($tmp);
  
  }

  private static function invoke($className, $methodName, $parameters) {
    try {
      // On récupère la méthode statique exposée par introspection
      $method = new ReflectionMethod($className,"pajax_".$methodName);
      // On l'invoque (static ==> NULL) : 
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

// On lance le serveur ^_^
Pajax::run();
?>