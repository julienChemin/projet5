<section id="blockModeratSchool" class="container">
    <h1 class="fullWidth">Modération de l'établissement</h1>
    <hr>
    <article id="moderatSchool">
        <?php
        if (!empty($data['schools'])) {
            //admin consulting his school
            $school = $data['schools'];
            ?>
            <div class="blockSchool">
                <div class="<?=$school->getIsActive() ? "" : "inactiveSchool"?>">
                    <div>
                        <figure>
                            <img src="<?=$school->getLogo()?>" alt="Logo de l'établissement">
                        </figure>
                    </div>
                    <div>
                        <h2><a href="indexAdmin.php?action=schoolProfile&school=<?=$school->getName()?>"><?=$school->getName()?></a></h2>
                    </div>
                </div>
                <div>
                    <span><?=$data['contractInfo']?></span>
                    <span>
                        <button><a href="indexAdmin.php?action=settings">Gérer le contrat</a></button>
                    </span>
                    <table>
                        <tr>
                            <td>Nom de l'établissement</td>
                            <td><?=$school->getName()?></td>
                            <td><button class="btnEditName"><i class="fas fa-pen"></i></button></td>
                        </tr>
                        <tr>
                            <td>Administrateur principal</td>
                            <td><?=$school->getNameAdmin()?></td>
                            <td>
                                <?php
                                if ($school->getIdAdmin() === intval($_SESSION['id'])) {
                                    echo '<button class="btnEditAdmin"><i class="fas fa-pen"></i></button>';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Mail</td>
                            <td><?=$school->getMail()?></td>
                            <td><button class="btnEditMail"><i class="fas fa-pen"></i></button></td>
                        </tr>
                        <tr>
                            <td>Code d'affiliation</td>
                            <td><?=$school->getCode()?></td>
                            <td><button class="btnEditCode"><i class="fas fa-pen"></i></button></td>
                        </tr>
                        <tr>
                            <td>Nombre de comptes</td>
                            <td>Total - <?=$school->getNbEleve()?> | Actif - <?=$school->getNbActiveAccount()?></td>
                            <td><a href="indexAdmin.php?action=moderatUsers"><button><i class="fas fa-eye"></i></button></a></td>
                        </tr>
                        <tr>
                            <td>Logo</td>
                            <td><?=$school->getLogo() === 'public/images/question-mark.png' ? 'Logo par défaut' : 'Logo personnalisé';?></td>
                            <td><button class="btnEditLogo"><i class="fas fa-pen"></i></button></td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php
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
            <input type="hidden" name="schoolName" value="<?=$school->getName()?>">

            <p id="blockName">
                <label for="editName">Nouveau nom de l'établissement</label>
                <input type="text" name="editName">
            </p>
            <?php
            if ($school->getIdAdmin() === intval($_SESSION['id'])) {
                ?>
                <p id="blockAdmin">
                    <label for="editAdmin">Identifiant du compte du nouvel Administrateur</label>
                    <input type="text" name="editAdmin">
                </p>
                <?php
            }
            ?>
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
                    <input type="file" name="uploadLogo" accept=".jpeg, .jpg, .jfif, .png, .gif">
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
