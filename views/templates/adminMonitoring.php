<?php 
    /** 
     * Affichage de la partie admin MONITORING : liste des articles avec tri et un bouton pour gérer la suppression d'un commentaire.  
     */
require_once('adminTabs.php')
?>

<h2>Monitoring des articles</h2>

<div class="adminArticle">
    <?php foreach ($articles as $article) { ?>
        <div class="articleLine">
            <div class="title"><?= $article->getTitle() ?></div>
            <div class="content"><?= $article->getContent(200) ?></div>
            <div><a class="submit" href="index.php?action=showUpdateArticleForm&id=<?= $article->getId() ?>">Modifier</a></div>
            <div><a class="submit" href="index.php?action=deleteArticle&id=<?= $article->getId() ?>" <?= Utils::askConfirmation("Êtes-vous sûr de vouloir supprimer cet article ?") ?> >Supprimer</a></div>
        </div>
    <?php } ?>
</div>

<a class="submit" href="index.php?action=showUpdateArticleForm">Ajouter un article</a>