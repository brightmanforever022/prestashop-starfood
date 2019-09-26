{**
* OrderEdit
*
* @category  Module
* @author    silbersaiten <info@silbersaiten.de>
* @support   silbersaiten <support@silbersaiten.de>
* @copyright 2015 silbersaiten
* @version   1.0.0
* @link      http://www.silbersaiten.de
* @license   See joined file licence.txt
*}
<!-- Change status form -->
<form action="{$current_index|escape:'html':'UTF-8'}&vieworder&token={$token|escape:'html':'UTF-8'}" method="post" id="stateSubmitForm">
    <div class="row">
        <div class="col-lg-9">
            <select id="id_order_state" name="id_order_state">
            {foreach from=$states item=state}
                <option value="{$state['id_order_state']|escape:'html':'UTF-8'}">{$state['name']|stripslashes|escape:'html':'UTF-8'}</option>
            {/foreach}
            </select>
            <input type="hidden" name="id_order" value="{$order->id|escape:'html':'UTF-8'}" />
        </div>
        <div class="col-lg-3">
            <input type="submit" name="submitState" value="{l s='Add' mod='orderedit'}" class="btn btn-primary" />
        </div>
    </div>
</form>
<br />

<!-- History of status -->
<table cellspacing="0" cellpadding="0" class="table history-status" style="width: 100%;">
    <colgroup>
        <col width="1%">
        <col width="">
        <col width="20%">
        <col width="20%">
        <col width="1%">
    </colgroup>
{foreach from=$history item=row key=key}
    {if ($key == 0)}
    <tr>
        <th><img src="../img/os/{$row['id_order_state']|escape:'html':'UTF-8'}.gif" /></th>
        <th>{$row['ostate_name']|stripslashes|escape:'html':'UTF-8'}</th>
        <th>{if $row['employee_lastname']}{$row['employee_firstname']|stripslashes|escape:'html':'UTF-8'} {$row['employee_lastname']|stripslashes|escape:'html':'UTF-8'}{/if}</th>
        <th>{dateFormat date=$row['date_add'] full=true}</th>
        <th>
            <a href="#" class="delete_order_status btn btn-default" rel="{$row['id_order_history']|escape:'html':'UTF-8'}">
                <i class="icon-trash"></i>
            </a>
        </th>
    </tr>
    {else}
    <tr class="{if ($key % 2)}alt_row{/if}">
        <td><img src="../img/os/{$row['id_order_state']|escape:'html':'UTF-8'}.gif" /></td>
        <td>{$row['ostate_name']|stripslashes|escape:'html':'UTF-8'}</td>
        <td>{if $row['employee_lastname']}{$row['employee_firstname']|stripslashes|escape:'html':'UTF-8'} {$row['employee_lastname']|stripslashes|escape:'html':'UTF-8'}{else}&nbsp;{/if}</td>
        <td>{dateFormat date=$row['date_add'] full=true}</td>
        <td>
            <a href="#" class="delete_order_status btn btn-default" rel="{$row['id_order_history']|escape:'html':'UTF-8'}">
                <i class="icon-trash"></i>
            </a>
        </td>
    </tr>
    {/if}
{/foreach}
</table>