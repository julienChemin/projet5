<section id="resetPassword" class="container">
    <?php
    if (isset($data['user'])) {
        ?>
        <form id="formResetPassword" method="POST" action="index.php?action=resetPassword">
            <div>
                <p>
                    <label for="newPassword">Nouveau mot de passe</label>
                    <input type="password" name="newPassword" required="">
                </p>
                <p>
                    <label for="confirmNewPassword">Confirmez votre nouveau mot de passe</label>
                    <input type="password" name="confirmNewPassword" required="">
                </p>
                <p>
                    <input type="hidden" name="id" value=<?=htmlspecialchars($_GET['id'])?>>
                </p>
                <p>
                    <input type="hidden" name="key" value=<?=htmlspecialchars($_GET['key'])?>>
                </p>
                <p>
                    <input type="submit" name="submit" value="valider">
                </p>
            </div>
            <?php
            if (isset($data['message'])) {
                echo '<p class="infoError">' . $data['message'] . '</p>';
            }
            ?>
        </form>
        <?php
    } else {
        echo '<div>';
        if (isset($data['message'])) {
            echo '<p class="infoComment">' . $data['message'] . '</p>';
        }
        echo '<p class="infoComment"><a href="index.php">Retourner sur le site</a></p>';
        echo '</div>';
    }
    ?>
</section>
