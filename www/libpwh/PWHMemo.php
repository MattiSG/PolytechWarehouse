<?php
    class PWHMemo
    {
        private $_Text;
        
        public function __construct()
        {
            $this->_Text = "";
        }
        
        public function Html()
        {
            $strbuf = '<div class="memo"><div class="up"><div></div></div><p><img src="' . IMG_PATH() . 'information.png"/>' . $this->_Text . '</p>';
		    return $strbuf . '<div class="down"><div></div></div></div>'; 
        }
        
        public function SetText($text)
        {
            $this->_Text = $text;
        }
    }
?>
