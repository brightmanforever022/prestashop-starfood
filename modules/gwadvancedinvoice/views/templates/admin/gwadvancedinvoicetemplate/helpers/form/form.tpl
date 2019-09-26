{*
* Do not edit the file if you want to upgrade the module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2016 Globo ., Jsc
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}
{extends file="helpers/form/form.tpl"}
{block name="field"}
	{if $input.type == 'choose_design'}
        <div class="col-lg-9">
            {if isset($input.choosed)}
                <div class="row">
                     <div id="templateChosse">
                         <div class="col-xs-6 col-md-6">
                            <span class="thumbnail">
                              <img src="{$module_dir|escape:'htmlall':'UTF-8'}gwadvancedinvoice/{$input.choosed.large_thumbnail|escape:'htmlall':'UTF-8'}" alt=""/>
                            </span> 
                            <input type="hidden" value="{$input.choosed.id|escape:'htmlall':'UTF-8'}" name="choose_design" />
                        </div>
                    </div>
        		</div>
            {else}
        		<div class="row">
                     <div id="templateChosse"></div>
        		</div>
                <div id="templateChosse_box">
                </div>
            {/if}
        </div>
	{/if}
    {if $input.type == 'watermark_lang'}
        <div class="col-lg-9">
    		<div class="row">
    			{foreach from=$languages item=language}
    				{if $languages|count > 1}
    					<div class="translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
    				{/if}
    					<div class="col-lg-12">
                            <div  class="col-lg-3">   
                                {if isset($fields_value[$input.name][$language.id_lang]) && $fields_value[$input.name][$language.id_lang]|escape:'htmlall':'UTF-8'}
        						      <div class="col-lg-12">
                                        <img src="{$fields_value['image_baseurl']|escape:'htmlall':'UTF-8'}{$fields_value[$input.name][$language.id_lang]|escape:'htmlall':'UTF-8'}" class="img-thumbnail" />
        						      </div>
                                      <div class="col-lg-12">
                                            <input type="checkbox" name="{$input.name|escape:'htmlall':'UTF-8'}_remove_{$language.id_lang|escape:'htmlall':'UTF-8'}" value="1" /><span>{l s='Remove %1$s' sprintf=$input.label mod='gwadvancedinvoice'}</span>
                                      </div>
                                {/if}
                            </div>
                            <div class="clear"></div>
                            <div class="col-lg-6">
        						<div class="dummyfile input-group">
        							<input id="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}" type="file" name="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}" class="hide-file-upload" />
        							<span class="input-group-addon"><i class="icon-file"></i></span>
        							<input id="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}-name" type="text" class="disabled" name="filename" readonly />
        							<span class="input-group-btn">
        								<button id="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
        									<i class="icon-folder-open"></i> {l s='Choose a file' mod='gwadvancedinvoice'}
        								</button>
        							</span>
        						</div>
                            </div>
    					</div>
    				{if $languages|count > 1}
    					<div class="col-lg-2">
    						<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
    							{$language.iso_code|escape:'htmlall':'UTF-8'}
    							<span class="caret"></span>
    						</button>
    						<ul class="dropdown-menu">
    							{foreach from=$languages item=lang}
    							<li><a href="javascript:hideOtherLanguage({$lang.id_lang|escape:'htmlall':'UTF-8'});" tabindex="-1">{$lang.name|escape:'htmlall':'UTF-8'}</a></li>
    							{/foreach}
    						</ul>
    					</div>
    				{/if}
    				{if $languages|count > 1}
    					</div>
    				{/if}
    				<script>
    				$(document).ready(function(){
    					$('#{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}-selectbutton').click(function(e){
    						$('#{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}').trigger('click');
    					});
    					$('#{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}').change(function(e){
    						var val = $(this).val();
    						var file = val.split(/[\\/]/);
    						$('#{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}-name').val(file[file.length-1]);
    					});
    				});
    			</script>
    			{/foreach}
                <div>
                    {foreach from=$languages item=language}
    				{if $languages|count > 1}
    					<div class="translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
    				{/if}
    					<div class="col-lg-12">
                            <div class="col-lg-4">
                                <label>{l s='Watermark Text' mod='gwadvancedinvoice'}</label>
                                <input id="{$input.name|escape:'htmlall':'UTF-8'}text_{$language.id_lang|escape:'htmlall':'UTF-8'}" type="text" name="{$input.name|escape:'htmlall':'UTF-8'}text_{$language.id_lang|escape:'htmlall':'UTF-8'}" value="{if isset($fields_value['watermarktext'][$language.id_lang]) && $fields_value['watermarktext'][$language.id_lang]|escape:'htmlall':'UTF-8'}{$fields_value['watermarktext'][$language.id_lang]|escape:'htmlall':'UTF-8'}{/if}" />
                            </div>
                            <div class="col-lg-4">
                                <label>{l s='Watermark Text Font' mod='gwadvancedinvoice'}</label>
                                <select id="{$input.name|escape:'htmlall':'UTF-8'}font_{$language.id_lang|escape:'htmlall':'UTF-8'}"  name="{$input.name|escape:'htmlall':'UTF-8'}font_{$language.id_lang|escape:'htmlall':'UTF-8'}">
                                    {if isset($fields_value['watermarkfont'][$language.id_lang]) && $fields_value['watermarkfont'][$language.id_lang]|escape:'htmlall':'UTF-8'}
                                        {foreach $fields_value['fonts'] as $key=> $font}
                                            <option {if $key == $fields_value['watermarkfont'][$language.id_lang]} selected="selected" {/if} value="{$key|escape:'htmlall':'UTF-8'}">{$font|escape:'htmlall':'UTF-8'}</option>
                                        {/foreach}
                                    {else}
                                        {foreach $fields_value['fonts'] as $key=> $font}
                                            <option value="{$key|escape:'htmlall':'UTF-8'}">{$font|escape:'htmlall':'UTF-8'}</option>
                                        {/foreach}
                                    {/if}
                                </select>
                                
                            </div>
                            <div class="col-lg-4">
                                <label>{l s='Watermark Size' mod='gwadvancedinvoice'}</label>
                                <input id="{$input.name|escape:'htmlall':'UTF-8'}size_{$language.id_lang|escape:'htmlall':'UTF-8'}" type="text" name="{$input.name|escape:'htmlall':'UTF-8'}size_{$language.id_lang|escape:'htmlall':'UTF-8'}" value="{if isset($fields_value['watermarksize'][$language.id_lang]) && $fields_value['watermarksize'][$language.id_lang]|escape:'htmlall':'UTF-8'}{$fields_value['watermarksize'][$language.id_lang]|escape:'htmlall':'UTF-8'}{/if}" />
                            </div>
                        </div>
                        <script>
                            $(document).ready(function(){
                                $('select[name="{$input.name|escape:'htmlall':'UTF-8'}font_{$language.id_lang|escape:'htmlall':'UTF-8'}"]').chosen();
                            });
                        </script>
                    {if $languages|count > 1}
    					</div>
    				{/if} 
                    {/foreach} 
                </div>
    		</div>
        </div>
	{/if}
    {if $input.type == 'margin_layout'}
        <div class="col-lg-9">
            <div class="row">
                <div class="col-lg-4">
                    <label>{l s='Header margin' mod='gwadvancedinvoice'}</label>
                    <input type="text" name="mgheader" id="mgheader" value="{$fields_value['mgheader']|escape:'htmlall':'UTF-8'}" class="" size="255"/>
                </div>
                <div class="col-lg-4">
                    <label>{l s='Content margin' mod='gwadvancedinvoice'}</label>
                    <input type="text" name="mgcontent" id="mgcontent" value="{$fields_value['mgcontent']|escape:'htmlall':'UTF-8'}" class="" size="255"/>
                    <div class="help-block">{l s='Left - Top - Right - Bottom' mod='gwadvancedinvoice'}</div>
                </div>
                <div class="col-lg-4">
                    <label>{l s='Footer margin' mod='gwadvancedinvoice'}</label>
                    <input type="text" name="mgfooter" id="mgfooter" value="{$fields_value['mgfooter']|escape:'htmlall':'UTF-8'}" class="" size="255"/>
                </div>
            </div>
        </div> 
    {/if}
    {if $input.type == 'textarea_fullwidth'}
        <div class="col-lg-9">
            {foreach from=$languages item=language}
    				{if $languages|count > 1}
    					<div class="translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
    				{/if}
                    {if $languages|count > 1}
                        <div class="col-sm-2">
        					<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
        						{$language.iso_code|escape:'htmlall':'UTF-8'}
        						<span class="caret"></span>
        					</button>
        					<ul class="dropdown-menu">
        						{foreach from=$languages item=lang}
        						<li><a href="javascript:hideOtherLanguage({$lang.id_lang|escape:'htmlall':'UTF-8'});" tabindex="-1">{$lang.name|escape:'htmlall':'UTF-8'}</a></li>
        						{/foreach}
        					</ul>
                        </div>
        			{/if}
                    {if $languages|count > 1}
                    </div>
                    {/if}
           {/foreach}
        </div>
        <div class="clear"></div>
        <br />
        <div class="col-lg-12">
            {assign var=use_textarea_autosize value=true}
    		{if isset($input.lang) AND $input.lang}
        		{foreach $languages as $language}
            		{if $languages|count > 1}
                		<div class="form-group translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}"{if $language.id_lang != $defaultFormLanguage} style="display:none;"{/if}>
                			<div class="col-lg-12">
                		{/if}
                				<textarea name="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}" class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{if isset($input.class)} {$input.class|escape:'htmlall':'UTF-8'}{/if}{else}{if isset($input.class)} {$input.class|escape:'htmlall':'UTF-8'}{else} textarea-autosize{/if}{/if}">{$fields_value[$input.name][$language.id_lang]|escape:'htmlall':'UTF-8'}</textarea>
                		{if $languages|count > 1}
                			</div>
                		</div>
            		{/if}                             
        		{/foreach}
    		{else}
    			<textarea name="{$input.name|escape:'htmlall':'UTF-8'}" id="{if isset($input.id)}{$input.id|escape:'htmlall':'UTF-8'}{else}{$input.name|escape:'htmlall':'UTF-8'}{/if}" {if isset($input.cols)}cols="{$input.cols|escape:'htmlall':'UTF-8'}"{/if} {if isset($input.rows)}rows="{$input.rows|escape:'htmlall':'UTF-8'}"{/if} class="{if isset($input.autoload_rte) && $input.autoload_rte}rte autoload_rte{if isset($input.class)} {$input.class|escape:'htmlall':'UTF-8'}{/if}{else} textarea-autosize{/if}">{$fields_value[$input.name]|escape:'htmlall':'UTF-8'}</textarea>
    		{/if}
        </div>
    {/if}
    {if $input.type == 'template_config'}
    <div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">{l s='Customize template style' mod='gwadvancedinvoice'}</div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class="row">
                    {foreach $fields_value['template_config'] as $template_config}
                    {if $template_config.type=='color'}
             			<div class="col-lg-6">
                            <div class="form-group">
                				<div class="">
                                    <label class="col-lg-6 control-label">
                                        {if isset($template_config.hint) && $template_config.hint !=''}
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{$template_config.hint|escape:'htmlall':'UTF-8'}">
                                        {/if}
                                        {l s='%1$s' sprintf=$template_config.title mod='gwadvancedinvoice'}
                                        {if isset($template_config.hint) && $template_config.hint !=''}
                                            </span>
                                        {/if}
                                    </label>
                					<div class="input-group col-lg-6">
                						<input type="color"
                						data-hex="true"
                						{if isset($input.class)} class="{$input.class|escape:'htmlall':'UTF-8'}"
                						{else} class="template_config color mColorPickerInput"{/if}
                						name="template_config[{$template_config.name|escape:'htmlall':'UTF-8'}]"
                						value="{$template_config.value|escape:'html':'UTF-8'}" />
                					</div>
                				</div>
                			</div>
                		</div>
                    {elseif $template_config.type=='text'}
                        <div class="col-lg-6">
                            <div class="form-group">
                				<div class="">
                                    <label class="col-lg-6 control-label">
                                    {if isset($template_config.hint) && $template_config.hint !=''}
                                        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{$template_config.hint|escape:'htmlall':'UTF-8'}">
                                    {/if}
                                    {l s='%1$s' sprintf=$template_config.title mod='gwadvancedinvoice'}</label>
                					{if isset($template_config.hint) && $template_config.hint !=''}
                                        </span>
                                    {/if}
                                    <div class="col-lg-6">
                						<input class="template_config" type="text" name="template_config[{$template_config.name|escape:'htmlall':'UTF-8'}]"
                						value="{$template_config.value|escape:'html':'UTF-8'}" />
                					</div>
                				</div>
                			</div>
                		</div>
                    {elseif $template_config.type=='font'}
                        <div class="col-lg-6">
                            <div class="form-group">
                				<div class="">
                                    <label class="col-lg-6 control-label">
                                        {if isset($template_config.hint) && $template_config.hint !=''}
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{$template_config.hint|escape:'htmlall':'UTF-8'}">
                                        {/if}
                                    {l s='%1$s' sprintf=$template_config.title mod='gwadvancedinvoice'}</label>
                					{if isset($template_config.hint) && $template_config.hint !=''}
                                        </span>
                                    {/if}
                                    <div class="col-lg-6">
                                        <select name="template_config[{$template_config.name|escape:'htmlall':'UTF-8'}]">
                                            {foreach $fields_value['fonts'] as $key=> $font}
                                                <option value="{$key|escape:'htmlall':'UTF-8'}" {if $key == $template_config.value} selected="selected" {/if}>{$font|escape:'htmlall':'UTF-8'}</option>
                                            {/foreach}
                                        </select>
                					</div>
                				</div>
                			</div>
                		</div>
                        <script>
                            $(document).ready(function(){
                                $('select[name="template_config[{$template_config.name|escape:'htmlall':'UTF-8'}]"]').chosen();
                            });
                        </script>
                    {/if}
                    {/foreach}
                </div>
            </div>
        </div>
    </div>
    </div>
    {/if}
    {if $input.type == 'productlist'}
        <div style="display:none;">
            <div id="templateChosse_box">
                <iframe id="templateChosse_box_iframe" src=""></iframe>
            </div>
            <a id="popup_template" href="#templateChosse_box">{l s='Change Template' mod='gwadvancedinvoice'}</a>
        </div>
        <div class="col-lg-9">
            {foreach from=$languages item=language}
    				{if $languages|count > 1}
    					<div class="translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
    				{/if}
                    {if $languages|count > 1}
                        <div class="col-sm-2">
        					<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
        						{$language.iso_code|escape:'htmlall':'UTF-8'}
        						<span class="caret"></span>
        					</button>
        					<ul class="dropdown-menu">
        						{foreach from=$languages item=lang}
        						<li><a href="javascript:hideOtherLanguage({$lang.id_lang|escape:'htmlall':'UTF-8'});" tabindex="-1">{$lang.name|escape:'htmlall':'UTF-8'}</a></li>
        						{/foreach}
        					</ul>
                        </div>
        			{/if}
                    {if $languages|count > 1}
                    </div>
                    {/if}
           {/foreach}
        </div>
        <div class="clear"></div>
        <br />
        <div class="col-lg-12">
    		<div class="row">
                <div id="slides"  style="position: relative;">
                    <table id="productlist" width="100%" border="0" cellpadding="0" cellspacing="0">
            			<tr class="title">
                            {if isset($fields_value['productcolumns'][$fields_value['id_language']]) && count($fields_value['productcolumns'][$fields_value['id_language']]) > 0}
                                {foreach $fields_value['productcolumns'][$fields_value['id_language']]->title as $key=>$productcolumn}
                                    <th style="width:{$fields_value['productcolumns'][$language.id_lang]->widthtitle[$key]|escape:'htmlall':'UTF-8'}%;">
                                        <input type="hidden" name="widthtitle[]" class="widthtitle" value="{$fields_value['productcolumns'][$language.id_lang]->widthtitle[$key]|escape:'htmlall':'UTF-8'}" />
                                        <div class="slide_{$key+1|escape:'htmlall':'UTF-8'} slide_column">
                                        {foreach from=$languages item=language}
                        				{if $languages|count > 1}
                        					<div class="translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                        				{/if}
                                        <div class="" style="position: relative;">
                                            <div class="col-sm-11">
           								         <input type="text" name="colums_title_{$language.id_lang|escape:'htmlall':'UTF-8'}[]" value="{$fields_value['productcolumns'][$language.id_lang]->title[$key]|escape:'htmlall':'UTF-8'}" />
                    						</div>
                                            <a class="remove_column" href="" style="position: absolute;top:7px; right:2px;color:#555;">
                						      <i class="icon-trash"></i>
                							</a>
                                            <div class="clear"></div>
                                        </div>
                        				{if $languages|count > 1}
                        					</div>
                        				{/if}
                            			{/foreach}
                                        </div>
                                    </th>
                                {/foreach}
                            {/if}
            			</tr>
            			<tr class="content">
                            {if isset($fields_value['productcolumns'][$fields_value['id_language']]) && count($fields_value['productcolumns'][$fields_value['id_language']]) > 0}
                				{foreach $fields_value['productcolumns'][$fields_value['id_language']]->title as $key=>$productcolumn}
                                    <td>
                                        <div class="slide_{$key+1|escape:'htmlall':'UTF-8'} slide_column">
                                        {foreach from=$languages item=language}
                        				{if $languages|count > 1}
                        					<div class="translatable-field lang-{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                        				{/if}
                                        <div class="">
                                            <div class="col-sm-12">
                                                <textarea class="col-sm-12" name="colums_content_{$language.id_lang|escape:'htmlall':'UTF-8'}[]">{$fields_value['productcolumns'][$language.id_lang]->content[$key]|escape:'htmlall':'UTF-8'}</textarea>
                    						</div>
                                            <div class="col-sm-12">
                                                <div class="btn-group align-group">
                                                    <button rel="left" type="button" class="colums_align_bt btn btn-default {if $fields_value['productcolumns'][$language.id_lang]->align[$key] == 'left'} active {/if}" aria-label="Left Align">
                                                        <i class="icon-align-left"></i>
                                                    </button>
                                                    <button rel="center" type="button" class="colums_align_bt btn btn-default {if $fields_value['productcolumns'][$language.id_lang]->align[$key] == 'center'} active {/if}" aria-label="Center Align">
                                                        <i class="icon-align-center"></i>
                                                    </button>
                                                    <button rel="right" type="button" class="colums_align_bt btn btn-default {if $fields_value['productcolumns'][$language.id_lang]->align[$key] == 'right'} active {/if}" aria-label="Right Align">
                                                        <i class="icon-align-right"></i>
                                                    </button>
                                                    <input type="hidden" class="align_value" name="colums_align_{$language.id_lang|escape:'htmlall':'UTF-8'}[]" value="{$fields_value['productcolumns'][$language.id_lang]->align[$key]|escape:'htmlall':'UTF-8'}" />
                                                </div>
                                            </div>
                                        </div>
                        				{if $languages|count > 1}
                        					</div>
                        				{/if}
                            			{/foreach}
                                        </div>
                                    </td>
                                {/foreach}
                            {/if}
            			</tr>																	
            		</table>
                    <a id="new_column" style="position: absolute;top:0;right: -20px;padding: 4px;" class="btn btn-default" href="" title="{l s='Add column' mod='gwadvancedinvoice'}">
        		      <i class="icon-plus"></i>
        			</a>
                </div>
    		</div>
        </div>
        <script>
			$(document).ready(function(){
				$('.colums_align_bt').live('click',function(e){
					val = $(this).attr('rel');
                    $(this).parent('.align-group').children('.colums_align_bt').removeClass('active');
                    $(this).addClass('active');
                    $(this).parent('.align-group').children('.align_value').val(val);
				});
			});
		</script>
	{/if}
	{$smarty.block.parent}
{/block}