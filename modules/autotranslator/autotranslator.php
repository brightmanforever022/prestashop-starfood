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

class AutoTranslator extends Module
{
    public $errors = array();

    public function __construct()
    {
        if (!defined('_PS_VERSION_')) {
            exit;
        }
        $this->name = 'autotranslator';
        $this->tab = 'administration';
        $this->version = '2.7.1';
        $this->author = 'Amazzing';
        $this->need_instance = 0;
        $this->module_key = 'f08869de6029835933882a99d65d03d4';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Automatic translations');
        $this->description = $this->l('Automatic translations for products, categories, attributes etc...');
        $this->db = Db::getInstance();
        $this->shop_ids = Shop::getContextListShopID();
        $this->is_17 = Tools::substr(_PS_VERSION_, 0, 3) === '1.7';
        $this->char_count = 0;
    }

    public function install()
    {
        if (!parent::install()
            || !$this->prepareDataBase()
            || !$this->registerHook('displayBackOfficeHeader')
            || !Configuration::updateValue('Y_API_KEY', '')) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() || !$this->clearDataBase() || !Configuration::deleteByName('Y_API_KEY')) {
            return false;
        }
        return true;
    }

    public function prepareDataBase()
    {
        $sql = array(
            'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'at_stats (
            id int(10) unsigned NOT NULL AUTO_INCREMENT,
            day tinyint(2) NOT NULL,
            month tinyint(2) NOT NULL,
            year smallint(4) NOT NULL,
            characters int(10) unsigned NOT NULL,
            PRIMARY KEY (id), KEY day (day), KEY month (month), KEY year (year)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8',
        );
        return $this->runSql($sql);
    }

    public function clearDataBase()
    {
        $sql = array(
            'DROP TABLE IF EXISTS '._DB_PREFIX_.'at_stats',
        );
        return $this->runSql($sql);
    }

    public function runSql($sql)
    {
        foreach ($sql as $s) {
            if (!$this->db->Execute($s)) {
                return false;
            }
        }
        return true;
    }

    public function hookDisplayBackOfficeHeader()
    {
        if ($this->is_17 && Tools::getValue('controller') == 'AdminTranslations' &&
            Tools::getValue('type') == 'themes') {
            // this can be used in further versions
            // $this->context->controller->addJquery();
            // $this->context->controller->addJS($this->_path.'views/js/theme-autotranslate.js?v='.$this->version);
        } elseif (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJquery();
            $this->context->controller->js_files[] = $this->_path.'views/js/back.js?v='.$this->version;
            $this->context->controller->css_files[$this->_path.'views/css/back.css?v='.$this->version] = 'all';
        }
    }

    public function hookDisplayBackOfficeTop()
    {
        if ($this->is_17 && Tools::getValue('controller') == 'AdminTranslations' &&
            Tools::getValue('type') == 'themes') {
            // this can be used in further versions
            // $ajax_action_url = 'index.php?controller=AdminModules&configure='.$this->name.
            // '&token='.Tools::getAdminTokenLite('AdminModules').'&ajax=1';
            // $this->context->smarty->assign(array(
            //     'ajax_action_url' => $ajax_action_url,
            //     'logo_path_png' => $this->_path.'/logo.png',
            //     'logo_path_gif' => $this->_path.'/logo.gif',
            // ));
            // return $this->display(__FILE__, 'views/templates/admin/theme-autotranslate-form.tpl');
        }
    }

    public function escapeApostrophe($string)
    {
        return str_replace("'", "\'", $string);
    }

    public function saveNewKey()
    {
        $saved = '';
        $new_key = Tools::getValue('yandex_api_key');
        if ($this->yandexTranslate('Hello', 'en', 'ru', $new_key)) {
            Configuration::updateValue('Y_API_KEY', $new_key);
            $saved = $new_key;
        }
        return $saved;
    }

    public function getStatsData()
    {
        $date = explode('-', gmdate('j-n-Y'));
        $chars_day = (int)$this->db->getValue('
            SELECT characters FROM '._DB_PREFIX_.'at_stats
            WHERE day = '.(int)$date[0].' AND month = '.(int)$date[1].' AND year = '.(int)$date[2].'
        ');
        $chars_month = (int)$this->db->getValue('
            SELECT SUM(characters) FROM '._DB_PREFIX_.'at_stats
            WHERE month = '.(int)$date[1].' AND year = '.(int)$date[2].'
        ');
        return array('day' => $chars_day, 'month' => $chars_month);
    }

    public function updateStatsData($value)
    {
        $date = explode('-', gmdate('j-n-Y'));
        $row = array('id' => '', 'day' => $date[0], 'month' => $date[1], 'year' => $date[2], 'characters' => $value);
        $today_row = $this->db->getRow('
            SELECT * FROM '._DB_PREFIX_.'at_stats
            WHERE day = '.(int)$date[0].' AND month = '.(int)$date[1].' AND year = '.(int)$date[2].'
        ');
        if (is_array($today_row)) {
            $row['id'] = $today_row['id'];
            $row['characters'] += $today_row['characters'];
        }
        return $this->db->execute('
            REPLACE INTO '._DB_PREFIX_.'at_stats VALUES
            ('.implode(', ', array_map('intval', $row)).')
        ');
    }

    public function getContent()
    {
        $this->key = Configuration::get('Y_API_KEY');
        if ($ajax_action = Tools::getValue('ajax_action')) {
            if ($ajax_action == 'autoTranslate') {
                $this->autoTranslate();
            } elseif ($ajax_action == 'saveOwerwriteOption') {
                $value = Tools::getValue('overwrite_existing');
                $this->saveOverwriteOption($value);
            } elseif ($ajax_action == 'callResourseList') {
                $this->ajaxCallResourseList();
            }
        }
        if (Tools::isSubmit('updateAPIkey')) {
            $this->key = $this->saveNewKey();
        }
        $key_status = '';
        if (!$this->key && !$this->errors) {
            $key_status = $this->displayWarning($this->l('Please, enter key'));
        } elseif (!$this->key) {
            $key_status = $this->displayError(implode('<br>', $this->errors));
        }

        $this->context->smarty->assign(array(
            'key' => $this->key,
            'stats_data' => $this->getStatsData(),
            'version' => $this->version,
            'info_links' => array(
                'changelog' => $this->_path.'Readme.md?v='.$this->version,
                'documentation' => $this->_path.'readme_en.pdf?v='.$this->version,
                'contact' => 'https://addons.prestashop.com/en/contact-us?id_product=19662',
                'modules' => 'https://addons.prestashop.com/en/2_community-developer?contributor=64815',
            ),
        ));
        $key_html = $key_status;
        $key_html .= $this->display(__FILE__, 'views/templates/admin/key-form.tpl');
        if (!$this->key) {
            return $key_html;
        }

        $this->context->smarty->assign(array(
            'content_types' => $this->getContentTypes(),
            'special_params' => $this->getSpecialParams(),
            'sorting_options' => $this->getSortingOptions(),
            'overwrite_existing' => Configuration::get('AT_OVERWRITE_EXISTING'),
        ));
        $this->prepareListVariables();
        $main_html = $this->display(__FILE__, 'views/templates/admin/configure.tpl');
        return $key_html.$main_html;
    }

    public function getSortingOptions()
    {
        $sorting_options = array(
            'id' => array(
                'name' => 'ID',
            ),
            'name' => array(
                'name' => $this->l('Name'),
            ),
            'date_add' => array(
                'name' => $this->l('Date added'),
                'class' => 'product category cms_category'
            ),
            'reference' => array(
                'name' => $this->l('Reference'),
                'class' => 'product'
            ),
            'active' => array(
                'name' => $this->l('Active status'),
                'class' => 'product category cms cms_category manufacturer supplier module'
            ),
        );
        return $sorting_options;
    }

    public function getSpecialParams()
    {
        $params = array(
            'product' => $this->getProductFilters(),
            'module' => array('location' => $this->getThemesOptions()),
        );
        return $params;
    }

    public function getProductFilters()
    {
        $filters = array(
            'id_category' => $this->getCategoryOptions(),
            'id_manufacturer' => $this->getManufacturerOptions(),
        );
        return $filters;
    }

    public function getContentTypes()
    {
        $content_types = array(
            'product' => $this->l('Products'),
            'attribute_group' => $this->l('Attribute groups'),
            'attribute' => $this->l('Attributes'),
            'feature' => $this->l('Features'),
            'feature_value' => $this->l('Feature values'),
            'category' => $this->l('Categories'),
            'cms' => $this->l('CMS Pages'),
            'cms_category' => $this->l('CMS Categories'),
            'manufacturer' => $this->l('Manufacturers'),
            'supplier' => $this->l('Suppliers'),
            'module' => $this->l('Installed modules'),
            'theme' => $this->l('Themes'),
            'meta' => $this->l('SEO and URLs'),
        );
        if (Module::isInstalled('ph_simpleblog')) {
            $content_types['simpleblog_post'] = $this->l('Simple blog posts');
            $content_types['simpleblog_category'] = $this->l('Simple blog categories');
        }
        if (Module::isInstalled('amazzingblog')) {
            $content_types['a_blog_post'] = $this->l('Amazzing blog posts');
            $content_types['a_blog_category'] = $this->l('Amazzing blog categories');
        }
        if ($this->is_17) {
            // temporarily not available in 1.7
            unset($content_types['theme']);
        }
        return $content_types;
    }

    public function prepareListVariables()
    {
        $languages = array();
        foreach (Language::getLanguages(false) as $lang) {
            $languages[$lang['iso_code']] = $lang['name'];
        }
        $languages['all'] = $this->l('All');
        $current_ct = Tools::getValue('at_ct', 'product');
        $current_lang_iso = Tools::getValue('at_lang', $this->context->language->iso_code);
        $identifier = 'id_'.$current_ct;
        if (in_array($current_ct, array('theme', 'module'))) {
            $current_lang_iso = 'en';
            $identifier = 'name';
        } elseif (Tools::substr($current_ct, 0, 7) === 'a_blog_') {
            $identifier = 'id_'.str_replace('a_blog_', '', $current_ct);
        }
        $fields_list = array(
            'name' => $this->l('Name'),
        );
        $order = array(
            'by' => Tools::getValue('order_by', 'id'),
            'way' => Tools::getValue('order_way', 'DESC'),
        );
        $pagination = array(
            'p' => Tools::getValue('p', 1),
            'npp' => Tools::getValue('npp', 20),
        );
        $this->context->smarty->assign(array(
            'identifier' => $identifier,
            'current_ct' => $current_ct,
            'languages' => $languages,
            'current_lang_iso' => $current_lang_iso,
            'fields_list' => $fields_list,
            'items' => $this->getItems($current_lang_iso, $current_ct, $pagination, $order),
            'total' => $this->getItems($current_lang_iso, $current_ct, $pagination, $order, true),
            'order' => $order,
            'pagination' => $pagination,
            'bulk_checkbox_actions' => $this->getBulkCheckboxActions(),
        ));
    }

    public function getBulkCheckboxActions()
    {
        $actions = array(
            'icon-check-sign' => $this->l('Check all'),
            'icon-check-empty' => $this->l('Uncheck all'),
            'icon-random' => $this->l('Invert selection'),
        );
        return $actions;
    }

    public function getThemesList()
    {
        $themes = array();
        if ($this->is_17) {
            $suffix = 'config/theme.yml';
            $theme_directories = glob(_PS_ALL_THEMES_DIR_.'*/'.$suffix);
            foreach ($theme_directories as $path) {
                $themes[] = basename(Tools::substr($path, 0, - Tools::strlen($suffix)));
            }
        } else {
            if (method_exists('Theme', 'getInstalledThemeDirectories')) {
                $themes = Theme::getInstalledThemeDirectories();
            } elseif (method_exists('Theme', 'getAvailable')) {
                $themes = Theme::getAvailable(false);
            }
        }
        if ($themes) {
            $themes = array_combine($themes, $themes);
        }
        return $themes;
    }

    public function getCategoryOptions()
    {
        $categories = $this->db->executeS('
            SELECT c.id_category, c.id_parent, cl.name
            FROM '._DB_PREFIX_.'category c
            '.Shop::addSqlAssociation('category', 'c').'
            LEFT JOIN '._DB_PREFIX_.'category_lang cl
                ON c.id_category = cl.id_category
                AND cl.id_shop = '.(int)$this->context->shop->id.'
                AND cl.id_lang = '.(int)$this->context->language->id.'
        ');
        $structured_categories = array();
        foreach ($categories as $c) {
            $structured_categories[$c['id_parent']][$c['id_category']] = $c;
        }
        $max_digits = Tools::strlen($this->db->getValue('SELECT MAX(id_category) FROM '._DB_PREFIX_.'category'));
        $id_root = $this->context->shop->getCategory();
        $root_parent = $this->db->getValue('
            SELECT id_parent FROM '._DB_PREFIX_.'category WHERE id_category = '.(int)$id_root.'
        ');
        $options = array('0' => $this->l('Select category'));
        $options += $this->getCatLevelOptions($root_parent, $structured_categories, $max_digits);
        return $options;
    }

    public function getCatLevelOptions($id_parent, $structured_categories, $max_id_digits, $prefix = '')
    {
        $options = array();
        if (isset($structured_categories[$id_parent])) {
            $categories = $structured_categories[$id_parent];
            $children_prefix = $prefix.'-';
            foreach ($categories as $c) {
                $id = $c['id_category'];
                $name = $c['name'];
                $options[$id] = $this->formatID($id, $max_id_digits).' '.$prefix.$name;
                $options += $this->getCatLevelOptions($id, $structured_categories, $max_id_digits, $children_prefix);
            }
        }
        return $options;
    }

    public function formatID($id, $max_id_digits)
    {
        $id = str_pad($id, $max_id_digits, '0', STR_PAD_LEFT);
        return $id;
    }

    public function getManufacturerOptions()
    {
        $options = array(0 => $this->l('Select manufacturer'));
        foreach ($this->db->executeS('SELECT * FROM '._DB_PREFIX_.'manufacturer') as $row) {
            $options[$row['id_manufacturer']] = $row['name'];
        }
        return $options;
    }

    public function getThemesOptions()
    {
        $options = array('core' => $this->l('Core (no theme)'));
        $options = array_merge($options, $this->getThemesList());
        return $options;
    }

    public function getItems($lang_iso, $content_type, $pagination, $order, $return_count = false)
    {
        if ($this->is_17 && $content_type == 'theme') {
            $items = array();
            foreach ($this->getThemesList() as $t) {
                $items[] = array(
                    'directory' => $t,
                    'name' => $t,
                );
            }
            return $return_count ? count($items) : $items;
        }

        $id_lang = Language::getIdByIso($lang_iso);
        $imploded_shop_ids = implode(', ', $this->shop_ids);
        $identifier = 'id_'.$content_type;
        $name_field = 'name';
        if ($content_type == 'cms') {
            $name_field = 'meta_title';
        } elseif ($content_type == 'theme') {
            $name_field = 'directory';
        } elseif ($content_type == 'feature_value') {
            $name_field = 'value';
        } elseif ($content_type == 'simpleblog_post') {
            $name_field = 'title';
        } elseif ($content_type == 'meta') {
            $name_field = 'title';
        } elseif (Tools::substr($content_type, 0, 7) === 'a_blog_') {
            $name_field = 'title';
            $identifier = 'id_'.str_replace('a_blog_', '', $content_type);
        }
        if ($order['by'] == 'id') {
            $order['by'] = $identifier;
        } elseif ($order['by'] == 'name') {
            $order['by'] = $name_field;
        } else {
            $select_order_by_column = 1;
        }
        if ($this->tableExists(_DB_PREFIX_.$content_type.'_shop') &&
            $this->columnExists(_DB_PREFIX_.$content_type.'_shop', $order['by'])) {
            $order['by'] = 'shop.'.$order['by'];
        }
        $query = new DbQuery();
        if ($return_count) {
            $query->select('COUNT(DISTINCT main.'.$identifier.')');
        } else {
            $p = $pagination['p'];
            $npp = $pagination['npp'];
            $offset = ($p - 1) * $npp;
            $query->select('main.'.$identifier.', '.$name_field.' AS name');
            if ($content_type == 'meta') {
                $query->select('main.page AS identifier_extension');
            }
            if ($content_type == 'feature_value') {
                $query->select('main.custom AS is_custom_value');
            }
            if (isset($select_order_by_column)) {
                $query->select($order['by']);
            }
            $query->orderBy(pSQL($order['by']).' '.pSQL($order['way']));
            $query->limit((int)$npp, (int)$offset);
            $query->groupBy('main.'.$identifier);
        }
        $query->from($content_type, 'main');
        if ($this->tableExists(_DB_PREFIX_.$content_type.'_shop')) {
            $query->innerJoin(
                $content_type.'_shop',
                'shop',
                'shop.'.$identifier.' = main.'.$identifier.'
                AND shop.id_shop IN ('.pSQL($imploded_shop_ids).')'
            );
        }
        if (!in_array($content_type, array('module', 'theme'))) {
            $query->innerJoin(
                $content_type.'_lang',
                'lang',
                'lang.'.$identifier.' = main.'.$identifier.'
                AND lang.id_lang = '.(int)$id_lang.'
                '.($this->columnExists(_DB_PREFIX_.$content_type.'_lang', 'id_shop') ?
                'AND lang.id_shop IN ('.pSQL($imploded_shop_ids).')' : '')
            );

            if ($content_type == 'product') {
                foreach (array_keys($this->getProductFilters()) as $key) {
                    if ($value = Tools::getValue($key)) {
                        if ($key == 'id_category') {
                            $query->leftJoin('category_product', 'cp', 'cp.id_product = main.id_product');
                            $query->where('cp.id_category = \''.pSQL($value).'\'');
                        } else {
                            $query->where('main.'.pSQL($key).' = \''.pSQL($value).'\'');
                        }
                    }
                }
            }
        }
        return $return_count ? $this->db->getValue($query) : $this->db->executeS($query);
    }

    public function columnExists($table_name, $column_name, $prefix_included = true)
    {
        $table_name = $prefix_included ? $table_name : _DB_PREFIX_.$table_name;
        return (bool)$this->db->executeS('
            SHOW COLUMNS FROM '.pSQL($table_name).' LIKE \''.pSQL($column_name).'\'
        ');
    }

    public function tableExists($table_name, $prefix_included = true)
    {
        $table_name = $prefix_included ? $table_name : _DB_PREFIX_.$table_name;
        return (bool)$this->db->executeS('SHOW TABLES LIKE \''.pSQL($table_name).'\'');
    }

    public function saveOverwriteOption($value)
    {
        return Configuration::updateGlobalValue('AT_OVERWRITE_EXISTING', (int)$value);
    }

    public function ajaxCallResourseList()
    {
        $this->prepareListVariables();
        $ret = array(
            'list_html' => utf8_encode($this->display(__FILE__, 'views/templates/admin/list.tpl')),
        );
        exit(Tools::jsonEncode($ret));
    }

    public function autoTranslate()
    {
        $identifier = Tools::getValue('identifier');
        $content_type = Tools::getValue('content_type');
        $from = Tools::getValue('from');
        $to = Tools::getValue('to');
        $to_id = Language::getIdByIso($to);
        $from_id = Language::getIdByIso($from);

        $time = microtime(true);
        $response = $this->l('No actions performed');
        $overwrite_existing = Tools::getValue('overwrite_existing');

        switch ($content_type) {
            case 'product':
            case 'attribute_group':
            case 'attribute':
            case 'feature':
            case 'feature_value':
            case 'category':
            case 'cms':
            case 'cms_category':
            case 'manufacturer':
            case 'supplier':
            case 'simpleblog_post':
            case 'simpleblog_category':
            case 'meta':
                $class_name = Tools::ucfirst($content_type);
                $special_names = array(
                    'attribute_group' => 'AttributeGroup',
                    'feature_value' => 'FeatureValue',
                    'cms' => 'CMS',
                    'cms_category' => 'CMSCategory',
                    'simpleblog_post' => 'SimpleBlogPost',
                    'simpleblog_category' => 'SimpleBlogCategory',
                );
                if (!empty($special_names[$content_type])) {
                    $class_name = $special_names[$content_type];
                }
                $obj = $this->createObject($class_name, $identifier);
                $translatable_fields = $this->getTranslatableFields($obj);
                $to_translate = array();

                foreach (array_keys($translatable_fields) as $field) {
                    // link_rewrite is generated later basing on translated name
                    if (!trim($obj->{$field}[$from_id]) || $field == 'link_rewrite' || $field == 'url_rewrite') {
                        continue;
                    }
                    if ($class_name == 'SimpleBlogPost' && ($field == 'video_code' || $field == 'external_url')) {
                        continue;
                    }
                    if ($overwrite_existing || !$obj->{$field}[$to_id]) {
                        $to_translate[$field] = $obj->{$field}[$from_id];
                    }
                }
                if ($to_translate) {
                    $translated = $this->yandexTranslate($to_translate, $from, $to);
                    foreach ($translated as $k => $t) {
                        $obj->{$k}[$to_id] = $t;
                    }

                    $rewrite_source = '';
                    if (isset($translated['name'])) {
                        $rewrite_source = $translated['name'];
                    } elseif (isset($translated['title'])) {
                        $rewrite_source = $translated['title'];
                    } elseif (isset($translated['meta_title'])) {
                        $rewrite_source = $translated['meta_title'];
                    }
                    if ($rewrite_source) {
                        if (isset($obj->link_rewrite)) {
                            $obj->link_rewrite[$to_id] = Tools::str2url($rewrite_source);
                        } elseif (isset($obj->url_rewrite)) {
                            $obj->url_rewrite[$to_id] = Tools::str2url($rewrite_source);
                        }
                    }

                    // fix for some complex multishop scenarios
                    if ($content_type == 'product' && empty($obj->price)) {
                        $obj->price = 0;
                    }

                    // truncate fields if required
                    foreach ($translatable_fields as $field_name => $truncate) {
                        if ($truncate) {
                            $value = $obj->{$field_name}[$to_id];
                            // $value =  mb_substr($value, 0, $truncate);
                            $truncate_options = array('ellipsis' => '', 'exact' => false);
                            $allow_html = array('description', 'description_short', 'content', 'short_content');
                            if (!in_array($field_name, $allow_html)) {
                                $truncate_options['html'] = false;
                            }
                            $value = Tools::truncateString($value, $truncate, $truncate_options);
                            $obj->{$field_name}[$to_id] = $value;
                        }
                    }
                    $this->saveObject($obj);
                }

                if ($content_type == 'product') {
                    // save tags
                    $all_tags = Tag::getProductTags($obj->id);
                    if ($all_tags) {
                        if ($overwrite_existing || !$all_tags[$to_id]) {
                            $all_tags[$to_id] = $this->yandexTranslate($all_tags[$from_id], $from, $to);
                            Tag::deleteTagsForProduct($obj->id);
                            foreach ($all_tags as $id_lang => $tags) {
                                Tag::addTags($id_lang, $obj->id, $tags);
                            }
                        }
                    }

                    // save legends
                    $already_translated = array();
                    foreach ($obj->getImages($from_id) as $img) {
                        $image = new Image($img['id_image']);

                        $definition = ObjectModel::getDefinition($image);
                        $truncate_legend = $definition['fields']['legend']['size'];
                        $truncate_options = array('ellipsis' => '', 'exact' => false);

                        // fix for some complex multishop scenarios
                        if (empty($image->id_product)) {
                            $image->id_product = $obj->id;
                        }
                        if ($image->legend[$from_id] && ($overwrite_existing || !$image->legend[$to_id])) {
                            // avoid duplicate translations
                            $original = $image->legend[$from_id];
                            if (!isset($already_translated[$original])) {
                                $legend = $this->yandexTranslate($original, $from, $to);
                                $legend = Tools::truncateString($legend, $truncate_legend, $truncate_options);
                                $already_translated[$original] = $legend;
                            }
                            $image->legend[$to_id] = $already_translated[$original];
                            $this->saveObject($image);
                        }
                    }

                    // update search index
                    if (in_array($obj->visibility, array('both', 'search')) &&
                        Configuration::get('PS_SEARCH_INDEXATION')) {
                        Search::indexation(false, $obj->id);
                    }
                }
                break;
            case 'a_blog_post':
            case 'a_blog_category':
                $identifier_name = 'id_'.str_replace('a_blog_', '', $content_type);
                $table_name = _DB_PREFIX_.$content_type.'_lang';
                $imploded_shop_ids = implode(', ', $this->shop_ids);
                $row_from = $this->db->getRow('
                    SELECT * FROM '.pSQL($table_name).'
                    WHERE '.pSQL($identifier_name).' = '.(int)$identifier.'
                    AND id_lang = '.(int)$from_id.' AND id_shop = '.(int)$this->context->shop->id.'
                ');
                $rows_to = $this->db->executeS('
                    SELECT * FROM '.pSQL($table_name).'
                    WHERE '.pSQL($identifier_name).' = '.(int)$identifier.'
                    AND id_lang = '.(int)$to_id.' AND id_shop IN ('.pSQL($imploded_shop_ids).')
                ');
                $to_translate = array();
                foreach ($rows_to as $row) {
                    foreach ($row as $name => $value) {
                        if (Tools::substr($name, 0, 3) === 'id_' || $name == 'date_upd' || $name == 'link_rewrite') {
                            continue;
                        }
                        if ($overwrite_existing || !$value) {
                            $to_translate[$name] = $row_from[$name];
                        }
                    }
                }
                if (implode('', $to_translate)) {
                    $translated = $this->yandexTranslate($to_translate, $from, $to);
                    $updated_rows = array();
                    foreach ($rows_to as $row) {
                        foreach ($row as $name => $value) {
                            if (isset($translated[$name]) && ($overwrite_existing || !$value)) {
                                $row[$name] = $translated[$name];
                                if ($name == 'title') {
                                    $row['link_rewrite'] = Tools::str2url($translated[$name]);
                                }
                                $date = date('Y-m-d H:i:s');
                                if (isset($row['date_upd']) && $row['date_upd'] != $date) {
                                    $row['date_upd'] = $date;
                                }
                            }
                            $row[$name] = pSQL($row[$name], true);
                        }
                        $updated_rows[] = '(\''.implode('\', \'', $row).'\')';
                    }
                    if ($updated_rows) {
                        try {
                            $this->db->execute('
                                REPLACE INTO '.pSQL($table_name).' VALUES '.implode(', ', $updated_rows).'
                            ');
                        } catch (Exception $e) {
                            $msg = '"'.$row_from[$identifier_name].' - '.$row_from['title'].'" '.
                            $this->l('Was not updated ').':<br>'.$e->getMessage();
                            $this->throwError($msg);
                        }
                    }
                }
                break;
            case 'module':
                // translating from original template strings, that should be in English
                $from = 'en';
                $modules_list = array($identifier);
                $admin_translations_obj = new AdminTranslationsController();
                $reflection = new ReflectionClass('AdminTranslationsController');

                $_POST['type'] = 'modules';
                $_POST['iso_code'] = $to;
                $location = Tools::getValue('location', 'core');
                if ($location != 'core') {
                    $_POST['theme'] = $location;
                }

                // workaround for PSCSX-5049
                $GLOBALS['_MODULES'] = array();

                $admin_translations_obj->getInformations();

                $get_files_method = $reflection->getMethod('getAllModuleFiles');
                $get_files_method->setAccessible(true);
                $args = array($modules_list, null, $to, true);
                $arr_files = $get_files_method->invokeArgs($admin_translations_obj, $args);

                $fill_method = $reflection->getMethod('findAndFillTranslations');
                $fill_method->setAccessible(true);
                foreach ($arr_files as $value) {
                    if ($value['module'] != $identifier) {
                        continue;
                    }
                    $args = array($value['files'], $value['theme'], $value['module'], $value['dir']);
                    $fill_method->invokeArgs($admin_translations_obj, $args);
                }

                $translations_prop = $reflection->getProperty('modules_translations');
                $translations_prop->setAccessible(true);
                $translations = $translations_prop->getValue($admin_translations_obj);
                $to_translate = array();
                $to_unwrap = array();

                // nested loop same as in translation_modules.tpl
                foreach ($translations as $theme_name => $theme) {
                    foreach ($theme as $mod_name => $module) {
                        foreach ($module as $template_name => $new_lang) {
                            foreach ($new_lang as $key => $value) {
                                $encoded_key = Tools::strtolower($mod_name);
                                if ($theme_name) {
                                    $encoded_key .= '_'.Tools::strtolower($theme_name);
                                }
                                $encoded_key .= '_'.Tools::strtolower($template_name);
                                $encoded_key .= '_'.md5($key);
                                $encoded_key = md5($encoded_key);

                                if ($value['trad'] && !$overwrite_existing) {
                                    $_POST[$encoded_key] = $this->unescape($value['trad']);
                                } else {
                                    $text = $key;
                                    if ($value['use_sprintf']) {
                                        $text = $this->wrapSrintfSymbols($text, $value['use_sprintf']);
                                        $to_unwrap[$encoded_key] = $value['use_sprintf'];
                                    }
                                    $to_translate[$encoded_key] = $this->unescape($text);
                                }
                            }
                        }
                    }
                }
                if ($to_translate) {
                    $translated = $this->yandexTranslate($to_translate, $from, $to);
                    foreach ($translated as $k => $t) {
                        if (isset($to_unwrap[$k])) {
                            $_POST[$k] = $this->unwrapSrintfSymbols($t, $to_unwrap[$k]);
                        } else {
                            $_POST[$k] = $t;
                        }
                    }
                }

                $translation_file = _PS_THEME_SELECTED_DIR_ ? _PS_THEME_SELECTED_DIR_ : _PS_ROOT_DIR_.'/';
                $translation_file .= 'modules/'.$identifier.'/translations/'.$to.'.php';
                $creation_required = !file_exists($translation_file);

                $write_method = $reflection->getMethod('findAndWriteTranslationsIntoFile');
                $write_method->setAccessible(true);
                foreach ($arr_files as $value) {
                    $args = array(
                        $value['file_name'],
                        $value['files'],
                        $value['theme'],
                        $value['module'],
                        $value['dir']
                    );
                    $write_method->invokeArgs($admin_translations_obj, $args);
                }

                if ($creation_required && file_exists($translation_file)) {
                    $new_file = str_replace(_PS_ROOT_DIR_, '', $translation_file);
                    $additinal_response = ' | '.sprintf($this->l('New file was created: %s'), $new_file);
                }
                break;
            case 'theme':
                $from = 'en';
                $_POST['type'] = 'front';
                $_POST['theme'] = $identifier;
                $_POST['iso_code'] = $to;
                $translations = $to_translate = $to_unwrap = array();

                if ($this->is_17) {
                    // temporarily not available
                    // require_once('controllers/admin/ThemeAutoTranslator.php');
                    // $translator = new ThemeAutoTranslator();
                } else {
                    $admin_translations_obj = new AdminTranslationsController();
                    $admin_translations_obj->getInformations();
                    $admin_translations_obj->initContent();

                    if (isset($admin_translations_obj->tpl_view_vars['tabsArray'])) {
                        $tpl_view_vars = $admin_translations_obj->tpl_view_vars['tabsArray'];
                        foreach ($tpl_view_vars as $f_name => $current_translations) {
                            foreach ($current_translations as $orig_str => $current_translation) {
                                // encoded_key same as in translation_form.tpl
                                $encoded_key = Tools::strtolower($f_name).'_'.md5($orig_str);
                                if ($current_translation['trad'] && !$overwrite_existing) {
                                    $translations[$encoded_key] = $this->unescape($current_translation['trad']);
                                } else {
                                    if ($current_translation['use_sprintf']) {
                                        $orig_str = $this->wrapSrintfSymbols(
                                            $orig_str,
                                            $current_translation['use_sprintf']
                                        );
                                        $to_unwrap[$encoded_key] = $current_translation['use_sprintf'];
                                    }
                                    $to_translate[$encoded_key] = $this->unescape($orig_str);
                                }
                            }
                        }
                    }
                    if ($to_translate) {
                        $translated = $this->yandexTranslate($to_translate, $from, $to);
                        foreach ($translated as $k => $t) {
                            if (isset($to_unwrap[$k])) {
                                $translations[$k] = $this->unwrapSrintfSymbols($t, $to_unwrap[$k]);
                            } else {
                                $translations[$k] = $t;
                            }
                            $file_path = _PS_ALL_THEMES_DIR_.Tools::getValue('theme').'/lang/'.$to.'.php';
                            $this->writeTranslationFile($file_path, '_LANG', $translations);
                        }
                    }
                }
                break;
        }

        $time = microtime(true) - $time;
        $msg = sprintf($this->l('%d characters in %s seconds'), $this->char_count, round($time, 2));
        $response = Tools::strtoupper($to).': '.$msg;
        if (isset($additinal_response)) {
            $response .= $additinal_response;
        }
        $ret = array(
            'hasError' => false,
            'response' => utf8_encode($response),
            'stats_data' => $this->getStatsData(),
        );
        exit(Tools::jsonEncode($ret));
    }

    public function createObject($class_name, $identifier)
    {
        if (!class_exists($class_name)) {
            $this->throwError(sprintf($this->l('Class %s is not available'), $class_name));
        }
        $obj = new $class_name($identifier);
        if (!Validate::isLoadedObject($obj)) {
            $this->throwError(sprintf($this->l('%s could not be loaded'), $class_name));
        }
        return $obj;
    }

    public function getTranslatableFields($obj)
    {
        $definition = ObjectModel::getDefinition($obj);
        $fields = array();
        foreach ($definition['fields'] as $field_name => $data) {
            if (!empty($data['lang'])) {
                $fields[$field_name] = !empty($data['size']) ? $data['size'] : 0;
            }
        }
        return $fields;
    }

    public function saveObject($obj)
    {
        try {
            $obj->save();
        } catch (Exception $e) {
            if (get_class($obj) == 'Image' && !empty($obj->id_product)) {
                $identifier = '[ID='.$obj->id_product.'], image '.$obj->id.':';
            } else {
                $identifier = '[ID='.$obj->id.']';
            }
            $msg = $identifier.' '.$e->getMessage();
            $this->throwError($msg);
        }
        return true;
    }

    public function unescape($string)
    {
        // cannot use Tools::stripslashes(), because it strips slashes only if magic_quotes are used
        return stripslashes($string);
    }

    /*
    * Need to wrap sprintf symbols in order to leave them without translation
    */
    public function wrapSrintfSymbols($string, $sprintf)
    {
        $spintf = explode(', ', $sprintf);
        $arr_replace = array();
        foreach ($spintf as $k => $spr) {
            $arr_replace[$spr] = '[|'.$k.'|]';
        }
        $string = str_replace(array_keys($arr_replace), $arr_replace, $string);
        return $string;
    }

    public function unwrapSrintfSymbols($string, $sprintf)
    {
        $spintf = explode(', ', $sprintf);
        $arr_replace = array();
        foreach ($spintf as $k => $spr) {
            $arr_replace['[|'.$k.'|]'] = $spr;
        }
        $string = str_replace(array_keys($arr_replace), $arr_replace, $string);
        return $string;
    }

    public function yandexTranslate($content, $from, $to, $key = false)
    {
        $iso_substitutions = array(
            'gb' => 'en',
            'si' => 'sl', // Slovenian is represented by si in previous PS versions
        );
        $from = isset($iso_substitutions[$from]) ? $iso_substitutions[$from] : $from;
        $to = isset($iso_substitutions[$to]) ? $iso_substitutions[$to] : $to;

        if (is_array($content)) {
            $separator = '<br class="ch_s">';
            $content_keys = array_keys($content);
            $content = implode($separator, $content);
        }
        $url = 'https://translate.yandex.net/api/v1.5/tr.json/translate';
        if (!$key) {
            $key = $this->key;
        }
        $data = array (
            'key'    => $key.'',
            'lang'   => $from.'-'.$to,
            'text'   => $content,
            'format' => 'html'
        );

        $translation = '';

        if (function_exists('curl_init')) {
            $session = curl_init($url);
            curl_setopt($session, CURLOPT_HTTPHEADER, array(
                'Expect: */*',
                'Content-Type: application/x-www-form-urlencoded',
            ));
            curl_setopt($session, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($session);
            curl_close($session);

            $response = Tools::jsonDecode($response, true);

            if ($response['code'] !== 200) {
                $msg = $this->l('Error').' '.$response['code'].' ('.$from.'-'.$to.')';
                if (isset($response['message'])) {
                    $msg .= ': '.$response['message'];
                }
                $this->errors[] = $msg;
            } elseif (!isset($response['text'][0])) {
                $this->errors[] = $this->l('Unknown error');
            } else {
                $translation = $response['text'][0];
                $translation = html_entity_decode($translation, ENT_QUOTES | ENT_XML1, 'UTF-8');
                if (isset($separator)) {
                    $exploded = explode($separator, $translation);
                    if (count($exploded) != count($content_keys)) {
                        $this->errors[] = $this->l('Bulk translation failed');
                    }
                    $translation = array();
                    foreach ($exploded as $k => $t) {
                        $translation[$content_keys[$k]] = $t;
                    }
                }
            }
        } else {
            $this->throwError($this->l('cURL extension is required for automatic translations'));
        }
        if ($this->errors && Tools::isSubmit('ajax_action')) {
            $this->throwError($this->errors);
        } elseif ($this->errors) {
            return false;
        }

        if (!Tools::isSubmit('updateAPIkey')) {
            $char_count = Tools::strlen($content);
            $this->updateStatsData($char_count);
            $this->char_count += $char_count;
        }

        return $translation;
    }

    /**
    * based on AdmiTranslationsController::writeTranslationFile
    **/
    protected function writeTranslationFile($file_path, $tab, $translations)
    {
        if ($file_path && !file_exists($file_path)) {
            if (!file_exists(dirname($file_path)) && !mkdir(dirname($file_path), 0777, true)) {
                $this->throwError(sprintf($this->l('Directory "%s" cannot be created'), dirname($file_path)));
            } elseif (!touch($file_path)) {
                $this->throwError(sprintf(Tools::displayError('File "%s" cannot be created'), $file_path));
            }
        }
        $thm_name = str_replace('.', '', Tools::getValue('theme'));
        $kpi_key = Tools::substr(Tools::strtoupper($thm_name.'_'.Tools::getValue('lang')), 0, 16);

        if ($fd = fopen($file_path, 'w')) {
            $to_insert = array();
            foreach ($translations as $key => $value) {
                if (!empty($value)) {
                    $to_insert[$key] = $value;
                }
            }

            ConfigurationKPI::updateValue('FRONTOFFICE_TRANSLATIONS_EXPIRE', time());
            ConfigurationKPI::updateValue('TRANSLATE_TOTAL_'.$kpi_key, count($translations));
            ConfigurationKPI::updateValue('TRANSLATE_DONE_'.$kpi_key, count($to_insert));

            // translations array is ordered by key (easy merge)
            ksort($to_insert);
            fwrite($fd, "<?php\n\nglobal \$".$tab.";\n\$".$tab." = array();\n");
            foreach ($to_insert as $key => $value) {
                fwrite($fd, '$'.$tab.'[\''.pSQL($key, true).'\'] = \''.pSQL($value, true).'\';'."\n");
            }
            fwrite($fd, "\n?>");
            fclose($fd);
        } else {
            $this->throwError(sprintf(Tools::displayError('Cannot write this file: "%s"'), $file_path));
        }
    }

    /*
    * retro-compatibility
    */
    public function displayWarning($msg)
    {
        if (!is_array($msg)) {
            $msg = array($msg);
        }
        $html = '<div class="alert alert-warning">';
        $html .= '<button type="button" class="close" data-dismiss="alert">&times;</button>';
        $html .= '<ul class="list-unstyled">';
        foreach ($msg as $m) {
            $html .= '<li>'.$m.'</li>';
        }
        $html .= '</ul>';
        $html .= '</div>';
        return $html;
    }

    public function throwError($errors)
    {
        if (!is_array($errors)) {
            $errors = array($errors);
        }
        $error_html = $this->displayError(implode('<br>', $errors));
        $ret = array(
            'hasError' => true,
            'errors' => utf8_encode($error_html),
        );
        exit(Tools::jsonEncode($ret));
    }
}
