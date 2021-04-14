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
            <h2 class="summaryCategory firstCategorySummary">Administration - Fonctionnalités du site<i class="fas fa-sort-down"></i></h2>

            <ul>
                <?php
                if ($isAdmin) {
                    ?>
                    <li><a href="#howToModeratSchool">- Modération de l'établissement</a></li>
                    <li><a href="#howToModeratAdmin">- Modération des comptes administrateurs / modérateurs</a></li>
                    <?php
                }
                ?>
                <li><a href="#howToModeratUsers">- Consulter la liste des élèves</a></li>
                <li><a href="#howToSchoolPost">- Publication de l'établissement</a></li>
                <li><a href="#howToSchoolProfile">- Profil de l'établissement</a></li>
            </ul>

            <h2 class="summaryCategory lastCategorySummary">Administration - Questions fréquentes<i class="fas fa-sort-down"></i></h2>

            <ul>
                <li><a href="#whatSchoolAdvantagesWithSub">- Pourquoi inscrire mon établissement sur ArtSchools ?</a></li>
                <li><a href="#whatIfSchoolSubEnd">- Que se passe-t-il si mon abonnement expire ?</a></li>
                <li><a href="#whatDifferencesBetweenAdminAndMod">- Quelles sont les différences entre "administrateur" et "modérateur" ?</a></li>
                <li><a href="#whatMainAdminMean">- "Administrateur principal" qu'est-ce que c'est ?</a></li>
            </ul>
            <?php
        }
        ?>

        <!-- ---------------------- -->
        <!-- FOR USERS AND VISITORS -->
        <!-- ---------------------- -->
        <h2 class="summaryCategory firstCategorySummary">Fonctionnalités du site<i class="fas fa-sort-down"></i></h2>

        <ul>
            <li><a href="#howToCustomProfile">- Personnaliser mon profil</a></li>
            <li><a href="#howToPostOnProfile">- Publier sur mon profil</a></li>
            <li><a href="#howToPostOnSchool">- Publier sur le profil de mon établissement</a></li>
        </ul>

        <h2 class="summaryCategory lastCategorySummary">Questions fréquentes<i class="fas fa-sort-down"></i></h2>

        <ul>
            <li><a href="#whatUserAdvantagesWithSub">- Quels sont les avantages d'un abonnement ?</a></li>
            <li><a href="#whatIfUserSubEnd">- Que se passe-t-il si mon abonnement expire ?</a></li>
            <li><a href="#howToGetMyPersonnalPage">- Comment obtenir ma page perso "CV / Portfolio" en ligne gratuitement ?</a></li>
            <li><a href="#dmca">- Digital Millennium Copyright Act - DMCA</a></li>
        </ul>
    </div>

    <hr>
    <p>
        Si vous ne trouvez pas de réponses dans la F.A.Q, <br>
        vous pouvez rejoindre le <a href="https://discord.gg/SZUF2AMbA3">discord</a> 
        pour poser votre question directement à la communauté ou à l'équipe d'<strong>Art-Schools</strong>
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
    <!-- --------------------------------------------- END SUMMARY --------------------------------------------- -->

    <?php
    // FOR ADMINS AND MODERATORS
    if ($isAdmin || $isModerator) {
        if ($isAdmin) {
            ?>
            <!-- howToModeratSchool -->
            <hr>
            <article>
                <h2 id="howToModeratSchool">Modération de l'établissement</h2>
                
                <div>
                    <p>
                        Sur la page <a href="indexAdmin.php?action=moderatSchool">Modération de l'établissement</a>, 
                        vous pouvez consulter et modifier des informations relatifs à votre établissement :
                    </p>

                    <ul>
                        <li>
                            <h3>Nom de l'établissement</h3>

                            <p>le nom sous lequel votre établissement sera connu sur le site.</p>
                        </li>
                        
                        <li>
                            <h3>Administrateur principal</h3>

                            <p>
                                Ce compte utilisateur est symboliquement à la tête de l'établissement sur Artschools.
                            </p>

                            <p>
                                Référez vous à <a href="index.php?action=faq#whatMainAdminMean">cette section</a> pour plus d'informations.
                            </p>
                        </li>

                        <li>
                            <h3>Mail</h3>

                            <p>
                                Cette adresse mail sera utilisée pour vous envoyer toutes les informations relatives à votre établissement.
                                <br>
                                Actuellement, les seuls mails que vous recevrez seront des rappels pour votre abonnement 
                                (un mois / une semaine avant l'expiration de l'abonnement).
                            </p>

                            <p>
                                Pour plus d'options sur les mails, consultez les <a href="indexAdmin.php?action=settings">paramètres</a>.
                            </p>
                        </li>

                        <li>
                            <h3>Code d'affiliation</h3>

                            <p>
                                Vos élèves auront besoin de ce code pour relier leurs comptes Artschools à votre établissement.
                                <br>
                                Il est conseillé de ne donner ce code qu'à des personnes qui devraient avoir le droit d'affilier leurs comptes à votre établissement.
                            </p>

                            <p>
                                Une fois tous les comptes affiliés à votre établissement, il est conseillé de modifier le code (pour qu'il reste confidentiel).
                            </p>

                            <p>
                                Au besoin, vous pouvez supprimer l'affiliation d'un compte à votre établissement, 
                                sur la page de <a href="indexAdmin.php?action=moderatUsers">modération des utilisateurs</a>.
                            </p>
                        </li>

                        <li>
                            <h3>Nombre de comptes</h3>

                            <p>
                                Vous pouvez y retrouver le nombre maximum ainsi que le nombre actuel de comptes affiliés à votre établissement.
                            </p>

                            <p>
                                Consultez la page de <a href="indexAdmin.php?action=moderatUsers">modération des utilisateurs</a> pour gérer les comptes affiliés.
                                <br>
                                Consultez les <a href="indexAdmin.php?action=settings">paramètres</a> pour modifier le nombre de compte maximum affiliés.
                            </p>
                        </li>

                        <li>
                            <h3>Logo</h3>

                            <p>
                                Cette image sera affiché lorsque des utilisateurs consulteront la <a href="index.php?action=listSchools">liste des établissements scolaires</a>, 
                                ou si votre établissement apparaît en résultat de recherche.
                            </p>
                        </li>
                    </ul>
                </div>
            </article>

            <!-- howToModeratAdmin -->
            <hr>
            <article>
                <h2 id="howToModeratAdmin">Modération des comptes administrateurs / modérateurs</h2>

                <div>
                    <p>
                        La page de <a href="indexAdmin.php?action=moderatAdmin">Modération des comptes administrateurs / modérateurs</a> est réservé au administrateurs.
                    </p>

                    <p>
                        Vous pouvez grader ou rétrograder un compte, supprimer l'affiliation d'un compte à votre établissement, ou supprimer définitivement un compte.
                        <br>
                        Il est conseillé de supprimer un compte seulement s'il est inutilisé. Sinon vous pouvez simplement supprimer l'affiliation à votre établissement.
                    </p>

                    <p>
                        <span>La suppression d'un compte est définitive et irréversible</span>
                    </p>

                    <figure>
                        <figcaption>Vous pouvez aussi créer un compte de modérateur qui sera directement affilié à votre établissement</figcaption>
                        <img src="public/images/faq/createModeratorAccount.png" alt="menu create moderator account">
                    </figure>

                    <p>
                        <span>Les comptes administrateurs et modérateurs ne sont pas limités en terme de quantité et ne sont pas comptabilisés en tant que comptes actifs.</span>
                    </p>
                </div>
            </article>
            <?php
        }
        ?>

        <!-- howToModeratUsers -->
        <hr>
        <article>
            <h2 id="howToModeratUsers">Consulter la liste des élèves</h2>

            <div>
                <p>
                    La page de <a href="indexAdmin.php?action=moderatUsers">Modération des comptes utilisateurs</a> vous permet de :
                </p>

                <ul>
                    <li>
                        <h3>Passer un compte utilisateur en compte modérateur</h3>

                        <p>
                            Dans le cas ou la personne à qui vous voulez donner les droits de modérateur a déjà un compte ArtSchools.
                        </p>
                    </li>

                    <li>
                        <h3>Activer / désactiver un compte utilisateur</h3>

                        <p>
                            Cela vous permet de garder un compte affilié à votre établissement, sans que celui-ci ne soit compté dans le nombre maximum de compte affilié.
                            <br>
                            <span>Un compte désactivé, même affilié à un établissement actif, n'a plus accès aux avantages d'un compte actif.</span>
                        </p>
                    </li>

                    <li>
                        <h3>Gérer les groupes</h3>

                        <p>
                            Vous pouvez créer, supprimer et attribuer des groupes à vos élèves. Cela vous permet de créer des niveaux d'accessibilité lors de vos <a href="index.php?action=faq#howToSchoolPost">publications</a>.
                            <br>
                            <span>La liste des élèves est triée par groupe, puis par ordre alphabétique.</span>
                        </p>
                    </li>
                </ul>
            </div>
        </article>

        <!-- howToSchoolPost -->
        <hr>
        <article>
            <h2 id="howToSchoolPost">Publication de l'établissement</h2>

            <div>
                <p>
                    Lorsque vous <a href="indexAdmin.php?action=addSchoolPost">publiez</a> sur le profil de l'établissement, vous avez le choix entre publication publique et privée.
                </p>

                <p>
                    Une publication publique apparaitra sur le profil de votre établissement et sera visible par tout le monde.
                    <br>
                    Une publication privée n'apparaîtra que pour les personnes affiliées à votre établissement. Vous pouvez restreindre la visibilité de la publication à un ou plusieurs groupes.
                </p>
            </div>
        </article>

        <!-- howToSchoolProfile -->
        <hr>
        <article>
            <h2 id="howToSchoolProfile">Profil de l'établissement</h2>

            <div>
                <p>
                    Sur le <a href="indexAdmin.php?action=schoolProfile&school=<?=$_SESSION['school'] ?>">profil de l'établissement</a> coté administrateur, 
                    vous pouvez cliquer sur <i class="orang fas fa-pencil-alt"></i> pour passer en mode édition.
                </p>

                <p>
                    D'autres <i class="orang fas fa-pencil-alt"></i> vont apparaître, notamment au niveau de la bannière, de la photo de profil et du texte qui l'accompagne.
                    <br>
                    Cliquez sur l'un d'eux pour faire apparaitre les options permettant de personnaliser chacun de ces éléments.
                </p>

                <p>
                    Le mode édition vous permet aussi de gérer le contenu des onglets "Profil", "Actualité" et "À propos".
                    <br>
                    La gestion de ces onglets fonctionne de la même manière que pour les <a href="index.php?action=faq#howToCustomProfile">profils utilisateurs</a>.
                </p>
            </div>
        </article>

        <!-- whatSchoolAdvantagesWithSub -->
        <hr>
        <article>
            <h2 id="whatSchoolAdvantagesWithSub">Pourquoi inscrire mon établissement sur ArtSchools ?</h2>

            <div>
                <p>
                    Voici les fonctionnalités et avantages auxquelles vous accèderez en tant qu'établissement actif sur le site:
                </p>
                
                <ul>
                    <li>
                        <p>- Publications privées, réservées aux membres de votre établissement</p>
                    </li>

                    <li>
                        <p>- Publications privées de fichiers .zip / .rar, pour transmettre des documents</p>
                    </li>

                    <li>
                        <p>- Système de dossier pour organiser vos publications</p>
                    </li>

                    <li>
                        <p>
                            - Système d'archivage, pour garder l'onglet "publication privée" lisible
                            <br>
                            <span>Cette fonctionnalité n'est pas encore disponible</span>
                        </p>
                    </li>

                    <li>
                        <p>- Système de groupe, pour catégoriser vos élèves (par classe par exemple) et créer une accessibilité restreinte à certains groupes</p>
                    </li>
                    
                    <li>
                        <p>
                            - ArtSchools réunis plusieurs établissements scolaires ainsi que leurs élèves sur le même site. 
                            <br>
                            De ce fait, toute l'audience sera centralisée sur ce site. Cela vous offre une plus grande visibilité et une plus grande audience.
                        </p>
                    </li>
                    
                    <li>
                        <p>
                            - Selon vos publications et celles de vos élèves, 
                            les utilisateurs se feront une meilleure idée de votre cursus scolaire et de votre établissement.
                        </p>
                    </li>
                </ul>

                <p>
                    ArtSchools est en constante évolution. L'objectif est de rendre ce site pratique et efficace.
                    <br>
                    <br>
                    <span>Vous pouvez me contacter si vous avez besoin de fonctionnalités spécifiques sur le site.</span>
                    <br>
                    Il y aura bientôt un agenda pour que vous puissiez savoir sur quelle fonctionnalité je travaille.
                </p>
            </div>
        </article>

        <!-- whatIfSchoolSubEnd -->
        <hr>
        <article>
            <h2 id="whatIfSchoolSubEnd">Que se passe-t-il si mon abonnement expire ?</h2>

            <div>
                <p>
                    Si votre établissement n'est plus actif :
                </p>

                <ul>
                    <li>
                        <p>Vous n'aurez plus accès aux fonctionnalités et avantages de l'abonnement.</p>
                    </li>

                    <li>
                        <p>Tous les comptes affiliés à votre établissement deviendront inactifs.</p>
                    </li>

                    <li>
                        <span>Les publications privées ne seront plus accessibles aux élèves de votre établissement, mais ne seront pas supprimées</span>
                    </li>
                </ul>
            </div>
        </article>

        <!-- whatDifferencesBetweenAdminAndMod -->
        <hr>
        <article>
            <h2 id="whatDifferencesBetweenAdminAndMod">Quelles sont les différences entre "administrateur" et "modérateur" ?</h2>

            <div>
                <p>
                    Sur ce site, le grade de modérateur a été pensé pour qu'il soit attribué aux professeurs et intervenants.
                    <br>
                    Aussi, ils n'ont pas accès à la modération de l'établissement, ni à la modération des modérateurs / admins.
                </p>

                <p>
                    Les modérateurs n'ont pas accès aux fonctionnalités suivantes :
                </p>

                <ul>
                    <li>
                        <p>La modération de l'établissement</p>
                    </li>

                    <li>
                        <p>La modération des comptes modérateurs / admins</p>
                    </li>

                    <li>
                        <p>Les paramètres de l'établissement</p>
                    </li>
                </ul>
            </div>
        </article>

        <!-- whatMainAdminMean -->
        <hr>
        <article>
            <h2 id="whatMainAdminMean">"Administrateur principal" qu'est-ce que c'est ?</h2>

            <div>
                <p>
                    Ce compte utilisateur est symboliquement à la tête de l'établissement sur Artschools.
                </p>

                <p>
                    Actuellement, rien ne différencie l'administrateur principal d'un autre administrateur, à part le statut.
                    <br>
                    Cela peut être amené à changer, selon les améliorations et fonctionnalités apportées au site.
                </p>
            </div>
        </article>
        <?php
    }
    ?>

    <!-- FOR USERS AND VISITORS -->

    <!-- howToCustomProfile -->
    <hr>
    <article>
        <h2 id="howToCustomProfile">Personnaliser mon profil</h2>

        <div>
            <h3>Passer en mode édition</h3>

            <p>
                Sur votre <a href="indexAdmin.php?action=schoolProfile&school=<?=$_SESSION['school'] ?>">profil</a>, 
                cliquez sur <i class="orang fas fa-pencil-alt"></i>.
            </p>

            <p>
                D'autres <i class="orang fas fa-pencil-alt"></i> vont apparaître, 
                notamment au niveau de la bannière, de la photo de profil et du texte qui l'accompagne.
                <br>
                Cliquez sur l'un d'eux pour faire apparaitre les options permettant de personnaliser chacun de ces éléments.
            </p>

            <figure>
                <figcaption>Le mode édition vous permet aussi de gérer le contenu des onglets "Profil" et "À propos".</figcaption>
                <img src="public/images/faq/toggleMenuEdit.gif" alt="toggle menu edit">
            </figure>

            <h3>Ajouter un nouveau bloc de contenu</h3>

            <p>
                Cliquez sur <i class="orang far fa-plus-square"></i> 
                puis utilisez les options pour faire la mise en page.
            </p>

            <ul>
                <li>
                    <figure>
                        <figcaption>Largeur : la taille du nouveau bloc.</figcaption>
                        <img src="public/images/faq/blockLength.png" alt="block length">
                    </figure>
                </li>

                <li>
                    <figure>
                        <figcaption>Numero : l'emplacement du nouveau bloc. par défaut il sera placé en dernier.</figcaption>
                        <img src="public/images/faq/blockNumber.gif" alt="block number">
                    </figure>
                </li>

                <li>
                    <figure>
                        <figcaption>Seul sur sa ligne : cochez pour que le bloc soit seul sur sa ligne. Vous pouvez le centrer à gauche, à droite ou au centre</figcaption>
                        <img src="public/images/faq/blockAloneInRow.gif" alt="block alone in his row">
                    </figure>
                </li>

                <li>
                    <figure>
                        <figcaption>Ajouter une image : </figcaption>
                        <img src="public/images/faq/blockContentInsertImage.gif" alt="insert image in block">
                    </figure>
                </li>
            </ul>

            <h3>Modifier un bloc</h3>

            <p>
                Cliquez sur le <i class="orang fas fa-pencil-alt"></i> du bloc. 
                Les options sont les mêmes que pour ajouter un nouveau bloc.
            </p>

            <figure>
                <figcaption>La seule différence est l'icône <i class="orang fas fa-trash"></i> qui vous permet de supprimer définitivement un bloc.</figcaption>
                <img src="public/images/faq/deleteBlock.gif" alt="delete block">
            </figure>
        </div>
    </article>

    <!-- howToPostOnProfile -->
    <hr>
    <article>
        <h2 id="howToPostOnProfile">Publier sur mon profil</h2>

        <div>
            <p>
                Pour faire une <a href="index.php?action=addPost">publication</a>, cliquez sur <i class="orang fas fa-file-import"></i> dans 
                le menu en haut de page ou dans le menu déroulant.
                <br>
                Vous pouvez aussi cliquer sur <i class="orang far fa-plus-square"></i> dans l'onglet publication de votre profil.
            </p>

            <p>
                <span>Si vous n'êtes pas abonné, vous avez une limite de 6 publications au total.</span>
            </p>
        </div>
    </article>

    <!-- howToPostOnSchool -->
    <hr>
    <article>
        <h2 id="howToPostOnSchool">Publier sur le profil de mon établissement</h2>

        <div>
            <p>
                Si votre compte est affilié à un établissement, 
                vous pouvez faire une publication "privé" qui sera visible seulement pour les membres de cet établissement.
                <br>
                Pour ce faire, rendez-vous sur le profil de votre établissement puis allez dans l'onglet profil.
            </p>

            <p>
                <span>Vous ne pouvez publier que dans un dossier.</span>
                <br>
                Vous devez donc d'abord consulter le dossier ou vous voulez publier, 
                puis cliquer sur <i class="orang fas fa-folder-plus"></i>
            </p>

            <p>
                <span>La publication étant privée, vous pouvez publier des fichiers .zip / .rar</span>
            </p>
        </div>
    </article>

    <!-- whatUserAdvantagesWithSub -->
    <hr>
    <article>
        <h2 id="whatUserAdvantagesWithSub">Quels sont les avantages d'un abonnement ?</h2>

        <div>
            <p>
                Voici la liste des avantages (la liste peut évoluer) :
            </p>

            <ul>
                <li>
                    <p>Une page CV personnel</p>
                </li>

                <li>
                    <p>Une page portfolio personnel</p>
                </li>

                <li>
                    <p>Une page personnalisable pour vos projets</p>
                </li>
            </ul>
        </div>
    </article>

    <!-- whatIfUserSubEnd -->
    <hr>
    <article>
        <h2 id="whatIfUserSubEnd">Que se passe-t-il si mon abonnement expire ?</h2>

        <div>
            <p>
                Si votre compte n'est plus actif :
            </p>

            <ul>
                <li>
                    <p>Vous n'aurez plus accès aux fonctionnalités et avantages de l'abonnement.</p>
                </li>

                <li>
                    <p>Votre compte deviendra inactif et vous n'aurez plus accès aux publications privées de votre établissement.</p>
                </li>
            </ul>
        </div>
    </article>

    <!-- howToGetMyPersonnalPage -->
    <hr>
    <article>
        <h2 id="howToGetMyPersonnalPage">Comment obtenir ma page perso "CV / Portfolio" en ligne gratuitement ?</h2>

        <div>
            <p>
                <span>Cette fonctionnalité n'est pas encore disponible</span>
            </p>

            <p>
                Ces pages seront directement relié à votre compte. 
                <br>
                Vous y aurez accès à partir de votre profil, ou directement avec l'adresse : artschools.fr/cv/*nom*_*prenom*
            </p>
        </div>
    </article>

    <!-- DMCA -->
    <hr>
    <article>
        <h2 id="dmca">Digital Millennium Copyright Act - DMCA</h2>

        <div>
            <p>
                <span>Faire une DMCA vous permet de signaler une violation de vos droits d'auteur</span>
            </p>

            <p>
                Votre signalement doit inclure un lien direct (URL) vers chaque document contrefait que vous souhaitez que nous supprimions.
                <br>
                Veillez à donner un maximum d'informations.
                <br>
                Vous pouvez inclure autant de liens que nécessaire dans un seul signalement.
            </p>

            <p>
                Nous ne pouvons pas appliquer des revendications non spécifiques basées sur des mots clés, des requêtes de recherche, 
                le titre, le nom ou la ressemblance (physique ou autre) avec un autre document.
            </p>

            <p>
                    <?php
                    if (!empty($_SESSION['firstName'])) {
                        echo '<span><a href="index.php?action=report&elem=other">Cliquez ici pour faire une DMCA</a></span>';
                    } else {
                        echo '<span>Vous devez être connecté pour faire une DMCA</span>';
                    }
                    ?>
            </p>
        </div>
    </article>
</section>