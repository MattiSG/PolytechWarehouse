<?php
    require_once(LIB_PATH() . "PWHIEntity.php");
    require_once(LIB_PATH() . "PWHErrorTypes.php");
    
    class PWHWork implements PWHIEntity
    {
        private $_ID;
        private $_Name;
        private $_ExtraTime;
        private $_Size;
        private $_Format;
        private $_GroupMin;
        private $_GroupMax;
        
        public function __construct($name, $extraTime, $size, $format, $groupMin, $groupMax)
        {
            $this->_ID = -1;
            $this->_Name = $name;
            $this->_ExtraTime = $extraTime;
            $this->_Size = $size;
            $this->_Format = $format;
            $this->_GroupMin = $groupMin;
            $this->_GroupMax = $groupMax;            
        }
        
        public static function ListWorks()
        {
            if ($db = @sqlite_open(DATABASE_FILE())) 
            {
                if($result = @sqlite_query($db, 'SELECT id FROM PWH_Work;'))
                {
                    sqlite_close($db);                  
                    $works = array();
                    while($tuple = sqlite_fetch_array($result))
                    {
                        $work = new PWHWork(null);
                        $work->Read($tuple['id']);
                        array_push($works, $work);
                    }
                    return $works;
                }
                else
                {
                    throw new Exception(PWHEQUERY . ': Liste des travaux');
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
                $query = "INSERT INTO PWH_Work VALUES(NULL, '" . $this->_Name . "','" . $this->_ExtraTime;
                $query .= "','" . $this->_Size . "','" . $this->_Format . "','" . $this->_GroupMin . "','" . $this->_GroupMax . "');";
                if(@sqlite_query($db, $query)
                    && $result = @sqlite_query($db, 'SELECT max(id) FROM PWH_Work;'))
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
                    throw new Exception(PWHEQUERY . ": Cr&eacute;tion du travail " . $this->_Name);
                }
            }
            else
            {
                throw new Exception(PWHEACCES);
            }
        }
        
        public function Read($id)
        {
            if($db = @sqlite_open(DATABASE_FILE())) 
            {
                $query = 'SELECT name, extra_time, size, format, group_min, group_max FROM PWH_Work';
                $query .= ' WHERE id = '. sqlite_escape_string($id) . ';';             
                if($result = @sqlite_query($db, $query))
                {
                    sqlite_close($db);
                    $entry = sqlite_fetch_array($result);
                    $this->_ID = $id;
                    $this->_Name = $entry['name'];
                    $this->_ExtraTime = $entry['extra_time'];
                    $this->_Size = $entry['size'];
                    $this->_Format = $entry['format'];
                    $this->_GroupMin = $entry['group_min'];
                    $this->_GroupMax = $entry['group_max'];
                }
                else
                {
                    throw new Exception(PWHEQUERY . ": Lecture d'un travail");
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
                    $query = "UPDATE PWH_Work SET name = '" . sqlite_escape_string($this->_Name);
                    $query .= "',extra_time = '" . sqlite_escape_string($this->_ExtraTime);
                    $query .= "',size = '" . sqlite_escape_string($this->_Size);
                    $query .= "',format = '" . sqlite_escape_string($this->_Format);
                    $query .= "',group_min = '" . sqlite_escape_string($this->_GroupMin);
                    $query .= "',group_max = '" . sqlite_escape_string($this->_GroupMax);
                    $query .= "' WHERE id = ". sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {                       
                        throw new Exception(PWHEQUERY . ": Mise &agrave; jour du travail " . $this->_Name);
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
                    $query = 'DELETE FROM PWH_Work WHERE id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new Exception(PWHEQUERY . ": Suppression du travail " . $this->_Name);
                    }
                    
                    $query = 'DELETE FROM PWH_WorkSubject WHERE work_id = '. sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new Exception(PWHEQUERY . ": Suppression du travail " . $this->_Name);
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
        
        public function GetFormat()
        {
            return $this->_Format;
        }
        
        public function SetFormat($format)
        {
            $this->_Format = $format;
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
        
        public function IsMapped()
        {
            return $this->_ID > 0;
        }
        
        public function __toString()
        {
            return '[' . $this->_ID . '] ' . $this->_Name . " " . $this->_ExtraTime . " " . $this->_Size . " " . $this->_Format
             . " " . $this->_GroupMin . " " . $this->_GroupMax;
        }
    }
?>
