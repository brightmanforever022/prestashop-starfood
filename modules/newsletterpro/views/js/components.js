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

var NewsletterProComponents = {

	init : function() {
		var self = this;
		var openTabs = [];

		$(window).resize(function(){
			openTabs = [];
		});

		jQuery(document).ready(function($) {

			self.addElement( 'productSearch', $('#poduct-search') )
				.addElement( 'productSort', $('#product-sort') )
				.addElement( 'productSearchAjax', $('.product-search-span') )
				.addElement( 'productList', $('#product-list .userlist table tbody') )

				.addElement( 'selectedProducts', $('#selected-products') )
				.addElement( 'productTemplateNumberPerRow', $('#save-product-nr-per-row') )
				.addElement( 'categoryList', $('#categories-list ul') )

				.addElement( 'tabNewsletter', $('#tab.newsletter') )
				.addElement( 'tabNewsletterContent', $('#tab_content.newsletter') )

				.addElement( 'tabNewsletterTemplate', $('#tab_template.newsletter-template') )
				.addElement( 'tabNewsletterTemplateContent',  $('#tab_template_content.newsletter-template') )

				.addElement( 'emailsToSend', $('#emails-to-send .userlist') )
				.addElement( 'emailsSent', $('#emails-sent .userlist') )

				.addElement( 'uploadCSVForm', $('#upload-csv-form') )
				.addElement( 'uploadCSV', $('#upload-csv') )
				.addElement( 'uploadCSVMessage', $('#upload-csv-message') )
				.addElement( 'uploadCSVFiles', $('#upload-csv-files') )
				.addElement( 'importExportContainer', $('#import-export-container') )
				.addElement( 'importDetails', $('#import-details') )
				.addElement( 'importSeparator', $('#import-separator') )

				.addElement( 'productsAdjustmentsDiv', $('#products-adjustments-div') )
				.addElement( 'productsAdjustments', $('#products-adjustments') );

			self.addObject( 'productList', self.ProductList.clone().init( self.elem.productList ) )
			self.addObject( 'productSearch', self.Search.clone().init( self.elem.productSearch, self.objs.productList ) )
			self.addObject( 'selectedProducts', self.SelectedProducts.clone().init( self.elem.selectedProducts ) )

			self.addObject( 'categoryList', self.CategoryList.clone().init( self.elem.categoryList, self.objs.productList ) );
			
			//self.addObject( 'sortControl', self.Sort.clone().init( self.elem.productSort, self.objs.productList ) );
			self.Sort.init( self.elem.productSort, self.objs.productList );

			self.addObject( 'tabItems', self.TabItems.clone().init( self.elem.tabNewsletter, self.elem.tabNewsletterContent, function(item){
				var id = item.attr('id');

				if (openTabs.indexOf(id) == -1)
				{
					openTabs.push(id);

					if (id === 'tab_newsletter_5') 
					{

					} 
					else if (id === 'tab_newsletter_3') 
					{
						NewsletterPro.modules.selectProducts.refreshSliders();
					} 
					else if (id === NewsletterPro.modules.createTemplate.tabName) 
					{
						if (typeof NewsletterPro.modules.createTemplate.dom.tinyNewsletter !== 'undefined')
							NewsletterPro.modules.createTemplate.updateBoth();

						NewsletterPro.modules.createTemplate.refreshSliders();

						if (!NewsletterPro.modules.createTemplate.isTinyInit())
						{
							// the tinyInitDfd must be resolved
							$('.tab-content-loading').show();
							setTimeout(function(){
								NewsletterPro.modules.createTemplate.initTiny().done(function(){
									$('.tab-content-loading').hide();
								});
							}, 50);
						}
					}
					else if (id === NewsletterPro.modules.frontSubscription.tabName)
					{
						NewsletterPro.modules.frontSubscription.onTabShow();

						if (!NewsletterPro.modules.frontSubscription.isTinyInit())
						{
							// the tinyInitDfd must be resolved
							$('.tab-content-loading').show();
							setTimeout(function(){
								NewsletterPro.modules.frontSubscription.initTiny().done(function(){
									$('.tab-content-loading').hide();
								});
							}, 50);
						}
					}
				}

			} ) );

			self.addObject( 'tabNewsletterTemplate', self.TabItems.clone().init( self.elem.tabNewsletterTemplate, self.elem.tabNewsletterTemplateContent, function(item){

			}));

			self.addObject( 'emailsSent', self.EmailSendList.clone().init( self.elem.emailsSent ) )
			self.addObject( 'emailsToSend', self.EmailSendList.clone().init( self.elem.emailsToSend, self.objs.emailsSent ) )

			self.addObject( 'uploadCSV', self.UploadCSV.init({ 
				form : self.elem.uploadCSVForm, 
				file : self.elem.uploadCSV,
				msg : self.elem.uploadCSVMessage,
				files : self.elem.uploadCSVFiles,
				container : self.elem.importExportContainer,
				details : self.elem.importDetails,
				separator : self.elem.importSeparator,
			}) )
		});
	},

	elem : {},
	objs : {},
	addElement : function( name, elem ) {
		this.elem[name] = elem;
		return this;
	},
	addObject : function( name, obj ) {
		this.objs[name] = obj;
		return this;
	},

	sortQntAsc : function(a, b){
		if(a.quantity_all_versions < b.quantity_all_versions){
			return -1;
		}
		else if(a.quantity_all_versions === b.quantity_all_versions){
			return 0;
		}
		else if(a.quantity_all_versions > b.quantity_all_versions){
			return 1;
		}
	},
	sortQntDesc : function(a, b){
		if(a.quantity_all_versions > b.quantity_all_versions){
			return -1;
		}
		else if(a.quantity_all_versions === b.quantity_all_versions){
			return 0;
		}
		else if(a.quantity_all_versions < b.quantity_all_versions){
			return 1;
		}
	},
	sortReferenceAsc : function(a, b){
		if(a.supplier_reference < b.supplier_reference){
			return -1;
		}
		else if(a.supplier_reference === b.supplier_reference){
			return 0;
		}
		else if(a.supplier_reference > b.supplier_reference){
			return 1;
		}
	},
	
	Sort : {
		dataItems : [],
		clone:  function() {
			return $.extend(true, {}, this)
		},

		init: function( element, objTarget ){
			var self = this;
			this.element = element;
			this.objTarget = objTarget;
			
			this.element.on('change', function(event){
				var parent = NewsletterProComponents;
				
				if( parent.Sort.dataItems.length ){
					if( parent.elem.productSort.val() == 'quantity' ){
						parent.Sort.dataItems.sort(parent.sortQntDesc);
					}
					else if( parent.elem.productSort.val() == 'reference' ){
						parent.Sort.dataItems.sort(parent.sortReferenceAsc);
					}
					self.objTarget.createItems(parent.Sort.dataItems);
				}
			});
		}
	},

	MenuCheckbox :
	{
		items : [],
		currentItem : null,
		hitTest : false,
		clone:  function() {
			return $.extend(true, {}, this)
		},
		init : function( element, type ) {
			if ( typeof(type) === 'undefined' )
				type = 'click';

			var self = this;
			this.element = element;
			this.element.attr('tabindex', '-1');
			this.element.css({
				'outline' : 'none'
			});
			this.eHeader = this.element.children('a').first();
			this.eHeader.addClass('header');
			this.eHeader.append('<span>&nbsp</span>');
			this.eMenu = this.element.children('ul').first();
			if ( this.eMenu.length == 0)
			{
				this.eMenu = this.element.children('table').first();
				this.eItems = this.eMenu.find('tr');
			}
			else
				this.eItems = this.eMenu.children('li');

			this.createItems( this.eItems );

			if ( type == 'click' ) {

				this.eHeader.on('click', function(event) {
					self.element.focus();
					self.toggleMenu();
				});

				this.element.on('focusout', function() {
					if (self.hitTest == false )
						self.hideMenu();
				});

				this.element.on('mouseleave', function() {
					self.hitTest = false;
				});

				this.element.on('mouseenter', function() {
					self.hitTest = true;
				});

			} else if (type == 'over') {
				this.element.hover(function(event) {
					self.showMenu();
				}, function(event) {
					self.hideMenu();
				});
			}
			return this;
		},

		itemTemplate : function( instance ) {
			var self = this;
			var input = $(instance).find('input');
			var checked = input.is(':checked') ? true  : false;

			var item = {
				instance : instance,
				input : input,
				click : function()	{
					this.instance.trigger('click');
					if ( this.input.is(':checked') )
						return false;
					else
						return true;
				},
				check : function() {
					if ( this.checked == false )
						this.click();
					return true;
				},
				uncheck : function() {
					if ( this.checked == true )
						this.click();
					return false;
				},
				checked : checked,
				val : function() {
					return this.input.val();
				},
				updateSelection : function() {
					if ( this.input.is(':checked') ) {
						this.input.attr('checked', false);
						this.checked = false;
						return true;
					} else {
						this.input.attr('checked', true);
						this.checked = true;
						return false;
					}
				}
			}

			item.instance.on('click', function(event) {
				self.currentItem = item;
				self.currentItem.updateSelection();

				event.stopPropagation();
			});

			item.input.on('click', function(event) {
				self.currentItem = item;
				self.currentItem.checked = self.currentItem.input.is(':checked') ? true : false;

				event.stopPropagation();
			});
			return item;
		},

		createItems : function( eItems ) {
			var self = this;

			$.each( eItems, function(index, item ) {
				self.items.push( self.itemTemplate( $(item) ) );
			});
		},

		showMenu : function() {
			var self = this;
			self.eMenu.show();
			return true;
		},

		hideMenu : function() {
			var self = this;
			self.eMenu.hide();
			return false;
		},

		toggleMenu : function() {
			var self = this;
			if ( self.eMenu.is(':visible') ) {
				self.eMenu.hide();
				return false;
			} else {
				self.eMenu.show();
				return true;
			}
		},

		getItems : function( bSelected ) {
			var self = this;

			var items = $.grep( self.items, function( item, index ) {
				if ( bSelected == true )
					return self.items[index].checked == true;
				else if ( bSelected == false )
					return self.items[index].checked == false;
				else
					return true;
			});

			return items;
		},

		isTriggerAll : false,

		checkAll : function() {
			var self = this;
			self.isTriggerAll = true;
			$.each( self.items, function( index, item ) {
				if ( self.items[index].checked == false )
					self.items[index].check();

				if ( self.items.length == index + 1 )
					self.isTriggerAll = false;
			});
		},

		uncheckAll : function() {
			var self = this;
			self.isTriggerAll = true;
			$.each( self.items, function( index, item ) {
				if ( self.items[index].checked == true )
					self.items[index].uncheck();

				if ( self.items.length == index + 1 )
					self.isTriggerAll = false;

			});
		},

		elementCheckToggle : function( element ) {
			var self = this;
			if ( element .data('value') == 1 ) {
				element.data('value', 0 );
				element.html(element.data('name').check);
				self.uncheckAll();
			} else {
				element.data('value', 1 );
				element.html(element.data('name').uncheck);
				self.checkAll();
			}
		}
	}, // end of MenuCheckbox

	Search : 
	{
		timer : null,
		minLength : 4,
		title : '',
		clone:  function() {
			return $.extend(true, {}, this)
		},

		init: function( element, objTarget ) {
			var self = this;
			this.element = element;
			this.title = element.val();
			this.objTarget = objTarget;

			this.element.on('keyup', function( event ) {
				var val = self.getVal();
				if ( val.length >= self.minLength )
					self.search(self.getVal());
				else
					self.resetSearch();
			});

			this.element.on('focus', function( event ) {
				if ( self.isEmpty() == true || self.getVal() == self.title )
					self.setVal('');
			});

			this.element.on('focusout', function( event ) {
				if ( self.isEmpty() == true || self.getVal() == self.title )
					self.setVal(self.title);
			});

			return this;
		},

		setVal : function( val ) {
			var self = this;
			self.element.val(val);
		},
		getVal : function( val ) {
			var self = this;
			return self.element.val();
		},
		resetSearch : function() {
			var parent = NewsletterProComponents;
			var self = this;

			if( self.isEmpty() ) {
				parent.objs.productList.element.empty();
			}
		},

		search : function( query ) {
			var parent = NewsletterProComponents;
			var self = this;	
			if ( self.timer != null ) clearTimeout(self.timer);

			parent.elem.productSearchAjax.show();
			self.timer = setTimeout(function(){

				$.postAjax({'submit': 'searchProducts', searchProducts:query}).done(function(data){ 
					parent.elem.productSearchAjax.hide();
					if( parent.elem.productSort.val() == 'quantity' ){
						data.products.sort(parent.sortQntDesc);
					}
					parent.Sort.dataItems = data.products;
					self.objTarget.createItems(data.products);
				});

			}, 200);

		},

		isEmpty : function() {
			var self = this;
			if ( self.getVal() == '' ) {
				if ( !self.element.hasClass('empty') )
					self.element.addClass('empty');
				return true;
			} else {
				if ( self.element.hasClass('empty') )
					self.element.removeClass('empty');
				return false;
			}
		}
	}, // end of Search

	ProductList : 
	{
		dataItems : [],
		items : [],
		eItems : [],
		element : null,
		currentItem : null,
		lastItemClass : null,
		clone:  function() {
			return $.extend(true, {}, this)
		},

		init: function( element ) {
			this.element = element;
			return this;
		},

		createItems : function( dataItems ) 
		{
			var self = this;

			self.dataItems = dataItems;
			self.items = [];
			self.eItems = [];
			self.currentItem = null;
			self.lastItemClass = null;
			self.element.empty();

			$.each(dataItems, function(index, item) {
				item.thumb = { path : item.thumb_path, width : item.thumb_width, height : item.thumb_height };
				var it = self.eItemTemplate( item  );
				self.items.push( self.itemTemplate(it, item) );
			});

			return this;
		},

		parseItems: function(func)
		{
			for (var i = 0; i < this.items.length; i++) 
			{
				var result = func(Number(this.items[i].id), this.items[i]);
				if (result != null)
					return result;
			}
		},

		eItemTemplate : function(item) 
		{
			var box = NewsletterPro,
				parent = NewsletterProComponents,
				self = this,
				cls,
				addButton,
				removeButton,
				productsIds,
				data,
				l = box.translations.l(box.translations.global);

			cls = (self.lastItemClass == null) ? 'even' : ((self.lastItemClass == 'even') ? 'odd' : 'even');
			self.lastItemClass = cls;

			addButton = '<td class="options">\
								<a class="btn btn-default add-product" href="javascript:{}" data-info=\'{"id":"'+item.id_product+'","value":"1"}\'>\
									<i class="icon icon icon-plus-circle"></i> <span>'+l('add')+'</span>\
								</a>\
							</td>';

			removeButton = '<td class="options">\
									<a class="btn btn-default add-product" href="javascript:{}" data-info=\'{"id":"'+item.id_product+'"}\'>\
										<i class="icon icon icon-plus-circle"></i> <span>'+l('remove')+'</span>\
									</a>\
								</td>';

			productsIds = box.components.Product.getProductsId();
			data = (productsIds.indexOf(Number(item.id_product)) == -1) ? addButton : removeButton;

			function isActive() {
				return ( parseInt(item.active) ? true : false );
			}

			function inStock() {
				return ( parseInt(item.quantity) ? true : false );
			}

			var eTemplate = '<tr class="'+cls+'">';
				eTemplate += (NewsletterPro.dataStorage.getNumber('configuration.DISPLAY_PRODUCT_IMAGE') == true ) ? '<td class="image"><img src="'+item.thumb.path+'" alt="" height="'+item.thumb.height+'" width="'+item.thumb.width+'"></td>' : '';
				eTemplate +='<td class="name '+(isActive()?'':'inactive')+'">'+item.supplier_reference+'</td>';
				eTemplate +='<td class="reference '+(isActive()?'':'inactive')+'">'+(item.reference != null ? item.reference : '')+'</td>';
				eTemplate +='<td class="search-price '+(isActive()?'':'inactive')+'">'+(item.price_tax_exc_display != null ? item.price_tax_exc_display : '')+'</td>';
				eTemplate +='<td class="search-active">'+(isActive() ? 
											'<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>' : 
											'<span class="list-action-enable action-disabled"><i class="icon icon-remove"></i></span>'
										)+'</td>';
				eTemplate += '<td class="search-clearance">'
					+ (parseInt(item.stock_clearance) ? '<span class="list-action-enable action-disabled" title="Stock clearance"><i class="icon icon-arrow-down"></i></span>' : '') 
					+'</td>';
				eTemplate +='<td class="search-stock '+(inStock()?'':'inactive')+'">'+(item.quantity_all_versions != null ? item.quantity_all_versions : '<span style="color: red;">0</span>')+'</td>';
				eTemplate += data;
				eTemplate +='</tr>';

			eTemplate = $(eTemplate);
			self.element.append(eTemplate);
			return eTemplate;
		},

		itemTemplate : function(instance, data) 
		{
			var parent = NewsletterProComponents;
			var self = this;

			var button = instance.find('a.add-product');
			var text = button.find('span');
			var img = button.find('img');
			var dataInfo = button.data('info');
			var id = dataInfo.id;
			var pImg = instance.find('td.image img');
			
			var box = NewsletterPro,
				l = box.translations.l(box.translations.global);

			var item = {
				instance : instance,
				button : button,
				click : function() {
					this.button.trigger('click');
					return this.val();
				},
				val : function() 
				{
					return this.dataInfo.value;
				},
				id : id,
				dataInfo : dataInfo,
				data : data,
				text : text,
				img : img,
				pImg : pImg,
				toggleValue : function() 
				{
					if( this.val() == '1')
						this.remove();
					else
						this.add();
				},
				setToAdd: function()
				{
					this.dataInfo.value = '1';
					this.text.html(l('add'));
				},
				setAsRemove: function()
				{
					this.dataInfo.value = '-1';
					this.text.html(l('remove'));
				},
				add : function() 
				{
					var box = NewsletterPro;

					this.dataInfo.value = '1';
					this.text.html(l('add'));
					
					box.modules.selectProducts.productSelection.remove(this.id);
				},
				remove : function() 
				{
					var box = NewsletterPro;
						that = this;

					this.dataInfo.value = '-1';
					this.text.html(l('remove'));

					box.modules.selectProducts.productSelection.add(this.id);
				}
			}

			item.button.on('click', function(event) {
				self.currentItem = item;
				self.currentItem.toggleValue();
				event.stopPropagation();
			});
			item.pImg.on('mouseover', function(event){
				var imgView = $('<img id="productBigImage"/>');
				imgView.attr('src', item.data.image_path);
				imgView.css({position:'fixed',top:0,left:0,zIndex:1100});
				$('body').append(imgView);
			});
			item.pImg.on('mouseout', function(event){
				$('#productBigImage').remove();
			});

			return item;
		},

		getItems : function( bool ) {

			if( bool == true ) {
				var items = $.grep(this.items, function(item) {
					return item.val() == '-1';
				});
				return items;
			} else if ( bool == false ) {
				var items = $.grep(this.items, function(item) {
					return item.val() == '1';
				});
				return items;
			} else {
				return this.items;
			}
		}

	}, // end of ProductList

	SelectedProducts : 
	{
		template : '',
		itemsId : [],
		items : [],
		element : null,
		productHeader: {},

		clone:  function() {
			return $.extend(true, {}, this)
		},

		init: function( element ) {
			var parent = NewsletterProComponents;
			var self = this;
			this.element = element;

			$.postAjax({'submit': 'getProductContent', getProductContent:true}, 'html').done(function(data) {
				self.template = data;
			});

			return this;
		},

		toggleSlider : function (name, slider, title) 
		{
			var self = this,
				targetParent,
				title = title || '';

			targetParent = slider.dom.target.parent();

			function refreshSliders() 
			{
				NewsletterPro.modules.selectProducts.refreshSliders();
			}

			function isDynamicVar(element) 
			{
				return NewsletterPro.modules.selectProducts.isDynamicVar(element);
			}

			if (self.items.length) 
			{
				var firstItem = self.items[0];
				if ((firstItem.dom[name].length > 0 && !isDynamicVar(firstItem.dom[name])) ||
					(firstItem.dom[name].length > 0 && name === 'product')) 
				{
					targetParent.show();
					refreshSliders();
				} 
				else 
				{
					targetParent.hide();
				}
			} 
			else 
			{
				targetParent.hide();
			}
		},

		toggleSliders: function() 
		{
			var sliderName = NewsletterPro.modules.selectProducts.vars.sliderName,
				sliderDescription = NewsletterPro.modules.selectProducts.vars.sliderDescription,
				sliderDescriptionShort = NewsletterPro.modules.selectProducts.vars.sliderDescriptionShort,
				sliderImageSize = NewsletterPro.modules.selectProducts.vars.sliderImageSize,
				sliderProductsWidth = NewsletterPro.modules.selectProducts.vars.sliderProductsWidth,
				sliderProductsPerRow = NewsletterPro.modules.selectProducts.vars.sliderProductsPerRow;

			var productHeader = this.productHeader;

			if (productHeader.hasOwnProperty('displayColumns') && productHeader.displayColumns == false)
				this.hideSlider('', sliderProductsPerRow);
			else
				this.showSlider('', sliderProductsPerRow);

			this.toggleSlider('name', sliderName);
			this.toggleSlider('description', sliderDescription);
			this.toggleSlider('description_short', sliderDescriptionShort);

			if (productHeader.hasOwnProperty('displayImageSize'))
			{
				if (productHeader.displayImageSize == true)
					this.toggleSlider('image', sliderImageSize);
			}
			else
				this.toggleSlider('image', sliderImageSize);

			if (productHeader.hasOwnProperty('displayProductWidth'))
			{
				if (productHeader.displayProductWidth == true)
					this.toggleSlider('product', sliderProductsWidth);
			}
			else
				this.toggleSlider('product', sliderProductsWidth);
		},

		hideSlider: function(name, slider) 
		{
			var self = this,
				targetParent;
			targetParent = slider.dom.target.parent();

			targetParent.hide();
		},

		showSlider: function(name, slider)
		{
			var self = this,
				targetParent;
			targetParent = slider.dom.target.parent();

			targetParent.show();
		},

		hideSliders: function() 
		{
			var sliderName = NewsletterPro.modules.selectProducts.vars.sliderName,
				sliderDescription = NewsletterPro.modules.selectProducts.vars.sliderDescription,
				sliderDescriptionShort = NewsletterPro.modules.selectProducts.vars.sliderDescriptionShort,
				sliderImageSize = NewsletterPro.modules.selectProducts.vars.sliderImageSize,
				sliderProductsWidth = NewsletterPro.modules.selectProducts.vars.sliderProductsWidth;

			this.hideSlider('name', sliderName);
			this.hideSlider('description', sliderDescription);
			this.hideSlider('description_short', sliderDescriptionShort);
			this.hideSlider('image', sliderImageSize);
			this.hideSlider('product', sliderProductsWidth);
		},

		parseProducts: function(func) {
			var self = this,
				products = self.items;

			$.each(products, function(i, product){
				func(product, product.value, product.data);
			});

		},
		renderImages: false,
		addItem : function(data)
		{
			var self = this,
				response = self.formatItemData(data),
				item = $(response.product),
				formatedData = response.formatedData;

			self.element.find('tr.last-row').last().append(item);
			self.itemsId.push( data.id_product );

			var product = {
				id: data.id_product,
				value: item,
				data: formatedData,
				dom: {
					name: item.find('.newsletter-pro-name'),
					description: item.find('.newsletter-pro-description'),
					description_short: item.find('.newsletter-pro-description_short'),
					image: item.find('.newsletter-pro-image'),
					product: item.find('.newsletter-pro-product'),
				},
			};

			self.items.push(product);

			function trimString(str, value, end) 
			{
				return NewsletterPro.modules.selectProducts.trimString(str, value, end);
			}

			function getLastImageWidth() 
			{
				if ( typeof NewsletterPro.modules.selectProducts.vars.lastImageWidth !== 'undefined')
					return parseInt(NewsletterPro.modules.selectProducts.vars.lastImageWidth);
				return false;
			}

			function setImagesOfProducts(width) 
			{
				return NewsletterPro.modules.selectProducts.setImagesOfProducts(width);
			}

			function getProductWidth() 
			{
				var width = 0;
				if (NewsletterPro.dataStorage.data.hasOwnProperty('product_width'))
					width = parseInt(NewsletterPro.dataStorage.data.product_width);
				return width;
			}

			function isDynamicVar(element)
			{
				return NewsletterPro.modules.selectProducts.isDynamicVar(element);
			}

			// this part of code need improvements 
			function applySlidersValues() 
			{
				var sliderName = NewsletterPro.modules.selectProducts.vars.sliderName,
					sliderDescription = NewsletterPro.modules.selectProducts.vars.sliderDescription,
					sliderDescriptionShort = NewsletterPro.modules.selectProducts.vars.sliderDescriptionShort,
					sliderImageSize = NewsletterPro.modules.selectProducts.vars.sliderImageSize;

				if (!isDynamicVar(product.dom.name))
					product.dom.name.html( trimString(product.data.name, sliderName.getValue()) );

				if (!isDynamicVar(product.dom.description))
					product.dom.description.html( trimString(product.data.description, sliderDescription.getValue()) );

				if (!isDynamicVar(product.dom.description_short))
					product.dom.description_short.html( trimString(product.data.description_short, sliderDescriptionShort.getValue()) );

				var sliderProductsWidth = NewsletterPro.modules.selectProducts.vars.sliderProductsWidth;

				var width = getProductWidth();

				if (width < 45)
					product.dom.product.width('auto');
				else
					product.dom.product.width(width);
			}

			applySlidersValues();
			self.toggleSliders()

			NewsletterPro.modules.selectProducts.updateProductsWidth();

		},

		triggerRemoveAll: false,
		removeItem : function( element ) 
		{
			var parent = NewsletterProComponents;
			var self = this,
				columns = getColumns();

			function getColumns() {
				var columns = parseInt(NewsletterPro.dataStorage.data.product_tpl_nr);
				return (columns ? columns : 3 );
			}

			element.parent().remove();

			var id = element.data('id');
			var index = self.itemsId.indexOf(id);
			self.itemsId.splice(index, 1);

			if( parent.objs.productList !== 'undefined' ) {

				var items = parent.objs.productList.getItems( true );

				$.each(items, function(index, item) {
					if( item.id == id )
						item.add();
				});
			}

			var items = self.element.find('td.product-item');
			self.element.empty();
			self.itemIndex = 0;	
			$.each(items, function(index, item) {

				if( self.itemIndex % columns == 0 || self.itemIndex == 0) {
					self.element.append('<tr style="margin: 0; padding: 0;" class="last-row";></tr>');
				}

				self.element.find('tr.last-row').last().append(item);

				self.itemIndex++;
			});

			$.each(self.items, function(i, item) {
				if( id !== 'undefined' && item !== 'undefined' && item.id !== 'undefined' && parseInt(item.id) == parseInt(id) ) {
					var index = self.items.indexOf(item);
					self.items.splice(index, 1);
					return false;
				}
			});

			if (!self.triggerRemoveAll) {
				self.toggleSliders();
				self.fixHeightOnRemove();
			} else {
				self.triggerRemoveAll = false;
				self.hideSliders();
			}

			NewsletterPro.modules.selectProducts.updateProductsWidth();
		},

		setProductsPerRow: function(columns) 
		{
			var dataStorage = NewsletterPro.dataStorage;
			dataStorage.add('product_tpl_nr', parseInt(columns));

			var items = $.map(this.items, function(item){
				return item.data;
			});

			self.renderImages = false;
			return this.renderItems(items);
		},

		renderItems: function(products) 
		{
			var dfd = new $.Deferred();
			var self = this,
				productList = NewsletterProComponents.objs.productList,
				selectedProducts = NewsletterPro.modules.selectedProducts;

			function setImageSizeByWidth(product, image, width) 
			{
				return NewsletterPro.modules.selectProducts.setImageSizeByWidth(product, image, width);
			}

			function getLastImageWidth() 
			{
				if ( typeof NewsletterPro.modules.selectProducts.vars.lastImageWidth !== 'undefined')
					return parseInt(NewsletterPro.modules.selectProducts.vars.lastImageWidth);
				return false;
			}

			function getImagesOfProducts(width) 
			{
				return NewsletterPro.modules.selectProducts.getImagesOfProducts(width);
			}

			function setImageOfProduct(items, product) 
			{
				return NewsletterPro.modules.selectProducts.setImageOfProduct(items, product);
			}

			var width = getLastImageWidth();

			if (self.renderImages && width) {

				getImagesOfProducts(width).done(function(items){
					renderItems();

					self.parseProducts(function(product, instance, data){
						setImageSizeByWidth(product, product.dom.image, width);
						setImageOfProduct(items, product);
					});
					dfd.resolve();
				});
			} 
			else 
			{
				dfd.resolve();
				renderItems();
			}

			function renderItems() 
			{
				self.removeItems();

				if (products.length) 
				{
					$.each(products, function(i, item){
						self.addItem(item);
					});
				}
			}

			self.fixHeight();
			return dfd.promise();
		},

		getItemsIds: function() {
			var self = this;
			return $.map(self.items, function(item){
				return parseInt(item.id);
			});
		},

		resetItems: function() 
		{
			var self = this;

			function getProductsById(ids, funct) {
				$.postAjax({'submit': 'getProductsById', 'ids': ids}).done(function(response){
					funct(response.products);
				});
			}

			getProductsById(self.getItemsIds(), function(products) {
				self.renderItems(products);
			});
		},

		fixHeight : function() {
			var self = this;

			if ( self.items.length > 0 ) {

				var maxHeight = $.map(self.items, function( item, index ) {
					var table = item.value.find('.newsletter-pro-product');
					if( table.length > 0 ) {
						var paddindT = parseInt(table.css('padding-top'));
						var paddindB = parseInt(table.css('padding-bottom'));
						var borderT = parseInt(table.css('border-top-width'));
						var borderB = parseInt(table.css('border-bottom-width'));
						var h = table.height() + paddindB + paddindT + borderT + borderB;
						return h;
					}
				});

				maxHeight = Math.max.apply( Math, maxHeight );

				$.each(	self.items, function(index, item) {
					var table = item.value.find('.newsletter-pro-product');
					 if( table.length > 0 )
						 table.height( maxHeight );
				});

				return maxHeight;
			} else
				return false;
		},

		fixHeightOnRemove : function() 
		{
			var self = this;

			if ( self.items.length > 0 ) {
				$.each(	self.items, function(index, item) {
					var table = item.value.find('.newsletter-pro-product');
					 if( table.length > 0 )
						table.css('height', 'auto');
				});
				self.fixHeight();
				return true;
			} else
				return false;
		},

		removeItems : function() 
		{
			// save a lot of calculation 
			this.triggerRemoveAll = true;

			var self = this;

			$.each(self.element.find('.remove-item'), function(index, item) {
				$(item).trigger('click');
			});

			self.fixHeightOnRemove();
		},

		getHeader: function(content)
		{
			var self = this;
			var match = content.match(/<!-- start header -->\s*?<!--([\s\S]*)-->\s*?<!-- end header -->/);
			var matchFullHeader,
				matchHeader,
				headerObject = {};

			if (match != null && match.length > 0)
			{
				matchFullHeader = match[0];
				matchHeader = match[1];
				headerObject = self.parseProductHeader(matchHeader);
				content = content.replace(matchFullHeader, '');
			}

			return headerObject;
		},

		parseProductHeader: function(headerString)
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
		},

		removeItemById : function( id ) 
		{
			var self = this;
			var item = self.element.find('*[data-id="'+id+'"]');

			if( item.length > 0 )
				item.trigger('click');

			self.fixHeightOnRemove();
		},
		columns : 3,
		itemIndex : 0,

		formatItemData : function(data)
		{
			var self = this,
				columns =  getColumns(),
				element = self.element,
				product,
				index = self.itemIndex,
				content = self.template,
				columnsExists = function() { return columns !== 'undefined' ? true : false; };

			function setVar(key, value) 
			{
				content = content.replace(new RegExp('\{'+key+'\}', 'g'), value);
			}

			function getPrice(value, zecimal) 
			{
				zecimal = zecimal || 2;
				return parseFloat(value).toFixed(zecimal);
			}

			function getProduct(id) 
			{
				return '<td style="margin: 0; padding: 0;" class="product-item"><a href="javascript:{}" class="remove-item" data-id="'+id+'" onclick="NewsletterProComponents.objs.selectedProducts.removeItem($(this));">&nbsp;</a>'+content+'</td>';
			}

			function getColumns() 
			{
				var columns = parseInt(NewsletterPro.dataStorage.data.product_tpl_nr);
				return (columns ? columns : self.columns );
			}

			function htmlToText(html) 
			{
				var text = $('<span></span>');
				text.html(html);
				return text.text();
			}

			function displayValue(value) 
			{
				if (typeof value === 'undefined' || value === null) 
					return '';
				return value;
			}

			self.productHeader = self.getHeader(content);

			var vars = {
				'id_product': data.id_product,
				'id_supplier': data.id_supplier,
				'id_manufacturer': data.id_manufacturer,
				'id_category_default': data.id_category_default,
				'id_shop_default': data.id_shop_default,
				'id_shop': data.id_shop, // ???

				'price': getPrice( data.price ),
				'price_convert': getPrice( data.price_convert ),
				'orderprice': getPrice( data.orderprice ),
				'price_tax_exc': getPrice( data.price_tax_exc ),
				'price_without_reduction': getPrice( data.price_without_reduction ),
				'price_without_reduction_convert': getPrice( data.price_without_reduction_convert ),
				'price_without_reduction_display': data.price_without_reduction_display,
				'price_tax_exc_convert': getPrice( data.price_tax_exc_convert ),
				'price_tax_exc_display': data.price_tax_exc_display,
				'wholesale_price_convert': getPrice( data.wholesale_price_convert ),
				'wholesale_price_display': data.wholesale_price_display,

				'price_display': data.price_display,
				'wholesale_price': data.wholesale_price,
				'unit_price_ratio': data.unit_price_ratio,

				'on_sale': data.on_sale,
				'online_only': data.online_only,
				'ecotax': data.ecotax,
				'quantity': data.quantity,
				'minimal_quantity': data.minimal_quantity,
				'currency': data.currency,

				'discount': data.discount,

				'unity': data.unity,
				'unit_price': data.unit_price,
				'unit_price_convert': data.unit_price_convert,
				'unit_price_display': data.unit_price_display,

				'unit_price_tax_exc': data.unit_price_tax_exc,
				'unit_price_tax_exc_convert': data.unit_price_tax_exc_convert,
				'unit_price_tax_exc_display': data.unit_price_tax_exc_display,

				'unit_price_bo': data.unit_price_bo,
				'unit_price_bo_convert': data.unit_price_bo_convert,
				'unit_price_bo_display': data.unit_price_bo_display,

				'additional_shipping_cost': data.additional_shipping_cost,
				'supplier_reference': data.supplier_reference,
				
				'reference': data.reference,

				'width': data.width,
				'height': data.height,
				'depth': data.depth,
				'weight': data.weight,
				'quantity_discount': data.quantity_discount,
				'condition': data.condition,
				'date_add': data.date_add,
				'date_upd': data.date_upd,

				'description': htmlToText(data.description),
				'description_short': htmlToText(data.description_short),

				'available_now': data.available_now,
				'available_later': data.available_later,
				'link_rewrite': data.link_rewrite,
				'name': data.name,
				'legend': data.legend,
				'manufacturer_name': data.manufacturer_name,
				'tax_name': data.tax_name,
				'rate': data.rate,
				'category_default': data.category_default,
				'link': data.link,
				'reduction': data.reduction,

				'image_path': data.image_path,
				'image_width': data.image_width,
				'image_height': data.image_height,

				'thumb_path': data.thumb_path,
				'thumb_width': data.thumb_width,
				'thumb_height': data.thumb_height,
			};

			var productTemplate = new NewsletterPro.components.ProductRender(content, vars);
			content = productTemplate.render();

			if (columnsExists())
				setVar('column_width', String(( 100 / columns) + '%'))

			// setVar('.*', '');
			setVar('(\\s+)?columns(\\s+)?=(\\s+)?\\d+(\\s+)?', '');

			product = getProduct(vars.id_product)

			if( index % columns == 0 || index == 0)
				element.append('<tr style="margin: 0; padding: 0;" class="last-row";></tr>');

			self.itemIndex++;
			return {product: $(product), formatedData: vars};
		},
	}, // end of SelectedProducts

	CategoryList : 
	{	
		element : null,
		objTarget : null,
		targetCallback : null,
		currentItem : null,
		clone:  function() {
			return $.extend(true, {}, this)
		},
		init: function( element, objTarget ) 
		{
			var self = this;
			this.element = element;
			this.objTarget = objTarget;
			this.initCallback(this.categoryListCallback);

			$.each(NewsletterPro.dataStorage.get('categories_list'), function(index, item)
			{
				var it = self.eItemTemplate( item, self.element );
				self.itemTemplate(it, item);
			});

			return this;
		},
		initCallback : function( targetCallback ) {
			this.targetCallback = targetCallback;

			return this;
		},
		eItemTemplate : function( item, write ) {
			var self = this;

			var hasSubcategory = ( item.subcategory.length > 0 ) ? 'class="category_arrow"' : '';

			var template = '';
				template += '<li class="even" data-id="'+item.id_category+'">';
				template += '<span '+hasSubcategory+'>&nbsp;</span>';
				template += '<span style="cursor: default; margin-left: 3px; display: inline-block;">'+item.name+'</span>';
				template += '<span class="subcategory_lis" style="display: none; margin-bottom: 0; color: black; font-weight: normal;">&nbsp;</span>';
				template += '</li>';

			template = $(template);
			write.append( template );
			return template;
		},

		itemTemplate : function(instance, item)
		{
			var self = this;

			var id = instance.data('id');
			var level = item.level;

			var item = {
				instance : instance,
				id : id,
				level : level,
			}

			item.instance.on('click',function( event ) {
				event.stopPropagation();
				self.currentItem = item;
				self.categoryListCallback( self.currentItem.id );
			});
		},

		categoryListCallback : function(id)
		{
			var parent = NewsletterProComponents;
			var self = this;
			var level = parseInt(self.currentItem.level);
			if ( !self.currentItem.instance.hasClass('selected') ) {
				self.currentItem.instance.addClass('selected');

				if (  self.lastItemSelected != null && self.lastItemSelected.hasClass('selected') ) {
					self.lastItemSelected.removeClass('selected');
				}

				if ( self.lastItemRootSelected != null && level == 2 ) {
					self.lastItemRootSelected.find('span.subcategory_lis').css('display', 'none').empty();
				}

				if ( level == 2  )
					self.lastItemRootSelected = self.currentItem.instance;

				self.lastItemSelected = self.currentItem.instance;
			}

			self.currentItem.instance.css({height : 'auto'});

			parent.elem.productSearchAjax.show();
			$.postAjax({'submit': 'getProducts', getProducts: id}).done(function(data) 
			{
				parent.elem.productSearchAjax.hide();
				if( parent.elem.productSort.val() == 'quantity' ){
					data.products.sort(parent.sortQntDesc);
				}
				parent.Sort.dataItems = data.products;
				self.objTarget.createItems(data.products);
			});

			var returnItem = Array();
			var findCategory = function( currentCategory ) {

				$.each( currentCategory, function( index, item ) {
					if ( item.id_category == id ) {
						returnItem = item;
						return false;
					} else {
						findCategory(item.subcategory);
					} 
				});
				return returnItem;
			};

			var category = findCategory(NewsletterPro.dataStorage.get('categories_list'));

			if (  typeof category == 'undefined' || category.subcategory.length <= 0)
				return false;

			var write = self.currentItem.instance.find('span.subcategory_lis').css('display', 'block').empty();

			$.each( category.subcategory, function( index, item ) {

				var it = self.eItemTemplate( item, write );
				self.itemTemplate( it, item );
			});
		},

	}, // end of CategoryList

	TabItems :
	{
		lastItem : null,
		buttons : [],
		onChange: null,
		clone:  function() {
			return $.extend(true, {}, this)
		},
		init : function( buttons, content, onChange ) 
		{
			NewsletterPro.extendSubscribeFeature(this);

			var self = this;
			this.onChange = onChange;
			this.content = content.children('div');

			$.each( buttons.find('a') , function( index, item ) 
			{
				item = $(item);
				item.id = item.attr('id');
				var march_id_num = String(item.attr('id')).match(/\d+/, '');
				item.idNum =  march_id_num ? parseInt( march_id_num ) : null;

				var regex = new RegExp("^.*"+item.idNum +"$", "g");

				item.target = $( $.grep( self.content, function( item, index ) {
					item = $(item);

					if ( index != 0 )
						item.hide();

					return regex.test( item.attr('id') ) ? item : null ;
				})[0] );

				if ( index == 0 && !item.hasClass('selected') ) {
					item.addClass('selected');
					self.lastItem = item;
				}

				self.addEvent( item );
				self.buttons.push(item);
			});

			var hash = window.location.hash;
			var currentTab = self.buttons.filter(function(item){
				var href = item.attr('href');
				return (href == hash);
			});

			if( hash != '#viewImported' ) 
			{
				if (typeof currentTab[0] != 'undefined') {
					currentTab[0].trigger('click');
					currentTab[0].target.show();
				}
				else
					self.trigger('tab_newsletter_3');
			}

			return this;
		},

		addEvent : function( item ) 
		{
			var self = this;
			item.on('click', function( event ) 
			{
				if ( self.lastItem != null && self.lastItem.target === item.target ) 
				{
					return false;
				} 
				else if ( self.lastItem != null ) 
				{
					self.lastItem.target.hide();
					item.target.show();

					if ( self.lastItem.hasClass('selected') )
						self.lastItem.removeClass('selected');
				}
				if ( !item.hasClass('selected') )
					item.addClass('selected');

				self.lastItem = item;

				self.publish('change', item);

				if (typeof self.onChange === 'function') 
				{
					self.onChange(item);
				}
			});
		},

		trigger : function( item_id ) 
		{

			var item = $.grep( this.buttons, function( item, index ) {
				return ( item.id == item_id ) ? item : null;
			})[0];

			if (typeof item !== 'undefined') {
				item.trigger('click');
			}

			return this;
		},

		triggerHref: function(href)
		{
			var item = $.grep( this.buttons, function( item, index ) {
				return ( item.attr('href') == href ) ? item : null;
			})[0];

			if (typeof item !== 'undefined') {
				item.trigger('click');
			}

			return this;
		},

		getLastItem : function()
		{
			if (this.lastItem != null)
				return this.lastItem;
			return false;
		}
	}, // end of TabItems

	EmailSendList : 
	{
		items : [],
		clone:  function() {
			return $.extend(true, {}, this)
		},
		init : function( element, objTarget ) {
			var self = this;
			this.element = element;
			this.objTarget = objTarget;

			// Create items that are displayed in html 
			// List item require data-email 
			var eItems = this.element.find('li');
			if( eItems.length > 0 ) {
				$.each(eItems, function(index, eItem) {
					var eItem = $(eItem);
					if( typeof eItem.data('email') !== 'undefined' ) {
						var dItem = eItem.data('email');
						self.items.push( self.itemTemplate( eItem, dItem ) );
					}
				});
			}
			return this;
		},

		eItemTemplate : function( email ) {
			var self = this;
			var cls = ( self.getLastItem() === false ) ? 'even' : ( self.getLastItem().instance.hasClass('odd') ? 'even' : 'odd' );
			var template = '';
 				template += '<li class="'+cls+'">';
				template += '<span class="email_text">'+email+'</span> ';
				template += '</li>';

			template = $(template);
			self.element.append( template );
			return template;
		},

		itemTemplate : function( instance, email ) {

			var item = {
				instance : instance,
				email : email,
			};

			return item;
		},

		createItems : function( emails ) 
		{
			var self = this;

			$.each(emails, function(index, email) {
				var it = self.eItemTemplate( email );
				self.items.push( self.itemTemplate( it, email ) );
			});
		},

		createObjItems : function( objItems, append ) {
			var self = this;
			append = append || 'prepend';

			$.each(objItems, function(index, oItem) {
				if( typeof oItem == 'object' ) {
					if (append == 'append')
						self.element.append(oItem.instance);
					else
						self.element.prepend(oItem.instance);

					self.items.push( oItem );
				}
			});
		},

		removeItems : function( items ) {
			var self = this;
			$.each(items, function(index, item ) {
				var index = self.items.indexOf(item);
				if( index != -1 ) {
					self.items[index].instance.remove();
					self.items.splice(index, 1);
				}
			});
		},

		removeAllItems : function() {
			var self = this;
			self.items = [];
			self.element.empty();
		},

		removeLast : function() {
			var self = this;
			if( this.items.length > 0 ) {
				var lastItem = self.items.pop();
				lastItem.instance.remove();
				return lastItem;
			} else {
				return false;
			}
		},

		removeFirst : function() {
			var self = this;
			if( this.items.length > 0 ) {
				var firstItem = self.items.shift();
				firstItem.instance.remove();
				return firstItem;
			} else {
				return false;
			}
		},

		moveFirst : function() {
			var self = this;
			var first = self.removeFirst();
			self.objTarget.createObjItems([ first ], 'prepend');
			return first;

		},

		getItems : function() {
			return this.items;
		},

		getLastItem: function() {
			if( this.items.length > 0 )
				return this.items[this.items.length-1];
			else
				return false;
		},

		getFirstItem : function() {
			if( this.items.length > 0 )
				return this.items[0];
			else
				return false;
		},

		getLength : function() {
			return parseInt(this.items.length);
		}
	}, // end of EmailList

	UploadCSV : 
	{
		fields : [],
		items : [],
		selected : null,
		form : null,
		file : null,
		msg : null,
		container : null,
		details : null,
		separator : null,
		init : function( cfg ) {
			var self       = $.extend(true, {}, this);
			self.file      = cfg.file;
			self.form      = cfg.form;
			self.msg       = cfg.msg;
			self.files     = cfg.files;
			self.container = cfg.container;
			self.details   = cfg.details;
			self.separator = cfg.separator;

			 self.createItems();
			 self.initEvents();

			 return self;
		},

		initEvents : function() {
			var self = this	;
			self.file.on('change', function(event) {
				self.uploadItem();
			});
		},

		setSelected : function( item ) {
			var self = this;

			if( self.selected != null && self.selected.instance.hasClass('selected') )
				self.selected.instance.removeClass('selected');

			self.selected = item;

			if( !self.selected.instance.hasClass('selected') )
				self.selected.instance.addClass('selected');

		},

		uploadItem : function() {
			var self = this;
			$.submitAjax({ 'submit': 'uploadCSV',  name : 'uploadCSV', form : self.form }).done(function(data) {
				if( data.status ) {
					var template = $('<tr data-name="'+data.data.name+'"> \
										<td> '+data.data.name+' </td> \
										<td class="center">\
											<a class="delete-added" href="javascript:{}" onclick="NewsletterProComponents.objs.uploadCSV.deleteItemByName(\''+data.data.name+'\');"><i class="icon icon-remove cross-white"></i></a>\
										</td> \
									</tr>');

					self.files.append( template );
					self.createItem( template );
				}
				else {
					self.writeMsg( data.msg );
				}
			});
		},

		writeMsg : function( msg, type ) {
			type = 'error' || type;
			this.msg.empty().append( '<span class="' + ( type == 'error' ? 'error-msg' : 'success-msg' ) + '">' + msg + '</span>');
		}, 

		deleteItemByName : function( name ) {
			var self = this;

			$.postAjax({'submit': 'deleteCSVByName', deleteCSVByName : name}).done(function(data) {
				if( data.status ) {
					self.removeItemByName( name );

					if( self.selected != null ) {
						if( self.selected.name == name )
							self.selected = null;
					}

					if( self.items.length <= 0 ) {
						self.files.hide();
					}
				} else {
					self.writeMsg( data.msg );
				}
			})
		},

		removeItemByName : function( name ) {
			var self = this;

			$.each(self.items, function(index, item) {
				if( typeof item != 'undefined' && item.name == name ) {

					var index = self.items.indexOf(item)
					item.instance.remove();
					self.items.splice(index, 1);
					return true;
				}
			});
		},

		createItems : function() {
			var self = this;
			var items = self.files.find('tbody tr');
			$.each(items, function(index, eItem ) {
				self.createItem( $(eItem) );
			});
		},

		createItem : function( eItem ) {
			var self = this;
			var item = {
				instance : eItem,
				name : eItem.data('name'),
				init : function() {
					item.initEvents();
					self.items.push( item );
				},
				initEvents : function() {
					item.instance.on('click', function(event) {
						event.stopPropagation();
						item.click();
					});
				},
				click : function() {
					self.setSelected( item );
				},
			}
			item.init();
		},

		nextStep : function( instance ) 
		{
			var self = this;
			self.fields = [];

			if( self.selected == null ) 
			{
				alert( instance.data('no-file') );
			} 
			else 
			{
				var filename = self.selected.name;
				var separator = $.trim(self.separator.val());
				var fromLine = $('#from-linenumber').val();
				var toLine = $('#to-linenumber').val();

				if( separator == ',' || separator == ';' ) {
					$.postAjax({ 'submit': 'loadCSV', loadCSV : filename, delimiter: separator, line : {from: fromLine, to: toLine} }, 'html').done(function(data) {
						self.container.hide();
						self.writeDetails(data);
					});
				} else {
					alert( instance.data('bad-separator') );
					return false;
				}
			}
		},

		importCSV : function( instance ) 
		{
			var self = this;
			var filename = self.selected.name;
			var separator = $.trim(self.separator.val());
			var fields = self.fields,
				filterName = $('#import-csv-filter-name').val();

			if( fields.length == 0 )
				alert( instance.data('no-fields') );
			else {

				var exists = $.grep(self.fields, function(item, index) {
					return item.db_field == 'email';
				});

				if( exists.length == 0 ) {
					alert( instance.data('no-email') );
					return false;
				} else {

					var fromLine = $('#from-linenumber').val();
					var toLine = $('#to-linenumber').val();
				
					$.postAjax({ 'submit': 'importCSV', importCSV : filename, delimiter: separator, fields : fields, line : {from: fromLine, to: toLine}, filter_name: filterName}, 'html').done(function(data) {
							self.writeDetails(data);
					});
				}

			}
		},

		geBack : function() {
			var self = this;
			self.writeDetails('');
			self.details.hide();
			self.container.show();
			self.fields = [];
		},

		writeDetails : function( detail ) {
			this.details.show().empty().append( detail );
		},

		addField : function( select ) 
		{
			var self = this;

			var csv_field = select.data('field');
			var db_field = select.val();

			var field = { csv_field : csv_field, db_field : db_field };

			if( db_field != 0 ) 
			{
				var exists = $.grep(self.fields, function(item, index) {
					return item.csv_field == field.csv_field;
				});

				if( exists.length == 0 ) {
					self.fields.push(field);
				} else {
					var index = self.fields.indexOf(exists[0]);
					self.fields[index] = field;
				}

			} else {
				$.each(self.fields, function(index, item) {
					 if( item.csv_field == field.csv_field ) {
					 	var i = self.fields.indexOf(item);
					 	self.fields.splice(i, 1);
					 }
				});
			}
		}
	}, // end of UploadCSV
}

NewsletterProComponents.init();