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

NewsletterPro.namespace('components.Product');
NewsletterPro.components.Product = function Product(cfg)
{
	if (!(this instanceof Product))
		return new Product(cfg);

	var self = this,
		static = NewsletterPro.components.Product,
		box = NewsletterPro;

	this.static = static;
	this.data = cfg.data;
	this.templateHeader = {};
	this.setTemplate(cfg.template);
	this.view = cfg.view;
	this.viewInstance = {};
	this.viewInfo = {};
	this.inView = [];

	this.edit = {};

	this.id = Number(this.data.variables.id_product);

	this.static.products.push(this);
	this.static.lastPosition++;
	this.position = this.static.lastPosition;
	this.static.view = this.view;

	if (!static.imageType)
		static.imageType = box.dataStorage.get('configuration.IMAGE_TYPE');
};

NewsletterPro.components.Product.prototype.setTemplate = function(template)
{
	var box = NewsletterPro,
		static = NewsletterPro.components.Product,
		sliderImageSize = box.modules.selectProducts.vars.sliderImageSize;

	this.templateHeader = this.getHeader(template);

	if (box.objSize(this.templateHeader))
	{
		if (this.templateHeader.hasOwnProperty('loadImageSize')) {
			static.setImageSize(Number(this.templateHeader.loadImageSize));
		}
	}
	else
	{
		static.imageWidth = sliderImageSize.getValue();
	}

	// remove headers
	// this.template = template.replace(/\{columns=\d+\}|<!--\s+\{columns=\d+\}\s+-->|<!-- start header -->[\s\S]*?<!-- end header -->/g, '');

	this.template = box.components.ProductTemplate({
		header: this.templateHeader,
		template: template,
	});

	// console.log('is template', this.template.isTemplate());
};

NewsletterPro.components.Product.prototype.getHeader = function(content)
{
	return NewsletterPro.parseProductHeader(content);
};

NewsletterPro.components.Product.prototype.addToView = function()
{
	var static = NewsletterPro.components.Product;

	if (this.isInView())	
		return false;

	this.inView.push(this.id);

	for (var idLang in this.view)
	{
		if (static.layout != null && !static.layout.hasOwnProperty(idLang))
		{
			static.layout[idLang] = $('\
				<table class="np-newsletter-layout-'+idLang+'" style="margin: 0 auto; padding; 0; border-collapse: collapse; border-spacing: 0;">\
				</table>\
			');
		}

		var container = this.view[idLang],
			item = $('<div>');

		this.viewInstance[idLang] = item;

		if (static.layout != null)
		{
			var lastRow = static.layout[idLang].find('[class^="np-newsletter-row-"]:last');

			if (lastRow.length > 0)
			{
				var tr,
				 	tds = lastRow.find('[class^="np-newsletter-column-product-id-"]'),
					num = tds.length,
					td = $('<td class="np-newsletter-column-product-id-'+this.id+'"></td>');

				td.append(item);

				if (num % static.columns == 0)
				{
					var c = lastRow.attr('class'),
						rowCount = Number(c.match(/np-newsletter-row-(\d+)/)[1]) + 1;

					tr = $('<tr class="np-newsletter-row-'+rowCount+'"></tr>');
					tr.append(td);
					tr.insertAfter(lastRow);
				}
				else
				{
					lastRow.append(td);
				}
			}
			else
			{
				var tr = $('<tr class="np-newsletter-row-1"></tr>'),
					td = $('<td class="np-newsletter-column-product-id-'+this.id+'"></td>');

				td.append(item);
				tr.append(td);
				static.layout[idLang].append(tr);

				this.view[idLang].sortable('disable');
				this.view[idLang].empty();
				this.view[idLang].append(static.layout[idLang]);
			}
		}
		else
		{
			if (this.template.isTemplate()) {

				var vars = this.getVars(idLang),
					columns = Number(this.template.tree.get('np-columns')),
					responsiveTable = $('<table class="np-responsive-table-container" border="0" cellpadding="0" cellspacing="0"></table>'),
					currentRow = $(this.template.tree.getRender('np-row', vars)),
					rowSeparator = $(this.template.tree.getRender('np-row-separator', vars)),
					productSeparator = $(this.template.tree.getRender('np-product-separator', vars));

				if (container.find('.np-responsive-table-container').length == 0) {
					container.append(responsiveTable);
				} else {
					responsiveTable = container.find('.np-responsive-table-container:last');
				}

				if (responsiveTable.find('.np-row-products-target:last').length == 0)  {
					responsiveTable.append(currentRow);
				}

				var len = static.products.length -1;
				if (len % columns == 0) {
					// add a new row

					responsiveTable.append(currentRow);
					responsiveTable.append(rowSeparator);
					currentRowTarget = responsiveTable.find('.np-row-products-target:last');
					currentRowTarget.append(item);
						
					if (columns > 1) {
						currentRowTarget.append(productSeparator);
					}

				} else {
					currentRowTarget = responsiveTable.find('.np-row-products-target:last');
					currentRowTarget.append(item);

					if (len % columns < columns - 1) {
						currentRowTarget.append(productSeparator);
					}
				}

			} else {
				container.append(item);
			}
		}
	}

	return true;
};

