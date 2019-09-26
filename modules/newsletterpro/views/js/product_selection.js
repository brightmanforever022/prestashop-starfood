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

;(function($){
	NewsletterPro.namespace('components.ProductSelection');
	NewsletterPro.components.ProductSelection = function ProductSelection(cfg)
	{
		if (!(this instanceof ProductSelection))
			return new ProductSelection(cfg);

		var self = this,
			box = NewsletterPro;

		box.extendSubscribeFeature(this);

		this.dom = {};
		this.winEdit = null;
		this.l = null;
		
		this.ready(function(dom){

			self.l = box.translations.l(box.translations.components.ProductSelection);

			var currencySelect = NewsletterPro.components.CurrencySelect.getInstanceById('#products-currency-change');

			currencySelect.on('change', '#products-currency-change', function(idCurrency){
				self.render(box.dataStorage.get('id_selected_lang'), Number(idCurrency));
			});

			dom.btnSort.on('change', function(){
				self.sort();
			});

			dom.sortOrder.on('change', function(){
				self.sort();
			});

			self.winEdit = new gkWindow({
				width: 800,
				height: 600,
				setScrollContent: 540,
				title: self.l('Edit') + ' : ' + '%s',
				className: 'np-product-attribute-edit-win',
				show: function(win) {},
				close: function(win) {},
				content: function(win) 
				{
					var template = $('\
						<div class="form-group clearfix">\
							<table id="np-edit-product-attibutes" class="table table-bordered np-edit-product-attibutes">\
								<thead>\
									<tr>\
										<th class="np-attribute-id" data-field="id">'+self.l('ID')+'</th>\
										<th class="np-attribute-image" data-template="image">'+self.l('Image')+'</th>\
										<th class="np-attribute-attribute-name" data-field="attribute_name">'+self.l('Name')+'</th>\
										<th class="np-attribute-value" data-template="value">'+self.l('Color')+'</th>\
										<th class="np-attribute-reference" data-field="reference">'+self.l('Reference')+'</th>\
										<th class="np-attribute-price" data-field="price">'+self.l('Price')+'</th>\
										<th class="np-attribute-available-date" data-field="available_date">'+self.l('Availalbe Date')+'</th>\
									</tr>\
								</thead>\
							</table>\
						</div>\
					');

					win.dataModel = new gk.data.Model({
						id: 'id',
					});

					win.dataSource = new gk.data.DataSource({
						pageSize: 9,
						transport: {
							data: [],
						},
						schema: {
							model: win.dataModel
						},
					});

					win.dataGrid = template.find('#np-edit-product-attibutes').gkGrid({
						dataSource: win.dataSource,
						selectable: true,
						currentPage: 1,
						pageable: true,
						template: 
						{
							image: function(item, value)
							{
								return '<img src="'+item.data.image+'" style="width: 40px; height: 40px;">';
							},

							value: function(item, value)
							{
								var value = '',
									isColor = Number(item.data.is_color_group);

								if (isColor)
								{
									value = '<div style="width: 15px; height: 15px; border: solid 1px #CCCCCC; background-color: '+item.data.attribute_color+';"></div>';
								}

								return value;
							},

							available_date: function(value)
							{
								return (value !== '0000-00-00' ? value : '');
							}
						},
						events:
						{
							select: function(item)
							{
								var data = item.data,
									product = box.components.Product.getInstanceById(data.id_product);

								product.viewInfo[data.id_lang].id_product_attribute = data.id_product_attribute;
								product.render(null, null, product.viewInfo[data.id_lang].id_product_attribute);

								win.hide();
							},
						},
						defineSelected: function(item) 
						{
							return item.data.selected;
						},
					});

					return win.dataGrid;
				}
			});
		
			// console.log('add products');

			// self.add(1);
			// self.add(2);
			// self.add(3);
			// self.add(4);
			// self.add(5);
			// self.add(6);
		});
	};

	NewsletterPro.components.ProductSelection.prototype.sort = function()
	{
		var box = NewsletterPro,
			dom = this.dom,
			val = dom.btnSort.val();

		if (val !== '0')
			box.components.Product.sortProducts(val, Number(dom.sortOrder.val()));
		else
			box.components.Product.sortProducts('position', Number(dom.sortOrder.val()));
	};

	NewsletterPro.components.ProductSelection.prototype.add = function(idProduct)
	{
		var self = this,
			box = NewsletterPro,
			dom = self.dom,
			template = box.dataStorage.get('product_template');

		this.publish('beforeAdd', {
			template: template,
			idProduct: idProduct,
			product: null,
			ids: box.components.Product.getProductsId(),
		});

		$.postAjax({'submit_product_selection': 'addProduct', id_product: idProduct}).done(function(response){
			if (!response.success)
				box.alertErrors(response.errors);
			else
			{
				var data = response.product;

				var product = new box.components.Product({
					data: data,
					template: template,
					view: dom.containerLang,
				});

				self.render();
				self.sort();

				self.publish('add', {
					template: template,
					idProduct: idProduct,
					product: product,
					ids: box.components.Product.getProductsId(),
				});
			}
		});
	};

	NewsletterPro.components.ProductSelection.prototype.remove = function(idProduct)
	{
		var box = NewsletterPro;

		box.components.Product.removeProduct(Number(idProduct));
	};

	NewsletterPro.components.ProductSelection.prototype.render = function(idLang, idCurrency, idProductAttribute)
	{
		var box = NewsletterPro;
		box.components.Product.render(idLang, idCurrency, idProductAttribute);
	};

	NewsletterPro.components.ProductSelection.prototype.ready = function(func)
	{
		var self = this,
			box = NewsletterPro;

		$(document).ready(function(){
			self.dom = {
				container: $('#selected-products'),
				containerLang: {},
				btnSort: $('#np-selected-products-sort'),
				sortOrder: $('#np-selected-products-sort-order'),
				languageSelect: $('#np-change-view-template-lang'),
			};

			var languages = box.dataStorage.get('all_languages'),
				idSelectedLang = box.dataStorage.getNumber('id_selected_lang');

			for (var i = 0; i < languages.length; i++)
			{
				var idLang = Number(languages[i].id_lang),
					template = $('<div id="selected-products-'+idLang+'" class="clearfix '+(idLang != idSelectedLang ? 'np-lang-visible-hide' : '')+'" data-lang="'+idLang+'" data-visible="1" style="padding 0; margin: 0 auto; display: block;"></div>');

				self.dom.containerLang[idLang] = template;
				self.dom.containerLang[idLang].sortable();
				self.dom.container.append(self.dom.containerLang[idLang]);
			}

			func(self.dom);
		});
	};
}(jQueryNewsletterProNew));