/**
* This is main js file. Don't edit the file if you want to update module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2016 Globo ., Jsc
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
$(document).ready(function(){
    $("#popup_template").fancybox();
    $('.choose_temp').live('click',function(){
        rel = $(this).attr('rel');
        $('#choose_design').val(rel);
        $('#templateChosse').html($(this).html());
        $('.fancybox-close').trigger('click');
    });
    columnsWidthResized = function(){
        productlist_width = $("#productlist").width();
		columns = $("#productlist").find(".title th");
        total = 100;
        t = columns.length;
		columns.each(function(){
		  if (t == 1) {
		      $(this).children('.widthtitle').val(total);
              console.debug(total);
		  }else{
		      t--;
              widthtitle = Math.floor($(this).width()/productlist_width*100);
              console.debug(widthtitle);
		      $(this).children('.widthtitle').val(widthtitle);
              total -=widthtitle;
		  }
        });
        
	};
    $('#new_column').click(function(){
        $("#productlist").colResizable({disable:true}); 
	    $(".title th:first").clone().appendTo('.title');
        $(".content td:first").clone().appendTo('.content');
        $("#productlist").colResizable({liveDrag:true,onResize:columnsWidthResized,gripInnerHtml:"<i class=\"icon-resize-horizontal column_resize\"></i>"});
        $( window ).resize();
        columnsWidthResized();
        return false;
    });
    $("#productlist").on("click", ".remove_column", function ( event ) {
        $("#productlist").colResizable({disable:true});
        var ndx = $(this).parents('th').index() + 1;
        $("th", event.delegateTarget).remove(":nth-child(" + ndx + ")");
        $("td", event.delegateTarget).remove(":nth-child(" + ndx + ")");
        $("#productlist").colResizable({liveDrag:true,onResize:columnsWidthResized,gripInnerHtml:"<i class=\"icon-resize-horizontal column_resize\"></i>"});
        $( window ).resize();
        columnsWidthResized();
        return false;
    });	
	$("#productlist").colResizable({liveDrag:true,onResize:columnsWidthResized,gripInnerHtml:"<i class=\"icon-resize-horizontal column_resize\"></i>"});
    $('button[name="previewTemplate"]').click(function(){
        tinyMCE.triggerSave();
        $('form#gwadvancedinvoicetemplate_form').attr('target', '_blank');
        $(this).val(id_language);
        return true;
    }
    );
    $('button[name="submitAddgwadvancedinvoicetemplateAndStay"]').click(function(){
        tinyMCE.triggerSave();
        $('form#gwadvancedinvoicetemplate_form').removeAttr('target');
    });
    $('button[name="saveTemplate"]').click(function(){
        tinyMCE.triggerSave();
        $('form#gwadvancedinvoicetemplate_form').removeAttr('target');
    });
    mcediv = 0;
    mceload = 0;
    if($('#activeheader_on').length > 0){
        activeheader_on = $('#activeheader_on').offset();
        mcediv = Math.round(activeheader_on.top - $(window).height()/2);
        $(window).scroll(function() {
            if (($(window).scrollTop() > mcediv && !mceload) || ($(window).scrollTop() < mcediv && mceload) ) {
                mceload = (mceload+1)%2;
                data = 'getstyle=true&choose_design='+$('input[name="choose_design"]').val()+'&id_language='+id_language;
                $.each($('.template_config'), function() {
                  data +='&'+$(this).attr('name')+'='+$(this).val();
                });
                $.ajax({
                  url: "../modules/gwadvancedinvoice/controllers/admin/ajax.php",
                  type : 'POST',                      
                  data: data,
                    })
                .done(function(data) {
                    $.each($('iframe[id^="invoice_"]'), function() {
                        if($(this).contents().find('head').find('#customcss').length){
                            $(this).contents().find('head').find('#customcss').html(data+$('#customcss').val());
                        }else
                            $(this).contents().find('head').append('<style id="customcss">'+data+$('#customcss').val()+'</style>');
                    });
                    $.each($('iframe[id^="invoice_"]'), function() {
                        if($(this).contents().find('head').find('#customcss').length){
                            $(this).contents().find('head').find('#customcss').html(data+$('#customcss').val());
                        }else
                            $(this).contents().find('head').append('<style id="customcss">'+data+$('#customcss').val()+'</style>');
                    });
                    $.each($('iframe[id^="header_"]'), function() {
                        if($(this).contents().find('head').find('#customcss').length){
                            $(this).contents().find('head').find('#customcss').html(data+$('#customcss').val());
                        }else
                            $(this).contents().find('head').append('<style id="customcss">'+data+$('#customcss').val()+'</style>');
                    });
                    $.each($('iframe[id^="footer_"]'), function() {
                        if($(this).contents().find('head').find('#customcss').length){
                            $(this).contents().find('head').find('#customcss').html(data+$('#customcss').val());
                        }else
                            $(this).contents().find('head').append('<style id="customcss">'+data+$('#customcss').val()+'</style>');
                    });
                    
                });
            }
        });
        $(window).resize(function() {
            activeheader_on = $('#activeheader_on').offset();
            mcediv = Math.round(activeheader_on.top - $(window).height()/2);
            
        });
    }
    
    $("#GWADVANCEDINVOICE_TEMPLATE").change(function(){
        if($(this).val() == "")
            $("#configuration_form_submit_btn").attr("disabled","disabled");
        else
            $("#configuration_form_submit_btn").removeAttr("disabled");
    });
    if($("#GWADVANCEDINVOICE_TEMPLATE").length > 0)
        if($("#GWADVANCEDINVOICE_TEMPLATE").val() == "")
            $("#configuration_form_submit_btn").attr("disabled","disabled");
            
    
    if($(".pagesize.pagesize_ajaxcall").length > 0){
        $("#gwadvancedinvoicetemplate_form_submit_btn.chooseTemplate_ajaxcall").attr("disabled","disabled");
        if($(this).val() != '0' && $(this).val() !=''){
            $.ajax({
              url: "../modules/gwadvancedinvoice/controllers/admin/ajax.php",
              data: "pagesize="+$(this).val(),
                })
            .done(function(data) {
                var obj = jQuery.parseJSON(data);
                $("#templateChosse_box").html( obj.templates );
            });
        }
    }
    $(".pagesize").change(function(){
        if($(this).hasClass('pagesize_ajaxcall')){
            $("#gwadvancedinvoicetemplate_form_submit_btn.chooseTemplate_ajaxcall").attr("disabled","disabled");
            $.ajax({
              url: "../modules/gwadvancedinvoice/controllers/admin/ajax.php",
              data: "pagesize="+$(this).val(),
                })
            .done(function(data) {
                var obj = jQuery.parseJSON(data);
                $("#templateChosse_box").html( obj.templates );
            });
        }
    });
    $("input[name=\'choose_design\']").live("change",function(){
        if ($("input[name=\'choose_design\']:checked").val() != "undefined"){
            $("#gwadvancedinvoicetemplate_form_submit_btn.chooseTemplate_ajaxcall").removeAttr("disabled");
        }
    });
    
})