<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Artschools</title>
        <link rel="shortcut icon" type="image/png" href="public/images/favicon.png"/>
        <!--meta-->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Artschools est un site qui répertorie des artistes en cours d'études, ainsi que leurs établissements">

        <meta name="twitter:title" content="Artschools">
        <meta name="twitter:description" content="Artschools est un site qui répertorie des artistes en cours d'études, ainsi que leurs établissements">
        <meta name="twitter:image" content="images/logo.png">

        <meta property="og:title" content="Artschools" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="julienchemin.fr/projet5/index.php" />
        <meta property="og:image" content="images/logo.png" />
        <meta property="og:description" content="Artschools est un site qui répertorie des artistes en cours d'études, ainsi que leurs établissements" /> 
        <meta property="og:site_name" content="Artschools" />
        <!--css-->
        <?php
        require 'view/style.php';
        ?>
        <!--font awesome-->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
        <!--Google font-->
        <link href="https://fonts.googleapis.com/css?family=Asap|Slabo+27px&display=swap" rel="stylesheet">
    </head>
    <body>
        <?php
        if (!SITE_MAINTENANCE) {
            require 'navbar.php';
        }

        echo '<main>' . $content . '</main>';

        if (!SITE_MAINTENANCE) {
            require 'footer.php';
            require 'jScript.php';
        }
        ?>
    </body>
</html>
