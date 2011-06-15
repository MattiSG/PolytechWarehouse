<?php
  //@seb: moved config definition into config/local.conf
	
	// Global variables
	$GLOBALS['PWH_LIB_DIRNAME'] = 'libpwh/';
	$GLOBALS['EXTERNAL_LIB_DIRNAME'] = 'libext/';
	$GLOBALS['PWH_DATABASE_DIRNAME'] = 'database/';
	$GLOBALS['PWH_LOG_DIRNAME'] = 'log/';
	$GLOBALS['PWH_CONFIG_DIRNAME'] = 'config/';
	$GLOBALS['PWH_IMG_DIRNAME'] = 'img/';
	$GLOBALS['PWH_DATABASE_FILENAME'] = 'pwh.sqlite';
	$GLOBALS['PWH_LOG_FILENAME'] = 'log.sqlite';
	$GLOBALS['PWH_TYPECONGIF_FILENAME'] = 'types.xml';
	$GLOBALS['PWH_UPLOAD_DIRNAME'] = 'uploads/';
	$GLOBALS['PWH_DOWNLOAD_DIRNAME'] = 'downloads/';
	
	
	/*
	 * Global variables getters
	 */
	function LIB_PATH() 
	{
		return $GLOBALS['PWH_PATH'] . $GLOBALS['PWH_LIB_DIRNAME'];
	}
	
	function IMG_PATH() 
	{
		return $GLOBALS['PWH_PATH'] . $GLOBALS['PWH_IMG_DIRNAME'];
	}
	
	function UPLOAD_PATH() 
	{
		return $GLOBALS['PWH_PATH'] . $GLOBALS['PWH_UPLOAD_DIRNAME'];
	}
	
	function DOWNLOAD_PATH() 
	{
		return $GLOBALS['PWH_PATH'] . $GLOBALS['PWH_DOWNLOAD_DIRNAME'];
	}
	
	function DATABASE_PATH() 
	{
		return $GLOBALS['PWH_PATH'] . $GLOBALS['PWH_DATABASE_DIRNAME'];
	}
	
	function LOG_PATH() 
	{
		return $GLOBALS['PWH_PATH'] . $GLOBALS['PWH_LOG_DIRNAME'];
	}
	
	function DATABASE_FILE() 
	{
		return DATABASE_PATH() . $GLOBALS['PWH_DATABASE_FILENAME'];
	}
	
	function LOG_FILE() 
	{
		return LOG_PATH() . $GLOBALS['PWH_LOG_FILENAME'];
	}
	
	function CONFIG_PATH() 
	{
		return $GLOBALS['PWH_PATH'] . $GLOBALS['PWH_CONFIG_DIRNAME'];
	}
	
	function TYPECONFIG_FILE() 
	{
		return CONFIG_PATH() . $GLOBALS['PWH_TYPCONFIG_FILENAME'];
	}
	
	// User type
	define('UNREGISTERED_TYPE', -1);
	define('STUDENT_TYPE', 0);
	define('TEACHER_TYPE', 1);
	define('ADMIN_TYPE', 2);
	define('NO_ID', -1);
?>
