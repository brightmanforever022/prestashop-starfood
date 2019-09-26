<?php

require_once(dirname(__FILE__) . '/SPVBase.php');

class AdminSameProductVariant extends AdminTab
{
    const ModeTuples = 1;
    const ModeSingleProds = 2;

    var $svpMode;  //! list display mode
    
    
	public function __construct()
	{
        $this->module = 'sameproductvariant';
        $this->noLink = true;

        // dealing with mode switch
        if (isset($_REQUEST['spv_mode']))
        {
            $this->spvMode = intval($_REQUEST['spv_mode']);
            Context::getContext()->cookie->SpvMode = $this->spvMode;
        }
        elseif(isset(Context::getContext()->cookie->SpvMode))
        {
            $this->spvMode = Context::getContext()->cookie->SpvMode;
        }
        else
        {
            $this->spvMode = self::ModeTuples;
        }

		// configure list according with mode
        if ($this->spvMode == self::ModeTuples)
        {
            $this->className = 'SPVBase';
            $this->delete = true;
            $this->table = 'spv_base';
            $this->_orderBy = 'id';
            $this->_defaultOrderBy = 'id';
            $this->identifier = 'id';
            $this->_select = 'a.id as id, group_concat(p.supplier_reference separator \', \') as supplier_reference, group_concat(id_image) as images, '.
                'group_concat(v.product_id) as product_ids, s.name as supplier';
            $this->_join = ' left join '._DB_PREFIX_.'spv_variant v on a.id=v.base_id left join '._DB_PREFIX_.'product p on p.id_product=v.product_id'.
                ' left join '._DB_PREFIX_.'image i on i.id_product=p.id_product and cover=1 left join '._DB_PREFIX_.'supplier s on s.id_supplier=a.supplier_id';
            $this->_group = 'group by a.id';

            $this->fieldsDisplay = array(
            'id'    => array(
                'title'         => $this->l('ID') ,
                'align'         => 'center',
                'width'         => 25
                ) ,
             'supplier' => array(
                'title'         => $this->l('Supplier') ,
                'align'         => 'center',
                'width'         => 25,
                'filter_key'    => 's!name'
                ),
            'supref_digits'    => array(
                'title'         => $this->l('Sup ref common digits') ,
                'align'         => 'center',
                'width'         => 50,
                ) ,
            'supplier_reference'    => array(
                'title'         => $this->l('Product tuples') ,
                'align'         => 'center',
                'width'         => 275
                ) ,
            );
        }
        else
        {
            $this->_select = 'id_image';
            $this->table = 'product';
            $this->_join = 'left join '._DB_PREFIX_.'spv_variant v on v.product_id=a.id_product left join '._DB_PREFIX_.'image i on i.id_product=a.id_product and cover=1';
            $this->_where = ' and v.id is null';

            $this->fieldsDisplay = array(
            'id_product'    => array(
                'title'         => $this->l('ID') ,
                'align'         => 'center',
                'width'         => 25
                ) ,
            'supref_digits'    => array(
                'title'         => $this->l('Sup ref common digits') ,
                'align'         => 'center',
                'width'         => 50,
                'search'        => false
                ) ,
            'supplier_reference'    => array(
                'title'         => $this->l('Product tuples') ,
                'align'         => 'center',
                'width'         => 300
                ) ,
            );
        }
        
        
        
        parent::__construct();

        // add our confirm messages
        $this->_conf[50] = $this->l('Products automatched');
        $this->_conf[51] = $this->l('Tuple updated');
        $this->_conf[52] = $this->l('Tuple inserted');
    }


