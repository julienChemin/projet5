<section id="blockFaq" class="container">
    <h1>Foire aux questions</h1>
    <div id="summary" class="blockStyleOne">
        <?php
        if ($_SESSION['grade'] === ADMIN || $_SESSION['grade'] === MODERATOR) {
            ?>
            <h2 class="summaryCategory">Lorem ipsum dolor sit amet.</h2>
            <ul>
                <li><a href="#firstLorem">Lorem, ipsum.</a></li>
                <li><a href="#secondLorem">Lorem ipsum dolor sit amet consectetur.</a></li>
            </ul>
            <?php
        }
        ?>
        <h2 class="summaryCategory">Personnalisation de profil</h2>
        <ul>
            <li><a href="#">En-tête de profil</a></li>
            <li><a href="#">Contenu du profil</a></li>
        </ul>

        <h2 class="summaryCategory">Publication</h2>
        <ul>
            <li><a href="#">Publier sur mon profil</a></li>
            <?php
            if (!empty($_SESSION) && $_SESSION['school'] !== NO_SCHOOL) {
                echo '<li><a href="#">Publier sur le profil de mon établissement</a></li>';
            }
            ?>
        </ul>
    </div>
    <p>
        Si vous ne trouvez pas de réponses dans la F.A.Q<br>
        , vous pouvez rejoindre le <a href="https://discord.gg/uDfwPHH">discord</a> 
        Art-School pour poser votre question directement à la communauté ou a l'équipe d'Art-School
    </p>
    <article></article>
</section>
//TODO bloc article pour chaque LI, UL display none puis js clic h2 display ul