NewsletterPro.components.Product.prototype.isInView = function()
{
	return (this.inView.indexOf(this.id) != -1 ? true : false);
};

NewsletterPro.components.Product.prototype.render = function(idLang, idCurrency, idProductAttribute)
{
	var self = this,
		static = NewsletterPro.components.Product,
		box = NewsletterPro;
	
	if (!this.isInView())
		this.addToView();

	var render = function(lang)
	{
		var render = $(self.renderWithOptions(lang, idCurrency, idProductAttribute));

		// before render
		if (static.productWidth > 0)
			render.width(static.productWidth);
		else if (static.productWidth == 0)
			render.width('auto');

		// replace rander
		self.viewInstance[lang].replaceWith(render);
		self.viewInstance[lang] = render;

		if (static.productWidth > 0 && static.productWidth < render.width()) {
			static.productWidth = render.width();
		}

		// add edit button if not exists
		var edit = render.find('.np-edit-product-menu');

		if (!edit.length)
		{
			edit = $('\
				<div data-id="'+self.id+'" style="position: absolute;" class="np-edit-product-menu">\
					<a data-id="'+self.id+'" data-on-lang="'+lang+'" href="javascript:{}" class="np-edit-product-menu-btn-move np-edit-product-menu-btn pull-left">\
						<i class="icon icon-move"></i>\
					</a>\
					<a data-id="'+self.id+'" data-on-lang="'+lang+'" href="javascript:{}" onclick="NewsletterPro.components.Product.editProduct('+self.id+', '+lang+');" class="np-edit-product-menu-btn-edit np-edit-product-menu-btn pull-left">\
						<i class="icon icon-edit"></i>\
					</a>\
					<a data-id="'+self.id+'" data-on-lang="'+lang+'" href="javascript:{}" onclick="NewsletterPro.components.Product.removeProduct('+self.id+', '+lang+');" class="np-edit-product-menu-btn-remove np-edit-product-menu-btn pull-right">\
						<i class="icon icon-remove"></i>\
					</a>\
				</div>\
			');

			var select = render.find('td'),
				move = edit.find('.np-edit-product-menu-btn-move');

			if (!select.length)
				select = render.find('div');

			select.first().css('position', 'relative').prepend(edit);
			
			if (move.length)
			{
				if (static.layout == null)
					move.show();
				else
					move.hide();

				if (self.template.isTemplate()) {
					move.hide();
				}
			}
		}
	};

	if (idLang != null)
	{
		render(idLang);
	} 
	else
	{
		this.parseViewInstances(function(lang, item){
			render(lang);
		});
	}

	static.fixHeightAndWidthAndSliders();
};

NewsletterPro.components.Product.prototype.getViewInfo = function(idLang, idCurrency, idProductAttribute)
{
	var box = NewsletterPro;

	if (idCurrency == null)
	{
		if (!this.viewInfo.hasOwnProperty(idLang))
			idCurrency = box.dataStorage.getNumber('configuration.CURRENCY');
		else
			idCurrency = this.viewInfo[idLang].currency;
	}

	if (idProductAttribute == null)
	{
		if (!this.viewInfo.hasOwnProperty(idLang))
			idProductAttribute = 0;
		else
			idProductAttribute = this.viewInfo[idLang].id_product_attribute;
	}

	return {
		currency: idCurrency,
		id_product_attribute: idProductAttribute
	};
};

NewsletterPro.components.Product.prototype.setViewInfo = function(idLang, idCurrency, idProductAttribute)
{
	this.viewInfo[idLang] = {
		currency: idCurrency,
		id_product_attribute: idProductAttribute,
	};
};

NewsletterPro.components.Product.prototype.renderWithOptions = function(idLang, idCurrency, idProductAttribute)
{
	var box = NewsletterPro,
		static = box.components.Product,
		vars = this.getVars(idLang, idCurrency, idProductAttribute),
		template = this.template.html();

	// if (this.template.isTemplate()) {

	// 	var tree = {};
	// 	this.template.tree.parse(function(key, value) {
	// 		tree[key] = new box.components.ProductRender(value, vars).render();
	// 	});

	// } else {
	// var template = this.template.html();
	return new box.components.ProductRender(template, vars).render();
	// }
};

