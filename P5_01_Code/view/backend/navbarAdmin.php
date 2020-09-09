<header id="navbar" class="fullWidth adminNavbar">
    <div class="container">
        <div>
            <span>
                <a href="indexAdmin.php"><img src="public/images/banner.png"></a>
            </span>
            <nav>
                <ul>
                    <li title="Publier pour l'établissement" class="hideUnder600Width">
                        <a href="indexAdmin.php?action=addSchoolPost"><i class="fas fa-file-import"></i></a>
                    </li>
                    <hr class="hrNavbar hideUnder600Width">

                    <li title="Profil" class="hideUnder600Width">
                        <a href="index.php?action=userProfile&userId=<?=$_SESSION['id']?>"><i class="far fa-address-card"></i></a>
                    </li>
                    <hr class="hrNavbar hideUnder600Width">

                    <li title="Profil de l'établissement" class="hideUnder600Width">
                        <a href="indexAdmin.php?action=schoolProfile&school=<?=$_SESSION['school']?>"><i class="fas fa-school"></i></a>
                    </li>
                    <hr class="hrNavbar hideUnder600Width">

                    <li id="pseudo">
                        <?=$_SESSION['pseudo']?>
                        <i class="fas fa-sort-down"></i>
                    </li>
                    <hr class="hrNavbar">

                    <li title="Vers ArtSchool" class="hideUnder600Width">
                        <a href="index.php"><i class="fas fa-home"></i></a>
                    </li>
                    <hr class="hrNavbar hideUnder600Width">

                    <li title="Se Déconnecter">
                        <a href="indexAdmin.php?action=disconnect"><i class="fas fa-sign-out-alt"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
        <div id="menuNavbar">
            <div>
                <ul>
                    <li>
                        <a href="indexAdmin.php">
                            Page d'accueil<i class="fas fa-home"></i>
                        </a>
                    </li>
                    <hr>

                    <li>
                        <a href="indexAdmin.php?action=moderatUsers">
                            Modérer les utilisateurs<i class="fas fa-list-ul"></i>
                        </a>
                    </li>
                    <?php
                    if ($_SESSION['grade'] === ADMIN) {
                        ?>
                        <li>
                            <a href="indexAdmin.php?action=moderatAdmin">
                                Modérer l'administration<i class="fas fa-user-cog"></i>
                            </a>
                        </li>

                        <li>
                            <a href="indexAdmin.php?action=moderatSchool">
                                Modérer mon établissement<i class="fas fa-tools"></i>
                            </a>
                        </li>
                        <?php
                    }
                    ?>
                    <hr>

                    <li>
                        <a href="index.php?action=userProfile&userId=<?=$_SESSION['id']?>">
                            Profil<i class="far fa-address-card"></i>
                        </a>
                    </li>

                    <li>
                        <a href="indexAdmin.php?action=schoolProfile&school=<?=$_SESSION['school']?>">
                            Profil de l'établissement<i class="fas fa-school"></i>
                        </a>
                    </li>
                    
                    <li>
                        <a href="indexAdmin.php?action=addSchoolPost">
                            Publier pour l'établissement<i class="fas fa-file-import"></i>
                        </a>
                    </li>

                    <li>
                        <a href="indexAdmin.php?action=schoolHistory">
                            Historique<i class="fas fa-history"></i>
                        </a>
                    </li>
                    <hr>

                    <li>
                        <a href="index.php">
                            Vers le site ArtSchool<i class="fas fa-home"></i>
                        </a>
                    </li>
                    <hr>

                    <?php
                    if ($_SESSION['grade'] === ADMIN) {
                        ?>
                        <li title="paramètres">
                            <a href="indexAdmin.php?action=settings">
                                Paramètres<i class="fas fa-cog"></i>
                            </a>
                        </li>
                        <?php
                    }
                    ?>

                    <li>
                        <a href="indexAdmin.php?action=disconnect">
                            Se déconnecter<i class="fas fa-sign-out-alt"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
