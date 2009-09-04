<?php
    class PWHPersonTable
    {
        public function Html($persons, $submit)
        {
            
            $strbuf = '<table class="colored_table underlined_table">';
		    $strbuf .= '<tr><th>Nom</th><th>Pr&eacute;nom</th><th>S&eacute;lection</th></tr>';
		    if(count($persons) == 0)
            {
                    $strbuf .= '<tr><td colspan="3">Aucun &eacute;l&eacute;ment disponible</td></tr>';
            }
            else
            {
                $id = 0;
                foreach($persons as $person)
                {
                    $class = "";
                    if($id%2 == 1)
                    {
                        $class = ' class="alt"';
                    }
                    $strbuf .= '<tr' . $class . ' onclick="CheckBox(\'' . $id . '\');">';
                    $strbuf .= '<td>' . $person->GetLastName() . '</td>';
                    $strbuf .= '<td>' . $person->GetFirstName(). '</td>';
                    $strbuf .= '<td><input onclick="CheckForm(\'' . $id . '\');" id="cb_' . $id . '" type="checkbox" name="' . $person->GetID() . '" /></td>';
                    $strbuf .= '</tr>';
                    $id++;
                }
                $strbuf .= '<tr class="submit_line">';
                $strbuf .= '<td></td>';
                $strbuf .= '<td></td>';
                $strbuf .= '<td><input type="submit" id="next" value="' . $submit . '"/></td>';
                $strbuf .= '</tr>';
            }        
	        return $strbuf . '</table>';
        }
        
        public function FilterPersons($persons, $ids)
        {
            $i = 0;
            while($i < count($persons))
            {
                if(in_array($persons[$i]->GetID(), $ids))
                {
                    array_splice($persons, $i, 1);
                }
                else
                {
                    $i++;
                }
            }
            return $persons;
        }
    }
?>
