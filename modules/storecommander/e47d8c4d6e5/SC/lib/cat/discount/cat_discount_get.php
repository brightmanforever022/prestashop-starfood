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
	$id_product=intval(Tools::getValue('id_product'));

	function getRowsFromDB(){
		global $id_lang,$id_product;

		$sql = '
		SELECT *
		FROM '._DB_PREFIX_.'discount_quantity
		WHERE id_product = '.intval($id_product).'
		ORDER BY quantity';
		$res=Db::getInstance()->ExecuteS($sql);
		$xml='';
		foreach ($res AS $image)
		{
			$xml.=("<row id='".$image['id_discount_quantity']."'>");
				$xml.=("<cell style=\"color:#999999\">".$image['id_discount_quantity']."</cell>");
				$xml.=("<cell>".$image['quantity']."</cell>");
				$xml.=("<cell>".$image['value'].($image['id_discount_type']==1?'%':'')."</cell>");
			$xml.=("</row>");
		}
		return $xml;
	}

	//XML HEADER
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); } else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");

	$xml=getRowsFromDB();
?>
<rows id="0">
<head>
<column id="id_discount_quantity" width="40" type="ro" align="right" sort="int"><?php echo _l('ID')?></column>
<column id="quantity" width="80" type="edtxt" align="right" sort="str"><?php echo _l('Quantity')?></column>
<column id="value" width="80" type="edtxt" align="right" sort="int"><?php echo _l('Discount')?></column>
<afterInit>
<call command="enableMultiselect"><param>1</param></call>
</afterInit>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_discount').'</userdata>'."\n";
	echo $xml;
?>
</rows>
