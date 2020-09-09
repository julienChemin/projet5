<section id="blockFaq" class="container">
    <h1>Foire aux questions</h1>
    <div id="summary">
        <?php
        if ($_SESSION['grade'] === ADMIN || $_SESSION['grade'] === MODERATOR) {
            ?>
            <h2 class="summaryCategory">Lorem ipsum dolor sit amet.<i class="fas fa-sort-down"></i></h2>
            <ul>
                <li><a href="#firstLorem">- Lorem, ipsum.</a></li>
                <li><a href="#secondLorem">- Lorem ipsum dolor sit amet consectetur.</a></li>
            </ul>
            <?php
        }
        ?>
        <h2 class="summaryCategory">Personnalisation de profil<i class="fas fa-sort-down"></i></h2>
        <ul>
            <li><a href="#profileHeader">- En-tête de profil</a></li>
            <li><a href="#profileContent">- Contenu du profil</a></li>
        </ul>

        <h2 class="summaryCategory">Publication<i class="fas fa-sort-down"></i></h2>
        <ul>
            <li><a href="#postOnProfile">- Publier sur mon profil</a></li>
            <?php
            if (!empty($_SESSION) && $_SESSION['school'] !== NO_SCHOOL) {
                echo '<li><a href="#postOnSchool">- Publier sur le profil de mon établissement</a></li>';
            }
            ?>
        </ul>
    </div>
    <p>
        Si vous ne trouvez pas de réponses dans la F.A.Q, <br>
        vous pouvez rejoindre le <a href="https://discord.gg/uDfwPHH">discord</a> 
        Art-School pour poser votre question directement à la communauté ou à l'équipe d'Art-School
    </p>
    <p>
        Si vous rencontrez un problème avec le site, vous pouvez le signaler <a href="index.php?action=report&amp;elem=other">en cliquant ici</a>
    </p>
    <hr>
    <?php
    if ($_SESSION['grade'] === ADMIN || $_SESSION['grade'] === MODERATOR) {
        ?>
        <article>
            <h1>Lorem ipsum dolor sit amet.</h1>
            <div id="firstLorem">
                <h2>Lorem, ipsum.</h2>
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
            <div id="secondLorem">
                <h2>Lorem ipsum dolor sit amet consectetur.</h2>
                <p>
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Excepturi, modi mollitia velit sint porro
                    odit non dolorem similique voluptatibus nam error neque asperiores. Lorem ipsum dolor sit amet.
                </p>
            </div>
        </article>
        <hr>
        <?php
    }
    ?>
    <article>
        <h1>Personnalisation de profil</h1>
        <div id="profileHeader">
            <h2>En-tête de profil</h2>
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
            <h2>Contenu de profil</h2>
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
        <h1>Publication</h1>
        <div id="postOnProfile">
            <h2>Publier sur mon profil</h2>
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
        if (!empty($_SESSION) && $_SESSION['school'] !== NO_SCHOOL) {
            ?>
            <div id="postOnSchool">
                <h2>Publier sur le profil de mon établissement</h2>
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
            <?php
        }
        ?>
    </article>
</section>