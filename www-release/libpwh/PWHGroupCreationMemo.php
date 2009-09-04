<?php
    class PWHGroupCreationMemo
    {
        private $_Name;
        private $_Parent;
        private $_Students;
        
        public function __construct()
        {
            $this->_Name = "";
            $this->_Parent = -1;
            $this->_Students = array();
        }
        
        public function Html()
        {
            $strbuf = '<div class="memo"><div class="up"><div></div></div><p><img src="' . IMG_PATH() . 'information.png"/>Nom du groupe : ' . $this->_Name . '</p>';
            if($this->_Parent == -1)
            {
                $name = "Aucun parent sp&eacute;cifi&eacute;";
            }
            else
            {
                $parent = new PWHGroup();
                $parent->Read($this->_Parent);
                $name = $parent->GetName();
            }
            $strbuf .= '<p>Groupe parent : ' . $name . '</p>';
            
	        $strbuf .= '<p>Etudiants : ';
	        foreach($this->_Students as $id)
	        {
	            $student = new PWHStudent();
	            $student->Read($id);
	            $strbuf .= $student->GetLastName() . ' ' . $student->GetFirstName() . ', ';
	        }
	        
	        if(count($this->_Students) == 0)
	        {
	            $strbuf .= "-";
	        }
	        else
	        {
	            $strbuf = substr($strbuf, 0, strlen($strbuf)-2);
	        }
	        
	        $strbuf .= '</p>';      
		    
		    return $strbuf . '<div class="down"><div></div></div></div>'; 
        }
        
        public function SetName($name)
        {
            $this->_Name = $name;
        }
        
        public function SetParent($parent)
        {
            $this->_Parent = $parent;
        }
        
        public function SetStudents($students)
        {
            $this->_Students = $students;
        }
    }
?>
    
