<?php
    class PWHDateTranslator
    { 
        const DATE = 1;
        const TIME = 2;
        const DATE_AND_TIME = 3;
        
        public function Html($timestamp, $mode)
        {
            if($timestamp == "")
            {
                return "";
            }
            
            $date = substr($timestamp, 0, 10);
            $time = substr($timestamp, 11, strlen($timestamp));
            
            $date = explode('-', $date);
            $time = explode(':', $time);
            $strbuf = "";
            
            
            if($mode == self::DATE || $mode == self::DATE_AND_TIME)
            {
                $strbuf .= $date[2] . ' ';
                switch((int)$date[1])
                {
                    case 1:
                        $strbuf .= "Janvier "; break;
                    case 2:
                        $strbuf .= "F&eacute;vrier "; break;
                    case 3:
                        $strbuf .= "Mars "; break;
                    case 4:
                        $strbuf .= "Avril "; break;
                    case 5:
                        $strbuf .= "Mai "; break;
                    case 6:
                        $strbuf .= "Juin "; break;
                    case 7:
                        $strbuf .= "Juillet "; break;
                    case 8:
                        $strbuf .= "Ao&ucirc;t "; break;
                    case 9:
                        $strbuf .= "Septembre "; break;
                    case 10:
                        $strbuf .= "Octobre "; break;
                    case 11:
                        $strbuf .= "Novembre "; break;
                    case 12:
                        $strbuf .= "D&eacute;cembre "; break;
               }
               $strbuf .= $date[0];
           }
           
           
           if($mode == self::DATE_AND_TIME)
           {
                $strbuf .= ' &agrave ';
           }
           
           if($mode == self::TIME || $mode == self::DATE_AND_TIME)
           {
                $strbuf .= $time[0] . "h" . $time[1];
           }
           
           return $strbuf;
        }
    }
?>
