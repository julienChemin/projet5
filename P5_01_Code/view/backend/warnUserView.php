<section id="blockWarnUser" class="container">
    <h1>Avertir un utilisateur</h1>
    <p>
        Vous êtes sur le point d'avertir cet utilisateur : 
        <a href="index.php?action=userProfile&userId=<?=$data['user']->getId()?>">
            <?=$data['user']->getPseudo()?> ( <?=$data['user']->getFirstName()?> <?=$data['user']->getLastName()?> )
        </a>
    </p>
    <p>Ce compte a déja reçu <?=$data['user']->getNbWarning()?> avertissement(s), dont <?=$data['nbActiveWarn']?> encore actif(s)</p>
    <?php
    if (!empty($data['banishmentInfo'])) {
        echo '<p>Ce compte est suspendu depuis le ' . $data['banishmentInfo']['dateBanishment'] . ' jusqu\'au ' . $data['banishmentInfo']['dateUnbanishment'] . '</p>';
    }
    ?>

    <form id="formWarnUser">
        <input type="hidden" name="idUser" value="<?=$_GET['idUser']?>">
        <h2>Informations complémentaires</h2>
        <p>
            <textarea name="reasonWarn" id="reasonWarn" placeholder="Indiquez la raison de l'avertissement" required></textarea>
        </p>
        <p id="msgBox" class="blockStyleOne orang hide"></p>
        <input type="submit" name="submit" id="submit" value="Avertir">
    </form>
</section>