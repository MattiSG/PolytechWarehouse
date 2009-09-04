<?php
    class PWHStudent extends PWHEntity
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
            return '{student} ' . parent::__toString() . ' [login:' . $this->_Login . '] [firstname:' . $this->_FirstName . '] [lastname:' . $this->_LastName . '] [email:' . $this->_Email . ']';
        }
                        
        public function Create($overwrite)
        {
            if ($db = @sqlite_open(DATABASE_FILE())) 
            {
                $query = 'INSERT INTO ' . __CLASS__ . " VALUES(NULL, '" . sqlite_escape_string($this->_Login) . "', '" . sqlite_escape_string($this->_FirstName) . "', '" . sqlite_escape_string($this->_LastName) ."', '" . sqlite_escape_string($this->_Email) . "');";
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
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Cr&eacute;ation de l'&eacute;tudiant " . $this->_Login);
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
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Lecture d'un &eacute;tudiant");
                }
            }
            else
            {
                throw new PWHIOException(PWHIOException::PWHEACCES);
            }       
        }
        
        public function Update()
        {
            if($this->IsPersistent())
            {
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    $query = 'UPDATE ' . __CLASS__ . " SET login = '" . sqlite_escape_string($this->_Login);
                    $query .=  "',firstname = '" . sqlite_escape_string($this->_FirstName);
                    $query .=  "',lastname = '" . sqlite_escape_string($this->_LastName);
                    $query .=  "',email = '" . sqlite_escape_string($this->_Email); 
                    $query .="' WHERE id = " . sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; jour de l'&eacute;tudiant " . $this->_Login);                        
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
            if($this->IsPersistent())
            {
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    $query = 'DELETE FROM ' . __CLASS__ . ' WHERE id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Suppression de l'&eacute;tudiant " . $this->_Login);
                    }
                    
                    $query = 'DELETE FROM ' . __CLASS__ . ' WHERE student_id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Suppression de l'&eacute;tudiant " . $this->_Login . " dans les groupes");                 
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
                
        public function GetLogin()
        {
            return $this->_Login;
        }
        
        public function SetLogin($login)
        {
            $this->_Login = $login;
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
                
        public function GetEmail()
        {
            return $this->_Email;
        }
        
        public function SetEmail($email)
        {
            $this->_Email = $email;
        }
                
        public function GetGroups()
        {
            if($this->IsPersistent())
            {
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    if($result = @sqlite_query($db, 'SELECT group_id FROM PWHStudentGroup WHERE student_id = ' . sqlite_escape_string($this->_ID) . ';'))
                    {
                        sqlite_close($db);                  
                        $groups = array();
                        while($tuple = sqlite_fetch_array($result))
                        {
                            $group = PWHEntity::NewInstance('PWHGroup');
                            $group->Read((int)$tuple['group_id']);
                            array_push($groups, $group);
                        }
                        return $groups;
                    }
                    else
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Liste des groupes de l'&eacute;tudiants");
                    }
                }
                else
                {
                    throw new PWHIOException(PWHIOException::PWHEACCES);
                }
            }
            else
            {
                throw new PWHQueryException(PWHQueryException::PWHENOTREG . ": Liste des groupes de l'&eacute;tudiant " . $this->_Login);
            }
        }
        
        public function HasGroups()
        {
            if($this->IsPersistent())
            {
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    if($result = @sqlite_query($db, 'SELECT COUNT(group_id) AS count FROM PWHStudentGroup WHERE student_id  = ' . sqlite_escape_string($this->_ID) . ';'))
                    {
                        sqlite_close($db);                  
                        $result = sqlite_fetch_single($result);
                        return $result['count'] > 0;
                    }
                    else
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Existence de groupes pour l'&eacute;tudiant " . $this->_Login);
                    }
                }
                else
                {
                    throw new PWHIOException(PWHIOException::PWHEACCES);
                } 
            }
            else
            {
                throw new PWHQueryException(PWHQueryException::PWHENOTREG . ": Existence de groupes pour l'&eacute;tudiant " . $this->_Login);
            }
        }
        
        public function GetDelivery($workID)
        {
            $work = new PWHWork();
            $work->Read($workID);
            $deliveries = $work->GetDeliveries();
            foreach($deliveries as $delivery)
            {
                $groups = $delivery->GetGroups();
                foreach($groups as $group)
                {
                    if($group->StudentExists($this->_ID))
                    {
                        return $delivery;
                    }
                }
            }
        }
        public function GetDeliverygroup($deliveryID)
        {
            if($this->IsPersistent())
            {
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    if($result = @sqlite_query($db, 'SELECT * FROM PWHStudentDeliverygroup WHERE student_id  = ' . sqlite_escape_string($this->_ID) . ';'))
                    {
                        while($entry = sqlite_fetch_array($result))
                        {
                            if($result2 = @sqlite_query($db, 'SELECT deliverygroup_id FROM PWHDeliverygroupDelivery WHERE delivery_id  = ' . sqlite_escape_string($deliveryID) . ' AND deliverygroup_id = ' . sqlite_escape_string($entry['deliverygroup_id']) . ';'))
                            {                
                                $result2 = sqlite_fetch_single($result2);
                                if($result2)
                                {
                                    $deliverygroup = new PWHDeliverygroup();
                                    $deliverygroup->Read($result2);
                                    sqlite_close($db);
                                    return $deliverygroup;
                                }
                            }
                            else
                            {
                                throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Existence de groupes de rendu pour l'&eacute;tudiant " . $this->_Login);
                            }
                        }
                    }
                    else
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Existence de groupes de rendu pour l'&eacute;tudiant " . $this->_Login);
                    }
                }
                else
                {
                    throw new PWHIOException(PWHIOException::PWHEACCES);
                } 
            }
            else
            {
                throw new PWHQueryException(PWHQueryException::PWHENOTREG . ": Existence de groupes de rendu pour l'&eacute;tudiant " . $this->_Login);
            }
        }     
        
        public function HasDeliverygroup($deliveryID)
        {
            if($this->IsPersistent())
            {
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    if($result = @sqlite_query($db, 'SELECT * FROM PWHStudentDeliverygroup WHERE student_id  = ' . sqlite_escape_string($this->_ID) . ';'))
                    {
                        while($entry = sqlite_fetch_array($result))
                        {
                            if($result2 = @sqlite_query($db, 'SELECT COUNT(deliverygroup_id) AS count FROM PWHDeliverygroupDelivery WHERE delivery_id  = ' . sqlite_escape_string($deliveryID) . ' AND deliverygroup_id = ' . sqlite_escape_string($entry['deliverygroup_id']) . ';'))
                            {                 
                                $result2 = sqlite_fetch_single($result2);
                                if($result2['count'] > 0) 
                                {
                                    sqlite_close($db);
                                    return true; 
                                }
                            }
                            else
                            {
                                throw new PWHQueryException(PWHQueryException::PWHEQUERY . ":1 Existence de groupes de rendu pour l'&eacute;tudiant " . $this->_Login);
                            }
                        }
                        sqlite_close($db);
                        return false;
                    }
                    else
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ":2 Existence de groupes de rendu pour l'&eacute;tudiant " . $this->_Login);
                    }
                }
                else
                {
                    throw new PWHIOException(PWHIOException::PWHEACCES);
                } 
            }
            else
            {
                throw new PWHQueryException(PWHQueryException::PWHENOTREG . ":3 Existence de groupes de rendu pour l'&eacute;tudiant " . $this->_Login);
            }
        }
        
        public static function Debug()
        {
            if($db = @sqlite_open(DATABASE_FILE())) 
            {
                echo '<table><caption>' . __CLASS__ . '</caption>';
                echo '<tr><th>id</th><th>login</th><th>firstname</th><th>lastname</th><th>email</th></tr>';
                $query = 'SELECT * FROM ' . __CLASS__ . ';';             
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
