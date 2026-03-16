<?php 
    /** 
     * Affichage de la navigation partie admin : EDITION / MONITORING.  
     */
?>
<div class="adminTabs">
    <a class="adminTab <?php echo $panel === 'edition' ? 'active' : ''; ?>" href="index.php?action=admin&panel=edition">Edition des articles</a>
    <a class="adminTab <?php echo $panel === 'monitoring' ? 'active' : ''; ?>" href="index.php?action=admin&panel=monitoring">Monitoring</a>
</div>
