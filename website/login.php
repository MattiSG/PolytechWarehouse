<?php
  /** Login form for the PWH Platform
   * @author Sebastian Mosser <mosser@polytech.unice.fr>
   * @copyright Polytech'Nice Sophia Engineering School, Web Courses
   * @date 01 / 19 / 2009
   */
require_once "bootstrap.php";

if (array_key_exists("to_do",$_POST)) {
  // The user asks us to do something ... let's go
  $toDo = $_POST["to_do"];
  switch ($toDo) {
  case "login" :
    $login    = (array_key_exists("login",$_POST) ? $_POST["login"]  : false);
    $password = (array_key_exists("pass",$_POST)  ? $_POST["pass"]   : false);
    if (! performLogin($login,$password))
      $_GET["error"] = "bad-login";
    break;
  case "logout":
    performLogout();
    break;
  }
}

// Retrieving error message, in case of error situation
$error    = (array_key_exists("error",$_GET)    ? $_GET["error"]  : false);
$errorText = "";
if ($error !== false)
  $errorText = "<p class=\"error\">".$SECURITY_ERRORS[$error]."</p>";
includeHeader("Formulaire de connexion");
?>

  <h1><?php echo (isConnected() ? "Déconnexion" : "Connexion"); ?></h1>
    <div style="width: 50%;">
      <fieldset>
        <legend>Compte Polytech'Sophia</legend>
        <form action="<?php echo $_SERVER["SCRIPT_NAME"]?>" method="post">
          <input type="hidden" name="to_do"
                 value="<?php echo (isConnected() ? 'logout' : 'login'); ?>" />
          <ul>
 <?php  
if(!isConnected()) {
$form = <<<EOS
            <li>Login : <input type="text" name="login" /> </li>
            <li>Password : <input type="password" name="pass" /> </li>
EOS;
} else {
$item = $_SESSION["login"];
$form = "            <li> Quitter la session de l'utilisateur <code>$item</code> ? </li>";
}
echo $form . "\n";
?>
          </ul>
          <div align="center"><input type="submit"/></div>
        </form>
<?php
  if ($errorText != ""){
    echo "<div align='center'>$errorText</div>\n";
  }
?>
      </fieldset>
    </div>
    <p>
      <a href="http://validator.w3.org/check?uri=referer">
        <img src="http://www.w3.org/Icons/valid-xhtml10-blue"
             alt="Valid XHTML 1.0 Strict" height="31" width="88" /></a>
    </p>

  </body>
</html>
