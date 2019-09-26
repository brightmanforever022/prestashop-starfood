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

NewsletterPro.namespace('modules.selectProducts');
NewsletterPro.modules.selectProducts = ({
	dom: null,
	box: null,
	timer: null,
	productSelection: null,
	vars: {},
	init: function(box) {
		var self = this;

		self.box = box;
		self.vars.lastImageWidth = 0;

		self.updateProductTemplateView = function(content, matchContent) {

			if (content) {
				var productTemplateHeadler = box.parseProductHeader(content, matchContent);
				if (productTemplateHeadler.hasOwnProperty('content') && productTemplateHeadler.content == 'template') {
					box.dataStorage.set('is_product_template', true);
				} else {
					box.dataStorage.set('is_product_template', false);
				}
			}	

			var dom = self.dom;
			if (box.dataStorage.get('is_product_template')) {
				dom.productTemplateContentTextarea.show();
				dom.productTemplateContent.hide();
			} else {
				dom.productTemplateContentTextarea.hide();
				dom.productTemplateContent.show();
			}
		};

		self.trimString = function(str, value, end) {
			return trimString(str, value, end);
		};

		self.setImageSizeByWidth = function(product, image, width) {
			return setImageSizeByWidth(product, image, width);
		};

		self.setImagesOfProducts = function(width) {
			return setImagesOfProducts(width);
		};

		self.getImagesOfProducts = function(width) {
			return getImagesOfProducts(width);
		};

		self.setImageOfProduct = function(items, product) {
			return setImageOfProduct(items, product);
		};

		self.refreshProducts = function() {
			return refreshProducts();
		};

		self.getImageTypeByWidthValue = function(value) {
			return getImageTypeByWidthValue(value);
		};

		self.isDynamicVar = function(element) {
			return isDynamicVar(element);
		}

		self.updateProductsWidth = function()
		{
			updateProductsWidth();
		};

		self.displayProductsWidth = function()
		{
			return displayProductsWidth();
		};

		function displayProductsWidth()
		{
			var box = NewsletterPro,
				width = 0,
				idSelectedLang = box.dataStorage.get('id_selected_lang'),
				layout = box.components.Product.view[idSelectedLang],
				children;

			if (layout == null)
				return false;
			
			children = layout.find('[class="np-newsletter-layout-'+idSelectedLang+'"]');

			if (children.length > 0)
				width = children.width();
			else
				width = layout.width();

			self.dom.spWidth.html(width);
		}

		function delay(func, ms) 
		{
			if ( self.timer != null ) clearTimeout(self.timer);
			self.timer = setTimeout(function(){
				func();
			}, ms);
		}

		function isDynamicVar(element)
		{
			var value = $.trim(element.html());

			if (/\{\$\w+/.test(value))
				return true;
			return false;
		}

		function setImageSizeByWidth(product, image, width) {
			var oldHeight = product.data.image_height,
				oldWidth = product.data.image_width,
				ratio = oldHeight/oldWidth,
				height = width * ratio;

			image.width(width);
			image.height(height);
			image.attr({
				'width': width,
				'height': height,
			});
		}

		function sortImageSize(a,b) 
		{
			if (parseInt(a.width) < parseInt(b.width))
				return -1;
			if (parseInt(a.width) > parseInt(b.width))
				return 1;
			return 0;
		}

		function getImageTypeByWidthValue(value)
		{
			var imageSize = NewsletterPro.dataStorage.data.images_size,
				imageType = '';

			imageSize.sort(sortImageSize);

			for (var i = 0; i < imageSize.length; i++)
			{
				var image = imageSize[i],
					width = Number(image.width);

				if (value > width && typeof imageSize[i + 1] !== 'undefined')
				{
					imageType = imageSize[i + 1].name;
				}
			}

			if (imageSize.length && !imageType.length) {
				imageType = imageSize[0].name;
			}

			return imageType;
		}

		function getImagesOfProducts(value) {
			var selectedProducts = NewsletterProComponents.objs.selectedProducts,
				itemsIds = selectedProducts.getItemsIds(),
				imageType = getImageTypeByWidthValue(value),
				dfd = new $.Deferred();

			$.postAjax({'submit':'getImagesOfProducts', 'ids': itemsIds, 'image_type': imageType}).done(function(response){
				if (!response.status) {
					alert(response.errors.join('\n'));
				} else {
					dfd.resolve(response.products);
				}
			});
			return dfd.promise();
		}

		function setImagesOfProducts(width) {
			var selectedProducts = NewsletterProComponents.objs.selectedProducts;

			getImagesOfProducts(width).done(function(items){
				selectedProducts.parseProducts(function(product, instance, data){
					setImageOfProduct(items, product);
					setImageSizeByWidth(product, product.dom.image, width);
				});
			});
		}

		function setImageOfProduct(items, product) {
			var id = parseInt(product.id);
			if (items.hasOwnProperty(id)) {
				product.data.image_path = items[id].src;
				product.data.image_width = items[id].width;
				product.data.image_height = items[id].height;

				product.dom.image.attr({
					'src': items[id].src,
					'width': items[id].width,
					'height': items[id].height,
				});
			}
		}

		function triggerRenderImages() {
			NewsletterProComponents.objs.selectedProducts.renderImages = true;
		}

		function refreshProducts() 
		{
			var categoryList = NewsletterProComponents.objs.categoryList,
				selectedProducts = NewsletterProComponents.objs.selectedProducts;

			if (categoryList.currentItem !== null) 
			{
				categoryList.categoryListCallback(categoryList.currentItem.id);
				selectedProducts.resetItems();
			}
		}

		function fixProductsHeight() 
		{
			return NewsletterProComponents.objs.selectedProducts.fixHeightOnRemove();
		}

		function updateProductsWidth()
		{
			var dom = self.dom,
			width = dom.selectedProducts.width();

			dom.spWidth.html(width);
		}

		self.ready(function(dom) {

			try {
				dom.productTemplateContentTextarea.on('keydown', function(event){
					var key = event.keyCode,
						textarea = dom.productTemplateContentTextarea.get(0);

					if (key == 9) {
						try {
					        var newCaretPosition;
					        newCaretPosition = textarea.getCaretPosition() + "    ".length;
					        textarea.value = textarea.value.substring(0, textarea.getCaretPosition()) + "    " + textarea.value.substring(textarea.getCaretPosition(), textarea.value.length);
					        textarea.setCaretPosition(newCaretPosition);
					        return false;
						} catch (error) {
							console.warn(error);
						}
					}
					if (key == 8) {
						try {
						    if (textarea.value.substring(textarea.getCaretPosition() - 4, textarea.getCaretPosition()) == "    ") { 
					            var newCaretPosition;
					            newCaretPosition = textarea.getCaretPosition() - 3;
					            textarea.value = textarea.value.substring(0, textarea.getCaretPosition() - 3) + textarea.value.substring(textarea.getCaretPosition(), textarea.value.length);
					            textarea.setCaretPosition(newCaretPosition);
					        }
						} catch (error) {
							console.warn(error);	
						}
					}
				});
			} catch (error) {
				console.warn(error);
			}

			$(window).resize(function(){
				self.refreshSliders();
			});

			var l = NewsletterPro.translations.l(NewsletterPro.translations.modules.selectProducts),
				templateDataModel,
				templateDataSource,
				templateGrid = dom.templateGrid,
				dataStorage = box.dataStorage,
				sliderImageSize,
				sliderProductsPerRow,
				sliderProductsWidth,
				nbProducts,
				nbProductsText,					
				nbProductsResponse,
				imageSizeText,
				imageSize;
				// productTemplateHeader = box.parseProductHeader(box.dataStorage.get('product_tpl_header'), false);

			self.productSelection = new box.components.ProductSelection({});

			// if (productTemplateHeader.hasOwnProperty('content') && productTemplateHeader.content == 'template') {
			// 	box.dataStorage.set('is_product_template', true);
			// } else {
			// 	box.dataStorage.set('is_product_template', false);
			// }
	
			self.updateProductTemplateView(box.dataStorage.get('product_tpl_header'), false);

			var displaySliders = function()
			{
				if (box.components.Product.count())
				{
					var first = box.components.Product.first(),
						idSelectedLang = box.dataStorage.getNumber('id_selected_lang'),
						template = (
							first.viewInstance.hasOwnProperty(idSelectedLang) ? 
							first.viewInstance[idSelectedLang] :
							false
						),
						templateName = (template
								? template.find('.newsletter-pro-name')
								: []
							),
						templateDescriptionShort =(template
								? template.find('.newsletter-pro-description_short')
								: []
							),
						templateDescription = (template
								? template.find('.newsletter-pro-description')
								: []
							);

					sliderProductsPerRow.show();
					sliderProductsWidth.show();

					if (template && template.find('.newsletter-pro-image').length > 0)
						sliderImageSize.show();
					else
						sliderImageSize.hide();

					if (templateName.length > 0)
					{
						// check if the name is a dynamic variable
						if (templateName.html().match(/\{\$name/i))
							sliderName.hide();
						else
							sliderName.show();
					}
					else
						sliderName.hide();

					if (templateDescriptionShort.length > 0)
					{
						if (templateDescriptionShort.html().match(/\{\$description_short/i))
							sliderDescriptionShort.hide();
						else
							sliderDescriptionShort.show();
					}
					else
						sliderDescriptionShort.hide();

					if (templateDescription.length > 0)
					{
						if (templateDescriptionShort.html().match(/\{\$description/i))
							sliderDescription.hide();
						else
							sliderDescription.show();
					}
					else
						sliderDescription.hide();

					if (box.objSize(first.templateHeader))
					{
						if (first.templateHeader.hasOwnProperty('displayColumns'))
						{
							if (first.templateHeader.displayColumns)
								sliderProductsPerRow.show();
							else
								sliderProductsPerRow.hide();
						}

						if (first.templateHeader.hasOwnProperty('displayImageSize'))
						{
							if (first.templateHeader.displayImageSize)
								sliderImageSize.show();
							else
								sliderImageSize.hide();
						}

						if (first.templateHeader.hasOwnProperty('displayProductWidth'))
						{
							if (first.templateHeader.displayProductWidth)
								sliderProductsWidth.show();
							else
								sliderProductsWidth.hide();
						}
					}

				}
				else
				{
					sliderImageSize.hide();
					sliderProductsWidth.hide();
					sliderName.hide();
					sliderDescriptionShort.hide();
					sliderDescription.hide();
					sliderProductsPerRow.show();
				}
			};

			self.productSelection.subscribe('add', function(obj){
				displayProductsWidth();
				displaySliders();
			});

			self.productSelection.subscribe('remove', function(obj){

				var productList = NewsletterProComponents.objs.productList;
				displayProductsWidth();
				displaySliders();

				// update the products list buttons
				productList.parseItems(function(id, item){
					if (Number(id) == Number(obj.idProduct))
					{
						item.setToAdd();
						return true;
					}
				});
			});

			templateDataModel = new gk.data.Model({
				id: 'id',
			});

			templateDataSource = new gk.data.DataSource({
				pageSize: 6,
				transport: {
					read: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=getProductTemplates',
						dataType: 'json',
					},

					destroy: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=deleteProductTemplate&id',
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
				template: {
					info: function(item) {
						var info = item.data.info;

						if (info.hasOwnProperty('color'))
						{
							return '\
								<div style="background-color: ' + info.color + '; padding: 5px 10px; color: ' + info.display_text_color + '">' + info.for_template_name + '</div>\
							';
						}

						return '';
					},
					actions: function(item) {
						var name = item.data.name.toLowerCase(),
							filename = item.data.filename,
							deleteTemplate = '';
						if (name !== 'default')
						{
							deleteTemplate = $('#delete-product-template')
								.gkButton({
									title: l('delete template'),
									name: 'delete-product-template',
									click: function(e) 
									{							

										var selected = item.data.selected;

										if (!confirm(l('confirm delete template')))
											return false;

										item.destroy('status');
										if (selected) 
										{
											var defaultTemplate = templateDataSource.getItemByValue('data.filename', 'default.html');
											templateDataSource.setSelected(defaultTemplate);
											changeTemplate(defaultTemplate);
										}
									},
									icon: '<i class="icon icon-trash-o"></i> ',
								});
						} 

						function appendButtons(arr) {
							var div = $('<div></div>');
							$.each(arr, function(i,item){
								div.append(item);
							});
							return div;
						}

						return appendButtons([deleteTemplate]);
					},
				},
				events: {
					select: function(item) 
					{
						changeTemplate(item);
					},
				},

				defineSelected: function(item) 
				{
					return item.data.selected;
				},

				done: function()
				{
					displaySliders();
				},

			});

			function changeTemplate(item) 
			{
				// console.log('da');
				var tinyProduct = dom.tinyProduct,
					viewProductTemplate = dom.viewProductTemplate,
					selectedProducts = NewsletterProComponents.objs.selectedProducts;

				$.each(templateDataSource.items, function(i,item){
					item.data.selected = false;
				});

				item.data.selected = true;

				$.postAjax({'submit': 'getProductTemplateContent', 'readcontent': 1, data: item.data}).done(function(response){
					if (!response.status) 
					{
						alert(response.errors.join('\n'));
					}
					else 
					{
						if (typeof self.lastItemChangedName === 'undefined')
						{
							self.lastItemChangedName = item.data.filename;
							nbProducts.val(response.columns);
						}
						else 
						{
							if (self.lastItemChangedName !== item.data.filename)
							{
								nbProducts.val(response.columns);
							}
							else
							{
								nbProducts.val(dataStorage.data.product_tpl_nr);
							}

							self.lastItemChangedName = item.data.filename;
						}

						dom.productTemplateContentTextarea.val(response.render);
						tinyProduct.setContent(response.render);
						box.modules.selectProducts.updateProductTemplateView(response.render);

						try {
							viewProductTemplate.html(response.render);
						} catch (e) {
							// don't catch
						}

						box.dataStorage.set('product_template', response.content);
						box.components.Product.changeTemplate(response.content);
						displaySliders();

						sliderProductsPerRow.setValue(parseInt(nbProducts.val()));
						dataStorage.add('product_tpl_nr', parseInt(nbProducts.val()));
					}
				});
			}

			function getProductTemplateHeader(template, type)
			{
				type = type || 'content';
				var productHeader = {};

				if (type === 'header')
					productHeader = NewsletterProComponents.SelectedProducts.parseProductHeader(template);
				else
					productHeader = NewsletterProComponents.SelectedProducts.getHeader(template);
				return productHeader;
			}

			function setSettingsByTemplateHeader(template, type)
			{
				console.log('VERIFIC PORTIUNEA ASTA');

				type = type || 'content';
				var productHeader = getProductTemplateHeader(template, type);

				if (typeof imageSize !== 'undefined' && productHeader.hasOwnProperty('loadImageSize') && parseInt(productHeader.loadImageSize) > 0)
				{
					imageSize.hide();
					imageSizeText.hide();

					var imageType = getImageTypeByWidthValue(parseInt(productHeader.loadImageSize));
					var width = getImageWidthByType(imageType);
					
					imageSize.val(imageType);
					sliderImageSize.setValue(width);
					self.addVar('lastImageWidth', width);

					$.postAjax({'submit': 'changeProductImageSize', value: imageType}).done(function(errors) {
						if (errors.length)
							alert(errors.join('\n'));
					});
				}
				else
				{
					imageSize.show();
					imageSizeText.show();
				}

				if (productHeader.hasOwnProperty('displayColumns') && productHeader.displayColumns == false)
				{
					nbProductsText.hide();
					nbProducts.hide();
					var sliderPPRParent = sliderProductsPerRow.dom.target.parent();
					sliderPPRParent.hide();
				}
				else
				{
					nbProductsText.show();
					nbProducts.show();
					var sliderPPRParent = sliderProductsPerRow.dom.target.parent();
					sliderPPRParent.show();
					sliderProductsPerRow.refresh();
				}
			}

			function triggerChanged() 
			{
				var currentItem = templateDataSource.selected;
				if ( currentItem != null) {
					changeTemplate(currentItem);
				}
			}

			function getImageWidthByType(type) 
			{
				var images = $.grep(dataStorage.data.images_size, function(item){
					return item.name === type;
				});
				if (images.length) {
					return parseInt(images[0].width);
				}
				return false;
			}

			templateGrid.addFooter(function(columns){
				var tr, 
					td,
					languageText,
					language,
					currencyText,
					currency,
					newImageSize;

				function makeRow(arr) {
					tr = $('<tr></tr>');
					td = $('<td class="form-inline gk-footer" colspan="'+columns+'"></td>');

					$.each(arr, function(i, item){
						td.append(item);
					});

					tr.html(td);
					return tr;
				}

				function isSelected(option) {
					if (option.hasOwnProperty('selected')) {
						return (option.selected ? ' selected="selected" ' : '' );
					}
					return false;
				}

				function buildName(option, name) {
					var str = '';
					$.each(name, function(i, value){
						str += option[name];
					});
					return str;
				}

				function getSelect(data, value, funcName, funcData) {
					var select = $('<select class="gk-select"></select>');
					$.each(data, function(i, option){
						var opt = $('<option '+( typeof funcData === 'function' ? funcData(option) : '' )+' value="'+option[value]+'" '+(isSelected(option))+'>'+(funcName(option))+'</option>');
						select.append(opt);
					});

					select.css({
						'width': 'auto',
						'margin-left': '6px',
						'margin-right': '6px'
					});
					return select;
				}

				imageSizeText = $('<span>'+l('image size')+'</span>');

				imageSize = getSelect(dataStorage.data.images_size, 'name', function(option){
					return option['width'] + 'x' + option['height'] + ' ( ' + option['name'] + ' )';
				}, function(option){
					return ' data-width="'+option['width']+'" data-height="'+option['height']+'" ';
				});

				imageSize.on('change', function(event){
					var element = $(this)
						val = element.val(),
						width = getImageWidthByType(val);

					if (width)
					{
						sliderImageSize.setValue(width);
						self.addVar('lastImageWidth', width);
					}

					$.postAjax({'submit': 'changeProductImageSize', value: val}).done(function(errors) {
						if (errors.length) {
							alert(errors.join('\n'));
						} else {
							triggerChanged();
							refreshProducts();
						}
					});

				});

				languageText = $('<span>'+l('language')+'</span>');

				language = getSelect(dataStorage.data.languages, 'id_lang', function(option){
					return option['name'];
				});

				language.on('change', function(event){
					var element = $(this)
						val = element.val();

					triggerRenderImages();

					$.postAjax({'submit': 'changeProductLanguage', value: val}).done(function(errors) {
						if (errors.length) {
							alert(errors.join('\n'));
						} else {
							refreshProducts();
						}
					}).always(function(){

					});
				});

				currencyText = $('<span>'+l('currency')+'</span>');

				currency = getSelect(dataStorage.data.currencies, 'id_currency', function(option){
					return option['sign'] + ' ( ' + option['name'] + ' ) ';
				});

				currency.on('change', function(event){
					var element = $(this)
						val = element.val();

					triggerRenderImages();

					$.postAjax({'submit': 'changeProductCurrency', value: val}).done(function(errors) {
						if (errors.length) {
							alert(errors.join('\n'));
						} else {
							refreshProducts();
						}
					}).always(function(){

					});
				});

				nbProductsText = $('<span>'+l('products per row')+'</span>');
				nbProducts = $('<input class="form-control text-center" type="text" value="'+dataStorage.data.product_tpl_nr+'">');
				nbProducts.css({
					'width': '40px',
					'margin-right': '6px',
					'margin-left': '6px',
				});

				nbProductsResponse = $('<span></span>');

				nbProducts.on('change', function(event){
					var element = $(this),
			 			val = parseInt(element.val());

			 		triggerRenderImages();

			 		val = (/^\d{1}$/.test(val)) ? parseInt(val) : 3;
			 		element.val(val);

 					$.postAjax({submit: 'saveProductNumberPerRow', number: val}).done(function(response) {
 						if (!response.status) {
 							alert(response.errors.join("\n"));
 							val = 3;
 							element.val(val);
 						}
 						dataStorage.add('product_tpl_nr', parseInt(val));
 						sliderProductsPerRow.setValue(val);

 						refreshProducts();
 					});
				});

				newImageSize =  $('<a href="'+(dataStorage.data.new_image_size_link)+'" class="btn btn-default pull-right" target="_blank"><i class="icon icon-plus-square"> </i> '+l('add image size')+'</a>');

				return makeRow([imageSizeText, imageSize, languageText, language, currencyText, currency, nbProductsText, nbProducts, nbProductsResponse, newImageSize]);
			}, 'append');

			var sliderName = gkSlider({
				target: dom.sliderName,
				min : 0,
				max : 250,
				value : 250,
				values : [0,250],
				corectPosition: -5,
				move: function(obj) {
					delay(function(){
						box.components.Product.setNameLenght(obj.getValue());
					}, 100);
				},
				done: function(obj) {

				},
			});

			var sliderDescription = gkSlider({
				target: dom.sliderDescription,
				min : 0,
				max : 500,
				value : 500,
				values : [0,500],
				corectPosition: -5,
				move: function(obj) {

					delay(function(){
						box.components.Product.setDescriptionLength(obj.getValue());
					}, 100);
				},
				done: function(obj) {

				},
			});

			var sliderDescriptionShort = gkSlider({
				target: dom.sliderDescriptionShort,
				min : 0,
				max : 250,
				value : 250,
				values : [0,250],
				corectPosition: -5,
				move: function(obj) {

					delay(function(){
						box.components.Product.setShortDescriptionLenght(obj.getValue());
					}, 100);
				},
				done: function(obj) {

				},
			});

			box.components.Product.setLayout(parseInt(dataStorage.data.product_tpl_nr));

			sliderProductsPerRow = gkSlider({
				target: dom.sliderProductsPerRow,
				min : 0,
				max : 9,
				value : parseInt(dataStorage.data.product_tpl_nr),
				values : [0, 1, 9],
				corectPosition: -2,
				remplaceValues: {
					'0': l('auto'),
				},
				move: function(obj) {},
				start: function(obj) 
				{

				},
				done: function(obj) 
				{
					box.components.Product.setLayout(obj.getValue());
					displayProductsWidth();
				},
				onSetValue: function(value, obj)
				{
					box.components.Product.setLayout(obj.getValue());
					displayProductsWidth();
				},
			});

			var selectedImage = 0;
			var imagesWidth = $.map(dataStorage.data.images_size, function(item){
				if (item.selected) {
					selectedImage = parseInt(item.width);
				}
				return parseInt(item.width);
			});

			sliderImageSize = gkSlider({
				target: dom.sliderImageSize,
				snap : 6,
				min : imagesWidth.length ? imagesWidth[0] : 0,
				max : imagesWidth.length ? imagesWidth[imagesWidth.length - 1] : 0,
				value : selectedImage,
				values : imagesWidth,
				prefix: 'px',
				corectPosition: -10,
				move: function(obj) {

					delay(function()
					{
						var width = obj.getValue();
						box.components.Product.setImageSize(width);
					}, 100);
				},
				done: function(obj) {
					setImagesOfProducts(obj.getValue());
					self.addVar('lastImageWidth', obj.getValue());
				}
			});

			var minSize = imagesWidth.length ? imagesWidth[0] : 0;
			var maxSize = imagesWidth.length ? imagesWidth[imagesWidth.length - 1] : 0;
			var endSize = 860;
			endSize = maxSize >= endSize - 100 ? maxSize + 200 : endSize;
			var remplaceValues = {
				'0': 'auto',
			};

			sliderProductsWidth = gkSlider({
				target: dom.sliderProductsWidth,
				snap : 6,
				min : 0,
				max : endSize,
				value : 0,
				values : [0, minSize, maxSize, endSize],
				remplaceValues: remplaceValues,
				prefix: 'px',
				corectPosition: -10,
				move: function(obj) {

					delay(function(){
						box.components.Product.setWidth(obj.getValue());
					}, 100);
				},
				done: function(obj) {}
			});



			self.addVar('sliderName', sliderName);
			self.addVar('sliderDescription', sliderDescription);
			self.addVar('sliderDescriptionShort', sliderDescriptionShort);
			self.addVar('sliderProductsPerRow', sliderProductsPerRow);
			self.addVar('sliderImageSize', sliderImageSize);
			self.addVar('sliderProductsWidth', sliderProductsWidth);

			self.addVar('nbProducts', nbProducts);

			self.addVar('templateDataSource', templateDataSource);
		});

		return this;
	},

	refreshSliders: function() 
	{
		var self = this;

		self.vars.sliderName.refresh();
		self.vars.sliderDescription.refresh();
		self.vars.sliderDescriptionShort.refresh();

		self.vars.sliderProductsPerRow.refresh(); 

		self.vars.sliderImageSize.refresh();
		self.vars.sliderProductsWidth.refresh();
	},

	ready: function(func) {
		var self = this;
		$(document).ready(function(){
			self.dom = {
				templateGrid: $('#product-template-list'),

				viewProductTemplate: $('#view-product-template-content'),
				productsAdjustments: $('#products-adjustments'),
				sliderName: $('#slider-name'),
				sliderDescription: $('#slider-description'),
				sliderDescriptionShort: $('#slider-description-short'),
				sliderProductsPerRow: $('#slider-products-per-row'),
				sliderImageSize: $('#slider-image-size'),
				sliderProductsWidth: $('#slider-product-width'),

				sliderPprLoading: $('#slider-ppr-loading'),

				selectedProducts: $('#selected-products'),
				spWidth: $('#sp-width'),
				viewProductsBox: $('#np-view-products-box'),
				productTemplateContentTextarea: $('#product-template-content-textarea'),
				productTemplateContent: $('#product-content-box'),
			};

			NewsletterPro.onObject.setCallback('tinyProduct', function(ed){
				self.dom['tinyProduct'] = ed;
			});

			func(self.dom);
		});
	},

	addVar: function(name, value) {
		this.vars = this.vars || {};
		this.vars[name] = value;
	},

}.init(NewsletterPro));