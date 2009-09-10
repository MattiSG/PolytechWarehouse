<?php
    class PWHLegend
    {
        private $_Type;
                
        public function Html()
        {               
            $strbuf = '<div class="legend"><ul>';
            
            if($this->_Type == TEACHER_TYPE)
            {
                $strbuf .= '<li><img src="img/yellow.png"/>En cours</li>';
                $strbuf .= '<li><img src="img/orange.png"/>Periode de tol&eacute;rance</li>';
                $strbuf .= '<li><img src="img/red.png"/>Non publi&eacute;</li>';
                $strbuf .= '<li><img src="img/gray.png"/>Termin&eacute;</li>';
            }
            else if($this->_Type == STUDENT_TYPE)
            {
                $strbuf .= '<li><img src="img/yellow.png"/>A faire</li>';
                $strbuf .= '<li><img src="img/orange.png"/>En retard</li>';
                $strbuf .= '<li><img src="img/green.png"/>Fait</li>';
                $strbuf .= '<li><img src="img/red.png"/>Non fait</li>';
                $strbuf .= '<li><img src="img/gray.png"/>Termin&eacute;</li>';
            }
            return $strbuf . '</ul></div>';
        }
               
        public function SetType($type)
        {
            $this->_Type = $type;
        }
    }
?>
