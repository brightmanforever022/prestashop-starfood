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
?>
<script type="text/javascript">

    // Create interface
    var dhxLayout = new dhtmlXLayoutObject(document.body, "2U");
    dhxLayout.cells('a').setText('<?php echo _l('Catalog',1).' '.addslashes(Configuration::get('PS_SHOP_NAME'));?>');
    dhxLayout.cells('b').setText('<?php echo _l('Properties',1)?>');
    var start_cms_size_prop = getParamUISettings('start_cms_size_prop');
    if(start_cms_size_prop==null || start_cms_size_prop<=0 || start_cms_size_prop=="")
        start_cms_size_prop = 400;
    dhxLayout.cells('b').setWidth(start_cms_size_prop);
    dhxLayout.attachEvent("onPanelResizeFinish", function(){
        saveParamUISettings('start_cms_size_prop', dhxLayout.cells('b').getWidth())
    });
    var dhxLayoutStatus = dhxLayout.attachStatusBar();
    dhxLayoutStatus.setText('<div id="layoutstatusqueue" style="float: right; color: #ff0000; font-weight: bold;"><img src="lib/img/ajax-loader16.gif" style="height: 10px;" /> <span></span></div>'+"<?php echo SC_COPYRIGHT.' '.(SC_DEMO?'- Demonstration':'- '._l('License').' '.SCLIMREF.' - '.$SC_SHOP_cms_pageCOUNT.' '._l('cms_page')).' - Version '.SC_VERSION.(SC_BETA?' BETA':'').(SC_GRIDSEDITOR_INSTALLED?' GE'.(SC_GRIDSEDITOR_PRO_INSTALLED?'P':''):'').' (PS '._PS_VERSION_.(defined('_PS_CACHE_ENABLED_') && _PS_CACHE_ENABLED_ ? ' (cache)':'').' - PHP '.phpversion().') <span id=\"layoutstatusloadingtime\"></span>';?>");
    layoutStatusText = '<div id="layoutstatusqueue" style="float: right; color: #ff0000; font-weight: bold;"><img src="lib/img/ajax-loader16.gif" style="height: 10px;" /> <span></span></div>'+"<?php echo SC_COPYRIGHT.' '.(SC_DEMO?'- Demonstration':'- '._l('License').' '.SCLIMREF.' - '.$SC_SHOP_cms_pageCOUNT.' '._l('cms_page')).' - Version '.SC_VERSION.(SC_BETA?' BETA':'').(SC_GRIDSEDITOR_INSTALLED?' GE'.(SC_GRIDSEDITOR_PRO_INSTALLED?'P':''):'').' (PS '._PS_VERSION_.(defined('_PS_CACHE_ENABLED_') && _PS_CACHE_ENABLED_ ? ' (cache)':'').' - PHP '.phpversion().') <span id=\"layoutstatusloadingtime\"></span>';?>";

    <?php createMenu(); ?>
    cmsselection=0;
    shopselection=$.cookie('sc_shop_selected')*1;
    shop_list=$.cookie('sc_shop_list');
    lastcms_pageID=0;
    propertiesPanel='<?php echo _s('CMS_PAGEPROP_GRID_DEFAULT');?>';
    tree_mode='single';
    displayCmsFrom='all';
    copytocateg=false;
    dragdropcache='';
    draggedCmsPage=0;
    clipboardValue=null;
    clipboardType=null;

    <?php	//#####################################
    //############ Categories toolbar
    //#####################################
    ?>

    gridView='<?php echo _s('CMS_PAGE_GRID_DEFAULT')?>';
    oldGridView='';
    <?php
    if (SCMS)
    {
    ?>
    // Url array
    var shopUrls = new Array();
    <?php
    $sql_shop ="SELECT id_shop
					FROM "._DB_PREFIX_."shop
					WHERE deleted != '1'";
    $shops = Db::getInstance()->ExecuteS($sql_shop);
    if (version_compare(_PS_VERSION_, '1.5.0.2', '>=')) {
        $protocol = Tools::getShopProtocol();
    } else {
        $protocol = 'http://';
    }

    foreach($shops as $shop)
    {
        $url = Db::getInstance()->ExecuteS('SELECT *, CONCAT("'.$protocol.'", domain, physical_uri, virtual_uri) AS url
				FROM '._DB_PREFIX_.'shop_url
				WHERE id_shop = '.(int)$shop["id_shop"].'
					AND active = "1"
				ORDER BY main DESC
				LIMIT 1');
        if(!empty($url[0]["url"]))
        {
            echo 'shopUrls['.$shop["id_shop"].'] = "'.$url[0]["url"].'";'."\n";
        }
    }
    ?>
    // End url array

    cms = new dhtmlXLayoutObject(dhxLayout.cells("a"), "2U");

    <?php if(SCAS) { ?>
    cms_firstcolcontent = cms.cells("a").attachLayout("2E");

    cms_storePanel = cms_firstcolcontent.cells('a');
    cms_categoryPanel = cms_firstcolcontent.cells('b');
    <?php } else { ?>
    cms_firstcolcontent = cms.cells("a").attachLayout("2E");

    cms_storePanel = cms_firstcolcontent.cells('a');
    cms_categoryPanel = cms_firstcolcontent.cells('b');
    <?php } ?>

    cms_pagePanel = cms.cells('b');


    <?php	//#####################################
    //############ Boutiques Tree
    //#####################################
    ?>
    var has_shop_restrictions = false;

    cms.cells("a").setText('<?php echo _l('Stores',1)?>');
    cms.cells("a").showHeader();
    cms_storePanel.hideHeader();
    var start_cms_size_store = getParamUISettings('start_cms_size_store');
    if(start_cms_size_store==null || start_cms_size_store<=0 || start_cms_size_store=="")
        start_cms_size_store = 150;
    cms_storePanel.setHeight(start_cms_size_store);
    cms_firstcolcontent.attachEvent("onPanelResizeFinish", function(names){
        $.each(names, function(num, name){
            if(name=="a")
                saveParamUISettings('start_cms_size_store', cms_storePanel.getHeight())
        });
    });
    cms_shoptree=cms_storePanel.attachTree();
    cms_shoptree._name='shoptree';
    cms_shoptree.autoScroll=false;
    cms_shoptree.setImagePath('lib/js/imgs/');
    cms_shoptree.enableSmartXMLParsing(true);
    cms_shoptree.enableCheckBoxes(true, false);

    var cmsShoptreeTB = cms_storePanel.attachToolbar();
    cmsShoptreeTB.addButton("help", 0, "", "lib/img/help.png", "lib/img/help.png");
    cmsShoptreeTB.setItemToolTip('help','<?php echo _l('Help')?>');
    cmsShoptreeTB.attachEvent("onClick", function(id) {
        if (id=='help')
        {
            var display = "";
            var update = "";
            if(shopselection>0)
            {
                display = cms_shoptree.getItemText(shopselection);
            }
            else if(shopselection==0)
            {
                display = cms_shoptree.getItemText("all");
            }

            var all_checked = $.cookie('sc_shop_list').split(",");
            $.each(all_checked, function(index, id) {
                if(id!="all" && id.search("G")<0)
                {
                    if(update!="")
                        update += ", ";
                    update += cms_shoptree.getItemText(id);
                }
            });

            var msg = '<strong><?php echo addslashes(_l('Display:'));?></strong> '+display+'<br/><br/><strong><?php echo addslashes(_l('Update:'));?></strong> '+update;
            dhtmlx.message({text:msg,type:'info',expire:10000});
        }
    });


    displayShopTree();
    function checkWhenSelection(idshop)
    {
        if ((idshop == 'all' || idshop==0) && has_shop_restrictions==0)
        {
            var children = cms_shoptree.getAllSubItems("all").split(",");
            cms_shoptree.setCheck("all",1);
            $.each(children, function(index, id) {
                cms_shoptree.setCheck(id,1);
                cms_shoptree.disableCheckbox(id,1);
            });
        }
        else
        {
            if(has_shop_restrictions==0)
            {
                var children = cms_shoptree.getAllSubItems("all").split(",");
                $.each(children, function(index, id) {
                    cms_shoptree.disableCheckbox(id,0);
                });
            }
            else
            {
                var children = cms_shoptree.getAllSubItems(0).split(",");
                $.each(children, function(index, id) {
                    cms_shoptree.disableCheckbox(id,0);
                });
            }
            if(idshop>0)
            {
                cms_shoptree.setCheck(idshop,1);
                cms_shoptree.disableCheckbox(idshop,1);
            }
        }
    }
    function deSelectParents(idshop)
    {
        if(cms_shoptree.getParentId(idshop)!="")
        {
            var parent_id = cms_shoptree.getParentId(idshop);
            cms_shoptree.setCheck(parent_id,0);

            deSelectParents(parent_id);
        }
    }
    function saveCheckSelection()
    {
        var checked = cms_shoptree.getAllChecked();
        if(shopselection=="all" || shopselection=="0")
        {
            checked = cms_shoptree.getAllSubItems("all");
        }
        var all_checked = checked.split(",");
        var cookie_checked = "";
        $.each(all_checked, function(index, id) {
            if(id!="all" && id.search("G")<0)
            {
                if(cookie_checked!="")
                    cookie_checked += ",";
                cookie_checked += id;
            }
        });
        if(shopselection!=undefined && shopselection!="")
        {
            if(cookie_checked!="")
                cookie_checked += ",";
            cookie_checked += shopselection;
        }
        $.cookie('sc_shop_list',cookie_checked, { expires: 60 });
    }
    function displayShopTree(callback) {
        cms_shoptree.deleteChildItems(0);
        cms_shoptree.loadXML("index.php?ajax=1&act=cms_shop_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
            has_shop_restrictions = cms_shoptree.getUserData(0, "has_shop_restrictions");

            if(shopselection!=null && shopselection!=undefined)
                checkWhenSelection(shopselection);
            if(shop_list!=null && shop_list!="")
            {
                var selected = shop_list.split(",");
                $.each(selected, function(index, id) {
                    cms_shoptree.setCheck(id,1);
                });
            }
            if (shopselection!=null && shopselection!=undefined && shopselection!=0)
            {
                cms_shoptree.openItem(shopselection);
                cms_shoptree.selectItem(shopselection,true);
            }

            if(has_shop_restrictions)
            {
                selected = cms_shoptree.getSelectedItemId();
                if(selected==undefined || selected==null || selected=="")
                {
                    var all = cms_shoptree.getAllSubItems(0);
                    if(all!=undefined && all!=null && all!="")
                    {
                        all = all.split(",");
                        var id_to_select = "";
                        $.each(all, function(index, id) {
                            if(id.search("G")<0)
                            {
                                if(id_to_select=="")
                                    id_to_select = id;
                            }
                        });
                        shopselection = id_to_select;
                        cms_shoptree.openItem(shopselection);
                        cms_shoptree.selectItem(shopselection,true);
                        $.cookie('sc_shop_selected',shopselection, { expires: 60 });
                    }
                }
            }

            if (callback!='') eval(callback);
            cms_shoptree.openAllItems(0);
        });
    }
    cms_shoptree.attachEvent("onClick",onClickShopTree);
    function onClickShopTree(idshop, param,callback){
        if (idshop[0]=='G'){
            cms_shoptree.clearSelection();
            cms_shoptree.selectItem(shopselection,false);
            return false;
        }
        if (idshop == 'all'){
            idshop = 0;
        }
        checkWhenSelection(idshop);
        if (idshop != shopselection)
        {
            if(shopselection!=0 && idshop!=0 && idshop[0]!='G')
                cms_shoptree.setCheck(shopselection,0);
            else if(shopselection==0 && idshop!=0 && idshop[0]!='G')
            {
                if(has_shop_restrictions==0)
                {
                    var children = cms_shoptree.getAllSubItems("all").split(",");
                    cms_shoptree.setCheck("all",0);
                    $.each(children, function(index, id) {
                        if(id!=idshop)
                            cms_shoptree.setCheck(id,0);
                    });
                }
                else
                {
                    var children = cms_shoptree.getAllSubItems(0).split(",");
                    cms_shoptree.setCheck("all",0);
                    $.each(children, function(index, id) {
                        if(id!=idshop)
                            cms_shoptree.setCheck(id,0);
                    });
                }

            }
            shopselection = idshop;
            $.cookie('sc_shop_selected',shopselection, { expires: 60 });
            cms_categoryPanel.setText('<?php echo _l('Categories',1).' '._l('of',1)?> '+cms_shoptree.getItemText(shopselection));
            displayTree(callback_refresh);
        }
        else
        {
            var callback_refresh = "";
            if(callback!=undefined && callback!=null && callback!="")
                callback_refresh = callback_refresh + callback;

            displayTree(callback_refresh);
        }
        saveCheckSelection();
    }

    cms_shoptree.attachEvent("onCheck",function(idshop, state){
        if(idshop=="all")
        {
            var children = cms_shoptree.getAllSubItems("all").split(",");
            $.each(children, function(index, id) {
                cms_shoptree.setCheck(id,state);
            });
        }
        else if(idshop.search("G")>=0)
        {
            var children = cms_shoptree.getAllSubItems(idshop).split(",");
            $.each(children, function(index, id) {
                cms_shoptree.setCheck(id,state);
            });
        }
        else
        {
            deSelectParents(idshop);
        }
        saveCheckSelection();
    });




    <?php	//#####################################
    //############ Context menu
    //#####################################
    ?>
    var drag_disabled_for_sort = true; // for disable the drag  in tree after  sort and  "sort and save"
    cms_shop_cmenu_tree=new dhtmlXMenuObject();
    cms_shop_cmenu_tree.renderAsContextMenu();
    function onTreeContextButtonClickForShop(itemId){
        if (itemId=="goshop"){
            tabId=cms_shoptree.contextID;
            var cmsCatActive=(cms_shoptree.getItemImage(tabId,0,false)=='catalog.png'?0:1);
            if (cmsCatActive==1){
                return false;
            }
            <?php
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                if(SCMS) {
                ?>
                    if(shopUrls[tabId] != undefined && shopUrls[tabId] != "" && shopUrls[tabId] != null)
                        window.open(shopUrls[tabId]);
                <?php
                } else { ?>
                    window.open('<?php echo SC_PS_PATH_REL;?>');
                <?php }
            } else {
            ?>
                window.open('<?php echo SC_PS_PATH_REL;?>');
            <?php
            }
            ?>
        }
    }
    cms_shop_cmenu_tree.attachEvent("onClick", onTreeContextButtonClickForShop);
    var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
        '<item text="Object" id="object" enabled="false"/>'+
        '<item text="<?php echo _l('See shop')?>" id="goshop"/>'+
        '</menu>';
    cms_shop_cmenu_tree.loadStruct(contextMenuXML);
    cms_shoptree.enableContextMenu(cms_shop_cmenu_tree);

    cms_shoptree.attachEvent("onBeforeContextMenu", function(itemId){

        var display_id = itemId;
        var display_text = '<?php echo _l('Shop:')?> ';
        if(itemId=="all")
        {
            return false;
        }
        else if(itemId.search("G")>=0)
        {
            var display_id = itemId.replace("G","");
            var display_text = '';
        }

        cms_shop_cmenu_tree.setItemText('object', 'ID'+display_id+': '+display_text+cms_shoptree.getItemText(itemId));

        <?php if(SCMS) { ?>
        if(shopUrls[itemId] != undefined && shopUrls[itemId] != "" && shopUrls[itemId] != null)
        {
            cms_shop_cmenu_tree.setItemEnabled('goshop');
        }else{
            cms_shop_cmenu_tree.setItemDisabled('goshop');
        }
        <?php } ?>

        return true;
    });
    <?php
    }else{
    ?>
    cms = new dhtmlXLayoutObject(dhxLayout.cells("a"), "2U");
    cms_firstcolcontent = cms_categoryPanel = cms.cells('a');
    cms_pagePanel = cms.cells('b');
    <?php
    } ?>

    /* CATEGORIES */
    var start_cms_size_tree = getParamUISettings('start_cms_size_tree');
    if(start_cms_size_tree==null || start_cms_size_tree<=0 || start_cms_size_tree=="")
        start_cms_size_tree = 250;
    <?php if(SCMS) { ?>
    cms.cells("a").setWidth(start_cms_size_tree);
    cms.attachEvent("onPanelResizeFinish", function(){
        saveParamUISettings('start_cms_size_tree', cms.cells("a").getWidth())
    });
    <?php } else { ?>
    cms_categoryPanel.setWidth(start_cms_size_tree);
    cms.attachEvent("onPanelResizeFinish", function(){
        saveParamUISettings('start_cms_size_tree',cms_categoryPanel.getWidth())
    });
    <?php } ?>
    cms_tb=cms_categoryPanel.attachToolbar();
    cms_tb.addButton("help", 0, "", "lib/img/help.png", "lib/img/help.png");
    cms_tb.setItemToolTip('help','<?php echo _l('Help',1)?>');
    <?php if(_r("ACT_CMS_EMPTY_RECYCLE_BIN")) { ?>
    cms_tb.addButton("bin", 0, "", "lib/img/folder_delete.png", "lib/img/folder_delete.png");
    cms_tb.setItemToolTip('bin','<?php echo _l('Empty bin',1)?>');
    <?php } ?>
    <?php if(_r("ACT_CMS_ADD_CATEGORY")) { ?>
    cms_tb.addButton("add_ps", 0, "", "lib/img/add_ps.png", "lib/img/add_ps.png");
    cms_tb.setItemToolTip('add_ps','<?php echo _l('Create new category with the PrestaShop form',1)?>');
    cms_tb.addButton("add", 0, "", "lib/img/add.png", "lib/img/add.png");
    cms_tb.setItemToolTip('add','<?php echo _l('Create new category',1)?>');
    <?php } ?>
    <?php if (version_compare(_PS_VERSION_, '1.4.0.0', '>=')) { ?>
    cms_tb.addButton("cms_CmsCatManagement", 0, "", "lib/img/folder_wrench.png", "lib/img/folder_wrench.png");
    cms_tb.setItemToolTip('cms_CmsCatManagement','<?php echo _l('CMS categories management',1)?>');
    <?php } ?>
    cms_tb.addButtonTwoState("withSubCateg", 0, "", "lib/img/chart_organisation_add.png", "lib/img/chart_organisation_add.png");
    cms_tb.setItemToolTip('withSubCateg','<?php echo _l('If enabled: display cms_page from all subcategories',1)?>');
    cms_tb.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
    cms_tb.setItemToolTip('refresh','<?php echo _l('Refresh tree',1)?>');
    cms_tb.attachEvent("onClick",
        function(id){
            if (id=='help'){
                <?php echo "window.open('".getHelpLink('cms_toolbar_cat')."');"; ?>
            }
            if (id=='refresh'){
                displayTree();
            }
            if (id=='bin'){
                if (confirm('<?php echo _l('Are you sure to delete all CMS categories and CMS pages placed in the recycled bin?',1)?>'))
                {
                    var id_bin=cms_tree.findItemIdByLabel('<?php echo _l('SC Recycle Bin')?>',0,1);
                    if (id_bin==null)
                        id_bin=cms_tree.findItemIdByLabel('SC Recycle Bin',0,1);
                    if (id_bin!=null)
                        $.get("index.php?ajax=1&act=cms_category_update&action=emptybin&id_cms_category="+id_bin+'&id_lang='+SC_ID_LANG,function(id){
                            lastcms_pageID=0;
                            childlist=cms_tree.getAllSubItems(id_bin).split(',');
                            displayTree();
                            if (cmsselection==id_bin || in_array(cmsselection,childlist))
                            {
                                lastcms_pageID=0;
                                cms_grid.clearAll();
                                cms_grid_sb.setText('');
                            }
                        });
                }
            }
            if (id=='add'){
                if (cmsselection!=0)
                {
                    var cname=prompt('<?php echo _l('Create a CMS category:',1)?>');
                    if (cname!=null)
                        $.post("index.php?ajax=1&act=cms_category_update&action=insert&id_parent="+cmsselection+'&id_lang='+SC_ID_LANG,{name: (cname)},function(id){
                            cms_tree.insertNewChild(cmsselection,id,cname,0,'../../img/folder_grey.png','../../img/folder_grey.png','../../img/folder_grey.png');
                        });
                }else{
                    alert('<?php echo _l('You need to select a parent category before creating a category',1)?>');
                }
            }
            if (id=='cms_CmsCatManagement'){
                if (!dhxWins.isWindow("wCmsCatManagement"))
                {
                    wCmsCatManagement = dhxWins.createWindow("wCmsCatManagement", 0, 28, $(window).width(), $(window).height()-28);
                    wCmsCatManagement.setIcon('lib/img/folder_wrench.png','../../../lib/img/folder_wrench.png');
                    wCmsCatManagement.setText('<?php echo _l('CMS categories management',1)?>');
                    $.get("index.php?ajax=1&act=cms_win-cmscatmanagement_init",function(data){
                        $('#jsExecute').html(data);
                    });
                    wCmsCatManagement.attachEvent("onClose", function(win){
                        wCmsCatManagement.hide();
                        return false;
                    });
                }else{
                    $.get("index.php?ajax=1&act=cms_win-cmscatmanagement_init",function(data){
                        $('#jsExecute').html(data);
                    });
                    wCmsCatManagement.show();
                }
            }
            if (id=='add_ps'){
                if (!dhxWins.isWindow("wNewCmsCategory"))
                {
                    wNewCmsCategory = dhxWins.createWindow("wNewCmsCategory", 50, 50, 1260, $(window).height()-75);
                    wNewCmsCategory.setText('<?php echo _l('Create the new CMS category and close this window to refresh the tree',1)?>');
                    <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')){ ?>
                    wNewCmsCategory.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?controller=AdminCmsContent&addcms_category&id_parent="+cmsselection+"&token=<?php echo $sc_agent->getPSToken('AdminCmsContent');?>");
                    <?php }else{ ?>
                    wNewCmsCategory.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=admincmscontent&addcms_category&id_parent="+cmsselection+"&token=<?php echo $sc_agent->getPSToken('AdminCmsContent');?>");
                    <?php } ?>
                    wNewCmsCategory.attachEvent("onClose", function(win){
                        displayTree();
                        return true;
                    });
                }
            }
        }
    );
    cms_tb.attachEvent("onStateChange", function(id,state){
        if (id=='withSubCateg'){
            if (state) {
                tree_mode='all';
                cms_grid_tb.disableItem('setposition');
            }else{
                tree_mode='single';
                cms_grid_tb.enableItem('setposition');
            }
            displayCms();
        }
        if (id=='fromIDCategDefault'){
            if (state) {
                displayCmsFrom='default';
            }else{
                displayCmsFrom='all';
            }
            displayCms();
        }
    });
    $(document).ready(function(){
        if (<?php echo Tools::getValue('displayAllcms_page',0);?>)
            onMenuClick('cms_grid','','');
    });

    <?php	//#####################################
    //############ cms_tree
    //#####################################
    ?>

    cms_tree=cms_categoryPanel.attachTree();
    cms_tree._name='tree';
    cms_categoryPanel.setText('<?php echo _l('Categories',1)?>');
    cms_pagePanel.setText('<?php echo _l('cms_page',1)?>');
    cms_tree.autoScroll=false;
    cms_tree.setImagePath('lib/js/imgs/');
    cms_tree.enableSmartXMLParsing(true);
    <?php if(!SCSG && !_r("ACT_CMS_MOVE_CATEGORY") && !_r("ACT_CMS_MOVE_PAGES_IN_CATEGORY")) { ?>
    cms_tree.enableDragAndDrop(false);
    <?php } else { ?>
    cms_tree.enableDragAndDrop(true);
    <?php } ?>
    cms_tree.setDragBehavior("complex");
    cms_tree._dragBehavior="complex";


    function nameSortCmsCatTree(idA,idB)
    {
        var a = latinise(cms_tree.getItemText(idA)).toLowerCase();
        var b = latinise(cms_tree.getItemText(idB)).toLowerCase();
        if ( a < b )
            return -1;
        if ( a > b )
            return 1;
        return 0;
    }

    <?php if (!SCMS){ ?>
    displayTree();
    <?php } ?>


    <?php	//#####################################
    //############ Context menu
    //#####################################
    ?>
    cms_cmenu_tree=new dhtmlXMenuObject();
    cms_cmenu_tree.renderAsContextMenu();
    function onTreeContextButtonClick(itemId){
        if (itemId=="gopsbo"){
            tabId=cms_tree.contextID;
            wModifyCategory = dhxWins.createWindow("wModifyCategory", 50, 50, 1260, $(window).height()-75);
            wModifyCategory.setText('<?php echo _l('Modify the category and close this window to refresh the tree',1)?>');
            wModifyCategory.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=<?php echo (version_compare(_PS_VERSION_, '1.5.0.0', '>=')?'admincmscontent':'AdminCmsContent');?>&updatecms_category&id_cms_category="+tabId+"&id_lang="+SC_ID_LANG+"&adminlang=1&token=<?php echo $sc_agent->getPSToken('AdminCmsContent');?>");
            wModifyCategory.attachEvent("onClose", function(win){
                displayTree();
                return true;
            });
        }
        if (itemId=="goshop"){
            tabId=cms_tree.contextID;
            var cmsCatActive=(cms_tree.getItemImage(tabId,0,false)=='catalog.png'?0:1);
            if (cmsCatActive==1){
                return false;
            }
            <?php
            if (version_compare(_PS_VERSION_, '1.6.0.12', '>='))
            {
                if(SCMS) {
                ?>
                    if(shopUrls[shopselection] != undefined && shopUrls[shopselection] != "" && shopUrls[shopselection] != null)
                        window.open(shopUrls[shopselection]+'index.php?id_cms_category='+tabId+'&controller=cms&id_lang='+SC_ID_LANG);
                    <?php
                } else { ?>
                    window.open('<?php echo SC_PS_PATH_REL;?>index.php?id_cms_category='+tabId+'&controller=cms&id_lang='+SC_ID_LANG);
                <?php }
            }else if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
            ?>
                window.open('<?php echo SC_PS_PATH_REL;?>index.php?id_cms_category='+tabId+'&controller=cms&id_lang='+SC_ID_LANG);
            <?php
            } else {
            ?>
                window.open('<?php echo SC_PS_PATH_REL;?>cms.php?id_cms_category='+tabId);
            <?php
            }
            ?>
        }
        if (itemId=="expand"){
            tabId=cms_tree.contextID;
            cms_tree.openAllItems(tabId);
        }
        if (itemId=="collapse"){
            tabId=cms_tree.contextID;
            cms_tree.closeAllItems(tabId);
            if (tabId==1) cms_tree.openItem(1);
        }
        if (itemId=="sort"){
            drag_disabled_for_sort = false;
            tabId=cms_tree.contextID;
            cms_tree.setCustomSortFunction(nameSortCmsCatTree);
            cms_tree.sortTree(tabId,'ASC',1);
            dhtmlx.message({text:'<?php echo addslashes(_l('CMS Category sorted, click on the Refresh icon to allow reorder (drag and drop) on the categories tree.'));?>',type:'info',expire:5000});
        }
        if (itemId=="sort_and_save"){
            drag_disabled_for_sort = false;
            tabId=cms_tree.contextID;
            cms_tree.setCustomSortFunction(nameSortCmsCatTree);

            var children = cms_tree.getSubItems(tabId).split(",");
            cms_tree.sortTree(tabId,'ASC',1);
            children = cms_tree.getSubItems(tabId);

            $.post("index.php?ajax=1&act=cms_category_update&action=sort_and_save&id_cms_category="+tabId,{'children':children},function(){
                dhtmlx.message({text:'<?php echo addslashes(_l('CMS Category sorted and positions recorded'));?>',type:'success',expire:5000});});

        }
        if (itemId=="enable"){
            tabId=cms_tree.contextID;
            todo=(cms_tree.getItemImage(tabId,0,false)=='catalog.png'?0:1);
            $.get("index.php?ajax=1&act=cms_category_update&action=enable&id_cms_category="+tabId+'&enable='+todo,function(id){
                if (todo){
                    cms_tree.setItemImage2(tabId,'catalog.png','catalog.png','catalog.png');
                }else{
                    cms_tree.setItemImage2(tabId,'folder_grey.png','folder_grey.png','folder_grey.png');
                }
            });
        }
    }
    cms_cmenu_tree.attachEvent("onClick", onTreeContextButtonClick);
    var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
        '<item text="Object" id="object" enabled="false"/>'+
        '<item text="<?php echo _l('Expand')?>" id="expand"/>'+
        '<item text="<?php echo _l('Collapse')?>" id="collapse"/>'+
        '<item text="<?php echo _l('Sort')?>" id="sort"/>'+
        '<item text="<?php echo _l('Sort and save')?>" id="sort_and_save"/>'+
        '<item text="<?php echo _l('See on shop')?>" id="goshop"/>'+
        '<item text="<?php echo _l('Edit in PrestaShop BackOffice')?>" id="gopsbo"/>'+
        <?php if(_r("ACT_CMS_CONTEXTMENU_SHOHIDE_CATEGORY")) { ?>
        '<item text="<?php echo _l('Enable / Disable')?>" id="enable"/>'+
        <?php } ?>
        '</menu>';
    cms_cmenu_tree.loadStruct(contextMenuXML);
    cms_tree.enableContextMenu(cms_cmenu_tree);

    <?php	//#####################################
    //############ Events
    //#####################################
    ?>
    cms_tree.attachEvent("onClick",function(idcategory){
        if (idcategory!=cmsselection || SCMS) {
            cmsselection = idcategory;

            if (display_cms_after_cat_select) {
                displayCms();
            } else {
                display_cms_after_cat_select = true;
            }
            if (propertiesPanel=='accessories' && accessoriesFilter)
            {
                prop_tb._accessoriesGrid.clearAll(true);
                prop_tb._accessoriesGrid._rowsNum=0;
                displayAccessories('',0);
            }
        }
        cms_pagePanel.setText('<?php echo _l('cms_page',1).' '._l('of',1)?> '+cms_tree.getItemText(cmsselection)+(shopselection?' / '+cms_shoptree.getItemText(shopselection):''));
    });
    cms_tree.attachEvent("onDragIn",function doOnDragIn(idSource,idTarget,sourceobject,targetobject){
        if (drag_disabled_for_sort ==  false){
            if ( sourceobject._name=='tree' ){
                return false ;
            }
        }

        if(idSource!=undefined && idSource!=0)
        {
            var is_home = cms_tree.getUserData(idSource,"is_home");
            if(is_home==1)
            {
                return false;
            }
        }

        <?php if(!_r("ACT_CMS_MOVE_CATEGORY")) { ?>
        if (sourceobject._name=='tree') return false;
        <?php } ?>
        <?php if(!_r("ACT_CMS_MOVE_PAGES_IN_CATEGORY")) { ?>
        if (sourceobject._name=='grid' && targetobject._name=='tree') return false;
        <?php } ?>

        if (sourceobject._name=='tree' || sourceobject._name=='grid') return true;
        return false;
    });
    cms_tree.attachEvent("onDrop",function doOnDrop(idSource,idTarget,idBefore,sourceobject,targetTree){
        var real_parent_id = idTarget;
        if(real_parent_id==0)
        {
            real_parent_id = cms_tree.getUserData(idSource,"parent_root");
        }

        if (sourceobject._name=='tree')
            $.get("index.php?ajax=1&act=cms_category_update&action=move&idCateg="+idSource+"&idNewParent="+real_parent_id+"&idNextBrother="+idBefore+'&id_lang='+SC_ID_LANG, function(data){
            });
    });
    cms_tree.attachEvent("onDrag",function(sourceid,targetid,sibling,sourceobject,targetobject){
        if(sourceid!=undefined && sourceid!=0 && targetid!=undefined && targetid!=0)
        {
            var is_recycle_bin = cms_tree.getUserData(targetid,"is_recycle_bin");
            if(is_recycle_bin==1)
            {
                var not_deletable = cms_tree.getUserData(sourceid,"not_deletable");
                if(not_deletable==1)
                    return false;
            }

            if (sourceobject._name=='tree') { // sans ce if, il considère la page cms comme une catégorie
                var is_home = cms_tree.getUserData(sourceid,"is_home");
                if(is_home==1)
                {
                    return false;
                }
            }
        }

        <?php if(!_r("ACT_CMS_MOVE_CATEGORY")) { ?>
        if (sourceobject._name=='tree') return false;
        <?php } ?>
        <?php if(!_r("ACT_CMS_MOVE_PAGES_IN_CATEGORY")) { ?>
        if (sourceobject._name=='grid' && targetobject._name=='tree') return false;
        <?php } ?>
        if (targetid==0) {targetid=cms_tree.getUserData("","parent_root"); /*return false;*/}
        if (sourceobject._name=='grid')
        {
            var manuel_add = cms_tree.getUserData(targetid,"manuel_add");
            targetobject.setItemStyle(targetid,'background-color:#fedead;');
            var cmsPages=cms_grid.getSelectedRowId();
            if (cmsPages==null && draggedCmsPage!=0) cmsPages=draggedCmsPage;
            if (dragdropcache!=cmsselection+'-'+targetid+'-'+cmsPages)
            {
                var CMScategorySource = cmsselection;
                if(tree_mode=='all')
                {
                    CMScategorySource = cms_tree.getAllSubItems(cmsselection);
                }
                $.post("index.php?ajax=1&act=cms_category_droppageoncategory&mode=move&id_lang="+SC_ID_LANG,{'categoryTarget':targetid,'categorySource':CMScategorySource,'cmsPages':cmsPages},function(){
                    if (draggedCmsPage>0)
                    {
                        setTimeout('cms_grid.deleteRow('+draggedCmsPage+');',200);
                    }else{
                        setTimeout('cms_grid.deleteSelectedRows();',200);
                    }
                    if (propertiesPanel=='cms')
                        displayCmsCategories();
                    draggedCmsPage=0;
                });
                dragdropcache=cmsselection+'-'+targetid+'-'+cmsPages;
            }
            return false;
        }else{
            if (sourceobject._name=='tree')
                return true;
            return false;
        }
    });
    cms_tree.attachEvent("onBeforeContextMenu", function(itemId){
        cms_cmenu_tree.setItemText('object', 'ID'+itemId+': <?php echo _l('CMS Category:')?> '+cms_tree.getItemText(itemId));
        cms_cmenu_tree.showItem('sort');
        cms_cmenu_tree.showItem('goshop');
        cms_cmenu_tree.showItem('gopsbo');
        <?php if(_r("ACT_CMS_CONTEXTMENU_SHOHIDE_CATEGORY")) { ?>
        cms_cmenu_tree.showItem('enable');
        <?php } ?>
        <?php if(SCMS) { ?>
        if(shopUrls[shopselection] != undefined && shopUrls[shopselection] != "" && shopUrls[shopselection] != null)
        {
            cms_cmenu_tree.setItemEnabled('goshop');
        }else{
            cms_cmenu_tree.setItemDisabled('goshop');
        }
        <?php } ?>
        return true;
    });


    <?php	//#####################################
    //############ Display
    //#####################################
    ?>

    function displayTree(callback)
    {
        cms_tree.deleteChildItems(0);
        cms_tree.loadXML("index.php?ajax=1&act=cms_category_get&id_lang="+SC_ID_LANG+"&id_shop="+shopselection+"&"+new Date().getTime(),function(){
            if (cmsselection!=0) {
                var cms_pos = cms_tree.getIndexById(cmsselection);
                if((cms_pos!=undefined && cms_pos!==false && cms_pos!=null && cms_pos!="") || cms_pos===0) {
                    cms_tree.openItem(cmsselection);
                    cms_tree.selectItem(cmsselection,true);
                    if (callback!='') eval(callback);
                } else {
                    cms_grid.clearAll(true);
                }
            } else {
                if (callback!='') eval(callback);
            }
            drag_disabled_for_sort = true;
        });
    }

    <?php if(SCMS) { ?>
    if(shopselection=="all" || shopselection=="0")
        displayTree();
    <?php } ?>
</script>
