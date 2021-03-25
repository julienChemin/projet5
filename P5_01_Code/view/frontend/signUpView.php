<section id="signUp" class="container">
    <h1>Inscription</h1>
    <form id="formSignUp" method="POST" action="index.php?action=signUp">
        <div>
            <p class="hide orang" id="idMsg">L'identifiant vous permet de vous connecter</p>
            <p class="hide orang" id="mailMsg">L'adresse e-mail nous permet de vous contacter</p>
            <div>
                <p>
                    <label for="signUpPseudo">Identifiant</label>
                    <input type="text" name="signUpPseudo" id="signUpPseudo" required="">
                </p>
                <p>
                    <label for="signUpMail">Adresse e-mail</label>
                    <input type="email" name="signUpMail" id="signUpMail" required="">
                </p>
            </div>
            <hr>

            <p class="hide orang" id="nameMsg">Vos nom et prénom seront affiché sur votre profil et sur vos publications</p>
            <div>
                <p>
                    <label for="signUpLastName">Nom</label>
                    <input type="text" name="signUpLastName" id="signUpLastName" required="">
                </p>
                <p>
                    <label for="signUpFirstName">Prénom</label>
                    <input type="text" name="signUpFirstName" id="signUpFirstName" required="">
                </p>
            </div>
            <hr>

            <p class="hide orang" id="passwordMsg">Choisissez un mot de passe pour votre compte</p>
            <p class="hide orang" id="confirmPasswordMsg">Confirmez votre mot de passe</p>
            <p class="hide orang" id="errorPasswordMsg">Vous devez entrer deux mot de passe identiques</p>
            <div>
                <p>
                    <label for="password">Mot de passe</label>
                    <input type="password" name="password" id="password" required="">
                </p>
                <p>
                    <label for="confirmPassword">Confirmez le mot de passe</label>
                    <input type="password" name="confirmPassword" id="confirmPassword" required="">
                </p>
            </div>
            <hr>

            <p class="hide orang" id="codeMsg">Ce code est fournit par votre établissement scolaire</p>
            <div id="blockIsAffiliated">
                <p id="postIsAffiliated">
                    <input type="checkbox" name="isAffiliated" id="isAffiliated">
                    <label for="isAffiliated">Lier le compte à un établissement scolaire</label>
                </p>
                <p id="postAffiliationCode">
                    <label for="affiliationCode">Code d'affiliation</label>
                    <input type="text" name="affiliationCode" id="affiliationCode">
                </p>
                <hr>
            </div>

            <p id="cguMsg" class="hide orang">Vous devez accepter les conditions générales d'utilisation pour pouvoir créer un compte</p>
            <div id="blockAcceptCgu">
                <p>
                    <input type="checkbox" name="acceptCgu" id="acceptCgu" require>
                    <label for="acceptCgu">J'accepte les <a href="index.php?action=cgu">conditions générales d'utilisation</a></label>
                </p>
            </div>

            <p>
                <input type="submit" name="submit" value="Valider">
            </p>
        </div>
        <?php
        if (isset($data['message'])) {
            echo '<p class="msg orang">' . $data['message'] . '</p>';
        }
        ?>
    </form>
</section>
