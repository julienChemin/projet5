<section id="moderatReports" class="container">
    <h1 class="fullWidth">Modération des signalements</h1>
    <?php
    if (isset($data['idElem'])) {
        //reports from one comment / post / profile
        ?>
        <article id="content" class="moderatReportsFromElem">
            <div id="btnDeleteReportsFromElem">
                <p>Définir ce contenu comme traité</p>
                <i class="fas fa-check"></i>
            </div>

            <?php
            if ($_GET['elem'] === 'profile') {
                ?>
                <p><a href="index.php?action=userProfile&userId=<?=$data['idElem']?>">Voir le profil concerné</a></p>
                <?php
            } else {
                ?>
                <p><a href="index.php?action=post&id=<?=$data['idElem']?>">Voir le contenu concerné</a></p>
                <?php
            }
            ?>

            <table>
                <thead>
                    <th>Qui a signalé</th>
                    <th>Pourquoi</th>
                    <th>Supprimer</th>
                </thead>
                <tbody></tbody>
            </table>
            <ul id="paging"></ul>
        </article>
        <div id="noContent" class="blockStyleOne">Il n'y a pas de signalement</div>
        <?php
    } else {
        //all reports
        ?>
        <nav class="fullWidth">
            <ul>
                <li id="btnReportProfile">
                    Profile signalées
                </li>   
                <li id="btnReportPost">
                    Publications signalées
                </li>
                <li id="btnReportComment">
                    Commentaires signalés
                </li>
                <li id="btnReportOther">
                    Problèmes signalés
                </li>
            </ul>
        </nav>
        <article id="content">
            <table>
                <thead>
                    <th>Qui a signalé</th>
                    <th>Pourquoi</th>
                    <th>action</th>
                </thead>
                <tbody></tbody>
            </table>
            <ul id="paging"></ul>
        </article>
        <div id="noContent" class="blockStyleOne">Il n'y a pas de signalement</div>
        <?php
    }
    ?>
</section>
