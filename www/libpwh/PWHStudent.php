<?php
    require_once(LIB_PATH() . "Entity.php");
    
    class PWHStudent implements Entity
    {
        private $_ID;
        private $_Login;
        private $_Email;
        
        public function __construct($login, $email)
        {
            $this->_ID = -1;
            $this->_Login = $login;
            $this->_Email = $email;
        }
        
        public static function ListStudents()
        {
            if ($db = sqlite_open(DATABASE_FILE())) 
            {
                if($result = sqlite_query($db, 'SELECT id FROM PWH_Student;'))
                {
                    sqlite_close($db);                  
                    $students = array();
                    while($tuple = sqlite_fetch_array($result))
                    {
                        $student = new PWHStudent(null, null);
                        $student->Read($tuple['id']);
                        array_push($students, $student);
                    }
                    return $students;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        
        public function Create($overwrite)
        {
            if ($db = sqlite_open(DATABASE_FILE())) 
            {
                $query = "INSERT INTO PWH_Student VALUES(NULL, '" . $this->_Login . "', '" . $this->_Email . "');";
                if(sqlite_query($db, $query)
                    && $result = sqlite_query($db, 'SELECT max(id) FROM PWH_Student;'))
                {
                    sqlite_close($db);
                    $result = sqlite_fetch_single($result);
                    if($overwrite)
                    {
                        $tmp = $this->_ID;
                        $this->_ID = (int)$result;
                        return $tmp;
                    }
                    else
                    {
                        return (int)$result;
                    }
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        
        public function Read($id)
        {
            if ($db = sqlite_open(DATABASE_FILE())) 
            {
                $query = 'SELECT login, email FROM PWH_Student WHERE id = '. $id . ';';             
                if($result = sqlite_query($db, $query))
                {
                    sqlite_close($db);
                    $entry = sqlite_fetch_array($result);
                    $this->_ID = $id;
                    $this->_Login = $entry['login'];
                    $this->_Email = $entry['email'];
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        
        }
        
        public function Update()
        {
            if($this->_ID > 0)
            {
                if ($db = sqlite_open(DATABASE_FILE())) 
                {
                    $query = "UPDATE PWH_Student SET login = '" . $this->_Login . "',email = '" . $this->_Email . "' WHERE id = ". $this->_ID . ';';             
                    if(sqlite_query($db, $query))
                    {
                        sqlite_close($db);
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        
        public function Delete()
        {
            if($this->_ID > 0)
            {
                if ($db = sqlite_open(DATABASE_FILE())) 
                {
                    $query = 'DELETE FROM PWH_Student WHERE id = '. $this->_ID . ';';             
                    if(sqlite_query($db, $query))
                    {
                        sqlite_close($db);
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        
        public function GetID()
        {
            return $this->_ID;
        }
        
        public function GetLogin()
        {
            return $this->_Login;
        }
        
        public function SetLogin($login)
        {
            $this->_Login = $login;
        }
        
        public function GetEmail()
        {
            return $this->_Email;
        }
        
        public function SetEmail($email)
        {
            $this->_Email = $email;
        }
        
        public function IsMapped()
        {
            return $this->_ID > 0;
        }
        
        public function __toString()
        {
            return '[' . $this->_ID . '] ' . $this->_Login . ' ' . $this->_Email;
        }
        
        /* **** DEBUG FUNCTIONS **** */
        public static function Table()
        {
            echo '##### Table PWH_Student<br/>';
            echo 'id;login;email<br/>';
            if ($db = sqlite_open(DATABASE_FILE())) 
            {
                if($result = sqlite_query($db, 'SELECT * FROM PWH_Student;'))
                {
                    sqlite_close($db);
                    while($tuple = sqlite_fetch_array($result))
                    {
                        echo $tuple['id'] . ';' . $tuple['login'] . ';' . $tuple['email'] . '<br/>';
                    }
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
    }
?>
