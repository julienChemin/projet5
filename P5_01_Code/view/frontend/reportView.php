<section id="reportView" class="container">
    <?php
    if ($data['reportExists']) {
        echo '<div class="blockStyleOne"><p>Vous avez déja signalé ce contenu</p></div>';
    } else {
        ?>
        <h1>Signaler du contenu</h1>
        <div>
            <h2>À savoir</h2>
            <ul>
                <li>
                    - Soyez clair et concis
                </li>
                <li>
                    - Vous pouvez joindre une capture d'écran pour appuyer votre signalement (notamment dans le cas d'un vol de contenu)
                </li>
                <li>
                    - Chaque signalement est traité avec attention ! il est donc inutile de signaler plusieurs fois le même contenu
                </li>
                <li>
                    - Les signalements abusifs peuvent être sanctionné d'un avertissement (signaler un contenu en masse, signalement non justifié et répété, etc..)
                </li>
                <li>
                    - Trois avertissements entraine la suspension du compte pour un mois
                </li>
            </ul>
        </div>
        <form id="reportForm" method="POST" action="index.php?action=setReport" enctype="multipart/form-data">
            <input type="hidden" name="elem" value="<?=$_GET['elem']?>">
            <input type="hidden" name="idElem" value="<?=$_GET['id']?>">
            <?php
            !empty($_GET['idPost']) ? $idPost = $_GET['idPost'] : $idPost = $_GET['id'];
            ?>
            <input type="hidden" name="idPost" value="<?=$idPost?>">
            <label for="tinyMCEtextarea"><h2>Pourquoi signalez-vous ce contenu ?</h2></label>
            <textarea id="tinyMCEtextarea" name="tinyMCEtextarea" placeholder="Un signalement sans description ne sera pas pris en compte"></textarea>
            <p>
                <input type="submit" name="submit" value="Signaler">
            </p>
        </form>
        <?php
    }
    ?>
</section>
