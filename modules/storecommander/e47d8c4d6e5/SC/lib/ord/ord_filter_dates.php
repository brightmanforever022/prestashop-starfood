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
dhxlOrdFilterFromTo=wOrdFilterFromTo.attachLayout("1C");
dhxlOrdFilterFromTo.cells('a').hideHeader();
dhxlOrdFilterFromTo.cells('a').attachURL("index.php?ajax=1&act=ord_filter_dates_form<?php if(!empty($_GET['inv'])) echo "&inv=1"; ?>&id_lang="+SC_ID_LANG);
</script>