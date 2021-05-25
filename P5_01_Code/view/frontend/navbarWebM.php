<header id="navbar" class="fullWidth">
    <div class="container">
        <div>
            <span>
                <a href="index.php">
                    <img src="public/images/banner.png" alt="Bannière Art-Schools">
                </a>
            </span>
            <nav>
                <ul>
                    <li title="Rechercher">
                        <a href="index.php?action=search"><i class="fas fa-search"></i></a>
                    </li>
                    <hr class="hrNavbar">

                    <li id="pseudo">
                        <span class="hideUnder600Width"><?=$_SESSION['fullName']?></span>
                        <i class="fas fa-bars"></i>
                    </li>
                    <hr class="hrNavbar">

                    <li title="vers l'interface d'administration" class="hideUnder600Width">
                        <a href="indexAdmin.php"><i class="fas fa-cogs"></i></a>
                    </li>
                    <hr class="hrNavbar hideUnder600Width">
                    <li title="Se Déconnecter">
                        <a href="index.php?action=disconnect"><i class="fas fa-sign-out-alt"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
        <div id="menuNavbar">
            <div>
                <ul>
                    <li>
                        <a href="index.php?action=search">
                            Rechercher<i class="fas fa-search"></i>
                        </a>
                    </li>
                    <hr>

                    <li title="Voir les forums">
                        <a href="index.php?action=forum&school=allSchool">
                            Voir les forums<i class="fas fa-door-open"></i>
                        </a>
                    </li>
                    <hr>

                    <li title="Vers l'interface d'administration">
                        <a href="indexAdmin.php">
                            Vers l'interface d'administration<i class="fas fa-cogs"></i>
                        </a>
                    </li>
                    <hr>

                    <li title="Se Déconnecter">
                        <a href="index.php?action=disconnect">
                            Se Déconnecter<i class="fas fa-sign-out-alt"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
