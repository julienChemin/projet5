<header id="navbar" class="fullWidth">
    <div class="container">
        <div>
            <span>
                <a href="index.php">
                    <img src="public/images/banner.png">
                </a>
            </span>
            <nav>
                <ul>
                    <li title="Rechercher">
                        <a href="index.php?action=search">
                            <i class="fas fa-search"></i>
                        </a>
                    </li>
                    <?php
                    if (!isset($_SESSION['school'])) {
                        ?>
                        <hr class="hrNavbar">
                        <li title="Se connecter">
                            <a href="index.php?action=signIn">Se connecter</a>
                        </li>
                        <li title="S'inscrire">
                            <a href="index.php?action=signUp">S'inscrire</a>
                        </li>
                        <?php
                    } else {
                        if ($_SESSION['school'] !== ALL_SCHOOL) {
                            ?>
                            <hr class="hrNavbar">
                            <li title="Publication">
                                <a href="index.php?action=addPost">
                                    <i class="fas fa-file-import"></i>
                                </a>
                            </li>
                            <hr class="hrNavbar">
                            <li title="Profil">
                                <a href="index.php?action=userProfile&userId=<?=$_SESSION['id']?>">
                                    <i class="far fa-address-card"></i>
                                </a>
                            </li>
                            <?php
                            if ($_SESSION['school'] !== NO_SCHOOL) {
                                ?>
                                <hr class="hrNavbar">
                                <li title="Profil de mon établissement">
                                    <a href="index.php?action=schoolProfile&school=<?=$_SESSION['school']?>">
                                        <i class="fas fa-school"></i>
                                    </a>
                                </li>
                                <?php
                            }
                        }
                        ?>
                        <hr class="hrNavbar">
                        <li id="pseudo">
                            <?=$_SESSION['pseudo']?>
                            <i class="fas fa-sort-down"></i>
                        </li>
                        <?php
                        if ($_SESSION['grade'] === ADMIN || $_SESSION['grade'] === MODERATOR) {
                            ?>
                            <hr class="hrNavbar">
                            <li title="vers l'interface d'administration">
                                <a href="indexAdmin.php">
                                    <i class="fas fa-cogs"></i>
                                </a>
                            </li>
                            <?php
                        }
                        ?>
                        <hr class="hrNavbar">
                        <li title="Se Déconnecter">
                            <a href="index.php?action=disconnect">
                                <i class="fas fa-sign-out-alt"></i>
                            </a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </nav>
        </div>
        <?php
        if (isset($_SESSION['grade'])) {
            ?>
            <div id="menuNavbar">
                <div>
                    <ul>
                        <li>
                            <a href="index.php?action=search">
                                Rechercher<i class="fas fa-search"></i>
                            </a>
                        </li>
                        <hr>
                        <?php
                        if ($_SESSION['school'] !== ALL_SCHOOL) {
                            ?>
                            <li>
                                <a href="index.php?action=addPost">
                                    Publication<i class="fas fa-file-import"></i>
                                </a>
                            </li>
                            <li>
                                <a href="index.php?action=userProfile&userId=<?=$_SESSION['id']?>">
                                Profil<i class="far fa-address-card"></i>
                                </a>
                            </li>
                            <?php
                            if ($_SESSION['school'] !== NO_SCHOOL) {
                                ?>
                                <li>
                                    <a href="index.php?action=schoolProfile&school=<?=$_SESSION['school']?>">
                                        Profil de mon établissement<i class="fas fa-school"></i>
                                    </a>
                                </li>
                                <?php
                            }
                        }
                        if ($_SESSION['grade'] === ADMIN || $_SESSION['grade'] === MODERATOR) {
                            ?>
                            <hr>
                            <li title="Vers l'interface d'administration">
                                <a href="indexAdmin.php">
                                    Vers l'interface d'administration<i class="fas fa-cogs"></i>
                                </a>
                            </li>
                            <?php
                        }
                        ?>
                        <hr>
                        <li title="Se Déconnecter">
                            <a href="index.php?action=disconnect">
                                Se Déconnecter<i class="fas fa-sign-out-alt"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</header>
