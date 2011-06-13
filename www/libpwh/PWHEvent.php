<?php
    class PWHEvent extends PWHEntity
    {
        const ALL_EVENTS = -1;
        
        private $_TargetID;
        private $_TargetType;
        private $_Message;
        private $_Creation;
        
        public function __construct()
        {
            parent::__construct();
            $this->_TargetID = -1;
            $this->_TargetType = -1;
            $this->_Message = "";
            $this->_Creation = "";
        }
        
        public function __toString()
        {
            return '{event} ' . parent::__toString() . ' [target_id:' . $this->_TargetID . '] [target_type:' . $this->_TargetType . '] [message:' . $this->_Message . '] [creation:' . $this->_Creation . ']';
        }
                        
        public function Create($overwrite)
        {
            if ($db = @sqlite_open(DATABASE_FILE())) 
            {
                $query = 'INSERT INTO ' . __CLASS__ . " VALUES(NULL, '" . sqlite_escape_string($this->_TargetID) .  "', '" . sqlite_escape_string($this->_TargetType) . "', '" . sqlite_escape_string($this->_Creation) . "', '" . sqlite_escape_string($this->_Message) . "');";
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
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Cr&eacute;ation d'un &eacute;v&egrave;venement");
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
                    $this->_TargetID = $entry['target_id'];
                    $this->_TargetType = $entry['target_type'];
                    $this->_Creation = $entry['creation'];
                    $this->_Message = $entry['message'];
                    sqlite_close($db);
                    return true;
                }
                else
                {
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Lecture d'un &eacute;v&egrave;vement");
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
                    $query = 'UPDATE ' . __CLASS__ . " SET target_id = '" . sqlite_escape_string($this->_TargetID);
                    $query .=  "',target_type = '" . sqlite_escape_string($this->_TargetType);
                    $query .=  "',creation = '" . sqlite_escape_string($this->_Creation);
                    $query .=  "',message = '" . sqlite_escape_string($this->_Message);
                    $query .="' WHERE id = " . sqlite_escape_string($this->_ID) . ';';             
                    if(!@sqlite_query($db, $query))
                    {
                        throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Mise &agrave; jour d'un &eacute;v&egrave;vement");                     
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
                
        public function GetTargetID()
        {
            return $this->_TargetID;
        }
        
        public function SetTargetID($id)
        {
            $this->_TargetID = $id;
        }
        
        public function GetTargetType()
        {
            return $this->_TargetType;
        }
        
        public function SetTargetType($type)
        {
            $this->_TargetType = $type;
        }
        
        public function GetMessage()
        {
            return $this->_Message;
        }
        
        public function SetMessage($message)
        {
            $this->_Message = $message;
        }
        
        public function GetCreation()
        {
            return $this->_Creation;
        }
        
        public function SetCreation($creation)
        {
            $this->_Creation = $creation;
        }
        
        public static function Notify($persons, $type, $message)
        {
            foreach($persons as $person)
            {
                $event = new PWHEvent();
                $event->SetTargetID($person->GetID());
                $event->SetTargetType($type);
                $event->SetMessage($message);
                $event->SetCreation(date("Y-m-d H:i:s"));
                $event->Create(true);
            }
        }
        
        public static function GetEvents($id, $type, $size)
        {
            if ($db = @sqlite_open(DATABASE_FILE())) 
            {
                $query = 'SELECT * FROM ' . __CLASS__ . " WHERE target_id = '". sqlite_escape_string($id) . "' AND target_type = '" . sqlite_escape_string($type) . "';";             
                if($result = @sqlite_query($db, $query))
                {
                    $events = array();
                    while($entry = sqlite_fetch_array($result))
                    {
                        $event = new PWHEvent();
                        $event->Read($entry['id']);
                        array_push($events, $event);
                    }
                    sqlite_close($db);
                    
                    if($size == self::ALL_EVENTS || count($events) <= $size)
                    {
                        return $events;
                    }
                    else
                    {
                        return array_slice($events, count($events) - $size);
                    }
                }
                else
                {
                    throw new PWHQueryException(PWHQueryException::PWHEQUERY . ": Lecture d'&eacute;v&egrave;vements");
                }
            }
            else
            {
                throw new PWHIOException(PWHIOException::PWHEACCES);
            }    
        }
        
        public static function Debug()
        {
            if($db = @sqlite_open(DATABASE_FILE())) 
            {
                echo '<table><caption>' . __CLASS__ . '</caption>';
                echo '<tr><th>id</th><th>target_id</th><th>target_type</th><th>creation</th><th>message</th></tr>';
                $query = 'SELECT * FROM ' . __CLASS__ . ';';             
                if($result = sqlite_query($db, $query))
                {
                    while($entry = sqlite_fetch_array($result))
                    {
                        echo '<tr><td>'.$entry['id'].'</td><td>'.$entry['target_id'].'</td><td>'.$entry['target_type'].'</td><td>'.$entry['creation'].'</td><td>'.$entry['message'].'</td></tr>';
                    }
                }
                echo '</table>';

                sqlite_close($db);
            }
        }
    }
?>
