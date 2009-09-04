<?php 
    class PWHSubject extends PWHEntity implements PWHWritable
    {
        private $_Name;
        private $_OldName;
        private $_WorkManager;
        private $_TeacherManager;
        private $_GroupManager;
        
        public function __construct()
        {
            parent::__construct();
            $this->_Name = "";
            $this->_OldName = "";
            $this->_WorkManager = new PWHEntityManager('PWHWork');
            $this->_TeacherManager = new PWHEntityManager('PWHTeacher');
            $this->_GroupManager = new PWHEntityManager('PWHGroup');
        }
        
        public function __toString()
        {
            $strbuf = '{subject} ' . parent::__toString() . ' [name:' .$this->_Name . "] [works:";
            $works = $this->_WorkManager->GetEntities();
            foreach($works as $work)
            {
                $strbuf .= $work->GetID() . " ";
            }
            
            $strbuf .= '] [teachers:';
            $teachers  = $this->_TeacherManager->GetEntities();
            foreach($teachers as $teacher)
            {
                $strbuf .= $teacher->GetID() . " ";
            }
            
            $strbuf .= '] [groups:';
            $groups  = $this->_GroupManager->GetEntities();
            foreach($groups as $group)
            {
                $strbuf .= $group->GetID() . " ";
            }
            return $strbuf . "]";
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
                    $works = $this->_WorkManager->GetEntities();
                    foreach($works as $work)
                    {
                        if(!@sqlite_query($db, "INSERT INTO PWHWorkSubject VALUES('" . $result . "', '" . $work->GetID() . "');"))
                        {
                            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Ajout du travail " . $work->GetName() .  " dans la mati&egrave;re " . $this->_Name);
                        }
                    }
                    
                    $teachers  = $this->_TeacherManager->GetEntities();
                    foreach($teachers as $teacher)
                    {
                        if(!@sqlite_query($db, "INSERT INTO PWHTeacherSubject VALUES('" . $result . "', '" . $teacher->GetID() . "');"))
                        {
                            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Ajout de l'enseignant " . $teacher->GetLogin() .  " dans la mati&egrave;re " . $this->_Name);
                        }
                    }
                    
                    $groups  = $this->_GroupManager->GetEntities();
                    foreach($groups as $group)
                    {
                        if(!@sqlite_query($db, "INSERT INTO PWHGroupSubject VALUES('" . $result . "', '" . $group->GetID() . "');"))
                        {
                            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Ajout du groupe " . $group->GetName() .  " dans la mati&egrave;re " . $this->_Name);
                        }
                    }
                    $this->_WorkManager->Flush();   
                    $this->_TeacherManager->Flush(); 
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
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Cr&eacute;ation de la mati&egrave;re " . $this->_Name);
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
                    $entry = sqlite_fetch_array($result);
                    $this->_ID = $id;
                    $this->_Name = $entry['name'];
                    $this->_OldName = $entry['name']; 
                }
                else
                {
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Lecture d'une mati&egrave;re");
                }
                
                $query = 'SELECT * FROM PWHWorkSubject WHERE subject_id = '. sqlite_escape_string($id) . ';';             
                if($result = @sqlite_query($db, $query))
                {
                    $this->_WorkManager->Clear();
                    $works = array();
                    while($entry = sqlite_fetch_array($result))
                    {
                        array_push($works, (int)$entry['work_id']);
                    }
                    $this->_WorkManager->AddEntities($works);
                    $this->_WorkManager->Flush();      
                }
                else
                {
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Lecture des travaux de la mati&egrave;re " . $this->_Name);
                }
                
                $query = 'SELECT * FROM PWHTeacherSubject WHERE subject_id = '. sqlite_escape_string($id) . ';';             
                if($result = @sqlite_query($db, $query))
                {
                    $this->_TeacherManager->Clear();
                    $teachers = array();
                    while($entry = sqlite_fetch_array($result))
                    {
                        array_push($teachers, (int)$entry['teacher_id']);
                    }
                    $this->_TeacherManager->AddEntities($teachers);
                    $this->_TeacherManager->Flush();                         
                }
                else
                {
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Lecture des enseignants de la mati&egrave;re " . $this->_Name);
                }
                
                $query = 'SELECT * FROM PWHGroupSubject WHERE subject_id = '. sqlite_escape_string($id) . ';';             
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
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Lecture des enseignants de la mati&egrave;re " . $this->_Name);
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
                    $query = 'UPDATE ' . __CLASS__ . " SET name = '" . sqlite_escape_string($this->_Name);
                    $query .= "' WHERE id = ". sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; jour de la mati&egrave;re " . $this->_Name);
                    }
                     
                    $this->RenameDirectory();
                                     
                    $worksAdded = $this->_WorkManager->GetEntitiesAdded();
                    foreach($worksAdded as $work)
                    {
                        $query = "INSERT INTO PWHWorkSubject VALUES ('" . $this->_ID . "', '" . $work . "');";
                        if(!@sqlite_query($db, $query))
                        {
                            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; jour des travaux de la mati&egrave;re " . $this->_Name);
                        }
                    }
                    
                    $worksRemoved = $this->_WorkManager->GetEntitiesRemoved();                                   
                    foreach($worksRemoved as $work)
                    {
                        $query = 'DELETE FROM PWHWorkSubject WHERE subject_id = ' . sqlite_escape_string($this->_ID);
                        $query .= 'AND work_id = ' . sqlite_escape_string($work) . ';';
                        if(!@sqlite_query($db, $query))
                        {
                            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; jour des travaux de la mati&egrave;re " . $this->_Name);
                        }
                    }
                    $this->_WorkManager->Flush();
                    
                    $teachersAdded = $this->_TeacherManager->GetEntitiesAdded();
                    foreach($teachersAdded as $teacher)
                    {
                        $query = "INSERT INTO PWHTeacherSubject VALUES ('" . $this->_ID . "', '" . $teacher . "');";
                        if(!@sqlite_query($db, $query))
                        {
                            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; jour des enseignants de la mati&egrave;re " . $this->_Name);
                        }
                    }
                    
                    $teachersRemoved = $this->_TeacherManager->GetEntitiesRemoved();     
                    foreach($teachersRemoved as $teacher)
                    {
                        $query = 'DELETE FROM PWHTeacherSubject WHERE subject_id = ' . sqlite_escape_string($this->_ID);
                        $query .= 'AND teacher_id = ' . sqlite_escape_string($teacher) . ';';
                        if(!@sqlite_query($db, $query))
                        {
                            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; jour des enseignants de la mati&egrave;re " . $this->_Name);
                        }
                    }
                    $this->_TeacherManager->Flush();
                    
                    $groupsAdded = $this->_GroupManager->GetEntitiesAdded();
                    foreach($groupsAdded as $group)
                    {
                        $query = "INSERT INTO PWHGroupSubject VALUES ('" . $this->_ID . "', '" . $group . "');";
                        if(!@sqlite_query($db, $query))
                        {
                            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; jour des groupes de la mati&egrave;re " . $this->_Name);
                        }
                    }
                    
                    $groupsRemoved = $this->_GroupManager->GetEntitiesRemoved();     
                    foreach($groupsRemoved as $group)
                    {
                        $query = 'DELETE FROM PWHGroupSubject WHERE subject_id = ' . sqlite_escape_string($this->_ID);
                        $query .= 'AND group_id = ' . sqlite_escape_string($group) . ';';
                        if(!@sqlite_query($db, $query))
                        {
                            throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; jour des groupes de la mati&egrave;re " . $this->_Name);
                        }
                    }
                    $this->_GroupManager->Flush();
                    
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
                $works = $this->_WorkManager->GetEntities();
                foreach($works as $work)
                {
                    $work->Delete();
                }
                $this->RemoveDirectory();
                
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    $query = 'DELETE FROM ' . __CLASS__ . ' WHERE id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Suppression de la mati&egrave;re " . $this->_Name);
                    }
                    
                    $query = 'DELETE FROM PWHTeacherSubject WHERE subject_id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Suppression des enseignants dans la mati&egrave;re " . $this->_Name);
                    }
                    
                    $query = 'DELETE FROM PWHGroupSubject WHERE subject_id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Suppression des groupes dans la mati&egrave;re " . $this->_Name);
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
                        
        public function GetName()
        {
            return $this->_Name;
        }
        
        public function SetName($name)
        {
            $this->_OldName = $this->_Name;
            $this->_Name = $name;
        }
        
        
        public function AddWorks($works)
        {
            $this->_WorkManager->AddEntities($works);        
        }
        
        public function RemoveWorks($works)
        {
            $this->_WorkManager->RemoveEntities($works);
        }   
        
        public function GetWork($id)
        {
            return $this->_WorkManager->GetEntity($id);
        }
        
        public function GetWorks()
        {
            return $this->_WorkManager->GetEntities();
        }
        
        public function WorkExists($id)
        {
            return $this->_WorkManager->EntityExists($id);
        }
        
        public function HasWorks()
        {
            return !$this->_WorkManager->IsEmpty();
        }
        
        public function AddTeachers($teachers)
        {
            $this->_TeacherManager->AddEntities($teachers);  
        }
        
        public function RemoveTeachers($teachers)
        {
            $this->_TeacherManager->RemoveEntities($teachers);
        }   
        
        public function GetTeacher($id)
        {
            return $this->_TeacherManager->GetEntity($id);
        }
        
        public function GetTeachers()
        {
            return $this->_TeacherManager->GetEntities();
        }
        
        public function TeacherExists($id)
        {
            return $this->_TeacherManager->EntityExists($id);
        }
        
        public function HasTeachers()
        {
            return !$this->_TeacherManager->IsEmpty();
        }
        
        public function CountTeachers()
        {
            return $this->_TeacherManager->Size();
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
            return $this->_GroupManager->EntityExists($id);
        }
        
        public function HasGroups()
        {
            return !$this->_GroupManager->IsEmpty();
        }
        
        public function CountGroups()
        {
            return $this->_GroupManager->Size();
        }
        
        public function IsConfigured()
        {
            return $this->_Name != "";
        }
        
        public function GetPath()
        {
            return UPLOAD_PATH() . $this->_ID . '/';
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
                    throw new PWHIOException(PWHIOException::PWHEFILEEXISTS . " : R&eacute;pertoire de la mati&egrave,re " . $this->_Name);
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
                    throw new PWHIOException(PWHIOException::PWHEFILENOTEXISTS . " : R&eacute;pertoire de la mati&egrave;re " . $this->_Name);
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
                $oldSubjectDirectory = UPLOAD_PATH() . $this->_ID . '/';
                if(file_exists($oldSubjectDirectory))
                {
                    rename($oldSubjectDirectory, $this->GetPath());
                }
                else
                {
                    throw new PWHIOException(PWHIOException::PWHEFILENOTEXISTS . " : R&eacute;pertoire de la mati&egrave;re " . $this->_Name);
                }
            }
            else
            {
                throw new PWHQueryException(PWHQueryException::PWHENOTREG);
            }
        }
        
        public function GetPromotion()
        {
            if($this->CountGroups() > 0)
            {
                $groups = $this->GetGroups();
                return $groups[0]->GetPromotion();
            }
            else
            {
                return new PWHGroup();
            }
        }
                
        public static function debug()
        {
            if($db = @sqlite_open(DATABASE_FILE())) 
            {
                echo '<table><caption>' . __CLASS__ . '</caption>';
                echo '<tr><th>id</th><th>name</th></tr>';
                $query = 'SELECT * FROM ' . __CLASS__ . ';';             
                if($result = sqlite_query($db, $query))
                {
                    while($entry = sqlite_fetch_array($result))
                    {
                        echo '<tr><td>'.$entry['id'].'</td><td>'.$entry['name'].'</td></tr>';
                    }
                }
                echo '</table>';
                
                echo '<table><caption>PWHWorkSubject</caption>';
                echo '<tr><th>subject_id</th><th>work_id</th></tr>';
                $query = 'SELECT * FROM PWHWorkSubject;';             
                if($result = sqlite_query($db, $query))
                {
                    while($entry = sqlite_fetch_array($result))
                    {
                        echo '<tr><td>'.$entry['subject_id'].'</td><td>'.$entry['work_id'].'</td></tr>';
                    }
                }
                echo '</table>';
                
                echo '<table><caption>PWHTeacherSubject</caption>';
                echo '<tr><th>subject_id</th><th>teacher_id</th></tr>';
                $query = 'SELECT * FROM PWHTeacherSubject;';             
                if($result = sqlite_query($db, $query))
                {
                    while($entry = sqlite_fetch_array($result))
                    {
                        echo '<tr><td>'.$entry['subject_id'].'</td><td>'.$entry['teacher_id'].'</td></tr>';
                    }
                }
                echo '</table>';
                
                echo '<table><caption>PWHGroupSubject</caption>';
                echo '<tr><th>subject_id</th><th>group_id</th></tr>';
                $query = 'SELECT * FROM PWHGroupSubject;';             
                if($result = sqlite_query($db, $query))
                {
                    while($entry = sqlite_fetch_array($result))
                    {
                        echo '<tr><td>'.$entry['subject_id'].'</td><td>'.$entry['group_id'].'</td></tr>';
                    }
                }
                echo '</table>';
       
                sqlite_close($db);
            }
        }    
    }
?>
