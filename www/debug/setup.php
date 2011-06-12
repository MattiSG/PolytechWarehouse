

<?php

$GLOBALS['PWH_PATH'] = "../";
require_once($GLOBALS['PWH_PATH'] . 'libpwh/PWHHeader.php');
require_once($GLOBALS['PWH_PATH'] . 'include/util.php');

try {
	if (!file_exists(LOG_FILE())) {
    	@unlink(LOG_FILE());
    	require_once(LIB_PATH() . "PWHLogSetup.php");
    	echo "<p>" . LOG_FILE() . " supprimé et recréé</p>";
	} else {
		echo "<p>Rien à faire pour : " . LOG_FILE() . "</p>";
	}
} catch(Exception $ex) {
    echo "<p>Une erreur est survenue pour : " . LOG_FILE() . "</p>";
}

try {
	if (!file_exists(DATABASE_FILE())) {
    	@unlink(DATABASE_FILE());
    	require_once(LIB_PATH() . "PWHSQLiteSetup.php");
    	echo "<p>" . DATABASE_FILE() . " supprimé et recréé</p>";
	} else {
		echo "<p>Rien à faire pour : " . DATABASE_FILE() . "</p>";
	}
} catch(Exception $ex) {
        echo "<p>Une erreur est survenue pour : " . DATABASE_FILE() . "</p>";
}

?>