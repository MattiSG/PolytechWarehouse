<?php
    class PWHDelivery extends PWHEntity implements PWHWritable
    {
        private $_WorkID;
        private $_OwnerID;
        private $_Name;
        private $_OldName;
        private $_GroupCompostionDeadline;
        private $_Deadline;
        private $_GroupManager;
        
        public function __construct()
        {
            parent::__construct();
            $this->_WorkID = -1;
            $this->_OwnerID = -1;
            $this->_Name = "";
            $this->_GroupCompositionDeadline = "";
            $this->_Deadline = "";
            $this->_GroupManager = new PWHEntityManager('PWHGroup');
        }
        
        public function __toString()
        {
            $strbuf = '{delivery} ' . parent::__toString() . ' [workid:' .$this->_WorkID . '] [ownerid:' . $this->_OwnerID . '] [name:' . $this->_Name . ']';
            $strbuf .= '[groupcompositiondeadline:'  . $this->_GroupCompositionDeadline . '] [deadline:' .$this->_Deadline . '] ';
            $strbuf .= '[groups:';
            
            $groups = $this->_GroupManager->GetEntities();
            foreach($groups as $group)
            {
                $strbuf .= $group->GetID() . " ";
            }
            return $strbuf . ']';
        }
                
        public function Create($overwrite)
        {
            if($this->_WorkID > 0 && $this->_OwnerID > 0)
            {
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    $query = 'INSERT INTO ' . __CLASS__ . " VALUES(NULL, '" . sqlite_escape_string($this->_Name) . "','" . sqlite_escape_string($this->_GroupCompositionDeadline) . "','" . sqlite_escape_string($this->_Deadline) . "');";
                    if(@sqlite_query($db, $query)
                        && $result = @sqlite_query($db, 'SELECT max(id) FROM ' . __CLASS__ . ';'))
                    {
                        $result = sqlite_fetch_single($result);                       
                        if(!@sqlite_query($db, "INSERT INTO PWHDeliveryTeacher VALUES('" . sqlite_escape_string($this->_OwnerID) . "', '" . $result . "');"))
                        {
                            $teacher = PWHEntity::NewInstance('PWHTeacher');
                            $teacher->Read($_OwnerID);
                            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Ajout de l'enseignant " . $teacher->GetLogin() . " dans le rendu " . $this->_Name);
                        }
                        
                        $groups = $this->_GroupManager->GetEntities();          
                        foreach($groups as $group)
                        {
                            if(!@sqlite_query($db, "INSERT INTO PWHGroupDelivery VALUES('" . $result . "', '" . $group->GetID() . "');"))
                            {
                                throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Ajout d'un rendu " . $this->_Name . " dans le groupe " . $group->GetName());
                            }
                        }
                        $this->_GroupManager->Flush();                       
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
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Cr&eacute;tion du rendu " . $this->_Name);
                    }
                }
                else
                {
                    throw new PWHIOException(PWHIOException::PWHEACCES);
                }
            }
            else
            {
                throw new PWHQueryException(PWHQueryException::PWHENOOWNER . ": Cr&eacute;ation du rendu" . $this->_Name);
            }
        }
        
        public function Read($id)
        {
            if($db = @sqlite_open(DATABASE_FILE())) 
            {
                $query = 'SELECT * FROM ' . __CLASS__;
                $query .= ' WHERE id = '. sqlite_escape_string($id) . ';';             
                if($result = @sqlite_query($db, $query))
                {
                    $entry = sqlite_fetch_array($result);
                    $this->_ID = $id;
                    $this->_Name = $entry['name'];
                    $this->_OldName = $entry['name'];
                    $this->_GroupCompositionDeadline = $entry['group_comp_deadline'];
                    $this->_Deadline = $entry['deadline'];
                }
                else
                {
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Lecture d'un rendu");
                }
                
                $query = 'SELECT * FROM PWHGroupDelivery WHERE delivery_id = '. sqlite_escape_string($id) . ';';             
                if($result = @sqlite_query($db, $query))
                {
                    $this->_GroupManager->Clear();
                    $groups = array();
                    while($entry = sqlite_fetch_array($result))
                    {
                        array_push($groups, (int)$entry['group_id']);
                    }
                    $this->_GroupManager->AddEntities($groups);
                    $this->_GroupManager->Flush();   
                }
                else
                {
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Lecture des groupes du rendu " . $this->_Name);
                }
                
                $query = 'SELECT * FROM PWHDeliveryTeacher WHERE delivery_id = '. sqlite_escape_string($id) . ';';             
                if($result = @sqlite_query($db, $query))
                {
                    $entry = sqlite_fetch_array($result);
                    $this->_OwnerID = $entry['teacher_id'];     
                }
                else
                {
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Lecture du responsable du rendu " . $this->_Name);
                }
                
                $query = 'SELECT * FROM PWHDeliveryWork WHERE delivery_id = '. sqlite_escape_string($id) . ';';             
                if($result = @sqlite_query($db, $query))
                {
                    $entry = sqlite_fetch_array($result);
                    $this->_WorkID = $entry['work_id'];
                }
                else
                {
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Lecture du travail du rendu " . $this->_Name);
                }             
                
                sqlite_close($db);
            }
            else
            {
                throw new  PWHIOException(PWHIOException::PWHEACCES);
            }    
        }
        
        public function Update()
        {
            if($this->IsPersistent())
            {
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    $query = 'UPDATE ' . __CLASS__ . " SET name = '" . sqlite_escape_string($this->_Name);
                    $query .= "',group_comp_deadline = '" . sqlite_escape_string($this->_GroupCompositionDeadline);
                    $query .= "',deadline = '" . sqlite_escape_string($this->_Deadline);
                    $query .= "' WHERE id = ". sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query, 0666, $error))
                    {                       
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; jour du rendu " . $this->_Name . "[".$error."]");
                    }
                    
                    $query = "UPDATE PWHDeliveryTeacher SET teacher_id = '" . sqlite_escape_string($this->_OwnerID) . "' WHERE delivery_id = " . sqlite_escape_string($this->_ID) . ';';
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; jour du responsable du rendu " . $this->_Name);
                    }
                    
                    $query = "UPDATE PWHDeliveryWork SET work_id = '" . sqlite_escape_string($this->_WorkID) . "' WHERE delivery_id = " . sqlite_escape_string($this->_ID) . ';';
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; jour du travail du rendu " . $this->_Name);
                    }
                    
                    $groupsAdded = $this->_GroupManager->GetEntitiesAdded();                    
                    foreach($groupsAdded as $group)
                    {
                        $query = "INSERT INTO PWHGroupDelivery VALUES ('" . $this->_ID . "', '" . $group . "');";
                        if(!@sqlite_query($db, $query))
                        {
                            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; jour des groupes du rendu " . $this->_Name);
                        }
                    }
                    
                    $groupsRemoved = $this->_GroupManager->GetEntitiesRemoved();               
                    foreach($groupsRemoved as $group)
                    {
                        $query = 'DELETE FROM PWHGroupDelivery WHERE delivery_id = ' . sqlite_escape_string($this->_ID);
                        $query .= 'AND group_id = ' . sqlite_escape_string($group) . ';';
                        if(!@sqlite_query($db, $query))
                        {
                            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; jour des groupes du rendu " . $this->_Name);
                        }
                    }  
                    $this->_GroupManager->Flush();
                    sqlite_close($db);
                }
                else
                {
                    throw new  PWHIOException(PWHIOException::PWHEACCES);
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
                $deliverygroups = $this->GetDeliverygroups();
                foreach($deliverygroups as $deliverygroup)
                {
                    $deliverygroup->Delete();
                }
                
                $this->RemoveDirectory();
                
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    $query = 'DELETE FROM ' . __CLASS__ . ' WHERE id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Suppression du rendu " . $this->_Name);
                    }
                    
                    $query = 'DELETE FROM PWHDeliveryTeacher WHERE delivery_id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Suppression du rendu " . $this->_Name);
                    }
                    
                    $query = 'DELETE FROM PWHDeliveryWork WHERE delivery_id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Suppression du rendu " . $this->_Name);
                    }
                    
                    $query = 'DELETE FROM PWHGroupDelivery WHERE delivery_id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Suppression du rendu " . $this->_Name);
                    }
                    
                    $query = 'SELECT * FROM PWHDeliverygroupDelivery WHERE delivery_id = '. sqlite_escape_string($this->_ID) . ';';             
                    if($result = @sqlite_query($db, $query))
                    {
                        while($entry = sqlite_fetch_array($result))
                        {
                            $deliverygroup = PWHEntity::NewInstance('PWHDeliverygroup');
                            $deliverygroup->Read($entry['deliverygroup_id']);
                            $deliverygroup->Delete();
                        }
                    }
                    else
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Suppression du rendu " . $this->_Name);
                    }
                    sqlite_close($db);
                }
                else
                {
                    throw new  PWHIOException(PWHIOException::PWHEACCES);
                }
            }
            else
            {
                throw new PWHQueryException(PWHQueryException::PWHEDELNOTREG);
            }
        }
                
        public function GetWorkID()
        {
            return $this->_WorkID;
        }
        
        public function SetWorkID($id)
        {
            $this->_WorkID = $id;
        }
        
        public function GetOwnerID()
        {
            return $this->_OwnerID;
        }
        
        public function SetOwnerID($id)
        {
            $this->_OwnerID = $id;
        }
        
        public function GetName()
        {
            return $this->_Name;
        }
        
        public function SetName($name)
        {
            $this->OldName = $this->_Name;
            $this->_Name = $name;
        }
        
        public function GetGroupCompositionDeadline()
        {
            return $this->_GroupCompositionDeadline;
        }
        
        public function SetGroupCompositionDeadline($deadline)
        {
            $this->_GroupCompositionDeadline = $deadline;
        }
        
        public function GetDeadline()
        {
            return $this->_Deadline;
        }
        
        public function SetDeadline($deadline)
        {
            $this->_Deadline = $deadline;
        }
                
        public function AddGroups($groups)
        {
            $this->_GroupManager->AddEntities($groups);  
        }
        
        public function RemoveGroups($groups)
        {
            $this->_GroupManager->RemoveEntities($groups);
        }   
        
        public function GetGroup($id)
        {
            return $this->_GroupManager->GetEntity($id);
        }
        
        public function GetGroups()
        {
            return $this->_GroupManager->GetEntities();
        }
        
        public function GroupExists($id)
        {
            $this->_GroupManager->EntityExists($id);
        }
        
        public function HasGroups()
        {
            return !$this->_GroupManager->IsEmpty();
        }
        
        public function GetFreeStudents()
        {
            $freeStudents = array();
            $groups = $this->_GroupManager->GetEntities();
            foreach($groups as $group)
            {
                $students = $group->GetStudents();
                foreach($students as $student)
                {
                    if(!$student->HasDeliverygroup($this->_ID))
                    {
                        array_push($freeStudents, $student);
                    }
                }
            }
            return $freeStudents;
        }
        
        public function GetDeliverygroups()
        {
            if ($db = @sqlite_open(DATABASE_FILE())) 
            {
                if($result = @sqlite_query($db, 'SELECT * FROM PWHDeliverygroupDelivery WHERE delivery_id = ' . sqlite_escape_string($this->_ID) . ';'))
                {
                    sqlite_close($db);                  
                    $groups = array();
                    while($tuple = sqlite_fetch_array($result))
                    {
                        $group = new PWHDeliverygroup();
                        $group->Read((int)$tuple['deliverygroup_id']);
                        array_push($groups, $group);
                    }
                    return $groups;
                }
                else
                {
                    throw new Exception(PWHEQUERY . ": Liste des groupes de rendu du rendu " . $this->GetName());
                }
            }
            else
            {
                throw new Exception(PWHEACCES);
            }
        }
                
        public function IsConfigured()
        {
            $work = new PWHWork();
            $work->Read($this->_WorkID);
            if($work->IsSimple() || $work->GetGroupMax() == 1)
            {
                return $this->_Deadline != "";
            }
            else
            {
                return $this->_GroupCompositionDeadline != "" && $this->_Deadline != "";
            }
        }
        
        public function GetPath()
        {
            $work = PWHEntity::NewInstance('PWHWork');
            $work->Read($this->_WorkID);
            return $work->GetPath() . str_replace(" ", "_", $this->_Name) . '/';
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
                    throw new  PWHIOException(PWHIOException::PWHEFILEEXISTS . " : R&eacute;pertoire du rendu " . $this->_Name);
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
                    rmdir($this->GetPath());
                }
                else
                {
                    throw new  PWHIOException(PWHIOException::PWHEFILENOTEXISTS . " : R&eacute;pertoire du rendu " . $this->_Name);
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
                $work = PWHEntity::NewInstance('PWHWork');
                $work->Read($this->_WorkID);
                $oldDeliveryDirectory = $work->GetPath() . str_replace(" ", "_", $this->_OldName) . '/';
                if(file_exists($oldSubjectDirectory))
                {
                    rename($oldDeliveryDirectory, $this->GetPath());
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
        
        public function IsStillTimeForGroupComposition($timestamp)
        {
            $currentDate = substr($timestamp, 0, 10);
            $currentTime = substr($timestamp, 11, strlen($timestamp));
            
            $currentDate = explode('-', $currentDate);
            $currentTime = explode(':', $currentTime);
            
            $date = substr($this->_GroupCompositionDeadline, 0, 10);
            $time = substr($this->_GroupCompositionDeadline, 11, strlen($this->_GroupCompositionDeadline));
            
            $date = explode('-', $date);
            $time = explode(':', $time);
            
            $diff = mktime($time[0], $time[1], 0, $date[1], $date[2], $date[0]) - mktime($currentTime[0], $currentTime[1], $currentTime[2], $currentDate[1], $currentDate[2], $currentDate[0]);
            
            return $diff > 0;
        }
        
        public function IsStillTimeForDelivery($timestamp)
        {
            $work = new PWHWork();
            $work->Read($this->_WorkID);
            
            $currentDate = substr($timestamp, 0, 10);
            $currentTime = substr($timestamp, 11, strlen($timestamp));
            
            $currentDate = explode('-', $currentDate);
            $currentTime = explode(':', $currentTime);
            
            $date = substr($this->_Deadline, 0, 10);
            $time = substr($this->_Deadline, 11, strlen($this->_Deadline));
            
            $date = explode('-', $date);
            $time = explode(':', $time);
            
            $diff = mktime($time[0], $time[1], 0, $date[1], $date[2], $date[0]) + $work->GetExtraTime() * 86400 - mktime($currentTime[0], $currentTime[1], $currentTime[2], $currentDate[1], $currentDate[2], $currentDate[0]);
            
            return $diff > 0;
        }
        
        public function IsExtraTimeUsed($timestamp)
        {
            $work = new PWHWork();
            $work->Read($this->_WorkID);
            
            $currentDate = substr($timestamp, 0, 10);
            $currentTime = substr($timestamp, 11, strlen($timestamp));
            
            $currentDate = explode('-', $currentDate);
            $currentTime = explode(':', $currentTime);
            
            $date = substr($this->_Deadline, 0, 10);
            $time = substr($this->_Deadline, 11, strlen($this->_Deadline));
            
            $date = explode('-', $date);
            $time = explode(':', $time);
            
            $diff = mktime($time[0], $time[1], 0, $date[1], $date[2], $date[0]) - mktime($currentTime[0], $currentTime[1], $currentTime[2], $currentDate[1], $currentDate[2], $currentDate[0]);
            
            return $work->GetExtraTime() > 0 && $diff < 0 && $this->IsStillTimeForDelivery($timestamp);
        }
        
        public static function debug()
        {
            if($db = @sqlite_open(DATABASE_FILE())) 
            {
                echo '<table><caption>' . __CLASS__ . '</caption>';
                echo '<tr><th>id</th><th>name</th><th>group_comp_deadline</th><th>deadline</th><th>published</th></tr>';
                $query = 'SELECT * FROM ' . __CLASS__ . ';';             
                if($result = sqlite_query($db, $query))
                {
                    while($entry = sqlite_fetch_array($result))
                    {
                        echo '<tr><td>'.$entry['id'].'</td><td>'.$entry['name'].'</td><td>'.$entry['group_comp_deadline'].'</td><td>'.$entry['deadline'].'</td><td>'.$entry['published'].'</td></tr>';
                    }
                }
                echo '</table>';
                
                echo '<table><caption>PWHDeliveryTeacher</caption>';
                echo '<tr><th>teacher_id</th><th>delivery_id</th></tr>';
                $query = 'SELECT * FROM PWHDeliveryTeacher;';             
                if($result = sqlite_query($db, $query))
                {
                    while($entry = sqlite_fetch_array($result))
                    {
                        echo '<tr><td>'.$entry['teacher_id'].'</td><td>'.$entry['delivery_id'].'</td></tr>';
                    }    
                }
                echo '</table>';
                
                echo '<table><caption>PWHGroupDelivery</caption>';
                echo '<tr><th>delivery_id</th><th>group_id</th></tr>';
                $query = 'SELECT * FROM PWHGroupDelivery;';             
                if($result = sqlite_query($db, $query))
                {
                    while($entry = sqlite_fetch_array($result))
                    {
                        echo '<tr><td>'.$entry['delivery_id'].'</td><td>'.$entry['group_id'].'</td></tr>';
                    }   
                }
                echo '</table>';
       
                sqlite_close($db);
            }
        }    
    }
?>
