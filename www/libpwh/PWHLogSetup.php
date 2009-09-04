<?php    
    if($db = @sqlite_open(LOG_FILE(), 0666, $sqliteError)) 
    { 
         if(!@sqlite_query($db,
         'CREATE TABLE PWHLog (
            type VARCHAR(10),
            user_login VARCHAR(10),
            user_ip VARCHAR(20),
            date TIMESTAMP,
            message TEXT
         );', 0666, $sqliteError))
        {
            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Echec lors de l'initialisation de la base de donn&eacute;es Table PWHLog [" . $sqliteError . "]");
        }
        
        sqlite_close($db);
    }
    else
    {
        throw new PWHIOException(PWHIOException::PWHEACCES);
    }
?>
