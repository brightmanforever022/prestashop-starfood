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

// for FrontOffice and BackOffice
var NewsletterPro = NewsletterPro || {};

// this is for the front office compatibillity
;(function($){
	// run function for initialization
	$(function(){
		try 
		{
			NewsletterPro.modifyTextarea();
		}
		catch (error) 
		{
			console.warn(error);
		}
	});

	NewsletterPro.define = {
		SEND_METHOD_DEFAULT: 0,
		SEND_METHOD_ANTIFLOOD: 1,
		SEND_THROTTLER_TYPE_EMAILS: 0,
		SEND_THROTTLER_TYPE_MB: 1,
	};

	NewsletterPro.prototype = {
		name: 'Newsletter Pro',
		version: '4.0.1',
	}

	NewsletterPro.namespace = function(ns_string) {
		var parts = ns_string.split('.'),
			parent = NewsletterPro,
			i;

		if (parts[0] === 'NewsletterPro') {
			parts = parts.slice(1);
		}

		for (i = 0; i < parts.length; i++) {
			if (typeof parent[parts[i]] === 'undefined') {
				parent[parts[i]] = {};
			}
			parent = parent[parts[i]];
		}
		return parent;
	};

	NewsletterPro.alertErrors = function(errors) {
		var string;
		if (typeof errors === 'string')
			string = errors.replace(/\&quot;/g, '"');
		else
			string = String(errors.join('\n')).replace(/\&quot;/g, '"');

		string = string.replace(/\&\#039;/g, '"');
		alert(string);
	};

	NewsletterPro.displayAlert = function(string, separator) {

		separator = separator || '\n';

		if (typeof string === 'object')
			string = string.join(separator);

		string = string.replace(/\&quot;/g, '"');
		string = string.replace(/\&\#039;/g, '"');
		string = string.replace(/\&\gt;/g, '>');
		string = string.replace(/\&\lt;/g, '<');
		return string;
	};

	NewsletterPro.showAjaxLoader = function(target) {
		var loder = target.find('.btn-ajax-loader');
		if (loder.length > 0)
			loder.show();
		else
		{
			loder = target.find('.ajax-loader');
			if (loder.length > 0)
				loder.show();
		}
	};

	NewsletterPro.hideAjaxLoader = function(target) {
		var loder = target.find('.btn-ajax-loader');
		if (loder.length > 0)
			loder.hide();
		else
		{
			loder = target.find('.ajax-loader');
			if (loder.length > 0)
				loder.hide();
		}
	};

	NewsletterPro.getUrl = function(params) 
	{
		return NewsletterPro.dataStorage.get('ajax_url')+this.parseUrl(params);
	};

	NewsletterPro.parseUrl = function(params, trimFirst)
	{
		if (typeof trimFirst === 'undefined')
			trimFirst = false;

		var paramsString = '';
		for(var key in params)
		{
			var value = params[key];
			paramsString += '&'+key+'='+value;
		}
		return trimFirst == true ? paramsString.substr(1) : paramsString;
	};


	NewsletterPro.extendSubscribeFeature = function(obj)
	{
		obj.subscriptionFeature = obj.subscriptionFeature || {};

		obj.subscribe = function(eventName, func, instance)
		{
			if (!obj.subscriptionFeature.hasOwnProperty(eventName))
				obj.subscriptionFeature[eventName] = [];

				obj.subscriptionFeature[eventName].push({
					func: func,
					instance: instance
				});
		};

		obj.publish = function(eventName, data)
		{
			if (obj.subscriptionFeature.hasOwnProperty(eventName))
			{
				for (var i = 0; i < obj.subscriptionFeature[eventName].length; i++) {

					var result = (typeof data === 'function' ? data() : data);
					var func = obj.subscriptionFeature[eventName][i].func;
					var instance = obj.subscriptionFeature[eventName][i].instance || obj;
					func.call(instance, result);
				}
			}
		};
	};

	NewsletterPro.dataStorage = ({
		data: {},
		init:function()
		{
			NewsletterPro.extendSubscribeFeature(this);
			return this 
		},

		get: function(name) 
		{
			if (typeof name === 'undefined')
				return this.data;
			
			var split = name.split('.');

			if (split.length > 1)
			{
				var target = 'NewsletterPro.dataStorage.data' + '.' + split.join('.');

				try
				{
					if (eval('typeof ' + target) !== 'undefined')
						return eval(target);
				}
				catch(e)
				{
					return false;
				}
			}
			else if (this.data.hasOwnProperty(name))
				return this.data[name];

			return false;
		},

		getNumber: function(name)
		{
			var value = Number(this.get(name));
			if (isNaN(value))
				return 0;
			return value;
		},

		has: function(name)
		{
			if (typeof name === 'undefined')
				return this.data;
			
			var split = name.split('.');

			if (split.length > 1)
			{
				var target = 'NewsletterPro.dataStorage.data' + '.' + split.join('.');

				try
				{
					if (eval('typeof ' + target) !== 'undefined')
						return true;
				}
				catch(e)
				{
					return false;
				}
			}
			else if (this.data.hasOwnProperty(name))
				return true;

			return false;
		},

		add: function(name, value) 
		{
			var splitName = name.split('.'),
				parent = this.data,
				i;

			for (i = 0; i < splitName.length; i++)
			{
				if (i == splitName.length - 1)
					parent[splitName[i]] = value;
				else
				{
					if (typeof parent[splitName[i]] !== 'object')
						parent[splitName[i]] = {};
				}
			    
			    if (typeof parent === 'object')
			    	parent = parent[splitName[i]];
			}
		},

		set: function(name, value)
		{
			this.add(name, value);
			this.publish('change'+name, value);
		},

		addObject: function(obj) {
			for (var i in obj)
				this.data[i] = obj[i];
		},

		append: function(name, value)
		{
			if (this.data.hasOwnProperty(name))
				this.data[name].push(value);
			else
			{
				this.data[name] = [];
				this.data[name].push(value);
			}
		},

		on: function(evt, name, func)
		{
			switch(evt)
			{
				case 'change': 
					this.subscribe('change'+name, func);
				break;
			}
		},

	}.init());

	NewsletterPro.test = {
		dom: function() {
			var modules = NewsletterPro.modules;
			for (var i in modules) {
				var moduleName = i,
					module = modules[i];
					dom = module.dom;

				for (var j in dom) {
					var name = j,
						value = dom[j];

					if( value.length == 0 )
						throw Error('Error: The element "'+name+'" does not exist in the module "'+moduleName+'"');
				}
			}	
		}
	};

	NewsletterPro.onObject = {
		callback: {},
		setCallback: function(name, func) {
			this.callback[name] = func;
		},

		run: function(name, ed) {
			if (this.callback.hasOwnProperty(name)) {
				this.callback[name](ed);
			}
		}
	};

	NewsletterPro.uniqueId = function(length)
	{
		length = length || 3;

		var output = '';
		function s4()
		{
			return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);	
		}

		for (var i = 0; i < length; i++) 
		{
			output += s4() + '-';
		}

		return output.replace(/-$/, '');
	};

	NewsletterPro.ucfirst = function(string)
	{
		return string.charAt(0).toUpperCase() + string.slice(1);
	};

	NewsletterPro.modifyTextarea = function()
	{
		HTMLTextAreaElement.prototype.getCaretPosition = function () { 
		    return this.selectionStart;
		};
		HTMLTextAreaElement.prototype.setCaretPosition = function (position) {
		    this.selectionStart = position;
		    this.selectionEnd = position;
		    this.focus();
		};
		HTMLTextAreaElement.prototype.hasSelection = function () { 
		    if (this.selectionStart == this.selectionEnd) {
		        return false;
		    } else {
		        return true;
		    }
		};
		HTMLTextAreaElement.prototype.getSelectedText = function () { 
		    return this.value.substring(this.selectionStart, this.selectionEnd);
		};
		HTMLTextAreaElement.prototype.setSelection = function (start, end) { 
		    this.selectionStart = start;
		    this.selectionEnd = end;
		    this.focus();
		};
	};

	NewsletterPro.setBoxSizeing = function(array)
	{
		for(var i in array)
		{
			array[i].css({
				'box-sizing': 'content-box'
			});
		}
	};

	NewsletterPro.versionCompare = function(v1, v2, options) 
	{
	    var lexicographical = options && options.lexicographical,
	        zeroExtend = options && options.zeroExtend,
	        v1parts = v1.split('.'),
	        v2parts = v2.split('.');

	    function isValidPart(x) 
	    {
	        return (lexicographical ? /^\d+[A-Za-z]*$/ : /^\d+$/).test(x);
	    }

	    if (!v1parts.every(isValidPart) || !v2parts.every(isValidPart)) 
	        return NaN;

	    if (zeroExtend) 
	    {
	        while (v1parts.length < v2parts.length) v1parts.push("0");
	        while (v2parts.length < v1parts.length) v2parts.push("0");
	    }

	    if (!lexicographical) 
	    {
	        v1parts = v1parts.map(Number);
	        v2parts = v2parts.map(Number);
	    }

	    for (var i = 0; i < v1parts.length; ++i) 
	    {
	        if (v2parts.length == i)
	            return 1;

	        if (v1parts[i] == v2parts[i])
	            continue;
	        else if (v1parts[i] > v2parts[i])
	            return 1;
	        else
	            return -1;
	    }

	    if (v1parts.length != v2parts.length)
	        return -1;

	    return 0;
	};

	NewsletterPro.subscription = function(object, subscription)
	{
		for (var i = 0; i < object.subscription.length; i++)
		{
			var value = object.subscription[i];
			value[0].subscribe(value[1], object[value[2]], object);
		}
	};

	NewsletterPro.htmlEncode = function(value)
	{
		return $('<div/>').text(value).html();
	};

	NewsletterPro.htmlDecode = function(value)
	{
		return $('<div/>').html(value).text();
	};

	NewsletterPro.getXHRError = function(jqXHR, size)
	{
		size = size || 1000;

		var text = jqXHR.responseText,
			msg = this.htmlEncode(text.slice(0, 1000));

		if (text.length > 1000)
			msg += '...';

		return msg;
	};

	NewsletterPro.bootstrap = function(func)
	{
		var bool = Number(this.dataStorage.get('bootstrap'));
		return func(bool);
	};

	NewsletterPro.isTinyHigherVersion = function()
	{
		if (tinyMCE.majorVersion >= 4)
			return true;
		return false;
	};

	NewsletterPro.linkAdd = function(link, params, hash)
	{
		params = params || '';
		hash = hash || '';

		if (link) {
			var hashIndex = link.indexOf('#');

			if (/\?/.test(link) && params.length > 0)
			{
				if (hashIndex != -1)
					link = link.substr(0, hashIndex) + '&' + params + link.substr(hashIndex);
				else
					link = link + '&' + params;
			}
			else if (params.length > 0)
			{
				if (hashIndex != -1)
					link = link.substr(0, hashIndex) + '?' + params + link.substr(hashIndex);
				else
					link = link + '?' + params;
			}

			if (hashIndex != -1 && hash.length > 0)
				link = link + hash;
			else if (hash.length > 0)
				link = link + '#' + hash;
		}

		return link;
	};


	NewsletterPro.trimString = function(str, value, end)
	{
		end = end || '...';
		value = parseInt(value);

		if (true) {
			if(str.length > (value + end.length) )
				str = str.slice(0, value) + end;
		} else {
			if(str.length > value )
				str = str.slice(0, value) + end;
		}

		return str;
	};

	NewsletterPro.objSize = function(obj)
	{
		var size = 0;

		for (var i in obj) {
			size++;
		}

		return size;
	};

	NewsletterPro.parseProductHeader = function(content, matchContent) {

		matchContent = typeof matchContent !== 'undefined' ? matchContent : true;

		var parseProductHeader = function(headerString)
		{
			var match = headerString.split('\n');
			var headerLine = '';
			if (match != null && match.length > 0)
			{
				for(var i = 0; i < match.length; i++)
				{
				    var item = match[i].replace(/\s+/g,'');
				    
				    if (item != '')
				    {    
				        headerLine += item;
				        if (item[item.length - 1] !== ';')
				        {
				            headerLine += ';';
				        }
				    }
				}
			}

			var header = headerLine.split(';');
			var headerObject = {};
			if (header != null && header.length > 0)
			{
				for(var i = 0; i < header.length; i++)
				{
				    var line = header[i].replace(/\s+/g,'');
				    if (line != '')
				    {
				        var prop = line.split('=');
				        if (typeof prop[1] !== 'undefined')
				        {
				            headerObject[prop[0]] = (
				                prop[1] === 'false' ? false : (
				                    prop[1] === 'true' ? true : (
				                        !isNaN(Number(prop[1])) ? Number(prop[1]) : prop[1]
				                    ) 
				                )
				            );
				           
				        }
				    }
				}
			}
			return headerObject;
		};

		var getHeader = function(content)
		{
			var match = content.match(/<!-- start header -->\s*?<!--([\s\S]*)-->\s*?<!-- end header -->/);

			if (!matchContent) {
				match = [];
				match[0] = '';
				match[1] = content;
			}

			var matchFullHeader,
				matchHeader,
				headerObject = {};

			if (match != null && match.length > 0)
			{
				matchFullHeader = match[0];
				matchHeader = match[1];
				headerObject = parseProductHeader(matchHeader);
				content = content.replace(matchFullHeader, '');
			}

			return headerObject;
		};

		return getHeader(content);
	};

}(jQueryNewsletterProNew));

function NP_RunTabPerformanceTest()
{
	var arr = NewsletterProComponents.objs.tabItems.buttons;
	for (var i in arr)
	{
		var t1 = new Date().getTime();
		arr[i].trigger('click');
		var t2 = new Date().getTime();
		console.log( 'delay: ' + (t2 - t1) + ' milliseconds');
	}
}

