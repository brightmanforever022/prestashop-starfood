$(function() {
// change document paid state link
    $('.documentPaidChangeLink').on('click', function (e) {
        e.preventDefault();
        var documentId = $(this).parent().parent().attr('documentId');
        var clickedA = this;
        $.post(admin_order_tab_link, {id: documentId, ajax: 1, action: 'toggleInvoicePaidState'}, function (result) {
            if (result.error)
            {
                jAlert(result.error);
            }
            if (result.paid)
            {
                $(clickedA).children('i').removeClass('icon-remove').addClass('icon-check');
                $(clickedA).removeClass('action-disabled').addClass('action-enabled');
            } else
            {
                $(clickedA).children('i').removeClass('icon-check').addClass('icon-remove');
                $(clickedA).removeClass('action-enabled').addClass('action-disabled');
            }
        }, 'json');
    });
    
    $('.sumToPay').on('dblclick', function(e){
            e.preventDefault();
            var documentId = $(this).parent().attr('documentId');
            var oldValue = parseFloat($(this).text().replace(/\./g, '').replace(',', '.'));
            var amountField = $(this);
            
            amountField.html('<input type="text" name="amount" size="6" id="editAmountField" value="'+oldValue+'">');
            $('#editAmountField').focus().blur(function(){
                                             // then focus lost try to save
                                             // field
                                             var newValue = $(this).val();
                                             if (oldValue==newValue)
                                             {
                                                 amountField.html(formatCurrency(oldValue, currency_format, currency_sign, currency_blank));
                                                 return;
                                             }
            
            //formatCurrency(parseFloat(order.total_products_wt), currency_format, currency_sign, currency_blank)
            $.post(admin_order_tab_link, {id: documentId, amount: newValue, ajax: 1, action: 'setInvoiceSumToPay'}, function(result){
                    if (result.error)
                    {
                        jAlert(result.error);
                    }
                    else
                    {
                        amountField.html(formatCurrency(parseFloat(newValue), currency_format, currency_sign, currency_blank));
                    }
               }, 'json');
           });
        });
        
        $('.dueDate').on('dblclick', function(e){
            e.preventDefault();
            var documentId = $(this).parent().attr('documentId');
            var oldValue = $(this).text();
            var dateField = $(this);
            
            dateField.html('<input type="text" name="due_date" size="10" id="editDueDateField" value="'+oldValue+'">');
            $("#editDueDateField").datepicker({prevText:"", nextText:"", dateFormat:"dd.mm.yy"});
            dateField.append('<a href="#" id="dueDateCancelBtn" class="button">'+textCancel+'</a>');
            dateField.append('&nbsp;<a href="#" id="dueDateSaveBtn" class="button">'+textSave+'</a>');
            $('#dueDateCancelBtn').click(function(){
                dateField.html(oldValue);
            });
    
            $('#dueDateSaveBtn').click(function () {
                var newValue = $('#editDueDateField').val();
                if (oldValue==newValue)
                {
                    dateField.html(oldValue);
                    return;
                }

                $.post(admin_order_tab_link, {id: documentId, dueDate: newValue, ajax: 1, action: 'setInvoiceDueDate'}, function (result) {
                if (result.error)
                {
                    jAlert(result.error);
                } else
                {
                    dateField.html(newValue);
                }
            }, 'json');
        });
            $('#editDueDateField').focus().blur(function(){
                                             // then focus lost try to save
                                             // field
                                             var newValue = $(this).val();
                                             if (oldValue==newValue)
                                             {
                                                 amountField.html(formatCurrency(oldValue, currency_format, currency_sign, currency_blank));
                                                 return;
                                             }
            
            //formatCurrency(parseFloat(order.total_products_wt), currency_format, currency_sign, currency_blank)
            $.post(admin_order_tab_link, {id: documentId, amount: newValue, ajax: 1, action: 'setInvoiceAmount'}, function(result){
                    if (result.error)
                    {
                        jAlert(result.error);
                    }
                    else
                    {
                        amountField.html(formatCurrency(parseFloat(newValue), currency_format, currency_sign, currency_blank));
                    }
               }, 'json');
           });
        });
});