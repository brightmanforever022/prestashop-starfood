<?php
$autocreate = Tools::getValue("autocreate");
?><script type="text/javascript">
    wCatFoulefactory.setDimension($(window).width(), $(window).height()-28);
    wCatFoulefactory.setPosition(0, 28);
    wCatFoulefactory.setText("<?php echo _l('FouleFactory project management')?>");

    FFManagement=wCatFoulefactory.attachLayout("3J");

    // COLONNE A
        var cellProjects = FFManagement.cells('a');
        cellProjects.hideHeader();

        var projects_tb = cellProjects.attachToolbar();
        projects_tb.addButton('ff_projects_refresh',100,'','lib/img/arrow_refresh.png','lib/img/arrow_refresh.png');
        projects_tb.setItemToolTip('ff_projects_refresh','<?php echo _l('Refresh',1)?>');
        projects_tb.addButton('ff_projects_add',100,'','lib/img/add.png','lib/img/add.png');
        projects_tb.setItemToolTip('ff_projects_add','<?php echo _l('New project',1)?>');
        projects_tb.addButton('ff_projects_getprice',100,'','lib/img/money_euro.png','lib/img/money_euro.png');
        projects_tb.setItemToolTip('ff_projects_getprice','<?php echo _l('Get prices',1)?>');
        projects_tb.addButton('ff_projects_putarchived',100,'','lib/img/folder_grey_delete.png','lib/img/folder_grey_delete.png');
        projects_tb.setItemToolTip('ff_projects_putarchived','<?php echo _l('Archive selected projects',1)?>');
        projects_tb.addButtonTwoState('ff_projects_archived',100,'','lib/img/folder_grey_find.png','lib/img/folder_grey_find.png');
        projects_tb.setItemToolTip('ff_projects_archived','<?php echo _l('Show archived projects',1)?>');
        projects_tb.addButton('ff_projects_config',100,'','lib/img/cog.png','lib/img/cog.png');
        projects_tb.setItemToolTip('ff_projects_config','<?php echo _l('Configuration',1)?>');
        projects_tb.addButton("ff_projects_help", 100, "", "lib/img/help.png", "lib/img/help.png");
        projects_tb.setItemToolTip('ff_projects_help','<?php echo _l('Help')?>');

        projects_tb.attachEvent("onClick",
            function(id){
                if (id=='ff_projects_help')
                {
                    <?php
                    if ($user_lang_iso == 'fr') {
                        echo " window.open('http://www.storecommander.com/redir.php?dest=2016061032'); ";
                    } else {
                        echo " window.open('http://www.storecommander.com/redir.php?dest=2016061031'); ";
                    }
                    ?>
                }
                if (id=='ff_projects_refresh')
                {
                    displayFFProjects();
                }
                if (id=='ff_projects_add')
                {
                    var name_project = prompt('<?php echo _l('Project name ?',1)?>', "New project");

                    if (name_project != null) {
                        var newId = new Date().getTime();
                        projectsGrid.addRow("new" + newId, [name_project, "feature", "<?php echo date("Y-m-d"); ?>", "<?php echo _l("To configure"); ?>","", "-", "-", "-", "0", "-"]);
                        idxName=projectsGrid.getColIndexById('name');
                        FFProjectEditCell(2, "new" + newId, idxName,name_project,"");
                    }
                }
                if(id=='ff_projects_config')
                {
                    if (!dhxWins.isWindow('wCatFoulefactoryConfig'))
                    {
                        wCatFoulefactoryConfig = dhxWins.createWindow('wCatFoulefactoryConfig', 28, 28, 700, 500);
                        wCatFoulefactoryConfig.setIcon('lib/img/foulefactory_icon.png');
                        wCatFoulefactoryConfig.setText('<?php echo _l('FouleFactory Configuration',1)?>');
                        wCatFoulefactoryConfig.attachURL('index.php?ajax=1&act=cat_win-foulefactory_config');
                    }
                }
                if (id=='ff_projects_getprice')
                {
                    $.post("index.php?ajax=1&act=cat_win-foulefactory_project_actions&action=get_quote&id_project="+projectsGrid.getSelectedRowId()+"&id_lang="+parent.SC_ID_LANG+"&"+new Date().getTime(),function(data){
                        parent.displayFFProjects();
                    });
                }
                if (id=='ff_projects_putarchived')
                {
                    if(confirm('<?php echo _l('Are you sure you want to archive these projects?', 1); ?>'))
                    {
                        $.post("index.php?ajax=1&act=cat_win-foulefactory_project_actions&action=put_archived&id_project="+projectsGrid.getSelectedRowId()+"&id_lang="+parent.SC_ID_LANG+"&"+new Date().getTime(),function(data){
                            parent.displayFFProjects();
                        });
                    }
                }
            });
        var show_archived = 0
        projects_tb.attachEvent("onStateChange", function(id,state){
            if (id=='ff_projects_archived'){
                if (state) {
                    show_archived=1;
                }else{
                    show_archived=0;
                }
                displayFFProjects();
            }
        });

        projectsGrid=cellProjects.attachGrid();
        projectsGrid.setImagePath("lib/js/imgs/");
        projectsGrid.enableMultiselect(true);


        function FFProjectEditCell(stage, rId, cIn,nValue,oValue, autocreate){
            if (stage==1 && this.editor && this.editor.obj) this.editor.obj.select();
            if (stage==2)
            {
                idxName=projectsGrid.getColIndexById('name');
                idxType=projectsGrid.getColIndexById('type');
                idxStatusUpdate=projectsGrid.getColIndexById('status_update');
                var field = "";
                if(cIn==idxName)
                {
                    var patt = /new/g;
                    if(patt.test(rId) && nValue!=undefined && nValue!=null && nValue!="" && nValue!=false)
                    {
                        var url_params = "";
                        if(autocreate!=undefined && autocreate!=null && autocreate!="" && autocreate!=0)
                            url_params = "&autocreate="+autocreate;
                        $.post("index.php?ajax=1&act=cat_win-foulefactory_project_update&action=insert"+url_params+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'tempId': rId, name: nValue.replace(/#/g,'')},function(data){
                            if(data.tempId!=undefined && data.tempId!=null && data.tempId!="" && data.tempId!="0")
                            {
                                if(data.newId!=undefined && data.newId!=null && data.newId!="" && data.newId!="0")
                                {
                                    projectsGrid.changeRowId(data.tempId,data.newId);
                                }
                            }
                        }, "json");
                    }
                    else if(!patt.test(rId))
                    {
                        field = "name";
                    }
                }
                if(cIn==idxType)
                {
                    field = "type";
                }
                if(cIn==idxStatusUpdate)
                {
                    field = "status_update";
                }
                if(field!=undefined && field!=null && field!="" && field!="0")
                {
                    $.post("index.php?ajax=1&act=cat_win-foulefactory_project_update&action=update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id_project': rId, field: field,val: nValue.replace(/#/g,'')},function(data){
                    });
                }
            }
            return true;
        }
        projectsGrid.attachEvent("onEditCell", FFProjectEditCell);

        // UISettings
        projectsGrid._uisettings_prefix='ff_projects';
        projectsGrid._uisettings_name=projectsGrid._uisettings_prefix;
        projectsGrid._first_loading=1;

        // UISettings
        initGridUISettings(projectsGrid);

        displayFFProjects(true);

    projectsGrid.attachEvent("onSelectStateChanged",function (id){
        last_project_row_selected = id;
        displayConfigProject();
    });

    // COLONNE B
    var cellConfigProject = FFManagement.cells('b');
    cellConfigProject.setText("<?php echo _l('Project configuration')?>");
    cellConfigProject.setWidth(850);
    cellConfigProject.fixSize(true, false);

    // COLONNE C
    var cellFormBanner = FFManagement.cells('c');
    cellFormBanner.hideHeader();
    cellFormBanner.setHeight(155);
    cellFormBanner.fixSize(true, true);
    cellFormBanner.attachURL("index.php?ajax=1&act=cat_win-foulefactory_returnbanner&id_lang="+SC_ID_LANG+"&"+new Date().getTime());

    /*
    FUNCTIONS
     */
    var last_project_row_selected = null;
    function displayFFProjects(first_exec)
    {
        cellProjects.progressOn();
       projectsGrid.clearAll(true);
       projectsGrid.loadXML("index.php?ajax=1&act=cat_win-foulefactory_project_get&show_archived="+show_archived+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function()
        {
            cellProjects.progressOff();
            // UISettings
            loadGridUISettings(projectsGrid);
           projectsGrid._first_loading=0;

            if(last_project_row_selected!=undefined && last_project_row_selected!=null && last_project_row_selected!="" && last_project_row_selected!=0)
                projectsGrid.selectRowById(last_project_row_selected,true,true,true);

            if(first_exec!=undefined && first_exec!=null && first_exec!="" && first_exec!=0)
            {
                /*
                 * AUTO CREATE
                 */
                <?php  if(!empty($autocreate)) { ?>
                var name_project = prompt('<?php echo _l('Project name ?',1)?>', "New project");

                if (name_project != null) {
                    var newId = new Date().getTime();
                    projectsGrid.addRow("new" + newId, [name_project, "feature", "<?php echo date("Y-m-d"); ?>", "<?php echo _l("To configure"); ?>","", "-", "-", "-", "0", "-"]);

                    idxName=projectsGrid.getColIndexById('name');
                    FFProjectEditCell(2, "new" + newId, idxName,name_project,"","<?php echo $autocreate; ?>");
                }
                <?php } ?>
            }
        });
    }

    function displayConfigProject()
    {
        idxName=projectsGrid.getColIndexById('name');
        id_project = last_project_row_selected;//projectsGrid.getSelectedRowId();
        if (id_project!=undefined && id_project!='' && id_project!=null && id_project!=0)
        {
            var patt = /new/g;
            if (!patt.test(id_project))
            {
                var project_name = projectsGrid.cellById(id_project,idxName).getValue();
                cellConfigProject.setText("<?php echo _l('Project configuration:')?> "+project_name);

                cellConfigProject.attachURL("index.php?ajax=1&act=cat_win-foulefactory_project_form_get&id_project="+id_project+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime());

            }
        }
    }

    function setStatus(id_project, status)
    {
        if (id_project!=undefined && id_project!='' && id_project!=null && id_project!=0)
        {
            if (status!=undefined && status!='' && status!=null && status!=0)
            {
                cellConfigProject.progressOn();
                if(id_project!=projectsGrid.getSelectedRowId())
                    projectsGrid.selectRowById(id_project, true);
                $.post("index.php?ajax=1&act=cat_win-foulefactory_project_actions&action="+status+"&id_project="+id_project+"&id_lang="+parent.SC_ID_LANG+"&"+new Date().getTime(),function(data){
                    cellConfigProject.progressOff();
                    if (data!=undefined && data!='' && data!=null && data!=0)
                    {
                        if(data.status!=undefined && data.status=="success")
                        {
                        }
                        else if(data.status!=undefined && data.status=="success_toPay")
                        {
                            if (data.url!=undefined && data.url!='' && data.url!=null && data.url!=0)
                            {
                                if (!dhxWins.isWindow("wFFPayment"))
                                {
                                    wFFPayment = dhxWins.createWindow("wFFPayment", 100,50, 800,700);
                                    wFFPayment.setIcon('lib/img/foulefactory_icon.png','../../../lib/img/foulefactory_icon.png');
                                    wFFPayment.setText("<?php echo _l('FouleFactory - Project payment')?>");
                                    wFFPayment.setModal(true);
                                    wFFPayment.attachURL(data.url);
                                    wFFPayment.show();
                                    wFFPayment.attachEvent("onClose", function(win){
                                        displayFFProjects();
                                        return true;
                                    });
                                }else{
                                    wFFPayment.setDimension(800,600);
                                    wFFPayment.setModal(true);
                                    wFFPayment.attachURL(data.url);
                                    wFFPayment.show();
                                    wFFPayment.attachEvent("onClose", function(win){
                                        displayFFProjects();
                                        return true;
                                    });
                                }
                            }
                        }
                        else if(data.status!=undefined && data.status=="imported")
                        {
                            var msg = "<?php echo _l("Datas has been successfully imported!"); ?>";
                            dhtmlx.message({text:msg,type:'success',expire:5000});
                        }
                        else if(data.status!=undefined && data.status=="error")
                        {
                            var msg = data.message;
                            dhtmlx.message({text:msg,type:'error',expire:10000});
                        }
                        displayFFProjects();
                    }
                    else
                        displayFFProjects();
                },'json');
            }
        }
    }

    function showReturnForm()
    {
        if (!dhxWins.isWindow("wFFImported"))
        {
            wFFImported = dhxWins.createWindow("wFFImported", 100,100, $(window).width()-200,$(window).height()-200);
            wFFImported.setIcon('lib/img/foulefactory_icon.png','../../../lib/img/foulefactory_icon.png');
            wFFImported.setText("<?php echo _l('Your experience with FouleFactory service')?>");
            wFFImported.setModal(true);
            wFFImported.attachURL('http://www.storecommander.com/redir.php?dest=2016072901&email=<?php echo $sc_agent->email ?>&ffurlshop=<?php echo Tools::getShopDomain(true); ?>');
            wFFImported.show();
            wFFImported.attachEvent("onClose", function(win){
                return true;
            });
        }else{
            wFFImported.setDimension($(window).width()-200,$(window).height()-200);
            wFFImported.setModal(true);
            wFFImported.attachURL('http://www.storecommander.com/redir.php?dest=2016072901&email=<?php echo $sc_agent->email ?>&ffurlshop=<?php echo Tools::getShopDomain(true); ?>');
            wFFImported.show();
            wFFImported.attachEvent("onClose", function(win){
                return true;
            });
        }
    }
</script>