NewsletterPro.components.Product.prototype.getVars = function(idLang, idCurrency, idProductAttribute)
{
	var box = NewsletterPro,
		static = NewsletterPro.components.Product,
		info = this.getViewInfo(idLang, idCurrency, idProductAttribute);

	idCurrency = info.currency;
	idProductAttribute = info.id_product_attribute;

	this.setViewInfo(idLang, idCurrency, idProductAttribute);

	var self = this,
		productRender,
		vars,
		data = this.data,
		variables = data.variables,
		variablesLang = data.variables_lang,
		prices = (
				data.prices.hasOwnProperty(idCurrency) ?
				data.prices[idCurrency] :
				false
			),
		images = data.images,
		image_type = static.imageType,
		cover = images.cover_images[idLang][image_type],
		attributes_groups = data.attributes_groups.attributes_groups,
		attributes_groups_lang = (
			attributes_groups.hasOwnProperty(idLang) && $.isArray(attributes_groups[idLang]) ? 
			attributes_groups[idLang] :
			false
		),
		combination_images = (
				data.attributes_groups.combination_images.hasOwnProperty(idLang) && data.attributes_groups.combination_images[idLang].hasOwnProperty(idProductAttribute) ?
				data.attributes_groups.combination_images[idLang][idProductAttribute] :
				false
			),
		prices_attributes = (
				data.prices_attributes.hasOwnProperty(idProductAttribute) && data.prices_attributes[idProductAttribute].hasOwnProperty(idCurrency) ?
				data.prices_attributes[idProductAttribute][idCurrency] :
				false
			),
		attributeImage,
		separator = data.attributes_combinations.attribute_anchor_separator,
		attribute_link,
		size,
		getImageSize = function(width, height)
		{
			var imageWidth = Number(width),
				imageHeight = Number(height),
				imageRatio = imageHeight / imageWidth;

			if (static.imageWidth > 0)
			{
				imageWidth = static.imageWidth;
				imageHeight = Math.round(imageWidth * imageRatio);
			}

			return {
				width: Number(imageWidth),
				height: Number(imageHeight)
			};
		};

	vars = {};

	for (var name in variables) {
		vars[name] = variables[name];
	}

	// setup the reference
	if (attributes_groups_lang && Number(idProductAttribute) > 0)
	{
		var attributes = {};
		for (var i = 0; i < attributes_groups_lang.length; i++)
		{
			var attribute = attributes_groups_lang[i],
				id = attribute.id_product_attribute;

			if (!attributes.hasOwnProperty(id))
				attributes[id] = [];

			attributes[id].push(attribute);
		}

		if (attributes.hasOwnProperty(idProductAttribute))
		{
			var items = attributes[idProductAttribute];
			// setup the reference
			if ($.trim(items[0].reference).length > 0)
				vars['reference'] = items[0].reference;

			// setup the attribute link
			attribute_link = '';
			if (box.dataStorage.get('isPS16'))
			{
				for (var i = 0; i < items.length; i++) {
					attribute_link += '/' + String(items[i].id_attribute + separator + items[i].group_name + separator + items[i].attribute_name).toLowerCase();
				}
			}
			else
			{
				for (var i = 0; i < items.length; i++) {
					attribute_link += '/' + String(items[i].group_type + separator + items[i].attribute_name).toLowerCase();
				}
			}
		}
	}

	for (var name in variablesLang)
	{
		if (variablesLang[name].hasOwnProperty(idLang))
			vars[name] = variablesLang[name][idLang];
	}

	vars['link'] = box.linkAdd(vars['link'], 'SubmitCurrency=yes&id_currency=' + idCurrency);

	if (attribute_link && attribute_link.length > 0)
		vars['link'] = box.linkAdd(vars['link'], '', attribute_link);

	if (static.productNameLength != -1)
		vars['name'] = box.trimString(vars['name'], static.productNameLength);

	if (static.productShortDescriptionLength != -1)
		vars['description_short'] = box.trimString(vars['description_short'], static.productShortDescriptionLength);

	if (static.productDescriptionLength != -1)
		vars['description'] = box.trimString(vars['description'], static.productDescriptionLength);

	// if there are product attributes set that price
	if (prices_attributes)
		prices = prices_attributes;

	if (prices)
	{
		for (var name in prices)
		{
			vars[name] = prices[name];
		}
	}

	// add combination images
	if (combination_images && $.isArray(combination_images) && combination_images.length > 0)
	{
		var first_image = combination_images[0].images;

		if (first_image.hasOwnProperty(image_type))
		{
			attributeImage = first_image[image_type];
		}
	}

	if (attributeImage)
	{		
		size = getImageSize(attributeImage.width, attributeImage.height);

		vars['image_path'] = attributeImage.link;
		vars['image_width'] = size.width;
		vars['image_height'] = size.height;
	}
	else
	{
		size = getImageSize(cover.width, cover.height);

		vars['image_path'] = cover.link;
		vars['image_width'] = size.width;
		vars['image_height'] = size.height;
	}
	
	return vars;
};

