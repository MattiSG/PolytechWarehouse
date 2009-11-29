<?php
    class PWHGroup extends PWHEntity
    {
        private $_ParentID;
        private $_Name;
        private $_StudentManager;
                
        public function __construct()
        {
            parent::__construct();
            $this->_ParentID = -1;
            $this->_Name = "";
            $this->_StudentManager = new PWHEntityManager('PWHStudent');
        }
        
        public function __toString()
        {
            $strbuf = '{group} ' . parent::__toString() . ' [name:' .$this->_Name . "] [students:";
            $students = $this->_StudentManager->GetEntities();
            foreach($students as $student)
            {
                $strbuf .= $student->GetID() . " ";
            }
            return $strbuf . ']';
        }
                
        public function Create($overwrite)
        {
            if ($db = @sqlite_open(DATABASE_FILE())) 
            {
                $query = 'INSERT INTO ' . __CLASS__ . " VALUES(NULL, '" . sqlite_escape_string($this->_Name) . "');";             
                if(@sqlite_query($db, $query)
                    && $result = @sqlite_query($db, 'SELECT max(id) FROM ' . __CLASS__ . ';'))
                {
                    $result = sqlite_fetch_single($result);
                    
                    if(!@sqlite_query($db, "INSERT INTO PWHGroupTree VALUES('" . sqlite_escape_string($this->_ParentID) . "', '" . $result . "');"))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Ajout du parent du groupe " . $this->_Name);
                    }
                        
                    $students = $this->_StudentManager->GetEntities();
                    foreach($students as $student)
                    {
                        if(!@sqlite_query($db, "INSERT INTO PWHStudentGroup VALUES('" . $result . "', '" . $student->GetID() . "');"))
                        {
                            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Ajout de l'&eacute;tudiant " . $student->GetLogin() . " dans le groupe " . $this->_Name);
                        }
                    }
                    $this->_StudentManager->Flush();              
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
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Cr&eacute;tion du groupe " . $this->_Name);
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
                $query = 'SELECT * FROM PWHGroup WHERE id = '. sqlite_escape_string($id) . ';';             
                if($result = @sqlite_query($db, $query))
                {               
                    $entry = sqlite_fetch_array($result);
                    $this->_ID = $id;
                    $this->_Name = $entry['name'];        
                }
                else
                {
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Lecture d'un groupe");
                }
                
                $query = 'SELECT * FROM PWHGroupTree WHERE child_id = '. sqlite_escape_string($id) . ';';             
                if($result = @sqlite_query($db, $query))
                {               
                    $entry = sqlite_fetch_array($result);
                    $this->_ParentID = $entry['parent_id'];
                }
                else
                {
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Lecture du parent du groupe " . $this->_Name);
                }
                
                $query = 'SELECT * FROM PWHStudentGroup WHERE group_id = '. sqlite_escape_string($id) . ';';             
                if($result = @sqlite_query($db, $query))
                {
                    $this->_StudentManager->Clear();
                    $students = array();
                    while($entry = sqlite_fetch_array($result))
                    {
                        array_push($students, (int)$entry['student_id']);
                    }
                    $this->_StudentManager->AddEntities($students);
                    $this->_StudentManager->Flush();
                    sqlite_close($db);       
                }
                else
                {
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Lecture des &eacute;tudiant du groupe " . $this->_Name);
                }
            }
            else
            {
                throw new PWHIOException(PWHIOException::PWHEACCES);
            }
        }
        
        public function ReadFromFile($fileName)
        {        
            $students = array();
            $errors = array();
            $errors[0] = array();
            $errors[1] = array();
            // ajouter .* pour nom prénom
            $accents = "éèêëàâäïîûùüöôç'";
            if($file = @file($fileName))
            {
                $i = 0;
                foreach($file as $line)
                {
                    $i++;
                    $line = rtrim($line);
                    if(preg_match("#[a-zA-Z0-9]+;[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4};[-" . $accents . "a-zA-Z0-9 ]+;[-" . $accents . "a-zA-Z0-9 ]+;#", $line))
                    {
                        try 
                        {
                            $info = explode(';', $line);
                            $student = new PWHStudent();
                            $student->SetLogin(strtolower($info[0]));
                            $student->SetFirstName($info[3]);
                            $student->SetLastName($info[2]);
                            $student->SetEmail($info[1]);
                            $student->Create(true);
                            array_push($students, $student->GetID());
                        }
                        catch(PWHQueryException $ex)
                        {
                            array_push($errors[0], $info[3] . " " . $info[2]);
                        }
                    }
                    else
                    {
                        array_push($errors[1], "L" . $i . " : " . $line);
                    }
                }
                $this->_StudentManager->Clear();
                $this->_StudentManager->AddEntities($students);              
                $this->_StudentManager->Flush(); 
                return $errors;
            }
            else
            {
                throw new PWHIOException(PWHIOException::PWHEPRMNOTFOUND);
            }
        }
        
        public function Update()
        {
            if($this->IsPersistent())
            {
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    $query = "UPDATE PWHGroup SET name = '" . sqlite_escape_string($this->_Name);
                    $query .= "' WHERE id = ". sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; jour du groupe " .$this->_Name);
                    }
                    
                    $query = "UPDATE PWHGroupTree SET parent_id = '" . sqlite_escape_string($this->_ParentID);
                    $query .= "' WHERE child_id = ". sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; jour du parent du groupe " .$this->_Name);
                    }
                                     
                    $studentsAdded = $this->_StudentManager->GetEntitiesAdded();
                    foreach($studentsAdded as $student)
                    {
                        $query = "INSERT INTO PWHStudentGroup VALUES ('" . $this->_ID . "', '" . $student . "');";
                        if(!@sqlite_query($db, $query))
                        {
                            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; jour des &eacute;tudiants du groupe " . $this->_Name);
                        }
                    }
                    
                    $studentsRemoved = $this->_StudentManager->GetEntitiesRemoved();                 
                    foreach($studentsRemoved as $student)
                    {
                        $query = 'DELETE FROM PWHStudentGroup WHERE group_id = ' . sqlite_escape_string($this->_ID);
                        $query .= 'AND student_id = ' . sqlite_escape_string($student) . ';';
                        if(!@sqlite_query($db, $query))
                        {
                            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; jour des &eacute;tudiants d'un groupe " . $this->_Name);
                        }
                    }
                    
                    $this->_StudentManager->Flush();
                    sqlite_close($db);
                }
                else
                {
                    throw new PWHIOException(PWHIOException::PWHEACCES);
                }
            }
            else
            {
                throw new  PWHQueryException(PWHQueryException::PWHEUPNOTREG);
            }
        }
        
        public function Delete()
        {
            if($this->IsPersistent())
            {
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    $children = self::GetChildrenOf($this->_ID);
                    foreach($children as $child)
                    {
                        $child->Delete();
                    }
                    
                    $query = 'DELETE FROM PWHGroup WHERE id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . "Suppression du groupe " . $this->_Name);
                    }
                    
                    $query = 'DELETE FROM PWHGroupTree WHERE child_id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . "Suppression du groupe " . $this->_Name);
                    }
                    
                    $query = 'DELETE FROM PWHStudentGroup WHERE group_id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . "Suppression des &eacute;tudiants du groupe " . $this->_Name);                      
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
        
        public function GetParentID()
        {
            return $this->_ParentID;
        }
        
        public function SetParentID($id)
        {
            return $this->_ParentID = $id;
        }
                
        public function GetName()
        {
            return $this->_Name;
        }
        
        public function SetName($name)
        {
            $this->_Name = $name;
        }
        
        public function AddStudents($students)
        {
            $this->_StudentManager->AddEntities($students);            
        }
        
        public function RemoveStudents($students)
        {
            $this->_StudentManager->RemoveEntities($students); 
        }   
        
        public function GetStudent($id)
        {
            $this->_StudentManager->GetEntity($id); 
        }
        
        public function GetStudents()
        {
            return $this->_StudentManager->GetEntities(); 
        }
        
        public function StudentExists($id)
        {
            return $this->_StudentManager->EntityExists($id); 
        }
        
        public function HasStudents()
        {
            return !$this->_StudentManager->IsEmpty(); 
        }
        
        public function GetDeliveries($recursive)
        {
            if($this->IsPersistent())
            {
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    if($result = @sqlite_query($db, 'SELECT delivery_id FROM PWHGroupDelivery WHERE group_id = ' . sqlite_escape_string($this->_ID) . ';'))
                    {
                        sqlite_close($db);                  
                        $deliveries = array();
                        while($tuple = sqlite_fetch_array($result))
                        {
                            $delivery = new PWHDelivery();
                            $delivery->Read((int)$tuple['delivery_id']);
                            array_push($deliveries, $delivery);
                        }
                        if($recursive)
                        {
                            $children = self::GetChildrenOf($this->_ID);
                            foreach($children as $child)
                            {
                                $deliveries = array_merge($deliveries, $child->GetDeliveries(true));
                            }
                        }
                        return $deliveries;
                    }
                    else
                    {
                        throw new Exception(PWHEQUERY . ': Liste des rendus');
                    }
                }
                else
                {
                    throw new Exception(PWHEACCES);
                }
            }
            else
            {
                throw new Exception(PWHENOTREG . ": Liste des rendus du groupe " . $this->_Name);
            }
        }
        
        public function HasDeliveries()
        {
            if($this->IsPersistent())
            {
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    if($result = @sqlite_query($db, 'SELECT COUNT(delivery_id) AS count FROM PWHGroupDelivery WHERE group_id  = ' . sqlite_escape_string($this->_ID) . ';'))
                    {
                        sqlite_close($db);                  
                        $result = sqlite_fetch_single($result);
                        return $result['count'] > 0;
                    }
                    else
                    {
                        throw new Exception(PWHEQUERY . ': Existence de rendus pour le groupe ' . $this->_Name);
                    }
                }
                else
                {
                    throw new Exception(PWHEACCES);
                } 
            }
            else
            {
                throw new Exception(PWHENOTREG . ": Existence de rendus pour le groupe " . $this->_Name);
            }
        }
        
        public function HasSubjects($recursive)
        {
            if($this->IsPersistent())
            {
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    if($result = @sqlite_query($db, 'SELECT COUNT(subject_id) AS count FROM PWHGroupSubject WHERE group_id  = ' . sqlite_escape_string($this->_ID) . ';'))
                    {
                        sqlite_close($db);                  
                        $result = sqlite_fetch_single($result);
                        $children = self::GetChildrenOf($this->_ID);
                        $childHas = false;
                        if($recursive)
                        {
                            foreach($children as $child)
                            {
                                if($child->HasSubjects(true))
                                {
                                    $childHas = true;
                                }
                            }
                        }
                        return $result['count'] > 0 || $childHas;
                    }
                    else
                    {
                        throw new Exception(PWHEQUERY . ': Existence de mati&egrave;res pour le groupe ' . $this->_Name);
                    }
                }
                else
                {
                    throw new Exception(PWHEACCES);
                } 
            }
            else
            {
                throw new Exception(PWHENOTREG . ": Existence de mati&egrave;res pour le groupe " . $this->_Name);
            }
        }
        public function GetSubjects($recursive)
        {
            if($this->IsPersistent())
            {
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    if($result = @sqlite_query($db, 'SELECT subject_id FROM PWHGroupSubject WHERE group_id = ' . sqlite_escape_string($this->_ID) . ';'))
                    {
                        sqlite_close($db);                  
                        $subjects = array();
                        while($tuple = sqlite_fetch_array($result))
                        {
                            $subject = new PWHSubject();
                            $subject->Read((int)$tuple['subject_id']);
                            array_push($subjects, $subject);
                        }
                        
                        if($recursive)
                        {
                            $children = self::GetChildrenOf($this->_ID);
                            foreach($children as $child)
                            {
                                $subjects = array_merge($subjects, $child->GetSubjects(true));
                            }
                        }
                        return array_unique($subjects);
                    }
                    else
                    {
                        throw new Exception(PWHEQUERY . ': Liste des mati&egrave;res');
                    }
                }
                else
                {
                    throw new Exception(PWHEACCES);
                }
            }
            else
            {
                throw new Exception(PWHENOTREG . ": Liste des mati&egrave;res du groupe " . $this->_Name);
            }
        }
        
        
        public function GetParents()
        {
            $parents = array();
            $id = $this->_ParentID;
            while($id != -1)
            {
                $parent = new PWHGroup();
                $parent->Read($id);
                array_push($parents, $parent);
                $id = $parent->GetParentID();
            }
            return $parents;
        }
        
        public function GetPromotion()
        {
            if($this->_ParentID == -1)
            {
                $group = new PWHGroup();
                $group->Read($this->_ID);
                return $group;    
            }
            else
            {
                $parent = new PWHGroup();
                $parent->Read($this->_ID);
                while($parent->GetParentID() != -1)
                {
                    $parent->Read($parent->GetParentID());
                }
                return $parent;
            } 
        }
        
        public function GetEmail()
        {
            $strbuf = "";
            $students = $this->_StudentManager->GetEntities();
            foreach($students as $student)
            {
                $strbuf .= $student->GetEmail() . ",";
            }
            $strbuf = substr($strbuf, 0, strlen($strbuf) - 1);
            return $strbuf;
        }
        
        public function IsChildUniqueName($name)
        {
            $unique = true;
            $groups = self::GetChildrenOf($this->GetID());
            foreach($groups as $group)
            {
                if($group->GetName() == $name)
                {
                    $unique = false;
                }
            }
            return $unique;
        }
        
        public static function GetChildrenOf($id)
        {
            if ($db = @sqlite_open(DATABASE_FILE())) 
            {
                if($result = @sqlite_query($db, 'SELECT * FROM PWHGroupTree WHERE parent_id = ' . sqlite_escape_string($id) . ';'))
                {
                    sqlite_close($db);                  
                    $groups = array();
                    while($tuple = sqlite_fetch_array($result))
                    {
                        $group = new PWHGroup();
                        $group->Read((int)$tuple['child_id']);
                        array_push($groups, $group);
                    }
                    usort($groups, "entity_comparator");
                    return $groups;
                }
                else
                {
                    throw new Exception(PWHEQUERY . ": Arborescence des groupes");
                }
            }
            else
            {
                throw new Exception(PWHEACCES);
            }
        }
        
        public static function GetPromotions()
        {
            if ($db = @sqlite_open(DATABASE_FILE())) 
            {
                if($result = @sqlite_query($db, 'SELECT * FROM PWHGroupTree WHERE parent_id = -1;'))
                {
                    sqlite_close($db);                  
                    $promos = array();
                    while($tuple = sqlite_fetch_array($result))
                    {
                        $promo = new PWHGroup();
                        $promo->Read((int)$tuple['child_id']);
                        array_push($promos, $promo);
                    }
                    usort($promos, "entity_comparator");
                    return $promos;
                }
                else
                {
                    throw new Exception(PWHEQUERY . ": Arborescence des groupes");
                }
            }
            else
            {
                throw new Exception(PWHEACCES);
            }
        }
        
        public static function GetFamily($id)
        {
            $family = array();
            $children = PWHGroup::GetChildrenOf($id);
            foreach($children as $child)
            {
                array_push($family, $child);
                $littleFamily = self::GetFamily($child->GetID());
                if(count($littleFamily) > 0)
                {
                    $family = array_merge($family, $littleFamily);
                }
            }
            if(count($family) > 0)
            {
                return $family;
            }
        }   
        
        public static function Debug()
        {
            if($db = @sqlite_open(DATABASE_FILE())) 
            {
                echo '<table><caption>PWHGroup</caption>';
                echo '<tr><th>id</th><th>name</th></tr>';
                $query = 'SELECT * FROM PWHGroup;';             
                if($result = sqlite_query($db, $query))
                {
                    while($entry = sqlite_fetch_array($result))
                    {
                        echo '<tr><td>'.$entry['id'].'</td><td>'.$entry['name'].'</td></tr>';
                    }
                }
                echo '</table>';
                
                echo '<table><caption>PWHGroupTree</caption>';
                echo '<tr><th>parent_id</th><th>child_id</th></tr>';
                $query = 'SELECT * FROM PWHGroupTree;';             
                if($result = sqlite_query($db, $query))
                {
                    while($entry = sqlite_fetch_array($result))
                    {
                        echo '<tr><td>'.$entry['parent_id'].'</td><td>'.$entry['child_id'].'</td></tr>';
                    }
                }
                echo '</table>';
                
                echo '<table><caption>PWH_StudentGroup</caption>';
                echo '<tr><th>group_id</th><th>student_id</tr>';
                $query = 'SELECT * FROM PWHStudentGroup;';             
                if($result = sqlite_query($db, $query))
                {
                    while($entry = sqlite_fetch_array($result))
                    {
                        echo '<tr><td>'.$entry['group_id'].'</td><td>'.$entry['student_id'].'</td></tr>';
                    }    
                }
                echo '</table>';

                sqlite_close($db);
            }
        }
    }
?>
