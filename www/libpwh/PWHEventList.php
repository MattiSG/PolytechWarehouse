<?php
    class PWHEventList
    {
        private $_PersonID;
        private $_Size;
        private $_PersonType;
        
        public function __construct()
        {
            $this->_PersonID = -1;
            $this->_Size = -1;
            $this->_PersonType = -1;
        }
        
        public function Html()
        {
            $dateTranslator = new PWHDateTranslator();
            if($this->_PersonID > 0 && ($this->_Size == PWHEvent::ALL_EVENTS || $this->_Size > 0))
            {
                $events = PWHEvent::GetEvents($this->_PersonID, $this->_PersonType, $this->_Size);
                $strbuf = '<div class="list"><ul>';
                for($i = count($events) -1; $i>=0; $i--)
                {
                    $strbuf .= '<li><h5><img src="img/information.png"/>Le ' . $dateTranslator->Html($events[$i]->GetCreation(), PWHDateTranslator::DATE_AND_TIME) . '</h5>' . $events[$i]->GetMessage() . "</li>";
                }
                return $strbuf . "</ul></div>";
            }   
        }
        
        public function SetPersonID($id)
        {
            $this->_PersonID = $id;
        }
        
        public function SetPersonType($type)
        {
            $this->_PersonType = $type;
        }
        
        public function SetSize($size)
        {
            $this->_Size = $size;
        }
    }
?>
