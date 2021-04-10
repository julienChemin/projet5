<section id="listTagsView">
    <h1>Liste des tags</h1>

    <?php
    foreach ($data['tags'] as $letter => $arrTags) {
        ?>
        <div class="tagsCategory container">
            <h2><?=$letter?></h2>
        </div>

        <section class="fullWidth">
            <div class="blockResult blockResultTag container">
            <?php
            foreach ($data['tags'][$letter] as $tag) {
                ?>
                <div>
                    <a href="index.php?action=search&sortBy=tag&tag=<?=$tag['name']?>">
                        <p class="tag"><?=$tag['name']?></p>
                        <span>- (<?=$tag['tagCount']?>)</span>
                    </a>
                </div>
                <?php
            }
            ?>
            </div>
        </section>

        <?php
    }
    ?>
</section>
