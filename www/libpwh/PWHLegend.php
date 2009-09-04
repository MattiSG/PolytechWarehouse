<?php
    class PWHLegend
    {
        private $_Type;
                
        public function Html()
        {               
            $strbuf = '<div class="legend"><ul>';
            
            if($this->_Type == TEACHER_TYPE)
            {
                $strbuf .= '<li><img src="img/yellow"/>En cours</li>';
                $strbuf .= '<li><img src="img/orange"/>Periode de tol&eacute;rance</li>';
                $strbuf .= '<li><img src="img/red"/>Non publi&eacute;</li>';
                $strbuf .= '<li><img src="img/gray"/>Termin&eacute;</li>';
            }
            else if($this->_Type == STUDENT_TYPE)
            {
                $strbuf .= '<li><img src="img/yellow"/>A faire</li>';
                $strbuf .= '<li><img src="img/orange"/>En retard</li>';
                $strbuf .= '<li><img src="img/green"/>Fait</li>';
                $strbuf .= '<li><img src="img/red"/>Non fait</li>';
                $strbuf .= '<li><img src="img/gray"/>Termin&eacute;</li>';
            }
            return $strbuf . '</ul></div>';
        }
               
        public function SetType($type)
        {
            $this->_Type = $type;
        }
    }
?>