NewsletterPro.components.Product.prototype.parseViewInstances = function(func)
{
	for (var idLang in this.viewInstance)
	{
		func(idLang, this.viewInstance[idLang]);
	}
};

NewsletterPro.components.Product.prototype.remove = function()
{
	var box = NewsletterPro,
		static = NewsletterPro.components.Product;
		index = this.static.products.indexOf(this);

	if (index !== -1)
	{
		this.static.products.splice(index, 1);

		for (var idLang in this.viewInstance)
		{
			var item = this.viewInstance[idLang];

			if (Number(item.data('id')) == Number(this.id))
				item.remove();
		}

		// trigger the remove event
		box.modules.selectProducts.productSelection.publish('remove', {
			idProduct: this.id,
			ids: static.getProductsId(),
		});

		// refersh the layout
		static.setLayout();

		return true;
	}

	return false;
};

NewsletterPro.components.Product.prototype.getEditData = function(idLang)
{
	var data = this.data,
		editData = [],
		variables = data.variables,
		variables_lang = data.variables_lang,
		name = variables_lang.name[idLang],
		images = data.images,
		attributes_groups = (
			data.attributes_groups.attributes_groups && data.attributes_groups.attributes_groups.hasOwnProperty(idLang) ?
			data.attributes_groups.attributes_groups[idLang] :
			false
		),
		separator = data.attributes_combinations.attribute_anchor_separator,
		currency = this.viewInfo[idLang].currency,
		idSelectedAttribute = this.viewInfo[idLang].id_product_attribute,
		price = data.prices[currency].price_display,
		prices_attributes = data.prices_attributes,
		selected = (!Number(idSelectedAttribute) ? true : false);

	editData.push({
		id: 0,
		id_product: this.id,
		id_lang: idLang,
		id_product_attribute: 0,
		is_color_group: 0,
		attribute_color: '',
		image: images.cover_images[idLang][data.images.image_type_small].link,
		attribute_name: 'Default',
		reference: variables.reference,
		price: price,
		available_date: '',
		selected: selected,
	});

	if (attributes_groups)
	{
		var combination_images;
		var attributes = {};
		
		for (var i = 0; i < attributes_groups.length; i++)
		{
			var attribute = attributes_groups[i],
				id = attribute.id_product_attribute;

			if (!attributes.hasOwnProperty(id))
				attributes[id] = [];

			attributes[id].push(attribute);
		}

		for (var idProductAttribute in attributes)
		{
			idProductAttribute = Number(idProductAttribute);

			var items = attributes[idProductAttribute],
				attribute_name = '',
				image,
				is_color_group = 0,
				attribute_color = '',
				available_date,
				reference,
				price_attribute = (
					prices_attributes.hasOwnProperty(idProductAttribute) && 
					prices_attributes[idProductAttribute].hasOwnProperty(currency) ?
						prices_attributes[idProductAttribute][currency].price_display :
						false
				);

			for (var i = 0; i < items.length; i++)
			{
				var item = items[i];
				attribute_name += item.public_group_name + ' ' + separator + ' ' + item.attribute_name + ', ';

				if (Number(item.is_color_group))
				{
					is_color_group = 1;
					attribute_color = item.attribute_color;
				}

				if (i == 0)
				{
					available_date = item.available_date;
					
					if ($.trim(item.reference).length > 0)
						reference = item.reference;
					else
						reference = variables.reference
				}
			}

			attribute_name = attribute_name.replace(/, $/, '');

			combination_images = (
				data.attributes_groups.combination_images && 
				data.attributes_groups.combination_images.hasOwnProperty(idLang) && 
				data.attributes_groups.combination_images[idLang].hasOwnProperty(idProductAttribute) ?
				data.attributes_groups.combination_images[idLang][idProductAttribute] :
				false
			);

			if (combination_images)
				image = combination_images[0].images[data.images.image_type_small].link;
			else
				image = images.cover_images[idLang][data.images.image_type_small].link;

			if (price_attribute)
			{
				price = price_attribute;
			}

			var attribute = {
					id: idProductAttribute,
					id_product: this.id,
					id_lang: idLang,
					id_product_attribute: idProductAttribute,
					is_color_group: is_color_group,
					attribute_color: attribute_color,
					image: image,
					attribute_name: attribute_name,
					reference: reference,
					price: price,
					available_date: available_date,
					selected: (Number(idSelectedAttribute) ==  Number(idProductAttribute) ? true : false),
				};

			editData.push(attribute);
		}
	}

	return editData;
};

