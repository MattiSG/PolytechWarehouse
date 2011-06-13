<?php
    abstract class PWHEntity
    {                         
        protected $_ID;
         
        public function __construct()
        {
            $this->_ID = -1;
        }
        
        public function __toString()
        {
            return '[id:' . $this->_ID . ']';
        }
          
        public function GetID()
        {
            return $this->_ID;
        }
        
        public function IsPersistent()
        {
            return $this->_ID > 0;
        }
                
        abstract public function Create($overwrite);
        abstract public function Read($id);
        abstract public function Update();
        abstract public function Delete();
        
        public static function NewInstance($typeName) 
        {
            $class = new ReflectionClass($typeName);
            return $class->newInstance();
        }
        
        public static function ListAll($entityName)
        {
            if ($db = @sqlite_open(DATABASE_FILE())) 
            {
                if($result = @sqlite_query($db, "SELECT id FROM $entityName;"))
                {
                    $entities = array();
                    while($tuple = sqlite_fetch_array($result))
                    {
                        $entity = self::NewInstance($entityName);
                        $entity->Read((int)$tuple['id']);
                        array_push($entities, $entity);
                    }
                    sqlite_close($db);                  
                    return $entities;
                }
                else
                {
                    throw new Exception(PWHEQUERY . ": Liste des entit&eacute;s $entityName");
                }
            }
            else
            {
                throw new Exception(PWHEACCES);
            }
        }
        
        public static function Exist($entityName)
        {
            if ($db = @sqlite_open(DATABASE_FILE())) 
            {
                if($result = @sqlite_query($db, "SELECT COUNT(id) AS count FROM $entityName;"))
                {
                    $result = sqlite_fetch_single($result);
                    sqlite_close($db);
                    return $result['count'] > 0;
                }
                else
                {
                    throw new Exception(PWHEQUERY . ": Existence d'entit&eacute;s $entityName");
                }
            }
            else
            {
                throw new Exception(PWHEACCES);
            }
        }
        
        public static function Valid($entityName, $id)
        {
            if ($db = @sqlite_open(DATABASE_FILE())) 
            {
                if($result = @sqlite_query($db, "SELECT COUNT(id) AS count FROM $entityName WHERE id = '" . sqlite_escape_string($id) . "';"))
                {
                    $result = sqlite_fetch_single($result);
                    sqlite_close($db);
                    return $result['count'] > 0;
                }
                else
                {
                    throw new Exception(PWHEQUERY . ": Entit&eacute; inexistante");
                }
            }
            else
            {
                throw new Exception(PWHEACCES);
            }
        }
    }
?>
