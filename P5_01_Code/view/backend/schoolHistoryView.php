<section id="blockSchoolHistory" class="container">
    <h1>Historique</h1>
    <hr>
    <article id="schoolHistory">
        <div>
            <div>
                <div id="search">
                    <i class="fas fa-search"></i>
                    <button value="category">Catégorie</button>
                    <button value="date">Date</button>
                    <button value="categoryAndDate">Catégorie et date</button>
                </div>
                <form>
                    <span class="hide"><?=$data['school']->getId()?></span>
                    <input type="hidden" name="schoolName" id="schoolName" value="<?=$data['school']->getName()?>">
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
            <div id="blockEntries">
                <?php
                if (!empty($data['entries'])) {
                    foreach ($data['entries'] as $entry) {
                        echo '<div class="entry">';
                        echo '<span>' . $entry->getDateEntry() . '</span>';
                        echo '<span>' . $entry->getEntry() . '</span>';
                        echo '</div>';
                    }
                } else {
                    //no school entry to display
                    echo '<div class="entry">';
                    echo '<span></span>';
                    echo '<span>Il n\'y a pas d\'historique à afficher</span>';
                    echo '</div>';
                }
                ?>
            </div>
            <p id="showMore" class="orang">Afficher plus</p>
        </div>
    </article>
</section>
