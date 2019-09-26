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

include_once(_PS_MODULE_DIR_.'leomenusidebar/classes/Sbmegamenu.php');
include_once(_PS_MODULE_DIR_.'leomenusidebar/libs/Helper.php');
if (file_exists(_PS_MODULE_DIR_.'leotempcp/classes/widgetbase.php')) {
    require_once( _PS_MODULE_DIR_.'leotempcp/classes/widgetbase.php' );
}
if (file_exists(_PS_MODULE_DIR_.'leotempcp/classes/widget.php')) {
    require_once( _PS_MODULE_DIR_.'leotempcp/classes/widget.php' );
}

class Leomenusidebar extends Module
{
    private $_html = '';
    private $configs = '';
    protected $params = null;
    public $_languages;
    public $_defaultFormLanguage;
    public $base_config_url;
    public $widget;
    public $theme_name = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->name = 'leomenusidebar';
        $this->tab = 'front_office_features';
        $this->version = '3.0.0';
        $this->author = 'LeoTheme';
        $this->need_instance = 0;
        $this->bootstrap = true;

        $this->secure_key = Tools::encrypt($this->name);

        parent::__construct();
        if (Module::isInstalled($this->name)) {
            $this_version = Configuration::get('LEO_MENUSIDEBAR_VERSION') ? Configuration::get('LEO_MENUSIDEBAR_VERSION') : '';
            $this->checkVersion($this_version);
        }

        $current_index = AdminController::$currentIndex;

        $this->base_config_url = $current_index.'&configure='.$this->name.'&token='.Tools::getValue('token');

        $this->displayName = $this->l('Leo  Megamenu Silebar');
        $this->description = $this->l('Leo  Megamenu Silebar Support Leo Framework Version 3.0.0');
        $this->languages();
        $this->theme_name = Context::getContext()->shop->getTheme();
        $this->img_path = _PS_ALL_THEMES_DIR_.$this->theme_name.'/img/modules/'.$this->name.'/icons/';

