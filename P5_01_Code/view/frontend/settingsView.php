
<section id="blockSettings" class="container">
    <h1>Paramètres</h1>
    <hr>
    <div>
        <?php
        if ($_SESSION['school'] === NO_SCHOOL) {
            // TODO following h2 and div are hide -> futur feature
            ?>
            <h2 class="hide">Activation du compte</h2>
            <div class="hide blockStyleTwo">
                <p>Activer votre compte vous donne accès à plusieurs fonctionnalitées - <a href="index.php?action=faq">Voir les avantages</a></p>
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
            </div>
            <?php
        }
        ?>
        <h2>Paramètres du compte</h2>
        <div id="accountSettings" class="blockStyleTwo">
            <div>
                <h3 class="orang">Identifiant</h3>
                <p>
                    Identifiant actuel : <span><?=$data['user']->getPseudo()?></span><button id="editPseudo" inputNeeded="text" elem="pseudo">Modifier</button>
                </p>
                <span></span>
            </div>

            <div>
                <h3 class="orang">Mot de passe</h3>
                <p>
                    Utilisez l'outil de récupération de mots de passe pour le modifier
                </p>
            </div>

            <div>
                <h3 class="orang">Nom</h3>
                <p>
                    Nom actuel : <span><?=$data['user']->getLastName()?></span><button id="editLastName" inputNeeded="text" elem="lastName">Modifier</button>
                </p>
                <span></span>
            </div>

            <div>
                <h3 class="orang">Prénom</h3>
                <p>
                    Prénom actuel : <span><?=$data['user']->getFirstName()?></span><button id="editFirstName" inputNeeded="text" elem="firstName">Modifier</button>
                </p>
                <span></span>
            </div>

           <div>
                <h3 class="orang">Mail</h3>
                <p>
                    Mail actuel : <span><?=$data['user']->getMail()?></span><button id="editMail" inputNeeded="text" elem="mail">Modifier</button>
                </p>
                <span></span>
           </div>

           <?php
            if (!$data['user']->getIsAdmin()) {
                ?>
                <div class="fullWidth">
                    <h3 class="orang">Établissement scolaire</h3>
                    <?php
                    if ($data['user']->getSchool() === NO_SCHOOL) {
                        ?>
                        <p>
                            <span>Vous ne faite parti d'aucun établissement scolaire</span><button id="editSchool" inputNeeded="text" elem="joinSchool">Rejoindre un établissement</button>
                        </p>
                        <?php
                    } else {
                        ?>
                        <p>
                            Établissement actuel : <span><?=$data['user']->getSchool()?></span><button id="editSchool" inputNeeded="text" elem="leaveSchool">Quitter l'établissement</button>
                        </p>
                        <?php
                    }
                    ?>
                    <span></span>
                </div>
                <?php
            }
           ?>
        </div>

        <?php
        if (!$data['user']->getIsAdmin() && !$data['user']->getIsModerator() && $data['user']->getIsActive()) {
            ?>
            <h2>Paramètres Cv</h2>

            <div id="cvSettings" class="blockStyleTwo">
                <div class="fullWidth">
                    <?php
                    if (!$data['cvInfo']->getShortLink()) {
                        echo '<p>Votre Cv n\'est pas disponible en ligne tant que vous n\'avez pas défini d\'adresse</p>';

                        echo '<div class="orang">';
                            echo '<p>Vous n\'avez pas défini d\'adresse pour votre Cv</p>';
                            echo '<span>(Le lien ne doit contenir que des chiffres, des lettres et des underscores. 2 à 20 caractères)</span>';
                            echo '<button id="editShortLink" inputNeeded="text" elem="cvShortLink">Définir</button>';
                        echo'</div>';
                    } else {
                        echo '<div>';
                            echo '<span>artschools.fr/</span><span class="orang">' . $data['cvInfo']->getShortLink() . '</span>';
                            echo '<button id="editShortLink" inputNeeded="text" elem="cvShortLink">Modifier</button>';
                            echo '<p>Le lien ne doit contenir que des chiffres, des lettres et des underscores. 2 à 20 caractères</p>';
                        echo'</div>';
                    }
                    ?>
                    <span></span>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</section>
<div id="modal">
    <form class="container">
        <input type="hidden" name="elem" value="">

        <p id="pText">
            <label for="textValue"></label>
            <input type="text" name="textValue" id="textValue">
        </p>

        <p>
            <input type="button" name="cancel" value="Annuler">
            <input type="submit" name="submit" value="Valider">
        </p>
    </form>
</div>