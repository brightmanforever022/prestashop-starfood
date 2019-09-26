<script type="text/javascript" src="<?php
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
 echo SC_JQUERY;?>"></script>
<style>
.meta_title {
font-size: 18px;
font-weight: normal;
font-family: arial,sans-serif;
line-height: 1.2;
color: #1a0dab;
white-space: nowrap;
padding: 0px;
margin: 0px;
}
.url {
font-size: 14px;
color: #006621;
font-style: normal;
white-space: nowrap;
line-height: 16px;
font-family: arial,sans-serif;
}
.meta_desc {
line-height: 1.4;
word-wrap: break-word;
color: #545454;
font-family: arial,sans-serif;
font-size: small;
font-weight: normal;
}

.none {display: none;}
</style>
<h3 class="meta_title">META Title</h3>
<span class="url">http://www.cms/url.html</span><br/>
<span class="meta_desc">Meta description, lorem ipsum......</span>

<h3 class="meta_title_none none">META Title</h3>
<span class="url_none none">http://www.cms/url.html</span><br/>
<span class="meta_desc_none none">Meta description, lorem ipsum......</span>

<script>
var tempIdList = (parent.prop_tb._cmsSeoGrid.getSelectedRowId()!=null?parent.prop_tb._cmsSeoGrid.getSelectedRowId():"");
if(tempIdList.search(",")>=0)
{
	var temp = tempIdList.split(",");
	tempIdList=temp[0];
}
if(tempIdList!=undefined && tempIdList!=null && tempIdList!=0 && tempIdList!="")
{
	idxCmsSeoTitle=parent.prop_tb._cmsSeoGrid.getColIndexById('meta_title');
	idxCmsSeoDesc=parent.prop_tb._cmsSeoGrid.getColIndexById('meta_description');

	var meta_title = parent.prop_tb._cmsSeoGrid.cells(tempIdList,idxCmsSeoTitle).getValue();
	var meta_description = parent.prop_tb._cmsSeoGrid.cells(tempIdList,idxCmsSeoDesc).getValue();
	var url = parent.prop_tb._cmsSeoGrid.getUserData(tempIdList,"url");

	if(meta_title!=undefined && meta_title!=null && meta_title!=0 && meta_title!="")
		$(".meta_title").html(meta_title);
	else
		$(".meta_title").html($(".meta_title_none").html());

	if(meta_description!=undefined && meta_description!=null && meta_description!=0 && meta_description!="")
		$(".meta_desc").html(meta_description);
	else
		$(".meta_desc").html($(".meta_desc_none").html());

	if(url!=undefined && url!=null && url!=0 && url!="")
		$(".url").html(url);
	else
		$(".url").html($(".url_none").html());

}
</script>
