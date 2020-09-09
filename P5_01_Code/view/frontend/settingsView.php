
<section id="blockManageContract" class="container">
    <h1>Paramètres</h1>
    <hr>
    <div>
        <?php
        if ($_SESSION['school'] === NO_SCHOOL) {
            ?>
            <h2>Activation du compte</h2>
            <p>Activer votre compte vous donne accès a plusieurs fonctionnalitées - <a href="index.php?action=faq">Voir les avantages</a></p>
            <p><?=$data['contractInfo']?></p>
            <form id="formExtendContract">
                <p>
                    <label for="userDuration">Durée de la prolongation</label>
                    <select name="userDuration" id="userDuration">
                        <option value="1">1 mois</option>
                        <option value="3">3 mois</option>
                        <option value="6">6 mois</option>
                        <option value="12">12 mois</option>
                    </select>
                </p>
            </form>
            <?php
        }
        ?>
    </div>
</section>