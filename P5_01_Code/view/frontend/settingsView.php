
<section id="blockSettings" class="container">
    <h1>Paramètres</h1>
    <hr>
    <div>
        <?php
        if ($_SESSION['school'] === NO_SCHOOL) {
            ?>
            <h2>Activation du compte</h2>
            <div class="blockStyleTwo">
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
        </div>
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
            <input type="submit" name="submit" value="Valider">
            <input type="button" name="cancel" value="Annuler">
        </p>
    </form>
</div>