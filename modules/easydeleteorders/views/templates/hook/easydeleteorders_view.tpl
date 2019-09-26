{**
* Easy Delete Orders
*
* NOTICE OF LICENSE
*
* This product is licensed for one customer to use on one installation (test stores and multishop included).
* Site developer has the right to modify this module to suit their needs, but can not redistribute the module in
* whole or in part. Any other use of this module constitues a violation of the user agreement.
*
* DISCLAIMER
*
* NO WARRANTIES OF DATA SAFETY OR MODULE SECURITY
* ARE EXPRESSED OR IMPLIED. USE THIS MODULE IN ACCORDANCE
* WITH YOUR MERCHANT AGREEMENT, KNOWING THAT VIOLATIONS OF
* PCI COMPLIANCY OR A DATA BREACH CAN COST THOUSANDS OF DOLLARS
* IN FINES AND DAMAGE A STORES REPUTATION. USE AT YOUR OWN RISK.
*
*  @author    idnovate.com <info@idnovate.com>
*  @copyright 2016 idnovate.com
*  @license   See above
*}

{if version_compare($smarty.const._PS_VERSION_,'1.5','<')}
    {literal}
    <script type="text/javascript">
        if (document.URL.indexOf('id_order') > 0) {
            $(document).ready(function() {
                var id_order = '{/literal}{$smarty.get.id_order|default:0|escape:'htmlall':'UTF-8'}{literal}'
                var controller = 'AdminOrders';
                var action = 'deleteorder';
                var confirm = '{/literal}{$confirm|escape:'htmlall':'UTF-8'}{literal}';
                confirm = confirm.replace('~', id_order);
                var html = ' <a href="{/literal}{$admin_base_dir|escape:'htmlall':'UTF-8'}{literal}&' + action + '" onclick="if (confirm(\'' + confirm + '\')){ return true; }else{ event.stopPropagation(); event.preventDefault();};" ><img src="../img/admin/delete.gif" alt="{/literal}{$action_delete|escape:'htmlall':'UTF-8'}{literal}"> {/literal}{$action_delete|escape:'htmlall':'UTF-8'}{literal}</a>';
                $(this).find("a[href='javascript:window.print()']").append(html);
            });
        } else {
            $(document).ready(function() {
                $('.table.table tbody tr').each(function(){
                    var id_order = $(this).find('td:nth-child(2)').html().trim();
                    var token = '{/literal}{$smarty.get.token|escape:'htmlall':'UTF-8'}{literal}';
                    var controller = 'AdminOrders';
                    var action = 'deleteorder';
                    var confirm = '{/literal}{$confirm|escape:'htmlall':'UTF-8'}{literal}';
                    confirm = confirm.replace('~', id_order);
                    var html = '<a href="{/literal}{$admin_base_dir|escape:'htmlall':'UTF-8'}{literal}&id_order=' + id_order + '&' + action + '&token=' + token + '" onclick="if (confirm(\'' + confirm + '\')){ return true; }else{ event.stopPropagation(); event.preventDefault();};" ' + 'class="delete" title="{/literal}{$action|escape:'htmlall':'UTF-8'}{literal}"><img src="../img/admin/delete.gif" alt="{/literal}{$action|escape:'htmlall':'UTF-8'}{literal}" ' + 'title="{/literal}{$action|escape:'htmlall':'UTF-8'}{literal}" /></a>';
                    $(this).find('td:last').append(html);
                })
            });
        }
    </script>
    {/literal}
{else}
    {literal}
    <script type="text/javascript">
        var token = '{/literal}{$smarty.get.token|escape:'htmlall':'UTF-8'}{literal}';
        var controller = 'AdminOrders';
        var action = 'easydeleteorder=1';
        {/literal}{if version_compare($smarty.const._PS_VERSION_,'1.5','<')}{literal}
            var action = 'deleteorder';
        {/literal}{/if}{literal}

        if (document.URL.indexOf('id_order') > 0 && document.URL.indexOf('deleteorder') < 0 && document.URL.indexOf('easydeleteorder') < 0) {
            $(document).ready(function(){
                var id_order = '{/literal}{if isset($smarty.get.id_order)}{$smarty.get.id_order|escape:'htmlall':'UTF-8'}{/if}{literal}';
                var action = 'easydeleteorder=2';
                var confirm = '{/literal}{$confirm|escape:'htmlall':'UTF-8'}{literal}';
                confirm = confirm.replace('~', id_order);
                {/literal}{if version_compare($smarty.const._PS_VERSION_,'1.6','>=')}{literal}
                    var toolbar = $('ul#toolbar-nav').prepend('<li><a id="page-header-desc-order-delete" class="toolbar_btn" href="{/literal}{$admin_base_dir|escape:'htmlall':'UTF-8'}{literal}&' + action + '" onclick="if (confirm(\'' + confirm + '\')){ return true; }else{ event.stopPropagation(); event.preventDefault();};" title="{/literal}{$action_delete|escape:'htmlall':'UTF-8'}{literal}"><i class="process-icon-delete"></i><div>{/literal}{$action_delete|escape:'htmlall':'UTF-8'}{literal}</div></a></li>');
                {/literal}{/if}{literal}
                var html = '<a class="btn btn-default" href="{/literal}{$admin_base_dir|escape:'htmlall':'UTF-8'}{literal}&' + action + '" onclick="if (confirm(\'' + confirm + '\')){ return true; }else{ event.stopPropagation(); event.preventDefault();};" ><i class="icon-trash"></i> {/literal}{$action_delete|escape:'htmlall':'UTF-8'}{literal}</a>';
                
                {/literal}{if version_compare($smarty.const._PS_VERSION_,'1.5','<')}{literal}
                    $("#content div.col-lg-7 .panel:first .hidden-print:first").prepend(html);
                {/literal}{elseif version_compare($smarty.const._PS_VERSION_,'1.6','>=')}{literal}
                    $("#content div.col-lg-7 .panel:first .hidden-print:first").prepend(html);
                {/literal}{else}{literal}
                    var html = '<a class="toolbar_btn" href="{/literal}{$admin_base_dir|escape:'htmlall':'UTF-8'}{literal}&' + action + '" onclick="if (confirm(\'' + confirm + '\')){ return true; }else{ event.stopPropagation(); event.preventDefault();};" ><span class="process-icon-delete process-icon-delete"></span> <div>{/literal}{$action_delete|escape:'htmlall':'UTF-8'}{literal}</div></a>';
                    $('ul.cc_button').prepend('<li>' + html + '</li>');
                {/literal}{/if}{literal}
            });
        } else {
            $(document).ready(function(){
                $('.table.order tbody tr').each(function(){
                    if ($.isNumeric($(this).find('td:nth-child(2)').html().trim())) {
                        var id_order = $(this).find('td:nth-child(2)').html().trim();
                    } else {
                        var id_order = $(this).find('td:nth-child(1)').html().trim();
                    }
                    var confirm = '{/literal}{$confirm|escape:'htmlall':'UTF-8'}{literal}';
                    confirm = confirm.replace('~', id_order);
                    var html = '<a href="{/literal}{$admin_base_dir|escape:'htmlall':'UTF-8'}{literal}&id_order=' + id_order + '&' + action + '&token=' + token + '" onclick="if (confirm(\'' + confirm + '\')){return true;}else{event.stopPropagation(); event.preventDefault();};" title="{/literal}{$action|escape:'htmlall':'UTF-8'}{literal}" class="btn btn-default"> <i class="icon-trash"></i> {/literal}{if version_compare($smarty.const._PS_VERSION_,'1.6','<')}{literal}<img src="../img/admin/delete.gif" alt="delete">{/literal}{else}{$action|escape:'htmlall':'UTF-8'}{/if}{literal}</a>';
                    {/literal}{if version_compare($smarty.const._PS_VERSION_,'1.5','<')}{literal}
                        $(this).find('td:last div').append(html);
                    {/literal}{elseif version_compare($smarty.const._PS_VERSION_,'1.6','>=')}{literal}
                        $(this).find('td:last').append(html);
                    {/literal}{else}{literal}
                        $(this).find('td:last').append(html);
                    {/literal}{/if}{literal}
                })
                {/literal}{if version_compare($smarty.const._PS_VERSION_,'1.5','>=')}{literal}
                    var confirm = '{/literal}{$action_delete_bulk_confirm|escape:'htmlall':'UTF-8'}{literal}';
                    var dropdown_menu = $('div.bulk-actions').find('ul.dropdown-menu').append('<li><a href="#" onclick="if (confirm(\'' + confirm + '\')){sendBulkAction($(this).closest(\'form\').get(0), \'submitBulkdeleteorder=1\');}"><i class="icon-trash"></i>&nbsp;{/literal}{$action_delete_bulk|escape:'htmlall':'UTF-8'}{literal}</a></li>');
                    {/literal}{if $msg neq ''}{literal}
                    $('div#ajax_confirmation').next().show().append('{/literal}{$msg|escape:'quotes':'UTF-8'}{literal}');
                    {/literal}{/if}{literal}
                {/literal}{/if}{literal}
            });
        }
    </script>
    {/literal}
{/if}