    public function postProcess()
    {
        if (Tools::isSubmit('spv_automatch'))
        {
            SPVBase::autoMatchAll();
            Tools::redirectAdmin(self::$currentIndex.'&conf=50&token='.$this->token);
            return;
        }
        else
        {
            // determine if update or create tuple form was submitted
            $fieldNames = array_keys($_REQUEST);
            $baseId = 0;
            $productId = 0;
            foreach($fieldNames as $name)
            {
                if (strpos($name, 'update_tuple')===0)
                {
                    $baseId = intval(substr($name, 13));
                    break;
                }

                if (strpos($name, 'create_tuple')===0)
                {
                    $productId = substr($name, 13);
                    break;
                }
            }

            // if update or create fomr submitted
            try{
                if ($baseId)
                {
                    // validate form
                    $result = $this->validateSaveTupleForm($_REQUEST['supref_common_'.$baseId], $_REQUEST['sup_refs_'.$baseId], $baseId);
                    // save tuple
                    SPVBase::updateBaseTuple($baseId, $_REQUEST['supref_common_'.$baseId], $result['productIds'], true);

                    Tools::redirectAdmin(self::$currentIndex.'&conf=51&token='.$this->token);
                }
                elseif($productId)
                {
                    // validate form
                    $result = $this->validateSaveTupleForm($_REQUEST['supref_common_'.$productId], $_REQUEST['sup_refs_'.$productId]);
                    // save tuple
                    SPVBase::insertBaseTuple($_REQUEST['supref_common_'.$productId], $result['productIds'], $result['supplierId'], true);
                    
                    Tools::redirectAdmin(self::$currentIndex.'&conf=52&token='.$this->token);
                }
            }
            catch(SPVValidateException $e)
            {
                // show error message
                foreach($e->getErrors() as $field=>$error)
                {
                    $this->_errors[] = ' <b>'.$field.'</b>: '.$error;
                }

            }
        }
            
        parent::postProcess();
    }


    /**
     * Validates create or update tuple form
     * @param $supRefCommon content of form field
     * @param $tuple content of form field
     * @param id of record in spv_base table, in case if it is update
     * @returns array(supplierId=>, productIds=>array of product ids to save in tuple)
     * @throws SPVValidateException in case of error, it contains array('fieldName'=>error))
     */
    function validateSaveTupleForm($supRefCommon, $tuple, $baseId=0)
    {
        $fieldName1 = $this->l('Sup ref common digits');
        $fieldName2 = $this->l('Product tuple');
        $errors = array();
        // check that base id is still exists
        if ($baseId && !Db::getInstance()->getValue('select id from '._DB_PREFIX_.'spv_base where id='.$baseId))
        {
            $errors[$fieldName1] = sprintf($this->l('Base with base id %d doesn\'t exist'), $baseId);
        }
        else
        {
            // check common part
            if (empty($supRefCommon))
            {
                $errors[$fieldName1] = $this->l('Common part should not be empty, it will be used for gluing. If you want it not be glued enter letters.');
            }
            else
            {
                // check if same common part already exists
                $sql = 'select id from '._DB_PREFIX_.'spv_base where supref_digits=\''.addslashes($supRefCommon).'\'';
                if($baseId)
                {
                    $sql .= ' and id<>'.$baseId;
                }
                
                if ($foundId = Db::getInstance()->getValue($sql))
                {
                    $errors[$fieldName1] = sprintf($this->l('Tuple with same common part already exists, it id is %d'), $foundId);
                }
            }
        }

        // check tuple field
        try{
            if (empty($tuple))
            {
                throw new Exception($this->l('Product tuple should not be empty.'));
            }
            // check tuple, that all supplier references exist
            // prepare supplier references
            $supplierRefs = explode(',', $tuple);

            $supplierRefs2 = array();
            $searchString = '';
            foreach($supplierRefs as $ref)
            {
                $ref = trim($ref);
                if (!empty($ref))
                {
                    $supplierRefs2[strtolower($ref)] = $ref;
                    $searchString .= (!empty($searchString)?', ':'').'\''.addslashes($ref).'\'';
                }
            }

            if (count($supplierRefs2)==0)
            {
                throw new Exception($this->l('Product tuple should not be empty.'));
            }

            // reading products
            $sql = 'select id_product, supplier_reference, s.id_supplier, s.name as supplierName, v.base_id from '._DB_PREFIX_.'product p left join '._DB_PREFIX_.
                'supplier s on s.id_supplier=p.id_supplier left join '._DB_PREFIX_.'spv_variant v on v.product_id=p.id_product'.
                ' where supplier_reference COLLATE utf8_general_ci in('.$searchString.')';
            $products = Db::getInstance()->ExecuteS($sql);
            
            if(count($products)<2)
            {
                throw new Exception($this->l('Tuple should contain at least 2 products.'));
            }

            $supplierId = $products[0]['id_supplier'];
            $supplierName = $products[0]['supplierName'];
            $productIds = array();
            // check that supplier references exist and record corresponding
            // product ids, check that all products have same supplier
            foreach($products as $product)
            {
                // check that all products have same supplier
                if ($product['id_supplier']!=$supplierId)
                {
                    throw new Exception(sprintf($this->l('Products in tupple must have same supplier. Now they have different suppliers: product %s have supplier %s and product %s have supplier %s'),
                                                $products[0]['supplier_reference'], $supplierName, $product['supplier_reference'], $product['supplierName']));
                }

                // check that products are not included in other tupple
                if ($product['base_id'] && $product['base_id']!=$baseId)
                {
                    throw new Exception(sprintf($this->l('Product %s belongs to other tupple with id %d and can\'t be included in this one.'), $product['supplier_reference'],
                                                $product['base_id']));
                }

                // prepare to check that all supplier references exist
                $curRefSmall = strtolower($product['supplier_reference']);
                if (isset($supplierRefs2[$curRefSmall]))
                {
                    unset($supplierRefs2[$curRefSmall]);
                    $productIds []= $product['id_product'];
                }
            }

            if (count($supplierRefs2))
            {
                throw new Exception($this->l('Following product references are not found:').' '.implode(', ', $supplierRefs2));
            }
        }
        catch(Exception $e)
        {
            $errors[$fieldName2] = $e->getMessage();
        }

        if (count($errors))
        {
            throw new SPVValidateException($errors);
        }
        return array('supplierId'=>$supplierId, 'productIds'=>$productIds);
    }

    
    public function displayTop()
    {
        echo '<form action='.self::$currentIndex.'&token='.$this->token.' method="post">'.$this->l('Mode:').' <select name="spv_mode" autocomplete="off" onChange="this.form.submit()">
             <option value="'.self::ModeTuples.'" '.($this->spvMode==self::ModeTuples?'selected="1"':'').'>'.$this->l('Product tuples').'</option>
             <option value="'.self::ModeSingleProds.'" '.($this->spvMode==self::ModeSingleProds?'selected="1"':'').'>'.$this->l('Single products').'</option>
             </select>
            </form>';
        
        // return parent::displayListHeader($token);
    }


