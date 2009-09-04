<?php   
    class PWHQueryException extends Exception
    {
        const PWHEQUERY = "Echec de l'ex&eacute;cution de la requ&ecirc;te";
        const PWHENOTREG = "Tentative d'utilisation d'une entit&eacute; non enregistr&eacute;";
        const PWHEUPNOTREG = "Tentative de mise &agrave; jour d'une entit&eacute; non enregistr&eacute;";
        const PWHEDELNOTREG = "Tentative de suppression d'une entit&eacute; non enregistr&eacute;";
        const PWHEPRMREAD = "Echec de la cr&eacute;ation de groupe par fichier .promo";
        const PWHENOOWNER = "Tentative de cr&eacute;ation d'une entit&eacute; sans responsable";
    }
?>
