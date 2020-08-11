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
                            <img src='<?=$school->getLogo()?>'>
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
                            <td><button class="btnEditName">Modifier</button></td>
                        </tr>
                        <tr>
                            <td>Administrateur</td>
                            <td><?=$school->getNameAdmin()?></td>
                            <td><button class="btnEditAdmin">Modifier</button></td>
                        </tr>
                        <tr>
                            <td>Mail</td>
                            <td><?=$school->getMail()?></td>
                            <td><button class="btnEditMail">Modifier</button></td>
                        </tr>
                        <tr>
                            <td>Code d'affiliation</td>
                            <td><?=$school->getCode()?></td>
                            <td><button class="btnEditCode">Modifier</button></td>
                        </tr>
                        <tr>
                            <td>Nombre de comptes</td>
                            <td>Total - <?=$school->getNbEleve()?> | Actif - <?=$school->getNbActiveAccount()?></td>
                            <td><a href="indexAdmin.php?action=moderatUsers"><button>Consulter</button></a></td>
                        </tr>
                        <tr>
                            <td>Logo</td>
                            <td><?=$school->getLogo() === 'public/images/question-mark.png' ? 'Logo par défaut' : 'Logo personnalisé';?></td>
                            <td><button class="btnEditLogo">Modifier</button></td>
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
            <p id="blockMail">
            <label for="editMail">Nouvelle adresse mail de l'établissement</label>
                <input type="text" name="editMail">
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
