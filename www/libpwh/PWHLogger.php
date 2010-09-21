<?php
    class PWHLogger
    {
        private static function GetCryptedPassword($userName)
        {
            $userName = addslashes($userName);
            $ldap_id = ldap_connect(LDAP_SERVER);
            ldap_set_option($ldap_id, LDAP_OPT_PROTOCOL_VERSION, 3);
            $binding = ldap_bind($ldap_id); 
            $search_results = ldap_search($ldap_id, LDAP_ROOT, '(uid=' . $userName . ')');
            $results = ldap_get_entries($ldap_id, $search_results);
            $line = explode("{crypt}", $results[0]['userpassword'][0]);
            ldap_unbind($ldap_id);
            return $line[1];
        }

        public static function CheckPassword($userName, $plainTextPassword)
        {
            $userName = addslashes($userName);
            $plainTextPassword = stripslashes($plainTextPassword);
            if(BROKEN_GLASS)
            {
                return true;
            }

            $expected = self::GetCryptedPassword($userName);
            if(crypt($plainTextPassword,$expected) == $expected)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        
        public static function GetUserType($userName)
        {
            if($db = @sqlite_open(DATABASE_FILE())) 
            {
                $query = "SELECT COUNT(id) AS count FROM PWHStudent WHERE login = '" . sqlite_escape_string($userName) . "';";
                
                if($result = @sqlite_query($db, $query, 0666, $err))
                {               
                    $result = sqlite_fetch_single($result);
                    if($result['count'] > 0)
                    {
                        sqlite_close($db);
                        return STUDENT_TYPE;
                    }
                }
                else
                {
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ":[" . $err . "] D&eacute;t&eacute;ction du type de l'utilisateur " . $userName);
                }
                
                $query = "SELECT COUNT(id) AS count FROM PWHTeacher WHERE login = '" . sqlite_escape_string($userName) . "';";
                if($result = @sqlite_query($db, $query))
                {
                                      
                    $result = sqlite_fetch_single($result);
                    if($result['count'] > 0)
                    {
                        sqlite_close($db);
                        return TEACHER_TYPE;
                    }
                }
                else
                {
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": D&eacute;t&eacute;ction du type de l'utilisateur " . $userName);
                }
                
                return UNREGISTERED_TYPE;
            }
            else
            {
                throw new Exception(PWHEACCES);
            } 
        }
        
        public static function GetUserID($userName, $type)
        {
            if($db = @sqlite_open(DATABASE_FILE())) 
            {
                if($type == STUDENT_TYPE)
                {
                    $query = "SELECT id FROM PWHStudent WHERE login = '" . sqlite_escape_string($userName) . "';";
                    if($result = @sqlite_query($db, $query, 0666, $err))
                    {               
                        $result = sqlite_fetch_single($result);
                        sqlite_close($db);
                        return (int)$result;
                    }
                    else
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ":[" . $err . "] D&eacute;t&eacute;ction du type de l'utilisateur " . $userName);
                    }
                }
                else if($type == TEACHER_TYPE)
                {
                    $query = "SELECT id FROM PWHTeacher WHERE login = '" . sqlite_escape_string($userName) . "';";
                    if($result = @sqlite_query($db, $query))
                    {
                        $result = sqlite_fetch_single($result);
                        sqlite_close($db);
                        return (int)$result;
                    }
                    else
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": D&eacute;t&eacute;ction du type de l'utilisateur " . $userName);
                    }
                }
                
                return NO_ID;
            }
            else
            {
                throw new Exception(PWHEACCES);
            } 
        }
    }
?>

