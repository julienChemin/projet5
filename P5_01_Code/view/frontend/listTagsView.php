<section id="listTagsView" class="container">
	<h1>Liste des tags</h1>
	<?php
	foreach ($data['tags'] as $letter => $arrTags) {
		?>
		<div class="tagsCategory">
			<h2><?=$letter?></h2>
			<hr>
		</div>
		<div class="blockResult blockResultTag fullWidth">
		<?php
		foreach ($data['tags'][$letter] as $tag) {
			?>
			<div>
				<a href="index.php?action=search&sortBy=tag&tag=<?=$tag['name']?>">
					<span class="tag"><?=$tag['name']?></span>
					<span>- (<?=$tag['tagCount']?>)</span>
				</a>
			</div>
			<?php
		}
		?>
		</div>
		<?php
	}
	?>
</section>