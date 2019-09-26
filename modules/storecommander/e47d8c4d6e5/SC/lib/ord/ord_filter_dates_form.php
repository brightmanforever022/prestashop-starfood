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

$invoice = Tools::getValue("inv", 0);
$iso_lang = UISettings::getSetting('forceSCLangIso');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Store Commander</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="lib/js/jquery-ui/css/cupertino/jquery-ui-1.10.4.custom.min.css">
<script type="text/javascript" src="<?php echo SC_JQUERY;?>"></script>
<script src="lib/js/jquery-ui/js/jquery-ui-1.10.4.custom.min.js"></script>
<script src="lib/js/jquery-ui/js/jquery.ui.datepicker-fr.js"></script>
<script type="text/javascript" src="lib/js/jquery.cokie.js"></script>
<script type="text/javascript" src="<?php echo SC_JSFUNCTIONS;?>"></script>
</head>
<body>

	<p><?php echo _l('From:')?> <input type="text" class="datepicker" id="datepicker_from"></p>
	<p><?php echo _l('To:')?> <input type="text" class="datepicker" id="datepicker_to"></p>
	
	<button id="btn_close" class="ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all" style="float: right; margin-right: 10px;" type="button"><?php echo _l('Submit',1)?></button>
	
	 <script>
	$(function() {
		
		$( ".datepicker" ).datepicker(<?php if(!empty($iso_lang)) { ?>$.datepicker.regional["<?php echo $iso_lang; ?>"]).datepicker( "option",<?php } ?>{
			showOtherMonths: true,
			selectOtherMonths: true,
			showButtonPanel: true,
			 changeMonth: true,
			 changeYear: true,
			 dateFormat: "yy-mm-dd"
		});
		
		var temp = $.cookie('sc_ord<?php if(!empty($invoice)) echo "_inv"; ?>_fromto_dates');
		if(temp!=undefined && temp!=null && temp!="")
		{
			var dates = temp.split("_");
			$("#datepicker_from").val(dates[0]);
			if(dates[1]!=undefined && dates[1]!=null && dates[1]!="")
				$("#datepicker_to").val(dates[1]);
		}

		$("#btn_close").click(function(){
			if($("#datepicker_from").val()=="" || $("#datepicker_to").val()=="")
				parent.dhtmlx.message({text:'<?php echo _l('You must write the two dates.',1)?>',type:'error',expire:10000});
			else if($("#datepicker_to").val() < $("#datepicker_from").val())
				parent.dhtmlx.message({text:'<?php echo _l('Your dates are wrong.',1)?>',type:'error',expire:10000});
			else
			{
				<?php if(!empty($invoice)) { ?>
				$.cookie('sc_ord_inv_fromto_dates',$("#datepicker_from").val()+"_"+$("#datepicker_to").val(), { expires: 60 });
				parent.periodselection = "inv_from_to_"+$("#datepicker_from").val()+"_"+$("#datepicker_to").val();
				$.cookie('sc_ord_periodselection',parent.periodselection, { expires: 60 });
				parent.ord_filter.setItemText('inv_from_to','<?php echo _l('Inv from'); ?> '+$("#datepicker_from").val()+" <?php echo _l('to'); ?> "+$("#datepicker_to").val());
				<?php } else { ?>
				$.cookie('sc_ord_fromto_dates',$("#datepicker_from").val()+"_"+$("#datepicker_to").val(), { expires: 60 });
				parent.periodselection = "from_to_"+$("#datepicker_from").val()+"_"+$("#datepicker_to").val();
				$.cookie('sc_ord_periodselection',parent.periodselection, { expires: 60 });
				parent.ord_filter.setItemText('from_to','<?php echo _l('Ord from'); ?> '+$("#datepicker_from").val()+" <?php echo _l('to'); ?> "+$("#datepicker_to").val());
				<?php } ?>

				parent.displayOrders();
				parent.wOrdFilterFromTo.close();
			}
		});
	});
	</script>
	
</body>
</html>