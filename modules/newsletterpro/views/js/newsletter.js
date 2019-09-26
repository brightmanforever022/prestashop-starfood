/**
* 2013-2015 Ovidiu Cimpean
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author    Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright 2013-2015 Ovidiu Cimpean
* @version   Release: 4
* @license   Do not edit, modify or copy this file
*/

var NEWSLETTER_PRO_DEBUG_MODE = true;
var jQueryNewsletterProNew = typeof NPRO_JQUERY_NEW !== 'undefined' ? NPRO_JQUERY_NEW : jQuery;
var jQueryNewsletterProOld = typeof NPRO_JQUERY_OLD !== 'undefined' ? NPRO_JQUERY_OLD : jQuery;

;(function($) {
	$.postAjax = function( data, dataType, catchError ) 
	{
		dataType = dataType || 'json';
		catchError = ( typeof catchError !== 'undefined' ) ? catchError : true ;

		return $.ajax({
   			url : NewsletterPro.dataStorage.get('ajax_url'),
   			type : 'POST',
   			dataType: dataType,
   			data : data,
   			error : function( data ) {
   				if( NEWSLETTER_PRO_DEBUG_MODE == true && dataType == 'json' )
				{
					if ( catchError == true )
					{
						var login = (data.getResponseHeader('Login') === 'true' ? true : false);

						if (login)
							alert('The login session has expired. You must refresh the browser and login again. The next time when you are login check the button "Stay logged in".');
						else
							alert('Ajax request error, please check your console for more details!');
					}

					console.warn('error: the returned data must be json, your response text is: ');
				    console.warn(data.responseText);
				}
  			}
		}).promise();
	};

	$.updateConfiguration = function(name, value)
	{
		return $.postAjax({'submit': 'jsUpdateConfiguration', name: name, value: value}).promise();
	};

	$.objSize = function( obj ) 
	{
	    var size = 0, key;
	    for (key in obj) {
	        if (obj.hasOwnProperty(key)) size++;
	    }
	    return size;
	};

	$.fn.getFormData = function() 
	{
        form = this;
        var formObj =  form.serializeArray();
        var formdata = {};
        var j = 0, len = formObj.length, item;
        for( ; j < len; j++)
        {
            item = formObj[j];
            formdata[item.name] = item.value;
        }

        if (formdata)
        	return formdata;
        return {};
	};

	$.setCookie = function(c_name, value, exdays) 
	{
	    var exdate = new Date();
	    exdate.setDate(exdate.getDate() + exdays);
	    var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
	    document.cookie = c_name + "=" + c_value;
	}

	$.getCookie = function(c_name) 
	{
	    var i, x, y, ARRcookies = document.cookie.split(";");
	    for (i = 0; i < ARRcookies.length; i++) {
	        x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
	        y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
	        x = x.replace(/^\s+|\s+$/g, "");
	        if (x == c_name) {
	            return unescape(y);
	        }
	    }
	}

	$.deleteCookie = function(name) 
	{
	    document.cookie = name + '=; expires=Thu, 01-Jan-70 00:00:01 GMT;';
	}

    $.submitAjax = function( obj, dataType, catchError ) 
    {
    	catchError = ( typeof catchError !== 'undefined' ) ? catchError : true ;
        dataType = dataType || 'json';
        var self = this;

        this.form = obj.form;

        var formObj =  this.form.serializeArray();


        var formdata = false;
        if(window.FormData)
            formdata = new FormData();

        var files = this.form.find('input[type="file"]');

        var k = 0, length = files.length, input;
        for ( ; k < length; k ++) {
            input = $(files[k]);

            var i = 0, len = input[0].files.length, file;

            for ( ; i < len; i++ ) {
                file = input[0].files[i];
                if (formdata) {
                    formdata.append(input.attr('name') + "", file);
                }
            }
        }

        var j = 0, len = formObj.length, item;
        for( ; j < len; j++)
        {
            item = formObj[j];

            if (formdata) {
                formdata.append(item.name, item.value);
            }
        }

        var submitModule = null;

        for (var key in obj)
        {
        	if (/^submit_\w+/.test(key))
        	{
        		submitModule = key
        		break;
        	}
        }

        if (formdata) 
        {
        	if (obj.hasOwnProperty('name'))
        	{
        		var appendValue = true;
        		if (obj.hasOwnProperty('value'))
        			appendValue = obj.value;

        		formdata.append(obj.name, appendValue);

        	}

            if (obj.hasOwnProperty('submit'))
	            formdata.append('submit', obj.submit);
	        else if (submitModule)
	        	formdata.append(submitModule, obj[submitModule]);

	        if (obj.hasOwnProperty('data'))
	        {
	        	$.each(obj.data, function(name, value){
	        		formdata.append(name, value);
	        	});
	        }
       	}

       	var errorCallback = function (data) 
       	{
        	if( NEWSLETTER_PRO_DEBUG_MODE == true && dataType == 'json' )
			{
				if ( catchError == true ) 
					alert('You have an request error, check the console for more details.');

				console.log('error: the returned data must be json, your response text is: ');
			    console.log(data.responseText);
			}
        };

        if(formdata) 
        {
            return $.ajax({
                url:  NewsletterPro.dataStorage.get('ajax_url'),
                type: 'POST',	            
                data: formdata,
                processData: false,
                contentType: false,
                dataType: dataType,
                error: errorCallback
            }).promise();
        }
        else
        {
        	var data = this.form.getFormData();

        	if (obj.hasOwnProperty('submit'))
        		data['submit'] = obj.submit;
      		else if (submitModule)
	        	formdata.append(submitModule, obj[submitModule]);

        	return $.ajax({
                url:  NewsletterPro.dataStorage.get('ajax_url'),
                type: 'POST',	            
                data: data,
                dataType: dataType,
                error: errorCallback
            }).promise();
        }

    };

	$.fn.getPaddingWidth = function() 
	{
		var left = parseFloat(this.css('padding-left')),
			right = parseFloat(this.css('padding-right'));
		return left + right;
	};

	$.fn.widthCSS = function(value) 
	{
		var that = $(this);
		if (typeof value !== 'undefined')
		{
			var val = 0;

			if (/%/.test(new RegExp(String(value))))
				val = value;
			else if (/px/.test(new RegExp(String(value))))
				val = value;
			else
				val = parseInt(value) + 'px';

			that.css('width', val);

			return that;
		}
		else
			return parseInt(that.css('width'));
	};

	$.fn.quickOuterWidth = function() 
	{
		var elem = this.get(0);
		if (window.getComputedStyle) 
		{
			var computedStyle = window.getComputedStyle(elem, null);
			return elem.offsetWidth + (parseInt(computedStyle.getPropertyValue('margin-left'), 10) || 0) + (parseInt(computedStyle.getPropertyValue('margin-right'), 10) || 0);
		} 
		else 
		{
			return elem.offsetWidth + (parseInt(elem.currentStyle["marginLeft"]) || 0) + (parseInt(elem.currentStyle["marginRight"]) || 0);
		}
	};

})(jQueryNewsletterProNew);