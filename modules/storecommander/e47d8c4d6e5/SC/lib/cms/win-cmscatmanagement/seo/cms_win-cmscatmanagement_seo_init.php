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
// INITIALISATION TOOLBAR
cms_prop_tb.addListOption('cms_prop_subproperties', 'cms_prop_seo', 4, "button", '<?php echo _l('SEO', 1) ?>', "lib/img/description.png");

cms_prop_tb.attachEvent("onClick", function(id){
    if(id=="cms_prop_seo")
    {
        hideCmsCatManagementSubpropertiesItems();
        cms_prop_tb.setItemText('cms_prop_subproperties', '<?php echo _l('SEO', 1) ?>');
        cms_prop_tb.setItemImage('cms_prop_subproperties', 'lib/img/description.png');
        actual_cmscatmanagement_subproperties = "cms_prop_seo";
        initCmsCatManagementPropSeo();
    }
});

cms_treegrid_grid.attachEvent("onRowSelect", function(id,ind){
    if (!dhxlCmsCatManagement.cells('b').isCollapsed())
    {
        if(actual_cmscatmanagement_subproperties == "cms_prop_seo"){
            getCmsCatManagementPropSeo();
        }
    }
});

cms_prop_tb.addButton('cms_prop_seo_refresh',100,'','lib/img/arrow_refresh.png','lib/img/arrow_refresh.png');
cms_prop_tb.setItemToolTip('cms_prop_seo_refresh','<?php echo _l('Refresh grid', 1) ?>');
if (isIPAD)
{
    cms_prop_tb.addButtonTwoState('cms_prop_seo_lightNavigation', 100, "", "lib/img/cursor.png", "lib/img/cursor.png");
    cms_prop_tb.setItemToolTip('cms_prop_seo_lightNavigation','<?php echo _l('Light navigation (simple click on grid)', 1) ?>');
}
hideCmsCatManagementSubpropertiesItems();

cms_prop_tb.attachEvent("onClick", function(id){
    if (id=='cms_prop_seo_refresh')
    {
        getCmsCatManagementPropSeo();
    }
});

cms_prop_tb.attachEvent("onStateChange",function(id,state){
    if (id=='cms_prop_seo_lightNavigation')
    {
        if (state)
        {
            cms_prop_seo_grid.enableLightMouseNavigation(true);
        }else{
            cms_prop_seo_grid.enableLightMouseNavigation(false);
        }
    }
});

