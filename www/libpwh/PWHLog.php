<?php
    class PWHLog
    {
        const INFO = "INFO";
        const WARNING = "WARNING";
        const ERROR = "ERROR";
        
        public static function Write($type, $source, $message)
        {
            if($db = @sqlite_open(LOG_FILE())) 
            {
                $query = 'INSERT INTO ' . __CLASS__ . " VALUES('" . sqlite_escape_string($type) .  "', '" . sqlite_escape_string($source) . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . date("Y-m-d H:i:s") . "', '" . sqlite_escape_string($message) . "');";
                if(@sqlite_query($db, $query, 0666, $err))
                {
                    sqlite_close($db);
                }
                else
                {
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Cr&eacute;ation d'un log " . $err);
                }
            }
            else
            {
                throw new PWHIOException(PWHIOException::PWHEACCES);
            }
        }

        public static function Debug()
        {
            if($db = @sqlite_open(LOG_FILE())) 
            {
                echo '<table><caption>' . __CLASS__ . '</caption>';
                echo '<tr><th>type</th><th>user_login</th><th>user_ip</th><th>date</th><th>message</th></tr>';
                $query = 'SELECT * FROM ' . __CLASS__ . ';';             
                if($result = sqlite_query($db, $query))
                {
                    while($entry = sqlite_fetch_array($result))
                    {
                        echo '<tr><td>'.$entry['type'].'</td><td>'.$entry['user_login'].'</td><td>'.$entry['user_ip'].'</td><td>'.$entry['date'].'</td><td>'.$entry['message'].'</td></tr>';
                    }
                }
                echo '</table>';

                sqlite_close($db);
            }
        }
    }
?>
