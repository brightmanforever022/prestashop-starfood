
<form id="asdasd" method="post" class="form-horizontal" action="{$form_action}">
<input type="hidden" id="product_id" value="{$product->id}" />
<div class="panel">
{if isset($errors)}
<div class="row">
	<div class="col-lg-12 alert alert-danger">
	{foreach $errors as $error}
	<p>{$error}</p>
	{/foreach}
	</div>
</div>
{/if}
<div class="row">
    
    {*}<pre>
    {$images|@print_r}
    </pre>*}
	<div class="col-lg-7">
		<div class="form-group">
			<label class="control-label col-lg-3">{l s='Name'}</label>
			<div class="col-lg-9">
			{foreach from=$languages item=language}
				<div class="input-group">
					<div class="input-group-addon">{$language.iso_code}</div>
					<input value="{$product->name[ $language.id_lang ]}" type="text" name="name_{$language.id_lang|intval}" class="form-control"/>
                    {assign var=lang_id value="`$language.id_lang`"}
				</div>
			{/foreach}
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3">{l s='Manufacturer'}</label>
			<div class="col-lg-9">
				<select name="id_manufacturer" id="id_manufacturer">
					<option value="0">- {l s='Choose (optional)'} -</option>
					{foreach $manufacturers as $manufacturer}
					
					<option value="{$manufacturer['id_manufacturer']}" 
					{if $product->id_manufacturer==$manufacturer['id_manufacturer']}selected="selected"{/if} 
						>{$manufacturer['name']}</option>
					{/foreach}
				</select>
			</div>
		</div>
        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Unit value'}</label>
            <div class="col-lg-9">
                <input value="{$product->unit_value}" type="text" name="unit_value" id="unit_value" class="form-control"/>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Pieces in unit'}</label>
            <div class="col-lg-9">
                <input value="{if is_null($product->pieces_unit)}1{else}{$product->pieces_unit}{/if}" type="number" name="pieces_unit" id="pieces_unit" class="form-control"/>
            </div>
        </div>
    
		<div class="form-group">
			<label class="control-label col-lg-3">{l s='Unit'}</label>
			<div class="col-lg-9">
				{*<input value="{$product->unit}" type="text" name="unit" class="form-control"/>*}
				<select name="id_measure_unit" id="id_measure_unit">
					<option value="0">- {l s='Choose (optional)'} -</option>
					{foreach $unit_options as $option}
					<option value="{$option['id_measure_unit']}" data-info='{$option|@json_encode}'
					{if $product->id_measure_unit == $option['id_measure_unit'] }selected="selected"{/if} 
						>{$option['name']}</option>
					{/foreach}
				</select>
			</div>
		</div>
    <div class="form-group">
      <label class="control-label col-lg-3">{l s='Liquid density'}</label>
      <div class="col-lg-9">
        <select name="id_liquid_density" id="id_liquid_density">
          <option value="0">- {l s='Choose (optional)'} -</option>
          {foreach $liquid_density_list as $option}
          <option value="{$option['id_liquid_density']}" data-info='{$option|@json_encode}'
          {if $product->id_liquid_density == $option['id_liquid_density'] }selected="selected"{/if} 
            >{$option['name']}</option>
          {/foreach}
        </select>
      </div>
    </div>

		<div class="form-group">
			<label class="control-label col-lg-3">{l s='Package'}</label>
			<div class="col-lg-9">
				{*<input value="{$product->package}" type="text" name="package" class="form-control"/>*}
				<select name="package" id="package">
					<option value="0">- {l s='Choose (optional)'} -</option>
					{foreach $package_options as $option}
					<option value="{$option['id_feature_value']}" 
					{if isset($option['selected'])&&$option['selected']==true}selected="selected"{/if} 
						>{$option['value']}</option>
					{/foreach}
				</select>

			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3">{l s='Units in package' mod='starfood'}</label>
			<div class="col-lg-9">
				{*<input value="{$product->units_per_package}" type="text" name="units_per_package" class="form-control"/>*}
				<select name="units_per_package" id="units_per_package">
					<option value="0">- {l s='Choose (optional)'} -</option>
					{foreach $units_box_options as $option}
					<option value="{$option['id_feature_value']}" 
					{if isset($option['selected'])&&$option['selected']==true}selected="selected"{/if} 
						>{$option['value']}</option>
					{/foreach}
				</select>

			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3">{l s='Weight'}</label>
			<div class="col-lg-9">
				<input value="{$product->weight}" type="text" name="weight" id="weight" class="form-control"/>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-lg-3" for="country">{l s='Country'}</label>
			<div class=" col-lg-9">
				{*<input name="country" type="text" value="{$product->country}" />*}
				<select name="country" id="country">
					<option value="0">- {l s='Choose (optional)'} -</option>
					{foreach $country_options as $option}
					<option value="{$option['id_feature_value']}" 
					{if isset($option['selected'])&&$option['selected']==true}selected="selected"{/if} 
						>{$option['value']}</option>
					{/foreach}
				</select>
				
			</div>
		</div>
        <div class="form-group">
            <label class="control-label col-lg-3" for="country">{l s='Article Number'  mod='starfood'}</label>
            <div class=" col-lg-9">
                <input type="text" name="reference" value="{$product->reference}" />
            </div>
        </div>
            
		<div class="form-group">
			<label class="control-label col-lg-3">
				{l s='Available for order' mod='starfood'}</label>
            
			<div class="col-lg-9">
				<input type="checkbox" name="available_for_order" id="available_for_order" value="1" {if $product->available_for_order}checked="checked"{/if} >
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3">{l s='Status'  mod='starfood'}</label>
			<div class="col-lg-9">
				<input type="checkbox" name="active" value="1" {if $product->active}checked="checked"{/if} >
			</div>
		</div>
		<div class="form-group loadwe" style="position:relative;">
            
			{$image_uploader}
            <img class="loading" style=" display:none;position: absolute;
    bottom: 25%;
    left: 50%;" src="../img/loader.gif" />
            <table id="imageTable" class="table tableDnD">
                <thead>
                    <tr class="nodrag nodrop">
                        <th class="fixed-width-lg">
                            <th class="title_box">{l s='Images'  mod='starfood'}</th>
                        </th>
                        <th class="fixed-width-lg">
                            <span class="title_box">{l s="Caption"  mod='starfood'}</span>
                        </th>
                        <th class="fixed-width-lg">
                            <label class="title_box">{l s='position' mod='starfood'}</label>
                        </th>
                        <th class="fixed-width-lg">
                            <label class="title_box">{l s='cover' mod='starfood'}</label>
                        </th>
                        <th>
                            
                        </th>
                    </tr>
                </thead>
                 {if isset($images)}
                <tbody id="imageList">
                      </tbody>
			     {/if}
			</table>
            <table id="lineType" style="display:none;">
        		<tr id="image_id">
        			<td>
        				<a href="{$smarty.const._THEME_PROD_DIR_}image_path.jpg" class="fancybox">
        					<img
        						src="{$smarty.const._THEME_PROD_DIR_}{$iso_lang}-default-{$imageType}.jpg"
        						alt="legend"
        						title="legend"
        						class="img-thumbnail" />
        				</a>
        			</td>
        			<td>legend</td>
        			<td id="td_image_id" class="pointer dragHandle center positionImage">
        				<div class="dragGroup">
        					<div class="positions">
        						image_position
                                                </div>
                                        </div>
        			</td>
        			{if $shops}
        				{foreach from=$shops item=shop}
        				<td>
        					<input
        						type="checkbox"
        						class="image_shop"
        						name="id_image"
        						id="{$shop.id_shop}image_id"
        						value="{$shop.id_shop}" />
        				</td>
        				{/foreach}
        			{/if}
        			<td class="cover">
        				<a href="#">
        					<i class="icon-check-empty icon-2x covered"></i>
        				</a>
        			</td>
        			<td>
        				<a href="#" class="delete_product_image pull-right btn btn-default" >
        					<i class="icon-trash"></i> {l s='Delete this image'}
        				</a>
        			</td>
        		</tr>
        	</table>
           
            <script type="text/javascript">
        	
            	var upbutton = '{l s='Upload an image' mod='starfood'}';
        		var come_from = 'product';
        		var success_add =  '{l s='The image has been successfully added.' mod='starfood'}';
        		var id_tmp = 0;
        		var current_shop_id = {$current_shop_id|intval};
        		{literal}
        		//Ready Function

    		function imageLine(id, path, position, cover, shops, legend)
    		{
    			line = $("#lineType").html();
    			line = line.replace(/image_id/g, id);
    			line = line.replace(/(\/)?[a-z]{0,2}-default/g, function($0, $1){
    				return $1 ? $1 + path : $0;
    			});
    			line = line.replace(/image_path/g, path);
    			line = line.replace(/image_position/g, position);
    			line = line.replace(/legend/g, legend);
    			line = line.replace(/icon-check-empty/g, cover);
    			line = line.replace(/<tbody>/gi, "");
    			line = line.replace(/<\/tbody>/gi, "");
    			if (shops != false)
    			{
    				$.each(shops, function(key, value){
    					if (value == 1)
    						line = line.replace('id="' + key + '' + id + '"','id="' + key + '' + id + '" checked=checked');
    				});
    			}
    			$("#imageList").append(line);
    		}
            function updateImagePosition(json)
    			{
    				doAdminAjax(
    				{
    					"action":"updateImagePosition",
    					"json":json,
    					"token" : "{/literal}{$token|escape:'html':'UTF-8'}{literal}",
    					"tab" : "AdminProducts",
    					"ajax" : 1
    				});
    			}
    
    			function delQueue(id)
    			{
    				$("#img" + id).fadeOut("slow");
    				$("#img" + id).remove();
    			}
                function runningJS(){
                    	{/literal}
                    {foreach from=$images item=image}
        				assoc = {literal}"{"{/literal};
        				{if $shops}
        					{foreach from=$shops item=shop}
        						assoc += '"{$shop.id_shop}" : {if $image->isAssociatedToShop($shop.id_shop)}1{else}0{/if},';
        					{/foreach}
        				{/if}
        				if (assoc != {literal}"{"{/literal})
        				{
        					assoc = assoc.slice(0, -1);
        					assoc += {literal}"}"{/literal};
        					assoc = jQuery.parseJSON(assoc);
        				}
        				else
        					assoc = false;
        				imageLine({$image->id}, "{$image->getExistingImgPath()}", {$image->position}, "{if $image->cover}icon-check-sign{else}icon-check-empty{/if}", assoc, "{$image->legend[$default_language]|@addcslashes:'\"'}");
        			{/foreach}
        			{literal}
        			var originalOrder = false;
        
        			$("#imageTable").tableDnD(
        			{	dragHandle: 'dragHandle',
                                        onDragClass: 'myDragClass',
                                        onDragStart: function(table, row) {
                                                originalOrder = $.tableDnD.serialize();
                                                reOrder = ':even';
                                                if (table.tBodies[0].rows[1] && $('#' + table.tBodies[0].rows[1].id).hasClass('alt_row'))
                                                        reOrder = ':odd';
                                                $(table).find('#' + row.id).parent('tr').addClass('myDragClass');
                                        },
        				onDrop: function(table, row) {
        					if (originalOrder != $.tableDnD.serialize()) {
        						current = $(row).attr("id");
        						stop = false;
        						image_up = "{";
        						$("#imageList").find("tr").each(function(i) {
        							$("#td_" +  $(this).attr("id")).html('<div class="dragGroup"><div class="positions">'+(i + 1)+'</div></div>');
        							if (!stop || (i + 1) == 2)
        								image_up += '"' + $(this).attr("id") + '" : ' + (i + 1) + ',';
        						});
        						image_up = image_up.slice(0, -1);
        						image_up += "}";
        						updateImagePosition(image_up);
        					}
        				}
        			});
                }
                function afterDeleteProductImage(data)
    			{
    				data = $.parseJSON(data);
    				if (data)
    				{
    					cover = 0;
    					id = data.content.id;
    					if (data.status == 'ok')
    					{
    						if ($("#" + id + ' .covered').hasClass('icon-check-sign'))
    							cover = 1;
    						$("#" + id).remove();
    					}
    					if (cover)
    						$("#imageTable tr").eq(1).find(".covered").addClass('icon-check-sign');
    					$("#countImage").html(parseInt($("#countImage").html()) - 1);
    					refreshImagePositions($("#imageTable"));
    					showSuccessMessage(data.confirmations);
    
    					if (parseInt($("#countImage").html()) <= 1)
    						$('#caption_selection').addClass('hidden');
    				}
    			}

    		$(document).ready(function(){
    		
    			runningJS();
    			/**
    			 * on success function
    			 */
    			
    
    			$('.delete_product_image').die().live('click', function(e)
    			{
    				e.preventDefault();
    				id = $(this).parent().parent().attr('id');
    				if (confirm("{/literal}{l s='Are you sure?' js=1}{literal}"))
    				doAdminAjax({
    						"action":"deleteProductImage",
    						"id_image":id,
    						"id_product" : {/literal}{$product->id}{literal},
    						"id_category" : {/literal}{$product->id_category_default}{literal},
    						"token" : "{/literal}{$token|escape:'html':'UTF-8'}{literal}",
    						"tab" : "AdminProducts",
    						"ajax" : 1 }, afterDeleteProductImage
    				);
    			});
    
    			$('.covered').die().live('click', function(e)
    			{
    				e.preventDefault();
    				id = $(this).parent().parent().parent().attr('id');
    				$("#imageList .cover i").each( function(i){
    					$(this).removeClass('icon-check-sign').addClass('icon-check-empty');
    				});
    				$(this).removeClass('icon-check-empty').addClass('icon-check-sign');
    
    				if (current_shop_id != 0)
    					$('#' + current_shop_id + id).attr('check', true);
    				else
    					$(this).parent().parent().parent().children('td input').attr('check', true);
    				doAdminAjax({
    					"action":"UpdateCover",
    					"id_image":id,
    					"id_product" : {/literal}{$product->id}{literal},
    					"token" : "{/literal}{$token|escape:'html':'UTF-8'}{literal}",
    					"controller" : "AdminProducts",
    					"ajax" : 1 }
    				);
    			});
    
    			$('.image_shop').die().live('click', function()
    			{
    				active = false;
    				if ($(this).attr("checked"))
    					active = true;
    				id = $(this).parent().parent().attr('id');
    				id_shop = $(this).attr("id").replace(id, "");
    				doAdminAjax(
    				{
    					"action":"UpdateProductImageShopAsso",
    					"id_image":id,
    					"id_product":id_product,
    					"id_shop": id_shop,
    					"active":active,
    					"token" : "{/literal}{$token|escape:'html':'UTF-8'}{literal}",
    					"tab" : "AdminProducts",
    					"ajax" : 1
    				});
    			});
    
    			
    
    
    			//$('.fancybox').fancybox();
    		});
    		
    		{/literal}
	   </script>
       
		</div>
        
	</div>
	<div class="col-lg-5">
		<div class="form-group">
			<label class="control-label col-lg-4" for="wholesale_price">
				{l s='Quantity'}
			</label>
			<div class="col-lg-8">
				<input name="quantity" type="text" value="{$quantity}" />
			</div>
		</div>
	
		<div class="form-group">
			<label class="control-label col-lg-4" for="supply_price">
				{l s='Supply price' mod='starfood'}
			</label>
			<div class="col-lg-8">
				<input name="supply_price" id="supply_price" type="text" value="{$product->supply_price}" onchange="this.value = this.value.replace(/,/g, '.');" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4" for="supply_currency_id">
				{l s='Supply currency' mod='starfood'}
			</label>
			<div class="col-lg-8">
				<select name="supply_currency_id" id="supply_currency_id">
					<option value="0">- {l s='Choose (optional)'} -</option>
					{foreach $currencies as $option}
					<option value="{$option['id_currency']}" 
					{if $option['id_currency']==$product->supply_currency_id}selected="selected"{/if} 
						>{$option['name']} ({$option['iso_code']})</option>
					{/foreach}
				</select>
				
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4" for="customs_tax">
				{l s='Customs tax' mod='starfood'}
			</label>
			<div class="col-lg-8">
				<input type="text" id="customs_tax" name="customs_tax" value="{$product->customs_tax|htmlentitiesUTF8}" />
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label col-lg-4" for="supply_cost">
				{l s='Supply cost' mod='starfood'}
			</label>
			<div class="col-lg-8">
				<input type="text" id="supply_cost" name="supply_cost" value="{$product->supply_cost|htmlentitiesUTF8}" />
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label col-lg-4" for="wholesale_price">
				{l s='Wholesale price' mod='starfood'}
			</label>
			<div class="col-lg-8">
				<input name="wholesale_price" id="wholesale_price" type="text" value="{$product->wholesale_price}" onchange="this.value = this.value.replace(/,/g, '.');" />
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label col-lg-4" for="base_price">
				{l s='Retail margin, %' mod='starfood'}
			</label>
			<div class="col-lg-8">
				<input type="text" id="margin_retail" name="margin_retail" value="{$product->margin_retail|htmlentitiesUTF8}" onchange="this.value = this.value.replace(/,/g, '.');"/>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-lg-4" for="base_price">
				{l s='Retail price' mod='starfood'}
			</label>
			<div class="col-lg-8">
				<input type="text" id="base_price" name="base_price" value="{$product->price|htmlentitiesUTF8}" onchange="this.value = this.value.replace(/,/g, '.');"/>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-4" for="base_price">
				{l s='Retail price with tax' mod='starfood'}
			</label>
			<div class="col-lg-8">
				<input type="text"  value="{$price_wt}" disabled="disabled">
			</div>
		</div>
		
		<div class="form-group">
			<label class="control-label col-lg-4">{l s='Tax'}</label>
			<div class="col-lg-8">
				<select name="id_tax_rules_group">
					<option value="0">--</option>
					{foreach $taxes as $tax}
					<option value="{$tax['id_tax_rules_group']}"
					{if $product->id_tax_rules_group == $tax['id_tax_rules_group']}selected="selected"{/if} 
						>{$tax['name']}</option>
					
					{/foreach}
				</select>

			</div>
		</div>
		
		{if isset($specific_prices)}
		{foreach $specific_prices as $specific_price}
		<div class="form-group">
			<label class="control-label col-lg-3">
				{l s='Bulk price' mod='starfood'}
				{*$specific_price['comment']*}
			</label>
		
			<div class="col-lg-9">
				<div class="input-group">
					<span class="input-group-addon">%</span>
					<input type="text" name="specific_price[{$specific_price['id_specific_price']}][margin]" value="{$specific_price['margin']|htmlentitiesUTF8}"/>
					<!-- input type="hidden" name="specific_price[{$specific_price['id_specific_price']}][margin]" value="{$specific_price['margin']|htmlentitiesUTF8}" /-->
					<span class="input-group-addon">{l s='per'}</span>
					<input type="text" name="specific_price[{$specific_price['id_specific_price']}][from_quantity]" value="{$specific_price['from_quantity']|htmlentitiesUTF8}" />
					<span class="input-group-addon">€</span>
					<input readonly type="text" name="specific_price[{$specific_price['id_specific_price']}][price]" value="{$specific_price['price']|htmlentitiesUTF8}"/>
				</div>
			</div>
		</div>
		{/foreach}
		{/if}
		
	</div>
</div>
<div class="form-group">
			<label class="control-label col-lg-3">{l s='Cateogry'}</label>
			<div class="col-lg-9">
			
                {$tree}
                
			</div>
		</div>
	<div class="panel-footer clearfix">
		<button type="submit" class="btn btn-primary pull-right">{l s='Save'}</button>
	</div>

</div>

</form>

