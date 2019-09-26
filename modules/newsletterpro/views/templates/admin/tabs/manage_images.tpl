{*
* 2013-2015 Ovidiu Cimpean
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright 2013-2015 Ovidiu Cimpean
* @version   Release: 4
*}

{if isset($fix_document_write) && $fix_document_write == 1}
<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: none;" class="images">
{else}
<script type="text/javascript"> 
	if(window.location.hash == '#manageImages') {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: block;" class="images">');
	{rdelim} else {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: none;" class="images">');
	{rdelim} 
</script>
{/if}

	<h4>{l s='Manage Images' mod='newsletterpro'}</h4>
	<div class="separation"></div>

	<div class="form-group clearfix">
		<form id="upload-image-form" class="defaultForm" method="post" enctype="multipart/form-data" {if isset($style)}style="{$style|escape:'quotes':'UTF-8'}"{/if}>
			<div class="form-inline">
				<div class="col-sm-6">
					<div class="input-group">
						<span class="input-group-addon">{l s='Upload image' mod='newsletterpro'}</span>
						<input class="form-control" type="file" name="upload_image"/>
					</div>
				</div>

				<div class="col-sm-2">
					<div class="input-group">
						<span class="input-group-addon">{l s='Width' mod='newsletterpro'}</span>
						<input class="form-control text-center" type="text" name="upload_image_width" id="upload-image-width" value="">
					</div>
				</div>

				<div class="col-sm-4">
					<a href="javascript:{}" id="upload-image" class="btn btn-default">
						<i class="icon icon-upload on-left"></i> <span>{l s='Upload' mod='newsletterpro'}</span>
					</a>
				</div>
			</div>
		</form>		
	</div>

	
	<div class="images-grid-box">
		<table id="images-list" class="table table-bordered images-list">
			<thead>
				<tr>
					<th class="preview" data-template="preview">&nbsp;</th>
					<th class="dimensions" data-template="dimensions">{l s='Dimensions' mod='newsletterpro'}</th>
					<th class="size" data-field="size">{l s='Size' mod='newsletterpro'}</th>			
					<th class="date" data-field="date">{l s='Date Add' mod='newsletterpro'}</th>
					<th class="actions" data-template="actions">{l s='Actions' mod='newsletterpro'}</th>
				</tr>
			</thead>
		</table>
	</div>
	<br>
</div>