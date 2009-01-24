<?php
  /** Constants factorizing PWH global settings
   * @author Sebastian mosser <mosser@polytech.unice.fr>
   * @copyright Polytech'Sophia IaI Team
   * @licence LGPL
   */
error_reporting(E_ALL | E_STRICT); 

// special files & directories
define("BOXES","/boxes");
define("PROMOTION_ROOT","/config/promos/");
define("COURSES_ROOT","/config/courses/");
define("TEACHERS_LIST","/config/teachers.xml");
define("MAIL_DOMAIN","@polytech.unice.fr");

// Project status
define("DELIVERED",0);
define("TOO_LATE",-1);
define("STILL_DELIVERABLE",1);

// HTML Page file
define("HEADER","/html/header.html");
define("FOOTER","/html/footer.html");

// File size
define("MAX_FILE_SIZE",15*1024*1024);

// LDAP connector
define("LDAP_SERVER","ldaps://godot.polytech.unice.fr");
define("LDAP_ROOT","dc=polytech,dc=unice,dc=fr");

define("BROKEN_GLASS",true);

// Authentication errors
$SECURITY_ERRORS = array("bad-login" => "Informations incorrectes !",
                         "unauthorized" => "Accès non authorisé !",
			 "teacher-only" => "Accès reservé aux enseignants");

?>