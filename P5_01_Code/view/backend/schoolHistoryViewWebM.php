<section id="blockSchoolHistory" class="container">
    <h1>Historique</h1>
    <hr>
    <article id="schoolHistory">
        <div id="blockSchools">
        <?php
        foreach ($data['schools'] as $school) {
            $school->getIsActive() ? $classIsActive = "" : $classIsActive = "inactiveSchool";
            ?>
            <div class="blockSchool">
                <div class="<?=$classIsActive?>">
                    <div>
                        <figure>
                            <img src='<?=$school->getLogo()?>'>
                        </figure>
                    </div>
                    <div>
                        <h2><?=$school->getName()?></h2>
                        <span class="hide"><?=$school->getId()?></span>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
        </div>
        <div>
            <div>
                <div id="search">
                    <i class="fas fa-search"></i>
                    <button value="category">Catégorie</button>
                    <button value="date">Date</button>
                    <button value="categoryAndDate">Catégorie et date</button>
                </div>
                <form>
                    <input type="hidden" name="sortBy" id="sortBy" value="">
                    <div>
                        <label for="tagCategory">Catégorie</label>
                        <select name="tagCategory" id="tagCategory">
                            <option value="" selected>Tout</option>
                            <option value="profil">Profil</option>
                            <option value="account">Comptes</option>
                            <option value="activityPeriod">Abonnement</option>
                        </select>
                    </div>
                    <div>
                        <div>
                            <span>Période</span>
                            <div>
                                <input type="date" name="firstDate" id="firstDate">
                                <input type="date" name="secondDate" id="secondDate">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div id="blockEntries"></div>
            <p id="showMore" class="orang">Afficher plus</p>
        </div>
    </article>
</section>
