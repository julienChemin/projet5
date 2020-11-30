<section id="reportView" class="container">
    <h1>Signaler un problème</h1>
    <div>
        <h2>À savoir</h2>
        <ul>
            <li>
                - Vous pouvez joindre jusqu'à 5 captures d'écran pour appuyer votre signalement
            </li>
            <li>
                - Vous pouvez aussi parler à un modérateur sur le <a href="https://discord.gg/uDfwPHH">discord</a> pour un contacte direct
            </li>
        </ul>
    </div>
    <form id="reportForm" method="POST" action="index.php?action=setReport" enctype="multipart/form-data">
        <input type="hidden" name="elem" value="other">
        <label for="tinyMCEtextarea"><h2>Que voulez vous signaler ?</h2></label>
        <textarea id="tinyMCEtextarea" name="tinyMCEtextarea" placeholder="Un signalement sans description ne sera pas pris en compte"></textarea>
        <p>
            <input type="submit" name="submit" value="Signaler">
        </p>
    </form>
</section>
