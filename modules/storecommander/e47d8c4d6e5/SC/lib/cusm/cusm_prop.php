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

	var allowed_properties_panel = new Array();
	prop_tb=dhxLayout.cells('b').attachToolbar();
	prop_tb._sb=dhxLayout.cells('b').attachStatusBar();
	icons=Array(
<?php
	if (0)
		echo eval('?>'.$pluginProductProperties['Title'].'<?php ');
?>
							);
<?php
	if (0)
		echo eval('?>'.$pluginProductProperties['ToolbarButtons'].'<?php ');
?>


	prop_tb.addButtonSelect('panel',0,'<?php echo _l('Orders and products',1)?>',icons,'lib/img/application_form_magnify.png','lib/img/application_form_magnify.png',false,true);
	prop_tb.setItemToolTip('panel','<?php echo _l('Select properties panel',1)?>');

	function hidePropTBButtons()
	{
		prop_tb.forEachItem(function(itemId){
        prop_tb.hideItem(itemId);
    });
    prop_tb.showItem('panel');
    prop_tb.showItem('help');
	}

	function setPropertiesPanel(id){
		if (id=='help'){
			<?php echo "window.open('".getHelpLink('cusm_toolbar_prod_prop')."');"; ?>
		}
		
		prop_tb._sb.setText('');

<?php
	if (0)
		echo eval('?>'.$pluginProductProperties['ToolbarActions'].'<?php ');
?>
		dhxLayout.cells('b').showHeader();
	}

	prop_tb.attachEvent("onClick", setPropertiesPanel);

	function setPropertiesPanelState(id,state){
<?php
	if (0)
		echo eval('?>'.$pluginProductProperties['ToolbarStateActions'].'<?php ');
?>
	}

	prop_tb.attachEvent("onStateChange", setPropertiesPanelState);


//#####################################
//############ Load functions
//#####################################

<?php
	echo eval('?>'.$pluginProductProperties['DisplayPlugin'].'<?php ');

	
	//##################
	//##################
	//################## Add internal extensions
	//##################
	//##################
	
	@$files = scandir(SC_DIR.'lib/cusm/');
	foreach ($files as $item)
		if ($item != '.' && $item != '..')
			if (is_dir(SC_DIR.'lib/cusm/'.$item) && file_exists(SC_DIR.'lib/cusm/'.$item.'/cusm_'.$item.'_init.php') && substr($item,0,4)!='win-')
				require_once(SC_DIR.'lib/cusm/'.$item.'/cusm_'.$item.'_init.php');

?>



	prop_tb.addButton("help", 1000, "", "lib/img/help.png", "lib/img/help.png");
	prop_tb.setItemToolTip('help','<?php echo _l('Help')?>');

	if(!in_array(propertiesPanel, allowed_properties_panel) && allowed_properties_panel.length>0)
		propertiesPanel = allowed_properties_panel[0];
	if(allowed_properties_panel.length>0)
		prop_tb.callEvent("onClick",[propertiesPanel]);
	else
	{
		prop_tb.unload();
		dhxLayout.cells('b').collapse();
	}
	
</script>