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

                    <?php
                    if ($_SESSION['isActive']) {
                        ?>
                        <li title="Publication" class="hideUnder600Width">
                            <a href="index.php?action=addPost"><i class="fas fa-file-import"></i></a>
                        </li>
                        <hr class="hrNavbar hideUnder600Width">
                        <?php
                    }
                    ?>

                    <li title="Profil" class="hideUnder600Width">
                        <a href="index.php?action=userProfile&userId=<?=$_SESSION['id']?>"><i class="far fa-address-card"></i></a>
                    </li>
                    
                    <?php
                    if ($_SESSION['school'] !== NO_SCHOOL) {
                        ?>
                        <hr class="hrNavbar hideUnder600Width">
                        <li title="Profil de mon établissement" class="hideUnder600Width">
                            <a href="index.php?action=schoolProfile&school=<?=$_SESSION['school']?>"><i class="fas fa-school"></i></a>
                        </li>
                        <?php
                    }
                    ?>
                    <hr class="hrNavbar hideUnder600Width">

                    <li id="pseudo">
                        <span class="hideUnder600Width"><?=$_SESSION['fullName']?></span>
                        <i class="fas fa-bars"></i>
                    </li>
                    <hr class="hrNavbar">

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

                    <?php
                    if ($_SESSION['isActive']) {
                        ?>
                        <li>
                            <a href="index.php?action=addPost">
                                Publication<i class="fas fa-file-import"></i>
                            </a>
                        </li>
                        <?php
                    }
                    ?>
                    
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
                        <hr>

                        <li title="Voir le forum">
                            <a href="index.php?action=forum&school=<?=$_SESSION['school']?>">
                                Forum<i class="fas fa-door-open"></i>
                            </a>
                        </li>

                        <hr>
                        <li title="Voir mon CV">
                            <a href="index.php?action=cv&userId=<?=$_SESSION['id']?>">
                                CV<i class="fas fa-portrait"></i>
                            </a>
                        </li>

                        <li title="Voir mon portfolio">
                            <a href="index.php?action=portfolio&userId=<?=$_SESSION['id']?>">
                                Portfolio<i class="fas fa-th"></i>
                            </a>
                        </li>
                        <?php
                    }
                    ?>
                    <hr>

                    <li title="paramètres">
                        <a href="index.php?action=settings">
                            Paramètres<i class="fas fa-cog"></i>
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
