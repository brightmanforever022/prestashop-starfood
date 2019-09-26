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
 
$errors = false;
?>
<style type="text/css">
.btn {
	background: linear-gradient(#e2efff, #d3e7ff) repeat scroll 0 0 rgba(0, 0, 0, 0);
    border: 1px solid #a4bed4;
    color: #34404b;
    font-size: 11px;
    height: 27px;
    overflow: hidden;
    position: relative;
	font-weight: bold;
	cursor: pointer;
	float: right;
	margin-top: 6px;
}

div { font-family: Tahoma;
    font-size: 11px !important; }
</style>
<div style="border: 1px solid #A4BED4; display: block;  height: 416px;  margin-top: 22px;margin-left: 12px; width: 738px;">
	<form action="index.php?ajax=1&act=cat_win-categorysynch_confirm&id_lang=<?php echo $sc_agent->id_lang ?>" method="post">
		
	<div style="display: block; width: 718px; height: 358px; padding: 10px; overflow: auto;">
		<?php 
		$id_shop_selected = SCI::getSelectedShop();

		$checked_shops = SCI::getSelectedShopActionList();
		
		$shops = Shop::getShops(false);
		
		?>
		<div style="width: 300px; float: left;">
			<?php
			echo "<strong>"._l('You want to synchronize the categories positions on the Shop')._l(':')."</strong>"."<br/><br/>";
			foreach($shops as $shop)
			{
				$checked = "";
				if($shop["id_shop"]==$id_shop_selected)
					$checked = 'checked';
				
				echo '<input type="radio" name="selected_shop" value="'.$shop["id_shop"].'" '.$checked.' /> '.$shop["name"]."<br/>";
			}
			?>
		</div>
		<div style="width: 350px; float: right;">
			<?php
			echo "<br/><strong>"._l('With Shop(s)')._l(':')."</strong>"."<br/><br/>";
			foreach($shops as $shop)
			{
				$checked = "";
				if(sc_in_array($shop["id_shop"], $checked_shops,"catWinCategSynchChoice_shops"))
					$checked = 'checked';
				
				echo '<input type="checkbox" name="checked_shops[]" value="'.$shop["id_shop"].'" '.$checked.' /> '.$shop["name"]."<br/>";
			}
			?>
		</div>
	</div>
	
	<div style="display: block; width: 733px; height: 38px;">
		<?php if(!$errors) { ?>
		<button class="btn" type="submit"><?php echo _l('Next step');?></button>
		<?php } ?>
	</div>
	
	</form>
</div>