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

function gkSearch(cfg)
{
	if (!(this instanceof gkSearch))
		return new gkSearch(cfg);

	var self      = this,
		timer     = null,
		minLength = cfg.minLength || 4,
		title     = cfg.element.val(),
		read      = cfg.read,
		result    = cfg.result,
		reset     = cfg.reset,
		dom       = {};

	init();

	this.clearVal = function()
	{
		clearVal();
	}

	function init()
	{
		initExeptions();
		initDom();
		initEvents();
	}

	function initExeptions()
	{
		if (typeof read === 'undefined')
		 	throw('The search read is undefined!');
	}

	function initDom()
	{
		dom['element']    = cfg.element;
		dom['ajaxLoader'] = cfg.ajaxLoader;
	}

	function initEvents()
	{
		dom.element.on('keyup', function( event ) 
		{
			if ( getVal().length >= minLength )
				search(getVal());
			else
				resetSearch();
		});

		dom.element.on('focus', function( event ) 
		{
			if ( isEmpty() == true || getVal() == title )
				setVal('');
		});

		dom.element.on('focusout', function( event ) 
		{
			if ( isEmpty() == true || getVal() == title )
				setVal(title);
		});
	}

	function clearVal()
	{
		setVal('');
		dom.element.trigger('focusout');
	}

	function setVal(val) 
	{
			dom.element.val(val);
	}

	function getVal(val) 
	{
		return dom.element.val();
	}

	function resetSearch() 
	{
		if( isEmpty() ) 
		{
			if ($.isFunction(reset)) 
				reset();
		}
	}

	function search(query)
	{
		if ( timer != null ) 
			clearTimeout(timer);

		dom.ajaxLoader.show();
		timer = setTimeout(function()
		{
			read.query = getVal();
			$.postAjax(read).done(function(response)
			{ 
				dom.ajaxLoader.hide();
				if ($.isFunction(result)) 
					result(response);
			});

		}, 200);
	}

	function isEmpty() 
	{
		if ( getVal() == '' ) 
		{
			if ( !dom.element.hasClass('empty') )
				dom.element.addClass('empty');
			return true;
		} 
		else 
		{
			if ( dom.element.hasClass('empty') )
				dom.element.removeClass('empty');
			return false;
		}
	}

	return this;
}