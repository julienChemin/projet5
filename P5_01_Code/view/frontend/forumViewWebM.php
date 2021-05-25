<section id="forum">
    <section id="blockSchools" class="container">
    <?php
        foreach ($data['schools'] as $school) {
            if ($school->getName() !== NO_SCHOOL) {
                !$school->getIsActive() ? $classIsActive = 'inactiveSchool' : $classIsActive = '';
                !$school->getIsActive() ? $title = ' Cet établissement n\'est plus actif sur le site' : $title = '';
                ?>
                <a href="index.php?action=forum&school=<?=$school->getName()?>" class="blockSchool" title="<?=$title?>">
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
                </a>
                <?php
            }
        }
    ?>
    </section>
</section>
