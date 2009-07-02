<?php
    require_once(LIB_PATH() . "PWHIEntity.php");
    require_once(LIB_PATH() . "PWHErrorTypes.php");
    
    class PWHStudent implements PWHIEntity
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
            if ($db = @sqlite_open(DATABASE_FILE())) 
            {
                if($result = @sqlite_query($db, 'SELECT id FROM PWH_Student;'))
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
                    throw new Exception(PWHEQUERY . ': Liste des &eacute;tudiants');
                }
            }
            else
            {
                throw new Exception(PWHEACCES);
            }
        }
        
        public function Create($overwrite)
        {
            if ($db = @sqlite_open(DATABASE_FILE())) 
            {
                $query = "INSERT INTO PWH_Student VALUES(NULL, '" . $this->_Login . "', '" . $this->_Email . "');";
                if(@sqlite_query($db, $query)
                    && $result = @sqlite_query($db, 'SELECT max(id) FROM PWH_Student;'))
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
                    throw new Exception(PWHEQUERY . ": Cr&eacute;ation de l'&eacute;tudiant " . $this->_Login);
                }
            }
            else
            {
                throw new Exception(PWHEACCES);
            }
        }
        
        public function Read($id)
        {
            if ($db = @sqlite_open(DATABASE_FILE())) 
            {
                $query = 'SELECT login, email FROM PWH_Student WHERE id = '. sqlite_escape_string($id) . ';';             
                if($result = @sqlite_query($db, $query))
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
                    throw new Exception(PWHEQUERY . ": Lecture d'un &eacute;tudiant");
                }
            }
            else
            {
                throw new Exception(PWHEACCES);
            }       
        }
        
        public function Update()
        {
            if($this->_ID > 0)
            {
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    $query = "UPDATE PWH_Student SET login = '" . sqlite_escape_string($this->_Login);
                    $query .=  "',email = '" . sqlite_escape_string($this->_Email) . 
                    $query .="' WHERE id = " . sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new Exception(PWHEQUERY . ": Mise &agrave; jour de l'&eacute;tudiant " . $this->_Login);                        
                    }
                    sqlite_close($db);
                }
                else
                {
                    throw new Exception(PWHEACCES);
                }
            }
            else
            {
                throw new Exception(PWHEUPNOTREG);
            }
        }
        
        public function Delete()
        {
            if($this->_ID > 0)
            {
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    $query = 'DELETE FROM PWH_Student WHERE id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new Exception(PWHEQUERY . ": Suppression de l'&eacute;tudiant " . $this->_Login);
                    }
                    
                    $query = 'DELETE FROM PWH_StudentGroup WHERE student_id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new Exception(PWHEQUERY . ": Suppression de l'&eacute;tudiant " . $this->_Login . " dans les groupes");                 
                    }
                    sqlite_close($db);
                }
                else
                {
                    throw new Exception(PWHEACCES);
                }
            }
            else
            {
                throw new Exception(PWHEDELNOTREG);
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
    }
?>
