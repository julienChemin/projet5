<section id="reportView" class="container">
    <h1>Signaler du contenu</h1>
    <div>
        <h2>À savoir</h2>
        <ul>
            <li>
                - Vous pouvez joindre jusqu'à 5 captures d'écran pour appuyer votre signalement
            </li>
            <li>
                - Vous pouvez aussi parler à un modérateur sur le <a href="https://discord.gg/SZUF2AMbA3">discord</a> pour un contact direct
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
</section>
