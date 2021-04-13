<section id="listSchoolsView">
    <?php
    if (!empty($data['schools'])) {
        echo '<h1 class="container">Liste des établissements scolaires présent sur le site</h1>';
        echo '<p class="container">Cliquez sur un établissement pour avoir plus d\'informations</p>';
        echo '<div id="blockSchools" class="container">';

        foreach ($data['schools'] as $school) {
            if ($school->getName() !== NO_SCHOOL) {
                !$school->getIsActive() ? $classIsActive = 'inactiveSchool' : $classIsActive = '';
                !$school->getIsActive() ? $title = ' Cet établissement n\'est plus actif sur le site' : $title = '';
                ?>
                <div class="blockSchool" title="<?=$title?>">
                    <div class="<?=$classIsActive?>">
                        <div>
                            <figure>
                                <img src="<?=$school->getLogo()?>" alt="Logo de l'établissement">
                            </figure>
                        </div>
                        <div>
                            <h2><?=$school->getName()?></h2>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        echo '</div>';

        ?>
        <div id="schoolInformation">
            <p id="linkSchoolProfile" class="container">
                <a href="#">Voir le profil de : <span></span></a>
            </p>

            <div>
                <h2 class="container">Administrateurs</h2>

                <article>
                    <div id="adminSection" class="blockResultUser fullWidth container"></div>
                </article>

                <h2 class="container">Modérateurs</h2>
                
                <article>
                    <div id="moderatorSection" class="blockResultUser fullWidth container"></div>
                </article>

                <h2 class="container">Étudiants</h2>

                <article>
                    <div id="studentSection" class="blockResultUser fullWidth container"></div>
                </article>
            </div>
        </div>
        <?php
    } else {
        echo '<p class="blockStyleOne">Il n\'y a aucun établissement présent sur le site pour le moment</p>';
    }
    ?>
</section>
