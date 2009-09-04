<?php    
    if ($db = @sqlite_open(DATABASE_FILE(), 0666, $sqliteError)) 
    { 
        if(!@sqlite_query($db,
        'CREATE TABLE PWHTeacher ( 
            id INTEGER PRIMARY KEY,
            login VARCHAR(10) UNIQUE,
            firstname VARCHAR(20),
            lastname VARCHAR(20),
            email VARCHAR(50)
         );', 0666, $sqliteError))
        {
            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Echec lors de l'initialisation de la base de donn&eacute;es Table PWHTeacher [" . $sqliteError . "]");
        }
        
        if(!@sqlite_query($db,
        'CREATE TABLE PWHStudent ( 
            id INTEGER PRIMARY KEY,
            login VARCHAR(10) UNIQUE,
            firstname VARCHAR(20),
            lastname VARCHAR(20),
            email VARCHAR(50)
         );', 0666, $sqliteError))
        {
            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Echec lors de l'initialisation de la base de donn&eacute;es Table PWHStudent [" . $sqliteError . "]");
        }
        
        if(!@sqlite_query($db,
        'CREATE TABLE PWHEvent ( 
            id INTEGER PRIMARY KEY,
            target_id INTEGER,
            target_type INTEGER,
            creation TIMESTAMP,
            message TEXT
         );', 0666, $sqliteError))
        {
            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Echec lors de l'initialisation de la base de donn&eacute;es Table PWHStudent [" . $sqliteError . "]");
        }
         
        if(!@sqlite_query($db,
         'CREATE TABLE PWHGroup ( 
            id INTEGER PRIMARY KEY,
            name VARCHAR(50)
         );', 0666, $sqliteError))
        {
            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Echec lors de l'initialisation de la base de donn&eacute;es Table PWHGroup [" . $sqliteError . "]");
        }
        
        if(!@sqlite_query($db,
         'CREATE TABLE PWHGroupTree ( 
            parent_id REFERENCES PWHGroup,
            child_id REFERENCES PWHGroup
         );', 0666, $sqliteError))
        {
            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Echec lors de l'initialisation de la base de donn&eacute;es Table PWHGroupTree [" . $sqliteError . "]");
        }
         
        if(!@sqlite_query($db,
         'CREATE TABLE PWHStudentGroup (        
            group_id REFERENCES PWHGroup,
            student_id REFERENCES PWHStudent
         );', 0666, $sqliteError))
        {
            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Echec lors de l'initialisation de la base de donn&eacute;es Table PWHStudentGroup[" . $sqliteError . "]");
        }
         
        if(!@sqlite_query($db,
         'CREATE TABLE PWHWork ( 
            id INTEGER PRIMARY KEY,
            name VARCHAR(10),
            extra_time INTEGER,
            size INTEGER,
            group_min INTEGER,
            group_max INTEGER,
            link VARCHAR(256),
            level INTEGER,
            simple BOOLEAN,
            published BOOLEAN
         );', 0666, $sqliteError))
        {
            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Echec lors de l'initialisation de la base de donn&eacute;es Table PWHWork [" . $sqliteError . "]");
        }
        
        if(!@sqlite_query($db,
         'CREATE TABLE PWHWorkFiles ( 
            work_id REFERENCES PWHWork,
            name VARCHAR(30),
            format VARCHAR(30)
         );', 0666, $sqliteError))
        {
            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Echec lors de l'initialisation de la base de donn&eacute;es Table PWHWorkFiles [" . $sqliteError . "]");
        }
         
        if(!@sqlite_query($db,
         'CREATE TABLE PWHDelivery ( 
            id INTEGER PRIMARY KEY,
            name VARCHAR(10),
            group_comp_deadline TIMESTAMP,
            deadline TIMESTAMP
         );', 0666, $sqliteError))
        {
            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Echec lors de l'initialisation de la base de donn&eacute;es Table PWHDelivery [" . $sqliteError . "]");
        } 
         
        if(!@sqlite_query($db,
         'CREATE TABLE PWHSubject ( 
            id INTEGER PRIMARY KEY,
            name VARCHAR(10)
         );', 0666, $sqliteError))
        {
            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Echec lors de l'initialisation de la base de donn&eacute;es Table PWHSubject [" . $sqliteError . "]");
        }
        
        if(!@sqlite_query($db,
         'CREATE TABLE PWHTeacherSubject ( 
            subject_id REFERENCES PWHSubject,
            teacher_id REFERENCES PWHTeacher
         );', 0666, $sqliteError))
        {
            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Echec lors de l'initialisation de la base de donn&eacute;es Table PWHSubjectTeacher [" . $sqliteError . "]");
        }
        
        if(!@sqlite_query($db,
         'CREATE TABLE PWHGroupSubject ( 
            subject_id REFERENCES PWHSubject,
            group_id REFERENCES PWHGroup
         );', 0666, $sqliteError))
        {
            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Echec lors de l'initialisation de la base de donn&eacute;es Table PWHSubjectTeacher [" . $sqliteError . "]");
        }
        
        if(!@sqlite_query($db,
         'CREATE TABLE PWHWorkSubject (
            subject_id REFERENCES PWHSubject,       
            work_id REFERENCES PWHWork
         );', 0666, $sqliteError))
        {
            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Echec lors de l'initialisation de la base de donn&eacute;es Table PWHWorkSubject [" . $sqliteError . "]");
        }
         
        if(!@sqlite_query($db,
         'CREATE TABLE PWHWorkTeacher (
            teacher_id REFERENCES PWHTeacher,
            work_id REFERENCES PWHWork
         );', 0666, $sqliteError))
        {
            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Echec lors de l'initialisation de la base de donn&eacute;es Table PWHWorkTeacher [" . $sqliteError . "]");
        }
         
        if(!@sqlite_query($db,
         'CREATE TABLE PWHDeliveryWork (
            work_id REFERENCES PWHWork,
            delivery_id REFERENCES PWHDelivery          
         );', 0666, $sqliteError))
        {
            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Echec lors de l'initialisation de la base de donn&eacute;es Table PWHDeliveryWork [" . $sqliteError . "]");
        }
         
        if(!@sqlite_query($db,
         'CREATE TABLE PWHGroupDelivery (
            delivery_id REFERENCES PWHDelivery,
            group_id REFERENCES PWHGroup
         );', 0666, $sqliteError))
        {
            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Echec lors de l'initialisation de la base de donn&eacute;es Table PWHGroupDelivery [" . $sqliteError . "]");
        }
             
        if(!@sqlite_query($db,
         'CREATE TABLE PWHDeliveryTeacher (
            teacher_id REFERENCES PWHTeacher,
            delivery_id REFERENCES PWHDelivery           
         );', 0666, $sqliteError))
        {
            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Echec lors de l'initialisation de la base de donn&eacute;es Table PWHDeliveryTeacher [" . $sqliteError . "]");
        }
         
        if(!@sqlite_query($db,
         'CREATE TABLE PWHDeliverygroup (
            id INTEGER PRIMARY KEY,
            super BOOLEAN,
            extra_time_used BOOLEAN,
            creation TIMESTAMP,
            last_delivery TIMESTAMP
         );', 0666, $sqliteError))
        {
            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Echec lors de l'initialisation de la base de donn&eacute;es Table PWHDeliverygroup [" . $sqliteError . "]");
        }
        
        if(!@sqlite_query($db,
         'CREATE TABLE PWHDeliverygroupDelivery (
            delivery_id REFERENCES PWHDelivery,
            deliverygroup_id REFERENCES PWHDeliverygroup          
         );', 0666, $sqliteError))
        {
            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Echec lors de l'initialisation de la base de donn&eacute;es Table PWHDeliverygroupDelivery [" . $sqliteError . "]");
        }
        
        if(!@sqlite_query($db,
         'CREATE TABLE PWHStudentDeliverygroup (
            deliverygroup_id REFERENCES PWHDeliverygroup,
            student_id REFERENCES PWHStudent          
         );', 0666, $sqliteError))
        {
            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Echec lors de l'initialisation de la base de donn&eacute;es Table PWHStudentDeliverygroup [" . $sqliteError . "]");
        }
        
        sqlite_close($db);      
    }
    else
    {
        throw new PWHIOException(PWHIOException::PWHEACCES);
    }
?>
