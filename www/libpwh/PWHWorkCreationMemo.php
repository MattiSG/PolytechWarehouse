<?php
    class PWHWorkCreationMemo
    {
        private $_Name;
        private $_ExtraTime;
        private $_Size;
        private $_Files;
        private $_GroupMin;
        private $_GroupMax;
        
        public function __construct()
        {
            $this->_Name = "";
            $this->_ExtraTime = "";
            $this->_Size = "";
            $this->_Files = array();
            $this->_GroupMin = "";
            $this->_GroupMax = "";
        }
        
        public function Html()
        {
            $strbuf = '<div class="memo"><div class="up"><div></div></div><p><img src="' . IMG_PATH() . 'information.png"/>Nom du travail : ' . $this->_Name . '</p>';
            $strbuf .= '<p>Membre minimum : ' . $this->_GroupMin . '</p>';
            $strbuf .= '<p>Membre maximum : ' . $this->_GroupMax . '</p>';
            $strbuf .= '<p>Tol&eacute;rance : ' . $this->_ExtraTime . '</p>';
            if($this->_Size == 0)
            {
                $strbuf .= '<p>Taille : -</p>';
            }
            else
            {
                $strbuf .= '<p>Taille : ' . $this->_Size . '</p>';
            }
            
            foreach($this->_Files as $name=>$format)
            {
                $strbuf .= '<p>Fichier [' . $name . '] : Format ' . PWHMetaType::GetName($format) . '</p>';
            }
		    
		    return $strbuf . '<div class="down"><div></div></div></div>'; 
        }
        
        public function SetName($name)
        {
            $this->_Name = $name;
        }
        
        public function SetExtraTime($extraTime)
        {
            $this->_ExtraTime = $extraTime;
        }
        
        public function SetSize($size)
        {
            $this->_Size = $size;
        }
        
        public function SetFormat($format)
        {
            $this->_Format = $format;
        }
        
        public function SetGroupMin($groupMin)
        {
            $this->_GroupMin = $groupMin;
        }
        
        public function SetGroupMax($groupMax)
        {
            $this->_GroupMax = $groupMax;
        }
        
        public function SetFiles($files)
        {
            $this->_Files = $files;
        }
    }
?>
    
