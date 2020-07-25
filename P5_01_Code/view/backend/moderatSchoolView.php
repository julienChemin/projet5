<section id="blockModeratSchool" class="container">
    <article id="moderatSchool">
        <?php
        if (!empty($data['schools'])) {
            if ($_SESSION['school'] === ALL_SCHOOL) {
                //webmaster consulting all school
                foreach ($data['schools'] as $school) {
                    ?>
                    <div class="blockSchool">
                        <div class="<?=$school->getIsActive() ? "" : "inactiveSchool"?>">
                            <div>
                                <figure>
                                    <img src='<?=$school->getLogo()?>'>
                                </figure>
                            </div>
                            <div>
                                <h1><a href="index.php?action=schoolProfile&school=<?=$school->getName()?>"><?=$school->getName()?></a></h1>
                            </div>
                            <div>
                                <i class="far fa-caret-square-down"></i>
                                <i class="far fa-caret-square-up"></i>
                            </div>
                        </div>
                        <div>
                            <span>Établissement : <?=$school->getIsActive() ? 'Actif' : 'Inactif'?></span>
                            <span><button class="btnEdit<?=$school->getIsActive() ? "ToInactive" : "ToActive"?>"><?=$school->getIsActive() ? "Désactiver" : "Activer"?></button></span>
                            <table>
                                <tr>
                                    <td>
                                        Nom de l'établissement
                                    </td>
                                    <td>
                                        <?=$school->getName()?>
                                    </td>
                                    <td>
                                        <button class="btnEditName">Modifier</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Administrateur
                                    </td>
                                    <td>
                                        <?=$school->getNameAdmin()?>
                                    </td>
                                    <td>
                                        <button class="btnEditAdmin">Modifier</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Code d'affiliation
                                    </td>
                                    <td>
                                        <?=$school->getCode()?>
                                    </td>
                                    <td>
                                        <button class="btnEditCode">Modifier</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Nombre de comptes
                                    </td>
                                    <td>
                                        Disponible - <?=$school->getNbEleve()?> | Actif - <?=$school->getNbActiveAccount()?>
                                    </td>
                                    <td>
                                        <button class="btnEditNbEleve">Modifier</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Logo
                                    </td>
                                    <td>
                                        <?=$school->getLogo() === 'public/images/question-mark.png' ? 'Default' : 'Custom';?>
                                    </td>
                                    <td>
                                        <button class="btnEditLogo">Modifier</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Date de fin du contrat
                                    </td>
                                    <td>
                                        <?=$school->getDateDeadline()?>
                                    </td>
                                    <td>
                                        <button class="btnEditDateDeadline">Modifier</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <?php
                }
            } elseif ($_SESSION['grade'] === ADMIN) {
                //admin consulting his school
                $school = $data['schools'];
                if ($school->getName() === $_SESSION['school']) {
                    ?>
                    <div class="blockSchool">
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
                        <div>
                            <span>Établissement : <?=$school->getIsActive() ? 'Actif' : 'Inactif'?></span>
                            <span>Date de fin de contrat : <?=$school->getDateDeadline()?></span>
                            <span><button><a href="indexAdmin.php?action=manageContract&elem=school&school=<?=$school->getName()?>">Gérer le contrat</a></button></span>
                            <table>
                                <tr>
                                    <td>
                                        Nom de l'établissement
                                    </td>
                                    <td>
                                        <?=$school->getName()?>
                                    </td>
                                    <td>
                                        <button class="btnEditName">Modifier</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Administrateur
                                    </td>
                                    <td>
                                        <?=$school->getNameAdmin()?>
                                    </td>
                                    <td>
                                        <button class="btnEditAdmin">Modifier</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Code d'affiliation
                                    </td>
                                    <td>
                                        <?=$school->getCode()?>
                                    </td>
                                    <td>
                                        <button class="btnEditCode">Modifier</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Nombre de comptes
                                    </td>
                                    <td>
                                        Disponible - <?=$school->getNbEleve()?> | Actif - <?=$school->getNbActiveAccount()?>
                                    </td>
                                    <td>
                                        <a href="indexAdmin.php?action=moderatUsers"><button>Consulter</button></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Logo
                                    </td>
                                    <td>
                                        <?=$school->getLogo() === 'public/images/question-mark.png' ? 'Default' : 'Custom';?>
                                    </td>
                                    <td>
                                        <button class="btnEditLogo">Modifier</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <?php
                }
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
        ?>
    </article>
</section>
<div id="modal">
    <form class="container" method="POST" action="indexAdmin.php?action=editSchool" enctype="multipart/form-data">
        <div>
            <input type="hidden" name="elem" value="">
            <input type="hidden" name="schoolName" value=<?=$_SESSION['school'] !== ALL_SCHOOL ? '"' . $school->getName() . '"' : "" ?>>

            <p id="blockName">
                <label for="editName">Nouveau nom de l'établissement</label>
                <input type="text" name="editName">
            </p>
            <p id="blockAdmin">
                <label for="editAdmin">Nom du nouvel Administrateur</label>
                <input type="text" name="editAdmin">
            </p>
            <p id="blockCode">
                <label for="editCode">Nouveau code</label>
                <input type="text" name="editCode">
            </p>
            <p id="blockNbEleve">
                <label for="editNbEleve">Nombre d'élèves</label>
                <input type="text" name="editNbEleve">
            </p>
            <div id="blockLogo">
                <p>
                    <label for="editLogo">Url du nouveau logo</label>
                    <input type="text" name="editLogo">
                </p>
                <p>ou</p>
                <p>
                    <label for="uploadLogo">Télécharger un nouveau logo (max : 2Mo)</label>
                    <input type="hidden" name="MAX_FILE_SIZE" value="2000000">
                    <input type="file" name="uploadLogo">
                </p>
            </div>
            <p id="blockDateDeadline">
                <label for="editDateDeadline">Nombre de mois à ajouter à la date d'échéance</label>
                <select name="editDateDeadline" id="editDateDeadline">
                    <option value="3">3 mois</option>
                    <option value="6">6 mois</option>
                    <option value="12">12 mois</option>
                </select>
            </p>
            <p id="blockToActive">
                <label for="editToActive">Pour activer l'établissement, indiquez le nombre de compte(s) élève(s)</label>
                <input type="text" name="editToActive">
            </p>
            <p id="blockToInactive">
                <span>L'établissement va être désactivé</span>
            </p>
            <p>
                <input type="submit" name="submit" value="Valider">
                <input type="button" name="cancel" value="Annuler">
            </p>
        </div>
    </form>
</div>
