<?php
	
	// Global variables
	$GLOBALS['PWH_LIB_DIRNAME'] = 'libpwh/';
	$GLOBALS['PWH_DATABASE_DIRNAME'] = 'database/';
	$GLOBALS['PWH_IMG_DIRNAME'] = 'img/';
	$GLOBALS['PWH_DATABASE_FILENAME'] = 'pwh.sqldb';
	
	
	/*
	 * Global variables getters
	 */
	function LIB_PATH() {
		return $GLOBALS['PWH_PATH'] . $GLOBALS['PWH_LIB_DIRNAME'];
	}
	
	function IMG_PATH() {
		return $GLOBALS['PWH_PATH'] . $GLOBALS['PWH_IMG_DIRNAME'];
	}
	
	function DATABASE_PATH() {
		return $GLOBALS['PWH_PATH'] . $GLOBALS['PWH_DATABASE_DIRNAME'];
	}
	
	function DATABASE_FILE() {
		return DATABASE_PATH() . $GLOBALS['PWH_DATABASE_FILENAME'];
	}
	
?>
