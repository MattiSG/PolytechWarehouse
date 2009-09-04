<?php
    class PWHMetaType
    {
        public static function GetMetaTypes()
        {
            $xml = simplexml_load_file("config/types.xml");
            $metatypes = $xml->xpath('/metatypes/metatype');
            $metatypesBuffer = array();
            foreach($metatypes as $metatype) 
            {
                $metatypeBuffer = array();
                $patternBuffer = array();
                $attributes = $metatype->attributes();
                $metatypeBuffer['id'] = (int)$attributes[0];
                $metatypeBuffer['name'] = (string)$attributes[1];
                array_push($metatypesBuffer, $metatypeBuffer);
                
                $patterns =  $xml->xpath("/metatypes/metatype[@id='" . $metatypeBuffer['id'] . "']/pattern");
                foreach($patterns as $pattern)
                {
                    $attributes = $pattern->attributes();
                    $patternBuffer['id'] = (string)$attributes[0];
                    $patternBuffer['name'] = (string)$attributes[1];
                    array_push($metatypesBuffer, $patternBuffer);
                }
            }
            return $metatypesBuffer;
        }
        
        public static function GetPatterns($id)
        {
            $xml = simplexml_load_file("config/types.xml");
            $buffer = array();
            
            if(preg_match("#^[0-9]+-[0-9]+$#", $id))
            {
                $pattern = $xml->xpath("//*[@id='" . $id . "']");
                array_push($buffer, "#" . (string)$pattern[0] . "#");
            }
            else
            {
                $patterns = $xml->xpath("/metatypes/metatype[@id='" . $id . "']/pattern");
                foreach($patterns as $pattern)
                {
                    array_push($buffer, "#" . (string)$pattern . "#");
                }
            }
            return $buffer;
        }
        
        public static function GetName($id)
        {
            $xml = simplexml_load_file("config/types.xml");
            $metatype = $xml->xpath("//*[@id='" . $id . "']");
            $attributes = $metatype[0]->Attributes();
            
            if(preg_match("#^[0-9]+-[0-9]+$#", $id))
            {
                return (string)$attributes[1] . " (" . substr((string)$metatype[0], 3, strlen((string)$metatype[0])) . ")";
            }
            else
            {
                return (string)$attributes[1];
            }
        }
    }
?>
