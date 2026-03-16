<?php 
    /** 
     * Affichage de la partie admin MONITORING : liste des articles avec tri et un bouton pour gérer la suppression d'un commentaire.  
     */
require_once('adminTabs.php')
?>

<h2>Monitoring des articles</h2>

<div class="adminMonitoring">
    <table class="monitoringTable">
        <thead>
            <tr>
                <th><a href="index.php?action=admin&panel=monitoring&sort=<?= $sortLinks['title'] ?>">
                        Titre <?= $sortIcons['title'] ?>
                    </a>
                </th>
                <th><a href="index.php?action=admin&panel=monitoring&sort=<?= $sortLinks['views'] ?>">
                        Vues <?= $sortIcons['views'] ?>
                    </a>
                </th>
                <th><a href="index.php?action=admin&panel=monitoring&sort=<?= $sortLinks['comments'] ?>">
                        Commentaires <?= $sortIcons['comments'] ?>
                    </a>
                </th>
                <th><a href="index.php?action=admin&panel=monitoring&sort=<?= $sortLinks['date'] ?>">
                        Date de création <?= $sortIcons['date'] ?>
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($articles as $article) { ?>
                <tr>
                    <td><?= $article->getTitle() ?></td>
                    <td><?= $article->getViews() ?></td>
                    <td><?= $article->getCommentsCount() ?></td>
                    <td><?= $article->getDateCreation()->format('d/m/Y') ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
