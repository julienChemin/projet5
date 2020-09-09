<header id="navbar" class="fullWidth adminNavbar">
    <div class="container">
        <div>
            <span>
                <a href="indexAdmin.php"><img src="public/images/banner.png"></a>
            </span>
            <nav>
                <ul>
                    <li title="Gérer les signalements" class="hideUnder600Width">
                        <a href="indexAdmin.php?action=moderatReports"><i class="far fa-flag"></i></a>
                    </li>
                    <hr class="hrNavbar hideUnder600Width">

                    <li title="Historique" class="hideUnder600Width">
                        <a href="indexAdmin.php?action=schoolHistory"><i class="fas fa-history"></i></a>
                    </li>
                    <hr class="hrNavbar hideUnder600Width">

                    <li title="Modération du site" class="hideUnder600Width">
                        <a href="indexAdmin.php?action=moderatWebsite"><i class="fas fa-hourglass-half"></i></a>
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
                        <a href="indexAdmin.php">Page d'accueil<i class="fas fa-home"></i></a>
                    </li>
                    <hr>

                    <li>
                        <a href="indexAdmin.php?action=moderatUsers">Modérer les utilisateurs<i class="fas fa-list-ul"></i></a>
                    </li>
                    <li>
                        <a href="indexAdmin.php?action=moderatAdmin">Modérer l'administration<i class="fas fa-user-cog"></i></a>
                    </li>
                    <li>
                        <a href="indexAdmin.php?action=addSchool">Ajout / édition des établissements<i class="fas fa-tools"></i></a>
                    </li>
                    <hr>

                    <li>
                        <a href="indexAdmin.php?action=moderatReports">Gérer les signalements<i class="far fa-flag"></i></a>
                    </li>
                    <li>
                        <a href="indexAdmin.php?action=schoolHistory">Historique<i class="fas fa-history"></i></a>
                    </li>
                    <li>
                        <a href="indexAdmin.php?action=moderatWebsite">Modération du site<i class="fas fa-hourglass-half"></i></a>
                    </li>
                    <hr>

                    <li>
                        <a href="index.php">Vers le site ArtSchool<i class="fas fa-home"></i></a>
                    </li>
                    <hr>

                    <li>
                        <a href="indexAdmin.php?action=disconnect">Se déconnecter<i class="fas fa-sign-out-alt"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
