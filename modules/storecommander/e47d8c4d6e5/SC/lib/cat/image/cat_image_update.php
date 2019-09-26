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
	$id_product=intval(Tools::getValue('id_product',0));
	$list_id_image=Tools::getValue('list_id_image',0);
	$action=Tools::getValue('action',0);
	$id_image_array=explode(',',$list_id_image);
	
	$multiple = false;
	if(strpos($id_product, ",") !== false)
		$multiple = true;

	if($action=="image_fill_legend_current_lang" || $action=="image_fill_legend_all_lang")
	{
		$id_products=(Tools::getValue('id_product',0));
		$cache_product_name = array();
	}

	foreach($id_image_array as $id_image)
	{
		switch($action){
			case 'update':
				$image=new Image((int)($id_image));
				//$id_product = $image->id;
				$col=Tools::getValue('col',0);
				$val=Tools::getValue('val',0);
				$fields=array('cover');
				$fields_lang=array('legend');
				$idlangByISO=array();
				$todo=array();
				$todo_lang=array();
				foreach($languages AS $lang)
				{
					$fields_lang[]='name¤'.$lang['iso_code'];
					$idlangByISO[$lang['iso_code']]=$lang['id_lang'];
				}
				SC_Ext::readCustomImageGridConfigXML("update_inArrayFields");
				foreach($fields AS $field)
				{
					if ($col==$field)
					{
						if ($col=='cover'){
							$sql="";
							if(version_compare(_PS_VERSION_, '1.6.1', '>='))
								$sql=("UPDATE "._DB_PREFIX_."image SET cover=NULL WHERE id_product=".intval($id_product));
							else
								$sql=("UPDATE "._DB_PREFIX_."image SET cover=0 WHERE id_product=".intval($id_product));
							if(!empty($sql))
								Db::getInstance()->Execute($sql);
							if(version_compare(_PS_VERSION_, '1.6.1', '>='))
								$sql=("UPDATE "._DB_PREFIX_."image_shop SET cover=NULL WHERE id_product=".intval($id_product)." AND id_shop IN (".SCI::getSelectedShopActionList(true).")");
							elseif (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
								Db::getInstance()->Execute("UPDATE "._DB_PREFIX_."image_shop SET cover=0 WHERE id_image IN (SELECT i.id_image FROM "._DB_PREFIX_."image i WHERE i.id_product=".intval($id_product).") AND id_shop IN (".SCI::getSelectedShopActionList(true).")");
							if(!empty($sql))
								Db::getInstance()->Execute($sql);
							@unlink(_PS_TMP_IMG_DIR_.'product_'.(int)$id_product.'.jpg');
							@unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$id_product.'.jpg');

							if(version_compare(_PS_VERSION_,'1.5.0.0','>=')){
								$shops = SCI::getSelectedShopActionList(false, (int)$id_product);
								foreach ($shops as $shop_id){
									@unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int)$id_product . '_'.(int)$shop_id.'.jpg');
								}
							}

							if(version_compare(_PS_VERSION_, '1.6.1', '>='))
							{
								if(empty($val))
									$val = "NULL";
								else
									$val = "'".intval($val)."'";
								$todo[]=$field."=".$val;
							}		
							else
								$todo[]=$field."='".psql(html_entity_decode($val))."'";
						}
						else
							$todo[]=$field."='".psql(html_entity_decode($val))."'";
						addToHistory('image','modification',$field,$id_image,$id_lang,_DB_PREFIX_."image",psql(Tools::getValue($field)));
					}
				}
				foreach($fields_lang AS $field)
				{
					if ($col==$field)
					{
						$todo_lang[]="`".$field."`='".psql($val)."'";
						addToHistory('image','modification',$field,$id_image,$id_lang,_DB_PREFIX_."image_lang",$val);
					}
				}
				if (count($todo))
				{
					$sql = "UPDATE "._DB_PREFIX_."image SET ".join(' , ',$todo)." WHERE id_image=".intval($id_image);
					Db::getInstance()->Execute($sql);
					if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && $col=='cover')
					{
						$sql = "UPDATE "._DB_PREFIX_."image_shop SET ".join(' , ',$todo)." WHERE id_image=".intval($id_image)." AND id_shop IN (".SCI::getSelectedShopActionList(true).")";
						Db::getInstance()->Execute($sql);
					}
				}
				if (count($todo_lang))
				{
					$sql2 = "UPDATE "._DB_PREFIX_."image_lang SET ".join(' , ',$todo_lang)." WHERE id_image=".intval($id_image)." AND id_lang=".intval($id_lang);
					Db::getInstance()->Execute($sql2);
				}
				sc_ext::readCustomImageGridConfigXML('onAfterUpdateSQL');
				break;
			case 'image_fill_legend_current_lang':
				$image=new Image((int)($id_image));
				if(empty($cache_product_name[$image->id_product]))
				{
					if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
						$pduct = new Product((int)$image->id_product, false, (int)$id_lang,
							(int)SCI::getSelectedShop());
					} else {
						$pduct = new Product((int)$image->id_product, (int)$id_lang);
					}
					if (is_array($pduct->name)) {
						$pduct->name = $pduct->name[$id_lang];
					}
					$cache_product_name[$image->id_product] = $pduct->name;
				}
				if(!empty($cache_product_name[$image->id_product]))
				{
					$sql2 = "UPDATE " . _DB_PREFIX_ . "image_lang SET legend='" . pSQL($cache_product_name[$image->id_product]) . "' WHERE id_image=" . intval($id_image) . " AND id_lang=" . intval($id_lang);
					Db::getInstance()->Execute($sql2);
				}
				break;
			case 'image_fill_legend_all_lang':
				$image=new Image((int)($id_image));
				if(empty($cache_product_name[$image->id_product]))
				{
					if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
						$pduct = new Product((int)$image->id_product, false, null,
							(int)SCI::getSelectedShop());
					} else {
						$pduct = new Product((int)$image->id_product);
					}
					$cache_product_name[$image->id_product]= $pduct->name;
				}
				if(!empty($cache_product_name[$image->id_product]))
				{
					$sql = "SELECT DISTINCT id_lang FROM " . _DB_PREFIX_ . "image_lang";
					$res = Db::getInstance()->ExecuteS($sql);
					foreach ($res as $language)
					{
						if(!empty($cache_product_name[$image->id_product][$language['id_lang']]))
						{
							$sql2 = "UPDATE " . _DB_PREFIX_ . "image_lang SET legend='" . pSQL($cache_product_name[$image->id_product][$language['id_lang']]) . "' WHERE id_image=" . intval($id_image). " AND id_lang=" . intval($language['id_lang']);
							Db::getInstance()->Execute($sql2);
						}
					}
				}
				break;
			case 'shop':
				$id_shop=Tools::getValue('shop',0);
                $value=Tools::getValue('val',0);
                $is_cover=Tools::getValue('is_cover',0);
				if(!empty($id_shop))
				{
                    $image=new Image((int)($id_image));
					//$pduct = new Product($image->id_product);
					if(!$image->isAssociatedToShop($id_shop) && $value=="1")
					{
						//SCI::addToShops('image', array((int)$id_image), array((int)$id_shop));
						if(version_compare(_PS_VERSION_, '1.6.1', '>='))
                        {
                            if(version_compare(_PS_VERSION_, '1.6.1.8', '>=') && empty($is_cover))
                                $is_cover = "NULL";
                            else
                                $is_cover = "'".(int)$is_cover."'";
                            $img = Db::getInstance()->getRow('
							SELECT `id_product` FROM `'._DB_PREFIX_.'image`
							WHERE `id_image` = '.intval($id_image).'');
                            $sql="INSERT INTO "._DB_PREFIX_."image_shop (id_shop,id_image, id_product, cover) VALUES ('".(int)$id_shop."','".(int)$id_image."','".(int)$img['id_product']."',".$is_cover.")";
                        }
						else
							$sql="INSERT INTO "._DB_PREFIX_."image_shop (id_shop,id_image) VALUES ('".(int)$id_shop."','".(int)$id_image."')";
						Db::getInstance()->Execute($sql);
					}
					elseif($image->isAssociatedToShop($id_shop) && empty($value))
					{
						$sql = "DELETE FROM `"._DB_PREFIX_."image_shop` WHERE `id_image` = '".psql($id_image)."' AND id_shop = '".psql($id_shop)."'";
						Db::getInstance()->Execute($sql);
					}
				}
				sc_ext::readCustomImageGridConfigXML('onAfterUpdateSQL');
				break;
			case 'delete':
					$image=new Image((int)($id_image));
					$id_product = $image->id_product;
					if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
					{
						$image->deleteProductAttributeImage();
						$image->deleteImage();
						
						Db::getInstance()->Execute('
									DELETE FROM `'._DB_PREFIX_.'image_lang`
									WHERE `id_image` = '.intval($id_image)."");
						Db::getInstance()->Execute('
									DELETE FROM `'._DB_PREFIX_.'image_shop`
									WHERE `id_image` = '.intval($id_image)."");
						Db::getInstance()->Execute('
									DELETE FROM `'._DB_PREFIX_.'product_attribute_image`
									WHERE `id_image` = '.intval($id_image)."");
						Db::getInstance()->Execute('
									DELETE FROM `'._DB_PREFIX_.'image`
									WHERE `id_image` = '.intval($id_image)."");
					}
					else
						$image->delete();
						@unlink(_PS_TMP_IMG_DIR_.'product_'.(int)$id_product.'.jpg');
						@unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$id_product.'.jpg');
					// Vérification que le produit à une image par défaut
					$has_cover = Db::getInstance()->getRow('
						SELECT id_image FROM `'._DB_PREFIX_.'image`
						WHERE `id_product` = '.(int)$id_product.'
						AND `cover`= 1');
					if (empty($has_cover["id_image"]))
					{
						$first_img = Db::getInstance()->getRow('
							SELECT `id_image` FROM `'._DB_PREFIX_.'image`
							WHERE `id_product` = '.intval($id_product).'
							ORDER BY position ASC');
						Db::getInstance()->Execute('
							UPDATE `'._DB_PREFIX_.'image`
							SET `cover` = 1
							WHERE `id_image` = '.intval($first_img['id_image']));
					}
					// Vérification que le produit à une image par défaut
					// pour chaque boutique associée
					if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
					{
						$shops = SCI::getSelectedShopActionList(false, (int)$id_product);
						foreach($shops as $shop_id)
						{
							$has_cover = Db::getInstance()->getRow('
								SELECT ishop.id_image
								FROM `'._DB_PREFIX_.'image_shop` ishop
									INNER JOIN `'._DB_PREFIX_.'image` i ON ishop.id_image = i.id_image
								WHERE i.`id_product` = '.(int)$id_product.'
								AND ishop.`cover`= 1
								AND ishop.id_shop = "'.(int)$shop_id.'"');
							if (empty($has_cover["id_image"]))
							{
								$first_img = Db::getInstance()->getRow('
										SELECT ishop.`id_image`
										FROM `'._DB_PREFIX_.'image_shop` ishop
											INNER JOIN `'._DB_PREFIX_.'image` i ON ishop.id_image = i.id_image
										WHERE i.`id_product` = '.intval($id_product).'
											AND ishop.id_shop = "'.(int)$shop_id.'"
										ORDER BY i.position ASC');
								Db::getInstance()->Execute('
										UPDATE `'._DB_PREFIX_.'image_shop`
										SET `cover` = 1
										WHERE `id_image` = '.intval($first_img['id_image']).'
											AND id_shop = "'.(int)$shop_id.'"');
							}
							@unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int)$id_product.'_'.(int)$shop_id.'.jpg');
						}
					}
				break;
			case 'position':
				if(!$multiple)
				{
					$todo=array();
					$row=explode(';',Tools::getValue('positions'));
					$high_position = Image::getHighestPosition($id_product) + 1;
					$todo[]="UPDATE "._DB_PREFIX_."image SET position=(position+".$high_position.") WHERE id_product=".intval($id_product);
					foreach($row AS $v)
					{
						if ($v!='')
						{
							$pos=explode(',',$v);
							$todo[]="UPDATE "._DB_PREFIX_."image SET position=".(intval($pos[1])+1)." WHERE id_product=".intval($id_product)." AND id_image=".intval($pos[0]);
						}
					}
					foreach($todo AS $task)
					{
						Db::getInstance()->Execute($task);
					}
				}
				sc_ext::readCustomImageGridConfigXML('onAfterUpdateSQL');
				break;
			default:
				break;
		}
	}

if(!empty($id_product)) {
	//update date_upd
	$sql = "UPDATE "._DB_PREFIX_."product SET date_upd = '".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product IN (".pSQL($id_product).");";
	if(SCMS) {
		$sql .= "UPDATE "._DB_PREFIX_."product_shop SET date_upd = '".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product IN (".pSQL($id_product).") AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true)).")";
	}
	Db::getInstance()->Execute($sql);
	// PM Cache
	ExtensionPMCM::clearFromIdsProduct($id_product);
}
