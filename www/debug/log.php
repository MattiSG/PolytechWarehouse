<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <title>PWH Log's Viewer</title>
        <style>
            table {
                margin-bottom: 20px;
                border: 1px solid black;
                border-collapse: collapse;
            }
            table tr, table td , table th {
                border: 3px solid black;
            }
            
            table th {
                color: white;
                background-color: black;
                border-color: blue;
            }
        </style>
    </head>
    <body>
        <?php
            $GLOBALS['PWH_PATH'] = "../";
            require_once("../libpwh/PWHHeader.php");
            
            function __autoload($className)
            {
                require_once(LIB_PATH() . $className . '.php');
            }

            PWHLog::debug();
       ?>
   </body>
</html>
