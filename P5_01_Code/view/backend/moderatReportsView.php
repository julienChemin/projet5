<section id="moderatReports" class="container">
	<?php
	if (isset($data['reportsFromElem'])) {
		$_GET['elem'] === 'post' ? $idPost = $data['reportsFromElem']->getId() : $idPost = $data['reportsFromElem']->getIdPost();
		//reports from one comment / post
		?>
		<article id="content" class="moderatReportsFromElem">
			<div id="btnDeleteReportsFromElem">
				<p>Définir ce contenu comme traité</p>
				<i class="fas fa-check"></i>
			</div>
			<p><a href="index.php?action=post&id=<?=$idPost?>">Voir le contenu concerné</a></p>
			<table>
				<thead>
					<th>Qui a signalé</th>
					<th>Pourquoi</th>
					<th>Supprimer</th>
				</thead>
				<tbody></tbody>
			</table>
			<ul id="paging"></ul>
		</article>
		<div id="noContent" class="blockStyleOne">Il n'y a pas de signalement</div>
		<?php
	} else {
		//all reports
		?>
		<nav class="fullWidth">
			<ul>
				<li id="btnReportPost">
					Publications signalées
				</li>
				<li id="btnReportComment">
					Commentaires signalés
				</li>
			</ul>
		</nav>
		<article id="content">
			<table>
				<thead>
					<th>Qui a signalé</th>
					<th>Pourquoi</th>
					<th>voir</th>
				</thead>
				<tbody></tbody>
			</table>
			<ul id="paging"></ul>
		</article>
		<div id="noContent" class="blockStyleOne">Il n'y a pas de signalement</div>
		<?php
	}
	?>
</section>