<?php
    class PWHWork extends PWHEntity implements PWHWritable
    {
        private $_OwnerID;
        private $_SubjectID;
        private $_Name;
        private $_OldName;
        private $_ExtraTime;
        private $_Size;
        private $_Files;
        private $_GroupMin;
        private $_GroupMax;
        private $_Simple;
        private $_Published;
        private $_Link;
        private $_Level;
        private $_DeliveryManager;
        
        public function __construct()
        {
            parent::__construct();
            $this->_OwnerID = -1;
            $this->_SubjectID = -1;
            $this->_Name = "";
            $this->_ExtraTime = "";
            $this->_Size = "";
            $this->_Files = array();
            $this->_GroupMin = "";
            $this->_GroupMax = "";
            $this->_Link = "";
            $this->_Level = 0;
            $this->_Simple = false;
            $this->_Published = false;
            $this->_DeliveryManager = new PWHEntityManager('PWHDelivery');     
        }
        
        public function __toString()
        {
            $strbuf = '{work} ' . parent::__toString() . ' [subjectid:' . $this->_SubjectID . '] [ownerid:' . $this->_OwnerID . '] [name:' . $this->_Name . '] ';
            $strbuf .= '[extratime:' . $this->_ExtraTime . '] [size:' . $this->_Size . ']';
            $strbuf .= '[groupmin:' . $this->_GroupMin . '] [groupmax:' . $this->_GroupMax . '] ';
            $strbuf .= '[link: ' . $this->_Link . '] ';
            $strbuf .= '[level: ' . $this->_Level . '] ';
            $strbuf .= '[simple: ' . $this->_Simple . '] ';
            $strbuf .= '[published: ' . $this->_Published . '] ';
            
            $strbuf .= '[files:';
            foreach($this->_Files as $name=>$format)
            {
                $strbuf .= "{" . $name . ", " . $format . "} ";
            }
            
            $strbuf .= '] [deliveries:';
            $deliveries = $this->_DeliveryManager->GetEntities();
            foreach($deliveries as $delivery)
            {
                $strbuf .= $delivery->GetID() . " ";
            }
            return $strbuf . ']';
        }
        
        public function Create($overwrite)
        {
            if($this->_OwnerID > 0 && $this->_SubjectID > 0)
            {
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    $simple = (int)$this->_Simple;
                    $published = (int)$this->_Published;
                     
                    $query = 'INSERT INTO ' . __CLASS__ . " VALUES(NULL, '" . sqlite_escape_string($this->_Name) . "', '" . sqlite_escape_string($this->_ExtraTime);
                    $query .= "', '" . sqlite_escape_string($this->_Size) . "', '" . sqlite_escape_string($this->_GroupMin) . "', '" . sqlite_escape_string($this->_GroupMax) . "', '" . sqlite_escape_string($this->_Link) . "', '" . sqlite_escape_string($this->_Level) . "', '" . $simple . "', '" . $published . "');";
                    if(@sqlite_query($db, $query, 0666, $err)
                        && $result = @sqlite_query($db, 'SELECT max(id) FROM ' . __CLASS__, 0666, $err))
                    {               
                        $result = sqlite_fetch_single($result);
                        if(!@sqlite_query($db, "INSERT INTO PWHWorkTeacher VALUES('" . $this->_OwnerID . "', '" . $result . "');"))
                        {
                            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Ajout d'un responsable pour le travail " . $this->_Name);
                        }
                        
                        foreach($this->_Files as $name=>$format)
                        {
                            if(!@sqlite_query($db, "INSERT INTO PWHWorkFiles VALUES('" . $result . "', '" . $name . "', '" . $format . "');"))
                            {
                                throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Ajout de fichiers pour le travail " . $this->_Name);
                            }
                        }
                        
                        $deliveries = $this->_DeliveryManager->GetEntities();
                        foreach($deliveries as $delivery)
                        {
                            if(!@sqlite_query($db, "INSERT INTO PWHDeliveryWork VALUES('" . $result . "', '" . $delivery->GetID() . "');"))
                            {
                                throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Ajout d'une rendu dans le travail " . $this->_Name);
                            }
                        }
                        $this->_DeliveryManager->Flush(); 
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
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Cr&eacute;ation du travail " . $this->_Name . '[' . $err . ']');
                    }
                }
                else
                {
                    throw new PWHIOException(PWHIOException::PWHEACCES);
                }
            }
            else
            {
                throw new PWHQueryException(PWHQueryException::PWHENOOWNER . ": Cr&eacute;ation du travail " . $this->_Name);
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
                    $this->_ExtraTime = $entry['extra_time'];
                    $this->_Size = $entry['size'];
                    $this->_GroupMin = $entry['group_min'];
                    $this->_GroupMax = $entry['group_max'];
                    $this->_Link = $entry['link'];
                    $this->_Level = $entry['level'];
                    $this->_Simple = (boolean)$entry['simple'];
                    $this->_Published = (boolean)$entry['published'];
                }
                else
                {
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Lecture d'un travail");
                }
                
                $query = 'SELECT * FROM PWHDeliveryWork WHERE work_id = '. sqlite_escape_string($id) . ';';             
                if($result = @sqlite_query($db, $query))
                {
                    $this->_DeliveryManager->Clear();
                    $deliveries = array();
                    while($entry = sqlite_fetch_array($result))
                    {
                        array_push($deliveries, (int)$entry['delivery_id']);
                    }
                    $this->_DeliveryManager->AddEntities($deliveries);
                    $this->_DeliveryManager->Flush();   
                }
                else
                {
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Lecture des rendus du travail " . $this->_Name);
                }
                
                $query = 'SELECT * FROM PWHWorkTeacher WHERE work_id = '. sqlite_escape_string($id) . ';';             
                if($result = @sqlite_query($db, $query))
                {
                    $entry = sqlite_fetch_array($result);
                    $this->_OwnerID = $entry['teacher_id'];       
                }
                else
                {
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Lecture du responsable du travail " . $this->_Name);
                }
                
                $this->_Files = array();
                $query = 'SELECT * FROM PWHWorkFiles WHERE work_id = '. sqlite_escape_string($id) . ';';             
                if($result = @sqlite_query($db, $query))
                {
                    while($entry = sqlite_fetch_array($result))
                    {
                        $this->_Files[$entry['name']] = $entry['format'];
                    }       
                }
                else
                {
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Lecture des fichiers du travail " . $this->_Name);
                }
                
                $query = 'SELECT * FROM PWHWorkSubject WHERE work_id = '. sqlite_escape_string($id) . ';';             
                if($result = @sqlite_query($db, $query))
                {
                    $entry = sqlite_fetch_array($result);
                    $this->_SubjectID = $entry['subject_id'];             
                }
                else
                {
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Lecture de la mati&egrave;re du travail " . $this->_Name);
                }             
                sqlite_close($db);
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
                    $simple = (int)$this->_Simple;
                    $published = (int)$this->_Published;
                    
                    $query = 'UPDATE ' . __CLASS__ . " SET name = '" . sqlite_escape_string($this->_Name);
                    $query .= "',extra_time = '" . sqlite_escape_string($this->_ExtraTime);
                    $query .= "',size = '" . sqlite_escape_string($this->_Size);
                    $query .= "',group_min = '" . sqlite_escape_string($this->_GroupMin);
                    $query .= "',group_max = '" . sqlite_escape_string($this->_GroupMax);
                    $query .= "',link = '" . sqlite_escape_string($this->_Link);
                    $query .= "',level = '" . sqlite_escape_string($this->_Level);
                    $query .= "',simple = '" . sqlite_escape_string($simple);
                    $query .= "',published = '" . sqlite_escape_string($published);
                    $query .= "' WHERE id = ". sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {                       
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; jour du travail " . $this->_Name);
                    }
                    
                    $query = "UPDATE PWHWorkTeacher SET teacher_id = '" . sqlite_escape_string($this->_OwnerID) . "' WHERE work_id = " . sqlite_escape_string($this->_ID) . ';';
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; jour du responsable du travail " . $this->_Name);
                    }
                    
                    $query = 'DELETE FROM PWHWorkFiles WHERE work_id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mis &agrave; jour des fichiers du travail " . $this->_Name);
                    }
                    
                    $query = 'DELETE FROM PWHWorkFiles WHERE work_id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mis &agrave; jour des fichiers du travail " . $this->_Name);
                    }
                    
                    foreach($this->_Files as $name=>$format)
                    {
                        if(!@sqlite_query($db, "INSERT INTO PWHWorkFiles VALUES('" . $this->_ID . "', '" . $name . "', '" . $format . "');"))
                        {
                            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Ajout de fichiers pour le travail " . $this->_Name);
                        }
                    }
                    
                    $deliveriesAdded = $this->_DeliveryManager->GetEntitiesAdded();
                    foreach($deliveriesAdded as $delivery)
                    {
                        $query = "INSERT INTO PWHDeliveryWork VALUES ('" . $this->_ID . "', '" . $delivery . "');";
                        if(!@sqlite_query($db, $query))
                        {
                            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; jour des rendus du travail " . $this->_Name);
                        }
                    }
                    
                    $deliveriesRemoved = $this->_DeliveryManager->GetEntitiesRemoved();                
                    foreach($deliveriesRemoved as $delivery)
                    {
                        $query = 'DELETE FROM PWHDeliveryWork WHERE work_id = ' . sqlite_escape_string($this->_ID);
                        $query .= 'AND delivery_id = ' . sqlite_escape_string($delivery) . ';';
                        if(!@sqlite_query($db, $query))
                        {
                            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; jour des rendus du travail " . $this->_Name);
                        }
                    }
                    $this->_DeliveryManager->Flush();
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
                $deliveries = $this->_DeliveryManager->GetEntities();
                foreach($deliveries as $delivery)
                {
                    $delivery->Delete();
                }
                        
                $this->RemoveDirectory();
                
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    $query = 'DELETE FROM ' . __CLASS__ . ' WHERE id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Suppression du travail " . $this->_Name);
                    }
                    
                    $query = 'DELETE FROM PWHWorkSubject WHERE work_id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Suppression du travail " . $this->_Name);
                    }
                    
                    $query = 'DELETE FROM PWHWorkTeacher WHERE work_id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Suppression du travail " . $this->_Name);
                    }
                    
                    $query = 'DELETE FROM PWHWorkFiles WHERE work_id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Suppression du travail " . $this->_Name);
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
                        
        public function GetOwnerID()
        {
            return $this->_OwnerID;
        }
        
        public function SetOwnerID($id)
        {
            $this->_OwnerID = $id;
        }
        
        public function GetSubjectID()
        {
            return $this->_SubjectID;
        }
        
        public function SetSubjectID($id)
        {
            $this->_SubjectID = $id;
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
        
        public function GetExtraTime()
        {
            return $this->_ExtraTime;
        }
        
        public function SetExtraTime($extraTime)
        {
            $this->_ExtraTime = $extraTime;
        }
        
        public function GetSize()
        {
            return $this->_Size;
        }
        
        public function SetSize($size)
        {
            $this->_Size = $size;
        }
        
        public function GetGroupMin()
        {
            return $this->_GroupMin;
        }
        
        public function SetGroupMin($groupMin)
        {
            $this->_GroupMin = $groupMin;
        }
        
        public function GetGroupMax()
        {
            return $this->_GroupMax;
        }
        
        public function SetGroupMax($groupMax)
        {
            $this->_GroupMax = $groupMax;
        }
        
        public function GetLink()
        {
            return $this->_Link;
        }
        
        public function SetLink($link)
        {
            $this->_Link = $link;
        }
        
        public function GetLevel()
        {
            return $this->_Level;
        }
        
        public function SetLevel($level)
        {
            $this->_Level = $level;
        }
        
        public function IsSimple()
        {
            return $this->_Simple;
        }
        
        public function SetSimple($tf)
        {
            $this->_Simple = $tf;
        }
        
        public function IsPublished()
        {
            return $this->_Published;
        }
        
        public function SetPublished($tf)
        {
            $this->_Published = $tf;
        }
        
        public function AddDeliveries($deliveries)
        {
            $this->_DeliveryManager->AddEntities($deliveries);           
        }
        
        public function RemoveDeliveries($deliveries)
        {
            $this->_DeliveryManager->RemoveEntities($deliveries);
        }
        
        public function GetDelivery($id)
        {
            return $this->_DeliveryManager->GetEntity($id);
        }
        
        public function GetDeliveries()
        {
            return $this->_DeliveryManager->GetEntities();
        }
        
        public function DeliveryExists($id)
        {
            return $this->_DeliveryManager->EntityExists($id);
        }
        
        public function HasDeliveries()
        {
            return $this->_DeliveryManager->IsEmpty();
        }
        
        public function GetFiles()
        {
            return $this->_Files;
        }
        
        public function AddFiles($files)
        {
            foreach($files as $name=>$format)
            {
                $this->_Files[$name] = $format;
            }
        }
        
        public function RemoveFiles($names)
        {
            foreach($names as $name)
            {
                $this->_Files = array_diff_key($this->_Files, array($name => 0));
            }
        }
        
        public function CountFiles()
        {
            return count($this->_Files);
        }
        
        public function ClearFiles()
        {
            $this->_Files = array();
        }
        
        public function IsConfigured()
        {
            return $this->_Name != "" && $this->_ExtraTime != "" && $this->_Size != "" && $this->_GroupMin != "" && $this->_GroupMax != "";
        }
               
        public function GetPath()
        {
            $subject = new PWHSubject();
            $subject->Read($this->_SubjectID);
            return  $subject->GetPath() . $this->_ID . '/';
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
                    throw new PWHIOException(PWHIOException::PWHEFILEEXISTS . " : R&eacute;pertoire du travail " . $this->_Name);
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
                    throw new PWHIOException(PWHIOException::PWHEFILENOTEXISTS . " : R&eacute;pertoire du travail " . $this->_Name);
                }
            }
            else
            {
                throw new PWHQueryException(PWHQueryException::PWHENOTREG. ": work");
            }
        }
        
        public function RenameDirectory()
        {
            if($this->IsPersistent())
            {            
                $subject = PWHEntity::NewInstance('PWHSubject');
                $subject->Read($this->_SubjectID);
                $oldWorkDirectory = $subject->GetPath() . $this->_ID . '/';
                if(file_exists($oldSubjectDirectory))
                {
                    rename($oldWorkDirectory, $this->GetPath());
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
        
        public function IsExtraTimeUsed($timestamp)
        {
            $used = false;
            $deliveries = $this->_DeliveryManager->GetEntities();
            foreach($deliveries as $delivery)
            {
                if($delivery->IsExtraTimeUsed($timestamp))
                {
                    $used = true;
                }
            }
            return $used;
        }
        
        public static function debug()
        {
            if($db = @sqlite_open(DATABASE_FILE())) 
            {
                echo '<table><caption>' . __CLASS__ . '</caption>';
                echo '<tr><th>id</th><th>name</th><th>extra_time</th><th>size</th><th>group_min</th><th>group_max</th><th>link</th><th>level</th><th>simple</th><th>published</th></tr>';
                $query = 'SELECT * FROM ' . __CLASS__ . ';';             
                if($result = sqlite_query($db, $query))
                {
                    while($entry = sqlite_fetch_array($result))
                    {
                        echo '<tr><td>'.$entry['id'].'</td><td>'.$entry['name'].'</td><td>'.$entry['extra_time'].'</td><td>'.$entry['size'].'</td><td>'.$entry['group_min'].'</td><td>'.$entry['group_max'].'</td><td>'.$entry['link'].'</td><td>'.$entry['level'].'</td><td>'.$entry['simple'].'</td><td>'.$entry['published'].'</td></tr>';
                    }
                }
                
                echo '<table><caption>PWHWorkFiles</caption>';
                echo '<tr><th>work_id</th><th>name</th><th>format</th></tr>';
                $query = 'SELECT * FROM PWHWorkFiles;';             
                if($result = sqlite_query($db, $query))
                {
                    while($entry = sqlite_fetch_array($result))
                    {
                        echo '<tr><td>'.$entry['work_id'].'</td><td>'.$entry['name'].'</td><td>'.$entry['format'].'</td></tr>';
                    }
                }
                
                echo '<table><caption>PWHWorkTeacher</caption>';
                echo '<tr><th>teacher_id</th><th>work_id</th></tr>';
                $query = 'SELECT * FROM PWHWorkTeacher;';             
                if($result = sqlite_query($db, $query))
                {
                    while($entry = sqlite_fetch_array($result))
                    {
                        echo '<tr><td>'.$entry['teacher_id'].'</td><td>'.$entry['work_id'].'</td></tr>';
                    }    
                }
                
                echo '<table><caption>PWHDeliveryWork</caption>';
                echo '<tr><th>work_id</th><th>delivery_id</th></tr>';
                $query = 'SELECT * FROM PWHDeliveryWork;';             
                if($result = sqlite_query($db, $query))
                {
                    while($entry = sqlite_fetch_array($result))
                    {
                        echo '<tr><td>'.$entry['work_id'].'</td><td>'.$entry['delivery_id'].'</td></tr>';
                    }
                }
                sqlite_close($db);
            }
        }
    }
?>
