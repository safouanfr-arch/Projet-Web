<?php

function main_article():string
{
    // model
    // http://4ipw3-aww/?page=article&ident_art=4
    // $_GET["ident_art"]  => 4
	$id = (int)($_GET['ident_art'] ?? 0);
	if ($id > 0) {
		$_SESSION['article_click_total'] = (int)($_SESSION['article_click_total'] ?? 0) + 1;
	}

	$article = ArticleModel::getById($id);
    
    // view
	return join( "\n", [
		html_head(get_menu()),
		html_article_detail($article),
		html_foot(),
	]);

}

