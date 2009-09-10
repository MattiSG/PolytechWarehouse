<?php
  /*
   * login.php
   * Displays a form to login the user.
   */

  // Sets of default values	
if(empty($_POST['login'])) $_POST['login'] = "";
if(empty($login)) $login = "";
if(empty($_POST['pwd'])) $_POST['pwd'] = "";
if(empty($pwd)) $pwd = "";

if (empty($_GET['action'])) $_GET['action']='';
$action = $_GET['action'];

if($action == 'login')
  {
    // Gets the specified login and password
    $login = $_POST['login'];
    $pwd = $_POST['pwd'];
    
    // admin account
    $login_admin = ADMIN_USER;
    $pwd_admin =  ADMIN_PASS;
    
    // Looks if the login and password matches
    if ($login == $login_admin && $pwd = $pwd_admin)
      {
	$_SESSION['logged'] = true;
	$_SESSION['login'] = $login;
	$_SESSION['type'] = ADMIN_TYPE;
	$_SESSION['id'] = 0;
	PWHLog::Write(PWHLog::INFO, "admin", "Connexion de l'administrateur");
	redirect('index.php');		
      }
    else if(PWHLogger::CheckPassword($login, $pwd))
      {
	$_SESSION['type'] = PWHLogger::GetUserType($login);
	if($_SESSION['type'] != UNREGISTERED_TYPE)
	  {
	    $_SESSION['id'] = PWHLogger::GetUserID($login, $_SESSION['type']);
	    if($_SESSION['id'] != NO_ID)
	      {
		$_SESSION['logged'] = true;
		$_SESSION['login'] = $login;
		PWHLog::Write(PWHLog::INFO, $_SESSION['login'], "Connexion de l'utilisateur");
		redirect('index.php');
	      }
	    else
	      {
		PWHLog::Write(PWHLog::ERROR, $login, "Echec de la connexion: utilisateur LDAP non charg&eacute;");
		errorReport("Echec de la connexion : Vos identifiants sont corrects mais vous n'avez pas encore &eacute;t&eacute; enregistr&eacute; dans le d&eacute;p&ocirc;t. Veuillez le signaler &agrave; votre responsable de parcours ou &agrave; l'administrateur.");   
	      }
	  }
	else
	  {
	    PWHLog::Write(PWHLog::ERROR, $login, "Echec de la connexion: utilisateur LDAP non charg&eacute;");
	    errorReport("Echec de la connexion : Vos identifiants sont corrects mais vous n'avez pas encore &eacute;t&eacute; enregistr&eacute; dans le d&eacute;p&ocirc;t. Veuillez le signaler &agrave; votre responsable de parcours ou &agrave; l'administrateur.");
	  }
      }
    else
      { 
	PWHLog::Write(PWHLog::ERROR, "__not_logged", "Echec de la connexion: identifiant et/ou mot de passe incorrects");
	errorReport("Echec de la connexion : L'identifiant et/ou le mot de passe sont incorrects."); 
      }	
  }

?>
<fieldset>
<legend>connexion</legend>
<?php
$help = new PWHHelp();
echo $help->Html("javascript:popup('include/help/login.html', 800, 550);");

displayErrorReport();
?>
<div class="section">
  <form name="authentication" method="post" action="index.php?page=login&action=login">
  <div class="input">
  <label for="login">Login:</label><input name="login" id="login" type="text" size="20" />            </div>
  <div class="input">
  <label for="pwd">Mot de passe:</label>
  <input name="pwd" id="pwd" type="password" size="20" />
  <input name="submit" id="submit" type="submit" value="Connexion !"/>
  </div>
  </form>
  </div>
  </fieldset>
  