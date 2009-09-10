<?php
    if($_SESSION['type'] == STUDENT_TYPE)
    {
        previousPage("student_home");
    }
    else if($_SESSION['type'] == TEACHER_TYPE)
    {
        previousPage("teacher_home");
    }
    else if($_SESSION['type'] == ADMIN_TYPE)
    {
        previousPage("admin_home");
    }
?>  
<fieldset>
    <legend>credits</legend>
    <?php
        $help = new PWHHelp();
        echo $help->Html("#");
    ?>
    <div class="section">
        <div class="list">
            <ul>
                <li><img src="img/user_gray.png"/>Auteur: Karim Matrah</li>
                <li><img src="img/user_suit.png"/>Encadrants: S&eacute;bastien Mosser & Marc Gaetano</li>
                <li><a href="http://www.famfamfam.com"><img src="img/palette.png"/>Optimis&eacute; par Silk Icons &copy;</a></li>
                <li><a href="http://www.polytechnice.fr"><img src="img/polytech.png"/>Polytech'Nice-Sophia</a></li>
                <li><a href="http://www.unice.fr"><img src="img/unice.png"/>Universit&eacute; Nice-Sophia Antipolis</a></li>
            </ul>
        </div>
    </div>
</fieldset>