NewsletterPro.components.Product.products = [];
NewsletterPro.components.Product.view = {};
NewsletterPro.components.Product.lastPosition = 0;
NewsletterPro.components.Product.imageType = null;
NewsletterPro.components.Product.imageWidth = 0;

NewsletterPro.components.Product.productWidth = -1;
// NewsletterPro.components.Product.productWidthTrigger = false;
NewsletterPro.components.Product.productNameLength = -1;
NewsletterPro.components.Product.productShortDescriptionLength = -1;
NewsletterPro.components.Product.productDescriptionLength = -1;

NewsletterPro.components.Product.layout = null;

NewsletterPro.components.Product.editProduct = function(id, idLang)
{	
	var box = NewsletterPro,
		static = NewsletterPro.components.Product,
		product = static.getInstanceById(id),
		win = box.modules.selectProducts.productSelection.winEdit;

	if (product && win)
	{
		var data = product.data,
			variables = data.variables,
			variables_lang = data.variables_lang,
			name = variables_lang.name[idLang],
			editData = product.getEditData(idLang);

		var title = win.getHeader().replace(/%s/, name);
		win.setHeader(title);

		win.dataSource.setData(editData);
		win.dataSource.sync();
		win.show();
	}
};

NewsletterPro.components.Product.removeProduct = function(id, idLang)
{
	var static = NewsletterPro.components.Product,
		product = static.getInstanceById(id);
	
	if (product)
		product.remove();
};

NewsletterPro.components.Product.removeAllProducts = function()
{
	for (var i = this.products.length - 1; i >= 0; i--)
	{
		var product = this.products[i];
		product.remove();
	}
};

NewsletterPro.components.Product.getInstanceById = function(id)
{
	var static = this;

	return static.parseProducts(function(idp, product){
		if (id == idp)
			return product;
	});
};

NewsletterPro.components.Product.getProductsId = function()
{
	var ids =[];
	this.parseProducts(function(id, product){
		ids.push(Number(id));
	});

	return ids;
};

NewsletterPro.components.Product.parseProducts = function(func)
{
	var static = this;

	for (var i = 0; i < static.products.length; i++)
	{
		var result = func(static.products[i].id, static.products[i]);
		if (result !== undefined)
			return result;
	}
};

NewsletterPro.components.Product.count = function()
{
	return this.products.length;
};

NewsletterPro.components.Product.first = function()
{
	if (this.products.length > 0)
		return this.products[0];
	return false;
};

NewsletterPro.components.Product.setImageSize = function(width)
{
	var box = NewsletterPro;

	this.imageWidth = Number(width);
	this.imageType = this.getImageTypeByWidthValue(this.imageWidth);
	this.render();

	box.modules.selectProducts.displayProductsWidth();
};

NewsletterPro.components.Product.setNameLenght = function(length)
{
	var box = NewsletterPro;

	this.productNameLength = Number(length);
	this.render();

	box.modules.selectProducts.displayProductsWidth();
};

NewsletterPro.components.Product.setWidth = function(value)
{
	var box = NewsletterPro;
	this.productWidth = Number(value);
	this.render();
	
	box.modules.selectProducts.displayProductsWidth();
};

NewsletterPro.components.Product.setShortDescriptionLenght = function(length)
{
	var box = NewsletterPro;

	this.productShortDescriptionLength = Number(length);
	this.render();

	box.modules.selectProducts.displayProductsWidth();
};

NewsletterPro.components.Product.setDescriptionLength = function(length)
{
	var box = NewsletterPro;

	this.productDescriptionLength = Number(length);
	this.render();

	box.modules.selectProducts.displayProductsWidth();
};

NewsletterPro.components.Product.render = function(idLang, idCurrency, idProductAttribute)
{
	this.parseProducts(function(id, product){
		product.render(idLang, idCurrency, idProductAttribute);
	});
};

NewsletterPro.components.Product.columns = 0;

