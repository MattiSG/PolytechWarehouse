<?php
    class PWHDeliverygroupCreationMemo
    {
        private $_Students;
        
        public function __construct()
        {
            $this->_Students = array();
        }
        
        public function Html()
        {
            $strbuf = '<div class="memo"><div class="up"><div></div></div><p><img src="' . IMG_PATH() . 'information.png"/>';
	        $strbuf .= 'Etudiants : ';
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
                
        public function SetStudents($students)
        {
            $this->_Students = $students;
        }
    }
?>
    