        $this->widget = new LeoTempcpWidget();
    }

    /**
     *
     */
    public function languages()
    {
        //global $cookie;
        $cookie = $this->context->cookie;
        $allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        if ($allow_employee_form_lang && !$cookie->employee_form_lang) {
            $cookie->employee_form_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        }
        $use_lang_from_cookie = false;
        $this->_languages = Language::getLanguages(false);
        if ($allow_employee_form_lang) {
            foreach ($this->_languages as $lang) {
                if ($cookie->employee_form_lang == $lang['id_lang']) {
                    $use_lang_from_cookie = true;
                }
            }
        }
        if (!$use_lang_from_cookie) {
            $this->_defaultFormLanguage = (int)Configuration::get('PS_LANG_DEFAULT');
        } else {
            $this->_defaultFormLanguage = (int)$cookie->employee_form_lang;
        }
    }

    public function install()
    {
        /* Adds Module */
        if (parent::install() && $this->registerHook('header') && $this->registerHook('leftColumn') && $this->registerHook('displayTop') && $this->registerHook('displayTopColumn') && $this->registerHook('displayNav') && $this->registerHook('displaySlideshow') && Configuration::updateValue('sbmenu_iscache', 1) && Configuration::updateValue('sbmenu_cachetime', 24) && Configuration::updateValue('LEO_MENUSIDEBAR_TYPE', 'right')) {
            $res = true;
            $res = $this->createTables();
            $this->registerHook('header'); # remove code in 2016
            return (bool)$res;
        }

        return false;
    }

    /**
     * @see Module::uninstall()
     */
    public function uninstall()
    {
        if (parent::uninstall()) {
            return Db::getInstance()->execute('
            DROP TABLE IF EXISTS `'._DB_PREFIX_.'sbmegamenu`, `'._DB_PREFIX_.'sbmegamenu_lang`, `'._DB_PREFIX_.'sbmegamenu_shop`');
        }
        return false;
    }

    /**
     * Creates tables
     */
    protected function createTables()
    {
        if ($this->installDataSample()) {
            return true;
        }
        $res = 1;
        include_once( dirname(__FILE__).'/install/install.php' );
        return $res;
    }

    private function installDataSample()
    {
        if (!file_exists(_PS_MODULE_DIR_.'leotempcp/libs/DataSample.php')) {
            return false;
        }
        require_once( _PS_MODULE_DIR_.'leotempcp/libs/DataSample.php' );

        $sample = new Datasample(1);
        return $sample->processImport($this->name);
    }

    /**
     * render content info
     */
    public function getContent()
    {
        // UPDATE VERSION
        LeoSbmegamenuHelper::leoCreateColumn('sbmegamenu', 'groupBox', 'varchar(255) DEFAULT "all"');

        $output = '';
        $this->_html .= $this->headerHTML();
        $this->_html .= '<h2>'.$this->displayName.'.</h2>';

        /* update tree megamenu positions */
        if (Tools::getValue('doupdatepos') && Tools::isSubmit('updatePosition')) {
            $list = Tools::getValue('list');
            $root = 1;
            $child = array();
            foreach ($list as $id => $parent_id) {
                if ($parent_id <= 0) {
                    $parent_id = $root;
                }

                $child[$parent_id][] = $id;
            }
            $res = true;
            foreach ($child as $id_parent => $menus) {
                $i = 0;
                foreach ($menus as $id_sbmegamenu) {
                    $sql = 'UPDATE `'._DB_PREFIX_.'sbmegamenu` SET `position` = '.(int)$i.', id_parent = '.(int)$id_parent.' 
                        WHERE `id_sbmegamenu` = '.(int)$id_sbmegamenu;
                    $res &= Db::getInstance()->execute($sql);
                    $i++;
                }
            }
            $this->clearCache();
            die($this->l('Update Positions Done'));
        }

        if (Tools::getValue('typesub') && Tools::isSubmit('updatetypesub')) {
            $type = Tools::getValue('type');
            Configuration::updateValue('LEO_MENUSIDEBAR_TYPE', $type);
            $this->clearCache();
            die($this->l('Update Type Sub  Done'));
        }

        /* delete megamenu item */
        if (Tools::getValue('dodel')) {
            $obj = new Sbmegamenu((int)Tools::getValue('id_sbmegamenu'));
            $res = $obj->delete();
            $this->clearCache();
            Tools::redirectAdmin(AdminController::$currentIndex.'&successful=1&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
        }
        if (Tools::isSubmit('save'.$this->name) && Tools::isSubmit('active')) {
            //if( Tools::getValue('type') == 'url' && !Tools::getValue('url')){
            // $errors[] = $this->l('Account details are required.');
            //}
//			if (!isset($errors) AND !count($errors)){
            if ($id_sbmegamenu = Tools::getValue('id_sbmegamenu')) {
                $megamenu = new Sbmegamenu((int)$id_sbmegamenu);
            } else {
                $megamenu = new Sbmegamenu();
            }

            $keys = LeoSbmegamenuHelper::getConfigKey(false);
            $post = LeoSbmegamenuHelper::getPost($keys, false);
            $keys = LeoSbmegamenuHelper::getConfigKey(true);
            $post += LeoSbmegamenuHelper::getPost($keys, true);

            $megamenu->copyFromPost($post);
            $megamenu->id_shop = $this->context->shop->id;

            $url_default = '';
            foreach ($megamenu->url as $menu_url) {
                if ($menu_url) {
                    $url_default = $menu_url;
                    break;
                }
            }
            if ($url_default) {
                foreach ($megamenu->url as &$menu_url) {
                    if (!$menu_url) {
                        $menu_url = $url_default;
                    }
                }
            }
            if ($megamenu->type && $megamenu->type != 'html' && Tools::getValue($megamenu->type.'_type')) {
                $megamenu->item = Tools::getValue($megamenu->type.'_type');
            }

            $errors = array();
            if ($megamenu->validateFields(false) && $megamenu->validateFieldsLang(false)) {
                if(!Tools::getValue('id_sbmegamenu')){
                    # Auto set position when create menu
                    $megamenu->position = Sbmegamenu::getLastPosition((int)$megamenu->id_parent);
                }

                $megamenu->save();
                $megamenu->cleanPositions($megamenu->id_parent);
                if (isset($_FILES['image']) && isset($_FILES['image']['tmp_name']) && !empty($_FILES['image']['tmp_name'])) {
                    $this->checkFolderIcon();
                    if (ImageManagerCore::validateUpload($_FILES['image'])) {
                        return false;
                    } elseif (!($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES['image']['tmp_name'], $tmp_name)) {
                        return false;
                    } elseif (!ImageManager::resize($tmp_name, $this->img_path.$_FILES['image']['name'])) {
                        return false;
                    }
                    unlink($tmp_name);
                    $megamenu->image = $_FILES['image']['name'];
                    $megamenu->save();
                } else if (Tools::getIsset('delete_icon')) {
                    if ($megamenu->image) {
                        unlink($this->img_path.$megamenu->image);
                        $megamenu->image = '';
                        $megamenu->save();
                    }
                }
                Tools::redirectAdmin(AdminController::$currentIndex.'&configure=leomenusidebar&successful=1&save'.$this->name.'&token='.Tools::getValue('token').'&id_sbmegamenu='.$megamenu->id);
            } else {
                $errors[] = $this->l('An error occurred while attempting to save.');
            }
//			}
            if (isset($errors) && count($errors)) {
                $output .= $this->displayError(implode('<br />', $errors));
            } else {
                $this->clearCache();
                $output .= $this->displayConfirmation($this->l('Settings updated.'));
            }
        }

        return $output.$this->displayForm();
    }

    /**
     * show megamenu item configuration.
     */
    protected function showFormSetting()
    {
        $this->context->controller->addJS(__PS_BASE_URI__.'modules/leomenusidebar/assets/admin/jquery.nestable.js');
        $this->context->controller->addJS(__PS_BASE_URI__.'modules/leomenusidebar/assets/admin/form.js');

        $this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/plugins/jquery.cookie-plugin.js');
        $this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/ui/jquery.ui.tabs.min.js');
        $this->context->controller->addCss(__PS_BASE_URI__.'js/jquery/ui/themes/base/jquery.ui.tabs.css');
        $this->context->controller->addCss(__PS_BASE_URI__.'modules/leomenusidebar/assets/admin/form.css');

        $this->widget->loadEngines();

        $id_lang = $this->context->language->id;
        $id_sbmegamenu = (int)Tools::getValue('id_sbmegamenu');
        $obj = new Sbmegamenu($id_sbmegamenu);
        $tree = $obj->getTree();
        $categories = LeoSbmegamenuHelper::getCategories();
        $manufacturers = Manufacturer::getManufacturers(false, $id_lang, true);
        $suppliers = Supplier::getSuppliers(false, $id_lang, true);
        $cmss = CMS::listCms($this->context->language->id, false, true);
        $menus = $obj->getDropdown(null, $obj->id_parent);

        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $soption = array(
            array(
                'id' => 'active_on',
                'value' => 1,
                'label' => $this->l('Enabled')
            ),
            array(
                'id' => 'active_off',
                'value' => 0,
                'label' => $this->l('Disabled')
            )
        );

        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Create New MegaMenu Item.'),
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'label' => $this->l('Megamenu ID'),
                    'name' => 'id_sbmegamenu',
                    'default' => 0,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Title:'),
                    'name' => 'title',
                    'value' => true,
                    'lang' => true,
                    'default' => '',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Sub Title:'),
                    'lang' => true,
                    'name' => 'text',
                    'cols' => 40,
                    'rows' => 10,
                    'default' => '',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Parent ID'),
                    'name' => 'id_parent',
                    'options' => array('query' => $menus,
                        'id' => 'id',
                        'name' => 'title'),
                    'default' => 'url',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Is Active'),
                    'name' => 'active',
                    'values' => $soption,
                    'default' => '1',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show Title'),
                    'name' => 'show_title',
                    'values' => $soption,
                    'default' => '1',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Menu Type'),
                    'name' => 'type',
                    'id' => 'menu_type',
                    'desc' => $this->l('Select a menu link type and fill data for following input'),
                    'options' => array('query' => array(
                            array('id' => 'url', 'name' => $this->l('Url')),
                            array('id' => 'category', 'name' => $this->l('Category')),
                            array('id' => 'product', 'name' => $this->l('Product')),
                            array('id' => 'manufacture', 'name' => $this->l('Manufacture')),
                            array('id' => 'supplier', 'name' => $this->l('Supplier')),
                            array('id' => 'cms', 'name' => $this->l('Cms')),
                            array('id' => 'html', 'name' => $this->l('Html'))
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'default' => 'url',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Product ID'),
                    'name' => 'product_type',
                    'id' => 'product_type',
                    'class' => 'menu-type-group',
                    'default' => '',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('CMS Type'),
                    'name' => 'cms_type',
                    'id' => 'cms_type',
                    'options' => array('query' => $cmss,
                        'id' => 'id_cms',
                        'name' => 'meta_title'),
                    'default' => '',
                    'class' => 'menu-type-group',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('URL'),
                    'name' => 'url',
                    'id' => 'url_type',
                    'lang' => true,
                    'required' => true,
                    'class' => 'url-type-group-lang',
                    'default' => '',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Category Type'),
                    'name' => 'category_type',
                    'id' => 'category_type',
                    'options' => array('query' => $categories,
                        'id' => 'id_category',
                        'name' => 'name'),
                    'default' => '',
                    'class' => 'menu-type-group',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Manufacture Type'),
                    'name' => 'manufacture_type',
                    'id' => 'manufacture_type',
                    'options' => array('query' => $manufacturers,
                        'id' => 'id_manufacturer',
                        'name' => 'name'),
                    'default' => '',
                    'class' => 'menu-type-group',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Supplier Type'),
                    'name' => 'supplier_type',
                    'id' => 'supplier_type',
                    'options' => array('query' => $suppliers,
                        'id' => 'id_supplier',
                        'name' => 'name'),
                    'default' => '',
                    'class' => 'menu-type-group',
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('HTML Type'),
                    'name' => 'content_text',
                    'desc' => $this->l('This menu is only for display content,PLease do not select it for menu level 1'),
                    'lang' => true,
                    'default' => '',
                    'autoload_rte' => true,
                    'class' => 'menu-type-group-lang',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Target Open'),
                    'name' => 'target',
                    'options' => array('query' => array(
                            array('id' => '_self', 'name' => $this->l('Self')),
                            array('id' => '_blank', 'name' => $this->l('Blank')),
                            array('id' => '_parent', 'name' => $this->l('Parent')),
                            array('id' => '_top', 'name' => $this->l('Top'))
                        ),
                        'id' => 'id',
                        'name' => 'name'),
                    'default' => '_self',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Menu Class'),
                    'name' => 'menu_class',
                    'display_image' => true,
                    'default' => ''
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Menu Icon Class'),
                    'name' => 'icon_class',
                    'display_image' => true,
                    'default' => '',
                    'desc' => $this->l('The module integrated with FontAwesome').'. '
                    .$this->l('Check list of icons and class name in here')
                    .' <a href="http://fontawesome.io/" target="_blank">http://fontawesome.io/</a> or your icon class'
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Or Menu Icon Image'),
                    'name' => 'image',
                    'display_image' => true,
                    'default' => '',
                    'desc' => $this->l('Use image icon if no use con Class'),
                    'thumb' => '',
                    'title' => $this->l('Icon Preview'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Group Submenu'),
                    'name' => 'is_group',
                    'values' => $soption,
                    'default' => '0',
                    'desc' => $this->l('Group all sub menu to display in same level')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Column'),
                    'name' => 'colums',
                    'values' => $soption,
                    'default' => '1',
                    'desc' => $this->l('Set each sub menu item as column'),
                ),
                array(
                    'type' => 'group',
                    'label' => $this->l('Group access'),
                    'name' => 'groupBox',
                    'values' => Group::getGroups(Context::getContext()->language->id),
                    'hint' => $this->l('Mark all of the customer groups which you would like to have access to this menu.'),
                    'default' => '1',
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button btn btn-danger'
            )
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        foreach (Language::getLanguages(false) as $lang) {
            $helper->languages[] = array(
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
            );
        }
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->toolbar_scroll = true;
        $helper->title = $this->displayName;
        $helper->submit_action = 'save'.$this->name;
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues($obj),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        $liveeditor_url = AdminController::$currentIndex.'&configure='.$this->name.'&liveeditor=1&token='.Tools::getAdminTokenLite('AdminModules');

        $action = AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules');
        $helper->toolbar_btn = array(
            'back' => array(
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );
        if(Tools::getValue('successful') == 1)
        {
            $this->_html .= '<div class="bootstrap">
                <div class="alert alert-success">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    Successfully
                </div>
            </div>';
        }
        $html = $this->_html.'<div class="col-lg-12"> <div class="" style="float: right"><div class="pull-right"><a href="'.$liveeditor_url.'" class="btn btn-danger"> '
                .$this->l('Live Edit Tools').'</a> '.$this->l('To Make Rich Content For Megamenu').'</div></div></div>';

        $output = $html.'
                 <ul class="nav nav-tabs clearfix">
                  <li class="active"><a href="#megamenu" data-toggle="tab">'.$this->l('Megamenu').'</a></li>
                </ul>

 
            <div class="tab-content clearfix">
              <div class="tab-pane active" id="megamenu">
        ';
        $typesub = Configuration::get('LEO_MENUSIDEBAR_TYPE');
        $addnew = AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules').'&configure='.$this->name.'&tab_module=front_office_features&module_name='.$this->name;
        $output .= '<div class="col-md-4"><div class="panel panel-default"><h3 class="panel-title">'.$this->l('Tree Megamenu Management').'</h3>
				<div class="panel-content">'.$this->l('To sort orders or update parent-child, you drap and drop expected menu, then click to Update button to Save')
                .'<hr><p><input type="button" value="'.$this->l('New Menu Item').'" id="addcategory" data-loading-text="'.$this->l('Processing ...').'" class="btn btn-danger" name="addcategory">
					<a   href="'.Context::getContext()->link->getAdminLink('AdminLeotempcpWidgets').'" class="leo-modal-action btn btn-modeal btn-success btn-info">'.$this->l('List Widget').'</a></p>
					<hr><p><input type="button" value="'.$this->l('Update Type Sub').'" id="typesub" data-loading-text="'.$this->l('Processing ...').'" class="btn btn-info" ></p>
						<label>'.$this->l('Type Sub').'</label>
						<select name="typesub" class="type_sub">
							<option value="auto" '.((isset($typesub) && $typesub == 'auto') ? 'selected="selected"' : null).'>'.$this->l('Auto').'</option>
							<option value="right" '.((isset($typesub) && $typesub == 'right') ? 'selected="selected"' : null).'>'.$this->l('Right').'</option>
							<option value="left" '.((isset($typesub) && $typesub == 'left') ? 'selected="selected"' : null).'>'.$this->l('Left').'</option>
						</select>
						
						<hr><p><input type="button" value="'.$this->l('Update Positions').'" id="serialize" data-loading-text="'.$this->l('Processing ...').'" class="btn btn-danger" name="serialize"></p><hr>'.$tree.'</div></div></div>
				<div class="col-md-8">'.$helper->generateForm($this->fields_form).'</div>
				<script type="text/javascript">var addnew ="'.$addnew.'"; var action="'.$action.'";$("#content").PavMegaMenuList({action:action,addnew:addnew});</script>';
        $output .= '</div>';
        $output .= '</div><script>$(\'#myTab a[href="#profile"]\').tab(\'show\')</script>';
        return $output;
    }

    public function getConfigFieldsValues($obj)
    {
        $languages = Language::getLanguages(false);
        $fields_values = array();
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
        $this->image_base_url = Tools::htmlentitiesutf8($protocol.$_SERVER['HTTP_HOST'].__PS_BASE_URI__).'themes/'.$this->theme_name.'/img/modules/leomenusidebar/icons/';
        foreach ($this->fields_form as $k => $f) {
            foreach ($f['form']['input'] as $j => $input) {

                if (isset($obj->{trim($input['name'])})) {
                    $data = $obj->{trim($input['name'])};

                    if ($input['name'] == 'image' && $data) {
                        $thumb = $this->image_base_url.$data;
                        $this->fields_form[$k]['form']['input'][$j]['thumb'] = $thumb;
                    }

                    if (isset($input['lang'])) {
                        foreach ($languages as $lang) {
                            $fields_values[$input['name']][$lang['id_lang']] = isset($data[$lang['id_lang']]) ? $data[$lang['id_lang']] : $input['default'];
                        }
                    } else {
                        $fields_values[$input['name']] = isset($data) ? $data : $input['default'];
                    }
                } else {
                    if (isset($input['lang'])) {
                        foreach ($languages as $lang) {
                            $v = Tools::getValue('title', Configuration::get($input['name'], $lang['id_lang']));
                            $fields_values[$input['name']][$lang['id_lang']] = $v ? $v : $input['default'];
                        }
                    } else {
                        $v = Tools::getValue($input['name'], Configuration::get($input['name']));
                        $fields_values[$input['name']] = $v ? $v : $input['default'];
                    }

                    if ($input['name'] == $obj->type.'_type') {
                        $fields_values[$input['name']] = $obj->item;
                    }
                }
            }
        }

        $id_menu_groups= $obj->getGroups();
        $groups = Group::getGroups($this->context->language->id);
        foreach ($groups as $group) {
            $fields_values['groupBox_'.$group['id_group']] = Tools::getValue('groupBox_'.$group['id_group'], (in_array($group['id_group'], $id_menu_groups) || (empty($id_menu_groups) && !$obj->id) || in_array('all', $id_menu_groups)));
        }

        return $fields_values;
    }

    /**
     * render menu tree using for editing
     */
    protected function ajxgenmenu()
    {
        $parent = '1';
        $params = array('params' => array());
        /* unset mega menu configuration */
        if (Tools::getValue('doreset')) {
            Configuration::updateValue('LEO_MENUSIDEBAR_PARAMS', '');
        }

        $params['params'] = Configuration::get('LEO_MENUSIDEBAR_PARAMS');
        if (isset($params['params']) && !empty($params['params'])) {
            $params['params'] = Tools::jsonDecode($params['params']);
        }

        $obj = new Sbmegamenu($parent);
        $typesub = Configuration::get('LEO_MENUSIDEBAR_TYPE');
        $tree = $obj->getFrontTree(1, true, $params['params'], $typesub);
        echo '<div id="leo-verticalmenu" class="leo-verticalmenu leo-menubackend highlighted hidden-xs hidden-sm ">
				<div class="box-content">
					<div class="navbar navbar-default">
						<div id="verticalmenu" class="verticalmenu" role="navigation">
							<a class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse" href="javascript:;">
								<span class="icon-bar"></span>
							</a>
							<div class="collapse navbar-collapse navbar-ex1-collapse">
                                '.$tree.'
							</div>
						</div>
                    </div>  
    				</div>	
    			</div>';
    }

    /**
     * Ajax Menu Information Action
     */
    public function ajxmenuinfo()
    {
        if (Tools::getValue('params')) {
            $params = trim(html_entity_decode(Tools::getValue('params')));
            Configuration::updateValue('LEO_MENUSIDEBAR_PARAMS', $params, true);
            $this->clearCache();
        }
        return $this->ajxgenmenu();
    }

    /**
     * show live editor tools 
     */
    protected function showLiveEditorSetting()
    {
        $this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/ui/jquery.ui.dialog.min.js');
        $this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/ui/jquery.ui.draggable.min.js');
        $this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/ui/jquery.ui.droppable.min.js');
        $this->context->controller->addJS(__PS_BASE_URI__.'modules/leomenusidebar/assets/admin/form.js');
        $this->context->controller->addCss(__PS_BASE_URI__.'modules/leomenusidebar/assets/admin/liveeditor.css');
        $this->context->controller->addJS(__PS_BASE_URI__.'modules/leomenusidebar/assets/admin/liveeditor.js');
        $tcss = _PS_ROOT_DIR_.'/themes/'.$this->context->shop->getTheme().'/css/modules/leomenusidebar/leomenusidebar.css';

        if (file_exists($tcss)) {
            $this->context->controller->addCss(_THEMES_DIR_.$this->context->shop->getTheme().'/css/modules/leomenusidebar/leomenusidebar.css');
        } else {
            $this->context->controller->addCss(__PS_BASE_URI__.'modules/leomenusidebar/leomenusidebar.css');
        }

        $liveedit_action = $this->base_config_url.'&liveeditor=1&do=livesave';
        $action_backlink = $this->base_config_url;
        $action_widget = _MODULE_DIR_.$this->name.'/widget.php';
        $action_addwidget = $this->base_config_url.'&liveeditor=1&do=addwidget';
        $ajxgenmenu = $this->base_config_url.'&liveeditor=1&do=ajxgenmenu';
        $ajxmenuinfo = $this->base_config_url.'&liveeditor=1&do=ajxmenuinfo';
        $id_shop = $this->context->shop->id;
        $shop = Shop::getShop($id_shop);
        if (!empty($shop)) {
            $live_site_url = $shop['uri'];
        } else {
            $live_site_url = __PS_BASE_URI__;
        }
        $model = $this->widget;
        $widgets = $model->getWidgets($id_shop);
        $type_menu = array('carousel', 'categoriestabs', 'manucarousel', 'map', 'producttabs');
        foreach ($widgets as $key => $widget) {
            if (in_array($widget['type'], $type_menu)) {
                unset($widgets[$key]);
            }
        }
        $typesub = Configuration::get('LEO_MENUSIDEBAR_TYPE');
        ob_start();
        $this_module = $this;
        require_once ( dirname(__FILE__).'/liveeditor.php' );
        $output = ob_get_contents();
        ob_end_clean();

        # validate module
        unset($liveedit_action);
        unset($action_backlink);
        unset($action_widget);
        unset($action_addwidget);
        unset($ajxgenmenu);
        unset($ajxmenuinfo);
        unset($live_site_url);
        unset($typesub);
        unset($this_module);

        return $output;
    }

    private function displayForm()
    {
        if (Tools::getValue('liveeditor')) {

            if (Tools::getValue('do')) {
                switch (Tools::getValue('do')) {
                    case 'ajxmenuinfo':
                        echo $this->ajxmenuinfo();
                        break;
                    case 'ajxgenmenu':
                        echo $this->ajxgenmenu();
                        break;
                    default:
                        break;
                }
                die;
            } else {
                return $this->showLiveEditorSetting();
            }
        } else {
            return $this->showFormSetting();
        }
    }

    /**
     *
     */
    private function postProcess()
    {
//		$errors = array();
    }

    public function hookDisplayHeaderRight()
    {
        return $this->processHook('displayheaderright');
    }

    public function hookDisplayNav()
    {
        return $this->processHook('displaynav');
    }

    public function hookDisplaySlideshow()
    {
        return $this->processHook('displayslideshow');
    }

    public function hookTopNavigation()
    {
        return $this->processHook('topnavigation');
    }

    public function hookDisplayPromoteTop()
    {
        return $this->processHook('displaypromoteTop');
    }

    public function hookDisplayBottom()
    {
        return $this->processHook('displaybottom');
    }

    public function hookDisplayContentBottom()
    {
        return $this->processHook('displaycontentbottom');
    }

    public function hookRightColumn()
    {
        return $this->processHook('rightcolumn');
    }

    public function hookLeftColumn()
    {
        return $this->processHook('leftcolumn');
    }

    public function hookDisplayTop()
    {
        return $this->processHook('displayTop');
    }

    public function hookDisplayTopColumn()
    {
        return $this->processHook('displayTopColumn');
    }

    public function hookHeader()
    {
        $this->context->controller->addCSS($this->_path.'leomenusidebar.css', 'all');
    }

    public function processHook($hook = null)
    {
        $theme_name = Context::getContext()->shop->getTheme();
        if (file_exists(_PS_ALL_THEMES_DIR_.$theme_name.'/modules/'.$this->name.'/views/templates/hook/'.$hook.'/megamenu.tpl')) {
            $tpl = '/views/templates/hook/'.$hook.'/megamenu.tpl';
        } elseif (file_exists(dirname(__FILE__).'/views/templates/hook/'.$hook.'/megamenu.tpl')) {
            $tpl = 'views/templates/hook/'.$hook.'/megamenu.tpl';
        } else {
            $tpl = 'views/templates/hook/megamenu.tpl';
        }

        if (!$this->isCached($tpl, $this->getCacheId())) {
            $params = array();
            $params['params'] = Configuration::get('LEO_MENUSIDEBAR_PARAMS');

            if (isset($params['params']) && !empty($params['params'])) {
                $params['params'] = Tools::jsonDecode($params['params']);
            }

            $obj = new Sbmegamenu();
            $obj->setModule($this);
            $typesub = Configuration::get('LEO_MENUSIDEBAR_TYPE');
            $theme = Context::getContext()->shop->theme_name;
            if ($typesub == 'auto') {
                $layout_direction = Configuration::get('LEOBASIC_LAYOUT_DIRECTION');
                $cookie = LeoSbmegamenuHelper::getCookie();
                if ($hook && $hook == 'rightcolumn') {
                    if (isset($cookie[$theme.'_layout_dir']) && $cookie[$theme.'_layout_dir']) {
                        $layout = $cookie[$theme.'_layout_dir'];
                        if ($layout == 'right-left-main' || $layout == 'right-main-left' || $layout == 'left-right-main') {
                            $typesub = 'right';
                        } elseif ($layout == 'main-left-right') {
                            $typesub = 'left';
                        } else {
                            if ($layout_direction == 'left-right-main' || $layout_direction == 'main-left-right') {
                                $typesub = 'right';
                            } else {
                                $typesub = 'left';
                            }
                        }
                    } else {
                        if ($layout_direction == 'left-right-main' || $layout_direction == 'main-left-right') {
                            $typesub = 'right';
                        } else {
                            $typesub = 'left';
                        }
                    }
                } elseif ($hook && $hook == 'leftcolumn') {
                    if (isset($cookie[$theme.'_layout_dir']) && $cookie[$theme.'_layout_dir']) {
                        $layout = $cookie[$theme.'_layout_dir'];
                        if ($layout == 'right-main-left' || $layout == 'main-left-right') {
                            $typesub = 'left';
                        } elseif ($layout == 'left-right-main' || $layout == 'right-left-main') {
                            $typesub = 'right';
                        } else {
                            if ($layout_direction == 'right-left-main' || $layout_direction == 'right-main-left') {
                                $typesub = 'left';
                            } else {
                                $typesub = 'right';
                            }
                        }
                    } else {
                        if ($layout_direction == 'right-left-main' || $layout_direction == 'right-main-left') {
                            $typesub = 'left';
                        } else {
                            $typesub = 'right';
                        }
                    }
                } elseif ($this->context->language->is_rtl) {
                    $typesub = 'left';
                } else {
                    $typesub = 'right';
                }
            }
            $boostrapmenu = $obj->getFrontTree(1, false, $params['params'], $typesub);
            $this->smarty->assign('boostrapmenu', $boostrapmenu);

            return $this->display(__FILE__, $tpl, $this->getCacheId());
        }
        return $this->display(__FILE__, $tpl, $this->getCacheId());
    }

    /**
     * Display Bootstrap MegaMenu
     */
    protected function getThemeDir()
    {
        return $this->useMobileTheme() ? _PS_THEME_MOBILE_DIR_ : _PS_THEME_DIR_;
    }

    protected function getCacheId($name = null, $hook = '')
    {
        $cache_array = array(
            $name !== null ? $name : $this->name,
            $hook,
            date('Ymd'),
            (int)Tools::usingSecureMode(),
            (int)$this->context->shop->id,
            (int)Group::getCurrent()->id,
            (int)$this->context->language->id,
            (int)$this->context->currency->id,
            (int)$this->context->country->id,
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443)
        );
        return implode('|', $cache_array);
    }

    /**
     * render widgets
     */
    public function renderwidget($id_shop)
    {
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }
        $widgets = Tools::getValue('widgets');

        $widgets = explode('|wid-', '|'.$widgets);
        if (!empty($widgets)) {
            unset($widgets[0]);
            $model = $this->widget;
            $model->setTheme(Context::getContext()->shop->getTheme());
            $model->langID = $this->context->language->id;
            $model->loadWidgets($id_shop);
            $model->loadEngines();
            $output = '';
            foreach ($widgets as $wid) {
                $content = $model->renderContent($wid);
                $output .= $this->getWidgetContent($wid, $content['type'], $content['data']);
            }
            echo $output;
        }
        die;
    }

    /**
     *
     */
    public function getWidgetContent($id, $type, $data, $show_widget_id = 1)
    {
        $data['id_lang'] = $this->context->language->id;

        $this->smarty->assign($data);
        $id_text = '';
        if ($show_widget_id) {
            $id_text = ' id="wid-'.$id.'"';
        }
        $output = '<div class="leo-widget"'.$id_text.'>';
        $output .= $this->display(__FILE__, 'views/widgets/widget_'.$type.'.tpl');
        $output .= '</div>';
        return $output;
    }

    /**
     *
     */
    public function clearCache()
    {
        $tpl = 'views/templates/hook/megamenu.tpl';
        $this->_clearCache($tpl);
    }

    /**
     *
     */
    public function headerHTML()
    {
        if (Tools::getValue('controller') != 'AdminModules' && Tools::getValue('configure') != $this->name) {
            return;
        }
        $this->context->controller->addJqueryUI('ui.sortable');
        $html = '';
        return $html;
    }

    public function checkVersion($version)
    {
        $versions = array(
            '3.0.0'
        );

        if ($version && $version == $versions[count($versions) - 1]) {
            return;
        }
        foreach ($versions as $ver) {
            if (!$version || ($version && $version < $ver)) {
                if ($this->checktable()) {
                    $checkcolumn = Db::getInstance()->executeS("
						SELECT * FROM INFORMATION_SCHEMA.COLUMNS
						WHERE TABLE_SCHEMA = '"._DB_NAME_."'
						AND TABLE_NAME='"._DB_PREFIX_."sbmegamenu_lang'
						AND COLUMN_NAME ='url'
				");
                    if (count($checkcolumn) < 1) {
                        Db::getInstance()->execute('
						ALTER TABLE `'._DB_PREFIX_.'sbmegamenu_lang` 
							ADD `url` varchar(255) DEFAULT NULL');
                        $menus = Db::getInstance()->executeS('SELECT `id_sbmegamenu`,`id_parent`,`url` FROM `'._DB_PREFIX_.'sbmegamenu`');
                        if ($menus) {
                            foreach ($menus as $menu) {
                                if ($menu['id_parent'] != 0) {
                                    $sbmegamenu = new Sbmegamenu((int)$menu['id_sbmegamenu']);
                                    $sbmegamenu->update();
                                }
                            }
                        }
                        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'sbmegamenu` DROP `url`');
                        Configuration::updateValue('LEO_MENUSIDEBAR_VERSION', $ver);
                    }
                }
            }
        }
    }

    public function checktable()
    {
        $checktable = Db::getInstance()->executeS("
						SELECT * FROM INFORMATION_SCHEMA.COLUMNS
						WHERE TABLE_SCHEMA = '"._DB_NAME_."'
						AND TABLE_NAME='"._DB_PREFIX_."sbmegamenu_lang'
				");
        if (count($checktable) < 1) {
            return false;
        } else {
            return true;
        }
    }

    public function checkFolderIcon()
    {
        if (file_exists($this->img_path) && is_dir($this->img_path)) {
            return;
        }
        if (!file_exists($this->img_path) && !is_dir($this->img_path)) {
            @mkdir(_PS_ALL_THEMES_DIR_.$this->theme_name.'/img/modules/', 0777, true);
            @mkdir(_PS_ALL_THEMES_DIR_.$this->theme_name.'/img/modules/'.$this->name.'/', 0777, true);
            @mkdir($this->img_path, 0777, true);
        }
    }
}