// FUNCTIONS
var cms_prop_seo = null;
var clipboardType_CmsCatPropSeo = null;
function initCmsCatManagementPropSeo()
{
    cms_prop_tb.showItem('cms_prop_seo_refresh');
    cms_prop_tb.showItem('cms_prop_seo_lightNavigation');

    cms_prop_seo = dhxlCmsCatManagement.cells('b').attachLayout("1C");
    dhxlCmsCatManagement.cells('b').showHeader();

    // GRID
    cms_prop_seo.cells('a').hideHeader();


    cms_prop_seo_grid = cms_prop_seo.cells('a').attachGrid();
    cms_prop_seo_grid.setImagePath("lib/js/imgs/");
    cms_prop_seo_grid.enableDragAndDrop(false);
    cms_prop_seo_grid.enableMultiselect(true);

    // UISettings
    cms_prop_seo_grid._uisettings_prefix='cms_prop_seo_grid';
    cms_prop_seo_grid._uisettings_name=cms_prop_seo_grid._uisettings_prefix;
    cms_prop_seo_grid._first_loading=1;

    // UISettings
    initGridUISettings(cms_prop_seo_grid);

    getCmsCatManagementPropSeo();

    // Data update
    cms_prop_seo_grid.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
        idxMetaTitle=cms_prop_seo_grid.getColIndexById('meta_title');
        idxMetaDescription=cms_prop_seo_grid.getColIndexById('meta_description');
        idxMetaKeywords=cms_prop_seo_grid.getColIndexById('meta_keywords');

        if(stage==0 || stage==1)
        {
            var is_recycle_bin = cms_prop_seo_grid.getUserData(rId,"is_recycle_bin");
            if(is_recycle_bin=="1")
            return false;
        }

        var field = 'link_rewrite';
        if(idxMetaTitle==cInd) {
            field = 'meta_title';
        }else if(idxMetaDescription==cInd) {
            field = 'meta_description';
        }else if(idxMetaKeywords==cInd) {
            field = 'meta_keywords';
        }
        var enableOnCols=new Array(
            idxMetaTitle,
            idxMetaDescription,
            idxMetaKeywords
        );
        if (!in_array(cInd,enableOnCols))
            return false;

        if(stage==2)
        {
            $.get("index.php?ajax=1&act=cms_win-cmscatmanagement_seo_update&action=update&gr_id="+rId+"&field="+field+"&value="+nValue+'&id_shop='+id_shop+'&id_lang='+SC_ID_LANG, function(data){
                var valueLength = nValue.length;
                if (field == 'meta_title') {
                    idMetaTitleCol=cms_prop_seo_grid.getColIndexById('meta_title_width');
                    cms_prop_seo_grid.cells(rId,idMetaTitleCol).setValue(valueLength);
                }
                if (field == 'meta_description') {
                    idMetaDescCol=cms_prop_seo_grid.getColIndexById('meta_description_width');
                    cms_prop_seo_grid.cells(rId,idMetaDescCol).setValue(valueLength);
                }
                if (field == 'meta_keywords') {
                    idMetaKeyCol=cms_prop_seo_grid.getColIndexById('meta_keywords_width');
                    cms_prop_seo_grid.cells(rId,idMetaKeyCol).setValue(valueLength);
                }
            });
        }
        return true;
    });


    cms_prop_seo_grid.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
        if(stage==2 && nValue!=oValue)
        {
            idxLinkRewrite=cms_prop_seo_grid.getColIndexById('link_rewrite');
            if (nValue!="" && cInd==idxLinkRewrite)
            {
            <?php
            $accented = Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
            if ($accented == 1) { ?>
                cms_prop_seo_grid.cells(rId,idxLinkRewrite).setValue(getAccentedLinkRewriteFromString(nValue.substr(0,<?php echo _s('CMS_LINK_REWRITE_SIZE') ?>)));
            <?php } else { ?>
                cms_prop_seo_grid.cells(rId,idxLinkRewrite).setValue(getLinkRewriteFromString(nValue.substr(0,<?php echo _s('CMS_LINK_REWRITE_SIZE') ?>)));
            <?php } ?>
            }
        }
        return true;
    });

    // Context menu for grid
    cms_prop_seo_cmenu=new dhtmlXMenuObject();
    cms_prop_seo_cmenu.renderAsContextMenu();
    function onGridCmsCatPropSeoContextButtonClick(itemId){
        tabId=cms_prop_seo_grid.contextID.split('_');
        tabId=tabId[0]+"_"+tabId[1]<?php if (SCMS) { ?>+"_"+tabId[2]<?php } ?>;
        if (itemId=="copy"){
            if (lastColumnRightClicked_CmsCatPropSeo!=0)
            {
                clipboardValue_CmsCatPropSeo=cms_prop_seo_grid.cells(tabId,lastColumnRightClicked_CmsCatPropSeo).getValue();
                cms_prop_seo_cmenu.setItemText('paste' , '<?php echo _l('Paste') ?> '+cms_prop_seo_grid.cells(tabId,lastColumnRightClicked_CmsCatPropSeo).getTitle());
                clipboardType_CmsCatPropSeo=lastColumnRightClicked_CmsCatPropSeo;
            }
        }
        if (itemId=="paste"){
            if (lastColumnRightClicked_CmsCatPropSeo!=0 && clipboardValue_CmsCatPropSeo!=null && clipboardType_CmsCatPropSeo==lastColumnRightClicked_CmsCatPropSeo)
            {
                selection=cms_prop_seo_grid.getSelectedRowId();
                if (selection!='' && selection!=null)
                {
                    idxMetaTitle=cms_prop_seo_grid.getColIndexById('meta_title');
                    idxMetaDescription=cms_prop_seo_grid.getColIndexById('meta_description');
                    idxMetaKeywords=cms_prop_seo_grid.getColIndexById('meta_keywords');

                    selArray=selection.split(',');
                    for(i=0 ; i < selArray.length ; i++)
                    {
                        cms_prop_seo_grid.cells(selArray[i],lastColumnRightClicked_CmsCatPropSeo).setValue(clipboardValue_CmsCatPropSeo);

                        var field = 'link_rewrite';
                        if(idxMetaTitle==lastColumnRightClicked_CmsCatPropSeo) {
                            field = 'meta_title';
                        }else if(idxMetaDescription==lastColumnRightClicked_CmsCatPropSeo) {
                            field = 'meta_description';
                        }else if(idxMetaKeywords==lastColumnRightClicked_CmsCatPropSeo) {
                         field = 'meta_keywords';
                        }

                        $.get("index.php?ajax=1&act=cms_win-cmscatmanagement_seo_update&action=update&DEBUG=1&gr_id="+selArray[i]+"&field="+field+"&value="+clipboardValue_CmsCatPropSeo+'&id_shop='+id_shop+'&id_lang='+id_actual_lang, function(data){});
                        colorActive();
                    }
                }
            }
        }
    }
    cms_prop_seo_cmenu.attachEvent("onClick", onGridCmsCatPropSeoContextButtonClick);
    var contextMenuXML=' <menu absolutePosition="auto" mode="popup" maxItems="8" globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
        ' <item text="Object" id="object" enabled="false"/>'+
        ' <item text="Object" id="object2" enabled="false"/>'+
        <?php if (SCMS) { ?>
        ' <item text="Object" id="object3" enabled="false"/>'+
        <?php } ?>
        ' <item text="<?php echo _l('Copy') ?>" id="copy"/>'+
        ' <item text="<?php echo _l('Paste') ?>" id="paste"/>'+
    ' </menu>';
    cms_prop_seo_cmenu.loadStruct(contextMenuXML);
    cms_prop_seo_grid.enableContextMenu(cms_prop_seo_cmenu);

    cms_prop_seo_grid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
        var enableOnCols=new Array(
            cms_prop_seo_grid.getColIndexById('link_rewrite'),
            cms_prop_seo_grid.getColIndexById('meta_title'),
            cms_prop_seo_grid.getColIndexById('meta_description'),
            cms_prop_seo_grid.getColIndexById('meta_keywords')
        );
        if (!in_array(colidx,enableOnCols))
        {
            return false;
        }
        lastColumnRightClicked_CmsCatPropSeo=colidx;
        cms_prop_seo_cmenu.setItemText('object', '<?php echo _l('Category:') ?> '+cms_prop_seo_grid.cells(rowid,cms_prop_seo_grid.getColIndexById('id_cms_category')).getTitle());
        cms_prop_seo_cmenu.setItemText('object2', '<?php echo _l('Lang:') ?> '+cms_prop_seo_grid.cells(rowid,cms_prop_seo_grid.getColIndexById('lang')).getTitle());
        <?php if (SCMS) { ?>cms_prop_seo_cmenu.setItemText('object3', '<?php echo _l('Shop:') ?> '+cms_prop_seo_grid.cells(rowid,cms_prop_seo_grid.getColIndexById('shop')).getTitle());<?php } ?>
        if (lastColumnRightClicked_CmsCatPropSeo==clipboardType_CmsCatPropSeo)
        {
            cms_prop_seo_cmenu.setItemEnabled('paste');
        }else{
            cms_prop_seo_cmenu.setItemDisabled('paste');
        }
        return true;
    });
}

function getCmsCatManagementPropSeo()
{
    cms_prop_seo_grid.clearAll(true);
    var tempIdList = (cms_treegrid_grid.getSelectedRowId()!=null?cms_treegrid_grid.getSelectedRowId():"");
    $.post("index.php?ajax=1&act=cms_win-cmscatmanagement_seo_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data)
    {
        cms_prop_seo_grid.parse(data);

        // UISettings
        loadGridUISettings(cms_prop_seo_grid);
        cms_prop_seo_grid._first_loading=0;
    });
}
