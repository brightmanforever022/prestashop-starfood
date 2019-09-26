{*
* Do not edit the file if you want to upgrade the module in future. The file is added since version 1.0.5
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2016 Globo., Jsc
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}
{extends file="helpers/form/form.tpl"}
{block name="field"}   
	{if $input.type == 'customergroupselect'}
        {if isset($fields_value['groups']) && $fields_value['groups']}
            <div class="col-lg-9">
                {foreach $fields_value['groups'] as $group}
                    <div class="form-group customer_group">
                        <label class="col-lg-3">
                            {$group.name|escape:'html':'UTF-8'}
                        </label>
                        <div class="col-lg-3">
                            {if $input.name == 'GWADVANCEDINVOICE_CUSTOMER_TEMPLATE'}
                                <select name="GWADVANCEDINVOICE_GROUP_{$group.id_group|escape:'html':'UTF-8'}" class="fixed-width-xl">
                                    {if isset($input.options) && $input.options}
                                        {foreach $input.options as $option}
                                            <option {if isset($fields_value["GWADVANCEDINVOICE_GROUP_{$group.id_group}"]) && $fields_value["GWADVANCEDINVOICE_GROUP_{$group.id_group}"] == $option.value} selected="selected" {/if}  value="{$option.value|escape:'html':'UTF-8'}">{$option.name|escape:'html':'UTF-8'}</option>
                                        {/foreach}
                                    {/if}
                                </select>
                            {else}
                                <select name="GWADVANCEDIDELIVERY_GROUP_{$group.id_group|escape:'html':'UTF-8'}" class="fixed-width-xl">
                                    {if isset($input.options) && $input.options}
                                        {foreach $input.options as $option}
                                            <option {if isset($fields_value["GWADVANCEDIDELIVERY_GROUP_{$group.id_group}"]) && $fields_value["GWADVANCEDIDELIVERY_GROUP_{$group.id_group}"] == $option.value} selected="selected" {/if}  value="{$option.value|escape:'html':'UTF-8'}">{$option.name|escape:'html':'UTF-8'}</option>
                                        {/foreach}
                                    {/if}
                                </select>
                            {/if}
                        </div>
                    </div>
                {/foreach}
            {$smarty.block.parent}
            </div>
            
        {/if} 
    {else}
    {$smarty.block.parent}
    {/if}
{/block}