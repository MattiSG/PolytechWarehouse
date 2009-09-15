<?php
    class PWHActiveDeliveriesTable
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
		    $strbuf .= '<tr><th>Nom</th><th>Groupe</th><th>Rendu</th><th>Extra</th></tr>';
		    if(count($this->_Undelivered) + count($this->_Delivered) == 0)
            {
                    $strbuf .= '<tr><td colspan="4">Aucun rendu actif</td></tr>';
            }
            else
            {
                $id=1;
                foreach($this->_Undelivered as $delivery)
                { 
                    $work = new PWHWork();
                    $work->Read($delivery->GetWorkID());
                    $subject = new PWHSubject();
                    $subject->Read($work->GetSubjectID());
                    
                    $currentDate = date('Y-m-d H:i:s');
                    
                    $deliveryDaysLeft = dateDiff($currentDate, $delivery->GetDeadline());
                    if($work->IsSimple() || $work->GetGroupMax() == 1)
                    {
                        $groupDaysLeft = 0;
                    }
                    else
                    {
                        $groupDaysLeft = dateDiff($currentDate, $delivery->GetGroupCompositionDeadline());
                    }
                    
                    $extraTimeLeft = 0;
                    if($work->GetExtraTime() > 0)
                    {
                        if($delivery->IsExtraTimeUsed($currentDate))
                        {
                            $extraTimeLeft = $deliveryDaysLeft + $work->GetExtraTime() * 86400;
                        }
                        else
                        {
                            $extraTimeLeft = $work->GetExtraTime() * 86400;
                        }
                    }
                    
                    $class = ' class="active_work"';
                    if($delivery->IsExtraTimeUsed(date("Y-m-d H:i:s")))
                    {
                        $class = ' class="extra_time_line"';
                    } 
                       
                    $strbuf .= '<tr' . $class . '>';
                    $strbuf .= '<td><a href="index.php?page=student_display_delivery&amp;subject_id=' . $subject->GetID() . '&amp;work_id=' . $work->GetID() . '&amp;delivery_id=' . $delivery->GetID() . '"><img src="img/bullet_go.png"/>' . $subject->GetName() .' / ' . $work->GetName() . '</a></td>';
                    $strbuf .= '<td id="delivery_days_left' . $id . '">' . ($groupDaysLeft < 0 ? 0 : $groupDaysLeft) . '</td>';
                    $strbuf .= '<td id="extra_time_left' . $id . '">' . ($deliveryDaysLeft < 0 ? 0 : $deliveryDaysLeft) . '</td>';
                    $strbuf .= '<td id="group_days_left' . $id . '">' . $extraTimeLeft . '</td>';
                    $strbuf .= '</tr>';
                    $id++;
                }
                
                foreach($this->_Delivered as $delivery)
                { 
                    $work = new PWHWork();
                    $work->Read($delivery->GetWorkID());
                    $subject = new PWHSubject();
                    $subject->Read($work->GetSubjectID());
                    
                    $currentDate = date('Y-m-d H:i:s');
                    
                    $deliveryDaysLeft = dateDiff($currentDate, $delivery->GetDeadline());
                    if($work->IsSimple() || $work->GetGroupMax() == 1)
                    {
                        $groupDaysLeft = 0;
                    }
                    else
                    {
                        $groupDaysLeft = dateDiff($currentDate, $delivery->GetGroupCompositionDeadline());
                    }
                    
                    $extraTimeLeft = 0;
                    if($work->GetExtraTime() > 0)
                    {
                        if($delivery->IsExtraTimeUsed($currentDate))
                        {
                            $extraTimeLeft = $deliveryDaysLeft + $work->GetExtraTime() * 86400;
                        }
                        else
                        {
                            $extraTimeLeft = $work->GetExtraTime() * 86400;
                        }
                    }
                    
                    $strbuf .= '<tr class="delivered_line">';
                    $strbuf .= '<td><a href="index.php?page=student_display_delivery&amp;subject_id=' . $subject->GetID() . '&amp;work_id=' . $work->GetID() . '&amp;delivery_id=' . $delivery->GetID() . '"><img src="img/bullet_go.png"/>' . $subject->GetName() .' / ' . $work->GetName() . '</a></td>';
                    $strbuf .= '<td id="delivery_days_left' . $id . '">' . ($groupDaysLeft < 0 ? 0 : $groupDaysLeft) . '</td>';
                    $strbuf .= '<td id="extra_time_left' . $id . '">' . ($deliveryDaysLeft < 0 ? 0 : $deliveryDaysLeft) . '</td>';
                    $strbuf .= '<td id="group_days_left' . $id . '">' . $extraTimeLeft . '</td>';
                    $strbuf .= '</tr>';
                    $id++;   
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
