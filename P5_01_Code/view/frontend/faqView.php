<?php
$isLoggedUser = !empty($_SESSION) ? true : false;
$isActive = $isLoggedUser && $_SESSION['isActive'] ? true : false;
$isStudent = $isLoggedUser && $_SESSION['school'] !== NO_SCHOOL ? true : false;
$isModerator = $isLoggedUser && $_SESSION['grade'] === MODERATOR ? true : false;
$isAdmin = $isLoggedUser && $_SESSION['grade'] === ADMIN ? true : false;

?>
<section id="blockFaq" class="container">
    <h1>Foire aux questions</h1>
    <div id="summary">
        <!-- ------------------------- -->
        <!-- FOR ADMINS AND MODERATORS -->
        <!-- ------------------------- -->
        <?php
        if ($isAdmin || $isModerator) {
            ?>
            <h2>F.A.Q - Administration</h2>
            
            <h3 class="summaryCategory firstCategorySummary">Fonctionnalitées du site<i class="fas fa-sort-down"></i></h3>
            <ul>
                <li><a href="#howToHomeAdmin">- Accueil de l'administration</a></li>
                <?php
                if ($isAdmin) {
                    ?>
                    <li><a href="#howToModeratSchool">- Modération de l'établissement</a></li>
                    <li><a href="#howToModeratAdmin">- Modération des comptes administrateurs / modérateurs</a></li>
                    <?php
                }
                ?>
                <li><a href="#howToModeratUsers">- Consulter la liste des élèves</a></li>
                <?php
                if ($isAdmin) {
                    ?>
                    <li><a href="#howToSettings">- Paramètres de l'établissement</a></li>
                    <?php
                }
                ?>
                <li><a href="#howToSchoolPost">- Publication de l'établissement</a></li>
                <li><a href="#howToSchoolProfile">- Profil de l'établissement</a></li>
            </ul>

            <h3 class="summaryCategory lastCategorySummary">Questions fréquentes<i class="fas fa-sort-down"></i></h3>
            <ul>
                <li><a href="#questionAdmin1">- Quels sont les avantages d'un abonnement ?</a></li>
                <li><a href="#questionAdmin2">- Que ce passe t-il si mon abonnement expire ?</a></li>
                <li><a href="#questionAdmin3">- "Administrateur principal" qu'est-ce que c'est ?</a></li>
            </ul>
            <?php
        }
        ?>

        <!-- ---------------------- -->
        <!-- FOR USERS AND VISITORS -->
        <!-- ---------------------- -->
        <h2>F.A.Q - Utilisateurs</h2>
        
        <h3 class="summaryCategory firstCategorySummary">Fonctionnalitées du site<i class="fas fa-sort-down"></i></h3>
        <ul>
            <li><a href="#howToSearch">- Chercher du contenu</a></li>
            <li><a href="#howToPostOnProfile">- Personnaliser mon profil</a></li>
            <li><a href="#howToPostOnProfile">- Publier sur mon profil</a></li>
            <li><a href="#howToPostOnSchool">- Publier sur le profil de mon établissement</a></li>
        </ul>

        <h3 class="summaryCategory lastCategorySummary">Questions fréquentes<i class="fas fa-sort-down"></i></h3>
        <ul>
            <li><a href="#question1">- Pourquoi créer un compte ?</a></li>
            <li><a href="#question2">- Quels sont les avantages d'un abonnement ?</a></li>
            <li><a href="#question3">- Que ce passe t-il si mon abonnement expire ?</a></li>
            <li><a href="#question4">- Comment obtenir ma page perso "CV / Portfolio" en ligne gratuitement ?</a></li>
        </ul>
    </div>
    <hr>
    <p>
        Si vous ne trouvez pas de réponses dans la F.A.Q, <br>
        vous pouvez rejoindre le <a href="https://discord.gg/uDfwPHH">discord</a> 
        Art-Schools pour poser votre question directement à la communauté ou à l'équipe d'Art-Schools
    </p>
    <?php
    if ($isLoggedUser) {
        ?>
        <p>
            Si vous rencontrez un problème avec le site, vous pouvez le signaler <a href="index.php?action=report&amp;elem=other">en cliquant ici</a>
        </p>
        <?php
    }
    ?>
    <hr>
    <!-- --------------------------------------------- END SUMMARY --------------------------------------------- -->

    <?php
    if ($isAdmin || $isModerator) {
        // FOR ADMINS AND MODERATORS
        ?>
        <article>
            <h2>Accueil de l'administration</h2>
            <div id="howToHomeAdmin">
                <h3>Présentation</h3>
                <p>
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Excepturi, modi mollitia velit sint porro
                    odit non dolorem similique voluptatibus nam error neque asperiores.
                </p>
            </div>
        </article>
        <hr>
        <?php
        if ($isAdmin) {
            ?>
            
            <li><a href="#howToModeratSchool">- Modération de l'établissement</a></li>
            <li><a href="#howToModeratAdmin">- Modération des comptes administrateurs / modérateurs</a></li>
            <?php
        }
        ?>
        // other
        <?php
    }
    ?>

    <!-- FOR USERS AND VISITORS -->
    <article>
        <h2>Personnalisation de profil</h2>
        <div id="profileHeader">
            <h3>En-tête de profil</h3>
            <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Excepturi, modi mollitia velit sint porro
                 odit non dolorem similique voluptatibus nam error neque asperiores.
            </p>
            <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Excepturi, modi mollitia velit sint porro
                 odit non doloremes. Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet consectetur adipisicing elit. Omnis, numquam.
            </p>
            <p>
                Excepturi, modi mollitia velit sint porro.
            </p>
        </div>
        <div id="profileContent">
            <h3>Contenu de profil</h3>
            <p>
                Excepturi, modi mollitia velit sint porro.
            </p>
            <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Excepturi, modi mollitia velit sint porro
                 odit non dolorem similique voluptatibus nam error neque asperiores. Lorem ipsum dolor sit amet.
            </p>
            <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Excepturi, modi mollitia velit sint porro
                 odit non doloremes. Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet consectetur adipisicing elit. Omnis, numquam.
            </p>
            <p>
                Excepturi, modi mollitia velit sint porro.
            </p>
        </div>
    </article>
    <hr>
    <article>
        <h2>Publication</h2>
        <div id="postOnProfile">
            <h3>Publier sur mon profil</h3>
            <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Excepturi, modi mollitia velit sint porro
                 odit non dolorem similique voluptatibus nam error neque asperiores.
            </p>
            <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Excepturi, modi mollitia velit sint porro
                 odit non doloremes. Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet consectetur adipisicing elit. Omnis, numquam.
            </p>
            <p>
                Excepturi, modi mollitia velit sint porro.
            </p>
        </div>
        <?php
        if ($isStudent) {
            ?>
            <div id="postOnSchool">
                <h3>Publier sur le profil de mon établissement</h3>
                <p>
                    Excepturi, modi mollitia velit sint porro.
                </p>
            </div>
            <?php
        }
        ?>
    </article>
</section>