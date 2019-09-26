/**
 * OrderEdit
 *
 * @category  Module
 * @author    silbersaiten <info@silbersaiten.de>
 * @support   silbersaiten <support@silbersaiten.de>
 * @copyright 2015 silbersaiten
 * @version   1.1.5
 * @link      http://www.silbersaiten.de
 * @license   See joined file licence.txt
 */
var orderedit_list = {
    init: function() {
        orderedit_list.createListDropdown();
        
        $(document).on('click', 'a.orderedit_edit', function(e){
            e.preventDefault();
            
            var id_order = $(this).attr('rel');
            
            $.post(orderedit_ajax, {action: 'getEditLink', id_order: id_order, iem: iem, iemp: iemp, id_shop: orderedit_id_shop}, function(data){
                if (typeof(data) != 'undefined' && data != 'false') {
                    var domain = window.location.origin,
                    path = window.location.pathname;
                    
                    window.location = domain + path + '?' + data;
                }
            });
            
            return false;
        })
    },
    
    createListDropdown: function() {
        var parent = $('table.table.order');
        
        if (parent.length) {
            var items = parent.find('tbody tr');
            
            if (items.length) {
                items.each(function(){
                    var last_cell = $(this).find('td:last');

                    var checkbox = $(this).find('td:first input[type=checkbox]');

                    if (checkbox.length > 0) {
                        var id_order = parseInt(checkbox.attr('value'));
                    } else {
                        var id_order = parseInt($(this).find('td:first').html());
                    }

                    if (last_cell.length) {
                        var button_container = last_cell.find('.btn-group'),
                            button = orderedit_list.createOrderEditButton(id_order);
                        
                        if (last_cell.find('.btn-group-action').length) {
                            button_container.find('ul.dropdown-menu').append(button);
                        } else {
                            button_container.wrap($(document.createElement('div')).addClass('btn-group-action'));
                            
                            button_container.append(
                                $(document.createElement('button')).addClass('btn btn-default dropdown-toggle').attr('data-toggle', 'dropdown')
                                .append($(document.createElement('i')).addClass('icon-caret-down'))
                            ).append($(document.createElement('ul')).addClass('dropdown-menu').append(button))
                        }
                    }
                });
            }
        }
    },
    
    createOrderEditButton: function(id_order) {
        return $(document.createElement('li')).append($(document.createElement('a')).attr({'href': '#', 'rel': id_order}).addClass('orderedit_edit').html('<i class="icon-pencil"></i> ' + orderedit_list.tr('Edit')));
    },
    
    tr: function(str) {
        return str;
    }
}

$(function(){
    orderedit_list.init();
});