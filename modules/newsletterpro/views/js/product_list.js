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

function gkProductList(cfg)
{
	if (!(this instanceof gkProductList))
		return new gkProductList(cfg);

	var self           = this,
		items          = [],
		eItems         = [],
		currentItem    = null,
		lastItemClass  = null,
		l              = cfg.l || function(value){return value;},
		inList         = cfg.inList || function(){return true;},
		addCallback    = cfg.add;
		removeCallback = cfg.remove;
		dom            = {};

	this.createItems = function(data) 
	{
		createItems(data);
	};

	this.removeItems = function()
	{
		return removeItems();
	};

	this.getItems = function(bool)
	{
		return getItems(bool);
	};

	init();

	function init()
	{
		initDom();
	}

	function initDom()
	{
		dom['element'] = cfg.element;
	}

	function createItems (data)
	{
		removeItems();

		$.each(data, function(index, item) 
		{
			item.thumb = { 
				'path' : item.thumb_path, 
				'width' : item.thumb_width, 
				'height' : item.thumb_height
			};

			add( createItem( getItemTemplate(item), item ) );
		});
	}

	function removeItems()
	{
		items = [];
		eItems = [];
		currentItem = null;
		lastItemClass = null;
		empty();
	}

	function empty()
	{
		dom.element.empty();
	}

	function append(item)
	{
		dom.element.append(item);
	}

	function add(item)
	{
		items.push(item);
	}

	function getItemTemplate(item)
	{
		var addButton    = '<td class="options">\
								<a class="btn btn-default add-product" href="javascript:{}" data-id="'+item.id_product+'" data-value="-1" >\
									<i class="icon icon-plus-circle"></i> <span>'+l('add')+'</span>\
								</a>\
							</td>';
		var removeButton = '<td class="options">\
								<a class="btn btn-default add-product" href="javascript:{}" data-id="'+item.id_product+'" data-value="1">\
									<i class="icon icon-minus-circle"></i> <span>'+l('remove')+'</span>\
								</a>\
							</td>';

		lastItemClass = (lastItemClass == null) ? 'even' : ((lastItemClass == 'even') ? 'odd' : 'even');

		function isActive() 
		{
			return ( parseInt(item.active) ? true : false );
		}

		function inStock() 
		{
			return ( parseInt(item.quantity) ? true : false );
		}

		var template = '<tr class="'+lastItemClass+'">';
			template += (NewsletterPro.dataStorage.getNumber('configuration.DISPLAY_PRODUCT_IMAGE') == true ) ? '<td class="image"><img src="'+item.thumb.path+'" alt="" height="'+item.thumb.height+'" width="'+item.thumb.width+'"></td>' : '';
			template +='<td class="name '+(isActive()?'':'inactive')+'">'+item.name+'</td>';
			template +='<td class="reference '+(isActive()?'':'inactive')+'">'+(item.reference != null ? item.reference : '')+'</td>';
			template +='<td class="search-price '+(isActive()?'':'inactive')+'">'+(item.price_display != null ? item.price_display : '')+'</td>';
			template +='<td class="search-active">'+(isActive() ? 
														'<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>' : 
														'<span class="list-action-enable action-disabled"><i class="icon icon-remove"></i></span>'
													)+'</td>';
			template +='<td class="search-stock '+(inStock()?'':'inactive')+'">'+(item.quantity != null ? item.quantity : '<span style="color: red;">0</span>')+'</td>';
			template += ( inList(item) ? addButton : removeButton );
			template +='</tr>';

		template = $(template);
		append(template);
		return template;
	}

	function createItem (instance, data)
	{
		var instance = instance,
			button   = instance.find('a.add-product'),
			text     = button.find('span'),
			img      = button.find('img'),
			value    = button.data('value'),
			id       = button.data('id');

		var item = ({
			init: function()
			{
				this.instance = instance;
				this.button   = button;
				this.value    = value;
				this.data     = data;
				this.text     = text;
				this.img      = img;
				this.id       = id;

				this.initEvents();
				return this;
			},

			initEvents: function()
			{
				var item = this;
				this.button.on('click', function(event) 
				{
					currentItem = item;
					currentItem.toggleValue();
					event.stopPropagation();
				});
			},

			click : function()
			{
				this.button.trigger('click');
				return this.val();
			},

			val: function()
			{
				return this.value;
			},

			toggleValue: function () 
			{
				if( this.val() == '1')
					this.remove();
				else
					this.add();
			},

			remove: function() 
			{
				this.value = '-1';
				this.text.html(l('add'));
				this.img.attr('src', NewsletterPro.dataStorage.get('module_img_path') + 'add.gif');
				if ($.isFunction(removeCallback))
					removeCallback(this.data, this);
			},

			add: function ()
			{
				this.value = '1';
				this.text.html(l('remove'));
				this.img.attr('src', NewsletterPro.dataStorage.get('module_img_path') + 'remove.gif');
				if ($.isFunction(addCallback))
					addCallback(this.data, this);
			}

		}.init());	
		return item;
	}

	function getItems(bool) 
	{
		if( bool == true ) 
		{
			var itm = $.grep(items, function(item) 
			{
				return item.val() == '-1';
			});
			return itm;
		} 
		else if ( bool == false )
		{
			var itm = $.grep(items, function(item) 
			{
				return item.val() == '1';
			});
			return itm;
		} 
		else 
		{
			return items;
		}
	}
	return this;
}