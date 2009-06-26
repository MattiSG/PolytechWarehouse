<html>
    <body>
        <?php
            $GLOBALS['PWH_PATH'] = "../";
            require_once("../libpwh/PWHGlobals.php");
            require_once(LIB_PATH() . "PWHStudent.php");
            require_once(LIB_PATH() . "PWHGroup.php");
            
            $student1 = new PWHStudent('matrah', 'matrah@polytech.unice.fr');          
            $student1->Create(true);
            
            $student2 = new PWHStudent('merzouga','merzouga@polytech.unice.fr');
            $student2->Create(true);
            
            $student3 = new PWHStudent('benabu','benabu@polytech.unice.fr');
            $student3->Create(true);
            
            $student4 = new PWHStudent('arnoux','arnoux@polytech.unice.fr');
            $student4->Create(true);
            
            PWHStudent::Table();
            
            $group1 = new PWHGroup('SI3');
            $students = array($student1->GetID(), $student2->GetID(), $student3->GetID());
            $group1->AddStudents($students);
            $group1->Create(true);
            echo $group1 . '<br/>';
            
            $group1->SetName('SI5');
            $group1->AddStudents(array($student4->GetID()));
            $group1->RemoveStudents(array($student2->GetID()));
            $group1->Update();
            echo $group1 . '<br/>';
            
            
            $studentX = $group1->GetStudent(1);
            echo $studentX . '<br/>';
            $studentX->SetLogin("kimious");
            $studentX->Update();
            
            $studentX2 = new PWHStudent(null, null);
            $studentX2->Read(1);
            echo $studentX2 . '<br/>';
                     
            PWHGroup::Table();
            
            $group2 = new PWHGroup('SI4');
            $group2->ReadFromFile('../si4.promo');
            $group2->Create(true);
            
            $groupX = new PWHGroup(null);
            $groupX->Read(1);
            echo $groupX . '<br/>';
            
            PWHGroup::Table();         
        ?>
    </body>
</html>
