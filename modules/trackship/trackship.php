<?php
/**
 * 
 */
if (!defined('_PS_VERSION_'))
	exit;

class Trackship extends Module
{
	protected $config_form = false;

	public function __construct()
	{
		$this->name = 'trackship';
		$this->tab = 'shipping_logistics';
		$this->version = '0.1.0';
		$this->author = 'NSWEB';
		$this->need_instance = 0;

		/**
		 * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
		 */
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('Tracking shipping');
		
		$this->description = $this->l('Tracking of order shipping by multiple tracking numbers. ');

		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => '1.6.99.99');
	}
	
	public function getContent()
	{
	    return $this->context->link->getModuleLink($this->name, 'numbers', array(
		    'action' => 'add',
		    'tkn' => Configuration::get('TRACKSHIP_TOKEN')
		));
	
	}

	/**
	 * Don't forget to create update methods if needed:
	 * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
	 */
	public function install()
	{
	    $tableCreateQuery =
	    'CREATE TABLE `'._DB_PREFIX_.'order_tracking_number` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `id_order` int(11) NOT NULL,
            `date_added` DATETIME NOT NULL,
            `code` VARCHAR(64),
            PRIMARY KEY  (`id`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';
	    
	    if (Db::getInstance()->execute($tableCreateQuery) == false){
	        $this->_errors[] = Db::getInstance()->getMsgError();
	        return false;
	    }
	    
	    Configuration::updateValue('TRACKSHIP_TOKEN', Tools::passwdGen(8));

		return parent::install() &&
			$this->registerHook('displayAdminOrderLeft') &&
			$this->registerHook('actionAdminControllerSetMedia');
	}

	public function uninstall()
	{
	    Db::getInstance()->query('DROP TABLE `'._DB_PREFIX_.'order_tracking_number`');
		return parent::uninstall();
	}


	public function hookActionAdminControllerSetMedia()
	{
		//$this->context->controller->addJS($this->_path.'js/back.js');
		//$this->context->controller->addCSS($this->_path.'css/back.css');
	}

	public function hookDisplayAdminOrderLeft($params)
	{
	    /**
	     * 
	     * @var DbQueryCore $query
	     */
	    $query = new DbQuery();
	    $query->select('*');
	    $query->from('order_tracking_number', 'otn');
	    $query->where('id_order = '. (int)$params['id_order']);
	    $order_tracking_numbers = Db::getInstance()->executeS($query);

	    $this->context->smarty->assign(array(
	        'order_tracking_numbers' => $order_tracking_numbers,
	        'trackship_token' => Configuration::get('TRACKSHIP_TOKEN')
	    ));
	    
	    return $this->context->smarty->fetch($this->local_path.'views/templates/admin/numbers.tpl');
	}
}
