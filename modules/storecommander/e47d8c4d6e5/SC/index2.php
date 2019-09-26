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

$sc_alerts= array();

require_once('init_sc.php');

$ajax = Tools::getValue('ajax', 0);


// mode ajax
if ($ajax)
{
    if (SC_TOOLS) {
        foreach ($sc_tools_list AS $tool) {
            if (file_exists(SC_TOOLS_DIR . $tool . '/hookStartAction.php')) {
                require_once(SC_TOOLS_DIR . $tool . '/hookStartAction.php');
            }
        }
    }
    $action = Tools::getValue('act');
    $panel = Tools::getValue('p');
    $xml = Tools::getValue('x');
    if ($xml) {
        $action = $xml;
    }
    if ($panel) {
        $action = $panel;
    }
    if ($action) {

        // DHTMLX4 compatibility
        if (strpos($action, '_update') !== false && isset($_POST['ids'])) {
            $str = str_replace('.', '_', $_POST['ids']) . '_';
            foreach ($_POST as $k => $val) {
                if (strpos($k, $str) !== false) {
                    $_POST[substr($k, strlen($str), 1000)] = $val;
                }
            }
        }

        if ($ajax == 1) {
            $act = explode('_', $action);
            $overridePrefix = '';
            if (file_exists(SC_TOOLS_DIR . 'lib/' . $act[0] . '/' . $act[1])) {
                $overridePrefix = SC_TOOLS_DIR;
            }
            ob_start("cleanXMLContent");
            if (file_exists($overridePrefix . 'lib/' . $act[0] . '/' . $act[1] . '/' . $act[0] . '_' . $act[1] . '_tools.php')) {
                require_once($overridePrefix . 'lib/' . $act[0] . '/' . $act[1] . '/' . $act[0] . '_' . $act[1] . '_tools.php');
            }
            if (file_exists($overridePrefix . 'lib/' . $act[0] . '/' . $act[1] . '/' . $action . '.php')) {
                require_once($overridePrefix . 'lib/' . $act[0] . '/' . $act[1] . '/' . $action . '.php');
            } elseif (file_exists($overridePrefix . 'lib/' . $act[0] . '/' . $action . '.php')) {
                require_once($overridePrefix . 'lib/' . $act[0] . '/' . $action . '.php');
            } elseif (file_exists($overridePrefix . 'lib/' . $act[0] . '/' . $act[1] . '/' . $act[2] . '/' . $action . '.php')) {
                require_once($overridePrefix . 'lib/' . $act[0] . '/' . $act[1] . '/' . $act[2] . '/' . $action . '.php');
            }
            ob_end_flush();
        } elseif ($ajax == 2) {
            require_once(SC_TOOLS_DIR . $action . '.php');
        }
        exit();
    }
    if ($panel) { // useless but kept for old extension compatibility
        if ($ajax == 1) {
            require_once('lib/panel/' . $panel . '.php');
        } elseif ($ajax == 2) {
            require_once(SC_TOOLS_DIR . $panel . '.php');
        }
        exit();
    }
    if ($xml) { // useless but kept for old extension compatibility
        if ($ajax == 1) {
            require_once('lib/xml/' . $xml . '.php');
        } elseif ($ajax == 2) {
            require_once(SC_TOOLS_DIR . $xml . '.php');
        }
        exit();
    }

}else{


$sql = "SELECT COUNT(*) FROM " . _DB_PREFIX_ . "product";
$SC_SHOP_PRODUCTSCOUNT = Db::getInstance()->getValue($sql);

// mode classic
checkDB();
require_once(SC_DIR . 'lib/php/maintenance.php');
runMaintenance();

require_once(SC_DIR . 'lib/php/menu.php');
$page = Tools::getValue('page', 'cat_tree');
$title ='';

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <?php
        switch ($page){
            case 'cat_tree':
                $title = 'Catalog';
                break;
            case 'cus_tree':
                $title ='Customer';
                break;
            case 'ord_tree':
                $title= 'Order';
                break;
            case 'cusm_tree':
                $title = 'Support';
                break;
            case 'cms_tree':
                $title = 'CMS';
                break;
            case 'man_tree':
                $title = 'Manufacturers';
                break;
            default:
                $title = 'Store Commander';
                break;
        }
    ?>
    <title>
        <?php echo _l($title); ?>
    </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="icon" href="lib/img/sc_only_red.png" />
    <link type="text/css" rel="stylesheet" href="<?php echo SC_CSSDHTMLX; ?>"/>
    <link type="text/css" rel="stylesheet" href="<?php echo SC_CSSSTYLE; ?>"/>
    <link type="text/css" rel="stylesheet" href="lib/js/skins/message_default.css"/>
    <link type="text/css" rel="stylesheet" href="lib/css/jquery.autocomplete.css"/>
    <script type="text/javascript" src="<?php echo SC_JSDHTMLX; ?>"></script>
    <script type="text/javascript" src="lib/js/message.js"></script>
    <script type="text/javascript" src="<?php echo SC_JQUERY; ?>"></script>
    <script type="text/javascript" src="lib/js/jquery.cokie.js"></script>
    <script type="text/javascript" src="<?php echo SC_JSFUNCTIONS; ?>"></script>
    <script type="text/javascript" src="lib/js/jquery.autocomplete.js"></script>
    <link rel="icon" type="image/vnd.microsoft.icon" href="/img/favicon.ico"/>
    <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico"/>
</head>
<body>
<script type="text/javascript">
    var isIPAD = navigator.userAgent.match(/iPad/i) != null;
    var mustOpenBrowserTab = isIPAD || <?php echo (_s('APP_FORCE_OPEN_BROWSER_TAB')?1:0); ?>;
    SC_ID_LANG =<?php echo $sc_agent->id_lang;?>;
    SC_PAGE = '<?php echo $page;?>';
    SCMS =<?php echo (SCMS?1:0);?>;
    var dhxWins = new dhtmlXWindows();
    dhtmlXWindowsCell.prototype.setIcon = function (icon) {
        this.wins.w[this._idd].hdr.firstChild.style.backgroundImage = "url(" + icon + ")";
        this.wins._winAdjustTitle(this._idd);
    };
    $(document).ready(function () {

        <?php
                if (defined('_PS_CACHE_ENABLED_') && _PS_CACHE_ENABLED_)
                {
        ?>
        dhtmlx.message({
            text: '<?php echo addslashes(_l('Note: Using the Prestashop cache system may interfere with the proper use of Store Commander as well as modules. To prevent this, go to the Preferences > Performance tab to disable the option at the end of the page.'));?>',
            type: 'info',
            expire: 12000
        });
        <?php
                }
        ?>
        document.onselectstart = new Function("return false;");
    });

    <?php
        UISettings::loadJS($page);
    ?>

    var layoutStatusText = "";
    var updateQueueLimit = '<?php
	$nb = _s("APP_UPDATEQUEUE_LIMIT");
	if(empty($nb))
		{$nb = 20;}
	echo $nb;
	?>';
    var lang_confirmclose = '<?php echo _l('Multiple actions are currently running. Do you really want to close?',1)?>';
    var lang_queuetasks = '<?php echo _l('Tasks',1)?>';
    var lang_queuetaskswindow = '<?php echo _l('Tasks error logs',1)?>';
    var lang_queueerror_1 = '<?php echo _l('An error has occured:',1)?>';
    var lang_queueerror_2 = '<?php echo _l('Check the logs to see the modification that triggered the error as well as other that could not be applied',1)?>';
    var lang_queueerror_3 = '<?php echo _l('See logs',1)?>';
    var lang_queueerror_4 = '<?php echo _l('An error has occured when inserting',1)?>';
</script>
<?php

switch ($page) {
    case 'cat_tree':
        include_once('lib/cat/cat_tree.php');
        require_once("lib/cat/cat_grid.php");
        require_once('lib/cat/cat_prop.php');
        require_once('lib/cat/quicksearch/cat_quicksearch.php');
        break;
    case 'ord_tree':
        include_once('lib/ord/ord_tree.php');
        require_once("lib/ord/ord_grid.php");
        require_once('lib/ord/ord_prop.php');
        break;
    case 'cus_tree':
        include_once('lib/cus/cus_tree.php');
        require_once("lib/cus/cus_grid.php");
        require_once('lib/cus/cus_prop.php');
        break;
    case 'cusm_tree':
        include_once('lib/cusm/cusm_tree.php');
        require_once("lib/cusm/cusm_grid.php");
        require_once('lib/cusm/cusm_prop.php');
        break;
    case 'cms_tree':
        include_once('lib/cms/cms_tree.php');
        require_once("lib/cms/cms_grid.php");
        require_once('lib/cms/cms_prop.php');
        break;
    case 'man_tree':
        include_once('lib/man/man_tree.php');
        require_once("lib/man/man_grid.php");
        require_once('lib/man/man_prop.php');
        break;
}

require_once('lib/all/win-quickexport/all_win-quickexport_init.php');
require_once('lib/all/win-help/all_win-help_init.php');

?>
<div id="jsExecute"></div>
<?php

require_once('lib/core/core_tips.php');

if(KAI9DF4==1)
    require_once('lib/core/core_trialtime.php');

if(!empty($sc_alerts) && count($sc_alerts)>0) { ?>
<script type="application/javascript">
    $( document ).ready(function() {
        dhtmlx.message({text:'<?php echo str_replace("'", "\'", implode("<br/><br/>", $sc_alerts)); ?>',type:'error',expire:-1});
    });
</script>

<?php }

$sql = "SELECT count(pm.id_module) 
        FROM " . _DB_PREFIX_ . "module AS pm INNER JOIN " . _DB_PREFIX_ . "hook_module AS hm ON (pm.id_module = hm.id_module) INNER JOIN " . _DB_PREFIX_ . "hook AS h ON (hm.id_hook = h.id_hook)
        WHERE h.name = 'actionProductUpdate' AND pm.active = 1 AND (LOWER(pm.name) = 'advsearch' OR LOWER(pm.name) = 'shoppingflux' OR LOWER(pm.name) = 'ebay' OR LOWER(pm.name) = 'mailchimpintegration') ";
$res = Db::getInstance()->getValue($sql);

if (_s('APP_FOULEFACTORY') && SCI::getFFActive()) { ?>
    <script type="application/javascript">
        function showWCatFoulefactory()
        {
            if (!dhxWins.isWindow('wCatFoulefactory'))
            {
                wCatFoulefactory = dhxWins.createWindow('wCatFoulefactory', 50, 50, 940, $(window).height()-75);
                wCatFoulefactory.setIcon('lib/img/foulefactory_icon.png','../../../lib/img/foulefactory_icon.png');
                wCatFoulefactory.setText('<?php echo _l('FouleFactory', 1); ?>'); //  and cancel modifications
                $.get('index.php?ajax=1&act=cat_win-foulefactory_init&id_lang='+SC_ID_LANG,function(data){
                    $('#jsExecute').html(data);
                });
            }
            else
            {
                wCatFoulefactory.show();
                $.get('index.php?ajax=1&act=cat_win-foulefactory_init&id_lang='+SC_ID_LANG,function(data){
                    $('#jsExecute').html(data);
                });
            }
        }
    </script>
<?php }

if (_s('CAT_NOTICE_HOOKACTIONPRODUCTUPDATE') && $res >= 1)
{

    ?>
    <script>
        dhtmlx.message
        ({
            type: 'error',
            text: ('<?php echo _l('Some modules installed on your shop are misconfigured which affects Store Commander performances.',1) .
                ' ' . '<a href="http://www.storecommander.com/support/'.($user_lang_iso=='fr'?'fr/problemes-connus-et-solutions/784-probleme-les-modifications-de-produits-ne-sont-pas-validees.html':'en/known-issues-amp-solutions/784-problem-modifications-are-not-being-applied.html').'" target="_blank">' . _l('Read more',1) . '</a>. ' . _l('To stop this alert: SC  > Tools > Settings > Section: Notice.',1) ?>'),
            expire: 10000
        });

    </script>
    <?php   
}
if (_s('CAT_NOTICE_PSVERSIONUPDATE') && (version_compare(_PS_VERSION_, '1.7.1.0', '>=') && version_compare(_PS_VERSION_, '1.7.2.3', '<=')))
{
    ?>
    <script>
        dhtmlx.message
        ({
            type: 'error',
            text: ('<?php echo _l('Your Prestashop version need to be uptaded. We strongly advise you to upgrade.',1) .
                ' ' . '<a href="http://build.prestashop.com/news/prestashop-1-7-2-4-maintenance-release/" target="_blank">' . _l('Read more',1) . '</a>. ' . _l('To stop this alert: SC  > Tools > Settings > Section: Notice.',1) ?>'),
            expire: 10000
        });

    </script>
    <?php
}
include('lib/all/win-trends/all_win-trends_loop.php');
?>
</body>
</html>
<?php
} // end mode classic
