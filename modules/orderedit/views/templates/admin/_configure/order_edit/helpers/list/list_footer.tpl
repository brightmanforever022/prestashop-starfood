{*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

			</table>
			{if $bulk_actions}
				<p>
					{foreach $bulk_actions as $key => $params}
						{if $key == 'changeStatuses'}
						<select name="bulk_order_state">
							{foreach from=$order_states item=order_state}
							<option value="{$order_state.id_order_state|escape:'html':'UTF-8'}">{$order_state.name|escape:'html':'UTF-8'}</option>
							{/foreach}
						</select>
						{/if}
						<input type="submit" class="button" name="submitBulk{$key|escape:'html':'UTF-8'}{$table|escape:'html':'UTF-8'}" value="{$params.text|escape:'html':'UTF-8'}" {if isset($params.confirm)}onclick="return confirm('{$params.confirm|escape:'html':'UTF-8'}');"{/if} />
					{/foreach}
				</p>
			{/if}
		</td>
	</tr>
</table>
{if !$simple_header}
	<input type="hidden" name="token" value="{$token|escape:'html':'UTF-8'}" />
	</form>
{/if}


{hook h='displayAdminListAfter'}
{if isset($name_controller)}
	{capture name=hookName assign=hookName}display{$name_controller|ucfirst|escape:'html':'UTF-8'}ListAfter{/capture}
	{hook h=$hookName}
{elseif isset($smarty.get.controller)}
	{capture name=hookName assign=hookName}display{$smarty.get.controller|ucfirst|htmlentities|escape:'html':'UTF-8'}ListAfter{/capture}
	{hook h=$hookName}
{/if}

{block name="after"}{/block}