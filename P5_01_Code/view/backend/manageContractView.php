<?php
if ($_GET['elem'] === 'school') {
    $school = $data['elem'];
    if ($school->getIsActive()) {
        $dateDeadlineInfo = 'Votre établissement est actif jusqu\'au ' . $school->getDateDeadline();
    } else {
        $dateDeadlineInfo = 'Votre établissement est inactif depuis le : ' . $school->getDateDeadline();
    }
    ?>
    <section id="blockManageContract" class="container">
        <div class="blockStyleOne">
            <p><?=$dateDeadlineInfo?></p>
            <p>Nombre de comptes : Disponible - <?=$school->getNbEleve()?> | Actif - <?=$school->getNbActiveAccount()?></p>
        </div>
        <div>
            <h1>Prolongation du contrat</h1>
            <form id="formExtendContract">
                <p class="orang">
                    Le nombre de compte ne peut être inférieur au nombre de comptes actif
                </p>
                <p>
                    <label for="nbAccount">Nombre de compte</label>
                    <input type="text" name="nbAccount" id="nbAccount">
                </p>
                <p>
                    <label for="schoolDuration">Durée de la prolongation</label>
                    <select name="schoolDuration" id="schoolDuration">
                        <option value="1">1 mois</option><!--TODO check nbAccount on js (+ display price)and php-->
                        <option value="3">3 mois</option><!--TODO check school->getIsActive // active -> dateDeadline + duration / inactive -> current date + duration-->
                        <option value="6">6 mois</option>
                        <option value="12">12 mois</option>
                    </select> 
                </p>
            </form>
        </div>
        <?php
            if ($isActive) {
            ?>
                    <div>
                        <h1>Ajout de compte utilisateur</h1>
                        <form id="formAddUsersAccounts">
                            <p class="orang">
                                La durée d'activité de ces nouveaux comptes s'ajustera sur la date de fin de contrat de votre établissement.
                            </p>
                            <p>
                                <label for="nbNewAccount">Nombre de compte à ajouter</label>
                                <input type="text" name="nbNewAccount" id="nbNewAccount">
                            </p>
                        </form>
                    </div>
            <?php
            }
        ?>
    </section>
    <?php
} elseif ($_GET['elem'] === 'user') {
    $user = $data['elem'];
    if ($user->getIsActive()) {
        $dateDeadlineInfo = 'Votre compte est actif jusqu\'au ' . $user->getDateDeadline();
    } else {
        $dateDeadlineInfo = 'Votre compte est inactif depuis le ' . $user->getDateDeadline();
    }
    ?>
    <section id="blockManageContract" class="container">
        <div>
            <h1>Activation du compte</h1>
            <p>Activer votre compte vous donne accès a plusieurs fonctionnalitées - <a href="index.php?action=faq">Voir la liste des fonctionnalitées</a></p>
            <p><?=$dateDeadlineInfo?></p>
            <form id="formExtendContract">
                <p>
                    <label for="userDuration">Durée de la prolongation</label>
                    <select name="userDuration" id="userDuration">
                        <option value="1">1 mois</option>
                        <option value="3">3 mois</option>
                        <option value="6">6 mois</option>
                        <option value="12">12 mois</option>
                    </select> <!--TODO js display price depend on the option selected-->
                </p>
            </form>
        </div>
    </section>
<?php
}
