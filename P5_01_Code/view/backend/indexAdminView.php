<section id="homeAdmin" class="container">
    <?php
    if (isset($_SESSION['grade']) && ($_SESSION['grade'] === ADMIN  || $_SESSION['grade'] === MODERATOR)) {
        // is connected
        ?>
        <article id="menuHome">
            <a href="indexAdmin.php?action=moderatUsers">
                <div class="itemHomeAdmin">
                    <i class="fas fa-list-ul"></i>
                    <span>Consulter la liste des élèves</span>
                </div>
            </a>

        <?php
        if ($_SESSION['grade'] === ADMIN) {
            ?>
                <a href="indexAdmin.php?action=moderatAdmin">
                    <div class="itemHomeAdmin">
                        <i class="fas fa-user-cog"></i>
                        <span>Modération des comptes administrateurs / modérateurs</span>
                    </div>
                </a>
            <?php
        }

        if ($_SESSION['school'] === ALL_SCHOOL) {
            ?>
                <a href="indexAdmin.php?action=addSchool">
                    <div class="itemHomeAdmin">
                        <i class="fas fa-tools"></i>
                        <span>Modération des Art Schools</span>
                    </div>
                </a>
            <?php
        } elseif ($_SESSION['grade'] === ADMIN) {
            ?>
                <a href="indexAdmin.php?action=moderatSchool">
                    <div class="itemHomeAdmin">
                        <i class="fas fa-tools"></i>
                        <span>Modérer mon Art School</span>
                    </div>
                </a>
            <?php
        }
        ?>
        </article>
        
        <?php
        if ($_SESSION['school'] !== ALL_SCHOOL) {
            ?>
            <aside id="slowSlide" class="sliderPosts">
                <input type="hidden" name="schoolName" id="schoolName" value="<?=$_SESSION['school']?>">
                <div>
                    <h2>Récemment posté par des élèves de votre établissement</h2>
                    <a href="index.php?action=search&sortBy=school&school=<?=$_SESSION['school']?>">Tout voir</a>
                </div>
                <hr>
                <div class="posts">
                    <div class="arrow arrowLeft">
                        <i class="fas fa-chevron-left"></i>
                    </div>
                    <div class="arrow arrowRight">
                        <i class="fas fa-chevron-left"></i>
                    </div>
                    <div class="slider"></div>
                </div>
            </aside>
            <?php
        }
    } else {
        // try to connect
        ?>
        <h1>Administration</h1>
        <form id="formConnect" method="POST" action="indexAdmin.php">
            <div>
                <p>
                    <label for="ConnectPseudoAdmin">Identifiant</label>
                    <input type="text" name="ConnectPseudoAdmin" id="ConnectPseudoAdmin" required="">
                </p>
                <p>
                    <label for="ConnectPasswordAdmin">Mot de passe</label>
                    <input type="password" name="ConnectPasswordAdmin" id="ConnectPasswordAdmin" required="">
                </p>
                <p>
                    <label for="stayConnect">Rester connecté</label>
                    <input type="checkbox" name="stayConnect" id="stayConnect">
                </p>
                <p>
                    <input type="submit" name="submit" value="Connection">
                    <span id="buttonForgetPassword">Mot de passe oublié</span>
                </p>
            </div>
            <?php
            if (isset($data['message'])) {
                echo '<p class="msg orang">' . $data['message'] . '</p>';
            }
            ?>
        </form>
        <!--display form for forget password-->
        <form id="formForgetPassword" method="POST" action="indexAdmin.php">
            <p>
                Saisissez l'adresse mail lié à votre compte, 
            </p>
            <p>
                un email vous sera envoyé à cette adresse pour réinitialiser votre mot de passe.
            </p>
            <div>
                <p>
                    <label for="postMail">Adresse email</label>
                    <input type="email" name="postMail" id="postMail" required="">
                </p>
                <p>
                    <input type="submit" name="submit" value="Envoyer">
                    <span id="buttonCancel">Annuler</span>
                </p>
            </div>
        </form>
        <!--modal for rules of cookies-->
        <div id="modal">
            <div>
                <p>En cochant "rester connecté", vous acceptez l'utilisation des cookies</p>
                <p>Les cookies sont de petites quantités d’informations stockées dans des fichiers au sein même du navigateur de votre ordinateur.</p>
                <p>Sur ArtSchools, les cookies servent uniquement à garder votre identifiant ainsi qu'une version encodé de votre mot de passe. Cela vous permet de rester connecté</p>
                <div>
                    <button id="btnAcceptCookie">Accepter</button>
                    <button id="btnCancelCookie">Refuser</button>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</section>
