<section id="blockModeratUsers" class="container">
    <article id="moderatUsers">
        <?php
        if (isset($data['users'])) {
            if ($_SESSION['school'] === ALL_SCHOOL) {
                if (!empty($data['schools'])) {
                    //display all schools for webmaster
                    $incrementalId = 0;
                    foreach ($data['schools'] as $school) {
                        ?>
                        <div class="blockSchool" id="school<?=$incrementalId?>">
                            <div class="<?=$school->getIsActive() ? "" : "inactiveSchool"?>">
                                <div>
                                    <figure>
                                        <img src='<?=$school->getLogo()?>'>
                                    </figure>
                                </div>
                                <div>
                                    <h1>
                                        <a href="index.php?action=schoolProfile&school=<?=$school->getName()?>"><?=$school->getName()?></a>
                                    </h1>
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
                                    <h2>Créer un nouveau groupe</h2>
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
                                    <h2>Liste des groupes</h2>
                                    <span>Cliquez sur l'icone <i class="fas fa-times"></i> pour supprimer un groupe</span>

                                    <ul class="listGroup"></ul>
                                </div>
                            </form>
                            <div>
                                <?php
                                if ($data['isActive'][$school->getName()]['active']) {
                                    //display active account
                                    ?>
                                    <table>
                                        <caption>Compte(s) actif(s)</caption>
                                        <tr>
                                            <th>
                                                Identifiant
                                            </th>
                                            <th>
                                                Groupe
                                            </th>
                                            <th>
                                                Passer en modérateur
                                            </th>
                                            <th>
                                                Désactiver le compte
                                            </th>
                                            <th>
                                                Supprimer le compte
                                            </th>
                                        </tr>
                                        <?php
                                        foreach ($data['users'][$school->getName()]['active'] as $user) {
                                            ?>
                                            <tr>
                                                <td>
                                                    <a href="index.php?action=userProfile&userId=<?=$user->getId()?>"><?=$user->getName()?></a>
                                                </td>
                                                <td>
                                                    <span class="userGroup">
                                                        <?=$user->getSchoolGroup() !== null? $user->getSchoolGroup() : 'Aucun groupe'?>
                                                    </span> - 
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
                                                    <i class="fas fa-user-shield toModerator" schoolname="<?=$school->getName()?>" ></i>
                                                </td>
                                                <td>
                                                    <i class="fas fa-user-times toInactive" schoolname="<?=$school->getName()?>" ></i>
                                                </td>
                                                <td>
                                                    <i class="fas fa-times toDelete" schoolname="<?=$school->getName()?>" ></i>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                } else {
                                    // there is no active account to display
                                    ?>
                                    <div class="blockStyleOne fullWidth">
                                        <p class="msg orang textCenter">
                                            Il n'y a aucun compte actif à afficher
                                        </p>
                                    </div>
                                    <?php
                                }
                                if ($data['isActive'][$school->getName()]['inactive']) {
                                    //display inactive account
                                    ?>
                                    <table>
                                        <caption>Compte(s) inactif(s)</caption>
                                        <tr>
                                            <th>
                                                Identifiant
                                            </th>
                                            <th>
                                                Groupe
                                            </th>
                                            <th>
                                                Activer le compte
                                            </th>
                                            <th>
                                                Supprimer le compte
                                            </th>
                                        </tr>
                                        <?php
                                        foreach ($data['users'][$school->getName()]['inactive'] as $user) {
                                            ?>
                                            <tr>
                                                <td>
                                                    <a href="index.php?action=userProfile&userId=<?=$user->getId()?>"><?=$user->getName()?></a>
                                                </td>
                                                <td>
                                                    <span class="userGroup">
                                                        <?=$user->getSchoolGroup() !== null? $user->getSchoolGroup() : 'Aucun groupe'?>
                                                    </span> - 
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
                                                    <i class="fas fa-user-plus toActive" schoolname="<?=$school->getName()?>" ></i>
                                                </td>
                                                <td>
                                                    <i class="fas fa-times toDelete" schoolname="<?=$school->getName()?>" ></i>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                    <?php
                                } else {
                                    // there is no inactive account to display
                                    ?>
                                    <div class="blockStyleOne fullWidth">
                                        <p class="msg orang textCenter">
                                            Il n'y a aucun compte inactif à afficher
                                        </p>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                        $incrementalId += 1;
                    }
                } else {
                    //no school to display
                    ?>
                    <div class="blockStyleOne">
                        <p class="msg orang textCenter">
                            Il n'y a pas d'école à afficher
                        </p>
                    </div>
                    <?php
                }
            } elseif ($_SESSION['grade'] === ADMIN || $_SESSION['grade'] === MODERATOR) {
                //display user's school
                $school = $data['schools'];
                if ($school->getName() === $_SESSION['school']) {
                    ?>
                    <div class="blockSchool" id="school0">
                        <div class="<?=$school->getIsActive() ? "" : "inactiveSchool"?>">
                            <div>
                                <figure>
                                    <img src='<?=$school->getLogo()?>'>
                                </figure>
                            </div>
                            <div>
                                <h1>
                                    <a href="indexAdmin.php?action=schoolProfile&school=<?=$school->getName()?>"><?=$school->getName()?></a>
                                </h1>
                            </div>
                        </div>
                        <form class="formAddGroup">    
                            <p class="orang">Gérer les groupes >></p>
                            <div>
                                <hr>
                                <h2>Créer un nouveau groupe</h2>
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
                                <h2>Liste des groupes</h2>
                                <span>Cliquez sur l'icone <i class="fas fa-times"></i> pour supprimer un groupe</span>
                                <ul class="listGroup"></ul>
                            </div>
                        </form>
                        <div>
                            <?php
                            if ($data['isActive']['active']) {
                                //display active account
                                ?>
                                <table>
                                    <caption>Compte(s) actif(s)</caption>
                                    <tr>
                                        <th>
                                            Identifiant
                                        </th>
                                        <th>
                                            Groupe
                                        </th>
                                        <th>
                                            Passer en modérateur
                                        </th>
                                        <th>
                                            Désactiver le compte
                                        </th>
                                        <th>
                                            Supprimer le compte
                                        </th>
                                    </tr>
                                    <?php
                                    foreach ($data['users'] as $user) {
                                        if ($user->getIsActive()) {
                                            ?>
                                            <tr>
                                                <td>
                                                    <a href="index.php?action=userProfile&userId=<?=$user->getId()?>"><?=$user->getName()?></a>
                                                </td>
                                                <td>
                                                    <span class="userGroup">
                                                        <?=$user->getSchoolGroup() !== null? $user->getSchoolGroup() : 'Aucun groupe'?>
                                                    </span> - 
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
                                                    <i class="fas fa-user-shield toModerator" schoolname="<?=$school->getName()?>" ></i>
                                                </td>
                                                <td>
                                                    <i class="fas fa-user-times toInactive" schoolname="<?=$school->getName()?>" ></i>
                                                </td>
                                                <td>
                                                    <i class="fas fa-times toDelete" schoolname="<?=$school->getName()?>" ></i>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </table>
                                <?php
                            } else {
                                // there is no active account to display
                                ?>
                                <div class="blockStyleOne fullWidth">
                                    <p class="msg orang textCenter">
                                        Il n'y a aucun compte actif à afficher
                                    </p>
                                </div>
                                <?php
                            }
                            if ($data['isActive']['inactive']) {
                                //display inactive account
                                ?>
                                <table>
                                    <caption>Compte(s) inactif(s)</caption>
                                    <tr>
                                        <th>
                                            Identifiant
                                        </th>
                                        <th>
                                            Groupe
                                        </th>
                                        <th>
                                            Activer le compte
                                        </th>
                                        <th>
                                            Supprimer le compte
                                        </th>
                                    </tr>
                                    <?php
                                    foreach ($data['users'] as $user) {
                                        if (!$user->getIsActive()) {
                                            ?>
                                            <tr>
                                                <td>
                                                    <a href="index.php?action=userProfile&userId=<?=$user->getId()?>"><?=$user->getName()?></a>
                                                </td>
                                                <td>
                                                    <span class="userGroup">
                                                        <?=$user->getSchoolGroup() !== null? $user->getSchoolGroup() : 'Aucun groupe'?>
                                                    </span> - 
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
                                                    <i class="fas fa-user-plus toActive" schoolname="<?=$school->getName()?>" ></i>
                                                </td>
                                                <td>
                                                    <i class="fas fa-times toDelete" schoolname="<?=$school->getName()?>" ></i>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </table>
                                <?php
                            } else {
                                // there is no inactive account to display
                                ?>
                                <div class="blockStyleOne fullWidth">
                                    <p class="msg orang textCenter">
                                        Il n'y a aucun compte inactif à afficher
                                    </p>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                <?php
                }
            }
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
