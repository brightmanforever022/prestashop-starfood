<?php
/**
 * Store Commander
 *
 * @category administration
 * @author Store Commander - support@storecommander.com
 * @version 2015-09-15
 * @uses Prestashop modules
 * @since 2009
 * @copyright Copyright &copy; 2009-2015, Store Commander
 * @license commercial
 * All rights reserved! Copying, duplication strictly prohibited
 *
 * *****************************************
 * *           STORE COMMANDER             *
 * *   http://www.StoreCommander.com       *
 * *            V 2015-09-15               *
 * *****************************************
 *
 * Compatibility: PS version: 1.1 to 1.6.1
 *
 **/

?>
<script>
    var wTrialWindow = null;

    if (!dhxWins.isWindow("wTrialWindow"))
    {
        wTrialWindow = dhxWins.createWindow("wTrialWindow", 50, 50, 670, 550);
        wTrialWindow.setText('<?php echo _l('Your Trial period information',1)?>');
    }


    wTrialWindow.attachURL("index.php?ajax=1&act=all_gettrialtime&id_lang="+SC_ID_LANG+"&"+new Date().getTime());

</script>