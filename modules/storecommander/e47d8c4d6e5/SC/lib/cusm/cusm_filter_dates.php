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
<script type="text/javascript">
dhxlCusmFilterFromTo=wCusmFilterFromTo.attachLayout("1C");
dhxlCusmFilterFromTo.cells('a').hideHeader();
dhxlCusmFilterFromTo.cells('a').attachURL("index.php?ajax=1&act=cusm_filter_dates_form&id_lang="+SC_ID_LANG);
</script>
