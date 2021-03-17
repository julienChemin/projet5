<section id="blockModeratSchool" class="container">
    <h1 class="fullWidth">Modération de l'établissement</h1>
    <hr>
    <article id="moderatSchool">
        <?php
        if (!empty($data['schools'])) {
            // display all school
            $schools = $data['schools'];
            for ($i = 0; $i < count($schools); $i++) {
                ?>
                <div class="blockSchool">
                    <div class="<?=$schools[$i]->getIsActive() ? "" : "inactiveSchool"?>">
                        <div>
                            <figure>
                                <img src="<?=$schools[$i]->getLogo()?>" alt="Logo de l'établissement">
                            </figure>
                        </div>
                        <div>
                            <h2><a href="indexAdmin.php?action=schoolProfile&school=<?=$schools[$i]->getName()?>"><?=$schools[$i]->getName()?></a></h2>
                        </div>
                        <div>
                            <i class="far fa-caret-square-down"></i>
                            <i class="far fa-caret-square-up"></i>
                        </div>
                    </div>
                    <div>
                        <span><?=$data['contractInfo'][$i]?></span>
                        <span>
                            <button class="btnEdit<?=$schools[$i]->getIsActive() ? "ToInactive" : "ToActive"?>">
                                <?=$schools[$i]->getIsActive() ? "Désactiver" : "Activer"?>
                            </button>
                        </span>
                        <table>
                            <tr>
                                <td>Nom de l'établissement</td>
                                <td><?=$schools[$i]->getName()?></td>
                                <td><button class="btnEditName"><i class="fas fa-pen"></i></button></td>
                            </tr>
                            <tr>
                                <td>Administrateur principal</td>
                                <td><?=$schools[$i]->getNameAdmin()?></td>
                                <td><button class="btnEditAdmin"><i class="fas fa-pen"></i></button></td>
                            </tr>
                            <tr>
                                <td>Mail</td>
                                <td><?=$schools[$i]->getMail()?></td>
                                <td><button class="btnEditMail"><i class="fas fa-pen"></i></button></td>
                            </tr>
                            <tr>
                                <td>Code d'affiliation</td>
                                <td><?=$schools[$i]->getCode()?></td>
                                <td><button class="btnEditCode"><i class="fas fa-pen"></i></button></td>
                            </tr>
                            <tr>
                                <td>Nombre de comptes</td>
                                <td>Total - <?=$schools[$i]->getNbEleve()?> | Actif - <?=$schools[$i]->getNbActiveAccount()?></td>
                                <td><button class="btnEditNbEleve"><i class="fas fa-pen"></i></button></td>
                            </tr>
                            <tr>
                                <td>Logo</td>
                                <td><?=$schools[$i]->getLogo() === 'public/images/question-mark.png' ? 'Logo par défaut' : 'Logo personnalisé';?></td>
                                <td><button class="btnEditLogo"><i class="fas fa-pen"></i></button></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php
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
    </article>
</section>
<div id="modal">
    <form class="container" method="POST" action="indexAdmin.php?action=editSchool" enctype="multipart/form-data">
        <div>
            <input type="hidden" name="elem" value="">
            <input type="hidden" name="schoolName" value="">

            <p id="blockName">
                <label for="editName">Nouveau nom de l'établissement</label>
                <input type="text" name="editName">
            </p>
            <p id="blockAdmin">
                <label for="editAdmin">Identifiant du compte du nouvel Administrateur</label>
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
                    <label for="uploadLogo">Télécharger un nouveau logo (max : 5Mo)</label>
                    <input type="hidden" name="MAX_FILE_SIZE" value="6000000">
                    <input type="file" name="uploadLogo" accept="image/*">
                </p>
            </div>
            <p id="blockMail">
            <label for="editMail">Nouvelle adresse mail de l'établissement</label>
                <input type="email" name="editMail">
            </p>
            <p id="blockToActive">
                <label for="editToActive">Pour activer l'établissement, indiquez le nombre de compte(s) élève(s)</label>
                <input type="text" name="editToActive">
                <label for="editToActiveDuration">ainsi que le temps d'extension du contrat (en mois)</label>
                <input type="text" name="editToActiveDuration">
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
