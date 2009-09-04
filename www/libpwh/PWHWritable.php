<?php
    interface PWHWritable
    {
        public function GetPath();
        public function CreateDirectory();
        public function RemoveDirectory();
        public function RenameDirectory();
    }
?>
