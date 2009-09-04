<?php
    
    /* 
     * An entity manager with lazy loading 
     */
     
    class PWHEntityManager
    {
        private $_TypeName;
        private $_Entities;
        private $_EntitiesAdded;
        private $_EntitiesRemoved;        
                
        public function __construct($typeName)
        {
            $this->_TypeName = $typeName;
            $this->_Entities = array();
            $this->_EntitiesAdded = array();
            $this->_EntitiesRemoved = array();
        }
        
        public function AddEntities($entities)
        {
            $this->_Entities = array_merge($this->_Entities, (array)$entities);
            $this->_EntitiesAdded = array_merge($this->_EntitiesAdded, (array)$entities); 
        }
                
        public function RemoveEntities($entities)
        {
            foreach($entities as $entityToRemove)
            {
                $i = 0;
                while($i < count($this->_Entities))
                {
                    if(is_int($this->_Entities[$i]) && $this->_Entities[$i] == $entityToRemove)
                    {
                        array_splice($this->_Entities, $i, 1);
                        array_push($this->_EntitiesRemoved, $entityToRemove);
                    }
                    else if(!is_int($this->_Entities[$i]) && $this->_Entities[$i]->GetID() == $entityToRemove)
                    {
                        array_splice($this->_Entities, $i, 1);
                        array_push($this->_EntitiesRemoved, $entityToRemove);
                    }
                    $i++;
                }
            }
        }   
        
        public function GetEntity($id)
        {
            $i = 0;
            while($i < count($this->_Entities))
            {
                if(is_int($this->_Entities[$i]) && $this->_Students[$i] == $id)
                {
                    $entity = PWHEntity::NewInstance($this->_TypeName);
                    $entity->Read($id);
                    $this->_Entities[$i] = $entity;
                    return $entity;
                }
                else if(!is_int($this->_Entities[$i]) && $this->_Entities[$i]->GetID() == $id)
                {
                    return $this->_Entities[$i];
                }
                $i++;
            }
            return false;
        }
        
        public function GetEntities()
        {
            $i = 0;
            $entities = array();
            while($i < count($this->_Entities))
            {
                if(is_int($this->_Entities[$i]))
                {
                    $entity = PWHEntity::NewInstance($this->_TypeName);
                    $entity->Read($this->_Entities[$i]);
                    $this->_Entities[$i] = $entity;
                    array_push($entities, $entity);
                }
                else
                {
                    array_push($entities, $this->_Entities[$i]);
                }
                $i++;
            }
            return $entities;
        }
        
        public function GetEntitiesAdded()
        {
            return $this->_EntitiesAdded;
        }
        
        public function GetEntitiesRemoved()
        {
            return $this->_EntitiesRemoved;
        }
        
        public function EntityExists($id)
        {
            $i = 0;
            while($i < count($this->_Entities))
            {
                
                if(is_int($this->_Entities[$i]) && $this->_Entities[$i] == $id)
                {
                    return true;
                    
                }
                else if(!is_int($this->_Entities[$i]) && $this->_Entities[$i]->GetID() == $id)
                {
                    return true;
                    
                }
                $i++;
            }
            return false;
        }
        
        public function IsEmpty()
        {
            return count($this->_Entities) == 0;
        }
        
        public function Size()
        {
            return count($this->_Entities);
        }
        
        public function Clear()
        {
            $this->_Entities = array();
        }
        
        public function Flush()
        {
            $this->_EntitiesAdded = array();
            $this->_EntitiesRemoved = array();
        }
    }
?>
