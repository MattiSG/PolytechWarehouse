<?php
    class PWHSummary
    {
        private $_Infos;
        
        public function __construct()
        {
            $this->Infos = array();
        }
        
        public function HTML()
        {
            $strbuf = '<table class="summary">';
            foreach($this->_Infos as $title=>$value)
            {
                $strbuf .= '<tr><td>' . $title . '</td><td>' . $value . '</td></tr>';
            }
            $strbuf .= '</table>';
            return $strbuf;
        }
        
        public function SetInfo($title, $value)
        {
            $this->_Infos[$title] = $value;
        }
    }
?>
