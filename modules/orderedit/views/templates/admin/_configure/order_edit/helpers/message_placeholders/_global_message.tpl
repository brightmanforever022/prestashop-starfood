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
{if $order->hasBeenPaid() || $order->hasBeenDelivered()}
    <div class="orderedit_msg warn">
        <ul>
            {if $order->hasBeenPaid() && $order->hasBeenDelivered()}
                <li class="alert alert-warning">{l s='This order has already been paid and delivered. It is advised to avoid modifying it now.' mod='orderedit'}</li>
            {else}
                {if $order->hasBeenPaid()}
                <li class="alert alert-warning">{l s='This order has already been paid. It is advised to avoid modifying it now.' mod='orderedit'}</li>
                {/if}
                {if $order->hasBeenDelivered()}
                <li class="alert alert-warning">{l s='This order has already been delivered. It is advised to avoid modifying it now.' mod='orderedit'}</li>
                {/if}
            {/if}
        </ul>
    </div>
{/if}