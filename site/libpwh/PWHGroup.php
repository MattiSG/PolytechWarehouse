<?php
    require_once(LIB_PATH() . "PWHIEntity.php");
    require_once(LIB_PATH() . "PWHStudent.php");
    require_once(LIB_PATH() . "PWHErrorTypes.php");
    
    class PWHGroup implements PWHIEntity
    {
        private $_ID;
        private $_Name;
        private $_Students;
        private $_StudentsAdded;
        private $_StudentsRemoved;
        
        public function __construct($name)
        {
            $this->_ID = -1;
            $this->_Name = $name;
            $this->_Students = array();
            $this->_StudentsAdded = array();
            $this->_StudentsRemoved = array();
        }
       
        public static function ListGroups() 
        {       
            if ($db = @sqlite_open(DATABASE_FILE())) 
            {
                if($result = @sqlite_query($db, 'SELECT id FROM PWH_Group;'))
                {
                    sqlite_close($db);
                    $groups = array();
                    while($tuple = sqlite_fetch_array($result))
                    {
                        $group = new PWHGroup(null);
                        $group->Read($tuple['id']);
                        array_push($groups, $group);
                    }
                    return $groups;
                }
                else
                {
                    throw new Exception(PWHEQUERY . ': Liste des groupes');
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
                $query = "INSERT INTO PWH_Group VALUES(NULL, '" . $this->_Name . "');";             
                if(@sqlite_query($db, $query)
                    && $result = @sqlite_query($db, 'SELECT max(id) FROM PWH_Group;'))
                {
                    $result = sqlite_fetch_single($result);
                    foreach($this->_Students as $student)
                    {
                        if(is_int($student))
                        {
                            if(!@sqlite_query($db, "INSERT INTO PWH_StudentGroup VALUES('" . $result . "', '" . $student . "');"))
                            {
                                $id = $student;
                                $student = new PWHStudent(null);
                                $student->Read($id);
                                throw new Exception(PWHEQUERY . ": Ajout de l'&eacute;tudiant " . $student->GetLogin() . " dans le groupe " . $this->_Name);
                            }
                        }
                        else
                        {
                            if(!@sqlite_query($db, "INSERT INTO PWH_StudentGroup VALUES('" . $result . "', '" . $student->GetID() . "');"))
                            {
                                throw new Exception(PWHEQUERY . ": Ajout de l'&eacute;tudiant " . $student->GetLogin() . " dans le groupe " . $this->_Name);
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
                    throw new Exception(PWHEQUERY . ": Cr&eacute;tion du groupe " . $this->_Name);
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
                $query = 'SELECT name FROM PWH_Group WHERE id = '. sqlite_escape_string($id) . ';';             
                if($result = @sqlite_query($db, $query))
                {               
                    $entry = sqlite_fetch_array($result);
                    $this->_ID = $id;
                    $this->_Name = $entry['name'];        
                }
                else
                {
                    throw new Exception(PWHEQUERY . ": Lecture d'un groupe");
                }
                
                $query = 'SELECT * FROM PWH_StudentGroup WHERE group_id = '. sqlite_escape_string($id) . ';';             
                if($result = @sqlite_query($db, $query))
                {
                    $this->ClearList();
                    $students = array();
                    while($entry = sqlite_fetch_array($result))
                    {
                        array_push($students, (int)$entry['student_id']);
                    }
                    $this->AddStudents($students);
                    $this->ClearBuffers();
                    sqlite_close($db);       
                }
                else
                {
                    throw new Exception(PWHEQUERY . ": Lecture des &eacute;tudiant du groupe " . $this->_Name);
                }
            }
            else
            {
                throw new Exception(PWHEACCES);
            }
        
        }
        
        public function ReadFromFile($fileName)
        {        
            $students = array();
            if($file = @file($fileName))
            {
                foreach($file as $line)
                {
                    $line = rtrim($line);
                    if(preg_match("#[a-zA-Z0-9]+;[a-zA-Z0-9]+@[a-zA-Z0-9]+#", $line))
                    {
                        try 
                        {
                            $info = explode(';', $line);
                            $student = new PWHStudent($info[0], $info[1]);
                            $student->Create(true);
                            array_push($students, $student->GetID());
                        }
                        catch(Exception $ex)
                        {
                            throw new Exception(PWHEPRMREAD .". " . $ex->getMessage());
                        }
                    }
                }
                $this->ClearList();
                $this->AddStudents($students);              
                $this->ClearBuffers(); 
            }
            else
            {
                throw new Exception(PWHEPRMNOTFOUND);
            }
        }
        
        public function Update()
        {
            if($this->_ID > 0)
            {
                if ($db = @sqlite_open(DATABASE_FILE())) 
                {
                    $query = "UPDATE PWH_Group SET name = '" . sqlite_escape_string($this->_Name);
                    $query .= "' WHERE id = ". sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new Exception(PWHEQUERY . ": Mise &agrave; jour du groupe " .$this->_Name);
                    }
                                     
                    foreach($this->_StudentsAdded as $student)
                    {
                        $query = "INSERT INTO PWH_StudentGroup VALUES ('" . $this->_ID . "', '" . $student . "');";
                        if(!@sqlite_query($db, $query))
                        {
                            throw new Exception(PWHEQUERY . ": Mise &agrave; jour des &eacute;tudiants du groupe " . $this->_Name);
                        }
                    }
                                       
                    foreach($this->_StudentsRemoved as $student)
                    {
                        $query = 'DELETE FROM PWH_StudentGroup WHERE group_id = ' . sqlite_escape_string($this->_ID);
                        $query .= 'AND student_id = ' . sqlite_escape_string($student) . ';';
                        if(!@sqlite_query($db, $query))
                        {
                            throw new Exception(PWHEQUERY . ": Mise &agrave; jour des &eacute;tudiants d'un groupe " . $this->_Name);
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
                    $query = 'DELETE FROM PWH_Group WHERE id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new Exception(PWHEQUERY . "Suppression du groupe " . $this->_Name);
                    }
                    
                    $query = 'DELETE FROM PWH_StudentGroup WHERE group_id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new Exception(PWHEQUERY . "Suppression des &eacute;tudiants du groupe " . $this->_Name);                      
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
        
        public function AddStudents($students)
        {
            $this->_Students = array_merge($this->_Students, (array)$students);
            if($this->IsMapped())
            {
                $this->_StudentsAdded = array_merge($this->_StudentsAdded, (array)$students);
            }
            
        }
        
        public function RemoveStudents($students)
        {
            foreach($students as $id)
            {
                $i = 0;
                while($i < count($this->_Students))
                {
                    if(is_int($this->_Students[$i]) && $this->_Students[$i] == $id)
                    {
                        array_splice($this->_Students, $i, 1);
                        if($this->IsMapped())
                        {
                            array_push($this->_StudentsRemoved, $id);
                        }
                    }
                    else if(!is_int($this->_Students[$i]) && $this->_Students[$i]->GetID() == $id)
                    {
                        array_splice($this->_Students, $i, 1);
                        if($this->IsMapped())
                        {
                            array_push($this->_StudentsRemoved, $id);
                        }
                    }
                    $i++;
                }             
            }
        }   
        
        public function GetStudent($id)
        {
            $i = 0;
            while($i < count($this->_Students))
            {
                if(is_int($this->_Students[$i]) && $this->_Students[$i] == $id)
                {
                    $student = new PWHStudent(null, null);
                    $student->Read($id);
                    $this->_Students[$i] = $student;
                    return $student;
                }
                else if(!is_int($this->_Students[$i]) && $this->_Students[$i]->GetID() == $id)
                {
                    return $this->_Students[$i];
                }
                $i++;
            }
            return false;
        }
        
        public function GetStudentsIDs()
        {
            $i = 0;
            $ids = array();
            while($i < count($this->_Students))
            {
                if(is_int($this->_Students[$i]))
                {
                    array_push($ids, $this->_Students[$i]);
                }
                else if(!is_int($this->_Students[$i]))
                {
                    array_push($ids, $this->_Students[$i]->GetID());
                }
                $i++;
            }
            return $ids;
        }
        
        public function StudentExists($id)
        {
            $i = 0;
            while($i < count($this->_Students))
            {
                if(is_int($this->_Students[$i]) && $this->_Students[$i] == $id)
                {
                    return true;
                }
                else if(!is_int($this->_Students[$i]) && $this->_Students[$i]->GetID() == $id)
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
            foreach($this->_Students as $student)
            {
                if(is_int($student))
                {
                    $strbuf .= $student . " ";
                }
                else
                {
                    $strbuf .= $student->GetID() . " ";
                }
            }
            return $strbuf;
        }
        
        private function ClearList()
        {
            $this->_Students = null;
            $this->_Students = array();
        }
        
        private function ClearBuffers()
        {
            $this->_StudentsAdded = null;
            $this->_StudentsRemoved = null;
            $this->_StudentsAdded = array();
            $this->_StudentsRemoved = array();
        }
    }
?>
