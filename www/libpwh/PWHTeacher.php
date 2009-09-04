<?php
    class PWHTeacher extends PWHEntity
    {
        private $_Login;
        private $_FirstName;
        private $_LastName;
        private $_Email;
        
        public function __construct()
        {
            parent::__construct();
            $this->_Login = "";
            $this->_FirstName = "";
            $this->_LastName = "";
            $this->_Email = "";
        }
        
        public function __toString()
        {
            return '{teacher} ' . parent::__toString() . ' [login:' . $this->_Login . '] [firstname:' . $this->_FirstName . '] [lastname:' . $this->_LastName . '] [email:' . $this->_Email . ']';
        }
                
        public function Create($overwrite)
        {
            if ($db = @sqlite_open(DATABASE_FILE())) 
            {
                $query = 'INSERT INTO ' . __CLASS__ . " VALUES(NULL, '" . sqlite_escape_string($this->_Login) . "', '" . sqlite_escape_string($this->_FirstName) . "', '" . sqlite_escape_string($this->_LastName) . "', '" . sqlite_escape_string($this->_Email) . "');";
                if(@sqlite_query($db, $query)
                    && $result = @sqlite_query($db, 'SELECT max(id) FROM ' . __CLASS__ . ';'))
                {
                    $result = sqlite_fetch_single($result);
                    sqlite_close($db);
                    
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
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Cr&eacute;ation de l'enseignant " . $this->_Login);
                }
            }
            else
            {
                throw new PWHIOException(PWHIOException::PWHEACCES);
            }
        }
        
        public function Read($id)
        {
            if ($db = @sqlite_open(DATABASE_FILE())) 
            {
                $query = 'SELECT * FROM ' . __CLASS__ . ' WHERE id = '. sqlite_escape_string($id) . ';';             
                if($result = @sqlite_query($db, $query))
                {
                    sqlite_close($db);
                    $entry = sqlite_fetch_array($result);
                    $this->_ID = $id;
                    $this->_Login = $entry['login'];
                    $this->_FirstName = $entry['firstname'];
                    $this->_LastName = $entry['lastname'];
                    $this->_Email = $entry['email'];
                    return true;
                }
                else
                {
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Lecture d'un enseignant");
                }
            }
            else
            {
                throw new PWHIOException(PWHIOException::PWHEACCES);
            }       
        }
        
        public function Update()
        {
            if($this->_ID > 0)
            {
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    $query = "UPDATE " . __CLASS__ . " SET login = '" . sqlite_escape_string($this->_Login);
                    $query .=  "',firstname = '" . sqlite_escape_string($this->_FirstName);
                    $query .=  "',lastname = '" . sqlite_escape_string($this->_LastName);
                    $query .=  "',email = '" . sqlite_escape_string($this->_Email);
                    $query .="' WHERE id = " . sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new Exception(PWHEQUERY . ": Mise &agrave; jour de l'enseignant " . $this->_Login);                        
                    }
                    sqlite_close($db);
                }
                else
                {
                    throw new PWHIOException(PWHIOException::PWHEACCES);
                }
            }
            else
            {
                throw new PWHQueryException(PWHQueryException::PWHEUPNOTREG);
            }
        }
        
        public function Delete()
        {
            if($this->_ID > 0)
            {
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    $query = 'DELETE FROM ' . __CLASS__ . ' WHERE id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new Exception(PWHEQUERY . ": Suppression de l'enseignant " . $this->_Login);
                    }
                    
                    sqlite_close($db);
                }
                else
                {
                    throw new PWHIOException(PWHIOException::PWHEACCES);
                }
            }
            else
            {
                throw new PWHQueryException(PWHQueryException::PWHEDELNOTREG);
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
        
        public function GetFirstName()
        {
            return $this->_FirstName;
        }
        
        public function SetFirstName($firstName)
        {
            $this->_FirstName = $firstName;
        }
        
        public function GetLastName()
        {
            return $this->_LastName;
        }
        
        public function SetLastName($lastName)
        {
            $this->_LastName = $lastName;
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
        
        public function GetSubjects()
        {
            if($this->IsPersistent())
            {
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    if($result = @sqlite_query($db, 'SELECT subject_id FROM PWHTeacherSubject WHERE teacher_id = ' . sqlite_escape_string($this->_ID) . ';'))
                    {
                        sqlite_close($db);                  
                        $subjects = array();
                        while($tuple = sqlite_fetch_array($result))
                        {
                            $subject = PWHEntity::NewInstance('PWHSubject');
                            $subject->Read((int)$tuple['subject_id']);
                            array_push($subjects, $subject);
                        }
                        return $subjects;
                    }
                    else
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Liste des mati&egrave;res de l'enseignant " . $this->_Login);
                    }
                }
                else
                {
                    throw new PWHIOException(PWHIOException::PWHEACCES);
                }
            }
            else
            {
                throw new PWHQueryException(PWHQueryException::PWHENOTREG . ": Liste des mati&egrave;res de l'enseignant " . $this->_Login);
            }
        }
                
        public static function debug()
        {
            if($db = @sqlite_open(DATABASE_FILE())) 
            {
                echo '<table><caption>' . __CLASS__ . '</caption>';
                echo '<tr><th>id</th><th>login</th><th>lastname</th><th>firstname</th><th>email</th></tr>';
                $query = 'SELECT * FROM PWHTeacher;';             
                if($result = sqlite_query($db, $query))
                {
                    while($entry = sqlite_fetch_array($result))
                    {
                        echo '<tr><td>'.$entry['id'].'</td><td>'.$entry['login'].'</td><td>'.$entry['firstname'].'</td><td>'.$entry['lastname'].'</td><td>'.$entry['email'].'</td></tr>';
                    }
                }
                echo '</table>';

                sqlite_close($db);
            }
        }
    }
?>
