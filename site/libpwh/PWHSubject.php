<?php
    require_once(LIB_PATH() . "PWHIEntity.php");    
    require_once(LIB_PATH() . "PWHWork.php");
    
    class PWHSubject implements PWHIEntity
    {
        private $_ID;
        private $_Name;
        private $_Works;
        private $_WorksAdded;
        private $_WorksRemoved;
        
        public function __construct($name)
        {
            $this->_ID = -1;
            $this->_Name = $name;
            $this->_Works = array();
            $this->_WorksAdded = array();
            $this->_WorksRemoved = array();
        }
       
        public static function ListSubjects() 
        {       
            if ($db = @sqlite_open(DATABASE_FILE())) 
            {
                if($result = @sqlite_query($db, 'SELECT id FROM PWH_Subject;'))
                {
                    sqlite_close($db);
                    $subjects = array();
                    while($tuple = sqlite_fetch_array($result))
                    {
                        $subject = new PWHSubject(null);
                        $subject->Read((int)$tuple['id']);
                        array_push($subjects, $subject);
                    }
                    return $subjects;
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
        
        public function Create($overwrite)
        {
            if ($db = @sqlite_open(DATABASE_FILE())) 
            {
                $query = "INSERT INTO PWH_Subject VALUES(NULL, '" . $this->_Name . "');";             
                if(@sqlite_query($db, $query)
                    && $result = @sqlite_query($db, 'SELECT max(id) FROM PWH_Subject;'))
                {
                    $result = sqlite_fetch_single($result);
                    foreach($this->_Works as $work)
                    {
                        if(is_int($work))
                        {
                            if(!@sqlite_query($db, "INSERT INTO PWH_WorkSubject VALUES('" . $result . "', '" . $work . "');"))
                            {
                                $id = $work;
                                $work = new PWHWork(null, null, null, null, null, null);
                                $work->Read($id);
                                throw new Exception(PWHEQUERY . ": Ajout du travail " . $work->GetName() .  " dans la mati&egrave;re " . $this->_Name);
                            }
                        }
                        else
                        {
                            if(!@sqlite_query($db, "INSERT INTO PWH_WorkSubject VALUES('" . $result . "', '" . $work->GetID() . "');"))
                            {
                                throw new Exception(PWHEQUERY . ": Ajout du travail " . $work->GetName() .  " dans la mati&egrave;re " . $this->_Name);
                            }
                        }
                    }                
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
                    throw new Exception(PWHEQUERY . ": Cr&eacute;ation de la mati&egrave;re " . $this->_Name);
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
                $query = 'SELECT name FROM PWH_Subject WHERE id = '. sqlite_escape_string($id) . ';';             
                if($result = @sqlite_query($db, $query))
                {               
                    $entry = sqlite_fetch_array($result);
                    $this->_ID = $id;
                    $this->_Name = $entry['name'];        
                }
                else
                {
                    throw new Exception(PWHEQUERY . ": Lecture d'une mati&egrave;re");
                }
                
                $query = 'SELECT * FROM PWH_WorkSubject WHERE subject_id = '. sqlite_escape_string($id) . ';';             
                if($result = @sqlite_query($db, $query))
                {
                    $this->ClearList();
                    $works = array();
                    while($entry = sqlite_fetch_array($result))
                    {
                        array_push($works, (int)$entry['work_id']);
                    }
                    $this->AddWorks($works);
                    $this->ClearBuffers();
                    sqlite_close($db);       
                }
                else
                {
                    throw new Exception(PWHEQUERY . ": Lecture des travaux de la mati&egrave;re " . $this->_Name);
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
                    $query = "UPDATE PWH_Subject SET name = '" . sqlite_escape_string($this->_Name);
                    $query .= "' WHERE id = ". sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new Exception(PWHEQUERY . ": Mise &agrave; jour de la mati&egrave;re " . $this->_Name);
                    }
                                     
                    foreach($this->_WorksAdded as $work)
                    {
                        $query = "INSERT INTO PWH_WorkSubject VALUES ('" . $this->_ID . "', '" . $work . "');";
                        if(!@sqlite_query($db, $query))
                        {
                            throw new Exception(PWHEQUERY . ": Mise &agrave; des travaux de la mati&egrave;re " . $this->_Name);
                        }
                    }
                                       
                    foreach($this->_WorksRemoved as $work)
                    {
                        $query = 'DELETE FROM PWH_WorkSubject WHERE subject_id = ' . sqlite_escape_string($this->_ID);
                        $query .= 'AND work_id = ' . sqlite_escape_string($work) . ';';
                        if(!@sqlite_query($db, $query))
                        {
                            throw new Exception(PWHEQUERY . ": Mise &agrave; des travaux de la mati&egrave;re " . $this->_Name);
                        }
                    }
                    $this->ClearBuffers();
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
                    $query = 'DELETE FROM PWH_Subject WHERE id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new Exception(PWHEQUERY . ": Suppression de la mati&egrave;re " . $this->_Name);
                    }
                    
                    $query = 'DELETE FROM PWH_WorkSubject WHERE subject_id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {                      
                        throw new Exception(PWHEQUERY . ": Suppression des travaux de la mati&egrave;re " . $this->_Name);
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
                
        public function GetName()
        {
            return $this->_Name;
        }
        
        public function SetName($name)
        {
            $this->_Name = $name;
        }
        
        public function IsMapped()
        {
            return $this->_ID > 0;
        }
        
        public function AddWorks($works)
        {
            $this->_Works = array_merge($this->_Works, (array)$works);
            if($this->IsMapped())
            {
                $this->_WorksAdded = array_merge($this->_WorksAdded, (array)$works);
            }
            
        }
        
        public function RemoveWorks($works)
        {
            foreach($works as $id)
            {
                $i = 0;
                while($i < count($this->_Works))
                {
                    if(is_int($this->_Works[$i]) && $this->_Works[$i] == $id)
                    {
                        array_splice($this->_Works, $i, 1);
                        if($this->IsMapped())
                        {
                            array_push($this->_WorksRemoved, $id);
                        }
                    }
                    else if(!is_int($this->_Works[$i]) && $this->_Works[$i]->GetID() == $id)
                    {
                        array_splice($this->_Works, $i, 1);
                        if($this->IsMapped())
                        {
                            array_push($this->_WorksRemoved, $id);
                        }
                    }
                    $i++;
                }             
            }
        }   
        
        public function GetWork($id)
        {
            $i = 0;
            while($i < count($this->_Works))
            {
                if(is_int($this->_Works[$i]) && $this->_Works[$i] == $id)
                {
                    $work = new PWHWork(null, null, null, null, null, null);
                    $work->Read($id);
                    $this->_Works[$i] = $work;
                    return $work;
                }
                else if(!is_int($this->_Students[$i]) && $this->_Students[$i]->GetID() == $id)
                {
                    return $this->_Works[$i];
                }
                $i++;
            }
            return false;
        }
        
        public function GetWorksIDs()
        {
            $i = 0;
            $ids = array();
            while($i < count($this->_Works))
            {
                if(is_int($this->_Works[$i]))
                {
                    array_push($ids, $this->_Works[$i]);
                }
                else if(!is_int($this->_Works[$i]))
                {
                    array_push($ids, $this->_Works[$i]->GetID());
                }
                $i++;
            }
            return $ids;
        }
        
        public function WorkExists($id)
        {
            $i = 0;
            while($i < count($this->_Works))
            {
                if(is_int($this->_Works[$i]) && $this->_Works[$i] == $id)
                {
                    return true;
                }
                else if(!is_int($this->_Works[$i]) && $this->_Works[$i]->GetID() == $id)
                {
                    return true;
                }
                $i++;
            }
            return false;
        }   
        
        public function __toString()
        {
            $strbuf = $this->_Name . ": ";
            foreach($this->_Works as $work)
            {
                if(is_int($work))
                {
                    $strbuf .= $work . " ";
                }
                else
                {
                    $strbuf .= $work->GetID() . " ";
                }
            }
            return $strbuf;
        }
        
        private function ClearList()
        {
            $this->_Works = null;
            $this->_Works = array();
        }
        
        private function ClearBuffers()
        {
            $this->_WorksAdded = null;
            $this->_WorksRemoved = null;
            $this->_WorksAdded = array();
            $this->_WorksRemoved = array();
        }
    }
?>
