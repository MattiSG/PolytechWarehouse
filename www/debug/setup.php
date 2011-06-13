<!doctype html>  
<html lang="fr-FR">
	<head>
		<meta charset="utf-8">
	
		<title>Initialisation des bases de données</title>
	</head>
	
	<body>
		<h1>Initialisation des bases de données</h1>
		<dl>
<?php

$GLOBALS['PWH_PATH'] = '../';
require_once($GLOBALS['PWH_PATH'] . 'libpwh/PWHHeader.php');
require_once($GLOBALS['PWH_PATH'] . 'include/util.php');


function handleDatabase($path, $initializerPath) {
	echo '<dt><code>' . basename($path) . '</code></dt><dd>';

	try {		
		if (! file_exists($path)) {
			include_once($initializerPath);
			echo 'Base initialisée.';
		} else {
			echo 'Rien à faire !';
		}
	} catch (Exception $ex) {
		echo 'Une erreur est survenue !';
	}
	
	echo '</dd>';
}

handleDatabase(LOG_FILE(), LIB_PATH() . 'PWHLogSetup.php');
handleDatabase(DATABASE_FILE(), LIB_PATH() . 'PWHSQLiteSetup.php');

?>
		</dl>
	</body>
</html>