NewsletterPro.components.Product.setLayout = function(columns, sort)
{
	var static = NewsletterPro.components.Product,
		first = static.first();

	if (first) {

		if (first.template.isTemplate()) {
			var viewProducts = this.getViewProducts(sort);

			for (var idLang in this.view)
			{
				this.view[idLang].sortable('disable');
				this.view[idLang].empty();

				var container = this.view[idLang];

				if (viewProducts.hasOwnProperty(idLang))
				{
					for (var i = 0; i < viewProducts[idLang].length; i++)
					{
						var product = viewProducts[idLang][i],
							render = product.viewInstance[idLang],
							move = render.find('.np-edit-product-menu .np-edit-product-menu-btn-move'),
							vars = product.getVars(idLang);

						if (move.length > 0)
							move.hide();

						var item = render,
							columns = Number(first.template.tree.get('np-columns')),
							responsiveTable = $('<table class="np-responsive-table-container" border="0" cellpadding="0" cellspacing="0"></table>'),
							currentRow = $(first.template.tree.getRender('np-row', vars)),
							rowSeparator = $(first.template.tree.getRender('np-row-separator', vars)),
							productSeparator = $(first.template.tree.getRender('np-product-separator', vars));

						if (container.find('.np-responsive-table-container').length == 0) {
							container.append(responsiveTable);
						} else {
							responsiveTable = container.find('.np-responsive-table-container:last');
						}

						if (responsiveTable.find('.np-row-products-target:last').length == 0)  {
							responsiveTable.append(currentRow);
						}

						if (i % columns == 0) {
							// add a new row
							responsiveTable.append(currentRow);
							responsiveTable.append(rowSeparator);
							currentRowTarget = responsiveTable.find('.np-row-products-target:last');
							currentRowTarget.append(item);
								
							if (columns > 1) {
								currentRowTarget.append(productSeparator);
							}

						} else {

							currentRowTarget = responsiveTable.find('.np-row-products-target:last');
							currentRowTarget.append(item);

							if (i % columns < columns - 1) {
								currentRowTarget.append(productSeparator);
							}
						}

						// this.view[idLang].append(render);
					}
				}
			}

			// do not continue the script
			return false;
		}
	}

	var viewProducts = this.getViewProducts(sort);

	if (columns != null)
		this.columns = columns;
	else
		columns = this.columns;

	if (columns == 0)
	{
		this.layout = null;

		for (var idLang in this.view)
		{
			this.view[idLang].sortable('enable');
			this.view[idLang].empty();

			if (viewProducts.hasOwnProperty(idLang))
			{
				for (var i = 0; i < viewProducts[idLang].length; i++)
				{
					var product = viewProducts[idLang][i],
						render = product.viewInstance[idLang],
						move = render.find('.np-edit-product-menu .np-edit-product-menu-btn-move');

					if (move.length > 0)
						move.show();

					this.view[idLang].append(render);
				}
			}
		}
	}
	else
	{
		this.layout = {};

		for (var idLang in this.view)
		{
			var table = $('\
					<table class="np-newsletter-layout-'+idLang+'" style="margin: 0 auto; padding; 0; border-collapse: collapse; border-spacing: 0;">\
					</table>\
				'),
				rowCount = 0;

			this.view[idLang].sortable('disable');
			this.view[idLang].empty();
			this.view[idLang].append(table);

			if (viewProducts.hasOwnProperty(idLang))
			{
				for (var i = 0; i < viewProducts[idLang].length; i++)
				{
					var product = viewProducts[idLang][i],
						render = product.viewInstance[idLang],
						move,
						tr,
						td = $('<td class="np-newsletter-column-product-id-'+product.id+'"></td>');

					if (i % columns == 0)
					{
						rowCount++;
						tr = $('<tr class="np-newsletter-row-'+rowCount+'"></tr>');
						table.append(tr);
					}

					move = render.find('.np-edit-product-menu .np-edit-product-menu-btn-move');

					if (move.length > 0)
						move.hide();

					td.html(render);
					
					tr.append(td);
				}
			}

			this.layout[idLang] = table;
		}
	}
};

NewsletterPro.components.Product.getViewProducts = function(sort)
{
	var box = NewsletterPro;

	// sorted id
	sort = sort || [];

	if (!sort.length)
	{
		var idSelectedLang = box.dataStorage.get('id_selected_lang');

		if (this.view.hasOwnProperty(idSelectedLang))
		{
			var info = this.view[idSelectedLang].find('.newsletter-pro-product');

			if (info.length > 0)
			{
				$.each(info, function(i, item){
					sort.push($(item).data('id'));
				});
			}
		}
	}

	var obj = {};

	this.parseProducts(function(id, product){
		product.parseViewInstances(function(lang, item){
			if (!obj.hasOwnProperty(lang))
				obj[lang] = [];

			if (sort.length > 0)
			{
				obj[lang][sort.indexOf(Number(id))] = product;
			}
			else
			{
				obj[lang].push(product);
			}
		});
	});

	return obj;
};

NewsletterPro.components.Product.fixWidthAndSlidersTimeout = null;

