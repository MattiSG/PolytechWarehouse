<?php
    class PWHSubjectCreationMemo
    {
        private $_Name;
        private $_Teachers;
        
        public function __construct()
        {
            $this->_Name = "";
            $this->_Teachers = array();
        }
        
        public function Html()
        {
            $strbuf = '<div class="memo"><div class="up"><div></div></div><p><img src="' . IMG_PATH() . 'information.png"/>Nom de la mati&egrave;re : ' . $this->_Name . '</p>';
		    if(count($this->_Teachers) > 0)
		    {
		        $strbuf .= '<p>Enseignants responsables : ';
		        foreach($this->_Teachers as $id)
		        {
		            $teacher = new PWHTeacher();
		            $teacher->Read($id);
		            $strbuf .= $teacher->GetLastName() . ' ' . $teacher->GetFirstName() . ', ';
		        }
		        $strbuf = substr($strbuf, 0, strlen($strbuf)-2);
		        $strbuf .= '</p>';
		    }       
		    
		    return $strbuf . '<div class="down"><div></div></div></div>'; 
        }
        
        public function SetName($name)
        {
            $this->_Name = $name;
        }
        
        public function SetTeachers($teachers)
        {
            $this->_Teachers = $teachers;
        }
    }
?>
    
