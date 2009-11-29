<?php
    class PWHDateSelector
    {   
        // Date at format YYYY-MM-DD
        public function Html($name, $timestamp)
        {
            if($timestamp == "")
            {
                $timestamp = date("Y-m-d");
                $timestamp .= " 23:59:00";
            }
            
            $timestamp = explode(' ', $timestamp);
            $date = explode('-', $timestamp[0]);
            $time = explode(':', $timestamp[1]);
            
            $strbuf = '<select name="' . $name . '_day" id="' . $name . '_day">';
            for($i=1; $i <= 31; ++$i)
            {
                $strbuf .= '<option ';
                if($i < 10)
                {
                    if('0'.$i == $date['2'])
                    {
                        $strbuf .= 'selected="selected" ';
                    }
                    $strbuf .= 'value="0' . $i . '">0';   
                }
                else
                {
                    if($i == $date['2'])
                    {
                        $strbuf .= 'selected="selected" ';
                    }
                    $strbuf .= 'value="' . $i . '">';
                }
                $strbuf .= $i . '</option>';
            }
            $strbuf .= '</select>';
            
            
            $strbuf .= '<select name="' . $name . '_month" id="' . $name . '_month">';
            
            $strbuf .= '<option ';
            if($date['1'] == "01")
            {
                $strbuf .= 'selected="selected" ';
            }
            $strbuf .= 'value="01">Janvier</option>';
            
            $strbuf .= '<option ';
            if($date['1'] == "02")
            {
                $strbuf .= 'selected="selected" ';
            }
            $strbuf .= 'value="02">F&eacute;vrier</option>';
            
            $strbuf .= '<option ';
            if($date['1'] == "03")
            {
                $strbuf .= 'selected="selected" ';
            }
            $strbuf .= 'value="03">Mars</option>';
            
            $strbuf .= '<option ';
            if($date['1'] == "04")
            {
                $strbuf .= 'selected="selected" ';
            }
            $strbuf .= 'value="04">Avril</option>';
            
            $strbuf .= '<option ';
            if($date['1'] == "05")
            {
                $strbuf .= 'selected="selected" ';
            }
            $strbuf .= 'value="05">Mai</option>';
            
            $strbuf .= '<option ';
            if($date['1'] == "06")
            {
                $strbuf .= 'selected="selected" ';
            }
            $strbuf .= 'value="06">Juin</option>';
            
            $strbuf .= '<option ';
            if($date['1'] == "07")
            {
                $strbuf .= 'selected="selected" ';
            }
            $strbuf .= 'value="07">Juillet</option>';
            
            $strbuf .= '<option ';
            if($date['1'] == "08")
            {
                $strbuf .= 'selected="selected" ';
            }
            $strbuf .= 'value="08">Ao&ucirc;t</option>';
            
            $strbuf .= '<option ';
            if($date['1'] == "09")
            {
                $strbuf .= 'selected="selected" ';
            }
            $strbuf .= 'value="09">Septembre</option>';
            
            $strbuf .= '<option ';
            if($date['1'] == "10")
            {
                $strbuf .= 'selected="selected" ';
            }
            $strbuf .= 'value="10">Octobre</option>';
            
            $strbuf .= '<option ';
            if($date['1'] == "11")
            { 
                $strbuf .= 'selected="selected" ';
            }
            $strbuf .= 'value="11">Novembre</option>';
            
            $strbuf .= '<option ';
            if($date['1'] == "12")
            {
                $strbuf .= 'selected="selected" ';
            }
            $strbuf .= 'value="12">D&eacute;cembre</option>';
            $strbuf .= '</select>';
            
            $year = (int)date('Y');
            $strbuf .= '<select name="' . $name . '_year" id="' . $name . '_year">';
            for($i=$year; $i <= $year+5; ++$i)
            {
                $strbuf .= '<option ';
                if((int)$date[0] == $i)
                {
                    $strbuf .= 'selected="selected" ';
                }
                $strbuf .= 'value="' . $i . '">' . $i . '</option>';   
            }
            $strbuf .= '</select>';
            
            $strbuf .= ' &agrave; <input type="text" size="2" name="' . $name . '_hour" id="' . $name . '_hour" value="'. $time[0] .'" /> h ';
            $strbuf .= '<input type="text" size="2" name="' . $name . '_minute" id="' . $name . '_minute" value="'. $time[1] .'" />';
            
            return $strbuf;
        }  
    }
?>
