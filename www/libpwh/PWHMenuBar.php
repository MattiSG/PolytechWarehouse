<?php
    class PWHMenuBar
    {
        private $_Type;
        private $_User;
                
        public function Html()
        {               
            $strbuf = '<div id="menu"><ul>';
            $strbuf .= '<li><a href="index.php"><img src="img/user_suit"/>' . $this->_User . '</a></li>';
            
            if($this->_Type == TEACHER_TYPE)
            {
                $strbuf .= '<li><a href="index.php?page=teacher_list_subjects&see=less"><img src="img/book_open.png"/>Mati&egrave;res</a></li>';
                $strbuf .= '<li><a href="index.php?page=teacher_list_groups_deliveries"><img src="img/package.png"/>Tableau de bord</a></li>';
                $strbuf .= '<li><a href="index.php?page=teacher_list_groups"><img src="img/group.png"/>Promos</a></li>'; 
                $strbuf .= '<li><a href="index.php?page=teacher_email_groups"><img src="img/email.png"/>Mailing</a></li>';
            }
            else if($this->_Type == STUDENT_TYPE)
            {
                $strbuf .= '<li><a href="index.php?page=student_list_deliveries"><img src="img/package.png"/>Mes rendus</a></li>';
                $strbuf .= '<li><a href="export/index.php?id='. $_SESSION['id'] . '&amp;action=show_cal"><img src="img/calendar.png"/>iCalendar</a></li>';
                $strbuf .= '<li><a href="index.php?page=student_email_groups"><img src="img/email.png"/>Mailing</a></li>';
            }
            else if($this->_Type == ADMIN_TYPE)
            {
                $strbuf .= '<li><a href="index.php?page=admin_database_management"><img src="img/database.png"/>BD-SQLite</a></li>';
                $strbuf .= '<li><a href="index.php?page=admin_log_management"><img src="img/page_edit.png"/>Log-SQLite</a></li>';
            }
            $strbuf .= '<li><a href="index.php?page=logout"><img src="img/logout.png"/>D&eacute;connexion</a></li>';
            return $strbuf . '</ul></div>';
        }
        
        public function EmptyHtml()
        {
            return '<div id="menu"></div>';
        }
        
        public function SetType($type)
        {
            $this->_Type = $type;
        }
        
        public function SetUser($user)
        {
            $this->_User = $user;
        }
    }
?>
