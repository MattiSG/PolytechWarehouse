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


// HTML Page file
define("HEADER","/html/header.html");
define("FOOTER","/html/footer.html");

// File size
define("MAX_FILE_SIZE",15*1024*1024);
?>