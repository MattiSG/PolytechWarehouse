<?php
    interface Entity
    {
        public function Create($overwrite);
        public function Read($id);
        public function Update();
        public function Delete();
        public function IsMapped();
        public function GetID();
    }
?>
