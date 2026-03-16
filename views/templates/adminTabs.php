<?php 
    /** 
     * Affichage de la partie admin : liste des articles avec un bouton "modifier" pour chacun. 
     * Et un formulaire pour ajouter un article. 
     */
?>
<div class="adminTabs">
    <a class="adminTab <?php echo $panel === 'edition' ? 'active' : ''; ?>" href="index.php?action=admin&panel=edition">Edition des articles</a>
    <a class="adminTab <?php echo $panel === 'monitoring' ? 'active' : ''; ?>" href="index.php?action=admin&panel=monitoring">Monitoring</a>
</div>
