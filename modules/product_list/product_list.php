<?php
/**
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2017 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Product_list extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'product_list';
        $this->tab = 'merchandizing';
        $this->version = '1.0.0';
        $this->author = 'wheelronix';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Product list');
        $this->description = $this->l('Shows list of products with photo and stock');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }
    

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        return parent::install() && $this->installModuleTab('AdminProductList', 'Products list', 'AdminCatalog') && 
                $this->installModuleTab('AdminDiffPayments', 'Invoices', 'AdminOrders');
    }

    
    /*
     * Creates a "subtab" in "Catalog" tab.
     *
     * @access private
     * @param string $class    - Class name, like "AdminCatalog"
     * @param string $name     - Tab title
     *
     * @return boolean
     */
    private function installModuleTab($class, $name, $tabName)
    {
        $sql = ' SELECT `id_tab` FROM `' . _DB_PREFIX_ . 'tab` WHERE `class_name` = "'.addslashes($tabName).'"';

        $tabParent = (int) (Db::getInstance()->getValue($sql));

        if (!is_array($name))
            $name = self::getMultilangField($name);
        /*
          if (self::fileExistsInModulesDir('logo.gif') && is_writeable(_PS_IMG_DIR_ . 't/'))
          $this->copyLogo($class);
         */

        $tab = new Tab();
        $tab->name = $name;
        $tab->class_name = $class;
        $tab->module = $this->name;
        $tab->id_parent = $tabParent;

        return $tab->save();
    }

    /*
     * Turns a string into an array with language IDs as keys. This array can
     * be used to create multilingual fields for prestashop
     *
     * @access private
     * @scope static
     * @param mixed $field    - A field to turn into multilingual
     *
     * @return array
     */
    private static function getMultilangField($field)
    {
        $languages = Language::getLanguages();
        $res = array();

        foreach ($languages as $lang)
            $res[$lang['id_lang']] = $field;

        return $res;
    }

    public function getModuleDir()
    {
        return $this->local_path;
    }
    
    public function uninstall()
    {
        $sql = '
		SELECT `id_tab` FROM `' . _DB_PREFIX_ . 'tab` WHERE `module` = "' . pSQL($this->name) . '"';

        $result = Db::getInstance()->ExecuteS($sql);

        if ($result && sizeof($result))
        {
            foreach ($result as $tabData)
            {
                $tab = new Tab($tabData['id_tab']);

                if (Validate::isLoadedObject($tab))
                    $tab->delete();
            }
        }

        return parent::uninstall();
    }
}
