/*
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

NewsletterPro.namespace('components.LanguageSelect');
NewsletterPro.components.LanguageSelect = function LanguageSelect(cfg)
{
	if (!(this instanceof LanguageSelect))
		return new LanguageSelect(cfg);

	var box = NewsletterPro;
	var selfStatic     = NewsletterPro.components.LanguageSelect,
		self           = this,
		selector       = cfg.selector,
		languages      = cfg.languages,
		dom            = buildTemplate(selector),
		id = selector.attr('id');

	selfStatic.addInstance(id, self);
	box.extendSubscribeFeature(this);

	fixDropDownPosition();

	this.setHeader = function(value)
	{
		dom.headerText.html(value);
	};

	this.getCfg = function()
	{
		return cfg;
	};

	this.fixDropDownPosition = function()
	{
		fixDropDownPosition();
	};

	this.headerClick = function()
	{
		self.toggle();
	};
	
	this.click = function(lang, key)
	{
		self.publish('change', {
			lang: lang,
			key: key
		});

		$.each(getLanguageFields(), function(k, item){
			item = $(item);

			var isDataVisible = item.data('visible') == '1' ? true : false;

			if (Number(item.data('lang')) == Number(lang.id_lang))
			{
				if (isDataVisible)
				{
					item.removeClass('np-lang-visible-hide');
					item.show();
				}
				else
				{
					item.show();
				}
			}
			else
			{
				if (isDataVisible)
				{
					item.addClass('np-lang-visible-hide');
				}
				else
				{
					item.hide();
				}
			}
		});

		$.each(selfStatic.getInstances(), function(id, obj){
			obj.setHeader(lang.iso_code);
		});

		dom.headerText.html(lang.iso_code);
		self.close();
		if (typeof cfg.click === 'function')
		{
			cfg.click(lang, key);
		}

		if (typeof selfStatic.clickEvent === 'function')
		{
			selfStatic.clickEvent(lang, key);
		}

		box.dataStorage.set('id_selected_lang', parseInt(lang.id_lang));

	};

	this.open = function()
	{
		this.fixDropDownPosition();

		dom.dropDown.show();
		setTimeout(function(){
			dom.dropDown.focus();
		}, 1);
	};

	this.close = function()
	{
		dom.dropDown.hide();
	};

	this.toggle = function()
	{
		if (dom.dropDown.is(':visible'))
			self.close();
		else
			self.open();
	};

	function getLanguageFields()
	{
		return $('[data-lang]');
	}

	function fixDropDownPosition()
	{
		var headerWidth = dom.header.innerWidth();
		var dropDownWidth = dom.dropDown.innerWidth();
		var dropDownLeft = dom.dropDown.offset().left;
		var distance = dropDownLeft + dropDownWidth;
		var winWidth = $(window).width();

		if (distance > winWidth);
		{
			dom.dropDown.css({
				'left': -dropDownWidth + headerWidth,
			})
		}
	}

	dom.dropDown.focusout(function(){
		self.close();
	});

	dom.header.on('click', function(event){
		self.headerClick.call(header);
	});

	function buildTemplate(selector)
	{
		var header = $('<button class="gk-lang-header btn btn-default dropdown-toggle" tabindex="-1"> </button>');
		var headerText = $('<span style="margin-right: 5px; display: inline-block;"></span>');
		var headerIcon = $('<i class="icon icon-caret-down"> </i> ');
		var dropDown = $('<ul id="lang_menu_'+box.uniqueId()+'" class="gk-lang-menu dropdown-menu" style="display: none;"></ul>');
		var rows = [];

		selector.css({
			'position': 'relative'
		});

		header.append(headerText);
		header.append(headerIcon);
		selector.append(header);
		selector.append(dropDown);

		parseLanguages(function(lang, key){
			var rowTpl = $('<li id="lang_item_'+box.uniqueId()+'"><a href="javascript:{}" style="width: 100%;">'+lang.name+'</a></li>');

			if (lang.selected == true)
				headerText.html(lang.iso_code);

			rowTpl.on('click', function(event){
				self.click(lang, key);
			});

			dropDown.append(rowTpl);
			rows.push(rowTpl);
		});

		return {
			header: header,
			headerText: headerText,
			headerIcon: headerIcon,
			dropDown: dropDown,
			rows: rows,
			selector: selector,
		};
	}

	function parseLanguages(func)
	{
		for(var i in languages)
			func(languages[i], i);
	}

	return this;
};

NewsletterPro.components.LanguageSelect.instances = {};

NewsletterPro.components.LanguageSelect.addInstance = function(id, value)
{
	id = id == null ? NewsletterPro.uniqueId() : '#' + id;
	var instances = NewsletterPro.components.LanguageSelect.instances;
	instances[id] = value;
};

NewsletterPro.components.LanguageSelect.getInstanceById = function(id)
{
	var instances = NewsletterPro.components.LanguageSelect.instances;

	if (instances.hasOwnProperty(id))
		return instances[id];
	return false;
};

NewsletterPro.components.LanguageSelect.getInstances = function()
{
	return NewsletterPro.components.LanguageSelect.instances;
};

NewsletterPro.components.LanguageSelect.parseInstances = function(func)
{
	var instances = NewsletterPro.components.LanguageSelect.getInstances();
	for(var i in instances)
		func(instances[i], i);
};

NewsletterPro.components.LanguageSelect.initBySelection = function(selection)
{
	var box = NewsletterPro;
	$.each(selection, function(key, item){
		item = $(item);
		new box.components.LanguageSelect({
			selector: item,
			languages: box.dataStorage.get('all_languages'),
		});
	});
};

NewsletterPro.components.LanguageSelect.updateLanguages = function()
{
	var box = NewsletterPro,
		idLang = box.dataStorage.getNumber('id_selected_lang');

	if (idLang > 0)
	{
		$.each($('[data-lang]'), function(k, item){
			item = $(item);

			var isDataVisible = item.data('visible') == '1' ? true : false;

			if (Number(item.data('lang')) == idLang)
			{
				if (isDataVisible)
				{
					item.removeClass('np-lang-visible-hide');
					item.show();
				}
				else
				{
					item.show();
				}
			}
			else
			{
				if (isDataVisible) {
					item.addClass('np-lang-visible-hide');
				} else {
					item.hide();
				}
			}
		});
	}
};

NewsletterPro.components.LanguageSelect.fixDropDownPosition = function()
{
	this.parseInstances(function(instance){
		instance.fixDropDownPosition();
	});
};