<?php
    $GLOBALS['PWH_PATH'] = "../";
    require_once("../libpwh/PWHGlobals.php");
    
    if ($db = sqlite_open(DATABASE_FILE(), 0666, $sqliteError)) 
    { 
        if(sqlite_query($db, 
        'CREATE TABLE PWH_Student ( 
            id INTEGER PRIMARY KEY,
            login VARCHAR(10),
            email VARCHAR(50)
         );', SQLITE_BOTH, $requestFailure)
         
         && sqlite_query($db,
         'CREATE TABLE PWH_Group ( 
            id INTEGER PRIMARY KEY,
            name VARCHAR(10)
         );', SQLITE_BOTH, $requestFailure)
         
         && sqlite_query($db,
         'CREATE TABLE PWH_StudentGroup (        
            group_id REFERENCES PWH_Group,
            student_id REFERENCES PWH_Student
         );', SQLITE_BOTH, $requestFailure))
         {
            echo "Installation r&eacute;ussie";
         }
         else
         {
            die($requestFailure);
         }
        sqlite_close($db);
    }
    else
    {
        die($sqliteError);
    }
?>
