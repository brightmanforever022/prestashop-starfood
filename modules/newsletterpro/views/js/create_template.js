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

NewsletterPro.namespace('modules.createTemplate');
NewsletterPro.modules.createTemplate = ({
	tinyWasInit: false,
	dom: null,
	box: null,
	vars: {},
	tabName: 'tab_newsletter_4',
	newsletterTemplate: null,
	tinySetupArray: [],

	initTinyCallback: function(config, cfg)
	{
		this.tinySetupArray.push(config);
	},

	isTinyInit: function()
	{
		return this.tinyWasInit;
	},

	initTiny: function()
	{
		var self = this;
		this.tinyInitDfd = $.Deferred();
		this.tinyWasInit = true;

		// resolve the deffered after 10 seconds if is not resolved
		setTimeout(function(){
			var message = 'Deffered was never resolved.';
			if (typeof self.tinyInitDfd.state === 'function')
			{
				if (self.tinyInitDfd.state() !== 'resolved')
				{
					self.tinyInitDfd.resolve();
					console.warn(message);
				}
			}
			else if (typeof self.tinyInitDfd.isResolved === 'function')
			{
				if (!self.tinyInitDfd.isResolved())
				{
					self.tinyInitDfd.resolve();
					console.warn(message);
				}
			}
		}, 10000);

		if (this.tinySetupArray.length > 0)
		{
			$.each(this.tinySetupArray, function(key, config){
				tinySetup(config);
			});				
		}

		return this.tinyInitDfd.promise();
	},

	init: function(box) {
		var self = this,
			vars = self.vars,
			templateDataSource,
			hexDigits = new Array("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f"),
			templateContent;

		self.box = box;

		self.varExists = function(name)
		{
			return self.vars.hasOwnProperty(name);
		};

		self.changeTemplate = function(item)
		{
			return changeTemplate(item);
		};

		function setStyle(selection, name, value, idLang) 
		{
			self.newsletterTemplate.setStyle(selection, name, value, idLang);
		}

		function setStyleObject(obj, css)
		{
			self.newsletterTemplate.setStyleObject(obj, css);
		}

		function parseTinyProducts(func) 
		{
			var products = self.newsletterTemplate.template.products;

			for (idLang in products)
			{
				if (products[idLang].length > 0) 
				{
					for(var i = 0; i < products[idLang].length; i++) {
						func(idLang, products[idLang][i]);
					}
				}
			}
		}

		function parseViewProducts(func) {
			var products = self.newsletterTemplate.view.products;
			if (products.length > 0) {
				for(var i = 0; i < products.length; i++) {
					func(products[i]);
				}
			}
		}

		function parseLinks(func) 
		{
			var links = self.newsletterTemplate.template.links;
			for (idLang in links)
			{
				if (links[idLang].length > 0) 
				{
					for (var i = 0; i < links[idLang].length; i++) {
						var link = $(links[idLang][i]);
						func(idLang, link);
					}
				}
			}
		}

		function parseViewLinks(func) {
			var links = self.newsletterTemplate.view.links;
			if ( links.length > 0) {
				for (var i = 0; i < links.length; i++) {
					var link = $(links[i]);
					func(link);
				}
			}
		}

		function changeTemplate(item) 
		{
			var name = item.data.filename;

			$.each(templateDataSource.items, function(i,item){
				item.data.selected = false;
			});

			item.data.selected = true;

			box.dataStorage.set('configuration.NEWSLETTER_TEMPLATE', name);

			$.postAjax({'submit_template_controller': 'changeTemplate', name: name}).done(function(response){
				if (!response.success)
					box.alertErrors(response.errors);
				else
				{
					var html = response.html;
					box.modules.createTemplate.newsletterTemplate.setTemplate(html);
				}
			});
		}

		self.ready(function(dom) {

			$(window).resize(function(){
				self.refreshSliders();
			});

			var l = NewsletterPro.translations.l(NewsletterPro.translations.modules.createTemplate),
				templateDataModel,
				templateGrid = dom.templateGrid,
				templateWidthSlider;

			templateDataModel = new gk.data.Model({
				id: 'id',
			});

			templateDataSource = new gk.data.DataSource({
				pageSize: 6,
				transport: {
					read: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit_template_controller=getNewsletterTemplates',
						dataType: 'json',
					},

					destroy: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit_template_controller=deleteTemplate&id',
						type: 'POST',
						dateType: 'json',
						success: function(response, itemData) {
							if(!response.status) {
								alert(response.errors.join("\n"));
							}
						},
						error: function(data, itemData) {
							alert(l('delete template error'));
						},
						complete: function(data, itemData) {},
					},					
				},
				schema: {
					model: templateDataModel
				},
				trySteps: 2,
				errors: 
				{
					read: function(xhr, ajaxOptions, thrownError) 
					{
						templateDataSource.syncStepAvailableAdd(3000, function(){
							templateDataSource.sync();
						});
					},
				},
			});

			templateGrid.gkGrid({
				dataSource: templateDataSource,
				selectable: true,
				currentPage: 1,
				pageable: true,
				template: 
				{
					attachment: function(item)
					{
						var len = item.data.attachments.length;
						if (len > 0)
							return '<span style="font-weight: bold;">'+len+'</span>';

						return '';
					},

					actions: function(item) 
					{
						var name = item.data.name.toLowerCase(),
							filename = item.data.filename,
							deleteTemplate = '';

						if (name !== 'default')
						{
							deleteTemplate = $('#delete-newsletter-template')
								.gkButton({
									title: l('delete template'),
									name: 'delete-newsletter-template',
									click: function(e) 
									{

										if (!confirm(l('confirm delete template')))
											return false;

										var selected = item.data.selected;

										item.destroy('status');
										if (selected) {
											var defaultTemplate = templateDataSource.getItemByValue('data.filename', 'default.html');
											templateDataSource.setSelected(defaultTemplate);
											changeTemplate(defaultTemplate);
										}
									},
									icon: '<i class="icon icon-trash-o"></i> ',
								});
						} 

						function appendButtons(arr) 
						{
							var div = $('<div></div>');
							$.each(arr, function(i,item){
								div.append(item);
							});
							return div;
						}

						return appendButtons([deleteTemplate]);
					},
				},
				events: 
				{
					select: function(item) 
					{
						changeTemplate(item);
					},
				},

				defineSelected: function(item) 
				{
					return item.data.selected;
				},
			});

			function isLoadTemplate() 
			{
				return self.newsletterTemplate.isLoadTemplate();
			}

			function isViewActive() 
			{
				return self.newsletterTemplate.isViewActive();
			}

			templateWidthSlider = gkSlider({
				target: dom.templateWidthSlider,
				snap : 6,
				min : 400,
				max : 1080,
				value : 400,
				values : [400,600,640,700,760,800,860,900,1080],
				corectPosition: -10,
				remplaceValues: {
					'400': 'auto',
					'1080': '100%',
				},
				move: function(obj) 
				{
					var value = parseInt(obj.getValue()),
						autoSize = 400,
						fullSize = 1080,
						content = self.newsletterTemplate.template.content;

					if (isLoadTemplate()) {
						if (value <= autoSize) {
							setStyle(content, 'width', 'auto');
						} else if (value >= fullSize) {
							setStyle(content, 'width', '100%');
						} else {
							setStyle(content, 'width', value);
						}
					}

					if (isViewActive()) 
					{
						var view = self.newsletterTemplate.view;

						if (value <= autoSize) {
							view.content.width('auto');
						} else if(value >= fullSize) {
							view.content.width('100%');
						} else {
							view.content.width(value);
						}
					}
				},
				done: function(obj) {},
			});

			dom.templateContainerColor.on('change', function(){
				var val = $(this).val();

				if (isLoadTemplate()) 
				{
					setStyle(self.newsletterTemplate.template.container, 'background-color', '#'+val);

					setStyleObject(self.newsletterTemplate.template.tinyBody, {
							'background-color': '#'+val
					});
				}

				if (isViewActive()) 
				{
					self.newsletterTemplate.view.container.css({'background-color': '#'+val, });
				}
			});

			dom.templateContentColor.on('change', function(){
				var val = $(this).val();

				if (isLoadTemplate()) {
					setStyle(self.newsletterTemplate.template.content, 'background-color', '#'+val);
				}

				if (isViewActive()) {
					self.newsletterTemplate.view.content.css({'background-color': '#'+val, });
				}
			});

			dom.productsBgColor.on('change', function(){
				var val = $(this).val();

				if (isLoadTemplate()) {
					parseTinyProducts(function(idLang, product){
						if (product.product.length > 0) {
							setStyle(product.product, 'background-color', '#'+val, idLang);
						}
					});
				}

				if (isViewActive()) {

					parseViewProducts(function(product){
						if (product.product.length > 0) {
							product.product.css({'background-color': '#'+val });
						}
					});

				}
			});

			dom.productsBorderColor.on('change', function(){
				var val = $(this).val();

				if (isLoadTemplate()) {
					parseTinyProducts(function(idLang, product){
						if (product.product.length > 0) {
							setStyle(product.product, 'border-color', '#'+val, idLang);
						}
					});
				}

				if (isViewActive()) {
					parseViewProducts(function(product){
						if (product.product.length > 0) {
							product.product.css({'border-color': '#'+val });
						}
					});
				}
			});

			dom.productsNameColor.on('change', function(){
				var val = $(this).val();

				if (isLoadTemplate()) {
					parseTinyProducts(function(idLang, product){
						if (product.name.length > 0) {
							setStyle(product.name, 'color', '#'+val, idLang);
						}
					});
				}

				if (isViewActive()) {
					parseViewProducts(function(product){
						if (product.name.length > 0) {
							product.name.css({'color': '#'+val });
						}
					});
				}

			});

			dom.productsSDescriptionColor.on('change', function(){
				var val = $(this).val();

				if (isLoadTemplate()) {
					parseTinyProducts(function(idLang, product){
						if (product.description_short.length > 0) {
							setStyle(product.description_short, 'color', '#'+val, idLang);
						}
					});
				}

				if (isViewActive()) {
					parseViewProducts(function(product){
						if (product.description_short.length > 0) {
							product.description_short.css({'color': '#'+val });
						}
					});
				}
			});

			dom.productsDescriptionColor.on('change', function(){
				var val = $(this).val();

				if (isLoadTemplate()) {
					parseTinyProducts(function(idLang, product){
						if (product.description.length > 0) {
							setStyle(product.description, 'color', '#'+val, idLang);
						}
					});
				}

				if (isViewActive()) {
					parseViewProducts(function(product){
						if (product.description.length > 0) {
						}
					});
				}
			});

			dom.productsPriceColor.on('change', function(){
				var val = $(this).val();

				if (isLoadTemplate()) {
					parseTinyProducts(function(idLang, product){
						if (product.price.length > 0) {
							setStyle(product.price, 'color', '#'+val, idLang);
						}
					});
				}

				if (isViewActive()) {
					parseViewProducts(function(product){
						if (product.price.length > 0) {
							product.price.css({'color': '#'+val });
						}
					});
				}
			});

			dom.linksColor.on('change', function(){
				var val = $(this).val();

				if (isLoadTemplate()) {
					parseLinks(function(idLang, link){
						setStyle(link, 'color', '#'+val);
					});
				}

				if (isViewActive()) {
					parseViewLinks(function(link){
						link.css({'color': '#'+val });
					});
				}
			});

			self.addVar('templateWidthSlider', templateWidthSlider);
			self.addVar('templateDataSource', templateDataSource);
		});	

		return this;
	},

	addVar: function (name, value) {
		this.vars[name] = value;
	},

	ready: function(func) {
		var self = this;

		$(document).ready(function(){
			self.dom = {
				templateGrid: $('#newsletter-template-list'),
				templateWidthSlider: $('#template-width-slider'),
				templateContainerColor: $('#template-container-color'),
				templateContentColor: $('#template-content-color'),
				sliderContainer: $('#slider-container'),
				tabGlobalCss: $('.tab-global-css'),

				productsBgColor: $('#products-bg-color'),
				productsNameColor: $('#products-name-color'),
				productsSDescriptionColor: $('#products-s-description-color'),
				productsDescriptionColor: $('#products-description-color'),
				productsPriceColor: $('#products-price-color'),

				linksColor: $('#links-color'),

				productsBorderColor: $('#products-border-color'),

				template: {
					container: null,
					content: null,
					products: [],
				},
			};

			self.newsletterTemplate = new NewsletterPro.components.NewsletterTemplate();

			func(self.dom);
		});

	},

	refreshSliders: function() 
	{
		var self = this;
		self.vars.templateWidthSlider.refresh();
	},

}.init(NewsletterPro));