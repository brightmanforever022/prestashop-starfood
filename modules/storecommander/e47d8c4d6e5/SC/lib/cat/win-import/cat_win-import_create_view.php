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
$id_lang=intval(Tools::getValue('id_lang'));
$etape=intval(Tools::getValue('etape', "1"));
if($etape=="1")
{
	?>
	<img src="lib/img/loading.gif" alt="loading" title="loading" height="70px;" style="float: left;" />
	<div style="float: left;margin-left: 10px;font-family: Tahoma; font-size: 12px !important; line-height: 70px;"><?php echo _l("Preparing the import settings"); ?></div>
	<?php
}
elseif($etape=="2")
{
	$sql = "SELECT * FROM "._DB_PREFIX_."configuration WHERE name = 'PS_SC_IMPORT_VIEW' LIMIT 1";
	$res = Db::getInstance()->ExecuteS($sql);
	if(empty($res) || count($res)<=0)
	{
		$creating = 0;
		$already_exist = 0;
		
		// CHECK ALREADY EXIST
		$sql = "SELECT * FROM information_schema.tables WHERE table_name = '"._DB_PREFIX_."sc_import_index' LIMIT 1;";
		$res = Db::getInstance()->ExecuteS($sql);
		if(empty($res) || count($res)<=0)
		{
			$sql_create = "SELECT p.id_product, pa.id_product_attribute,
						p.date_upd,
						p.reference as p_reference, pa.reference as pa_reference,
						p.id_supplier,
						p.supplier_reference as p_supplier_reference, pa.supplier_reference as pa_supplier_reference
				FROM "._DB_PREFIX_."product p
					LEFT JOIN "._DB_PREFIX_."product_attribute pa ON (p.id_product = pa.id_product)
				GROUP BY p.id_product, pa.id_product_attribute";
			if(version_compare(_PS_VERSION_,'1.5.0.0','>='))
			{
				$sql_create = "SELECT p.id_product, pa.id_product_attribute,p_shop.id_shop,
						p.date_upd,
						p.reference as p_reference, pa.reference as pa_reference,
						p.id_supplier,
						p.supplier_reference as p_supplier_reference, pa.supplier_reference as pa_supplier_reference,CONCAT('||',GROUP_CONCAT( DISTINCT(ps.product_supplier_reference) SEPARATOR '||' ),'||') as product_supplier_reference
				FROM "._DB_PREFIX_."product p
					LEFT JOIN "._DB_PREFIX_."product_attribute pa ON (p.id_product = pa.id_product)
					LEFT JOIN "._DB_PREFIX_."product_shop p_shop on (p_shop.id_product=p.id_product)
					LEFT JOIN "._DB_PREFIX_."product_supplier ps on (ps.id_product=p.id_product AND ps.id_product_attribute=pa.id_product_attribute)
				GROUP BY p.id_product, pa.id_product_attribute,p_shop.id_shop";
			}
		
			$sql = "CREATE VIEW "._DB_PREFIX_."sc_import_index AS (".$sql_create.")";
			Db::getInstance()->Execute($sql);
			$creating = 1;
		}
		else
			$already_exist = 1;

		// CHECK EXIST AFTER CREATE
		if(empty($already_exist))
		{
			$sql = "SELECT * FROM information_schema.tables WHERE table_name = '"._DB_PREFIX_."sc_import_index' LIMIT 1;";
			$res = Db::getInstance()->ExecuteS($sql);
			if(!empty($res) && count($res)>0)
			{
				if(version_compare(_PS_VERSION_,'1.5.0.0','>='))
					Configuration::updateValue('PS_SC_IMPORT_VIEW', "1", 0, 0);
				else
					Configuration::updateValue('PS_SC_IMPORT_VIEW', "1");
				if($creating==1)
				{?>
					<div style="margin: 10px; padding: 20px; text-align: center; background: #EDFCE5; border: 1px #4A892A solid; color: #4A892A;">
						<?php echo _l("The import is ready"); ?>
					</div>
				<?php }
				else
				{?>
					<script>
					window.parent.wCatImportCreateView.close();
					</script>
				<?php }
			}
			else
			{
				if(version_compare(_PS_VERSION_,'1.5.0.0','>='))
					Configuration::updateValue('PS_SC_IMPORT_VIEW', "0", 0, 0);
				else
					Configuration::updateValue('PS_SC_IMPORT_VIEW', "0");
				if($creating==1)
				{?>
					<div style="margin: 5px; padding: 10px; text-align: center; background: #FBE6E7; border: 1px #A91113 solid; color: #A91113;">
						<?php echo _l("Notice: Store Commander could not optimize the database to reduce the time required for the import process. Please refer to this page for more information:"); ?> 
						<a href="<?php echo getHelpLink('cat_win-import_init'); ?>"><?php echo _l("Documentation"); ?></a>
					</div>
				<?php }
				else
				{?>
					<script>
					window.parent.wCatImportCreateView.close();
					</script>
				<?php }
			}
		}
		else
		{?>
			<script>
			window.parent.wCatImportCreateView.close();
			</script>
		<?php }
	}
	else
	{?>
		<script>
		window.parent.wCatImportCreateView.close();
		</script>
	<?php }
}