<?php
    class PWHDeliverygroup extends PWHEntity implements PWHWritable
    {
        private $_DeliveryID;
        private $_Super;
        private $_Creation;
        private $_LastDelivery;
        private $_OldName;
        private $_ExtraTimeUsed;
        private $_StudentManager;
        
        public function __construct()
        {
            parent::__construct();
            $this->_DeliveryID = -1;
            $this->_Super = false;
            $this->_Creation = "";
            $this->_LastDelivery = "";
            $this->_OldName = "";
            $this->_ExtraTimeUsed = false;
            $this->_StudentManager = new PWHEntityManager('PWHStudent');
        }
        
        public function __toString()
        {
            $strbuf = '{deliverygroup} ' . parent::__toString() . ' [deliveryid:' . $this->_DeliveryID . '] [students:';
            
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
                $super = (int)$this->_Super;
                $extraTimeUsed = (int)$this->_ExtraTimeUsed;
                
                $query = 'INSERT INTO ' . __CLASS__ . " VALUES(NULL, '" . $super . "', '" . $extraTimeUsed . "', '" . sqlite_escape_string($this->_Creation) . "', '" . sqlite_escape_string($this->_LastDelivery) . "');";             
                if(@sqlite_query($db, $query)
                    && $result = @sqlite_query($db, 'SELECT max(id) FROM ' . __CLASS__ . ';'))
                {
                    $result = sqlite_fetch_single($result);
                    if(!@sqlite_query($db, "INSERT INTO PWHDeliverygroupDelivery VALUES('" . sqlite_escape_string($this->_DeliveryID) . "', '" . $result . "');"))
                    {
                        $delivery = PWHEntity::NewInstance('PWHDelivery');
                        $delivery->Read($_DeliveryID);
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Ajout d'un group de rendu pour le rendu " . $delivery->GetName());
                    }
                    
                    $students = $this->_StudentManager->GetEntities();
                    foreach($students as $student)
                    {
                        if(!@sqlite_query($db, "INSERT INTO PWHStudentDeliverygroup VALUES('" . $result . "', '" . $student->GetID() . "');"))
                        {
                            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Ajout de l'&eacute;tudiant " . $student->GetLogin() . " dans un groupe de rendu");
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
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Cr&eacute;tion d'un groupe de rendu");
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
                $query = 'SELECT * FROM PWHDeliverygroup WHERE id = '. sqlite_escape_string($id) . ';';             
                if($result = @sqlite_query($db, $query))
                {
                    $entry = sqlite_fetch_array($result);
                    $this->_ID = $id;
                    $this->_Super = (boolean)$entry['super'];
                    $this->_ExtraTimeUsed = (boolean)$entry['extra_time_used'];
                    $this->_Creation = $entry['creation'];
                    $this->_LastDelivery = $entry['last_delivery'];
                }
                else
                {
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Lecture d'un groupe de rendu");
                }
                            
                $query = 'SELECT * FROM PWHDeliverygroupDelivery WHERE deliverygroup_id = '. sqlite_escape_string($id) . ';';             
                if($result = @sqlite_query($db, $query))
                {
                    $entry = sqlite_fetch_array($result);
                    $this->_DeliveryID = $entry['delivery_id'];
                }
                else
                {
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Lecture du rendu d'un groupe de rendu");
                }
                
                $query = 'SELECT * FROM PWHStudentDeliverygroup WHERE deliverygroup_id = '. sqlite_escape_string($id) . ';';             
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
                
        public function Update()
        {
            if($this->IsPersistent())
            {
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    $super = (int)$this->_Super;
                    $extraTimeUsed = (int)$this->_ExtraTimeUsed;
                    $query = "UPDATE PWHDeliverygroup SET super = '" . $super . "', extra_time_used = '" . $extraTimeUsed . "', creation = '" . sqlite_escape_string($this->_Creation) . "', last_delivery = '" . sqlite_escape_string($this->_LastDelivery) . "' WHERE id = " . sqlite_escape_string($this->_ID) . ';';
                    if(!@sqlite_query($db, $query, 0666, $err))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; d'un groupe de rendu  ##" . $err);
                    }
                                  
                    $query = "UPDATE PWHDeliverygroupDelivery SET delivery_id = '" . sqlite_escape_string($this->_DeliveryID) . "' WHERE deliverygroup_id = " . sqlite_escape_string($this->_ID) . ';';
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; jour du rendu d'un groupe de rendu");
                    }
                    
                    $studentsAdded = $this->_StudentManager->GetEntitiesAdded();               
                    foreach($studentsAdded as $student)
                    {
                        $query = "INSERT INTO PWHStudentDeliverygroup VALUES ('" . $this->_ID . "', '" . $student . "');";
                        if(!@sqlite_query($db, $query))
                        {
                            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; jour des &eacute;tudiants d'un groupe de rendu");
                        }
                    }
                    
                    $studentsRemoved = $this->_StudentManager->GetEntitiesRemoved();                
                    foreach($studentsRemoved as $student)
                    {
                        $query = 'DELETE FROM PWHStudentDeliverygroup WHERE deliverygroup_id = ' . sqlite_escape_string($this->_ID);
                        $query .= 'AND student_id = ' . sqlite_escape_string($student) . ';';
                        if(!@sqlite_query($db, $query))
                        {
                            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; jour des &eacute;tudiants d'un groupe de rendu");
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
                throw new PWHQueryException(PWHQueryException::PWHEUPNOTREG);
            }
        }
        
        public function Delete()
        {
            if($this->IsPersistent())
            {
                $this->RemoveDirectory();
                
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    $query = 'DELETE FROM ' . __CLASS__ . ' WHERE id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . "Suppression d'un groupe de rendu");
                    }
                    
                    $query = 'DELETE FROM PWHDeliverygroupDelivery WHERE deliverygroup_id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Suppression du rendu d'un groupe de rendu");
                    }
                    
                    $query = 'DELETE FROM PWHStudentDeliverygroup WHERE deliverygroup_id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . "Suppression des &eacute;tudiants d'un groupe de rendu");                      
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
                        
        public function GetDeliveryID()
        {
            return $this->_DeliveryID;
        }
        
        public function SetDeliveryID($deliveryID)
        {
            $this->_DeliveryID = $deliveryID;
        }
        
        public function IsSuper()
        {
            return $this->_Super;
        }
        
        public function SetSuper($tf)
        {
            $this->_Super = $tf;
        }
        
        public function IsExtraTimeUsed()
        {
            return $this->_ExtraTimeUsed;
        }
        
        public function SetExtraTimeUsed($tf)
        {
            $this->_ExtraTimeUsed = $tf;
        }
        
        public function GetCreation()
        {
            return $this->_Creation;
        }
        
        public function SetCreation($creation)
        {
            $this->_Creation = $creation;
        }
        
        public function GetLastDelivery()
        {
            return $this->_LastDelivery;
        }
        
        public function SetLastDelivery($lastDelivery)
        {
            $this->_LastDelivery = $lastDelivery;
        }
                
        public function AddStudents($students)
        {
            $this->_OldName = $this->GetName();
            $this->_StudentManager->AddEntities($students);
            if($this->IsPersistent())
            {
                $this->RenameDirectory();
            }
        }
        
        public function RemoveStudents($students)
        {
            $this->_OldName = $this->GetName();
            $this->_StudentManager->RemoveEntities($students);
            if($this->IsPersistent())
            {
                $this->RenameDirectory();
            }
        }   
        
        public function GetStudent($id)
        {
            $this->_StudentManager->GetEntity($id); 
        }
        
        public function GetStudents()
        {
            $students = $this->_StudentManager->GetEntities();
            return $students; 
        }
        
        public function StudentExists($id)
        {
            return $this->_StudentManager->EntityExists($id); 
        }
        
        public function HasStudents()
        {
            return !$this->_StudentManager->IsEmpty(); 
        }
        
        public function CountStudents()
        {
            return $this->_StudentManager->Size();
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
        
        public function GetName()
        {
            $strbuf = "";
            $students = $this->_StudentManager->GetEntities();
            if(count($students) == 0)
            {
                return "deliverygroup_" . $this->GetID();
            }
            
            foreach($students as $student)
            {
                $strbuf .= $student->GetLogin() . "-";
            }
            $strbuf = substr($strbuf, 0, strlen($strbuf) - 1);
            return $strbuf;
        }
        
        public function GetPath()
        {
            $delivery = new PWHDelivery();
            $delivery->Read($this->_DeliveryID);
            return $delivery->GetPath() . str_replace(" ", "_", $this->GetName()) . '/';
        }
        
        public function CreateDirectory()
        {
            if($this->IsPersistent())
            {
                if(!file_exists($this->GetPath()))
                {
                    mkdir($this->GetPath());
                }
                else
                {
                    throw new  PWHIOException(PWHIOException::PWHEFILEEXISTS . " : R&eacute;pertoire du groupe de rendu " . $this->GetName());
                }
            }
            else
            {
                throw new PWHQueryException(PWHQueryException::PWHENOTREG);
            }
        }
        
        public function RemoveDirectory()
        {
            if($this->IsPersistent())
            {
                if(file_exists($this->GetPath()))
                {
                    exec("rm -rf " . $this->GetPath());
                }
                else
                {
                    throw new  PWHIOException(PWHIOException::PWHEFILENOTEXISTS . " : R&eacute;pertoire du groupe de rendu " . $this->_Name);
                }
            }
            else
            {
                throw new PWHQueryException(PWHQueryException::PWHENOTREG);
            }
        }
        
        public function RenameDirectory()
        {
            if($this->IsPersistent())
            {            
                $delivery = new PWHDelivery();
                $delivery->Read($this->_DeliveryID);
                $oldDeliverygroupDirectory = $delivery->GetPath() . str_replace(" ", "_", $this->_OldName) . '/';
                if(file_exists($oldDeliverygroupDirectory))
                {
                    rename($oldDeliverygroupDirectory, $this->GetPath());
                }
                else
                {
                    throw new PWHIOException(PWHIOException::PWHEFILENOTEXISTS . " : R&eacute;pertoire du travail " . $this->_Name);
                }
            }
            else
            {
                throw new PWHQueryException(PWHQueryException::PWHENOTREG);
            }
        }

        public static function debug()
        {
            if($db = @sqlite_open(DATABASE_FILE())) 
            {
                echo '<table><caption>' . __CLASS__ . '</caption>';
                echo '<tr><th>id</th><th>super</th><th>extra_time_used</th><th>creation</th><th>last_delivery</th></tr>';
                $query = 'SELECT * FROM ' . __CLASS__ . ';';             
                if($result = sqlite_query($db, $query))
                {
                    while($entry = sqlite_fetch_array($result))
                    {
                        echo '<tr><td>'.$entry['id'].'</td><td>'.$entry['super'].'</td><td>'.$entry['extra_time_used'].'</td><td>'.$entry['creation'].'</td><td>'.$entry['last_delivery'].'</td></tr>';
                    }
                }
                echo '</table>';
                
                echo '<table><caption>PWHDeliverygroupDelivery</caption>';
                echo '<tr><th>delivery_id</th><th>deliverygroup_id</th></tr>';
                $query = 'SELECT * FROM PWHDeliverygroupDelivery;';             
                if($result = sqlite_query($db, $query))
                {
                    while($entry = sqlite_fetch_array($result))
                    {
                        echo '<tr><td>'.$entry['delivery_id'].'</td><td>'.$entry['deliverygroup_id'].'</td></tr>';
                    }    
                }
                echo '</table>';
                
                echo '<table><caption>PWHStudentDeliverygroup</caption>';
                echo '<tr><th>deliverygroup_id</th><th>student_id</tr>';
                $query = 'SELECT * FROM PWHStudentDeliverygroup;';             
                if($result = sqlite_query($db, $query))
                {
                    while($entry = sqlite_fetch_array($result))
                    {
                        echo '<tr><td>'.$entry['deliverygroup_id'].'</td><td>'.$entry['student_id'].'</td></tr>';
                    }    
                }
                echo '</table>';

                sqlite_close($db);
            }
        }
    }
?>
