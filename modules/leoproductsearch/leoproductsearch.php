<?php
/**
 *  Leo Theme for Prestashop 1.6.x
 *
 * @author    http://www.leotheme.com
 * @copyright Copyright (C) October 2013 LeoThemes.com <@emai:leotheme@gmail.com>
 *               <info@leotheme.com>.All rights reserved.
 * @license   GNU General Public License version 2
 */

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

class LeoProductSearch extends Module
{

    public function __construct()
    {
        $this->name = 'leoproductsearch';
        $this->tab = 'search_filter';
        $this->version = '2.0.0';
        $this->author = 'LeoTheme';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Quick product search by category block');
        $this->description = $this->l('Adds a quick product search field to your website.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        if (!parent::install() || !$this->registerHook('top') || !$this->registerHook('header') || !$this->registerHook('displayMobileTopSiteMap')) {
            return false;
        }
        $this->_installDataSample();
        return true;
    }

    private function _installDataSample()
    {
        if (!file_exists(_PS_MODULE_DIR_.'leotempcp/libs/DataSample.php')) {
            return false;
        }
        require_once( _PS_MODULE_DIR_.'leotempcp/libs/DataSample.php' );

        $sample = new Datasample(1);
        return $sample->processImport($this->name);
    }

    public function hookdisplayMobileTopSiteMap($params)
    {
        $this->smarty->assign(array('hook_mobile' => true, 'instantsearch' => false));
        $params['hook_mobile'] = true;
        return $this->hookTop($params);
    }

    public function hookHeader($params)
    {
        $this->context->controller->addCSS(($this->_path).'assets/leosearch.css', 'all');
		$this->context->controller->addCSS(_THEME_CSS_DIR_.'product_list.css');
        if (Configuration::get('PS_SEARCH_AJAX')) {
            $this->context->controller->addJS(($this->_path).'assets/jquery.autocomplete_productsearch.js');
            $this->context->controller->addCSS(($this->_path).'assets/jquery.autocomplete_productsearch.css');
            Media::addJsDef(array('leo_search_url' => $this->context->link->getModuleLink('leoproductsearch', 'productsearch', array(), Tools::usingSecureMode())));
            $this->context->controller->addJS(($this->_path).'assets/leosearch.js');
        }
    }

    public function hookLeftColumn($params)
    {
        if (Tools::getValue('search_query') || !$this->isCached('leosearch.tpl', $this->getCacheId())) {

            $this->calculHookCommon($params);
            $this->smarty->assign(array(
                'blocksearch_type' => 'block',
                'search_query' => (string)Tools::getValue('search_query'),
                'selectedCate' => (string)Tools::getValue('cate'),
                'cates' => $this->getCategories($this->context->language->id),
            ));
        }
        Media::addJsDef(array('blocksearch_type' => 'block'));
//		$search_query = (string)Tools::getValue('search_query');
        return $this->display(__FILE__, 'leosearch.tpl', Tools::getValue('search_query') ? null : $this->getCacheId());
    }

    public function hookTop($params)
    {
        if (Tools::getValue('search_query') || !$this->isCached('leosearch_top.tpl', $this->getCacheId())) {
            $this->calculHookCommon($params);
            $this->smarty->assign(array(
                'blocksearch_type' => 'top',
                'search_query' => (string)Tools::getValue('search_query'),
                'selectedCate' => (string)Tools::getValue('cate'),
                'cates' => $this->getCategories($this->context->language->id),
            ));
        }
        Media::addJsDef(array('blocksearch_type' => 'top'));
//		$search_query = (string)Tools::getValue('search_query');
        return $this->display(__FILE__, 'leosearch_top.tpl', Tools::getValue('search_query') ? null : $this->getCacheId());
    }

    public function hookDisplayNav($params)
    {
        return $this->hookTop($params);
    }

    private function calculHookCommon($params)
    {
        $this->smarty->assign(array(
            'ENT_QUOTES' => ENT_QUOTES,
            'search_ssl' => Tools::usingSecureMode(),
            'ajaxsearch' => Configuration::get('PS_SEARCH_AJAX'),
            'instantsearch' => Configuration::get('PS_INSTANT_SEARCH'),
            'self' => dirname(__FILE__),
        ));

        unset($params);
        return true;
    }

    /**
     * copy from function hookFooter of class "modules/blockcategories/blockcategories"
     */
    public function getCategories($id_lang = false, $active = true)
    {
        $maxdepth = Configuration::get('BLOCK_CATEG_MAX_DEPTH');
        // Get all groups for this customer and concatenate them as a string: "1,2,3..."
        $groups = implode(', ', Customer::getGroupsStatic((int)$this->context->customer->id));
        $sql = '
				SELECT DISTINCT c.id_parent, c.id_category, cl.name, cl.description, cl.link_rewrite
				FROM `'._DB_PREFIX_.'category` c
				'.Shop::addSqlAssociation('category', 'c').'
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.`id_lang` = '.(int)$this->context->language->id.Shop::addSqlRestrictionOnLang('cl').')
				LEFT JOIN `'._DB_PREFIX_.'category_group` cg ON (cg.`id_category` = c.`id_category`)
				WHERE (c.`active` = 1 OR c.`id_category` = 1)
				'.((int)($maxdepth) != 0 ? ' AND `level_depth` <= '.(int)($maxdepth) : '').'
				AND cg.`id_group` IN ('.pSQL($groups).')
				ORDER BY `level_depth` ASC, '.(Configuration::get('BLOCK_CATEG_SORT') ? 'cl.`name`' : 'category_shop.`position`').' '.(Configuration::get('BLOCK_CATEG_SORT_WAY') ? 'DESC' : 'ASC');

        unset($id_lang);
        unset($active);
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }
}
