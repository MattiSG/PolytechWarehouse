<?php
    class PWHEmailTable
    {
        public function Html($persons)
        {
            
            $strbuf = '<table class="colored_table underlined_table">';
		    $strbuf .= '<tr><th>Nom</th><th>Pr&eacute;nom</th><th>Email</th></tr>';
		    if(count($persons) == 0)
            {
                    $strbuf .= '<tr><td colspan="3">Aucun &eacute;l&eacute;ment disponible</td></tr>';
            }
            else
            {
                foreach($persons as $person)
                {
                    $strbuf .= '<tr>';
                    $strbuf .= '<td>' . $person->GetLastName() . '</td>';
                    $strbuf .= '<td>' . $person->GetFirstName(). '</td>';
                    $strbuf .= '<td><a href="mailto:' . $person->GetEmail() . '"><img src="img/email.png"/></a></td>';
                    $strbuf .= '</tr>';
                }
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
