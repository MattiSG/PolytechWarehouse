<?php
	/*
	 * login.php
	 * Displays a form to login the user.
	 */

	// Setting of default values
	if(empty($error_message)) $error_message = "";
	
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
		$error_message = "";
		
		
		// TODO Add LDAP login and password search and check
		// A valid account
		$login2 = "admin";
		$pwd2 = "admin";
		
		// Looks if the logins and passwords matches
		if ($login2 == $login && $pwd2 == $pwd)
		{
			$_SESSION['login'] = $login;
			redirect('index.php');
			
		}
		
		else { $error_message .= "Authentication failed"; }	
	}
?>
<fieldset>
    <legend>connexion</legend>
    <div id="login">
        <?php
	        if($error_message != "")
	        {
		        echo '<p>'. $error_message . '</p>';
	        }

        ?>	
        <form name="authentication" method="post" action="index.php?page=login&action=login">
	        <table style="margin-left:auto; margin-right:auto;">
		        <tr>
			        <td class="no_align"><label for="login">Identifiant</label></td>
			        <td class="no_align"><input name="login" id="login" type="text" size="20" /></td>
		        </tr>
		        <tr>
			        <td class="no_align"><label for="pwd">Mot de passe</label></td>
			        <td class="no_align">
			            <input name="pwd" id="pwd" type="password" size="20" />
			            <input name="submit" id="submit" type="submit" value="Connexion"/>
			        </td>
		        </tr>
	        </table>
        </form>
    </div>
</fieldset>