NewsletterPro.components.Product.fixHeightAndWidthAndSliders = function()
{	
	var box = NewsletterPro,
		static = this,
		idSelectedLang = box.dataStorage.get('id_selected_lang'),
		info = {},
		sliderImageSize = box.modules.selectProducts.vars.sliderImageSize,
		sliderProductsWidth = box.modules.selectProducts.vars.sliderProductsWidth,
		minHeight = {},
		productsHeight = {};

	if (static.imageWidth == 0)
		static.imageWidth = sliderImageSize.getValue();

	if (static.fixWidthAndSlidersTimeout != null)
		clearTimeout(static.fixWidthAndSlidersTimeout);

	static.fixWidthAndSlidersTimeout = setTimeout(function(){
		static.parseProducts(function(id, product){
			// fix width
			if (product.viewInstance.hasOwnProperty(idSelectedLang))
			{
				var render = product.viewInstance[idSelectedLang],
					image = render.find('.newsletter-pro-image');

				if (image.length > 0)
				{
					var imageWidth = image.width(),
						imageHeight = image.height(),
						width = render.width();

					info[id] = {
						width: width,
						imageWidth: imageWidth,
						imageHeight: imageHeight,
						imageRatio: imageHeight / imageWidth,
						diff: width - imageWidth, 
					};
				}
			}

			// fix height
			product.parseViewInstances(function(lang, item){

				var height = item.height();

				if (!minHeight.hasOwnProperty(lang))
					minHeight[lang] = 0;

				if (!productsHeight.hasOwnProperty(lang))
					productsHeight[lang] = {};

				productsHeight[lang][id] = height;

				if (height > minHeight[lang])
					minHeight[lang] = height;
			});
		});

		static.parseProducts(function(id, product){

			// fix product height for the standart templates
			// if (product.template.isTemplate())
			// {
				// product.parseViewInstances(function(lang, render){

				// 	// fix height
				// 	if (minHeight[lang] > 0 && productsHeight[lang][id] < minHeight[lang])
				// 	{
				// 		var selector = '.np-product-fix-height',
				// 			lastTd = render.find(selector);

				// 		if (lastTd.length > 0)
				// 		{
				// 			var height = productsHeight[lang][id],
				// 				diff = minHeight[lang] - height;
				// 				// padding = diff + parseInt(lastTd.css('padding-bottom'));

				// 				console.log(height, minHeight[lang]);
				// 			// console.log(diff);

				// 			// lastTd.height(diff);

				// 			// lastTd.css('padding-bottom', padding + 'px');
				// 		}
				// 		// else
				// 		// {
				// 		// 	// render.css('min-height', minHeight + 'px');
				// 		// }
				// 	}

				// });
			// }

			if (!product.template.isTemplate() && product.template.columns > 1)
			{
				product.parseViewInstances(function(lang, render){
					// fix width
					if (info.hasOwnProperty(id))
						var data = info[id];

					// fix height
					if (minHeight[lang] > 0 && productsHeight[lang][id] < minHeight[lang])
					{
						var selector = 'td:first',
							lastTd = render.find(selector);

						if (lastTd.length > 0)
						{
							var height = productsHeight[lang][id],
								diff = minHeight[lang] - height,
								padding = diff + parseInt(lastTd.css('padding-bottom'));

							lastTd.css('padding-bottom', padding + 'px');
						}
						else
						{
							render.css('min-height', minHeight + 'px');
						}
					}

				});
			}
		});

	}, 100);
};

NewsletterPro.components.Product.getImageTypeByWidthValue = function(value)
{	
	var box = NewsletterPro,
		imageSize = box.dataStorage.get('images_size'),
		imageType = '';

	imageSize.sort(function(a, b){
		if (parseInt(a.width) < parseInt(b.width))
			return -1;
		if (parseInt(a.width) > parseInt(b.width))
			return 1;
		return 0;
	});

	for (var i = 0; i < imageSize.length; i++)
	{
		var image = imageSize[i],
			width = Number(image.width);

		if (value > width && typeof imageSize[i + 1] !== 'undefined') {
			imageType = imageSize[i + 1].name;
		}
	}

	if (imageSize.length && !imageType.length) {
		imageType = imageSize[0].name;
	}

	return imageType;
};

NewsletterPro.components.Product.changeTemplate = function(template)
{
	this.parseProducts(function(id, product){
		product.setTemplate(template);
	});

	this.render();
};

