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
<div class="form-group clearfix">
	<label class="control-label col-sm-3"><span class="label-tooltip">{$title_name|escape:'html':'UTF-8'}</span></label>

	<div class="col-sm-9">
		
		<div class="fixed-width-l clearfix">
			<div class="col-sm-3">
				<div class="row">
					<span class="switch prestashop-switch">
						<input id="{$label_id|escape:'html':'UTF-8'}_yes" type="radio" value="1" name="{$label_name|escape:'html':'UTF-8'}" {if isset($is_checked) && $is_checked}checked{/if} 
							{if isset($input_onchange)}onchange="{$input_onchange|escape:'html':'UTF-8'}"{/if}>
						
						<label for="{$label_id|escape:'html':'UTF-8'}_yes">
							{l s='Yes' mod='newsletterpro'}
						</label>

						<input id="{$label_id|escape:'html':'UTF-8'}_no" type="radio" value="0" name="{$label_name|escape:'html':'UTF-8'}" {if isset($is_checked) && !$is_checked}checked{/if}
							{if isset($input_onchange)}onchange="{$input_onchange|escape:'html':'UTF-8'}"{/if}>

						<label for="{$label_id|escape:'html':'UTF-8'}_no">
							{l s='No' mod='newsletterpro'}
						</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>

		</div>
		{if isset($description)}
			<p class="help-block">{$description|escape:'quotes':'UTF-8'}</p>
		{/if}
	</div>
</div>