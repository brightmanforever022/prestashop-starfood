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
<div id="search">
	<form id="searchbox" action="" method="GET" onSubmit="return false;">		
		<div id="quicksearch" style="margin-top: 2px;">
			<input id="search_query" class="ac_input" value="" type="search" name="search_query" placeholder="<?php echo _l('Search')?>" onClick="this.select();" />
			<img src="lib/img/ajax-loader16.gif" id="quicksearch_loading" alt="" style="display: none; float: right; margin-top: 3px; margin-right: 10px;" />
		</div>
		<div id="menuObj" class="align_right" dir="ltr"></div>
		<input type="submit" style="display:none" class="autocomplete"/>
	</form>
</div>
<script type="text/javascript">
id_product_attributeToSelect=0;
$('document').ready(function(){
		if ($.cookie('sc_cat_qs_filter_cookie') != null) {
			var sc_qs_filter = JSON.parse($.cookie('sc_cat_qs_filter_cookie'));
			myAutoCompleteURL="index.php?ajax=1&act=cat_quicksearch_get"+
				"&id_product="+sc_qs_filter['id_product']+
				"&id_product_attribute="+sc_qs_filter['id_product_attribute']+
				"&name="+sc_qs_filter['name']+
				"&reference="+sc_qs_filter['reference']+
				"&supplier_reference="+sc_qs_filter['supplier_reference']+
				<?php if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')) : ?>
				"&supplier_reference_all="+sc_qs_filter['supplier_reference_all']+
				<?php endif; ?>
				<?php if(version_compare(_PS_VERSION_, '1.4.0.2', '>=')) : ?>
				"&upc="+sc_qs_filter['upc']+
				<?php endif; ?>
				"&ean="+sc_qs_filter['ean']+
				"&short_desc="+sc_qs_filter['short_desc']+
				"&desc="+sc_qs_filter['desc'];

		} else {
			myAutoCompleteURL="index.php?ajax=1&act=cat_quicksearch_get";
			var sc_qs_filter = {
				'id_product':1,
				'id_product_attribute':1,
				'name':1,
				'reference':1,
				'supplier_reference':1,
				<?php if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')) : ?>
				'supplier_reference_all':1,
				<?php endif; ?>
				'ean':1,
				<?php if(version_compare(_PS_VERSION_, '1.4.0.2', '>=')) : ?>
				'upc':1,
				<?php endif; ?>
				'short_desc':0,
				'desc':0
			};
		}
		myAutoCompleteLoading = "quicksearch_loading";
		$("#search_query").autocomplete("index.php?ajax=1&act=cat_quicksearch_get",{
				minChars: 1,
				max: 20,
				width: 500,
				cacheLength:0,
				selectFirst: false,
				scroll: false,
				blockSubmit:true,
				dataType: "json",
				formatItem: function(data, i, max, value, term){
					return value;
				},
				parse: function(data){
						var mytab = new Array();
						for (var i = 0; i < data.length; i++){
							mytab[mytab.length]={
								data: data[i],
								value: data[i].cname+' > '+data[i].pname
							};
						}
						return mytab;
				},
				extraParams:{
					ajaxSearch: 1
				}
		})
		.result(function(event, data, formatted){
				lastProductSelID=0;
				catselection=0;
				if (typeof data!='undefined')
				{
					cat_tree.openItem(data.id_category);
					cat_tree.selectItem(data.id_category,false);
					catselection=data.id_category;
					displayProducts('id_product_attributeToSelect='+Number(data.id_product_attribute)+';lastProductSelID=0;idxProductID=cat_grid.getColIndexById("id");oldFilters["id"]="'+data.id_product+'";cat_grid.getFilterElement(idxProductID).value="'+data.id_product+'";cat_grid.filterByAll();cat_grid.selectRowById('+data.id_product+',false,true,true);');
				}
				return false;
		})

	filterQuickSearch = new dhtmlXMenuObject("menuObj");
	qsXMLMenuData=''+
	'<menu>'+
	'<item id="filters" text="<?php echo _l('Filters')?>" img="lib/img/filter.png" imgdis="lib/img/filter.png">'+
		'<item id="id_product" type="checkbox" '+(sc_qs_filter['id_product']==1?'checked="true"':'')+' text="<?php echo _l('id_product')?>"></item>'+
		'<item id="id_product_attribute" type="checkbox" '+(sc_qs_filter['id_product_attribute']==1?'checked="true"':'')+' text="<?php echo _l('id_product_attribute')?>"></item>'+
		'<item id="name" type="checkbox" '+(sc_qs_filter['name']==1?'checked="true"':'')+' text="<?php echo _l('Name')?>"></item>'+
		'<item id="reference" type="checkbox" '+(sc_qs_filter['reference']==1?'checked="true"':'')+' text="<?php echo _l('Reference')?>"></item>'+
		'<item id="supplier_reference" type="checkbox" '+(sc_qs_filter['supplier_reference']==1?'checked="true"':'')+' text="<?php echo _l('Default Supplier Ref.')?>"></item>'+
		<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) : ?>
		'<item id="supplier_reference_all" type="checkbox" '+(sc_qs_filter['supplier_reference_all']==1?'checked="true"':'')+' text="<?php echo _l('All Supplier Ref.')?>"></item>'+
		<?php endif; ?>
		'<item id="ean" type="checkbox" '+(sc_qs_filter['ean']==1?'checked="true"':'')+' text="<?php echo _l('EAN13')?>"></item>'+
		<?php if (version_compare(_PS_VERSION_, '1.4.0.2', '>=')) : ?>
		'<item id="upc" type="checkbox" '+(sc_qs_filter['upc']==1?'checked="true"':'')+' text="<?php echo _l('UPC')?>"></item>'+
		<?php endif; ?>
		'<item id="short_desc" type="checkbox" '+(sc_qs_filter['short_desc']==1?'checked="true"':'')+' text="<?php echo _l('Short description')?>"></item>'+
		'<item id="desc" type="checkbox" '+(sc_qs_filter['desc']==1?'checked="true"':'')+' text="<?php echo _l('Description')?>"></item>'+
		'<item type="separator"></item>'+
		'<item id="all_tick" text="<?php echo _l('Tick all filters',1)?>"></item>'+
		'<item id="all_untick" text="<?php echo _l('Untick all filters',1)?>"></item>'+
	'</item>'+
	'</menu>';
	filterQuickSearch.loadStruct(qsXMLMenuData);
	function onMenuClick(id, state, zoneId, casState){
		state=Number(!state);
		if(id=='all_tick') {
			filterQuickSearch.forEachItem(function(itemId){
				filterQuickSearch.setCheckboxState(itemId, 1);
			});
		}
		if(id=='all_untick') {
			filterQuickSearch.forEachItem(function(itemId){
				filterQuickSearch.setCheckboxState(itemId, 0);
			});
		}
		sc_qs_filter['id_product'] = (id=='id_product'?state:Number(filterQuickSearch.getCheckboxState('id_product')));
		sc_qs_filter['id_product_attribute'] = (id=='id_product_attribute'?state:Number(filterQuickSearch.getCheckboxState('id_product_attribute')));
		sc_qs_filter['name'] = (id=='name'?state:Number(filterQuickSearch.getCheckboxState('name')));
		sc_qs_filter['reference'] = (id=='reference'?state:Number(filterQuickSearch.getCheckboxState('reference')));
		sc_qs_filter['supplier_reference'] = (id=='supplier_reference'?state:Number(filterQuickSearch.getCheckboxState('supplier_reference')));
		sc_qs_filter['supplier_reference_all'] = (id=='supplier_reference_all'?state:Number(filterQuickSearch.getCheckboxState('supplier_reference_all')));
		sc_qs_filter['ean'] = (id=='ean'?state:Number(filterQuickSearch.getCheckboxState('ean')));
		sc_qs_filter['upc'] = (id=='upc'?state:Number(filterQuickSearch.getCheckboxState('upc')));
		sc_qs_filter['short_desc'] = (id=='short_desc'?state:Number(filterQuickSearch.getCheckboxState('short_desc')));
		sc_qs_filter['desc'] = (id=='desc'?state:Number(filterQuickSearch.getCheckboxState('desc')));
		myAutoCompleteURL="index.php?ajax=1&act=cat_quicksearch_get"+
							"&id_product="+sc_qs_filter['id_product']+
							"&id_product_attribute="+sc_qs_filter['id_product_attribute']+
							"&name="+sc_qs_filter['name']+
							"&reference="+sc_qs_filter['reference']+
							"&supplier_reference="+sc_qs_filter['supplier_reference']+
							"&supplier_reference_all="+sc_qs_filter['supplier_reference_all']+
							"&ean="+sc_qs_filter['ean']+
							"&upc="+sc_qs_filter['upc']+
							"&short_desc="+sc_qs_filter['short_desc']+
							"&desc="+sc_qs_filter['desc'];
		$.cookie('sc_cat_qs_filter_cookie' , JSON.stringify(sc_qs_filter), { expires: 60 });
		return true;
	}
	filterQuickSearch.attachEvent("onCheckboxClick",onMenuClick);
	filterQuickSearch.attachEvent("onClick",onMenuClick);
	if (isIPAD)
	{
		$('#search').css('width','150px');
		$('#searchbox').css('width','150px');
		$('#menuObj').css('width','100px');
		$('#menuObj').css('display','inline');
		$('#search_query').css('width','100px');
		$('#search_query').css('display','inline');
	}
});
</script>
