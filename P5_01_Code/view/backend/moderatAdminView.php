<section id="blockModeratAdmin" class="container">
    <h1 class="fullWidth">Modération de l'administration</h1>
    <hr>
    <article id="moderatAdmin">
        <?php
        //display user's school
        $school = $data['schools'];
        if (isset($data['message'])) {
            ?>
            <div class="blockStyleOne blockMsg">
                <i class="fas fa-times orang"></i>
                <p class="msg orang"><?=$data['message']?></p>
            </div>
            <?php
        }
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
            <div class="tableAdminModerator">
                <div id="listAction" class="blockStyleOne fullWidth">
                    <ul>
                        <li><i class="fas fa-plus-square"></i>Passer en administrateur</li>
                        <li><i class="fas fa-minus-square"></i>Rétrograder le compte</li>
                        <li><i class="fas fa-times"></i>Supprimer le compte</li>
                    </ul>
                </div>
                <table> 
                    <tr>
                        <th>Administrateur(s)</th>
                        <th>Passer en modérateur</th>
                    </tr>
                    <?php
                    //display all admins
                    foreach ($data['users'] as $user) {
                        if ($user->getIsAdmin()) {
                            ?>
                            <tr>
                                <td>
                                    <a href="index.php?action=userProfile&userId=<?=$user->getId()?>">
                                        <?=$user->getLastName()?> <?=$user->getFirstName()?>
                                    </a>
                                </td>
                                <?php
                                if ($user->getId() !== $school->getIdAdmin() && $user->getId() !== intval($_SESSION['id'])) {
                                    echo '<td><i class="far fa-minus-square toModerator" userpseudo="' . $user->getPseudo() . '" schoolname="' . $school->getName() . '"></i></td>';
                                } else {
                                    echo '<td><i class="far fa-minus-square inactifLink"></i></td>';
                                }
                                ?>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </table>
                <?php
                if ($data['nbModerator'] > 0) {
                    //display all moderator
                    ?>
                    <table> 
                        <tr>
                            <th>Modérateur(s)</th>
                            <th>Action</th>
                        </tr>
                        <?php
                        foreach ($data['users'] as $user) {
                            if ($user->getIsModerator()) {
                                ?>
                                <tr>
                                    <td>
                                        <a href="index.php?action=userProfile&userId=<?=$user->getId()?>">
                                            <?=$user->getLastName()?> <?=$user->getFirstName()?>
                                        </a>
                                    </td>
                                    <td>
                                        <i class="far fa-plus-square toAdmin" userpseudo="<?=$user->getPseudo()?>" schoolname="<?=$school->getName()?>"></i> - 
                                        <i class="far fa-minus-square toNormalUser" userpseudo="<?=$user->getPseudo()?>" schoolname="<?=$school->getName()?>"></i> - 
                                        <i class="fas fa-times toDelete" userpseudo="<?=$user->getPseudo()?>" schoolname="<?=$school->getName()?>" ></i>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                    echo '</table>';
                } else {
                    // there is no moderator to display
                    ?>
                    <div class="blockStyleOne fullWidth">
                        <p class="msg orang textCenter">Aucun modérateur actuellement</p>
                    </div>
                    <?php
                }
                if ($school->getIsActive()) {
                    ?>
                    <form class="formAddModerator" method="POST" action="indexAdmin.php?action=moderatAdmin&amp;option=addModerator">
                        <p class="orang">Ajouter un modérateur >></p>
                        <div>
                            <hr>
                            <h3>Information sur le nouveau modérateur</h3>
                            <div>
                                <p>
                                    <label for="moderatorName">Identifiant</label>
                                    <input type="text" name="moderatorName" required="">
                                </p>
                                <p>
                                    <label for="moderatorMail">Adresse email</label>
                                    <input type="email" name="moderatorMail" required="">
                                </p>
                                <p>
                                    <label for="moderatorPassword">Mot de passe</label>
                                    <input type="password" name="moderatorPassword" required="">
                                </p>
                                <p>
                                    <label for="moderatorConfirmPassword">Confirmez le mot de passe</label>
                                    <input type="password" name="moderatorConfirmPassword" required="">
                                </p>
                                <p>
                                    <label for="moderatorLastName">Nom</label>
                                    <input type="text" name="moderatorLastName" required="">
                                </p>
                                <p>
                                    <label for="moderatorFirstName">Prénom</label>
                                    <input type="text" name="moderatorFirstName" required="">
                                </p>
                            </div>
                            <div>
                                <p>
                                    <input type="hidden" name="schoolName" value="<?=$school->getName()?>">
                                    <input type="submit" name="submit" value="Valider">
                                    <input type="button" name="cancel" value="Annuler">
                                </p>
                            </div>
                        </div>
                    </form>
                    <?php
                }
                ?>
            </div>
        </div>
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
