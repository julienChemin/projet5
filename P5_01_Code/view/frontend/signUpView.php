<section id="signUp" class="container">
    <h1>Inscription</h1>
    <form id="formSignUp" method="POST" action="index.php?action=signUp">
        <div>
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
            <div>
                <hr>
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
