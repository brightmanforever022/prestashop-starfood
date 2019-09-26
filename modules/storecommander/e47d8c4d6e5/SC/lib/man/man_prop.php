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
	function inArray(array, p_val) {
	    var l = array.length;
	    for(var i = 0; i < l; i++) {
	        if(array[i] == p_val) {
	            return true;
	        }
	    }
	    return false;
	}

	var allowed_properties_panel = new Array();
	prop_tb=dhxLayout.cells('b').attachToolbar();
	prop_tb._sb=dhxLayout.cells('b').attachStatusBar();
	icons=Array(
<?php
	echo eval('?>'.$pluginManufacturerProperties['Title'].'<?php ');
?>
							);
<?php
	echo eval('?>'.$pluginManufacturerProperties['ToolbarButtons'].'<?php ');
?>


	prop_tb.addButtonSelect('panel',0,'<?php echo _l('Descriptions',1)?>',icons,'lib/img/application_form_magnify.png','lib/img/application_form_magnify.png',false,true);
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
		<?php echo $prop_toolbar_js_action; ?>
	}

	prop_tb.attachEvent("onClick", setPropertiesPanel);

	function setPropertiesPanelState(id,state){
<?php
	echo eval('?>'.$pluginManufacturerProperties['ToolbarStateActions'].'<?php ');
?>
	}

	prop_tb.attachEvent("onStateChange", setPropertiesPanelState);


//#####################################
//############ Load functions
//#####################################

<?php
	echo eval('?>'.$pluginManufacturerProperties['DisplayPlugin'].'<?php ');
	
	//##################
	//##################
	//################## Add internal extensions
	//##################
	//##################
	
	@$files = scandir(SC_DIR.'lib/man/');
	foreach ($files as $item)
		if ($item != '.' && $item != '..')
			if (is_dir(SC_TOOLS_DIR.'lib/man/'.$item) && file_exists(SC_TOOLS_DIR.'lib/man/'.$item.'/man_'.$item.'_init.php') && substr($item,0,4)!='win-')
			// OVERRIDE
			{
				require_once(SC_TOOLS_DIR.'lib/man/'.$item.'/man_'.$item.'_init.php');
				if($item=="combination")
				{
					@$sub_files = scandir(SC_TOOLS_DIR.'lib/man/'.$item);
					foreach ($sub_files as $sub_item)
						if ($sub_item != '.' && $sub_item != '..')
							if (is_dir(SC_TOOLS_DIR.'lib/man/'.$item.'/'.$sub_item) && file_exists(SC_TOOLS_DIR.'lib/man/'.$item.'/'.$sub_item.'/man_'.$item.'_'.$sub_item.'_init.php'))
							{
								require_once(SC_TOOLS_DIR.'lib/man/'.$item.'/'.$sub_item.'/man_'.$item.'_'.$sub_item.'_init.php');
							}
				}
			}elseif (is_dir(SC_DIR.'lib/man/'.$item) && file_exists(SC_DIR.'lib/man/'.$item.'/man_'.$item.'_init.php') && substr($item,0,4)!='win-')
			// STANDARD BEHAVIOR
			{
				require_once(SC_DIR.'lib/man/'.$item.'/man_'.$item.'_init.php');
				if($item=="combination")
				{
					@$sub_files = scandir(SC_DIR.'lib/man/'.$item);
					foreach ($sub_files as $sub_item)
						if ($sub_item != '.' && $sub_item != '..')
							if (is_dir(SC_DIR.'lib/man/'.$item.'/'.$sub_item) && file_exists(SC_DIR.'lib/man/'.$item.'/'.$sub_item.'/man_'.$item.'_'.$sub_item.'_init.php'))
							{
								require_once(SC_DIR.'lib/man/'.$item.'/'.$sub_item.'/man_'.$item.'_'.$sub_item.'_init.php');
							}
				}
			}

?>


	var allTabs=["descriptions"];
	var localTabs=prop_tb.getAllListOptions('panel');
	for (var i = 0; i < allTabs.length; i++) {
		if (in_array(allTabs[i],localTabs))
			prop_tb.setListOptionPosition('panel',allTabs[i], i+1);
	}

	prop_tb.addButton("help", 1000, "", "lib/img/help.png", "lib/img/help.png");
	prop_tb.setItemToolTip('help','<?php echo _l('Help')?>');

	if(!inArray(allowed_properties_panel, propertiesPanel) && allowed_properties_panel.length>0)
		propertiesPanel = allowed_properties_panel[0];
	if(allowed_properties_panel.length>0)
		prop_tb.callEvent("onClick",[propertiesPanel]);
	else
	{
		prop_tb.unload();
		dhxLayout.cells('b').collapse();
	}


//#####################################
//############ SORT LIST OPTIONS
//#####################################
var all_panel_options = new Object();
prop_tb.forEachListOption('panel', function(optionId){
	 var pos = prop_tb.getListOptionPosition('panel', optionId);
	 var text = prop_tb.getListOptionText('panel', optionId);
	 all_panel_options[text] = optionId;
});

var myObj = all_panel_options,
keys = [],
k, i, len;

for (k in myObj)
{
    if (myObj.hasOwnProperty(k))
        keys.push(k);
}

function frsort(a,b) {
	return a.localeCompare(b);
}

keys.sort(frsort);
len = keys.length;

for (i = 0; i < len; i++)
{
    k = keys[i];
    prop_tb.setListOptionPosition('panel', myObj[k], (i*1+1));
}

</script>
