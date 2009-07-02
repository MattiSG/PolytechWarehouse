<html>
    <body>
        <?php
            $GLOBALS['PWH_PATH'] = "../";
            require_once("../libpwh/PWHGlobals.php");
            require_once(LIB_PATH() . "PWHStudent.php");
            require_once(LIB_PATH() . "PWHGroup.php");
            require_once(LIB_PATH() . "PWHWork.php");
            require_once(LIB_PATH() . "PWHSubject.php");
            
            $student1 = new PWHStudent('gentile', 'matrah@polytech.unice.fr');          
            $student1->Create(true);
            
            $student2 = new PWHStudent('merzouga','merzouga@polytech.unice.fr');
            $student2->Create(true);
            
            $student3 = new PWHStudent('trepier','benabu@polytech.unice.fr');
            $student3->Create(true);
            
            $student4 = new PWHStudent('arnoux','arnoux@polytech.unice.fr');
            $student4->Create(true);
            
            
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
                     
            
            $group2 = new PWHGroup('SI4');
            $group2->ReadFromFile('../si4.promo');
            $group2->Create(true);
            
            $groupX = new PWHGroup(null);
            $groupX->Read(1);
            echo $groupX . '<br/>';
            
            
            $work1 = new PWHWork('td1', 0, 1, "*", 1, 1);          
            $work1->Create(true);
            
            $work2 = new PWHWork('td2', 2, 3, "*", 2, 2);
            $work2->Create(true);
            
            $work3 = new PWHWork('td3', 1, 2, "*", 2, 4);
            $work3->Create(true);
            
            $work4 = new PWHWork('td4', 0, 10, "*", 1, 1);
            $work4->Create(true);
            
            
            $subject1 = new PWHSubject('AppliRep');
            $works = array($work1->GetID(), $work2->GetID(), $work3->GetID());
            $subject1->AddWorks($works);
            $subject1->Create(true);
            echo $subject1 . '<br/>';
            
            $subject1->SetName('ProgConc');
            $subject1->AddWorks(array($work4->GetID()));
            $subject1->RemoveWorks(array($work2->GetID()));
            $subject1->Update();
            echo $subject1 . '<br/>';
            
            
            $workX = $subject1->GetWork(1);
            echo $workX . '<br/>';
            $workX->SetName("td1_v2");
            $workX->Update();
            
            $workX2 = new PWHWork(null, null, null, null, null, null);
            $workX2->Read(1);
            echo $workX2 . '<br/>';
            
            $subjectX = new PWHSubject(null);
            $subjectX->Read(1);
            echo $subjectX . '<br/>';
                     
        ?>
    </body>
</html>
