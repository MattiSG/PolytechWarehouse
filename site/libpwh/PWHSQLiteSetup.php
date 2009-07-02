<?php
    require_once(LIB_PATH() . "PWHErrorTypes.php");
    
    if ($db = @sqlite_open(DATABASE_FILE(), 0666, $sqliteError)) 
    { 
        if(@sqlite_query($db,
        'CREATE TABLE PWH_Student ( 
            id INTEGER PRIMARY KEY,
            login VARCHAR(10) UNIQUE,
            email VARCHAR(50)
         );')
         
         && @sqlite_query($db,
         'CREATE TABLE PWH_Group ( 
            id INTEGER PRIMARY KEY,
            name VARCHAR(10)
         );')
         
         && @sqlite_query($db,
         'CREATE TABLE PWH_StudentGroup (        
            group_id REFERENCES PWH_Group,
            student_id REFERENCES PWH_Student
         );')
         
         && @sqlite_query($db,
         'CREATE TABLE PWH_Work ( 
            id INTEGER PRIMARY KEY,
            name VARCHAR(10),
            extra_time INTEGER,
            size INTEGER,
            format VARCHAR(30),
            group_min INTEGER,
            group_max INTEGER
         );')
         
         && @sqlite_query($db,
         'CREATE TABLE PWH_Subject ( 
            id INTEGER PRIMARY KEY,
            name VARCHAR(10)
         );')
         
         && @sqlite_query($db,
         'CREATE TABLE PWH_WorkSubject (
            subject_id REFERENCES PWH_Subject,       
            work_id REFERENCES PWH_Work
            
         );'))
         {
            sqlite_close($db);
         }
         else
         {
            throw new Exception(PWHEQUERY . ": Echec lors de l'initialisation de la base de donn&eacute;es");
         }        
    }
    else
    {
        throw new Exception(PWHEACCES);
    }
?>
