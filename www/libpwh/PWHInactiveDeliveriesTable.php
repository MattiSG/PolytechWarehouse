<?php
    class PWHInactiveDeliveriesTable
    {
        private $_Undelivered;
        private $_Delivered;
        
        public function __construct()
        {
            $this->_Undelivered = array();
            $this->_Delivered = array();
        }
        
        public function HTML()
        {
            
            $strbuf = '<table class="colored_table underlined_table">';
		    $strbuf .= '<tr><th>Nom</th><th>Date de fin</th></tr>';
		    if(count($this->_Undelivered) + count($this->_Delivered) == 0)
            {
                $strbuf .= '<tr><td colspan="2">Aucun rendu inactif</td></tr>';
            }
            else
            {
                $dateTranslator = new PWHDateTranslator();
                foreach($this->_Undelivered as $delivery)
                { 
                    $work = new PWHWork();
                    $work->Read($delivery->GetWorkID());
                    $subject = new PWHSubject();
                    $subject->Read($work->GetSubjectID());
                    
                    $strbuf .= '<tr class="undelivered_line">';
                    $strbuf .= '<td><a href="index.php?page=student_display_delivery&amp;subject_id=' . $subject->GetID() . '&amp;work_id=' . $work->GetID() . '&amp;delivery_id=' . $delivery->GetID() . '"><img src="img/bullet_go.png"/>' . $subject->GetName() . ' / ' . $work->GetName() . '</a></td>';
                    $strbuf .= '<td>' . $dateTranslator->Html($delivery->GetDeadline(), PWHDateTranslator::DATE_AND_TIME) . '</td>';
                    $strbuf .= '</tr>';
                }
                
                foreach($this->_Delivered as $delivery)
                { 
                    $work = new PWHWork();
                    $work->Read($delivery->GetWorkID());
                    $subject = new PWHSubject();
                    $subject->Read($work->GetSubjectID());
                    
                    $strbuf .= '<tr class="unactive_work">';
                    $strbuf .= '<td><a href="index.php?page=student_display_delivery&amp;subject_id=' . $subject->GetID() . '&amp;work_id=' . $work->GetID() . '&amp;delivery_id=' . $delivery->GetID() . '"><img src="img/bullet_go.png"/>' . $subject->GetName() . ' / ' . $work->GetName() . '</a></td>';
                    $strbuf .= '<td>' . $dateTranslator->Html($delivery->GetDeadline(), PWHDateTranslator::DATE_AND_TIME) . '</td>';
                    $strbuf .= '</tr>';
                }
            }
            echo $strbuf . '</table>';
        }
                
        public function SetDelivered($delivered)
        {
            $this->_Delivered = $delivered;
        }
        
        public function SetUndelivered($undelivered)
        {
            $this->_Undelivered = $undelivered;
        }
    }
?>
