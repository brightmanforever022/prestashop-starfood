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
	$manufacturer_ids=(Tools::getValue('id_manufacturer'));
	$id_shop=(Tools::getValue('id_shop'));
	$link=new Link();
	$manufacturer_ids = explode(',',$manufacturer_ids);

	
	$xml='';

	function generateValue()
	{
		global $manufacturer_ids,$id_shop;
		foreach($manufacturer_ids as $id_manufacturer) {
			$xml .= '<row id="'.$id_manufacturer.'">';
			$xml .= '  <userdata name="id_manufacturer">'.(int)$id_manufacturer.'</userdata>';
			$cols = ['id_manufacturer','image'];
			foreach($cols as $col) {
				switch($col){
					case 'id_manufacturer':
						if(file_exists(_PS_MANU_IMG_DIR_.$id_manufacturer.'.jpg')) {
							$xml .= "<cell><![CDATA[" . $id_manufacturer . "]]></cell>";
						}
						break;
					case 'image':
						if (version_compare(_PS_VERSION_, '1.5.0.10', '>=')) {
							$shopUrl = new ShopUrl($id_shop);
							$shop_url = $shopUrl->getURL(Configuration::get('PS_SSL_ENABLED'));
						} else {
							$shop = new Shop($id_shop);
							if(Configuration::get('PS_SSL_ENABLED')) {
								$shop_url = 'https://'.$shop->domain_ssl.$shop->getBaseURI().'/';
							} else {
								$shop_url = 'http://'.$shop->domain.$shop->getBaseURI().'/';
							}
						}
						$to_img = 'img/m/'.$id_manufacturer.'.jpg';
						if(file_exists(_PS_MANU_IMG_DIR_.$id_manufacturer.'.jpg')) {
							$xml .= "<cell><![CDATA[<img src=\"".$shop_url.$to_img."?time=".time()."\" width=\"100%\"/>]]></cell>";
						} else {
							$xml .= "<cell></cell>";
						}
						break;
				}
			}
			$xml .= '</row>';
		}
		return $xml;
	}



//XML HEADER
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
?>
<rows id="0">
	<head>
		<beforeInit>
			<call command="attachHeader"><param><![CDATA[#text_filter,#text_filter]]></param></call>
		</beforeInit>
		<column id="id_manufacturer" width="100" type="ro" align="center" sort="int"><?php echo _l('ID manufacturer')?></column>
		<column id="image" width="200" type="ro" align="center" sort="str" color=""><?php echo _l('Image')?></column>
		<afterInit>
			<call command="enableMultiselect"><param>1</param></call>
		</afterInit>
	</head>
	<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('man_image').'</userdata>'."\n";
	echo generateValue();
	?>
</rows>