    /**
	 * Close list table and submit button
	 */
	public function displayListFooter($token = NULL)
	{
        parent::displayListFooter($token);

        echo '<form action='.self::$currentIndex;
        
        if (Tools::getIsset($this->identifier))
			echo '&'.$this->identifier.'='.(int)(Tools::getValue($this->identifier));
		echo '&token='.$this->token.' method="post">
              <input type="submit" name="spv_automatch" value="'.$this->l('Automatch all products').'" class="button">
              '.$this->l('NB: Only new matches will be added. No matches will be deleted.').'
              </form>';

        echo '<script type="text/javascript">
     
              $(function(){

                       $(\'.tupleField\').keypress( function(e) {

                       // enter pressed
                       if(e.which == 13)
                       {
                           var id = 0;
                           var fieldName = $(this).attr(\'name\');
                           // cut id
                           if (fieldName[3] == \'_\')
                           {
                               id = fieldName.substr(9);
                           }
                           else
                           {
                               id = fieldName.substr(14);
                           }

                           if ($(\'#update_tuple_\'+id).length)
                           {
                               $(\'#update_tuple_\'+id).click();
                           }
                           else
                           {
                               $(\'#create_tuple_\'+id).click();
                           }
                           return false;
                       }
                       
                  });
                  /*
                  $(\'select[name=pagination]\').change(function(){
                       $(this).parents(\'form\').submit();
                  });*/
               });
             </script>';
	}

    
    /**
	 * Get the current objects' list form the database
	 *
	 * @param integer $id_lang Language used for display
	 * @param string $orderBy ORDER BY clause
	 * @param string $_orderWay Order way (ASC, DESC)
	 * @param integer $start Offset in LIMIT clause
	 * @param integer $limit Row count in LIMIT clause
	 */
	public function getList($id_lang, $orderBy = NULL, $orderWay = NULL, $start = 0, $limit = NULL)
	{
        /*
        if ($this->spvMode == self::ModeTuples)
        {
            return parent::getList($id_lang, $orderBy, $orderWay, $start, $limit);
        }
        */
		$cookie = Context::getContext()->cookie;

		/* Manage default params values */
		if (empty($limit))
			$limit = ((!isset($cookie->{$this->table.'_pagination'})) ? $this->_pagination[1] : $limit = $cookie->{$this->table.'_pagination'});

		if (!Validate::isTableOrIdentifier($this->table))
			die (Tools::displayError('Table name is invalid:').' "'.$this->table.'"');

		if (empty($orderBy))
			$orderBy = $cookie->__get($this->table.'Orderby') ? $cookie->__get($this->table.'Orderby') : $this->_defaultOrderBy;
		if (empty($orderWay))
			$orderWay = $cookie->__get($this->table.'Orderway') ? $cookie->__get($this->table.'Orderway') : 'ASC';

		$limit = (int)(Tools::getValue('pagination', $limit));
		$cookie->{$this->table.'_pagination'} = $limit;

		/* Check params validity */
		if (!Validate::isOrderBy($orderBy) OR !Validate::isOrderWay($orderWay)
			OR !is_numeric($start) OR !is_numeric($limit)
			OR !Validate::isUnsignedId($id_lang))
			die(Tools::displayError('get list params is not valid'));

		/* Determine offset from current page */
		if ((isset($_POST['submitFilter'.$this->table]) OR
		isset($_POST['submitFilter'.$this->table.'_x']) OR
		isset($_POST['submitFilter'.$this->table.'_y'])) AND
		!empty($_POST['submitFilter'.$this->table]) AND
		is_numeric($_POST['submitFilter'.$this->table]))
			$start = (int)($_POST['submitFilter'.$this->table] - 1) * $limit;

		/* Cache */
		$this->_lang = (int)($id_lang);
		$this->_orderBy = $orderBy;
		$this->_orderWay = Tools::strtoupper($orderWay);

		/* SQL table : orders, but class name is Order */
		$sqlTable = $this->table == 'order' ? 'orders' : $this->table;

		/* Query in order to get results with all fields */
		$sql = 'SELECT distinct SQL_CALC_FOUND_ROWS
			'.($this->_tmpTableFilter ? ' * FROM (SELECT ' : '').'
			'.($this->lang ? 'b.*, ' : '').'a.*'.(isset($this->_select) ? ', '.$this->_select.' ' : '').'
			FROM `'._DB_PREFIX_.$sqlTable.'` a
			'.($this->lang ? 'LEFT JOIN `'._DB_PREFIX_.$this->table.'_lang` b ON (b.`'.$this->identifier.'` = a.`'.$this->identifier.'` AND b.`id_lang` = '.(int)($id_lang).')' : '').'
			'.(isset($this->_join) ? $this->_join.' ' : '').'
			WHERE 1 '.(isset($this->_where) ? $this->_where.' ' : '').($this->deleted ? 'AND a.`deleted` = 0 ' : '').(isset($this->_filter) ? $this->_filter : '').'
			'.(isset($this->_group) ? $this->_group.' ' : '').'
			'.((isset($this->_filterHaving) || isset($this->_having)) ? 'HAVING ' : '').(isset($this->_filterHaving) ? ltrim($this->_filterHaving, ' AND ') : '').(isset($this->_having) ? $this->_having.' ' : '').'
			ORDER BY '.(($orderBy == $this->identifier) ? 'a.' : '').'`'.pSQL($orderBy).'` '.pSQL($orderWay).
			($this->_tmpTableFilter ? ') tmpTable WHERE 1'.$this->_tmpTableFilter : '').'
			LIMIT '.(int)($start).','.(int)($limit);

		$this->_list = Db::getInstance()->ExecuteS($sql);
		$this->_listTotal = Db::getInstance()->getValue('SELECT FOUND_ROWS() AS `'._DB_PREFIX_.$this->table.'`');
	}


    public function displayListContent($token = NULL)
	{
		/* Display results in a table
		 *
		 * align  : determine value alignment
		 * prefix : displayed before value
		 * suffix : displayed after value
		 * image  : object image
		 * icon   : icon determined by values
		 * active : allow to toggle status
		 */
		$link = Context::getContext()->link;
		 
		$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

		$id_category = 1; // default categ

		$irow = 0;
		if ($this->_list AND isset($this->fieldsDisplay['position']))
		{
			$positions = array_map(create_function('$elem', 'return (int)($elem[\'position\']);'), $this->_list);
			sort($positions);
		}
		if ($this->_list)
		{
			$isCms = false;
			if (preg_match('/cms/Ui', $this->identifier))
				$isCms = true;
			$keyToGet = 'id_'.($isCms ? 'cms_' : '').'category'.(in_array($this->identifier, array('id_category', 'id_cms_category')) ? '_parent' : '');
			foreach ($this->_list AS $tr)
			{
				$id = $tr[$this->identifier];
				echo '<tr'.(array_key_exists($this->identifier,$this->identifiersDnd) ? ' id="tr_'.(($id_category = (int)(Tools::getValue('id_'.($isCms ? 'cms_' : '').'category', '1'))) ? $id_category : '').'_'.$id.'_'.$tr['position'].'"' : '').($irow++ % 2 ? ' class="alt_row"' : '').' '.((isset($tr['color']) AND $this->colorOnBackground) ? 'style="background-color: '.$tr['color'].'"' : '').'>
							<td class="center">';
				if ($this->delete AND (!isset($this->_listSkipDelete) OR !in_array($id, $this->_listSkipDelete)))
					echo '<input type="checkbox" name="'.$this->table.'Box[]" value="'.$id.'" class="noborder" />';
				echo '</td>';
				foreach ($this->fieldsDisplay AS $key => $params)
				{
					$tmp = explode('!', $key);
					$key = isset($tmp[1]) ? $tmp[1] : $tmp[0];
					echo '
					<td '.(isset($params['position']) ? ' id="td_'.(isset($id_category) AND $id_category ? $id_category : 0).'_'.$id.'"' : '').' class="'.((!isset($this->noLink) OR !$this->noLink) ? 'pointer' : '').((isset($params['position']) AND $this->_orderBy == 'position')? ' dragHandle' : ''). (isset($params['align']) ? ' '.$params['align'] : '').'" ';
					if (!isset($params['position']) AND (!isset($this->noLink) OR !$this->noLink))
						echo ' onclick="document.location = \''.self::$currentIndex.'&'.$this->identifier.'='.$id.($this->view? '&view' : '&update').$this->table.'&token='.($token!=NULL ? $token : $this->token).'\'">'.(isset($params['prefix']) ? $params['prefix'] : '');
					else
						echo '>';
					if (isset($params['active']) AND isset($tr[$key]))
						$this->_displayEnableLink($token, $id, $tr[$key], $params['active'], Tools::getValue('id_category'), Tools::getValue('id_product'));
					elseif (isset($params['activeVisu']) AND isset($tr[$key]))
						echo '<img src="../img/admin/'.($tr[$key] ? 'enabled.gif' : 'disabled.gif').'"
						alt="'.($tr[$key] ? $this->l('Enabled') : $this->l('Disabled')).'" title="'.($tr[$key] ? $this->l('Enabled') : $this->l('Disabled')).'" />';
					elseif (isset($params['position']))
					{
						if ($this->_orderBy == 'position' AND $this->_orderWay != 'DESC')
						{
							echo '<a'.(!($tr[$key] != $positions[sizeof($positions) - 1]) ? ' style="display: none;"' : '').' href="'.self::$currentIndex.
									'&'.$keyToGet.'='.(int)($id_category).'&'.$this->identifiersDnd[$this->identifier].'='.$id.'
									&way=1&position='.(int)($tr['position'] + 1).'&token='.($token!=NULL ? $token : $this->token).'">
									<img src="../img/admin/'.($this->_orderWay == 'ASC' ? 'down' : 'up').'.gif"
									alt="'.$this->l('Down').'" title="'.$this->l('Down').'" /></a>';

							echo '<a'.(!($tr[$key] != $positions[0]) ? ' style="display: none;"' : '').' href="'.self::$currentIndex.
									'&'.$keyToGet.'='.(int)($id_category).'&'.$this->identifiersDnd[$this->identifier].'='.$id.'
									&way=0&position='.(int)($tr['position'] - 1).'&token='.($token!=NULL ? $token : $this->token).'">
									<img src="../img/admin/'.($this->_orderWay == 'ASC' ? 'up' : 'down').'.gif"
									alt="'.$this->l('Up').'" title="'.$this->l('Up').'" /></a>';						}
						else
							echo (int)($tr[$key] + 1);
					}
					elseif (isset($params['image']))
					{
						// item_id is the product id in a product image context, else it is the image id.
						$item_id = isset($params['image_id']) ? $tr[$params['image_id']] : $id;
						// If it's a product image
						if (isset($tr['id_image']))
						{
							$image = new Image((int)$tr['id_image']);
							$path_to_image = _PS_IMG_DIR_.$params['image'].'/'.$image->getExistingImgPath().'.'.$this->imageType;
						}else
							$path_to_image = _PS_IMG_DIR_.$params['image'].'/'.$item_id.(isset($tr['id_image']) ? '-'.(int)($tr['id_image']) : '').'.'.$this->imageType;

						echo cacheImage($path_to_image, $this->table.'_mini_'.$item_id.'.'.$this->imageType, 45, $this->imageType);
					}
					elseif (isset($params['icon']) AND (isset($params['icon'][$tr[$key]]) OR isset($params['icon']['default'])))
						echo '<img src="../img/admin/'.(isset($params['icon'][$tr[$key]]) ? $params['icon'][$tr[$key]] : $params['icon']['default'].'" alt="'.$tr[$key]).'" title="'.$tr[$key].'" />';
					elseif (isset($params['price']))
						echo Tools::displayPrice($tr[$key], (isset($params['currency']) ? Currency::getCurrencyInstance((int)($tr['id_currency'])) : $currency), false);
					elseif (isset($params['float']))
						echo rtrim(rtrim($tr[$key], '0'), '.');
					elseif (isset($params['type']) AND $params['type'] == 'date')
						echo Tools::displayDate($tr[$key], (int)$cookie->id_lang);
					elseif (isset($params['type']) AND $params['type'] == 'datetime')
						echo Tools::displayDate($tr[$key], (int)$cookie->id_lang, true);
					else
					{
                        // print field
                        if ($this->spvMode==self::ModeSingleProds)
                        {
                            switch($key)
                            {
                            case 'supref_digits':
                                echo '<input type="text" class="tupleField" name="supref_common_'.$id.'" value="'.htmlspecialchars(Tools::getValue('supref_common_'.$id, '')).'">';
                                break;
                            case 'supplier_reference':
                                // show product photo
                                echo '<a href="'.$link->getProductLink($tr['id_product']).'" target="_blank"><img src="'.
                                    $link->getImageLink('aaa', $tr['id_image'], 'medium_default').'"/></a>';
                                echo '<input type="text" class="tupleField" name="sup_refs_'.$id.'" size="120" value="'.htmlspecialchars(Tools::getValue('sup_refs_'.$id, $tr[$key])).'">';
                                echo '<input type="submit" class="button" name="create_tuple_'.$id.'" id="create_tuple_'.$id.'" value="'.$this->l('Create tuple').'">';
                                break;
                            default:
                                echo $tr[$key];
                            }
                        }
                        else
                        {
                            switch($key)
                            {
                            case 'supref_digits':
                                echo '<input type="text" class="tupleField" name="supref_common_'.$id.'" size="15" value="'.
                                    htmlspecialchars(isset($_REQUEST['submitFilter'])?$tr[$key]:Tools::getValue('supref_common_'.$id, $tr[$key])).'">';
                                break;
                            case 'supplier_reference':
                                // show product photos
                                $productIds = explode(',', $tr['product_ids']);
                                $imagesIds = explode(',', $tr['images']);
                                
                                foreach($productIds as $i=>$productId)
                                {
                                    echo '<a href="'.$link->getProductLink($productId).'" target="_blank"><img src="'.
                                        $link->getImageLink('aaa', $imagesIds[$i], 'medium_default').'"/></a>';
                                }

                                if (strlen($tr[$key])<120)
                                {
                                    echo '<input type="text" class="tupleField" name="sup_refs_'.$id.'" size="120" value="'.
                                        htmlspecialchars(isset($_REQUEST['submitFilter'])?$tr[$key]:Tools::getValue('sup_refs_'.$id, $tr[$key])).'">';
                                }
                                else
                                {
                                    echo '<textarea name="sup_refs_'.$id.'" cols="120" rows="2">'.
                                        htmlspecialchars(Tools::getValue('sup_refs_'.$id, $tr[$key])).'</textarea>';
                                }
                                echo '<input type="submit" class="button" id="update_tuple_'.$id.'" name="update_tuple_'.$id.'" value="'.$this->l('Update').'">';
                                break;
                            default:
                                echo $tr[$key];
                            }
                        }
					}
						

					echo (isset($params['suffix']) ? $params['suffix'] : '').
					'</td>';
				}

				if ($this->edit OR $this->delete OR ($this->view AND $this->view !== 'noActionColumn'))
				{
					echo '<td class="center" style="white-space: nowrap;">';
					if ($this->view)
						$this->_displayViewLink($token, $id);
					if ($this->edit)
						$this->_displayEditLink($token, $id);
					if ($this->delete AND (!isset($this->_listSkipDelete) OR !in_array($id, $this->_listSkipDelete)))
						$this->_displayDeleteLink($token, $id);
					if ($this->duplicate)
						$this->_displayDuplicate($token, $id);
					echo '</td>';
				}
				echo '</tr>';
			}
		}
	}
}


class SPVValidateException extends Exception
{
    var $errors;
    
    public function __construct($errors)
    {
        $this->errors = $errors;
    }

    function &getErrors()
    {
        return $this->errors;
    }
}