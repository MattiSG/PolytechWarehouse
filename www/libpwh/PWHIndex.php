<?php
    class PWHIndex
    {
        public function Html($link, $active, $secure, $persons)
        {
            $strbuf = '<div class="index">';
            if($secure)
            {
                if($persons == null || count($persons) > 0)
                {
                    $strbuf .= '<a href="javascript:MakeIndex(\'' . $link . '\', \'*\');">*</a> ';
                }
                else
                {
                    $strbuf .= '<a class="empty">*</a>';
                }
                for($letter = 'A'; $letter < 'Z'; ++$letter)
                {
                    if($persons != null)
                    {
                        $filter = $this->FilterPersons($persons, $letter);
                    }
                    
                    if($persons == null || count($filter) > 0)
                    {
                        if(strcmp($letter, $active) == 0)
                        {
                            $strbuf .= '<a class="active" ';
                        }
                        else
                        {
                            $strbuf .= '<a ';
                        }
                        $strbuf .= 'href="javascript:MakeIndex(\'' . $link . '\', \'' . $letter . '\');">' . $letter . '</a>' ;
                    }
                    else
                    {
                        $strbuf .= '<a class="empty">' . $letter . '</a>';
                    }
                }
                
                if($persons != null)
                {
                    $filter = $this->FilterPersons($persons, "Z");
                }
                
                if($persons == null || count($filter) > 0)
                {
                    $strbuf .= '<a href="javascript:MakeIndex(\'' . $link . '\', \'Z\');">Z</a> ';
                }
                else
                {
                    $strbuf .= '<a class="empty">Z</a>';
                }
            }
            else
            {
                if($persons == null || count($persons) > 0)
                {
                    $strbuf .= '<a href="' . $link . '&amp;index=*">*</a> ';
                }
                else
                {
                    '<a class="empty">*</a>';
                }
                for($letter = 'A'; $letter < 'Z'; ++$letter)
                {
                    if($persons != null)
                    {
                        $filter = $this->FilterPersons($persons, $letter);
                    }
                    
                    if($persons == null || count($filter) > 0)
                    {
                        if(strcmp($letter, $active) == 0)
                        {
                            $strbuf .= '<a class="active" ';
                        }
                        else
                        {
                            $strbuf .= '<a ';
                        }
                        $strbuf .= 'href="' . $link . '&amp;index=' . $letter . '">' . $letter . '</a>' ;
                    }
                    else
                    {
                        $strbuf .= '<a class="empty">' . $letter . '</a>';
                    }
                }
                
                if($persons != null)
                {
                    $filter = $this->FilterPersons($persons, "Z");
                }
                
                if($persons == null || count($filter) > 0)
                {
                    $strbuf .= '<a href="' . $link . '&amp;index=Z">Z</a> ';
                }
                else
                {
                    '<a class="empty">Z</a>';
                }
            }
            return $strbuf . '</div>';
        }
        
        public function FilterPersons($persons, $index)
        {
            if($index != "*")
            {
                $i = 0;
                while($i < count($persons))
                {
                    if(strcmp(substr($persons[$i]->GetLastName(), 0, 1), $index) != 0)
                    {
                        array_splice($persons, $i, 1);
                    }
                    else
                    {
                        $i++;
                    }
                }
            }
            return $persons;
        }  
    }
?>
