<?php

namespace Chemin\ArtSchool\Model;

class Frontend
{
	public function home()
	{
		$PostsManager = new PostsManager();

		RenderView::render('template.php', 'frontend/indexView.php', ['slide' => true]);
	}

	public function error(string $error_msg)
	{
		RenderView::render('template.php', 'frontend/errorView.php', ['error_msg' => $error_msg]);
	}	
}
