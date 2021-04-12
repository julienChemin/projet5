<section id="blockSettings" class="container">
    <h1>Paramètres</h1>
    <hr>
    <div class="blockStyleOne">
        <p><?=$data['contractInfo']?></p>
        <p>Nombre de comptes : <?=$data['school']->getNbEleve()?> dont <?=$data['school']->getNbActiveAccount()?> actif(s)</p>
    </div>
    <div>
        <h2>Prolongation de l'abonnement</h2>
        <p>Cette section est vide pour l'instant</p>
        <!-- <form id="formExtendContract">
            <p class="orang">Le nombre de compte ne peut être inférieur au nombre de comptes actif</p>
            <p>
                <label for="nbAccount">Nombre de compte</label>
                <input type="text" name="nbAccount" id="nbAccount">
            </p>
            <p>
                <label for="schoolDuration">Durée de la prolongation</label>
                <select name="schoolDuration" id="schoolDuration">
                    <option value="1">1 mois</option>
                    <option value="3">3 mois</option>
                    <option value="6">6 mois</option>
                    <option value="12">12 mois</option>
                </select> 
            </p>
        </form> -->
    </div>
    <?php
        if ($data['school']->getIsActive()) {
            ?>
            <div>
                <h2>Ajout de compte utilisateur</h2>
                <p>Cette section est vide pour l'instant</p>
                <!-- <form id="formAddUsersAccounts">
                    <p class="orang">
                        La durée d'activité de ces nouveaux comptes s'ajustera sur la date de fin de contrat de votre établissement.
                        (<a href="index.php?action=faq">Plus d'info</a>)
                    </p>
                    <p>
                        <label for="nbNewAccount">Nombre de compte à ajouter</label>
                        <input type="text" name="nbNewAccount" id="nbNewAccount">
                    </p>
                </form> -->
            </div>
            <?php
        }
    ?>
</section>
<?php
