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

NewsletterPro.namespace('components.CurrencySelect');
NewsletterPro.components.CurrencySelect = function CurrencySelect(cfg)
{
	if (!(this instanceof CurrencySelect))
		return new CurrencySelect(cfg);
	
	var box = NewsletterPro,
		selfStatic = box.components.CurrencySelect,
		self = this;

	box.extendSubscribeFeature(this);

	this.cfg = cfg;
	this.currency_id = 0;
	this.selector = cfg.selector;
	this.currencies = cfg.currencies;
	this.dom = this.buildTemplate(this.selector);
	this.id = '#'+this.selector.attr('id');

	this.fixDropDownPosition();

	this.dom.dropDown.focusout(function(){
		self.close();
	});

	this.dom.header.on('click', function(event){
		self.headerClick.call(self, header);
	});

	selfStatic.addInstance(this.id, this);
};

NewsletterPro.components.CurrencySelect.prototype.on = function(evt, id, func)
{
	this.subscribe('change'+id, func);
};

NewsletterPro.components.CurrencySelect.prototype.setHeader = function(value)
{
	this.dom.headerText.html(value);
};

NewsletterPro.components.CurrencySelect.prototype.getCfg = function()
{
	return this.cfg;
};

NewsletterPro.components.CurrencySelect.prototype.headerClick = function()
{
	this.toggle();
};

NewsletterPro.components.CurrencySelect.prototype.click = function(currency, key)
{
	this.dom.headerText.html(currency.iso_code);
	this.close();
	if (typeof this.cfg.click === 'function')
	{
		this.cfg.click(currency, key);
	}

	this.currency_id = Number(currency.id_currency);

	this.publish('change'+this.id, this.currency_id);
};

NewsletterPro.components.CurrencySelect.prototype.val = function()
{
	return this.currency_id;
};

NewsletterPro.components.CurrencySelect.prototype.open = function()
{
	var dom = this.dom;
	
	dom.dropDown.show();
	setTimeout(function(){
		dom.dropDown.focus();
	}, 1);
};

NewsletterPro.components.CurrencySelect.prototype.close = function()
{
	this.dom.dropDown.hide();
};

NewsletterPro.components.CurrencySelect.prototype.toggle = function()
{
	if (this.dom.dropDown.is(':visible'))
		this.close();
	else
		this.open();
};

NewsletterPro.components.CurrencySelect.prototype.fixDropDownPosition = function()
{
	var dom = this.dom;

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
};

NewsletterPro.components.CurrencySelect.prototype.buildTemplate = function(selector)
{
	var box = NewsletterPro,
		self = this;

	var header = $('<button class="np-currency-header btn btn-default dropdown-toggle" tabindex="-1"> </button>');
	var headerText = $('<span style="margin-right: 5px; display: inline-block;"></span>');
	var headerIcon = $('<i class="icon icon-caret-down"> </i> ');
	var dropDown = $('<ul id="currency_menu_'+box.uniqueId()+'" class="np-currency-menu dropdown-menu" style="display: none;"></ul>');
	var rows = [];

	selector.css({
		'position': 'relative'
	});

	header.append(headerText);
	header.append(headerIcon);
	selector.append(header);
	selector.append(dropDown);

	this.parseCurrencyes(function(currency, key){
		var rowTpl = $('<li id="currency_item_'+box.uniqueId()+'"><a href="javascript:{}" style="width: 100%;">'+currency.name+'</a></li>');

		if (currency.selected == true)
		{
			headerText.html(currency.iso_code);
			self.currency_id = Number(currency.id_currency);
		}

		rowTpl.on('click', function(event){
			self.click(currency, key);
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
};

NewsletterPro.components.CurrencySelect.prototype.parseCurrencyes = function(func)
{
	for(var i in this.currencies)
		func(this.currencies[i], i);
};

NewsletterPro.components.CurrencySelect.instances = {};

NewsletterPro.components.CurrencySelect.addInstance = function(id, value)
{
	id = id == '' ? NewsletterPro.uniqueId() : id;
	var instances = NewsletterPro.components.CurrencySelect.instances;
	instances[id] = value;
};

NewsletterPro.components.CurrencySelect.getInstanceById = function(id)
{
	var instances = NewsletterPro.components.CurrencySelect.instances;

	if (instances.hasOwnProperty(id))
		return instances[id];
	return false;
};

NewsletterPro.components.CurrencySelect.getInstances = function()
{
	return NewsletterPro.components.CurrencySelect.instances;
};

NewsletterPro.components.CurrencySelect.parseInstances = function(func)
{
	var instances = NewsletterPro.components.CurrencySelect.getInstances();
	for(var i in instances)
		func(instances[i], i);
};

NewsletterPro.components.CurrencySelect.initBySelection = function(selection)
{
	var box = NewsletterPro;

	$.each(selection, function(key, item){
		item = $(item);

		new box.components.CurrencySelect({
			selector: item,
			currencies: box.dataStorage.get('currencies'),
		});
	});
};

