<?php
    class PWHDate
    {
        private $_Year;
        private $_Month
        private $_Day;
    }
    
    public function __construct($year, $month, $day)
    {
        $this->_Year = $year;
        $this->_Month = $month;
        $this->_Day = $day;
    }
    
    public function SetYear($year)
    {
        $this->_Year = $year;
    }
    
    public function GetYear()
    {
        return $this->_Year;
    }
    
    public function SetMonth($month)
    {
        $this->_Month = $month;
    }
    
    public function GetMonth()
    {
        return $this->_Month;
    }
    
    public function SetDay($day)
    {
        $this->_Day = $day;
    }
    
    public function GetDay()
    {
        return $this->_Day;
    }
    
    public __toString()
    {
        return $day . " - " . $month . " - " . $year;
    }
?>
