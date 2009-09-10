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
        <h4>Auteur</h4>
        <div class="section">
        <div class="list">
            <ul>
                <li><img src="img/user_gray.png"/>Karim Matrah</li>
                <li><a href="mailto:kimious@free.fr"><img src="img/email.png"/>kimious@free.fr</a></li>
                <li><a href="mailto:matrah@polytech.unice.fr"><img src="img/email.png"/>matrah@polytech.unice.fr</a></li>
            </ul>
        </div>
    </div>
    <h4>Encadrants</h4>      
    <div class="section">
        <div class="list">
            <ul>
                <li><img src="img/user_suit.png"/>S&eacute;bastien Mosser</li>
                <li><a href="mailto:mosser@polytech.unice.fr"><img src="img/email.png"/>mosser@polytech.unice.fr</a></li>
                <li><img src="img/user_suit.png"/>Marc Gaetano</li>
                <li><a href="mailto:gaetano@polytech.unice.fr"><img src="img/email.png"/>gaetano@polytech.unice.fr</a></li>
            </ul>
        </div>
    </div>    
    <div class="section">
        <div class="list">
            <ul>
                <li><a href="http://www.famfamfam.com"><img src="img/palette.png"/>Optimis&eacute; par Silk Icons &copy;</a></li>
                <li><a href="http://www.polytechnice.fr"><img src="img/polytech.png"/>Polytech'Nice-Sophia</a></li>
                <li><a href="http://www.unice.fr"><img src="img/unice.png"/>Universit&eacute; Nice-Sophia Antipolis</a></li>
            </ul>
        </div>
    </div>
</fieldset>
