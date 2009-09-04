<?php
    class PWHIOException extends Exception
    {
        const PWHEACCES = 'Echec de la connection avec la base de donn&eacute;es';
        const PWHEPRMNOTFOUND = "Echec de l'ouverture du fichier .promo";
        const PWHEFILEEXIST = "Tentative de cr&eacute;ation d'un r&eacute;pertoire d&eacute;j&agrave; existant";
        const PWHEFILENOTEXISTS = "Tentative de suppression d'un r&eacute;pertoire inexistant";
    }
?>
