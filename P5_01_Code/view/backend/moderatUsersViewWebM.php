<section id="blockModeratUsers" class="container">
    <h1 class="fullWidth">Modération des utilisateurs</h1>
    <hr>
    <article id="moderatUsers">
        <?php
        if (!empty($data['schools'])) {
            //display all schools for webmaster
            foreach ($data['schools'] as $school) {
                ?>
                <div class="blockSchool" id="school<?=$data['incrementalId']?>">
                    <div class="<?=$school->getIsActive() ? "" : "inactiveSchool"?>">
                        <div>
                            <figure>
                                <img src="<?=$school->getLogo()?>" alt="Logo de l'établissement">
                            </figure>
                        </div>
                        <div>
                            <h2><a href="indexAdmin.php?action=schoolProfile&school=<?=$school->getName()?>"><?=$school->getName()?></a></h2>
                        </div>
                        <div>
                            <i class="far fa-caret-square-down"></i>
                            <i class="far fa-caret-square-up"></i>
                        </div>
                    </div>
                    <form class="formAddGroup">    
                        <p class="orang">Gérer les groupes >></p>
                        <div>
                            <hr>
                            <h3>Créer un nouveau groupe</h3>
                            <span>Le nom d'un groupe ne peut contenir que des chiffres, des lettres ainsi que les caractères " - " et " _ "</span>
                            <div>
                                <p>
                                    <label for="addGroup">Nom du groupe : </label>
                                    <input type="text" name="addGroup">
                                </p>
                            </div>
                            <div>
                                <p>
                                    <input type="hidden" name="schoolName" value="<?=$school->getName()?>">
                                    <input type="submit" name="submit" value="Valider">
                                    <input type="button" name="cancel" value="Annuler">
                                </p>
                            </div>
                            <hr>
                        </div>
                        <div>
                            <h3>Liste des groupes</h3>
                            <span>Cliquez sur l'icone <i class="fas fa-times"></i> pour supprimer un groupe</span>
                            <ul class="listGroup"></ul>
                        </div>
                    </form>
                    <div>
                        <div id="listAction" class="blockStyleOne fullWidth">
                            <ul>
                                <li><i class="fas fa-user-shield"></i>Donner les droits de modérateur</li>
                                <li><i class="fas fa-user-plus"></i>Activer le compte</li>
                                <li><i class="fas fa-user-minus"></i>Désactiver le compte</li>
                                <li><i class="fas fa-times"></i>Supprimer le compte</li>
                            </ul>
                        </div>
                        <?php
                        if (!empty($data['users'][$school->getName()]['active'])) {
                            //display active account
                            ?>
                            <table>
                                <caption>Compte(s) actif(s)</caption>
                                <tr>
                                    <th>Identifiant</th>
                                    <th>Groupe</th>
                                    <th>Action</th>
                                </tr>
                                <?php
                                foreach ($data['users'][$school->getName()]['active'] as $user) {
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="index.php?action=userProfile&userId=<?=$user->getId()?>"><?=$user->getLastName()?> <?=$user->getFirstName()?></a>
                                            <span class="hide"><?=$user->getPseudo()?></span>
                                        </td>
                                        <td>
                                            <span class="userGroup"><?=$user->getSchoolGroup() !== null? $user->getSchoolGroup() : 'Aucun groupe'?></span> - 
                                            <span class="btnEditGroup">Modifier le groupe</span>
                                            <div class="listEditGroup">
                                                <select class="inputListGroup">
                                                    <option value="Aucun groupe">Aucun groupe</option>
                                                    <?php
                                                    if (!empty($school->getListSchoolGroups())) {
                                                        foreach ($school->getListSchoolGroups() as $group) {
                                                            $user->getSchoolGroup() === $group ? $selectedVal = "selected" : $selectedVal = "";
                                                            echo '<option value="' . $group . '" ' . $selectedVal . '>' . $group . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <i class="fas fa-user-shield toModerator" userpseudo="<?=$user->getPseudo()?>" schoolname="<?=$school->getName()?>" ></i> - 
                                            <i class="fas fa-user-minus toInactive" userpseudo="<?=$user->getPseudo()?>" schoolname="<?=$school->getName()?>" ></i> - 
                                            <i class="fas fa-times toDelete" userpseudo="<?=$user->getPseudo()?>" schoolname="<?=$school->getName()?>" ></i>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            echo '</table>';
                        } else {
                            // there is no active account to display
                            ?>
                            <div class="blockStyleOne fullWidth">
                                <p class="msg orang textCenter">Il n'y a aucun compte actif à afficher</p>
                            </div>
                            <?php
                        }
                        if (!empty($data['users'][$school->getName()]['inactive'])) {
                            //display inactive account
                            ?>
                            <table>
                                <caption>Compte(s) inactif(s)</caption>
                                <tr>
                                    <th>Identifiant</th>
                                    <th>Groupe</th>
                                    <th>Action</th>
                                </tr>
                                <?php
                                foreach ($data['users'][$school->getName()]['inactive'] as $user) {
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="index.php?action=userProfile&userId=<?=$user->getId()?>"><?=$user->getLastName()?> <?=$user->getFirstName()?></a>
                                            <span class="hide"><?=$user->getPseudo()?></span>
                                        </td>
                                        <td>
                                            <span class="userGroup"><?=$user->getSchoolGroup() !== null? $user->getSchoolGroup() : 'Aucun groupe'?></span> - 
                                            <span class="btnEditGroup">Modifier le groupe</span>
                                            <div class="listEditGroup">
                                                <select class="inputListGroup">
                                                    <option value="Aucun groupe">Aucun groupe</option>
                                                    <?php
                                                    if (!empty($school->getListSchoolGroups())) {
                                                        foreach ($school->getListSchoolGroups() as $group) {
                                                            $user->getSchoolGroup() === $group ? $selectedVal = "selected" : $selectedVal = "";
                                                            echo '<option value="' . $group . '" ' . $selectedVal . '>' . $group . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <i class="fas fa-user-plus toActive" userpseudo="<?=$user->getPseudo()?>" schoolname="<?=$school->getName()?>" ></i> - 
                                            <i class="fas fa-times toDelete" userpseudo="<?=$user->getPseudo()?>" schoolname="<?=$school->getName()?>" ></i>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            echo '</table>';
                        } else {
                            // there is no inactive account to display
                            ?>
                            <div class="blockStyleOne fullWidth">
                                <p class="msg orang textCenter">Il n'y a aucun compte inactif à afficher</p>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <?php
                $data['incrementalId'] += 1;
            }
        } else {
            //no school to display
            ?>
            <div class="blockStyleOne">
                <p class="msg orang textCenter">Il n'y a pas d'école à afficher</p>
            </div>
            <?php
        }
        ?>
        <div id="modal">
            <div>
                <p></p>
                <div>
                    <a href="#">
                        <input type="button" name="ok" value="Valider">
                    </a>
                    <input type="button" name="cancel" value="Annuler">
                </div>
            </div>
        </div>
    </article>
</section>