NewsletterPro.components.Product.sortProducts = function(by, asc)
{
	var static = this;

	if (typeof asc === 'undefined')
		asc = true;

	var static = NewsletterPro.components.Product,
		productsInfo = {};

	static.parseProducts(function(id, product){
		product.parseViewInstances(function(lang, item){
			var vars = product.getVars(lang),
				price = !isNaN(Number(vars.price)) ? Number(vars.price) : 0,
				reduction = !isNaN(Number(vars.reduction)) ? Number(vars.reduction) : 0,
				discount = !isNaN(parseFloat(vars.discount_decimals)) ? parseFloat(vars.discount_decimals) : 0;
	
			if (!productsInfo.hasOwnProperty(lang))
				productsInfo[lang] = []; 

			productsInfo[lang].push({
				position: product.position,
				price: price,
				name: vars.name,
				reduction: reduction,
				discount: discount,
				item: item,
				id: product.id,
				product: product,
				vars: vars,
			});
		});
	});

	var sort = function(byVariable)
	{
		for (var idLang in productsInfo)
		{
			var info = productsInfo[idLang],
				sortByIds = [],
				sorted = info.sort(function(objA, objB){

					a = objA[byVariable];
					b = objB[byVariable];

					if (asc)
					{
						if (a < b)
							return -1;
						
						if (a > b)
							return 1;
					}
					else
					{
						if (b == null)
							return -1;

						if (a < b)
							return 1;
					}

					return 0;
				});

			if (static.layout != null)
			{
				for (var i = 0; i < info.length; i++)
					sortByIds.push(Number(info[i].id));

				static.setLayout(null, sortByIds);
			}
			else
			{
				var first = static.first();
				if (first) {

					if (first.template.isTemplate())
					{
						var container = static.view[idLang];
						container.empty();
						var len = 0;
						$.each(sorted, function(i, obj){
							var item = obj.item,
								columns = Number(first.template.tree.get('np-columns')),
								responsiveTable = $('<table class="np-responsive-table-container" border="0" cellpadding="0" cellspacing="0"></table>'),
								currentRow = $(first.template.tree.getRender('np-row', obj.vars)),
								rowSeparator = $(first.template.tree.getRender('np-row-separator', obj.vars)),
								productSeparator = $(first.template.tree.getRender('np-product-separator', obj.vars));

							if (container.find('.np-responsive-table-container').length == 0) {
								container.append(responsiveTable);
							} else {
								responsiveTable = container.find('.np-responsive-table-container:last');
							}

							if (responsiveTable.find('.np-row-products-target:last').length == 0)  {
								responsiveTable.append(currentRow);
							}

							if (len % columns == 0) {
								// add a new row
								responsiveTable.append(currentRow);
								responsiveTable.append(rowSeparator);
								currentRowTarget = responsiveTable.find('.np-row-products-target:last');
								currentRowTarget.append(item);
									
								if (columns > 1) {
									currentRowTarget.append(productSeparator);
								}

							} else {

								currentRowTarget = responsiveTable.find('.np-row-products-target:last');
								currentRowTarget.append(item);

								if (len % columns < columns - 1) {
									currentRowTarget.append(productSeparator);
								}
							}

							len++;
						});

					}
					else
					{
						$.each(sorted, function(i, obj){
							static.view[idLang].append(obj.item);
						});
					}

				}


/*				var columns = Number(this.template.tree.get('np-columns')),
					responsiveTable = $('<table class="np-responsive-table-container" border="0" cellpadding="0" cellspacing="0"></table>'),
					currentRow = $(this.template.tree.get('np-row')),
					rowSeparator = $(this.template.tree.get('np-row-separator')),
					productSeparator = $(this.template.tree.get('np-product-separator'));

				if (container.find('.np-responsive-table-container').length == 0) {
					container.append(responsiveTable);
				} else {
					responsiveTable = container.find('.np-responsive-table-container:last');
				}

				if (responsiveTable.find('.np-row-products-target:last').length == 0)  {
					responsiveTable.append(currentRow);
				}

				var len = static.products.length -1;
				if (len % columns == 0) {
					// add a new row

					responsiveTable.append(currentRow);
					responsiveTable.append(rowSeparator);
					currentRowTarget = responsiveTable.find('.np-row-products-target:last');
					currentRowTarget.append(item);
						
					if (columns > 1) {
						currentRowTarget.append(productSeparator);
					}

				} else {
					currentRowTarget = responsiveTable.find('.np-row-products-target:last');
					currentRowTarget.append(item);

					if (len % columns < columns - 1) {
						currentRowTarget.append(productSeparator);
					}
				}*/

			}
		}
	};

	switch(by)
	{
		case 'price':
			sort('price');
		break;

		case 'name':
			sort('name');
		break;

		case 'reduction':
			sort('reduction');
		break;

		case 'discount':
			sort('discount');
		break;

		case 'position':
		default:
			sort('position');
		break;
	}
};