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

var NEWSLETTER_PRO_DEBUG_MODE = true;
var jQueryNewsletterProNew = typeof NPRO_JQUERY_NEW !== 'undefined' ? NPRO_JQUERY_NEW : jQuery;
var jQueryNewsletterProOld = typeof NPRO_JQUERY_OLD !== 'undefined' ? NPRO_JQUERY_OLD : jQuery;

;(function($) {
	$.postAjax = function( data, dataType, catchError ) 
	{
		dataType = dataType || 'json';
		catchError = ( typeof catchError !== 'undefined' ) ? catchError : true ;

		return $.ajax({
   			url : NewsletterPro.dataStorage.get('ajax_url'),
   			type : 'POST',
   			dataType: dataType,
   			data : data,
   			error : function( data ) {
   				if( NEWSLETTER_PRO_DEBUG_MODE == true && dataType == 'json' )
				{
					if ( catchError == true )
					{
						var login = (data.getResponseHeader('Login') === 'true' ? true : false);

						if (login)
							alert('The login session has expired. You must refresh the browser and login again. The next time when you are login check the button "Stay logged in".');
						else
							alert('Ajax request error, please check your console for more details!');
					}

					console.warn('error: the returned data must be json, your response text is: ');
				    console.warn(data.responseText);
				}
  			}
		}).promise();
	};

	$.updateConfiguration = function(name, value)
	{
		return $.postAjax({'submit': 'jsUpdateConfiguration', name: name, value: value}).promise();
	};

	$.objSize = function( obj ) 
	{
	    var size = 0, key;
	    for (key in obj) {
	        if (obj.hasOwnProperty(key)) size++;
	    }
	    return size;
	};

	$.fn.getFormData = function() 
	{
        form = this;
        var formObj =  form.serializeArray();
        var formdata = {};
        var j = 0, len = formObj.length, item;
        for( ; j < len; j++)
        {
            item = formObj[j];
            formdata[item.name] = item.value;
        }

        if (formdata)
        	return formdata;
        return {};
	};

	$.setCookie = function(c_name, value, exdays) 
	{
	    var exdate = new Date();
	    exdate.setDate(exdate.getDate() + exdays);
	    var c_value = escape(value) + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
	    document.cookie = c_name + "=" + c_value;
	}

	$.getCookie = function(c_name) 
	{
	    var i, x, y, ARRcookies = document.cookie.split(";");
	    for (i = 0; i < ARRcookies.length; i++) {
	        x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
	        y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
	        x = x.replace(/^\s+|\s+$/g, "");
	        if (x == c_name) {
	            return unescape(y);
	        }
	    }
	}

	$.deleteCookie = function(name) 
	{
	    document.cookie = name + '=; expires=Thu, 01-Jan-70 00:00:01 GMT;';
	}

    $.submitAjax = function( obj, dataType, catchError ) 
    {
    	catchError = ( typeof catchError !== 'undefined' ) ? catchError : true ;
        dataType = dataType || 'json';
        var self = this;

        this.form = obj.form;

        var formObj =  this.form.serializeArray();


        var formdata = false;
        if(window.FormData)
            formdata = new FormData();

        var files = this.form.find('input[type="file"]');

        var k = 0, length = files.length, input;
        for ( ; k < length; k ++) {
            input = $(files[k]);

            var i = 0, len = input[0].files.length, file;

            for ( ; i < len; i++ ) {
                file = input[0].files[i];
                if (formdata) {
                    formdata.append(input.attr('name') + "", file);
                }
            }
        }

        var j = 0, len = formObj.length, item;
        for( ; j < len; j++)
        {
            item = formObj[j];

            if (formdata) {
                formdata.append(item.name, item.value);
            }
        }

        var submitModule = null;

        for (var key in obj)
        {
        	if (/^submit_\w+/.test(key))
        	{
        		submitModule = key
        		break;
        	}
        }

        if (formdata) 
        {
        	if (obj.hasOwnProperty('name'))
        	{
        		var appendValue = true;
        		if (obj.hasOwnProperty('value'))
        			appendValue = obj.value;

        		formdata.append(obj.name, appendValue);

        	}

            if (obj.hasOwnProperty('submit'))
	            formdata.append('submit', obj.submit);
	        else if (submitModule)
	        	formdata.append(submitModule, obj[submitModule]);

	        if (obj.hasOwnProperty('data'))
	        {
	        	$.each(obj.data, function(name, value){
	        		formdata.append(name, value);
	        	});
	        }
       	}

       	var errorCallback = function (data) 
       	{
        	if( NEWSLETTER_PRO_DEBUG_MODE == true && dataType == 'json' )
			{
				if ( catchError == true ) 
					alert('You have an request error, check the console for more details.');

				console.log('error: the returned data must be json, your response text is: ');
			    console.log(data.responseText);
			}
        };

        if(formdata) 
        {
            return $.ajax({
                url:  NewsletterPro.dataStorage.get('ajax_url'),
                type: 'POST',	            
                data: formdata,
                processData: false,
                contentType: false,
                dataType: dataType,
                error: errorCallback
            }).promise();
        }
        else
        {
        	var data = this.form.getFormData();

        	if (obj.hasOwnProperty('submit'))
        		data['submit'] = obj.submit;
      		else if (submitModule)
	        	formdata.append(submitModule, obj[submitModule]);

        	return $.ajax({
                url:  NewsletterPro.dataStorage.get('ajax_url'),
                type: 'POST',	            
                data: data,
                dataType: dataType,
                error: errorCallback
            }).promise();
        }

    };

	$.fn.getPaddingWidth = function() 
	{
		var left = parseFloat(this.css('padding-left')),
			right = parseFloat(this.css('padding-right'));
		return left + right;
	};

	$.fn.widthCSS = function(value) 
	{
		var that = $(this);
		if (typeof value !== 'undefined')
		{
			var val = 0;

			if (/%/.test(new RegExp(String(value))))
				val = value;
			else if (/px/.test(new RegExp(String(value))))
				val = value;
			else
				val = parseInt(value) + 'px';

			that.css('width', val);

			return that;
		}
		else
			return parseInt(that.css('width'));
	};

	$.fn.quickOuterWidth = function() 
	{
		var elem = this.get(0);
		if (window.getComputedStyle) 
		{
			var computedStyle = window.getComputedStyle(elem, null);
			return elem.offsetWidth + (parseInt(computedStyle.getPropertyValue('margin-left'), 10) || 0) + (parseInt(computedStyle.getPropertyValue('margin-right'), 10) || 0);
		} 
		else 
		{
			return elem.offsetWidth + (parseInt(elem.currentStyle["marginLeft"]) || 0) + (parseInt(elem.currentStyle["marginRight"]) || 0);
		}
	};

})(jQueryNewsletterProNew);
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

$.fn.gkGrid = function( cfg ) 
{
	var self = this;

	if( typeof id_grid == 'undefined' )
        id_grid = 0;

    id_grid++;

	self.addClass('gk-data-grid');

	self.pageable    = $.type(cfg.pageable) != 'undefined' ? cfg.pageable : true;
	self.currentPage = cfg.currentPage || 1;
	self.template    = cfg.template || null;
	self.events      = cfg.events || null;
	self.selectable  = cfg.selectable || false;
	self.checkable   = cfg.checkable || false;
	self.footer      = null;
	self.countChecked = 0;
	this.defineSelected = cfg.defineSelected || null;

	if ($.isFunction(cfg.start))
		cfg.start();

	var fn = {
		setFields : function() {
			var fields = [];
			var columns = [];

			var tr = self.find('tr');
			$.each( tr.find('th'), function(index, field) {
				$(field).addClass('gk-header-datagrid');

				var tdClass = $(field).attr('class').split(' ');
				if (typeof tdClass[0] !== 'undefined')
					tdClass = tdClass[0];
				else 
					tdClass = '';

				var className = ( $.type($(field).attr('class')) != 'undefined' ? $.trim(tdClass) : null );
				var name = $(field).text();

				if( $.type( $(field).data('field') ) != 'undefined' ) {
					var template  = ( $.type( $(field).data('template') ) != 'undefined' 
										? $(field).data('template') 
										: ( ( self.template != null && $.type(self.template[$(field).data('field')]) == 'function' ) ? $(field).data('field') : null )
									);
					var obj = {
						field : $(field).data('field'),
						name : name,
						className : className,
						template : template,
					}
					fields.push( obj );
					columns.push( obj );
				} else {
					var template  = ( $.type( $(field).data('template') ) != 'undefined' 
										? $(field).data('template') 
										: null );

					columns.push({
						field: 'gk_'+index, 
						name: name,
						className : className,
						template : template,
					});
				}
			});

			cfg.dataSource.schema.model.fields = fields;
			cfg.dataSource.schema.model.columns = columns;
		}, // end of setFields

		setFooter: function() {
			if( self.pageable ) {

				var page = {
					instance: null,
					eFirst : null,
					ePrev: null,
					eInput: null,
					eNext: null,
					eLast: null,
					ePageTotal: null,
					eItemsInfo: null,

					init: function() {
						var columns = cfg.dataSource.schema.model.columns;


						var pageTemplate = '';
						pageTemplate += '<div class="gk-pagination form-inline">';
						pageTemplate += '<select class="form-control gk-rpp">\
								<option selected value="10">10</option><option value="50">50</option>\
								<option value="100">100</option><option value="500">500</option>\
								</select>';
						pageTemplate += '<a class="btn btn-default gk-page-first" href="javascript:{}">\
											<i class="icon icon-step-backward gk-icon gk-icon-first"></i>\
										</a>';
						pageTemplate += '<a class="btn btn-default gk-page-prev" href="javascript:{}">\
											<i class="icon icon-chevron-left gk-icon gk-icon-prev"></i>\
										</a>';
						pageTemplate += '<span>'+l('page')+' </span>';
						pageTemplate += '<input class="form-control text-center gk-page-input" type="text" value="'+self.currentPage+'">';
						pageTemplate += '<span class="gk-page-total"></span>';

						pageTemplate += '<a class="btn btn-default gk-page-next" href="javascript:{}">\
											<i class="icon icon-chevron-right gk-icon gk-icon-next"></i>\
										</a>';
						pageTemplate += '<a class="btn btn-default gk-page-last" href="javascript:{}">\
											<i class="icon icon-step-forward gk-icon gk-icon-last"></i>\
										</a>';
						pageTemplate += '</div>';
						pageTemplate += '<div class="gk-checked" '+( !self.checkable ? 'style="display: none;"' : '')+'><span>( <span class="gk-checked-count">0</span> ) '+l('selected')+'</span></div>';
						pageTemplate += '<div class="gk-pagination-info">';
						pageTemplate += '<span class="gk-items-info"></span>';
						pageTemplate += '</div>';

						var template = '';
						template += '<tfoot>';
						template += '<tr>';
						template += '<td class="gk-footer" colspan="'+columns.length+'">'+pageTemplate+'</td>';
						template += '</tr>';
						template += '</tfoot>';
						template = $(template);

						page.instance = template;

						self.append(template);

						page.setComponents();

						self.footer = page;
						return self.page;
					},

					setComponents : function() {
						page.eFirst = self.find('.gk-page-first');
						page.ePrev = self.find('.gk-page-prev');
						page.eInput = self.find('.gk-page-input');
						page.eNext = self.find('.gk-page-next');
						page.eLast = self.find('.gk-page-last');
						page.ePageTotal = self.find('.gk-page-total');
						page.eItemsInfo = self.find('.gk-items-info');
						page.eChecked = self.find('.gk-checked');
						page.eCheckedCount = self.find('.gk-checked-count');
						
						page.eRpp = self.find('.gk-rpp');

						page.eFirst.on('click', page.first);
						page.ePrev.on('click', page.prev);
						page.eInput.on('keyup', page.input);
						page.eNext.on('click', page.next );
						page.eLast.on('click', page.last);
						
						page.eRpp.on('change', page.rpp);
					},
					
					rpp: function(event){
						cfg.dataSource.pageSize = parseInt( $(event.currentTarget).val() );
						page.goToPage( 1 );
					},

					first : function( event ) {
						page.goToPage( 1 );
					},

					prev : function(event) {
						//page.setCurrentPage( --self.currentPage );
						page.goToPage( --self.currentPage );
					},

					input : function(event) {
						if( event.keyCode == 13 ) {
							var pageNumber = $(event.currentTarget).val();

							if( /^-?[\d]+$/.test(pageNumber) == false )
								return false;

							pageNumber = parseInt(pageNumber);
							page.goToPage( pageNumber );
						}
					},

					next : function(event) {
						//page.setCurrentPage( ++self.currentPage );
						page.goToPage( ++self.currentPage );
					},

					last : function(event) {
						page.goToPage( cfg.dataSource.getPageInfo().lastPage() );
					},

					goToPage : function( nr ) {
						page.setCurrentPage( nr );
						cfg.dataSource.setView( self.currentPage );
					}, 

					setCurrentPage : function( nr ) {
						self.currentPage = nr;
						page.eInput.val( self.currentPage );
					},

					writePageTotal : function() {
						if( self.pageable ) {
							page.ePageTotal.html( l('of') + ' ' + cfg.dataSource.getPageInfo().lastPage() );
							return true;
						}
						return false;
					},

					writeItemsInfo: function() {
						if( self.pageable ) {
							var total = cfg.dataSource.getPageInfo().countIntems();
							var start = cfg.dataSource.getPageInfo().start();
							var end = cfg.dataSource.getPageInfo().end();

							var template = '<span>'+start+' - '+end+' '+l('of')+' '+total+' '+l('items')+' '+'<span>';

							page.eItemsInfo.html(template);
							
							if (cfg.dataSource.schema.model.id == 'id_customer'){
								$('#customers_filtered').text('('+ total +')');
							}

						}
					},

					writeCheckedInfo: function( value ) {
						if (self.checkable) {
							page.eChecked.show();
							self.countChecked = self.countChecked + value;

							if (self.countChecked < 0)
								self.countChecked = 0;

							page.eCheckedCount.html( cfg.dataSource.getSelectedEmails().length );
						} else {
							page.eChecked.hide();
						}
					},

					setCheckedInfo: function( value ) {
						if (self.checkable) {
							page.eChecked.show();
							self.countChecked = value;
							page.eCheckedCount.html( self.countChecked );
						} else {
							page.eChecked.hide();
						}
					},
				}
				page.init();
			}
		},  // end of setFields
	}; // end of fn

	self.updateFooter = function() {
		if( self.footer != null ) {
			self.footer.writePageTotal();
			self.footer.writeItemsInfo();
		} 
	};

	self.addFooter = function(func, method) {
		method = method || 'append';

		var columns = cfg.dataSource.schema.model.columns.length,
			row = func.call(this, columns),
			footer;

		footer = self.find('tfoot');
		footer[method](row);
		return row;
	};

	self.addHeader = function(func, method) {
		method = method || 'append';

		var columns = cfg.dataSource.schema.model.columns.length,
			row = func(columns),
			header;

		header = self.first();
		header[method](row);
		return row;
	};

	self.makeRow = function(arr, columns)
	{
		columns = columns || cfg.dataSource.schema.model.columns.length;
		var tr, td;
		tr = $('<tr></tr>');
		td = $('<td class="gk-footer" colspan="'+columns+'"></td>');

		$.each(arr, function(i, item){
			td.append(item);
		});

		tr.html(td);
		return tr;
	};

	fn.setFields();
	fn.setFooter();

	cfg.dataSource.dataGrid = self;

	cfg.dataSource.sync().done(function(dataSource){
		if (typeof cfg.done === 'function') {
			cfg.done.call(dataSource, dataSource);
		}
	});

	return self;
};

$.fn.gkButton = function( cfg ) {

	var self = $.extend(true, {

		vars: {
			title: cfg.title || null,
			name: cfg.name || '',
			className: cfg.className  || null,
			item: cfg.item || null,
			command: cfg.command || null,
			css: cfg.css || null,
			attr: cfg.attr || null,
			icon: cfg.icon || null,
		},

		setup: function() {

			var selector = self.fn.getSelector();
			var id       = self.fn.getId();

			if( self.length == 0 ) {
				self = $('<a href="javascript:{}" style="display: inline-block;"></a>');

				// Declare all variables 
				self.vars = this.vars;
				self.fn = this.fn;
				self.obj = this.obj;
			}

			if( selector.type == 'id' ) {
				self.attr('id', selector.name + id);
			} else if( selector.type == 'class' ) {
				self.attr('class', selector.name + id);
			}

			var item = self.vars.item;
			var html = $('<span></span>');

			self.addClass('btn');
			self.addClass('btn-default');

			if( self.vars.className != null )
				self.addClass(self.vars.className);

			if( self.vars.icon != null )
				html.append(self.vars.icon);

			if( self.vars.title != null )
				html.append('<span>'+self.vars.title+'</span>');

			if (html != null)
				self.html(html);

			if( self.vars.css != null )
				self.css(self.vars.css);

			if( self.vars.attr != null )
				self.attr(self.vars.attr);

			self.disable = function() {
				self.off();
				self.addClass('gk-button-disabled');
				self.removeClass('gk-button');
			}

			self.enable = function() {
				self.fn.addEvents();
				self.addClass('gk-button');
				self.removeClass('gk-button-disabled');	
			}

			self.changeTitle = function(title) {
				self.html(title);
			}

			self.fn.addEvents();
			return self;
		},

		obj: {}, // end of obj

		fn: {
			addEvents: function() {
				var item = self.vars.item;
				var command = $.trim(self.vars.command);

				self.on('click', function(event) {
					event.preventDefault();
					event.stopPropagation();

					if( $.type(cfg.click) == 'function' )
					{
						if (cfg.click.call(this, event) === false)
							return;
					}

					if( command != null ) {
						switch( command ) {
							case 'delete':
								if( $.type(cfg.confirm) === 'function' ) 
								{
									if (cfg.confirm.call(this, event)) {
										item.destroy();
									}
								} else {
									item.destroy();
								}

							break;
						}

					}
				});
			},

			getSelector: function() {
				var name = self.selector;
				var type = false;
				if( /^\#/.test(name) ) {
					type = 'id';
					name = name.slice(1, name.length );
				} else if( /^\./.test( name ) ) {
					type = 'class';
					name = name.slice(1, name.length );
				}
				return {name: name, type: type};
			},

			getId: function() {
				var id = '';
				if( self.vars.item != null )
					id = '_' + self.vars.item.data.id;
				else
					id = '_' + gk.fn.uniqueId();
				return id;
			},

		}, // end of fn

	}, this);

	return self.setup();
}

var gk = {
	template : function( template, templateVars ) {
		return this.fn.replaceCallback( template, templateVars);
	}, // end of template

	fn: {
		ajax : function( method ) {
			return $.ajax({
						url: method.url || '',
						type: method.type || 'POST',
						dataType: method.dataType || 'json',
						data: method.data || null,
						success : function( data ) 
						{
							if( $.type( method.success ) == 'function' )
								method.success(data, method.data );
						},
						error : function( xhr, ajaxOptions, thrownError ) 
						{
							if (thrownError.hasOwnProperty('message') && typeof thrownError.message === 'string')
								console.error(thrownError.message + ' ' + ' when creating the dataGrid. Please check the ajax request!');

							var data = {xhr:xhr, ajaxOptions:ajaxOptions, thrownError:thrownError};
							if( $.type( method.error ) == 'function' )
								method.error(data, method.data );

						},
						complete  : function( data ) 
						{
							if( $.type( method.complete ) == 'function' )
								method.complete(data, method.data );

						}
				}).promise();
		},

		replaceCallback : function( string, templateVars ) {
			templateVars = templateVars || {};
			$.each(templateVars, function(key, value) {
				string = string.replace( new RegExp("\\#=\\s?"+key+"\\s?\\#", "g") , value );
			});
			return string;
		},

		uniqueId: function () {
		  return Math.random().toString(36).substr(2, 9);
		}
	}, // end of fn

	data: {
		DataSource: function DataSource( cfg ) {
			if (!(this instanceof DataSource))
				return new DataSource(cfg);

			var readyDfd = $.Deferred();

			var self = this;
			this.selected = null;
			this.pageSize = cfg.pageSize || 0;
			this.view = [];
			this.items = [];
			this.searchItems = [];
			this.searchActive = false;
			this.itemsIndex = {};
			this.errors = cfg.errors || {};
			this.trySteps = cfg.trySteps || 1;
			this.trySetpsCount = 0;
			this.schema = cfg.schema;
			this.transport = cfg.transport || {};

			this.ready = function()
			{
				return readyDfd.promise();
			};

			this.read = function() {
				return gk.fn.ajax(cfg.transport.read).promise();
			};

			this.getData = function() {
				return cfg.transport.data;
			};

			this.setData = function(data) {
				cfg.transport.data = data;
			}

			this.addSyncStep = function() {
				self.trySetpsCount++;
			};

			this.syncStepAvailable = function() {
				if (self.trySetpsCount < this.trySteps)	
					return true;
				return false;
			};

			this.syncStepAvailableAdd = function(time, func) {
				time = time || 0;

				var ret =  self.syncStepAvailable();
				self.addSyncStep();

				setTimeout(function(){

					if (ret)
						func();

				}, time);
			};

			this.create = function( data ) {
				var create = $.extend(true, {}, cfg.transport.create);
				create.url = create.url;
				create.data = data || {};

				return gk.fn.ajax(create).promise();
			};

			this.update = function( data ) {
				var update = $.extend(true, {}, cfg.transport.update)
				update.url = update.url + '=' + data.id;
				update.data = data || {};
				return gk.fn.ajax(update).promise();
			};

			this.destroy = function( data ) {
				var destroy = $.extend(true, {}, cfg.transport.destroy);
				destroy.url = destroy.url + '=' + data.id;
				destroy.data = data || {};

				return gk.fn.ajax(destroy).promise();
			};

			this.search = function( value, data ) {
				var search = $.extend(true, {}, cfg.transport.search);

				search.url = search.url + '=' + value;
				search.data = data || {};

				return gk.fn.ajax(search).promise();
			};

			this.filter = function( data ) {
				var filter = $.extend(true, {}, cfg.transport.filter);
				filter.url = filter.url;
				filter.data = data || {};

				return gk.fn.ajax(filter).promise();
			};

			this.checkAll = function() {
				var usedItems = (self.searchActive ? self.searchItems : self.items );

				$.each(usedItems, function(i, item){
					item.check();
				});
			};

			this.uncheckAll = function() {
				var usedItems = (self.searchActive ? self.searchItems : self.items );

				$.each(usedItems, function(i, item){
					item.uncheck();
				});
			};

			this.setSelected = function(item) {
				fn.setSelected(item);
			};

			this.getItemByValue = function(name, value) {
				var usedItems = (self.searchActive ? self.searchItems : self.items );

				for (var i = 0; i < usedItems.length; i++) {
					var item = usedItems[i],
						path = 'item.'+name;
					if (typeof eval(path) !== 'undefined') 
					{
						if (eval(path) === value)
							return item;
					}
				}
				return false;
			};

			this.parse = function(func) {
				for(var i = 0; i < self.items.length; i++) {
					func(self.items[i]);
				}
			};

			this.getItems = function() {
				return self.items;
			};

			this.getSelection = function() {
				var selection = [];
				self.parse(function(item){
					if (item.checked == true)
						selection.push(item);
				});
				return selection;
			}

			this.grep = function(func, value) {
				var selection = [];
				self.parse(function(item){
					if (typeof value !== 'undefined') {
						var variable = eval('item.'+value);
						if (func(item) == true && typeof variable !== 'undefined') {
							selection.push(variable);
						}
					} else {
						if (func(item) == true) {
							selection.push(item);
						}
					}
				});
				return selection;
			}

			this.getSelectedEmails = function(func, value) {
				var selection = [];

				for(var i = 0; i < self.items.length; i++) {
					var item = self.items[i];
					if (item.checked)
						selection.push(item.data.email);
				}
				return selection;
			};

			this.getSelectedIds = function(range) {
				range = typeof range !== 'undefined' ? range : false;
				
				var selection = [];

				for(var i = 0; i < self.items.length; i++) {
					var item = self.items[i];
					if (item.checked)
						selection.push(Number(item.data.id));
				}


				selection.sort(function(a, b){
					return a - b;
				});

				
				if (range) {
					var rangeSelection = [],
						set = [];

					for (var i = 0, length = selection.length; i < length; i++) {
						var current = selection[i];

						if ((selection[i + 1] && selection[i + 1] == current + 1)) {
							set.push(current);
						} else {
							if (set.length > 0) {
								var value = String(set[0]) + '-' + String(set[set.length - 1] + 1);
								rangeSelection.push(value);
								set = [];
							} else {
								rangeSelection.push(String(current));
							}
						}
					}

					if (set.length > 0) {
						var value = String(set[0]) + '-' + String(set[set.length - 1] + 1);
						rangeSelection.push(value);
						set = [];
					}

					return rangeSelection;
				}

				return selection;
			};

			this.count = function() {
				return self.items.length;
			};

			var obj = {
				item : {
					data: {},
					checked: false,
					instance: null,
					init : function( data ) {
						var current = $.extend(true,{},this);

						current.instance = $('<tr></tr>');
						current.data = data;
						if( $.type(data[self.schema.model.id]) != 'undefined' )
							current.data.id = data[self.schema.model.id];

						return current;
					},

					destroy: function(status) {
						var current = this,
							returnStatus = false;

						var usedItems = (self.searchActive ? self.searchItems : self.items );

						var index = usedItems.indexOf(current);
						if( index > -1 ) {

							current.instance.remove();
							usedItems.splice(index,1);

							var viewIndex = self.view.indexOf(current);
							if(  viewIndex > -1 ) {
								self.view.splice(viewIndex, 1);
							}

							self.destroy(current.data).done(function(response) {

								if (typeof status === 'string' && typeof response[status] !== 'undefined') {
									response = response[status];
								}

								if( !response ) {
									usedItems.splice(index, 0, current );
									if( viewIndex > -1 )
										self.view.splice(viewIndex, 0, current );

									self.refreshView();
								} else {
									if (self.searchActive) {
										var id = current.data.id;
										if (typeof self.items[self.itemsIndex[id]] !== 'undefined' && self.itemsIndex.hasOwnProperty(id)) {
											self.items[self.itemsIndex[id]].instance.remove();
											self.items.splice(self.itemsIndex[id], 1);
										}
									}
									returnStatus = true;
								}
							});

							self.refreshView();

							fn.runEvent.call( this, 'destroy', current );
							return returnStatus;
						}

						return returnStatus;
					},

					update: function(data) 
					{
						var current = this;
						var dataPost = typeof data !== 'undefined' ? data : current.data;
						return self.update(dataPost).promise();
					},

					select : function( event ) {
						fn.setSelected(event.data.current);
						fn.runEvent.call( this, 'select', self.selected );
					},

					checkToggle: function(event) {
						var current = event.data.current;
						if (current.isChecked()) {
							current.uncheck();
						} else {
							current.check();
						}

						fn.runEvent.call(current, 'checkToggle', current, current.isChecked());
					},

					check: function() {
						var id = this.data.id;

						if (self.searchActive) {
							if (typeof self.items[self.itemsIndex[id]] !== 'undefined' && self.itemsIndex.hasOwnProperty(id)) {
								self.items[self.itemsIndex[id]].checked = true;
							}
						}

						if (!this.checked) {
							//self.dataGrid.footer.writeCheckedInfo(+1);
						}

						this.checked = true;

						if (fn.itemInView(this) && self.dataGrid.checkable ) {
							var ch = this.instance.find('input[type="checkbox"]');
							ch.prop('checked', true);
						}

						fn.runEvent.call(this, 'check', this, true);
						self.dataGrid.footer.writeCheckedInfo(+1);
					},

					uncheck: function() {

						var id = this.data.id;

						if (self.searchActive) {
							if (self.itemsIndex.hasOwnProperty(id)) {
								self.items[self.itemsIndex[id]].checked = false;
							}
						}

						if (this.checked) {
							//self.dataGrid.footer.writeCheckedInfo(-1);
						}

						this.checked = false;

						if (fn.itemInView(this) && self.dataGrid.checkable ) {
							var ch = this.instance.find('input[type="checkbox"]');
							ch.prop('checked', false);
						}

						fn.runEvent.call(this, 'check', this, false);
						self.dataGrid.footer.writeCheckedInfo(-1);
					},

					isChecked: function() {
						return this.checked;
					},

					render: function() {
						var current = this,
							data = current.data,
							itemFields = fn.getItemFields(data);

						if( self.dataGrid.selectable )
							current.instance.on('click',{ current: current }, current.select);

						if( self.dataGrid.checkable )
							current.instance.on('click',{ current: current }, current.checkToggle);

						current.instance.empty();

						$.each(itemFields, function(key, val) {

							var className = ' class="'+( val.className != null ? val.className : '')+'" ',
								template,
								td;

							if( data.hasOwnProperty(val.field) ) {
								current.data[key] = ( data[val.field] != null ? data[val.field] : '');

								template = current.data[key];

								if( $.type( self.dataGrid.template[val.template]) == 'function' )
									template = self.dataGrid.template[val.template]( current, template );

								td = $('<td '+className+'></td>').html(template);
							} else {

								var template = '';
								if( $.type( self.dataGrid.template[val.template]) == 'function' )
									template = self.dataGrid.template[val.template]( current, template );

								td = $('<td '+className+'></td>').html(template);
							}
							current.instance.append(td);
						});

						var prev = current.instance.prev();
						if (prev.length > 0)
							prev.after(current.instance);
						else {
							self.dataGrid.append(current.instance);
							current.addOddAndEven();
						}
					},

					addOddAndEven : function() {
						if( !self.hasOwnProperty('lastClass') )
							self.lastClass = 'odd';
						else
							self.lastClass = self.lastClass == 'odd' ? 'even' : 'odd';

						this.instance.removeClass('even odd');
						this.instance.addClass(self.lastClass);
					},

					removeFromView: function() {
						var current = this;

						var index = self.view.indexOf(current);

						if( index > -1 ) {
							current.instance.remove();
							self.view.splice(index,1);
						}
					},

					removeFromScreen: function() {
						var current = this,
							vIndex = self.view.indexOf(current),
							iIndex = self.items.indexOf(current);

						if (iIndex > -1) {
							self.items.splice(iIndex, 1);
						}

						if (vIndex > -1) {
							current.instance.remove();
							self.view.splice(vIndex, 1);
						}

						self.refreshView();
					},

					hide: function() {
						this.instance.hide();
					},

					show: function() {
						this.instance.show();
					}
				}, // end of item
			}

			var fn = {
				setSelected: function(item) {
					self.selected = item;

					if( !self.hasOwnProperty('prevItemSelected') ) {
						self.prevItemSelected = self.selected;
					} else {
						self.prevItemSelected.instance.removeClass('selected');
						self.prevItemSelected = self.selected;
					}

					self.selected.instance.addClass('selected');

				},

				getItemFields : function( first ) {
					if( $.type(self.itemFieldsDefined) == 'undefined' ) {
						var items = {};
						var columns = self.schema.model.getColumns();

						$.each(first, function(key, val) {
							$.each(columns, function(k, v) {
								if ($.type(val) !== 'null' && $.type(val['field'] !== 'undefined') && $.type(items[v.field]) === 'undefined')
									items[v.field] = v;
							});
						});

						self.itemFieldsDefined = items;
					}
					return self.itemFieldsDefined;
				}, // end of getItemFields

				runEvent: function( event ) {
					var args = Array.prototype.slice.call(arguments, 1);

					if( self.dataGrid.events != null && $.type(self.dataGrid.events[event]) == 'function' )
						self.dataGrid.events[event].apply( this, args );
				},

				initItems : function(data) {
					self.dataGrid.footer.setCheckedInfo(0);

					if( data.length > 0 ) {
						$.each(data, function(key, val) {
							fn.initItem( {key:key, data:val} );
							return;
						});
					}

				}, // end of initItems

				initItem : function( cfg ) {

					var key = cfg.key;
					var data = cfg.data;
					var item = obj.item.init( data );

					var usedItems = (self.searchActive ? self.searchItems : self.items );

					var onIndex = usedItems.push(item) - 1;

					if (!self.searchActive) {
						self.itemsIndex[item.data.id] = onIndex;
					}

					if (typeof self.dataGrid.defineSelected === 'function') {
						if (self.dataGrid.defineSelected(item)) {
							fn.setSelected(item);
						}
					}

					if (self.itemsIndex.hasOwnProperty(item.data.id)) {
						if (typeof self.items[self.itemsIndex[item.data.id]] !== 'undefined' && self.items[self.itemsIndex[item.data.id]].checked) {
							item.checked = true;
						} else {
							item.checked = false;
						}
					}

					var start = (self.dataGrid.currentPage - 1) * self.pageSize;

					if( self.dataGrid.pageable == false || self.pageSize == 0 || ( self.view.length < self.pageSize && key >= start ) ) {
						self.view.push(item);
					}
				},

				displayItems : function( data ) {

					$.each(self.view, function(index, item) {
						 item.render();
					});
					self.dataGrid.updateFooter();

					self.dfdDone.resolve(self);
				}, // end of displayItems

				clearView : function() {
					var len = self.view.length;
					while( len-- ) {
						self.view[len].removeFromView();
						self.view.splice(len,1);
					}
				}, // end of clearView

				setView : function( currentPage ) {
					fn.clearView();
					delete self.lastClass;	

					if( currentPage < 1 ) {
						currentPage = 1;
					} else if ( currentPage > self.getPageInfo().lastPage() ) {
						currentPage = self.getPageInfo().lastPage();
					}

					var start = ( currentPage - 1 )  * self.pageSize;
					var end = start + self.pageSize;

					var usedItems = (self.searchActive ? self.searchItems : self.items );

					self.view = usedItems.filter(function (item, key) { 
						if( key >= start && key < end ) {
							item.render();
							return true;
						}
					});

					self.dataGrid.footer.setCurrentPage( currentPage );	

					self.dataGrid.updateFooter();
				}, // end of setView

				getItemById: function( id ) {
					var item;

					var usedItems = (self.searchActive ? self.searchItems : self.items );

					var itemArray = $.grep(usedItems, function(itm, index){
							if (itm.data.id == id)
								return true;
							return false;
						});

					if (itemArray.length > 0) {
						return itemArray[0];
					}
					return false;
				},

				itemInView: function( item ) {
					var index = self.view.indexOf(item);
					if (index > -1)
						return true;
					return false;
				}
			};

			self.dfdDone = new $.Deferred();
			// initItemsDone
			this.sync = function(syncDone) {
				if (cfg.transport.hasOwnProperty('read')) 
				{
					self.read().done(function(data) {
						if( self.items.length == 0 ) 
						{
							fn.initItems( data );
							fn.displayItems();
						} 
						else 
						{
							self.items = [];
							this.itemsIndex = {};
							fn.clearView();
							self.lastClass = null;
							fn.initItems( data );
							fn.displayItems();
						}

						if (typeof syncDone === 'function')
							syncDone.call(self, self);

						readyDfd.resolve();						
					}).fail(function(xhr, ajaxOptions, thrownError){

						if (typeof self.errors.read === 'function')
							self.errors.read(xhr, ajaxOptions, thrownError);
					});
				}
				else
				{
					var data = self.getData();

					if( self.items.length == 0 ) 
					{
						fn.initItems( data );
						fn.displayItems();
					} 
					else 
					{
						self.items = [];
						this.itemsIndex = {};
						fn.clearView();
						self.lastClass = null;
						fn.initItems( data );
						fn.displayItems();
					}

					if (typeof syncDone === 'function')
						syncDone.call(self, self);

					readyDfd.resolve();
				}

				return self.dfdDone.promise();
			};

			this.applySearch = function(data) {
				self.searchItems = [];
				self.searchActive = true;
				self.dataGrid.currentPage = 1;
				fn.clearView();
				self.lastClass = null;
				fn.initItems( data );
				fn.displayItems();
			};

			this.clearSearch = function() {
				self.searchItems = [];
				self.searchActive = false;
				self.setView(1);
			};

			this.syncDataById = function(id, data) {
				data['id'] = id;
				var item = fn.getItemById(id);
				if (item) {
					item.data = data;
					if (fn.itemInView(item)) {
						item.render();
					}
				}
			};

			this.getItemById = function(id) {
				return fn.getItemById(id);
			};

			this.refreshView = function() {
				fn.setView( self.dataGrid.currentPage );
			},

			this.setView = function( currentPage ) {
				fn.setView( currentPage );
			}

			this.getPageInfo  = function() {
				return {
					lastPage : function () 
					{ 
						if( self.pageSize == 0 )
							return 1;
						else {
							var usedItems = (self.searchActive ? self.searchItems : self.items );
							return Math.ceil(usedItems.length / self.pageSize) 
						}
					},
					countIntems : function() 
					{
						var usedItems = (self.searchActive ? self.searchItems : self.items );
						return usedItems.length;
					},
					start : function() 
					{
						var start = (self.dataGrid.currentPage - 1) * self.pageSize + 1;
							if (start < 0)
								start = 0;
						return start;
					},
					end : function() 
					{
						var end = this.start() + self.pageSize;
						if( end >= this.countIntems() )
							return this.countIntems()
						else
							return end;
					}, 
				}
			}

			return this;
		},

		Model: function( cfg ) {
			var self = this;

			this.id      = cfg.id || null;
			this.fields  = cfg.fields || [];
			this.columns = [];

			$.each(cfg, function(key, val) {
				self.fields[key] = val;
			});

			this.getFields = function() {
				return self.fields;
			};

			this.getColumns = function() {
				return self.columns;
			};
			return this;
		}
	}
}

$.fn.gkDropDownMenu = function( cfg ) {

	var self = $.extend(true, {

		vars: {
			title: cfg.title || null,
			name: cfg.name || '',
			className: cfg.className  || null,
			css: cfg.css || null,
			attr: cfg.attr || null,
			data: cfg.data || [],
			checkall: cfg.checkall || true,
			change: cfg.change || null,
		},

		setup: function() {

			var selector = self.fn.getSelector();
			var id       = self.fn.getId();

			if( self.length == 0 ) {

				self = '';
				self += '<div class="gk-dropdown-menu-div" tabindex="-1" style="outline: none;">';
				self += 	'<a href="javascript:{}" class="btn btn-default gk-dropdown-menu-button" style="display:block;"><input type="checkbox" class="menu-checkall">'+this.vars.title+'<i class="icon icon-caret-down down-arrorw"></i></a>';
				self += 	'<div class="gk-dropdown-menu-table-div" style="display: none;">';
				self += 	'<table class="gk-dropdown-menu-table">';

				function createItem(item) {

					var obj = ({
						instance: null,
						data: item,
						init: function() {
							this.instance = this.getTemplate(item);
							this.instance.checkbox = this.instance.find('input[type="checkbox"].item-checkbox');

							this.addEvents();
							return this;
						},

						getTemplate: function(item) {
							var activeClass = '';
							if (typeof cfg.activeClass !== 'undefined')
							{
								if (item.hasOwnProperty('active'))
								{
									if (parseInt(item.active) == 1)
										activeClass = cfg.activeClass.enable;
									else
										activeClass = cfg.activeClass.disable;

								}
							}

							var tpl  = '';
								tpl +=	'<tr>';
								tpl += '<td class="gk-m-checkbox"><input type="checkbox" value="'+item.value+'" class="item-checkbox"/></td>';
								tpl += '<td class="gk-m-value"><a href="javascript:{}" class="'+(activeClass)+'">'+item.title+'</a></td>';
								tpl += '</tr>';
								tpl = $(tpl);
							return tpl;
						},

						addEvents: function() {
							var that = this,
								instance = that.instance,
								checkbox = that.instance.checkbox;

							function toggleCheckbox() {
								if (checkbox.is(':checked')) {
									checkbox.prop('checked', false);
								} else {
									checkbox.prop('checked', true);
								}
							}

							instance.on('click', function(e){
								toggleCheckbox();
								self.fn.change();
							});

							checkbox.on('click', function(e){
								e.stopPropagation();
								self.fn.change();
							});

						}

					}.init());

					self.items = self.items || [];
					self.items.push(obj);
					return obj.instance;
				}

				function createItems(data, appendTarget) {
					if (data.length) {
						$.each(data, function(i, item){
							appendTarget.append(createItem(item));
						});
					}
				}

				self +=	'</table>';
				self +=		'</div>';
				self += 	'<div class="clear">&nbsp;</div>';
				self += '</div>';
				self = $(self);

				// Declare all variables 
				self.vars = this.vars;
				self.fn = this.fn;
				self.obj = this.obj;
				self.button = self.find('.gk-dropdown-menu-button');
				self.menu = self.find('.gk-dropdown-menu-table-div');
				self.table = self.find('.gk-dropdown-menu-table');
				self.checkall = self.find('input[type="checkbox"].menu-checkall');
				self.items = [];

				createItems(this.vars.data, self.table);

				self.hitTest = false;
			}

			if( selector.type == 'id' ) {
				self.attr('id', selector.name + id);
			} else if( selector.type == 'class' ) {
				self.attr('class', selector.name + id);
			}

			self.addClass('gk-dropdown-menu');

			if( self.vars.className != null )
				self.addClass(self.vars.className);

			if( self.vars.css != null )
				self.css(self.vars.css);

			if( self.vars.attr != null )
				self.attr(self.vars.attr);

			self.toggleMenu = function() {
				if (self.menu.is(':visible'))
					self.menu.hide();
				else
					self.menu.show();
			};

			self.showMenu = function() {
				self.menu.show();
			};

			self.hideMenu = function() {
				self.menu.hide();
			};

			self.getSelected = function() {
				return self.fn.getSelected();
			}

			self.select = function(ids) 
			{
				return self.fn.select(ids);
			};

			self.mark = function(ids) 
			{
				return self.fn.mark.call(this, ids);
			};

			self.uncheckAll = function() {
				return self.fn.uncheckAll();
			}

			self.fn.addEvents();
			return self;
		},

		obj: {}, // end of obj

		fn: {
			addEvents: function() {

				self.button.on('click', function(event) {
					event.preventDefault();
					event.stopPropagation();

					self.focus();
					self.toggleMenu();

				});

				self.on('focusout', function(e) {
					if (self.hitTest == false )
						self.hideMenu();
				});

				self.on('mouseleave', function() {
					self.hitTest = false;
				});

				self.on('mouseenter', function() {
					self.hitTest = true;
				});

				self.checkall.on('click', function(e) {
					e.stopPropagation();
				});

				self.checkall.on('change', function(){
					$.each(self.items, function(i, item){
						if (self.checkall.is(':checked')) {
							item.instance.checkbox.prop('checked', true);
						} else {
							item.instance.checkbox.prop('checked', false);
						}
					});
					self.fn.change();
				});

			},

			change: function() {
				if (self.vars.change != null) {
					var selected = self.fn.getSelected();

					if (selected.length == 0) {
						self.checkall.prop('checked', false);
					} else {
						self.checkall.prop('checked', true);
					}

					self.vars.change(selected);
				}
			},

			getSelected: function() {
				var arr = [];
				$.each(self.items, function(i,item){
					var checkbox = item.instance.checkbox;
					if (checkbox.is(':checked'))
						arr.push(checkbox.val());
				});
				return arr;
			},

			select: function(ids)
			{
				var idsNumber = [];
				for(var i = 0; i < ids.length; i++)
					idsNumber.push(Number(ids[i]));

				$.each(self.items, function(i, item){
					var checkbox = item.instance.checkbox;
					if (idsNumber.indexOf(Number(item.data.value)) != -1  && !checkbox.is(':checked'))
						item.instance.click();
				});
			},

			mark: function(ids)
			{
				var self = this,
					idsIsStrimg = false,
					idsNumber = [];
					idsStrimg = [];

				if (ids.length > 0)
				{
					var first = isNaN(Number(ids[0]));
					idsIsStrimg = true;
				}

				if (idsIsStrimg)
				{
					for(var i = 0; i < ids.length; i++)
						idsStrimg.push(ids[i]);
				}
				else
				{
					for(var i = 0; i < ids.length; i++)
						idsNumber.push(Number(ids[i]));
				}

				$.each(self.items, function(i, item){
					var checkbox = item.instance.checkbox;

					if (idsIsStrimg)
					{
						if (idsStrimg.indexOf(item.data.value) != -1  && !checkbox.is(':checked'))
						{
							item.instance.checkbox.prop('checked', true);
							self.checkall.prop('checked', true);
						}
					}
					else
					{
						if (idsNumber.indexOf(Number(item.data.value)) != -1  && !checkbox.is(':checked'))
						{
							item.instance.checkbox.prop('checked', true);
							self.checkall.prop('checked', true);
						}
					}
				});
			},

			uncheckAll: function() 
			{
				self.checkall.prop('checked', false);

				$.each(self.items, function(i,item){
					var checkbox = item.instance.checkbox;
					checkbox.prop('checked', false);
				});
			},

			getSelector: function() {
				var name = self.selector;
				var type = false;
				if( /^\#/.test(name) ) {
					type = 'id';
					name = name.slice(1, name.length );
				} else if( /^\./.test( name ) ) {
					type = 'class';
					name = name.slice(1, name.length );
				}
				return {name: name, type: type};
			},

			getId: function() {
				var id = '';
				if( self.vars.item != null )
					id = '_' + self.vars.item.data.id;
				else
					id = '_' + gk.fn.uniqueId();
				return id;
			},

		}, // end of fn

	}, this);

	return self.setup();
}

function gkWindow(cfg) 
{
	if (!(this instanceof gkWindow))
		return new gkWindow(cfg);

	var self = this;
		task = NewsletterPro.modules.task,
		l = NewsletterPro.translations.l(NewsletterPro.translations.modules.task);

	function setTemplate() 
	{
		var title = cfg.title || '';

		var background = $('<div class="gk-background"></div>'),
			template = $('<div><div class="gk-header"><span class="gk-title"></span><a href="javascript:{}" class="gk-close"><i class="icon icon-remove"></i></a></div><div class="bootstrap gk-content"></div></div>'),
			body = $('body'),
			width = cfg.width || 0,
			height = cfg.height || 0;

		background.css({
			width: '100%',
			height: '100%',
			top: 0,
			left: 0,
			position: 'fixed',
			display: 'none',
			'z-index': '9999',
		});

		background.appendTo(body);

		template.css({
			width: cfg.width || 'auto',
			height: cfg.height || 'auto',
			position: 'fixed',
			left: width != 0 ? body.width() / 2 - width / 2 : body.width() / 2,
			top: height != 0 ? $(window).height() / 2 - height / 2 : $(window).height() / 2,
			display: 'none',
			'z-index': '99999',
		});

		template.header = template.find('.gk-header');
		template.title = template.find('.gk-title');
		if (title) {
			template.title.html(title);
		}
		template.content = template.find('.gk-content');
		var content = ( typeof cfg.content === 'function' ? cfg.content(self, template.content) : '' );
		if (content) 
		{
			template.content.html(content);
		}
		template.close = template.find('.gk-close');
		template.background = background;

		template.addClass('gk-task-window');
		template.addClass(cfg.className);
		background.addClass(cfg.className+'-'+'background');

		template.appendTo(body);

		addEvents(template, background);

		if (typeof cfg.getTemplate === 'function')
			cfg.getTemplate(template);

		if (typeof cfg.setScrollContent !== 'undefined')
		{
			template.content.css({
				'height': cfg.setScrollContent + 'px',
				'overflow-y': 'scroll',
				'overflow-x': 'hidden',
			});
		}

		return template;
	}

	function addEvents(template, background) {

		template.close.on('click', function(event) {
			self.hide();
			if (typeof cfg.close === 'function') {
				cfg.close(self);
			}
		});

		background.on('click', function(){
			template.close.trigger('click');
		});

		$(window).resize(function(event) {
			self.resetPosition();
		});
	}

	this.template = setTemplate();

	this.resetPosition = function() {
		self.template.css({
			left: $('body').width() / 2 - self.template.width() / 2,
			top: $(window).height() / 2 - self.template.height() / 2,
		});
	}

	this.setHeader = function(value) {
		this.template.header.find('.gk-title').html(value);
	};

	this.getHeader = function()
	{
		return this.template.header.find('.gk-title').html();
	};

	this.setContent = function(value) {
		this.template.content.html(value);
	};

	this.getContent = function() {
		return this.template.content;
	};

	this.hide = function() {
		this.template.fadeOut(200);
		return this.template.background.fadeOut(200).promise();
	};

	this.show = function(content) 
	{
		this.resetPosition();
		this.template.fadeIn(200);

		if (typeof cfg.show === 'function')
			cfg.show(self);

		if (typeof content !== 'undefined')
			this.setContent(content);

		return this.template.background.fadeIn(200).promise();
	};

	return this;
}
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

function gkSlider(cfg)
{
	if (!(this instanceof gkSlider))
		return new gkSlider(cfg);

	var self           = this,
		snap           = null,
		min            = null,
		max            = null,
		step           = null,
		position       = null,
		value          = null,
		drabable       = false,
		target         = null,
		move           = null,
		start          = null,
		done           = null,
		values         = [],
		remplaceValues = {},
		mouseSpeed     = 0,
		lastMouseX     = -1,
		prefix         = '',
		currentValue   = 0,
		dom = {};

	init();

	// global 
	self.dom    = dom;

	self.getValue = function () 
	{
		return getValue();
	};

	self.setValue = function (value) 
	{
		setValue(value);
	};

	self.refresh = function() 
	{
		return refresh();
	};

	self.show = function()
	{
		return show();
	};

	self.hide = function()
	{
		return hide();
	};

	// private 
	function init () 
	{
		initDom();
		initConfig();
		initCss();
		initEvents();
		refresh();
	}

	function initConfig()
	{
		snap           = cfg.snap || false;
		min            = parseInt(cfg.min)   || 0;
		max            = cfg.max || parseInt(dom.sliderBar.width() );
		step           = parseInt(cfg.step)  || 1;
		value          = cfg.value || 0;
		prefix         = cfg.prefix || '';
		values         = cfg.values || [];
		remplaceValues = cfg.remplaceValues || {};
		move           = cfg.move;
		start          = cfg.start;
		done           = cfg.done;
	}

	function initDom()
	{
		addDom('target', cfg.target);
		addDom('instance', getTemplate(cfg));
		addDom('sliderController', [dom.instance, '.slider-controller']);
		addDom('sliderBar', [dom.instance, '.slider-bar']);
		addDom('rulesBar', [dom.instance, '.rules-bar']);
		addDom('sliderInfo', getInfoTemplate());
		addDom('sliderInfoInput', [dom.sliderInfo, '.slider-value']);

		dom.sliderBar.prepend(dom.sliderInfo);
		dom.target.empty();
		dom.target.append(dom.instance);
	}

	function initCss()
	{
		dom.rulesBar.css( 'width', rulesBarWidth());
		dom.sliderInfo.css({'position' : 'absolute'});
		dom.instance.css({'position': 'relative', });
		dom.sliderController.css({'position': 'absolute'});
		dom.sliderBar.css({'position': 'absolute'});
		dom.rulesBar.css({'position': 'absolute'});
	}

	function initEvents()
	{
		dom.sliderController.on('mousedown', function(event) {
			startDrag();
			return false;
		});

		dom.sliderController.on('mouseup', function(event) {
			stopDrag();
			return false;
		});

		onMouseUp(function(event){
			if ( drabable == true )
				stopDrag();
			return false;
		});
	}

	function initRules()
	{
		dom.rulesBar.empty();	
		addRulesQuick(values);
	}

	function refreshRules()
	{
		initRules();
	}

	function addDom(name, data)
	{
		if (Object.prototype.toString.call( data ) === '[object Array]')
			dom[name] = data[0].find(data[1]);
		else
			dom[name] = data;
	}

	function rulesBarWidth()
	{
		return ((sliderWidth() / sliderParentWidth()) * 100) + '%';
	}

	function sliderParentWidth()
	{
		return dom.sliderBar.parent().width();
	}

	function getInfoTemplate()
	{
		var template  = '<span class="slider-value-container" style="left:'+(getValueToPosition(value))+'">';
			template += '<span class="slider-value">'+(getInfo())+'</span>';
			template += '</span>';

		return $(template);
	}

	function getTemplate(settings) 
	{
		var id = cfg.id || '',
			className = (typeof cfg.className === 'undefined') ? '' : (typeof cfg.className === 'array' ? cfg.className.join(' ') : cfg.className),
			slider;

		slider  = '<div id="'+id+'" class="slider style-dark '+className+'">';
		slider += '<div class="slider-bar">';
		slider += '<a href="javascript:{}" class="slider-controller">&nbsp;</a>';
		slider += '</div>';
		slider += '<div class="rules-bar">&nbsp;</div>';
		slider += '</div>';

		slider = $(slider);

		slider.css({
			'margin-top': '28px',
			'margin-bottom': '34px',
		});

		return slider;
	}

	function getPercent() 
	{
		var percent = 0;
		if ( position != 0 )
			percent = (( position / sliderBar.width()) ) * 100  ;
		return percent;
	}

	function setPosition(poz) 
	{
		position = poz;
		setLimits();

		value = getPositionToValue();
		setElementsPosition();

		if ( snap && mouseSpeed < 5) 
		{

			$.each(values, function(index, s) {
				if( getValue() > (s - snap) && getValue() < (s + snap) ) 
				{
					snapAtValue(s);
				}
			});
		}
	}

	function snapAtValue(val)
	{
		value = val;
		position = getValueToPosition();
		setElementsPosition();
	}

	function setLimits()
	{
		if ( position < 0 ) 
			position = 0;
		else if(position > sliderWidth()) 
			position = sliderWidth();
	}

	function controllerWidth()
	{
		return dom.sliderController.width();
	}

	function getControllerX()
	{
		return ( getLeft() / sliderWidth() ) * 100;
	}

	function getLeft()
	{
		return position * ((sliderWidth() - controllerWidth()) / sliderWidth());
	}

	function getInfoLeft()
	{
		return getLeft() - (sliderInfoWidth()/2) + (controllerWidth()/2) - 1;
	}

	function sliderInfoWidth()
	{
		return dom.sliderInfo.width();
	}

	function getInfoX()
	{
		return ( getInfoLeft() / dom.sliderBar.width() ) * 100;
	}

	function setElementsPosition()
	{
		dom.sliderController.css({'left' : getControllerX() + '%'});
		dom.sliderInfo.css({'left': getInfoX() + '%' } );
		writeInfo(getValue());
	}

	function writeInfo(value)
	{
		dom.sliderInfoInput.text(value + prefix);
	}

	function getInfo()
	{
		return value + prefix;
	}

	function getRange()
	{
		return (max - min); 
	}

	function getPositionToValue()
	{
		return Math.round( convertPositionToValue(position) );
	}

	function convertPositionToValue(position)
	{
		return (getRange() * position / sliderWidth()) + min;
	}

	function getValueToPosition()
	{
		return convertValueToPosition(value) - convertValueToPosition(min);
	}

	function convertValueToPosition(value)
	{
		return (sliderWidth() *  value / getRange());
	}

	function setValue (val)
	{
		value = val;

		setPosition(getValueToPosition());

		if (cfg.hasOwnProperty('onSetValue'))
			cfg.onSetValue(value, self);
	}

	function refresh() 
	{
		setPosition(getValueToPosition());
		refreshRules();
	}

	function show()
	{
		dom.target.parent().show();
		dom.instance.show();
		refresh();
	}

	function hide()
	{
		dom.target.parent().hide();
	}

	function offsetLeft()
	{
		return dom.sliderBar.offset().left;
	}

	function offsetRight()
	{
		return dom.sliderBar.offset().right;
	}

	function sliderWidth()
	{
		return dom.sliderBar.width();
	}

	function onMouseMove(func)
	{
		$(window).on('mousemove', function(event) {
			func(event);
		});	
	}

	function onMouseUp(func)
	{
		 $(window).on('mouseup', function(event) {
		 	func(event);
		 });
	}

	function startDrag() 
	{
		drabable = true;

		onMouseMove(function(event){
			setPosition(-offsetLeft() + event.pageX);

			if( move != null )
				move( self );

			if( lastMouseX > -1 )
				mouseSpeed = Math.abs(event.pageX - lastMouseX);

			lastMouseX = event.pageX;
		});

		if( start != null )
			start( self );
	}

	function stopDrag() 
	{
		drabable = false;
		$(window).unbind('mousemove');
		if( done != null )
			done( self );
	}

	function getValue()  
	{
		return value;
	}

	function addRule( value ) 
	{
		if( value < min )
			value = min;
		else if( value > max )
			value = max;

		var ctrlWidth = controllerWidth();
		var strlWidth = sliderWidth();
		// position in pixel's 
		var position = (( (strlWidth - ctrlWidth) / ( max - min) ) * (value - min) ) + (ctrlWidth/2) ;
		// position in percent's 
		position = (position / strlWidth ) * 100;

		var template = [
			'<div class="rule-line-'+value+'">',
			'<span class="rule" style="left: '+position+'%;"></span>',
			'<span class="rule-value" style="left: '+position+'%;">'+getValueReplacement(value)+'</span>',
			'</div>'
		];

		template = $(template.join(''));

		var ruleInfo = template.find('.rule-value');

		dom.rulesBar.append(template);
		var left = (position - ( (ruleInfo.width()/2) /  strlWidth ) * 100) + '%';
		ruleInfo.css({'left' : left});
	}

	function addRulesQuick(values)
	{
		var html = [];
		var ctrlWidth = controllerWidth();
		var strlWidth = sliderWidth();
		for (var i in values)
		{
			var value = values[i];

			if( value < min )
				value = min;
			else if( value > max )
				value = max;

			// position in pixel's 
			var position = (( (strlWidth - ctrlWidth) / ( max - min) ) * (value - min) ) + (ctrlWidth/2) ;
			// position in percent's 
			var positionPercent = (position / strlWidth ) * 100;
			var positionPercentCorrection = (( typeof cfg.corectPosition !== 'undefined' ? position + cfg.corectPosition : position ) / strlWidth ) * 100;

			var template = [
				'<div class="rule-line-'+value+'">',
				'<span class="rule" style="left: '+positionPercent+'%;"></span>',
				'<span class="rule-value" style="left: '+positionPercentCorrection+'%;">'+getValueReplacement(value)+'</span>',
				'</div>'
			];

			html.push(template.join(''));
		}

		dom.rulesBar.html(html.join(''));
	}

	function getValueReplacement(value) 
	{
		if (remplaceValues.hasOwnProperty(value))
			return remplaceValues[value];
		return value;
	}

	return this;
}

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

function gkSliderRange(cfg)
{
	if (!(this instanceof gkSliderRange))
		return new gkSliderRange(cfg);

	var self           = this,
		snap           = null,
		min            = null,
		max            = null,
		step           = null,
		positionMin    = null,
		positionMax    = null,
		valueMin       = null,
		valueMax       = null,
		drabable       = false,
		target         = null,
		move           = null,
		start          = null,
		done           = null,
		values         = [],
		remplaceValues = {},
		mouseSpeed     = 0,
		lastMouseX     = -1,
		prefix         = '',
		currentValue   = 0,
		current     = null,
		editable = typeof cfg.editable !== 'undefined' ? cfg.editable : false,
		dom = {};

	init();

	// global 
	self.dom    = dom;

	self.getValueMin = function () 
	{
		return getValueMin();
	};

	self.getValueMax = function () 
	{
		return getValueMax();
	};

	self.setValueMin = function (value) 
	{
		setValueMin(value);
	};

	self.setValueMax = function (value) 
	{
		setValueMax(value);
	};

	self.refresh = function() 
	{
		return refresh();
	};

	self.reset = function(config)
	{
		return reset(config);
	};

	self.show = function()
	{
		return show();
	};

	// private 
	function init () 
	{
		initDom();
		initConfig();
		initCss();
		initEvents();
		refresh();
	}

	function initConfig()
	{
		snap           = cfg.snap || false;
		min            = parseInt(cfg.min)   || 0;
		max            = cfg.max || parseInt(dom.sliderBar.width());
		step           = parseInt(cfg.step)  || 1;
		valueMin       = cfg.valueMin || 0;
		valueMax       = cfg.valueMax || 0;
		prefix         = cfg.prefix || '';
		values         = cfg.values || [];
		remplaceValues = cfg.remplaceValues || {};
		move           = cfg.move;
		start          = cfg.start;
		done           = cfg.done;
	}

	function initDom()
	{
		addDom('target', cfg.target);
		addDom('instance', getTemplate(cfg));
		addDom('sliderController', [dom.instance, '.slider-controller']);
		addDom('sliderControllerMax', [dom.instance, '.slider-controller-max']);
		addDom('sliderBar', [dom.instance, '.slider-bar']);
		addDom('rulesBar', [dom.instance, '.rules-bar']);

		addDom('sliderInfo', getInfoTemplate());
		addDom('sliderInfoMax', getInfoTemplate('max'));

		addDom('sliderInfoInput', [dom.sliderInfo, '.slider-value']);
		addDom('sliderInfoInputMax', [dom.sliderInfoMax, '.slider-value-max']);
		addDom('sliderRange', $('<div class="slider-range"></div>'));

		dom.sliderBar.prepend(dom.sliderInfo);
		dom.sliderBar.prepend(dom.sliderInfoMax);
		dom.sliderBar.prepend(dom.sliderRange);

		dom.target.append(dom.instance);
	}

	function initCss()
	{
		dom.rulesBar.css( 'width', rulesBarWidth());
		dom.sliderInfo.css({'position' : 'absolute'});
		dom.sliderInfoMax.css({'position' : 'absolute'});
		dom.instance.css({'position': 'relative', });
		dom.sliderController.css({'position': 'absolute'});
		dom.sliderControllerMax.css({'position': 'absolute'});
		dom.sliderBar.css({'position': 'absolute'});
		dom.rulesBar.css({'position': 'absolute'});
		dom.sliderRange.css({'position': 'absolute'});
	}

	function initEvents()
	{
		dom.sliderController.on('mousedown', function(event) {
			current = {
				controller: dom.sliderController,
				info: dom.sliderInfo,
				infoInput: dom.sliderInfoInput,
				value: valueMin,
				position: positionMin,
			};

			startDrag('min');
			return false;
		});

		dom.sliderController.on('mouseup', function(event) {
			stopDrag();
			return false;
		});

		dom.sliderControllerMax.on('mousedown', function(event) {
			current = {
				controller: dom.sliderControllerMax,
				info: dom.sliderInfoMax,
				infoInput: dom.sliderInfoInputMax,
				value: valueMin,
				position: positionMax,
			};
			startDrag('max');
			return false;
		});

		dom.sliderControllerMax.on('mouseup', function(event) {
			stopDrag();
			return false;
		});

		onMouseUp(function(event){
			if ( drabable == true )
				stopDrag();
			return false;
		});
	}

	function initRules()
	{
		dom.rulesBar.empty();

		$.each(values, function(index, val) {
			addRule( parseInt(val) );
		});
	}

	function addDom(name, data)
	{
		if (Object.prototype.toString.call( data ) === '[object Array]')
			dom[name] = data[0].find(data[1]);
		else
			dom[name] = data;
	}

	function rulesBarWidth()
	{
		return ((sliderWidth() / sliderParentWidth()) * 100) + '%';
	}

	function sliderParentWidth()
	{
		return dom.sliderBar.parent().width();
	}

	function getInfoTemplate(type)
	{
		type = type || 'min';

		var getvtopmin = getValueToPositionMin(valueMin),
			getvtomax = getValueToPositionMax(valueMax);

		if (isNaN(getvtopmin)) {
			getvtopmin = 0;
		}

		if (isNaN(getvtomax)) {
			getvtomax = 0;
		}

		var template = '';
		switch(type)
		{
			case 'min':

				template += '<span class="slider-value-container" style="left:'+ getvtopmin +'">';
				
				if (editable) {
					template += '<input type="text" class="slider-value np-slider-range-input-text" value="' + getInfoMin() + '">';	
				} else {
					template += '<span class="slider-value">'+(getInfoMin())+'</span>';
				}

				template += '</span>';

			break;

			case 'max':
				template += '<span class="slider-value-container-max" style="left:'+ getvtomax +'">';

				if (editable) {
					template += '<input type="text" class="slider-value-max np-slider-range-input-text" value="' + getInfoMax() + '">';	
				} else {
					template += '<span class="slider-value-max">'+(getInfoMax())+'</span>';
				}
				template += '</span>';

			break;
		}

		var tpl = $(template);

		if (editable) {
			tpl.find('input.slider-value').on('change', function() {
				var val = $(this).val();
				setValueMin(val);
			});

			tpl.find('input.slider-value-max').on('change', function() {
				var val = $(this).val();
				setValueMax(val);
			});
		}

		return tpl;
	}

	function getTemplate(settings) 
	{
		var id = cfg.id || '',
			className = (typeof cfg.className === 'undefined') ? '' : (typeof cfg.className === 'array' ? cfg.className.join(' ') : cfg.className),
			slider;

		slider  = '<div id="'+id+'" class="slider style-dark '+className+'">';
		slider += '<div class="slider-bar">';
		slider += '<a href="javascript:{}" class="slider-controller">&nbsp;</a>';
		slider += '<a href="javascript:{}" class="slider-controller-max">&nbsp;</a>';
		slider += '</div>';
		slider += '<div class="rules-bar">&nbsp;</div>';
		slider += '</div>';

		slider = $(slider);

		slider.css({
			'margin-top': '28px',
			'margin-bottom': '34px',
		});

		return slider;
	}

	function getPercentMin() 
	{
		var percent = 0;
		if ( positionMin != 0 )
			percent = (( positionMin / sliderBar.width()) ) * 100  ;
		return percent;
	}

	function getPercentMax() 
	{
		var percent = 0;
		if ( positionMax != 0 )
			percent = (( positionMax / sliderBar.width()) ) * 100  ;
		return percent;
	}

	function setPositionMin(poz) 
	{
		positionMin = poz;
		setLimitsMin();

		valueMin = getPositionToValueMin();

		setElementsPositionMin();

		if ( snap && mouseSpeed < 5) 
		{
			$.each(values, function(index, s) {
				if( getValueMin() > (s - snap) && getValueMin() < (s + snap) ) 
				{
					snapAtValueMin(s);
				}
			});
		}
	}

	function setPositionMax(poz) 
	{
		positionMax = poz;
		setLimitsMax();

		valueMax = getPositionToValueMax();

		setElementsPositionMax();

		if ( snap && mouseSpeed < 5) 
		{
			$.each(values, function(index, s) {
				if( getValueMax() > (s - snap) && getValueMax() < (s + snap) ) 
				{
					snapAtValueMax(s);
				}
			});
		}
	}

	function snapAtValueMin(val)
	{
		valueMin = val;
		positionMin = getValueToPositionMin();
		setElementsPositionMin();
	}

	function snapAtValueMax(val)
	{
		valueMax = val;
		positionMax = getValueToPositionMax();
		setElementsPositionMax();
	}

	function setLimitsMin()
	{
		if ( positionMin < 0 ) 
		{
			positionMin = 0;
		}
		else if(positionMin > sliderWidth() && !checkLimits()) 
		{

			positionMin = sliderWidth();
		}
		else if (checkLimits())
		{
			positionMin = positionMax;
		}
	}

	function checkLimits()
	{
		return (valueMax <= valueMin && positionMax <= positionMin);
	}

	function setLimitsMax()
	{
		if ( positionMax < 0 && !checkLimits()) 
		{
			positionMax = 0;
		}
		else if(positionMax > sliderWidth())
		{
			positionMax = sliderWidth();
		}
		else if (checkLimits())
		{
			positionMax = positionMin;
		}
	}

	function controllerWidth()
	{
		return dom.sliderController.width();
	}

	function getControllerXMin()
	{
		return ( getLeftMin() / sliderWidth() ) * 100;
	}

	function getControllerXMax()
	{
		return ( getLeftMax() / sliderWidth() ) * 100;
	}

	function getLeftMin()
	{
		return positionMin * ((sliderWidth() - controllerWidth()) / sliderWidth());
	}

	function getLeftMax()
	{
		return positionMax * ((sliderWidth() - controllerWidth()) / sliderWidth());
	}

	function getInfoLeftMin()
	{
		return getLeftMin() - (sliderInfoWidth()/2) + (controllerWidth()/2) - 1;
	}

	function getInfoLeftMax()
	{
		return getLeftMax() - (sliderInfoWidth()/2) + (controllerWidth()/2) - 1;
	}

	function sliderInfoWidth()
	{
		return dom.sliderInfo.width();
	}

	function getInfoXMin()
	{
		return ( getInfoLeftMin() / dom.sliderBar.width() ) * 100;
	}

	function getInfoXMax()
	{
		return ( getInfoLeftMax() / dom.sliderBar.width() ) * 100;
	}

	function getControllerDif()
	{
		return getControllerXMax() - getControllerXMin();
	}

	function setElementsPositionMin()
	{
		dom.sliderController.css({'left' : getControllerXMin() + '%'});
		dom.sliderInfo.css({'left': getInfoXMin() + '%' } );

		dom.sliderRange.css({'left': getControllerXMin() + '%' } );
		dom.sliderRange.css({'width':  getControllerDif() + '%' } );

		writeInfoMin(getValueMin());
	}

	function setElementsPositionMax()
	{
		dom.sliderControllerMax.css({'left' : getControllerXMax() + '%'});
		dom.sliderInfoMax.css({'left': getInfoXMax() + '%' } );

		dom.sliderRange.css({'left': getControllerXMin() + '%' } );
		dom.sliderRange.css({'width':  getControllerDif() + '%' } );

		writeInfoMax(getValueMax());

	}

	function isCurrent()
	{
		if (current != null)
			return true;
		return false;
	}

	function writeInfoMin(value)
	{
		if (editable) {
			dom.sliderInfo.find('input').val(value);
		} else {
			dom.sliderInfo.text(value + prefix);
		}
	}

	function writeInfoMax(value)
	{
		if (editable) {
			dom.sliderInfoMax.find('input').val(value);
		} else {
			dom.sliderInfoMax.text(value + prefix);
		}
	}

	function getInfoMin()
	{

		if (!valueMin) {
			valueMin = 0;
		}

		if (editable) {
			return valueMin;
		} else {
			return valueMin + prefix;
		}

	}

	function getInfoMax()
	{
		if (!valueMax) {
			valueMax = 0;
		}

		if (editable) {
			return valueMax;
		} else {
			return valueMax + prefix;
		}

	}

	function getRange()
	{
		return (max - min); 
	}

	function getPositionToValueMin()
	{
		return Math.round( convertPositionToValue(positionMin) );
	}

	function getPositionToValueMax()
	{
		return Math.round( convertPositionToValue(positionMax) );
	}

	function convertPositionToValue(poz)
	{
		return (getRange() * poz / sliderWidth()) + min;
	}

	function getValueToPositionMin()
	{
		return convertValueToPosition(valueMin) - convertValueToPosition(min);
	}

	function getValueToPositionMax()
	{
		return convertValueToPosition(valueMax) - convertValueToPosition(min);
	}

	function convertValueToPosition(val)
	{
		return (sliderWidth() *  val / getRange());
	}

	function setValueMin( val )
	{
		valueMin = val;
		setPositionMin(getValueToPositionMin());
	}

	function setValueMax( val ) 
	{
		valueMax = val;
		setPositionMax(getValueToPositionMax());
	}

	function refresh() 
	{
		setPositionMin(getValueToPositionMin());
		setPositionMax(getValueToPositionMax());
		initRules();
	}

	function reset(config)
	{
		config = config || {};
		$.each(config, function(i, v){
			cfg[i] = v;
		});

		initConfig();
		refresh();
	}

	function show()
	{
		dom.instance.show();
		refresh();
	}

	function offsetLeft()
	{
		return dom.sliderBar.offset().left;
	}

	function offsetRight()
	{
		return dom.sliderBar.offset().right;
	}

	function sliderWidth()
	{
		return dom.sliderBar.width();
	}

	function onMouseMove(func)
	{
		$(window).on('mousemove', function(event) {
			func(event);
		});	
	}

	function onMouseUp(func)
	{
		 $(window).on('mouseup', function(event) {
		 	func(event);
		 });
	}

	self.resetPositionMin = function()
	{
		resetPositionMin();
	};

	function resetPositionMin()
	{
		setPositionMin(-offsetLeft());
	}

	self.resetPositionMax = function()
	{
		resetPositionMax();
	};

	function resetPositionMax()
	{
		setPositionMax(-offsetLeft() + sliderWidth());
	}

	function startDrag(type) 
	{
		type = type || 'min';

		drabable = true;

		onMouseMove(function(event){
			switch(type)
			{
				case 'min':
					setPositionMin(-offsetLeft() + event.pageX);
				break;

				case 'max':
					setPositionMax(-offsetLeft() + event.pageX);
				break;
			}

			if( move != null )
				move( self );

			if( lastMouseX > -1 )
				mouseSpeed = Math.abs(event.pageX - lastMouseX);

			lastMouseX = event.pageX;
		});

		if( start != null )
			start( self );
	}

	function stopDrag() 
	{
		drabable = false;
		$(window).unbind('mousemove');
		if( done != null )
			done( self );
	}

	function getValueMin()  
	{
		return valueMin;
	}

	function getValueMax()
	{
		return valueMax;
	}

	function addRule( value ) 
	{
		if( value < min )
			value = min;
		else if( value > max )
			value = max;

		// position in pixel's 
		var position = (( (sliderWidth() - controllerWidth()) / ( max - min) ) * (value - min) ) + (controllerWidth()/2) ;
		// position in percent's 
		position = (position / sliderWidth() ) * 100;

		var template = '<div class="rule-line-'+value+'">';
			template += '<span class="rule" style="left: '+position+'%;"></span>';
			template += '<span class="rule-value" style="left: '+position+'%;">'+getValueReplacement(value)+'</span>';
			template += '</div>';

		template = $(template);

		var ruleInfo = template.find('.rule-value');

		dom.rulesBar.append(template);

		ruleInfo.css({'left' :  (position - ( (ruleInfo.width()/2) /  sliderWidth() ) * 100) + '%'});
	}

	function getValueReplacement(value) 
	{
		if (remplaceValues.hasOwnProperty(value))
			return remplaceValues[value];
		return value;
	}

	return this;
}

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

NewsletterPro.namespace('modules.createCustomField');
NewsletterPro.modules.createCustomField = ({
	dom: null,
	box: null,
	init: function(box) 
	{
		var self = this;
		this.box = box;

		this.ready(function(dom){

			var fieldsDataModel,
				fieldsDataSource,
				valuesListDataModel,
				valuesListDataSource,
				editableTypes = [];

			for (var key in box.dataStorage.get('custom_field.types_editable')) {
				editableTypes.push(Number(key));
			}

			var updateValuesListDataSourceAndSync = function(id, idLang)
			{
				if (typeof valuesListDataSource !== 'undefined')
				{
					valuesListDataSource.transport.read.data = {
						'id': id,
						'id_lang': idLang
					};

					valuesListDataSource.sync();
				}
			};

			self.l = l = box.translations.l(box.translations.modules.createCustomField);
			self.win = win = new gkWindow({
				width: 800,
				height: 600,
				setScrollContent: 540,
				title: l('Create Custom Fields'),
				className: 'np-costum-fields-win',
				show: function(win) {},
				close: function(win) {},
				content: function(win) 
				{
					var tempalte = self.tempalte();

					dom.btnAddNewVariable.on('click', function(){
						var btn = $(this);

						if (Number(btn.data('active')))
						{
							btn.data('active', 0);
							btn.html('<i class="icon icon-plus-square"></i> '+self.l('Add New Variable'));
							dom.fiedAdd.slideUp();
						}
						else
						{
							btn.data('active', 1);
							btn.html('<i class="icon icon-minus-square"></i> '+self.l('Add New Variable'));
							dom.fiedAdd.slideDown();
						}
					});

					dom.btnSaveVariable.on('click', function(){
						var btn = $(this),
							variable_name = dom.inputVariableName.val(),	
							type = dom.inputVariableType.val(),
							required = Number($('[name="np_custom_field_required"]:checked').val());

						box.showAjaxLoader(btn);

						$.postAjax({'submit_custom_field': 'addField', variable_name: variable_name, type: type, required: required}).done(function(response){

							if (!response.success)
								box.alertErrors(response.errors);
							else
							{
								if (typeof fieldsDataSource !== 'undefined')
									fieldsDataSource.sync();
							}

						}).always(function(){
							box.hideAjaxLoader(btn);
						});

					});

					fieldsDataModel = new gk.data.Model({
						id: 'id_newsletter_pro_subscribers_custom_field',
					});

					fieldsDataSource = new gk.data.DataSource({
						pageSize: 6,
						transport: {
							read: {
								url: NewsletterPro.dataStorage.get('ajax_url')+'&submit_custom_field=getFieldsList',
								dataType: 'json',
							},

							destroy: {
								url: NewsletterPro.dataStorage.get('ajax_url')+'&submit_custom_field=deleteField&id',
								type: 'POST',
								dateType: 'json',
								success: function(response, itemData) 
								{
									if(!response.success) 
										box.alertErrors(response.errors);

								},
								error: function(data, itemData) 
								{
									box.alertErrors(l('An error occurred.'));
								},
								complete: function(data, itemData) {},
							},					
						},
						schema: {
							model: fieldsDataModel
						},
						trySteps: 2,
						errors: 
						{
							read: function(xhr, ajaxOptions, thrownError) 
							{
								fieldsDataSource.syncStepAvailableAdd(3000, function(){
									fieldsDataSource.sync();
								});
							},
						},
					});

					dom.variablesList.gkGrid({
						dataSource: fieldsDataSource,
						selectable: false,
						currentPage: 1,
						pageable: true,
						template: 
						{
							variable_name: function(item, value)
							{
								return '{'+value+'}';
							},

							type_name: function(item)
							{
								return item.data.type_name;
							},

							required: function(item, value)
							{
								var div = $('<div>'),
									value = Number(value),
									buttonEnabled = $('\
										<a class="list-action-enable action-enabled" data-enabled="1" href="javascript:{}" title="Enabled">\
											<i class="icon-check"></i>\
											<i class="icon-remove hidden"></i>\
										</a>\
									'),
									buttonDisabled = $('\
										<a class="list-action-enable action-disabled" data-enabled="0" href="javascript:{}" title="Disabled">\
											<i class="icon-check hidden"></i>\
											<i class="icon-remove"></i>\
										</a>\
									');

								if (value)
									div.html(buttonEnabled);
								else
									div.html(buttonDisabled);
								
								var clickFunction = function(event)
								{
									var target = $(this),
										enabled = Number(target.data('enabled')),
										id = item.data.id_newsletter_pro_subscribers_custom_field,
										value = (enabled ? 0 : 1);

									$.postAjax({'submit_custom_field': 'changeFieldRequired', id: id, value: value }).done(function(response){
										if (!response.success)
											box.alertErrors(response.errors);
										else
										{
											if (enabled)
												div.html(buttonDisabled);
											else
												div.html(buttonEnabled);

											buttonEnabled.on('click', clickFunction);
											buttonDisabled.on('click', clickFunction);
										}
									});
								};

								buttonEnabled.on('click', clickFunction);
								buttonDisabled.on('click', clickFunction);

								return div;
							},

							actions: function(item)
							{
								var div = $('<div>');

								var deleteButton = $('#np-delete-custom-field-variables-list')
									.gkButton({
										title: self.l('delete'),
										name: 'np-delete-custom-field-variables-list',
										className: 'btn btn-default btn-margin pull-right',
										click: function(e) 
										{

											if (!confirm(self.l('Are you sure you want to do this action? You will lose all the data collected with this field.')))
												return false;

											item.destroy('success');
										},
										icon: '<i class="icon icon-trash-o"></i> ',
									});

								div.append(deleteButton);

								if (editableTypes.indexOf(Number(item.data.type)) != -1)
								{
									var editButton = $('#np-edit-custom-field-variables-list')
										.gkButton({
											title: self.l('Edit'),
											name: 'np-edit-custom-field-variables-list',
											className: 'btn btn-default btn-margin pull-right',
											click: function(e) 
											{
												box.dataStorage.set('custom_field.current_field_id', Number(item.data.id_newsletter_pro_subscribers_custom_field));

												dom.spanEditVariableName.html('{'+item.data.variable_name+'}')
											},
											icon: '<i class="icon icon-edit"></i> ',
										});

									div.append(editButton);
								}

								return div;
							}
						}
					});
					
					box.dataStorage.on('change', 'custom_field.current_field_id', function(id){
						var idLang = box.dataStorage.getNumber('id_selected_lang');

						if (typeof valuesListDataModel === 'undefined')
						{
							dom.fiedEdit.show();

							valuesListDataModel = new gk.data.Model({
								id: 'key',
							});

							valuesListDataSource = new gk.data.DataSource({
								pageSize: 6,
								transport: {
									read: {
										url: NewsletterPro.dataStorage.get('ajax_url')+'&submit_custom_field=getValuesList',
										dataType: 'json',
										data: {
											'id': id,
											'id_lang': idLang,
										}
									},
								},
								schema: {
									model: valuesListDataModel
								},
								trySteps: 2,
								errors: 
								{
									read: function(xhr, ajaxOptions, thrownError) 
									{
										valuesListDataSource.syncStepAvailableAdd(3000, function(){
											valuesListDataSource.sync();
										});
									},
								},
							});

							dom.valuesList.gkGrid({
								dataSource: valuesListDataSource,
								selectable: false,
								currentPage: 1,
								pageable: true,
								template: 
								{
									value: function(item, value)
									{
										return value;
									},

									actions: function(item, value)
									{
										var div = $('<div>');
										var deleteButton = $('#np-delete-custom-field-value')
											.gkButton({
												title: self.l('delete'),
												name: 'np-delete-custom-field-value',
												className: 'btn btn-default btn-margin pull-right',
												click: function(e) 
												{
													var id = box.dataStorage.getNumber('custom_field.current_field_id'),
														key = item.data.key;
													
													$.postAjax({'submit_custom_field': 'removeValueByKey', id: id, key: key}).done(function(response){
														if (!response.success)
															box.alertErrors(response.errors);
														else
															valuesListDataSource.sync();
													});

												},
												icon: '<i class="icon icon-trash-o btn-margin"></i> ',
											});

										div.append(deleteButton);

										var editButton = $('#np-edit-custom-field-value')
											.gkButton({
												title: self.l('Edit'),
												name: 'np-edit-custom-field-value',
												className: 'btn btn-default btn-margin pull-right',
												click: function(e) 
												{
													dom.btnFieldAdd.html('<i class="icon icon-save"></i> ' + self.l('Update'));
													dom.btnFieldAdd.data('edit', 1);
													dom.btnFieldAdd.data('editKey', item.data.key);

													var id = box.dataStorage.getNumber('custom_field.current_field_id'),
														key = item.data.key;

													$.postAjax({'submit_custom_field': 'getValueByKey', id: id, key: key }).done(function(response){
														if (!response.success)
															box.alertErrors(response.errors);
														else
														{
															var value = response.value;

															$.each(dom.customFieldVariable, function(key, item){
																var input = $(item),
																	inputIdLang = input.data('lang');

																if (value.hasOwnProperty(inputIdLang))
																	input.val(value[inputIdLang]);
															});
														}
													});

												},
												icon: '<i class="icon icon icon-edit btn-margin"></i> ',
											});

										div.append(editButton);

										return div;
									}
								}
							});

							dom.langSelect.on('click', function(){
								idLang = box.dataStorage.getNumber('id_selected_lang');
								id =  box.dataStorage.getNumber('custom_field.current_field_id');
								updateValuesListDataSourceAndSync(id, idLang);
							});

							dom.btnFieldAdd.on('click', function(){
								var btn = $(this),
									isEdit = btn.data('edit'),
									editKey = btn.data('editkey'),
									id =  box.dataStorage.getNumber('custom_field.current_field_id');

								if (isEdit)
								{
									var value = self.getInputsVal(dom.customFieldVariable),
										key = dom.btnFieldAdd.data('editKey');

									$.postAjax({'submit_custom_field': 'updateValue', id: id, key: key, value: value}).done(function(response){
										if (!response.success)
											box.alertErrors(response.errors);
										else
										{
											dom.btnFieldAdd.html('<i class="icon icon-plus-square"></i> ' + self.l('Add'));
											dom.btnFieldAdd.data('edit', 0);
											dom.btnFieldAdd.data('editKey', 0);

											$.each(dom.customFieldVariable, function(key, item){
												$(item).val('');
											});

											idLang = box.dataStorage.getNumber('id_selected_lang');
											updateValuesListDataSourceAndSync(id, idLang);
										}
									});
								}
								else
								{
									var value = self.getInputsVal(dom.customFieldVariable);

									$.postAjax({'submit_custom_field': 'addValue', id: id, value: value}).done(function(response){
										if (!response.success)
											box.alertErrors(response.errors);
										else
										{
											idLang = box.dataStorage.getNumber('id_selected_lang');
											updateValuesListDataSourceAndSync(id, idLang);
										}
									});
								}

							});

						} // end of undefined

						updateValuesListDataSourceAndSync(id, idLang);
					});

					return tempalte;
				}
			});

			dom.btnOpenWindow.on('click', function(){
				win.show();
			});

			dom.btnDisplayNewColumns.on('click', function(){
				win.hide();
				box.modules.sendNewsletters.vars.winDisplayCustomColumns.show()
			});

		});
	},

	getInputsVal: function(selector)
	{
		var result = {};
		$.each(selector, function(key, item){
			var matchLang = $(item).attr('name').match(/\d+$/),
				idLang = matchLang !== null ? matchLang[0] : 0;

			result[idLang] = $(item).val();
		});
		return result;
	},

	tempalte: function()
	{
		var l = this.l,
			box = this.box,
			self = this,
			types = box.dataStorage.get('custom_field.types'),
			all_active_languages = box.dataStorage.get('all_active_languages');

		var displayTypeOptions = '',
			displayAllActivaLanguagesValue = '';

		for (var key in types) {
			displayTypeOptions += '<option value="'+key+'">'+types[key]+'</option>';
		}

		for (var key in all_active_languages) {
			var lang = all_active_languages[key];

			displayAllActivaLanguagesValue += '<input data-lang="'+lang.id_lang+'" name="np_custom_field_variable_input_'+lang.id_lang+'" type="text" class="form-control fixed-width-xxl pull-left" style="'+(lang.selected ? 'display: block;' : 'display: none;')+'">';
		}

		var tempalte = $('\
			<div id="np-create-custom-field-content" class="np-create-custom-field-content">\
				<div class="form-group clearfix">\
					<a href="javascript:{}" id="np-add-new-variable" class="btn btn-default pull-right" date-active="0">\
						<i class="icon icon-plus-square"></i> '+self.l('Add New Variable')+'\
					</a>\
				</div>\
				<div id="np-custom-field-add" class="clearfix" style="display: none">\
					<hr style="margin-top: 0">\
					<div class="form-group clearfix">\
						<label class="control-label col-sm-3"><span class="label-tooltip">'+self.l('Variable Name')+'</span></label>\
						<div class="form-group col-sm-9">\
							<input id="np-create-custom-field-variable-name" type="text" class="form-control fixed-width-xxl pull-left">\
						</div>\
					</div>\
					<div class="form-group clearfix">\
						<label class="control-label col-sm-3"><span class="label-tooltip">'+self.l('Type')+'</span></label>\
						<div class="form-group col-sm-9">\
							<select class="form-control fixed-width-xxl pull-left" id="np-create-custom-field-variable-type">\
								<option value="0">- '+self.l('none')+' -</option>\
								'+displayTypeOptions+'\
							</select>\
						</div>\
					</div>\
					<div class="form-group clearfix">\
						<label class="control-label col-sm-3"><span class="label-tooltip">'+l('Required')+'</span></label>\
						<div class="col-sm-9">\
							<div class="fixed-width-l clearfix">\
								<div class="col-sm-3">\
									<div class="row">\
										<span class="switch prestashop-switch">\
											<input id="np_custom_field_required_yes" type="radio" name="np_custom_field_required" value="1">\
											<label for="np_custom_field_required_yes">\
												'+l('Yes')+'\
											</label>\
											<input id="np_custom_field_required_no" type="radio" name="np_custom_field_required" value="0" checked="checked">\
											<label for="np_custom_field_required_no">\
												'+l('No')+'\
											</label>\
											<a class="slide-button btn"></a>\
										</span>\
									</div>\
								</div>\
							</div>\
						</div>\
					</div>\
					<div class="form-group clearfix">\
						<div class="col-sm-offset-3 com-sm-9">\
							<a id="np-create-custom-field-save-variable" href="javascript:{}" class="btn btn-success pull-left">\
								<span class="btn-ajax-loader"></span>\
								<i class="icon icon-save"></i>\
								'+self.l('Save Variable')+'\
							</a>\
						</div>\
					</div>\
					<hr style="margin-top: 0">\
				</div>\
				<div class="form-group clearfix">\
					<table id="np-custom-field-variables-list" class="table table-bordered np-custom-field-variables-list">\
						<thead>\
							<tr>\
								<th class="variable-name" data-field="variable_name">'+l('Variable Name')+'</th>\
								<th class="type-name" data-template="type_name">'+l('Type')+'</th>\
								<th class="required" data-field="required">'+l('Required')+'</th>\
								<th class="np-actions" data-template="actions">'+l('Actions')+'</th>\
							</tr>\
						</thead>\
					</table>\
				</div>\
				<div class="form-group clearfix">\
					<label class="control-label col-sm-3"><span class="label-tooltip">'+self.l('List Columns')+'</span></label>\
					<div class="col-sm-9">\
						<div class="form-group clearfix">\
							<a href="javascript:{}" id="np-display-new-columns-custom-field" class="btn btn-default pull-left"><i class="icon icon-eye"></i> '+self.l('Display Custom Columns')+'</a>\
						</div>\
						<p class="help-block">'+self.l('Display a new column for the created fields on the list Visitors Subscribed (at the Newsletter Pro module).')+'</p>\
					</div>\
				</div>\
				<div id="np-custom-field-edit" class="form-group clearfix" style="display: none">\
					<h4 class="np-win-h4">'+l('Edit Variable')+' <span id="np-custom-field-edit-variable-name"></span></h4>\
					<div class="form-group clearfix">\
						<label class="control-label col-sm-3"><span class="label-tooltip">'+l('Value')+'</span></label>\
						<div class="col-sm-9" style="padding-right: 0">\
							<div class="form-inline">\
								<div class="form-group">\
									'+displayAllActivaLanguagesValue+'\
								</div>\
								<div class="form-group">\
									<div id="np-custom-field-value-lang" class="pull-right np-custom-field-value-lang gk_lang_select pull-left"></div>\
								</div>\
								<div class="form-group pull-right">\
									<a id="np-btn-custom-field-add" data-edit="0" data-editkey="0" href="javascript:{}" class="btn btn-default">\
										<i class="icon icon-plus-square"></i>\
										'+l('Add')+'\
									</a>\
								</div>\
							</div>\
						</div>\
					</div>\
					<div class="form-group clearfix">\
						<table id="np-custom-field-values-list" class="table table-bordered np-custom-field-values-list">\
							<thead>\
								<tr>\
									<th class="np-value" data-field="value">'+l('Value')+'</th>\
									<th class="np-actions" data-template="actions">'+l('Actions')+'</th>\
								</tr>\
							</thead>\
						</table>\
					</div>\
				</div>\
			</div>\
		');

		$.extend(this.dom, {
			winTemplate: tempalte,
			btnAddNewVariable: tempalte.find('#np-add-new-variable'),
			fiedAdd: tempalte.find('#np-custom-field-add'),
			fiedEdit: tempalte.find('#np-custom-field-edit'),
			valuesList: tempalte.find('#np-custom-field-values-list'),
			variablesList: tempalte.find('#np-custom-field-variables-list'),
			inputVariableName: tempalte.find('#np-create-custom-field-variable-name'),
			inputVariableType: tempalte.find('#np-create-custom-field-variable-type'),
			btnSaveVariable: tempalte.find('#np-create-custom-field-save-variable'),
			spanEditVariableName: tempalte.find('#np-custom-field-edit-variable-name'),
			langSelect: tempalte.find('#np-custom-field-value-lang'),
			btnFieldAdd: tempalte.find('#np-btn-custom-field-add'),
			customFieldVariable: tempalte.find('[name^="np_custom_field_variable_input_"]'),
			btnDisplayNewColumns: tempalte.find('#np-display-new-columns-custom-field'),
		});

		return tempalte;
	},

	ready: function(func) 
	{
		var self = this;

		$(document).ready(function(){
			self.dom = {
				btnOpenWindow: $('#np-create-custom-field')
			};

			func(self.dom);
		});
	}
}.init(NewsletterPro));
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
* 
* Examples:
* 
* {if isset(var)} yes {else} no {/if}
* {if var > 0} {var} is greater than 0!{/if}
* Signs: <, <=, ==, >= , >, ===, !==, !=
*
**/

NewsletterPro.namespace('components.ProductRender');
NewsletterPro.components.ProductRender = function ProductRender(content, vars)
{
	if (!(this instanceof ProductRender))
		return new ProductRender(content, vars);

	var CONDITIONAL_ISSET       = 1,
		CONDITIONAL_LT          = 2,
		CONDITIONAL_LT_EQ       = 3,
		CONDITIONAL_EQUAL       = 4,
		CONDITIONAL_GT_EQ       = 5,
		CONDITIONAL_GT          = 6,
		CONDITIONAL_EQUAL_E     = 7,
		CONDITIONAL_NOT_EQUAL   = 8,
		CONDITIONAL_NOT_EQUAL_E = 9,
		contentRender;

	content = content.replace(/\&gt;/g, '>');
	content = content.replace(/\&lt;/g, '<');

	this.render = function()
	{
		// render the content exclude the condtional statements
		for(var key in vars)
			setVar(key, displayValue(vars[key]));

		contentRender = content;

		try
		{
			executeConditions(getConditions());
		} 
		catch(e)
		{
			console.error(e.message);
		}

		return contentRender;
	};

	function setVar(key, value) 
	{
		content = content.replace(new RegExp('\{'+key+'\}', 'g'), value);
	}

	function displayValue(value) 
	{
		if (typeof value === 'undefined' || value === null) 
			return '';
		return value;
	}

	function getConditions()
	{
		var regex = /\{if\s(?:\s+)?([^}]+)\}([\s\S]+?)(?:\{\/if\})/g;
		var match = content.match(regex);
		var conditions = [];

		if (match != null)
		{
			var len = match.length;
			if (len > 0)
			{
				for(var i = 0; i < len; i++)
				{
					var match = regex.exec(content),
						type = getType(match[1]),
						condition = match[1];

					conditions.push({
						match: match[0],
						condition: condition,
						content: match[2],
						type: type,
						variable: getRightVariable(condition),
						value: getLeftValue(condition),
						sign: getSignByType(type),
						hasElse: function()
						{
							return /\{else\}/.test(this.match);
						},
						getElse: function()
						{
							if (this.hasElse())
							{
								var match = /\{else\}([\s\S]+)/.exec(this.content);
								if (match != null)
									return match[1];
							}
							return '';
						},
						getIf: function()
						{
							if (this.hasElse())
							{
								var match = /([\s\S]+?)\{else\}/.exec(this.content);
								if (match != null)
									return match[1];
							}
							return this.content;
						}
					});
				}
			}
		}

		return conditions;
	}

	function executeConditions(conditions)
	{
		if (conditions.length > 0)
		{
			for(var i in conditions)
			{
				var condition = conditions[i];
				 evaluate(condition);
			}
		}
	}

	function evaluate(condition)
	{
		var variable = condition.variable,
			value    = condition.value,
			content  = condition.getIf(),
			match    = condition.match;

		switch(condition.type)
		{
			case CONDITIONAL_ISSET:
				if (varExists(condition.variable))
					setConditionVar(match, content);
				else if (condition.hasElse())
					setConditionVar(match, condition.getElse());
				else
					setConditionVar(match, '');
				break;

			case CONDITIONAL_LT:
				if (varExists(variable) && getVarValue(variable) < value)
					setConditionVar(match, content);
				else if (condition.hasElse())
					setConditionVar(match, condition.getElse());
				else
					setConditionVar(match, '');
				break;

			case CONDITIONAL_LT_EQ:
				if (varExists(variable) && getVarValue(variable) <= value)
					setConditionVar(match, content);
				else if (condition.hasElse())
					setConditionVar(match, condition.getElse());
				else
					setConditionVar(match, '');
				break;

			case CONDITIONAL_EQUAL:
				if (varExists(variable) && getVarValue(variable) == value)
					setConditionVar(match, content);
				else if (condition.hasElse())
					setConditionVar(match, condition.getElse());
				else
					setConditionVar(match, '');
				break;

			case CONDITIONAL_GT_EQ:
				if (varExists(variable) && getVarValue(variable) >= value)
					setConditionVar(match, content);
				else if (condition.hasElse())
					setConditionVar(match, condition.getElse());
				else
					setConditionVar(match, '');
				break;

			case CONDITIONAL_GT:
				if (varExists(variable) && getVarValue(variable) > value)
					setConditionVar(match, content);
				else if (condition.hasElse())
					setConditionVar(match, condition.getElse());
				else
					setConditionVar(match, '');
				break;

			case CONDITIONAL_EQUAL_E:
				if (varExists(variable) && getVarValue(variable) === value)
					setConditionVar(match, content);
				else if (condition.hasElse())
					setConditionVar(match, condition.getElse());
				else
					setConditionVar(match, '');
				break;

			case CONDITIONAL_NOT_EQUAL:
				if (varExists(variable) && getVarValue(variable) != value)
					setConditionVar(match, content);
				else if (condition.hasElse())
					setConditionVar(match, condition.getElse());
				else
					setConditionVar(match, '');
				break;

			case CONDITIONAL_NOT_EQUAL_E:
				if (varExists(variable) && getVarValue(variable) !== value)
					setConditionVar(match, content);
				else if (condition.hasElse())
					setConditionVar(match, condition.getElse());
				else
					setConditionVar(match, '');
				break;
		}
	}

	function setConditionVar(match, content)
	{
		contentRender = contentRender.replace(match, content);
	}

	function varExists(name)
	{
		if (vars.hasOwnProperty(name))
			return true;
		return false;
	}

	function getVarValue(name)
	{
		if (varExists(name))
			return vars[name];
	}

	function getType(value)
	{
		var type;

		switch(true)
		{
			case /isset/.test(value):
				type = CONDITIONAL_ISSET;
				break;

			case getRegExp('<').test(value):
				type = CONDITIONAL_LT;
				break;

			case getRegExp('<=').test(value):
				type = CONDITIONAL_LT_EQ;
				break;

			case getRegExp('==').test(value):
				type = CONDITIONAL_EQUAL;
				break;

			case getRegExp('>=').test(value):
				type = CONDITIONAL_GT_EQ;
				break;

			case getRegExp('>').test(value):
				type = CONDITIONAL_GT;
				break;

			case getRegExp('===').test(value):
				type = CONDITIONAL_EQUAL_E;
				break;

			case getRegExp('!=').test(value):
				type = CONDITIONAL_NOT_EQUAL;
				break;

			case getRegExp('!==').test(value):
				type = CONDITIONAL_NOT_EQUAL_E;
				break;
		}

		return type;
	}

	function getRegExp(value)
	{
		var rl = '(\\s|\\w+|\"\')';
		return new RegExp(rl+value+rl);
	}

	function getSignByType(type)
	{
		var sign;
		switch(type)
		{
			case CONDITIONAL_ISSET:
				sign = 'isset';
				break;

			case CONDITIONAL_LT:
				sign = '<';
				break;

			case CONDITIONAL_LT_EQ:
				sign = '<=';
				break;

			case CONDITIONAL_EQUAL:
				sign = '==';
				break;

			case CONDITIONAL_GT_EQ:
				sign = '>=';
				break;

			case CONDITIONAL_GT:
				sign = '>';
				break;

			case CONDITIONAL_EQUAL_E:
				sign = '===';
				break;

			case CONDITIONAL_NOT_EQUAL:
				sign = '!=';
				break;

			case CONDITIONAL_NOT_EQUAL_E:
				sign = '!==';
				break;
		}
		return sign;
	}

	function getRightVariable(condition)
	{
		var type = getType(condition);
		if(type == CONDITIONAL_ISSET)
		{
			var regex = new RegExp(getSignByType(type)+'\\((\\w+)\\)');
			var match = regex.exec(condition);
			if (typeof match[1] !== 'undefined')
				return match[1];
		}
		else
			return $.trim(condition.match(/\w+/)[0]);
	}

	function getLeftValue(condition)
	{
		var match = /[=<>]+([^}]+)/.exec(condition);
		if (match !== null)
			return stripQuotes(match[1]);
	}

	function stripQuotes(value)
	{
		value = $.trim(value);

		if (value.length > 0)
		{
			var firstChar = value[0];
			var lastChar = value[value.length - 1];

			if (firstChar == '\'' || firstChar == '"')			
				return value.substr(1, value.length - 2);
		}
		return value;
	}

};
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

NewsletterPro.namespace('components.ProductTemplate');
NewsletterPro.components.ProductTemplate = function ProductTemplate(cfg)
{
	if (!(this instanceof ProductTemplate))
		return new ProductTemplate(cfg);

	var box = NewsletterPro,
		self = this,
		columns = 0,
		columnsMatch = null;

	this.cfg = cfg;
	this.header = this.cfg.header;

	columnsMatch = this.cfg.template.match(/\<\!--\s+?\{columns=(\d+)\}\s+?--\>/);

	if (columnsMatch) {
		if (columnsMatch.length > 1) {
			columns = Number(columnsMatch[1]);

			if (isNaN(columns)) {
				columns = 0;
			}
		}
	}

	this.columns = columns;

	this.template = this.cfg.template.replace(/\{columns=\d+\}|<!--\s+\{columns=\d+\}\s+-->|<!-- start header -->[\s\S]*?<!-- end header -->/g, '');

	if (this.isTemplate()) {
		this.tree = box.components.ProductTemplateTree(this.template);
	} else {
		this.tree = box.components.ProductTemplateTree(this.template, false);
	}

};

NewsletterPro.components.ProductTemplate.prototype = {
	html: function()
	{
		return this.tree.get('np-product');
	},

	isTemplate: function()
	{
		if (this.getHeader('content') === 'template') {
			return true;
		} else {
			return false;
		}
	},

	hasHeader: function(name)
	{
		if (this.header.hasOwnProperty(name)) {
			return true;
		} else {
			return false;
		}
	},

	getHeader: function(name)
	{
		if (this.hasHeader(name)) {
			return this.header[name];
		}
	},
};

NewsletterPro.namespace('components.ProductTemplateTree');
NewsletterPro.components.ProductTemplateTree = function ProductTemplateTree(string, needParse) {

	if (!(this instanceof ProductTemplateTree))
		return new ProductTemplateTree(string, needParse);

	needParse = typeof needParse === 'undefined' ? true : false;

	this.string = string;
	this.storage = {};

	if (needParse) {
		this.init(this.string);
	} else {
		this.storage['np-product'] = string;
	}
};

NewsletterPro.components.ProductTemplateTree.prototype = {
	init: function(string) {
        var i,
            len = string.length,
            current,
            next,
            prev,
            depth = 0,
            nodeName,
            nodeValue,
            nodeIndex = 0,
            endIndex = 0,
            openIndex = 0;

        for (i = 0; i < len; i++) {
            current = string[i];
            next = string[i + 1];
            prev = string[i - 1];

            if (current === '{' && next === '{') {
                nodeName = string.substr(nodeIndex, i - nodeIndex).replace(/\s+|\:/g, '');

                if (depth == 0) {
                    rootName = nodeName;
                }

                nodeIndex = i + 2;
                openIndex = nodeIndex;

                depth++;
            } else if (current === '}' && next === '}' && string[i + 2] === ',') {

                nodeIndex = i + 3;
                endIndex = nodeIndex

                nodeValue = string.substr(openIndex, i - openIndex).trim();

                if (depth > 1) {
                    this.add(nodeName, nodeValue);
                }

                depth--;
            } else if (current === '}' && next === '}') {
                depth--;
            }
        }

	},

	parse: function(func) {
		var keys = Object.keys(this.storage);
		for (var i = 0, len = keys.length; i < len; i++) {
			func(keys[i], this.storage[keys[i]]);
		}
	},

    add: function(name, value) {
        this.storage[name] = value;
    },
    get: function(name) {
        if (this.has(name)) {
            return this.storage[name];
        }
    },
    getRender: function(name, vars) {
    	if (this.has(name)) {
    		vars = vars || {};

    		var box = NewsletterPro,
    			value = this.get(name);

    		return new box.components.ProductRender(value, vars).render();
    	}
    },
    has: function(name) {
        return this.storage.hasOwnProperty(name);
    },
};


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
	NewsletterPro.namespace('components.FilterSelection');
	NewsletterPro.components.FilterSelection = function FilterSelection(cfg)
	{
		if (!(this instanceof FilterSelection))
			return new FilterSelection(cfg);

		this.customers = cfg.customers;
		this.visitors = cfg.visitors;
		this.visitors_np = cfg.visitors_np;
		this.added = cfg.added;

		this.customers_apply_callback =  cfg.customers_apply_callback;
		this.visitor_apply_callback = cfg.visitor_apply_callback;
		this.visitor_np_apply_callback = cfg.visitor_np_apply_callback;
		this.added_apply_callback = cfg.added_apply_callback;

		this.clearfilters = function()
		{
			this.customers.clear.trigger('click');

			if (typeof this.visitors !== 'undefined')
				this.visitors.clear.trigger('click');

			if (typeof this.visitors_np !== 'undefined')
				this.visitors_np.clear.trigger('click');

			this.added.clear.trigger('click');
		};

		this.parseFilters = function(list, filter, func)
		{
			for (var filterName in list)
			{
				var item = list[filterName];

				for (var filterNameSelection in filter)
				{
					var ids = filter[filterNameSelection];

					func(filterName, item, filterNameSelection, ids);
				}
			}
		};

		this.applyFilters = function(filters)
		{
			for (var listName in filters)
			{
				var filter = filters[listName];

				switch(listName)
				{
					case 'customers':
						this.parseFilters(this.customers, filter, function(filterName, item, filterNameSelection, ids){

							switch(filterName)
							{
								case 'groups':
								case 'languages':
								case 'shops':
								case 'gender':
								case 'subscribed':
									if (filterName === filterNameSelection)
									{
										item.mark(ids);
									}
								break;
							}
						});

						this.customers_apply_callback();

						break;

					case 'visitors':
						if (typeof this.visitors !== 'undefined')
						{
							this.parseFilters(this.visitors, filter, function(filterName, item, filterNameSelection, ids){
								switch(filterName)
								{
									case 'shops':
									case 'subscribed':
										if (filterName === filterNameSelection)
										{
											item.mark(ids);
										}
									break;
								}
							});

							this.visitor_apply_callback();
						}
						break;

					case 'visitors_np':
						if (typeof this.visitors_np !== 'undefined')
						{
							this.parseFilters(this.visitors_np, filter, function(filterName, item, filterNameSelection, ids){
								switch(filterName)
								{
									case 'languages':
									case 'shops':
									case 'gender':
									case 'subscribed':
									case 'filter_by_interest':
										if (filterName === filterNameSelection)
										{
											item.mark(ids);
										}
									break;
								}
							});

							this.visitor_np_apply_callback();
						}
						break;

					case 'added':
							this.parseFilters(this.added, filter, function(filterName, item, filterNameSelection, ids){
								switch(filterName)
								{
									case 'languages':
									case 'shops':
									case 'csv_name':
									case 'subscribed':
										if (filterName === filterNameSelection)
										{
											item.mark(ids);
										}
									break;
								}
							});

							this.added_apply_callback();
						break;
				}
			}
		};

		this.getFilters = function()
		{
			var filters = {};

			filters['customers'] = this.getCustomersFilter();

			if (typeof this.visitors !== 'undefined')
				filters['visitors'] = this.getVisitorsFilter();

			if (typeof this.visitors_np !== 'undefined')
				filters['visitors_np'] = this.getVisitorNpFilter();

			filters['added'] = this.getAddedFilter();

			return filters;
		};

		this.getCustomersFilter = function()
		{
			var filters = {};
			for(var filterName in this.customers)
			{
				var item = this.customers[filterName];

				switch(filterName)
				{
					case 'groups':
					case 'languages':
					case 'shops':
					case 'gender':
					case 'subscribed':
						filters[filterName] = item.getSelected();
						break;
				}
			}
			return filters;
		};

		this.getVisitorsFilter = function()
		{
			var filters = {};
			for(var filterName in this.visitors)
			{
				var item = this.visitors[filterName];

				switch(filterName)
				{
					case 'shops':
					case 'subscribed':
						filters[filterName] = item.getSelected();
						break;
				}

				// make the rest of cases
			}
			return filters;
		};

		this.getVisitorNpFilter = function()
		{
			var filters = {};
			for(var filterName in this.visitors_np)
			{
				var item = this.visitors_np[filterName];
			
				switch(filterName)
				{
					case 'languages':
					case 'shops':
					case 'gender':
					case 'subscribed':
					case 'filter_by_interest':
						filters[filterName] = item.getSelected();
						break;
				}

				// make the rest of cases
			}
			return filters;
		};

		this.getAddedFilter = function()
		{
			var filters = {};
			for(var filterName in this.added)
			{
				var item = this.added[filterName];

				switch(filterName)
				{
					case 'languages':
					case 'shops':
					case 'csv_name':
					case 'subscribed':
						filters[filterName] = item.getSelected();
						break;
				}

				// make the rest of cases
			}
			return filters;
		};
	};

}(jQueryNewsletterProNew));
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
	NewsletterPro.namespace('components.SyncNewsletters');
	NewsletterPro.components.SyncNewsletters = function SyncNewsletters(cfg)
	{
		if (!(this instanceof SyncNewsletters))
			return new SyncNewsletters(cfg);

		if (typeof cfg.connection === 'undefined')
			throw new Error('You must setup the connection.');

		var box = NewsletterPro,
			self = this;

		this.l               = box.translations.l(box.translations.components.SyncNewsletters);
		this.cfg             = cfg;
		this.connection      = cfg.connection;
		this.limit           = cfg.connection.limit || 2500;
		this.url             = cfg.connection.url;
		this.data            = cfg.data || {};
		this.syncErrorsLimit = cfg.syncErrorsLimit || 1000;
		this.selectors       = cfg.selectors || {};
		this.refreshRate     = cfg.refreshRate || 5000;

		this.data['limit'] = this.limit;

		this.subscription    = {};
		this.syncErrorsCount = 0;
		this.readyLength     = 0;
		this.sendId          = 0;

		this.syncInterval    = null;
		this.syncStart       = false;
	}

	NewsletterPro.components.SyncNewsletters.prototype.init = function()
	{
		// run the synchronisation
		this.sync();
	};

	NewsletterPro.components.SyncNewsletters.prototype.sync = function()
	{
		if (this.isSyncInProgress())
			return false;

		var self = this,
			sendManager = NewsletterPro.modules.sendManager,
			define = sendManager.define;

		self.publish('beforeRequest');

		this.syncInterval = setInterval(function(){
			self.getEmails(
				// success
				function(response, textStatus, jqXHR)
				{
					self.sendId = (response.id > 0 ? response.id : self.sendId);

					self.publish('emailsToSend', getEmailsToSendPublish(response, false));
					self.publish('emailsSent', getEmailsSentPublish(response, false));
					self.publish('progressbar', getProgressbarPublish(response, false));

					self.publish('syncSuccess', {
						response: response,
						textStatus: textStatus,
						jqXHR: jqXHR
					});

					if (!self.syncStart)
					{
						self.syncStart = true;

						self.publish('syncStart', {
							active: response.active,
							state: response.state,
						});
					}

					if (response.active && define.STATE_PAUSE == response.state)
					{
						self.publish('syncPause', {
							active: response.active,
							state: response.state,
						});

						self.clearSync();
					}
					else if (!response.active || define.STATE_DONE == response.state)
					{
						if (self.sendId)
						{
							$.postAjax({'submit': 'syncNewsletters', 'id': self.sendId, 'limit': self.limit}, 'json', false).done(function(response){

								self.sendId = 0;
								self.publish('emailsToSend', getEmailsToSendPublish(response, true));
								self.publish('emailsSent', getEmailsSentPublish(response, false));
								self.publish('progressbar', getProgressbarPublish(response, true));

								self.publish('syncEnd', {
									active: response.active,
									state: response.state,
								});

							}).fail(function(jqXHR, textStatus, errorThrown){
								addRequestError(jqXHR, textStatus, errorThrown);
							});	
						}

						self.publish('syncDone', {
							active: response.active,
							state: response.state,
						});

						self.clearSync();
					}
					else if (response.active && define.STATE_DEFAULT == response.state)
					{

						self.publish('syncContinue', {
							active: response.active,
							state: response.state,
						});

						sendManager.startSendNewsletters();
					}
				}, 
				// error 
				function(jqXHR, textStatus, errorThrown) 
				{
					addRequestError(jqXHR, textStatus, errorThrown);
				}
			);
		}, this.refreshRate);

		function getProgressbarPublish(response, completed)
		{
			completed = completed || false;

			return {
				errors: response.emails_error,
				success: response.emails_success,
				emailsCount: response.emails_count,
				done: (define.STATE_DONE == response.state),
				completed: completed,
			};
		}

		function getEmailsToSendPublish(response, completed)
		{
			completed = completed || false;

			return {
				remaining: response.remaining,
				emailsToSend: response.emails_to_send,
				completed: completed,
			};
		}

		function getEmailsSentPublish(response, completed)
		{
			completed = completed || false;

			return {
				success: response.emails_success,
				errors: response.emails_error,
				emailsSent: response.emails_sent,
				completed: completed,
			};
		}

		function addRequestError(jqXHR, textStatus, errorThrown)
		{
			self.syncErrorsCount++;

			var message,
				login = (jqXHR.getResponseHeader('Login') === 'true' ? true : false);
				alertErrors = false,
				display = false;

			if (login)
			{
				message = self.l('The login session has expired. You must refresh the browser and login again. The next time when you are login check the button "Stay logged in".');
				alertErrors = true;
				self.clearSync();
			}
			else
			{
				message = self.l('Error ocurred at newsletter synchronisation') + ' : ' + NewsletterPro.getXHRError(jqXHR);
				
				if (self.syncErrorsCount >= self.syncErrorsLimit)
				{
					alertErrors = true;
					self.clearSync();
				}
				else
					display = true;
			}

			self.publish('syncError', {
				message: message,
				alertErrors: alertErrors,
				display: display,
				jqXHR: jqXHR,
				textStatus: textStatus,
				errorThrown: errorThrown,
			});
		}

		return true;
	};

	NewsletterPro.components.SyncNewsletters.prototype.clearSync = function()
	{
		if (this.isSyncInProgress())
		{
			clearInterval(this.syncInterval);
			this.syncInterval = null;
			this.syncStart = false;
			return true;
		}
		return false;
	};

	NewsletterPro.components.SyncNewsletters.prototype.isSyncInProgress = function()
	{
		return (this.syncInterval != null);
	};

	NewsletterPro.components.SyncNewsletters.prototype.getEmails = function(success, error, complete)
	{
		return $.ajax({
			url: this.url,
			type: 'POST',
			dataType: 'json',
			data: this.data,
			success: function(data, textStatus, jqXHR)
			{
				if (typeof success === 'function')
					success(data, textStatus, jqXHR);
			},
			error: function(jqXHR, textStatus, errorThrown)
			{
				if (typeof error === 'function')
					error(jqXHR, textStatus, errorThrown);
			},
			complete: function(jqXHR, textStatus)
			{
				if (typeof complete === 'function')
					complete(jqXHR, textStatus);
			}
		}).promise();
	};

	NewsletterPro.components.SyncNewsletters.prototype.setData = function(name, value)
	{
		this.data[name] = value;
	};

	NewsletterPro.components.SyncNewsletters.prototype.subscribe = function(eventName, func, instance)
	{
		if (!this.subscription.hasOwnProperty(eventName))
			this.subscription[eventName] = [];

		this.subscription[eventName].push({
			func: func,
			instance: instance
		});

		if (this.cfg.hasOwnProperty('subscription') && this.cfg.subscription.hasOwnProperty('ready'))
		{
			if (this.cfg.subscription.ready.indexOf(eventName) != -1)
				this.readyLength++;

			if (this.readyLength == this.cfg.subscription.ready.length)
				this.init();
		}
	};

	NewsletterPro.components.SyncNewsletters.prototype.publish = function(eventName, data)
	{
		if (this.subscription.hasOwnProperty(eventName))
		{
			for (var i = 0; i < this.subscription[eventName].length; i++) {

				var result = (typeof data === 'function' ? data() : data);
				var func = this.subscription[eventName][i].func;
				var instance = this.subscription[eventName][i].instance || this;
				func.call(instance, result);
			}
		}
	};


}(jQueryNewsletterProNew));
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
	NewsletterPro.namespace('components.EmailsToSend');
	NewsletterPro.components.EmailsToSend = function EmailsToSend(cfg)
	{
		if (!(this instanceof EmailsToSend))
			return new EmailsToSend(cfg);

		if (typeof cfg.selector === 'undefined')
			throw new Error('You must define a selector.')
		
		var box  = NewsletterPro
			self = this;

		this.l = box.translations.l(box.translations.components.EmailsToSend);
		this.selector        = cfg.selector;
		this.fastPerformance = (typeof cfg.fastPerformance === 'undefined' ? true : cfg.fastPerformance);
		this.emails          = [];
		this.remainingEmails = 0;

		if (typeof cfg.subscription === 'undefined')
			throw new Error('You must set the subscription value.')

		this.subscription       = cfg.subscription;

		this.dom 			 = this.initDom();
		this.items           = [];

		box.extendSubscribeFeature(this);
		box.subscription(this, this.subscription);
	}

	NewsletterPro.components.EmailsToSend.prototype.sync = function(response)
	{
		var remaining = response.remaining,
			emailsToSend = response.emailsToSend,
			completed = response.completed;

		// this function need to be optimized

		if (this.fastPerformance)
			this.clearOptimized(emailsToSend);
		else
		{
			this.clear();
			this.createItems(emailsToSend);
		}

		this.writeRemainingEmails(remaining);
	};

	NewsletterPro.components.EmailsToSend.prototype.clear = function()
	{
		this.writeRemainingEmails(0);
		this.emails = [];
		this.items  = [];
		this.dom.list.empty();
	};

	NewsletterPro.components.EmailsToSend.prototype.writeRemainingEmails = function(value)
	{
		this.remainingEmails = parseInt(value);
		this.dom.remainingEmails.html(this.remainingEmails);
	};


	NewsletterPro.components.EmailsToSend.prototype.initDom = function()
	{
		this.selector.html(this.getTemplate());

		return {
			selector: this.selector,
			remainingEmails: this.selector.find('.emails-to-send-count'),
			list: this.selector.find('.userlist'),
		};
	};

	NewsletterPro.components.EmailsToSend.prototype.createItems = function(emails)
	{
		this.emails = emails;

		for(var i = 0; i < this.emails.length; i++) {
			this.createItem(this.emails[i]);
		}
	};

	NewsletterPro.components.EmailsToSend.prototype.createItemsOptimised = function(emailsData)
	{

		var dfd = new $.Deferred();

		for (var i = 0; i < emailsData.length; i++)
		{
			this.emails.push(emailsData[i]);
			this.createItem(emailsData[i], 'append');

			// this are inversed, there are not like createItem
			if (i == 0)
				this.publish('firstItemCreated', this.getItemByEmail(emailsData[i]));

			if (i == emailsData.length - 1)
			{
				this.publish('lastItemCreated', this.getItemByEmail(emailsData[i]));
				dfd.resolve();
			}
		}

		return dfd.promise();
	};

	NewsletterPro.components.EmailsToSend.prototype.createItem = function(email, creationMethod)
	{
		var creationMethod = creationMethod || 'append', // append or prepend
			className = this.getNextClassName(creationMethod);

		return ({
			template: null,
			parent: null,
			className: className,
			email: email,
			creationMethod: creationMethod,
			init: function(parent)
			{
				this.parent = parent;
				this.template = this.getItemTemplate();
				this.add();

				if (creationMethod === 'append')
					parent.items.push(this);
				else
					parent.items.unshift(this);	
			},
			add: function()
			{
				this.parent.dom.list[this.creationMethod](this.template);
			},
			render: function()
			{
				if (!this.parent.dom.list.find('[data-email="'+this.email+'"]'))
					this.add();
			},
			remove: function()
			{
				this.template.remove();
				var index = this.parent.emails.indexOf(this.email);
				if (index != -1)
				{
					this.parent.emails.splice(index, 1);
					this.parent.items.splice(index, 1);
					return true;
				}
				return false;

			},
			getItemTemplate: function()
			{
				// classes "odd" or "even"
				return $('\
					<li class="'+this.className+'" data-email="'+this.email+'">\
						<span class="email_text">'+this.email+'</span>\
						<span>&nbsp;</span>\
					</li>\
				');
			},
			getClassName: function()
			{
				return this.className;
			}
		}.init(this));
	};

	NewsletterPro.components.EmailsToSend.prototype.clearOptimized = function(newEmails)
	{
		var self = this,
			addEmails = [],
			removeEmails = [],
			oldEmails = self.emails;

		for(var i = 0; i < newEmails.length; i++)
		{
			if (oldEmails.indexOf(newEmails[i]) == -1)
				addEmails.push(newEmails[i]);
		}

		for(var i = 0; i < oldEmails.length; i++)
		{
			if (newEmails.indexOf(oldEmails[i]) == -1)
				removeEmails.push(oldEmails[i]);
		}

		self.removeEmails(removeEmails).done(function(){
			self.createItemsOptimised(addEmails);
		});
	};

	NewsletterPro.components.EmailsToSend.prototype.removeEmails = function(emails)
	{
		var dfd = new $.Deferred();

		var indexes = [];
		for(var i = 0; i < emails.length; i++)
		{
			var index = this.emails.indexOf(emails[i]);
			if (index != -1)
				indexes.push(index);
		}

		indexes.sort();

		if (indexes.length)
		{
			for(var i = indexes.length - 1; i >= 0; i--)
			{
				var item = this.items[indexes[i]];

				if (typeof item !== 'undefined')
					item.remove();


				if (i == 0)
					dfd.resolve();
			}
		}
		else
			dfd.resolve();

		return dfd.promise();
	};

	NewsletterPro.components.EmailsToSend.prototype.getNextClassName = function(creationMethod)
	{
		var className,
			len = this.items.length,
			creationMethod = creationMethod || 'append';

		if (len)
		{
			if (creationMethod == 'append')
				className = this.items[len - 1].getClassName();
			else
				className = this.items[0].getClassName();
		}
		else
			className = 'even';

		return (className == 'odd' ? 'even' : 'odd');
	};

	NewsletterPro.components.EmailsToSend.prototype.getTemplate = function()
	{
		return $('\
			<label class="control-label">'+this.l('Emails to send:')+' <span class="emails-to-send-count">0</span> '+this.l('remaining')+'</label>\
			<div class="clear">&nbsp;</div>\
			<ul class="userlist"></ul>\
			<div class="clear">&nbsp;</div>\
		');
	};

	NewsletterPro.components.EmailsToSend.prototype.getIndex = function(email)
	{
		return this.emails.indexOf(email);
	};

	NewsletterPro.components.EmailsToSend.prototype.getItemByEmail = function(email)
	{
		var index = this.getIndex(email);
		if (index != -1)
			return this.items[index];
		return false;
	};

	NewsletterPro.components.EmailsToSend.prototype.removeItemByEmail = function(email)
	{
		var item = this.getItemByEmail(email);
		if (item)
			return item.remove();
		return false;
	};
}(jQueryNewsletterProNew));
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
	NewsletterPro.namespace('components.EmailsSent');
	NewsletterPro.components.EmailsSent = function EmailsSent(cfg)
	{
		if (!(this instanceof EmailsSent))
			return new EmailsSent(cfg);

		if (typeof cfg.selector === 'undefined')
			throw new Error('You must define a selector.')
		
		var box  = NewsletterPro
			self = this;

		this.l               = box.translations.l(box.translations.components.EmailsSent);
		this.selector        = cfg.selector;
		this.fastPerformance = (typeof cfg.fastPerformance === 'undefined' ? true : cfg.fastPerformance);
		this.emailsData      = [];
		this.emails          = [];
		this.sentSuccess     = 0;
		this.sentErrors      = 0;

		if (typeof cfg.subscription === 'undefined')
			throw new Error('You must set the subscription value.')

		this.subscription       = cfg.subscription;

		this.dom 			 = this.initDom();
		this.items           = [];

		box.extendSubscribeFeature(this);
		box.subscription(this, this.subscription);

		this.windowErrors = new gkWindow({
			width: 640,
			height: 400,
			setScrollContent: 340,
			title: this.l('Sent errors'),
			className: 'np-emails-sent-window-error',
			show: function(win) {},
			close: function(win) {},
			content: function(win) 
			{
				return '';
			}
		});
	}

	NewsletterPro.components.EmailsSent.prototype.sync = function(response)
	{
		var success = response.success,
			errors = response.errors,
			emailsSent = response.emailsSent,
			completed = response.completed;

		if (this.fastPerformance)
			this.clearOptimized(emailsSent);
		else
		{
			this.clear();
			this.createItems(emailsSent);
		}

		this.writeSentSuccess(success);
		this.writeSentErrors(errors);
	};

	NewsletterPro.components.EmailsSent.prototype.clear = function()
	{
		this.writeSentSuccess(0);
		this.writeSentErrors(0);
		this.emailsData = [];
		this.emails     = [];
		this.items      = [];
		this.dom.list.empty();
	};

	NewsletterPro.components.EmailsSent.prototype.clearOptimized = function(newItems)
	{
		var self = this,
			addItems = [],
			removeEmails = [],
			newEmails = [],
			oldEmails = self.emails;

		for(var i = 0; i < newItems.length; i++)
		{
			newEmails.push(newItems[i].email);

			if (oldEmails.indexOf(newItems[i].email) == -1)
				addItems.push(newItems[i]);
		}

		for(var i = 0; i < oldEmails.length; i++)
		{
			if (newEmails.indexOf(oldEmails[i]) == -1)
				removeEmails.push(oldEmails[i]);
		}

		if (newEmails.length > 0)
		{
			self.removeItems(removeEmails).done(function(){
				self.createItemsOptimised(addItems);
			});
		}
	};

	NewsletterPro.components.EmailsSent.prototype.writeSentSuccess = function(value)
	{
		this.sentSuccess = parseInt(value);
		this.dom.sentSuccess.html(this.sentSuccess);
	};

	NewsletterPro.components.EmailsSent.prototype.writeSentErrors = function(value)
	{
		this.sentErrors = parseInt(value);
		this.dom.sentErrors.html(this.sentErrors);
	};

	NewsletterPro.components.EmailsSent.prototype.initDom = function()
	{
		this.selector.html(this.getTemplate());

		return {
			selector: this.selector,
			sentSuccess: this.selector.find('.emails-sent-count-succ'),
			sentErrors: this.selector.find('.emails-sent-count-err'),
			list: this.selector.find('.userlist'),
		};
	};

	NewsletterPro.components.EmailsSent.prototype.removeItems = function(emails)
	{
		var dfd = new $.Deferred();

		var indexes = [];
		for(var i = 0; i < emails.length; i++)
		{
			var index = this.emails.indexOf(emails[i]);
			if (index != -1)
				indexes.push(index);
		}

		indexes.sort();

		if (indexes.length)
		{
			for(var i = indexes.length - 1; i >= 0; i--)
			{
				var item = this.items[indexes[i]];

				if (typeof item !== 'undefined')
					item.remove();


				if (i == 0)
					dfd.resolve();
			}
		}
		else
			dfd.resolve();

		return dfd.promise();
	};

	NewsletterPro.components.EmailsSent.prototype.createItems = function(emailsData)
	{
		this.emailsData = emailsData;

		for(var i = 0; i < this.emailsData.length; i++) 
		{
			var data = this.emailsData[i];
			this.emails.push(data.email);
			this.createItem(data);

			if (i == 0)
				this.publish('firstItemCreated', this.getItemByEmail(this.emailsData[i].email));

			if (i == this.emailsData.length - 1)
				this.publish('lastItemCreated', this.getItemByEmail(this.emailsData[i].email));
		}
	};

	NewsletterPro.components.EmailsSent.prototype.createItemsOptimised = function(emailsData)
	{
		var dfd = new $.Deferred();

		for (var i = emailsData.length - 1; i >= 0; i--)
		{
			this.emails.unshift(emailsData[i].email);
			this.emailsData.unshift(emailsData[i]);

			this.createItem(emailsData[i], 'prepend');

			if (i == emailsData.length - 1)
				this.publish('firstItemCreated', this.getItemByEmail(emailsData[i].email));

			if (i == 0)
			{
				this.publish('lastItemCreated', this.getItemByEmail(emailsData[i].email));
				dfd.resolve();
			}
		}

		return dfd.promise();
	};

	NewsletterPro.components.EmailsSent.prototype.createItem = function(data, creationMethod)
	{
		var creationMethod = creationMethod || 'append', // append or prepend
			className      = this.getNextClassName(creationMethod),
			email          = data.email,
			status         = data.status,
			fwd            = data.fwd,
			errors         = data.errors;

		return ({
			template: null,
			parent: null,
			className: className,
			email: email,
			status: status,
			fwd : fwd,
			errors: errors,
			creationMethod: creationMethod,
			init: function(parent)
			{
				var self = this;

				this.parent = parent;
				this.template = this.getItemTemplate();

				this.btnErrors = this.template.find('.np-emails-sent-error');

				this.btnErrors.on('click', function(){
					if (self.errors.length)
					{
						var msg = '';
							msg += '<span style="font-weight: bold;">' + self.email + '</span> <br>';
							msg += '<br>' + self.errors.join('<br>');

						self.parent.windowErrors.setContent(msg);
						self.parent.windowErrors.show();
					}
				});

				this.add();

				if (creationMethod === 'append')
					parent.items.push(this);
				else
					parent.items.unshift(this);
			},
			add: function()
			{
				this.parent.dom.list[this.creationMethod](this.template);
			},
			render: function()
			{
				if (!this.parent.dom.list.find('[data-email="'+this.email+'"]'))
					this.add();
			},
			remove: function()
			{
				this.template.remove();
				var index = this.parent.emails.indexOf(this.email);
				if (index != -1)
				{
					this.parent.emails.splice(index, 1);
					this.parent.items.splice(index, 1);
					this.parent.emailsData.splice(index, 1);
					return true;
				}
				return false;

			},
			getItemTemplate: function()
			{
				var successIcon = '<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>',
					errorIcon = '<span class="list-action-enable action-disabled"><i class="icon icon-remove"></i></span>';

				return $('\
					<li class="'+this.className+'" data-email="'+this.email+'">\
						<span class="np-emails-sent-email">' + this.email + '</span>\
						<span class="status np-emails-sent-status">\
						'+(this.status ? successIcon : errorIcon)+'\
						</span>\
						<a href="javascript:{}" class="np-emails-sent-error" style="'+(this.errors.length > 0 ? 'display: inline-block;' : 'display: none;')+'"></a>\
						<span class="np-emails-sent-fwd" style="'+(this.fwd > 0 ? 'display: inline-block;' : 'display: none;')+'"> + ' + this.fwd + ' '+this.parent.l('forwarders')+'</span>\
						<div class="clear" style="clear: both;"></div>\
					</li>\
				');
			},
			getClassName: function()
			{
				return this.className;
			}
		}.init(this));
	};

	NewsletterPro.components.EmailsSent.prototype.getNextClassName = function(creationMethod)
	{
		var className,
			len = this.items.length,
			creationMethod = creationMethod || 'append';

		if (len)
		{
			if (creationMethod == 'append')
				className = this.items[len - 1].getClassName();
			else
				className = this.items[0].getClassName();
		}
		else
			className = 'even';

		return (className == 'odd' ? 'even' : 'odd');
	};

	NewsletterPro.components.EmailsSent.prototype.getTemplate = function()
	{
		return $('\
			<label class="control-label">'+this.l('Emails sent:')+' <span class="emails-sent-count-succ" style="color: green;">0</span> ' + this.l('sent') + ', <span class="emails-sent-count-err" style="color: red;">0</span> ' + this.l('errors') + '</label>\
			<div class="clear">&nbsp;</div>\
			<ul class="userlist"></ul>\
			<div class="clear">&nbsp;</div>\
		');
	};

	NewsletterPro.components.EmailsSent.prototype.getIndex = function(email)
	{
		return this.emails.indexOf(email);
	};

	NewsletterPro.components.EmailsSent.prototype.getItemByEmail = function(email)
	{
		var index = this.getIndex(email);
		if (index != -1)
			return this.items[index];
		return false;
	};

	NewsletterPro.components.EmailsSent.prototype.removeItemByEmail = function(email)
	{
		var item = this.getItemByEmail(email);
		if (item)
			return item.remove();
		return false;
	};
}(jQueryNewsletterProNew));
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
	NewsletterPro.namespace('components.SendProgressbar');
	NewsletterPro.components.SendProgressbar = function SendProgressbar(cfg)
	{
		if (!(this instanceof SendProgressbar))
			return new SendProgressbar(cfg);

		var box  = NewsletterPro,
			self = this,
			winTimer = null;

		if (!cfg.hasOwnProperty('selector'))
			throw new Error('You must define the selector.');

		this.selector = cfg.selector;

		if (!cfg.hasOwnProperty('subscription'))
			throw new Error('You must set the subscription value.')

		this.subscription = cfg.subscription;

		this.percent     = 0;
		this.sent        = 0;
		this.total       = 0;
		this.sentErrors  = 0;
		this.sentSuccess = 0;
		this.pause       = false;

		box.subscription(this, this.subscription);

		this.selector.html(getTemplate());

		this.dom = {
			selector: this.selector,
			bg: this.selector.find('.np-send-progressbar-bg'),
			bar: this.selector.find('.np-send-progressbar-bar'),
			percent: this.selector.find('.np-send-progressbar-percent'),
			totalBox: this.selector.find('.np-send-progressbar-total-box'),
			totalLeft: this.selector.find('.np-send-progressbar-total-left'),
			totalRight: this.selector.find('.np-send-progressbar-total-right'),
			errors: this.selector.find('.np-send-progressbar-error'),
			success: this.selector.find('.np-send-progressbar-success'),
			errSuccBox: this.selector.find('.np-send-progressbar-err-succ-box'),
		};

		this.selector.addClass('np-send-progressbar');


		$(window).resize(function(){

			if (winTimer !== null)
				clearTimeout(winTimer);

			winTimer = setTimeout(function(){
				self.refresh();
			}, 500);
		});
	}

	NewsletterPro.components.SendProgressbar.prototype.clear = function()
	{
		this.setPercent(0);
		this.setTotal(0);
		this.setSent(0);
		this.setSentErrors(0);
		this.setSentSuccess(0);
	};

	NewsletterPro.components.SendProgressbar.prototype.sync = function(response)
	{	
		var errors = Number(response.errors),
			success = Number(response.success),
			emailsCount = Number(response.emailsCount);
			done = response.done,
			completed = response.completed,
			total = Number(errors + success);

		if (!done || completed)
			this.set(total, emailsCount, errors, success);
	};

	NewsletterPro.components.SendProgressbar.prototype.set = function(sent, total, errors, success)
	{
		if (sent > total)
			sent = total;

		var percent = Number(sent / total) * 100;
		this.setPercent(percent);
		this.setTotal(total);
		this.setSent(sent);
		this.setSentErrors(errors);
		this.setSentSuccess(success);
	};

	NewsletterPro.components.SendProgressbar.prototype.setPause = function(value)
	{
		this.pause = Boolean(value);

		if (this.isPause())
			this.dom.bar.addClass('np-send-progressbar-bar-pause');
		else
			this.dom.bar.removeClass('np-send-progressbar-bar-pause');
	};

	NewsletterPro.components.SendProgressbar.prototype.isPause = function()
	{
		return this.pause;
	};

	NewsletterPro.components.SendProgressbar.prototype.get = function()
	{
		return this.percent;
	};

	NewsletterPro.components.SendProgressbar.prototype.refresh = function()
	{
		this.setPercent(this.percent);
	};

	NewsletterPro.components.SendProgressbar.prototype.setPercent = function(value)
	{
		var self = this;

		this.percent = value;
		this.dom.percent.html(parseFloat(value).toFixed(2)+'%');

		var selectorWidth = self.selector.width(),
			percentWidth = self.dom.percent.width(),
			leftPadding = 10,
			percentPercent = ((percentWidth + leftPadding) / selectorWidth) * 100,
			percentLeft = value - percentPercent;

		self.dom.percent.animate({
			'left': (percentLeft) + '%'
		}, {
			progress: function()
			{
				percentProgress();
			},

			complete: function()
			{
				percentProgress();
			},
			queue: false,
		});

		var totalBoxPadding = (leftPadding / selectorWidth) * 100,
			totalBoxMarginLeft = (percentWidth <= 5 ? 5 : 0);

		self.dom.totalBox.animate({
			'left': (value + totalBoxPadding) + '%',
			'margin-left': totalBoxMarginLeft + 'px',
		}, {
			queue: false,
		});

		this.dom.bar.animate({
			'width': value+'%'
		}, {
			progress: function() 
			{
				setPositions();
			},

			complete: function()
			{
				setPositions();
			},
			queue: false,
		});

		function percentProgress()
		{
			if (percentLeft > 0)
			{
				self.dom.percent.css({
					'opacity': 1
				});
			}
			else
			{
				self.dom.percent.css({
					'opacity': 0
				});
			}
		}

		function setPositions()
		{
			// hide elements on the screen
			var errSuccPosition = self.dom.errSuccBox.position().left,
				errSuccWidth = self.dom.errSuccBox.width(),
				totalPosition = self.dom.totalBox.position().left + self.dom.totalBox.width(),
				selectorWidth = self.selector.width(),
				barWidth = self.dom.bar.width(),
				leftSpace = selectorWidth - barWidth;

			if (totalPosition >= errSuccPosition)
			{
				self.dom.totalBox.css({
					'opacity': 0
				});
			}
			else
			{
				self.dom.totalBox.css({
					'opacity': 1
				});
			}

			if (leftSpace <= errSuccWidth)
			{
				var percentLeft = self.dom.percent.position().left,
					errSuccWidth = self.dom.errSuccBox.width(),
					errSuccBoxLeft = ((percentLeft - errSuccWidth - 10) / selectorWidth) * 100;

				self.dom.errSuccBox.css({
					'right': 'initial',
					'left': errSuccBoxLeft + '%',
				});
			}
			else
			{
				self.dom.errSuccBox.css({
					'right': '5px',
					'left': 'initial',
				});
			}
		}
	};

	NewsletterPro.components.SendProgressbar.prototype.setTotal = function(value)
	{
		this.total   = value;
		this.dom.totalRight.html(value);
	};

	NewsletterPro.components.SendProgressbar.prototype.setSent = function(value)
	{
		this.sent    = value;
		this.dom.totalLeft.html(value);
	};

	NewsletterPro.components.SendProgressbar.prototype.setSentErrors = function(value)
	{
		this.sentErrors = value;
		this.dom.errors.html(value);
	};

	NewsletterPro.components.SendProgressbar.prototype.setSentSuccess = function(value)
	{
		this.sentSuccess = value;
		this.dom.success.html(value);
	};



	function getTemplate()
	{
		return $('\
			<div class="np-send-progressbar-bg"></div>\
			<div class="np-send-progressbar-bar"></div>\
			<div class="np-send-progressbar-percent"></div>\
			<div class="np-send-progressbar-total-box">\
				<span><span class="np-send-progressbar-total-left">0</span> <span>/</span> <span class="np-send-progressbar-total-right">0</span></span>\
			</div>\
			<div class="np-send-progressbar-err-succ-box">\
				<div class="np-send-progressbar-error">0</div>\
				<div class="np-send-progressbar-success">0</div>\
				<div class="clear" style="clear: both;"></div>\
			</div>\
		');
	}
}(jQueryNewsletterProNew));
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

NewsletterPro.namespace('modules.settings');
NewsletterPro.modules.settings = ({
	dom: null,
	box: null,
	init: function(box) 
	{
		var self = this,
			l,
			topMenuClass;

		self.box = box;

		self.ready(function(dom) {
			self.dom = dom;
			l = self.l = NewsletterPro.translations.l(NewsletterPro.translations.modules.settings);

			topMenuClass = {
				'np-menu-top-bg' : dom.npMenuTopBg,
				'np-menu-top' : dom.newsletterproTab,
				'np-menu-top-content': dom.newsletterproTabContent,
			};

			var dataStorage = box.dataStorage;

			function isPS16() 
			{
				return dataStorage.data.isPS16;
			}

			function refershCurrentTab() 
			{
				if (typeof NewsletterProComponents.objs.tabItems !== 'undefined' && NewsletterProComponents.objs.tabItems != null) {
					var tab = NewsletterProComponents.objs.tabItems.lastItem,
						id = tab.attr('id');

					if (id === 'tab_newsletter_5') {
						NewsletterPro.modules.sendNewsletters.resetButtons();
					} else if (id === 'tab_newsletter_3') {
						NewsletterPro.modules.selectProducts.refreshSliders();
					} else if (id === 'tab_newsletter_4') 
					{

						if ( typeof NewsletterPro.modules.createTemplate.dom.tinyNewsletter !== 'undefined') 
						{
							NewsletterPro.modules.createTemplate.updateBoth();
						}
						
						NewsletterPro.modules.createTemplate.refreshSliders();
					}
				}
			}

			function getPadding()
			{
				 return dom.noBootstrap.innerWidth() - dom.noBootstrap.width();
			}

			function isLeftMenuActive()
			{
				return parseInt(box.dataStorage.get('left_menu_active'));
			}

			function run() 
			{
				setTimeout(function(){
					// add colde here
					refershCurrentTab();
				}, 100);
			}

			if (isPS16()) 
			{
				dom.menuCollapse.on('click', function(event){
					run();
				});

				$(window).resize(function(){
					run();
				});
				run();
			}

			dom.moduleCreateBackupButton.on('click', function(){
				var name = prompt(l('backup name'));
				if (!$.trim(name))
					return false;

				var that = this;
				box.showAjaxLoader($(this));

				$.postAjax({'submit': 'ajaxCreateBackup', 'name': name}).done(function(response){
					if (response.status)
					{
						box.alertErrors(response.msg)
						if (typeof self.loadBackupDataSource !== 'undefined')
							self.loadBackupDataSource.sync();
					}
					else
						box.alertErrors(response.errors.join('\n'));
				}).always(function(){
					box.hideAjaxLoader($(that));
				});
			});

			dom.moduleLoadBackupButton.on('click', function(){
				self.showLoadBackup();
			});

			function activateLeftMenu()
			{

				dom.npLeftSize.removeClass('col-sm-12');
				dom.npRightSize.removeClass('col-sm-12');

				dom.npLeftSize.addClass('col-sm-2');
				dom.npRightSize.addClass('col-sm-10');

				box.dataStorage.set('left_menu_active', 1);

				var tmt = dom.menuToggle.find('.toggle-menu-text');

				if (tmt.length)
					tmt.html(l('Top Menu'));

				for (var className in topMenuClass)
				{
					var obj = topMenuClass[className];
					if (obj.hasClass(className))
						obj.removeClass(className);
				}
			}

			function activateTopMenu()
			{
				dom.npLeftSize.removeClass('col-sm-2');
				dom.npRightSize.removeClass('col-sm-10');

				dom.npLeftSize.addClass('col-sm-12');
				dom.npRightSize.addClass('col-sm-12');

				box.dataStorage.set('left_menu_active', 0);
			
				var tmt = dom.menuToggle.find('.toggle-menu-text');


				if (tmt.length)
					tmt.html(l('Left Menu'));

				for (var className in topMenuClass)
				{
					var obj = topMenuClass[className];
					if (!obj.hasClass(className))
						obj.addClass(className);
				}
			}

			function toggleLeftTopMenu()
			{
				if (isLeftMenuActive())
					activateTopMenu();
				else
					activateLeftMenu();
			}

			if (isPS16())
			{
				dom.menuToggle.on('click', function(){

					toggleLeftTopMenu();

					run();
					
					$.postAjax({'submit': 'leftMenuActive', leftMenuActive : parseInt(box.dataStorage.get('left_menu_active'))}).done(function(response) {

						if (!response.status)
							box.alertErrors(response.errors);
					});
					

				});
			}

			dom.topShortcuts.on('change', function(e){
				var item = $(e.currentTarget),
					name = item.val(),
					value = Number(item.is(':checked'));
					
					$.postAjax({'submit': 'updateTopShortcuts', name: name, value: value}).done(function(response) {
						if (!response.status)
							box.alertErrors(response.errors);
						else
						{
							if (value)
								location.reload();
							else
								$('[id^=page-header-desc-configuration-'+name.toLowerCase()+']').hide();
						}
					});
			});

			var logWindows = new gkWindow({
				width: 800,
				height: 500,
				setScrollContent: 438,
				title: l('Log'),
			});

			dom.openLogFIle.on('click', function(e){
				e.preventDefault();

				var btn = $(this),
					href = btn.attr('href');

				box.showAjaxLoader(btn);
				$.postAjax({'submit': 'openLogFIle', filename: href}).done(function(response){
					if (!response.success)
						box.alertErrors(response.errors);
					else
					{
						logWindows.show('<pre class="np-log-display">'+response.content+'</pre>');
					}
				}).always(function(){
					box.hideAjaxLoader(btn);
				})
			});

			dom.clearModuleCache.on('click', function(){
				$.postAjax({'submit': 'clearModuleCache'}).done(function(response) {
					if (response.success) {
						location.reload();
					} else {
						box.alertErrors(response.errors);
					}
				});
			});

		}); // end of ready

		return this;
	},

	newsletterproSubscriptionOption: function(elem)
	{
		var self = this,
			dom = self.dom,
			isNewsletterPro = parseInt(elem.val()),
			targets = [dom.npSubscribeOptions];

		$.each(targets, function(i, target){
			if (isNewsletterPro)
				target.stop().slideDown();
			else
				target.stop().slideUp();
		});
	},

	newsletterproSubscriptionActive: function()
	{
		var box = NewsletterPro;
		var value = $('input[name="newsletterproSubscriptionActive"]:checked').val();
		var hooks_selector = $('input[name^="hook_"]');
		var hooks = [];

		if (hooks_selector.length > 0)
		{
			hooks = $.map(hooks_selector, function(value, i){
				value = $(value);
				if (value.is(':checked'))
					return value.val();
			});
		}

		$.postAjax({'submit': 'newsletterproSubscriptionActive', newsletterproSubscriptionActive : value, hooks: hooks}).done(function(response) {
			if ( response.status )
				location.reload();
			else
				box.alertErrors(response.errors);
		});
	},

	subscriptionSecureSubscribe: function(elem)
	{
		$.postAjax({'submit': 'subscriptionSecureSubscribe', subscriptionSecureSubscribe : elem.val()}).done(function(response) {
			if ( response.status )
				location.reload();
		});
	},

	importEmailsFromBlockNewsletter: function(elem)
	{
		var box = NewsletterPro;
		box.showAjaxLoader(elem);

		$.postAjax({'submit': 'importEmailsFromBlockNewsletter'}).done(function(response) {
			if (!response.status)
				box.alertErrors(response.errors);
			else
				alert(box.displayAlert(response.msg));

		}).always(function(){
			box.hideAjaxLoader(elem);
		});
	},

	clearSubscribersTemp: function(elem)
	{
		var box = NewsletterPro;
		box.showAjaxLoader(elem);

		$.postAjax({'submit': 'clearSubscribersTemp'}).done(function(response) {
			if (!response.status)
				box.alertErrors(response.errors);
			else
				alert(box.displayAlert(response.msg));
		}).always(function(){
			box.hideAjaxLoader(elem);
		});
	},

	clearLogFiles: function(elem)
	{
		var box = NewsletterPro;
		box.showAjaxLoader(elem);

		$.postAjax({'submit': 'clearLogFiles'}).done(function(response) {
			if (!response.status)
				box.alertErrors(response.errors);
			else
				alert(box.displayAlert(response.msg));
		}).always(function(){
			box.hideAjaxLoader(elem);
		});
	},

	showLoadBackup: function()
	{
		var self = this,
			l = self.l,
			box = NewsletterPro;

		if (typeof self.loadBackupWindow === 'undefined')
		{
			var content,
				dataModel,
				dataSource,
				dataGrind;

			self.loadBackupWindow = new gkWindow({
				width: 800,
				height: 500,
				title: l('load backup'),
				content: function(win)
				{
					$.postAjax({'submit': 'showLoadBackup'}, 'html').done(function(response) {
						win.setContent(response);

						content = win.getContent();
						content.css({
							'padding': 0,
						});

						dataGrind = content.find('#load-global-templates');

						dataModel = new gk.data.Model({
							id: 'id',
						});

						dataSource = self.loadBackupDataSource = new gk.data.DataSource({
							pageSize: 9,
							transport: {
								read: {
									url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=ajaxGetBackup',
									dataType: 'json',
								},

								destroy: {
									url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=ajaxDeleteBackup&id',
									type: 'POST',
									dateType: 'json',
									success: function(response, itemData) {

										if(!response.status) {
											alert(response.errors.join("\n"));
										}
									},
									error: function(data, itemData) {
										alert(l('delete record error'));
									},
									complete: function(data, itemData) {},
								},					
							},
							schema: {
								model: dataModel
							},
							trySteps: 2,
							errors: 
							{
								read: function(xhr, ajaxOptions, thrownError) 
								{
									dataSource.syncStepAvailableAdd(3000, function(){
										dataSource.sync();
									});
								},
							},
						});

						dataGrind.gkGrid({
							dataSource: dataSource,
							selectable: false,
							currentPage: 1,
							pageable: true,
							template: {
								actions: function(item) 
								{
									var contentBox = $('<div style="text-align: center;"></div>');
									var deleteRecord = $('#delete-backup-global')
										.gkButton({
											name: 'delete',
											title: l('delete'),
											className: 'btn btn-default btn-margin pull-right ',
											click: function(e)
											{
												if (!confirm(l('delete record confirm')))
													return false;

												item.destroy('status');
											},
											icon: '<i class="icon icon-trash-o"></i> ',
										});

									var loadRecord = $('#load-backup-global')
										.gkButton({
											name: 'load',
											title: l('load'),
											className: 'btn btn-default btn-margin pull-right ',
											click: function(e)
											{	
												var conf = confirm(l('load backup confirm'));
												if (!conf || conf == '')
													return false;

												box.showAjaxLoader(loadRecord);

												$.postAjax({'submit': 'ajaxLoadBackup', name: item.data.name}).done(function(response) {
													if (response.status)
													{
														box.alertErrors(response.msg);
														window.location.reload();
													}
													else
														box.alertErrors(response.errors.join('\n'))

												}).always(function(){
													box.hideAjaxLoader(loadRecord);
												});
											},
											icon: '<span class="btn-ajax-loader" style="margin-top: 4px;"></span> <i class="icon icon-upload"></i> ',
										});

									contentBox.append(loadRecord);
									contentBox.append(deleteRecord);

									return contentBox;
								}
							},
						});
					});
				}
			});

			self.loadBackupWindow.show();
		}
		else 
			self.loadBackupWindow.show();

		if (typeof self.loadBackupDataSource !== 'undefined')
			self.loadBackupDataSource.sync();
	},

	ready: function(func) 
	{
		var self = this;
		$(document).ready(function(){
			var navSlider = $('#nav-sidebar');
			self.dom = {
				newsletterpro: $('#newsletterpro'),

				npLeftSize: $('#np-left-side'),
				npRightSize: $('#np-right-side'),

				navSlider: navSlider,
				menuCollapse: navSlider.find('.menu-collapse'),
				alertDanger: $('.alert.alert-danger'),
				noBootstrap: $('#content.nobootstrap'),

				npMenuTopBg: $('#np-menu-top-bg'),
				newsletterproTab: $('#tab.newsletter'),
				newsletterproTabContent: $('#tab_content.newsletter'),

				menuToggle: $('#menu-toggle'),
				pageHead: $('.page-head'),


				npSubscribeSettings: $('#newsletter-pro-subscribe-settings'),
				bnSubscribeSettings: $('#block-newsletter-subscribe-settings'),

				npSubscribeOptions: $('#newsletter-pro-subscribe-options'),

				moduleCreateBackupButton: $('#module-create-backup-button'),
				moduleLoadBackupButton: $('#module-load-backup-button'),
				topShortcuts: $('#np-top-shortcuts input'),
				openLogFIle: $('.np-btn-open-log-file'),

				clearModuleCache: $('#pqnp-clear-module-cache'),
			};
			func(self.dom);
		});
	},
}.init(NewsletterPro));
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

NewsletterPro.namespace('modules.syncTask');
NewsletterPro.modules.syncTask = ({
	speed: {
		slow: 60,
		normal: 45,
		fast: 15,
	},
	dom: null,
	box: null,
	interval: null,
	progressIds: [],

	init: function(box) {
		var self = this;
		self.box = box;

		self.ready(function(dom){

			var taskList,
				taskHistory,
				sendHistory,
				progressIds = self.progressIds || [],
				removedIds = [];

			function updateRow(id_task, data) 
			{
				taskList = taskList || NewsletterPro.modules.task.ui.components.taskList;
				taskHistory = taskHistory || NewsletterPro.modules.task.ui.components.taskHistory;

				var pIndex = progressIds.indexOf(id_task),
					rIndex = removedIds.indexOf(id_task);
				if (pIndex == -1 && rIndex == -1)
					progressIds.push(id_task);

				if (typeof taskList !== 'undefined') {
					taskList.syncDataById(id_task, data);
				}
			}

			function done(done) 
			{
				if (done.length > 0) {
					$.each(done, function(index, data) {
						var id_task = data.id_newsletter_pro_task,
							isDone = parseInt(data.done) ? true : false;

						 if (isDone) {
							var item = taskList.getItemById(id_task);
							if (item) {
							 	taskList.sync();
							 	taskHistory.sync();
							}
						 }
					});
					taskList.refreshView();
				}
			}

			function progress(progress) 
			{
				if (progress.length > 0) {
					self.runInterval(self.speed.fast);
					$.each(progress, function(index, data) {
						var id_task = data.id_newsletter_pro_task;
						updateRow(id_task, data);
					});
				} else {
					self.runInterval(self.speed.normal);
				}
			}

			self.updateRow = function(id_task, data) {
				updateRow(id_task, data);
			};

			self.syncAjax = function () {
				taskList = taskList || NewsletterPro.modules.task.ui.components.taskList;
				taskHistory = taskHistory || NewsletterPro.modules.task.ui.components.taskHistory;

				$.postAjax({'submit': 'getTasksInProgress', getTasksInProgress: true, progressIds: progressIds}, 'json', false).done(function(response) {
					if (typeof response === 'object') 
					{
						progress(response.result);
						done(response.result_look);
					} 
					else if (response > 0 && response !== taskList.items.length) 
					{
						taskList.sync();
					 	taskHistory.sync();
					}
				});
			};

			self.runInterval(self.speed.normal);
		});

		return self;
	},

	runInterval: function(seconds) {
		var self = this;

		if (self.interval != null)
			clearInterval(self.interval);

		self.interval = setInterval(function() {
			self.syncAjax();
		}, 1000 * seconds);
	},

	ready: function(func) {
		var self = this;

		$(document).ready(function(){
			self.dom = {};
			func(self.dom)
		});
	},

}.init(NewsletterPro));

NewsletterPro.namespace('modules.task');
NewsletterPro.modules.task = ({
	storage: null,
	dom: null,
	box: null,
	init: function(box) {
		var self = this,
			syncTask = NewsletterPro.modules.syncTask;

		self.box = box;

		self.ready(function(dom) {
			var l = NewsletterPro.translations.l(NewsletterPro.translations.modules.task);

			self.initStorage();

			dom.taskButton.on('click', function() {
				self.taskClick.call(dom.taskButton, self);
			});

			function getOptions(opt) {
				opt = $.extend(true, {}, opt);
				var options = $.map(opt, function(option){
					var obj = {name: option.name, value: option.id_newsletter_pro_smtp, data: option};
					if (option.hasOwnProperty('selected')) {
						delete option['selected'];
						obj['selected'] = true;
					}
					return obj;
				});
				return options;
			}

			function setSelected(options, obj) {
				var key = obj.key,
					value = obj.value,
					selectedSet = false,
					name;

				for (name in options) {
					var option = options[name];

					if (option.data.hasOwnProperty(key) && option.data[key] === value) {
						option['selected'] = true;
						selectedSet = true;
					} else if (option.hasOwnProperty('selected')) {
						delete option['selected'];
					}
				}

				if (!selectedSet && options.length > 0) {
					options[0]['selected'] = true;
				}
				return options;
			}

			dom.taskTemplateSelect.on('click', function(event) {
				self.setStorage('template', $(this).val());
			});

			var options = getOptions(NewsletterPro.dataStorage.get('all_smtp'));
			var smtp = NewsletterPro.modules.smtp,
				smtpSelect = smtp.ui.SelectOption({
					name: 'taskSmptSelect',
					template: dom.taskSmptSelect,
					className: 'gk-smtp-select',
					options: options,
					onChange: function() 
					{
						var selected = smtpSelect.getSelected();
						if (selected != null) {
							var data = selected.data,
								email = NewsletterPro.dataStorage.get('configuration.PS_SHOP_EMAIL'),
								smtp = data.name,
								id_newsletter_pro_smtp = data.id_newsletter_pro_smtp;
								// email = data.user,

							dom.taskEmailTest.val(email);

							self.setStorage('smtp', smtp);
							self.setStorage('id_newsletter_pro_smtp', id_newsletter_pro_smtp);
						}
					}
				});

			var smtpSelected = smtpSelect.getSelected();

			if (smtpSelected != null)
				self.setStorage('smtp', smtpSelected.data.name);

			var template = dom.taskTemplate,
				datepicker = dom.datepicker,
				ui = self.ui,
				win = ui.TaskWindow({
					width: 425,
					className: 'gk-task-window',
					show: function(win) {
						dom.emailsCount.text(self.getEmails().length);

						var smptSelect = smtp.ui.components.smptSelect,
							data = smptSelect.getData(),
							options = getOptions(data);

						smtpSelect.refresh(options);
					},
				});

			win.setHeader(l('new task'));

			datepicker.datetimepicker({
				prevText: '',
				nextText: '',
				dateFormat: box.dataStorage.get('jquery_date_format'),
				currentText: l('Now'),
				closeText: l('Done'),
				ampm: false,
				amNames: ['AM', 'A'],
				pmNames: ['PM', 'P'],
				timeFormat: 'hh:mm:ss tt',
				timeSuffix: '',
				timeOnlyTitle: l('Choose Time'),
				timeText: l('Time'),
				hourText: l('Hour'),
				onSelect: function(date, dateObj) 
				{
			      	var dateAsObject = $(this).datepicker('getDate'),
			      		date = new Date(dateAsObject),
						m = date.getMonth() + 1,
						d = date.getDate();

					var year = date.getFullYear(),
						month = (String(m).length == 1 ? '0' + String(m) : String(m)),
						day = (String(d).length == 1 ? '0'+String(d) : String(d)),
						hours = date.getHours(),
						minutes = date.getMinutes(),
						seconds = date.getSeconds(),
						mysql_date = year+'-'+month+'-'+day + ' ' + hours + ':' + minutes + ':' + seconds;

					self.setStorage('mysql_date', mysql_date);
				},
				minDate: new Date(),
			});

			win.setContent(template);

			var sleepVal = parseInt(dom.taskSleep.val());
			dom.taskSleep.on('change', function(event) {
				var button = $(this),
					val = parseInt(button.val());

				if ( val < 0 ) {
					val = sleepVal;
				} else {
					sleepVal = val;
				}
				button.val(val);
				self.setStorage('sleep', val);
			});

			dom.taskSmtpTest.on('click', function(event) {
				var selected = smtpSelect.getSelected();

					self.storage.template = dom.taskTemplateSelect.val();

					var email = dom.taskEmailTest.val(),
						smtpId = (selected != null ? selected.data.id_newsletter_pro_smtp : 0),
						message = dom.taskSmtpTestMessage,
						templateName = self.storage.template,
						sendMethod = self.storage.send_method,
						idLang = (self.storage.id_lang ? self.storage.id_lang : box.dataStorage.get('id_selected_lang'));

					box.showAjaxLoader(dom.taskSmtpTest);

					$.postAjax({ 'submit': 'sendTestEmail', sendTestEmail: email, smtpId: smtpId, templateName: templateName, sendMethod: sendMethod, idLang: idLang }).done(function( response ) {
						if( response.status )
							message.empty().show().append('<div class="alert alert-success">'+l('email sent')+'</div>');
						else {
							message.empty().show().append('<div class="alert alert-danger">'+response.msg+'</div>');
						}

						setTimeout( function() { message.hide(); }, 15000);
					}).always(function(){
						box.hideAjaxLoader(dom.taskSmtpTest);
					});
			});


			dom.btnTaskLangSelectTest = $('#task-test-email-lang-select');

			var langSelect = new box.components.LanguageSelect({
				selector: dom.btnTaskLangSelectTest,
				languages: box.dataStorage.get('all_languages'),
				click: function(lang, key) {
					var idLang = Number(lang.id_lang);
					self.storage.id_lang = idLang;
				},
			});

			dom.addTask.on('click', function(event) {
				var message = dom.taskSmtpTestMessage;

				var emails = JSON.stringify(self.storage.emails);
				self.storage.emails = [];
				self.storage.template = dom.taskTemplateSelect.val();

				$.postAjax({'submit': 'addTask', addTask: self.storage, emails: emails}).done(function(response) {
					if (response.errors.length > 0) {
						message.empty().show().append('<div class="alert alert-danger">'+(response.errors.join('<br />'))+'</div>');
					} else {
						var taskList = taskList || NewsletterPro.modules.task.ui.components.taskList;
						win.hide().done(function() {
							taskList.sync();
						});
					}
					setTimeout( function() { message.hide(); }, 15000);
				});
			});

			var dataModel = new gk.data.Model({
				id: 'id_newsletter_pro_task',
			});

			var dataSource = new gk.data.DataSource({
				pageSize: 10,
				transport: {
					read: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=getTasks',
						dataType: 'json',
					},
					update: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=updateTask&updateTask',
						dataType: 'json'
					},
					destroy: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=deleteTask&deleteTask',
						type: 'POST',
						dateType: 'json',
						success: function(response, itemData) {
							if(!response)
								alert(l('delete task'));
						},
						error: function(data, itemData) {
							alert(l('delete task'));
						},
						complete: function(data, itemData) {},
					}
				},
				schema: {
					model: dataModel
				},
				trySteps: 2,
				errors: 
				{
					read: function(xhr, ajaxOptions, thrownError) 
					{
						dataSource.syncStepAvailableAdd(3000, function(){
							dataSource.sync();
						});
					},
				},
			});

			dom.mailMethod.on('change', function() {
				if (dom.mailMethod.is(':checked'))
				{
					self.setStorage('send_method', 'mail');
					dom.smtpSelectContainer.slideUp();
				}

			});

			dom.smtpMethod.on('change', function() {
				if (dom.smtpMethod.is(':checked'))
				{
					self.setStorage('send_method', 'smtp');
					dom.smtpSelectContainer.slideDown();
				}
			});

			dom.taskList.gkGrid({
				dataSource: dataSource,
				selectable: false,
				currentPage: 1,
				pageable: true,
				template: {
					template: function(item, value) 
					 {
						var div = $('<div></div>'),
							data = item.data,
							templateList = getTemplateList(),
							select = $('<select id="template-select-list" class="template-select-list gk-select"></select>');

						function addOption(item) {
							var name = item.name,
								value = item.value,
								selected = item.selected;

							var option = $('<option value="'+value+'" '+(selected ? 'selected="selected"' : '')+'>'+name+'</option>');
							select.append(option);
						}

						function getTemplateList() {
							var list = box.dataStorage.get('templates'),
								objs = [];

							for (var i in list)
							{
								var item = list[i];

								var obj = {
									name: item.name,
									value: item.filename,
									selected: false,
								};

								if ($.trim(obj.value) === $.trim(value)) {
									obj.selected = true;
								}
								objs.push(obj);
							}
							return objs;
						}

						for( var i in templateList) {
							var itemsObj = templateList[i];
							addOption(itemsObj);
						}

						select.on('change', function(event) {
							var val = select.val();
							if (val) {
								item.data.template = val;
								item.update().done(function(response) {
									if (!response)
										alert(l('template not found'));
								});
							}
						});

						if (parseInt(data.status) == 1 && parseInt(data.done) == 0 && parseInt(data.pause) == 0) {
							div.append($('<span class="task-text-p">'+value+'</span>'));
						} else {
							div.append(select);
						}

						return div;
					},

					actions : function(item, value) 
					{
						var data = item.data;

						var button = $('#task-delete').gkButton({
							name: 'delete',
							title: l('delete'),
							className: 'btn btn-default btn-margin task-delete',
							item: item,
							css: { 'display': 'inline-block' },
							command: 'delete',
							confirm: function() 
							{
								return confirm(l('delete record'));
							},
							icon: '<i class="icon icon-trash-o"></i> ',
						});

						var div = $('<div></div>');

						var send = $('#task-send').gkButton({
							name: 'send-task',
							title: l('send'),
							className: 'btn btn-default btn-margin send-task',
							css: { 'display': 'inline-block' },
							click: function(event) {
								syncTask.runInterval(syncTask.speed.fast);
								var id = data.id_newsletter_pro_task;
								send.hide();
								pauseBtn.show();

								$.postAjax({'submit': 'sendTaskAjax', sendTaskAjax: id}).done(function(response) {
									if (response.status)
									{
										send.disable();
										syncTask.syncAjax();
									}
									else
										box.alertErrors(response.errors);
								});
							},
							icon: '<i class="icon icon-send"></i> ',
						});
						send.hide();

						var continueBtn = $('#continue-send').gkButton({
							name: 'continue-task',
							title: l('continue'),
							className: 'btn btn-default btn-margin continue-task',
							css: {'display': 'inline-block'},
							click: function(event) {
								syncTask.runInterval(syncTask.speed.fast);
								var id = data.id_newsletter_pro_task;

								togglePauseContinue();

								$.postAjax({'submit': 'continueTaskAjax', 'id': id}).done(function(response) {
									syncTask.syncAjax();
								});
							},
							icon: '<i class="icon icon-refresh"></i> ',
						});

						continueBtn.hide();

						var pauseBtn = $('#pause-task').gkButton({
							name: 'pause-task',
							title: l('pause'),
							className: 'btn btn-default btn-margin pause-task',
							css: { 'display': 'inline-block' },
							click: function(event) {
								syncTask.runInterval(syncTask.speed.fast);
								var id = data.id_newsletter_pro_task;

								togglePauseContinue();

								$.postAjax({'submit': 'pauseTask', 'id': id}).done(function(response) {
									syncTask.syncAjax();
								});
							},
							icon: '<i class="icon icon-pause"></i> ',
						});
						pauseBtn.hide();

						function togglePauseContinue() {
							if (continueBtn.is(':visible')) {
								continueBtn.hide();
								pauseBtn.css({ 'display': 'inline-block' });
							} else {
								continueBtn.css({ 'display': 'inline-block' });
								pauseBtn.hide();
							}
							send.hide();
						}

						div.append(continueBtn);
						div.append(pauseBtn);
						div.append(send);

						if (parseInt(data.status) == 1 && parseInt(data.done) == 0) {
							if (parseInt(data.pause))
								continueBtn.css({ 'display': 'inline-block' });
							else
								pauseBtn.css({ 'display': 'inline-block' });
						} else {
							send.css({ 'display': 'inline-block' });
						}

						div.append(button);
						return div;
					},

					smtp: function(item, value) {
						var opt = getOptions(NewsletterPro.dataStorage.get('all_smtp')),
							select = $('<select id="smtp-select-list" class="gk-select" style="min-width: 160px !important; width: auto;"></select>'),
							id_smtp = item.data.id_newsletter_pro_smtp,
							options = setSelected(opt, {key:'id_newsletter_pro_smtp', value:id_smtp});

						var smtpSelect = smtp.ui.SelectOption({
								name: 'taskSmptSelectList',
								template: select,
								className: 'gk-smtp-select',
								options: options,
								onChange: function() {

									var selected = smtpSelect.getSelected();
									if (selected != null) {
										var data = selected.data,
											id_newsletter_pro_smtp = data.id_newsletter_pro_smtp;

										item.data.id_newsletter_pro_smtp = id_newsletter_pro_smtp;

										item.update().done(function(response) {
											if (!response)
												alert(l('smtp not update'));
										});
									}
								}
							});

						if (isMail()) {
							smtpSelect.template.hide();
						}

						function isMail() {
							if (item.data.send_method == 'mail')
								return true;
							return false;
						}

						var sendMethodSelect = $('<select id="send-method-select" class="send-method-select gk-select" autocomplete="off"><option value="smtp">SMTP</option><option value="mail" '+(isMail() ? 'selected="selected"' :'')+'>mail()</option></select>')

						sendMethodSelect.on('change', function(){
							var send_method = sendMethodSelect.val();
							if (send_method === 'mail') {
								smtpSelect.template.hide();
								item.data.id_newsletter_pro_smtp = 0;
							} else {

								var selected = smtpSelect.getSelected();
								if (selected != null) {
									var data = selected.data,
										id_newsletter_pro_smtp = data.id_newsletter_pro_smtp;

									item.data.id_newsletter_pro_smtp = id_newsletter_pro_smtp;
								}
								smtpSelect.template.show();
							}

							item.data.send_method = send_method;
							item.update().done(function(response) {
								if (!response)
									alert(l('send method not update'));
							});
						});

						var div = $('<div></div>');
						var sendTestListMessage = $('<span class="send-test-list-message" style="margin-top: 4px; display: inline-block;"></span>');

						var sendTest = $('#send-test-list').gkButton({
							name: 'send-test-list',
							className: 'btn btn-default task-smtp-test-list',
							title: l('test'),
							click: function()
							{
								var selected = smtpSelect.getSelected();
								var smtpId = (selected != null ? selected.data.id_newsletter_pro_smtp : 0),
									email = NewsletterPro.dataStorage.get('configuration.PS_SHOP_EMAIL'),
									templateName = item.data.template,
									sendMethod = item.data.send_method;

								sendTestListMessage.empty().show().append('<span class="ajax-loader">&nbsp;</span>');

								$.postAjax({'submit': 'sendTestEmail', sendTestEmail:email, smtpId: smtpId, templateName: templateName, sendMethod: sendMethod}).done(function(response) {

									if( response.status )
										sendTestListMessage.empty().show().append('<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>');
									else 
									{
										sendTestListMessage.empty().show().append('<span class="list-action-enable action-disabled"><i class="icon icon-remove"></i></span>');
										NewsletterPro.alertErrors(response.msg);
									}

									setTimeout( function() { sendTestListMessage.hide(); }, 10000);
								});
							},
							icon: '<i class="icon icon-envelope"></i> ',
						});

						var css = {'float': 'left'};
						select.css(css);
						sendTest.css(css);
						sendTestListMessage.css(css);

						function getSelectVal()
						{
							return sendMethodSelect.val() === 'mail' ? 'mail()' : 'SMTP';
						}

						if (parseInt(item.data.status) == 1 && parseInt(item.data.done) == 0 && parseInt(item.data.pause) == 0) {
							div.append( '<span class="task-text-p">' + (getSelectVal()) + '</span>' );
						} else {
							div.append(sendMethodSelect);
							div.append(select);
							div.append(sendTest);
							div.append(sendTestListMessage);
						}
						return div;
					},

					active: function(item, value) 
					{
						var id = item.data.id_newsletter_pro_task;
						function isActive() {
							return String(item.data.active) === '0' ? false : true;
						}

						var activeToggle = $('<a class="status-button" href="javascript:{}"></a>');

						var enabledHTML = '<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>',
							disableHTML = '<span class="list-action-enable action-disabled"><i class="icon icon-remove"></i></span>';

						if (isActive())
							activeToggle.html(enabledHTML);
						else
							activeToggle.html(disableHTML);

						activeToggle.toggleActive =	function ()
						{
							var button = activeToggle;
							item.data.active = isActive() ? 0 : 1;

							if (!isActive()) {
								button.html(disableHTML);
							} else {
								button.html(enabledHTML);
							}
						}

						activeToggle.on('click', function() {
							var button = activeToggle;
							button.toggleActive();

							item.update().done(function(response) {
								if (!response)
									button.toggleActive();
							});
						});
						return activeToggle;
					},

					date_start: function(item, value) 
					{

						var datePicker,
							tempDate = new Date(),
							tempYear = tempDate.getFullYear(),
							tempMonth = tempDate.getMonth(),
							tempDay = tempDate.getDate(),
							data = item.data,
							date = new Date(item.data.date_start),
							// minDate = ( date.getTime() <= new Date().getTime() ? date : new Date() ),
							minDate = new Date(tempYear, tempMonth, tempDay, 0, 0, 0),
							dateFormat = box.dataStorage.get('jquery_date_format');

						if (parseInt(data.status) == 1 && parseInt(data.done) == 0 && parseInt(data.pause) == 0) 
						{
							datePicker = $('<span>'+($.datepicker.formatDate(dateFormat, date))+'</span>');
						} 
						else 
						{
							datePicker = $('<input type="text" class="task-list-date-input gk-input" style="position: relative; z-index: 100000;">').datetimepicker({
								prevText: '',
								nextText: '',
								dateFormat: dateFormat,
								currentText: l('Now'),
								closeText: l('Done'),
								ampm: false,
								amNames: ['AM', 'A'],
								pmNames: ['PM', 'P'],
								timeFormat: 'hh:mm:ss tt',
								timeSuffix: '',
								timeOnlyTitle: l('Choose Time'),
								timeText: l('Time'),
								hourText: l('Hour'),
								onSelect: function(date, dateObj) 
								{
									var dateAsObject = $(this).datepicker('getDate'),
							      		dateObject = new Date(dateAsObject),
										m = dateObject.getMonth() + 1,
										d = dateObject.getDate();

									var year = dateObject.getFullYear(),
										month = (String(m).length == 1 ? '0' + String(m) : String(m)),
										day = (String(d).length == 1 ? '0'+String(d) : String(d)),
										hours = dateObject.getHours(),
										minutes = dateObject.getMinutes(),
										seconds = dateObject.getSeconds(),
										mysql_date = year+'-'+month+'-'+day + ' ' + hours + ':' + minutes + ':' + seconds;

									item.data.date_start = mysql_date;

									item.update().done(function(response) {
										if (!response)
											alert(l('date not changed'));
									});
								},
								minDate: minDate,
							});

							datePicker.datetimepicker('setDate', date);
						}

						return datePicker;
					},

					status: function(item, value) 
					{
						var div = $('<div></div>'),
							data = item.data,
							status = $('<span class="task-emails-status"> ( '+parseInt(data.emails_count)+' ) '+data.status+' </span>'),
							count = $('<span class="task-emails-count"> ( <span class="count">'+parseInt(data.emails_count)+'</span> ) emails </span>'),
							error = $('<span class="task-emails-error"> ( <span class="count">'+parseInt(data.emails_error)+'</span> ) </span>'),
							success = $('<span class="task-emails-success"> ( <span class="count">'+parseInt(data.emails_success)+'</span> ) </span>'),
							error_msg = $('<a href="javascript:{}" class="task-error-msg" style="display:none;"></a>'),
							messages = getMessage(item.data.error_msg);

						function getMessage(obj) {
							var arr = [];
							for (var i in obj)
								arr.push(obj[i]);

							if (arr.length > 0)
								return arr.join('<br />');
							return false;
						}

						if (messages) {
							error_msg.show();
							error_msg.on('click', function() {

							var winMessage = ui.TaskWindow({
									width: 425,
									className: 'gk-task-window',
									show: function(win) {},
								});

							winMessage.setHeader(l('errors'));
							winMessage.setContent('<span class="error-msg" style="float: none;">'+(messages.replace(/\\'/g, '"'))+'</span>');
							winMessage.show();

							});
						}

						if (parseInt(data.status) === 1) {
							div.append(count);
							div.append(success);
							div.append(error);

							function updateInitRow(id_task, data) {
								var pIndex = syncTask.progressIds.indexOf(id_task);

								if (pIndex == -1)
									syncTask.progressIds.push(id_task);
							}

							updateInitRow(data.id_newsletter_pro_task, data);

						} else {
							div.append(status);
						}
						div.append(error_msg);

						return div;
					}
				}
			});

			var dataModelSendHistory = new gk.data.Model({
				id: 'id_newsletter_pro_send',
			});

			var dataSourceSendHistory = new gk.data.DataSource({
				pageSize: 10,
				transport: {
					read: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=getSendHistory',
						dataType: 'json',
					},

					destroy: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=deleteSendHistory&deleteSendHistory',
						dateType: 'json',
						success: function(response, itemData)
						{
							if(!response)
								alert(l('delete send history'));
						},
						error: function(data, itemData)
						{
							alert(l('delete send history'));
						},
						complete: function(data, itemData)
						{

						},
					}

				},
				schema: {
					model: dataModelSendHistory
				},
				trySteps: 2,
				errors: 
				{
					read: function(xhr, ajaxOptions, thrownError) 
					{
						dataSourceSendHistory.syncStepAvailableAdd(3000, function(){
							dataSourceSendHistory.sync();
						});
					},
				},
			});

			function isSendNewsletterInProgress(func)
			{
				$.postAjax({'submit': 'isSendNewsletterInProgress'}).done(function(id){
					if (id)
					{
						var conf = confirm('The newsletter is in progress. Do you want to stop the sending process before to proceed?');

						if (conf)
							NewsletterProControllers.SendController.stopNewsletters().done(function(){
								return func(false);
							});
						else
						{
							return func(true);
						}
					}
					else
						return func(false);
				});
			}

			dom.sendHistory.gkGrid({
				dataSource: dataSourceSendHistory,
				selectable: false,
				currentPage: 1,
				pageable: true,
				template: {
					actions: function(item, value) 
					{
						var data = item.data,
							div = $('<div></div>'),
							steps = ( typeof item.data.steps !== 'undefined' && item.data.steps ? item.data.steps.split(',') : null ),
							detailsContent = $('<div class="detail-content"></div>'),
							id_history = parseInt(item.data.id_newsletter_pro_tpl_history),
							stepsButtons,
							winDetails;

						function exportCsv(idHisotry, emailsToSend, emailsSent) {
							var defaultSeparator = ';',
								exportForm = $('\
									<form id="' + NewsletterPro.uniqueId() + '" method="POST" action="' + NewsletterPro.dataStorage.get('ajax_url') + '#history">\
										<input type="hidden" name="export_send_history" value="1">\
										<input type="hidden" name="id_history" value="' + idHisotry + '">\
										<input type="hidden" name="export_emails_to_send" value="' + emailsToSend + '">\
										<input type="hidden" name="export_emails_sent" value="' + emailsSent + '">\
										<input type="hidden" name="csv_separator" value="' + defaultSeparator + '">\
									</form>\
								'),
								separator = prompt(l('CSV Separator'), defaultSeparator);

							if (separator == null) {
								return;
							}

							separator = $.trim(separator);

							if (separator == ';' || separator == ',') {
								
								exportForm.find('input[name="csv_separator"]').val(separator);
								exportForm.submit();
							} else {
								alert(l('Invalid CSV separator.'));
								return;
							}
						}

						function getDetail(id) {

							$.postAjax({'submit': 'getSendHistoryDetail', getSendHistoryDetail: id},'html').done(function(response){
								detailsContent.html(response);

								var localDom = {
									btnResend: detailsContent.find('#np-btn-resend-send'),
									checkboxResendLeft: detailsContent.find('#resend-left-list-send'),
									checkboxResendUndelivered: detailsContent.find('#resend-undelivered-list-send'),
									exportSend: detailsContent.find('#np-btn-export-send-history'),
									exportSendRem: detailsContent.find('#np-btn-export-send-history-rem'),
								};

								localDom.exportSendRem.on('click', function(e) {
									exportCsv(id_history, 1, 0);
								});

								localDom.exportSend.on('click', function(e) {
									exportCsv(id_history, 0, 1);
								});

								// add events here
								localDom.btnResend.on('click', function(e){

									isSendNewsletterInProgress(function(stopFunction){

										if (stopFunction)
											return;

										box.showAjaxLoader(localDom.btnResend);
										$.postAjax({'submit': 'resendSendHistory', id: id_history, resendLeft: Number(localDom.checkboxResendLeft.is(':checked')), resendUndelivered: Number(localDom.checkboxResendUndelivered.is(':checked'))}).done(function(response) {
											if (!response.success)
												box.alertErrors(response.errors);
											else
											{
												if (typeof winDetails !== 'undefined')
													winDetails.hide();

												NewsletterProComponents.objs.tabItems.trigger('tab_newsletter_5');
												$('html, body').animate({
													scrollTop: parseInt($('#emails-to-send').offset().top) - 120
												}, 1000);

												NewsletterProControllers.SendController.prepareEmails(response.emails);
											}
										}).always(function(){
											box.hideAjaxLoader(localDom.btnResend);
										}); // end of postAjax resendSendHistory

									});
								});

							});
						}

						function createButton(cfg) {

							var button = ({
									id: null,
									instance: null,
									init: function(cfg) {
										var self = this;
											id = cfg.step,
											count = cfg.count;

										self.id = id;
										self.instance = $('<a href="javascript:{}" class="history-details-steps" data-id="'+id+'">'+(l('step'))+' '+(count)+'</a>');

										if (count == 1) {
											getDetail(self.id);
											self.instance.addClass('selected');
										}

										(function addEvents(instance) {
											instance.on('click', function(event){
												click.call(instance, self ,event);
											});
										}(self.instance));

										function click(obj) {
											if (stepsButtons != null) {
												var buttons = stepsButtons.find('a.selected');
												$.each(buttons, function(index, button) {
													$(button).removeClass('selected');
												});
											}
											this.addClass('selected');
											getDetail(obj.id);
										}

										return self;
									},
									getInstance: function() {
										return this.instance;
									}
								}.init(cfg));
							return button;
						}

						function createButtons () {
							var count = 0,
								buttons = $('<div class="detail-buttons"></div>');

							$.each(steps, function(index, step) {
								var button = createButton({step:step, count: ++count});
								buttons.append(button.getInstance());
							});
							return buttons;
						}

						var button = $('#task-history-delete').gkButton({
							name: 'delete',
							title: l('delete'),
							className: 'btn btn-default btn-margin task-delete',
							item: item,
							command: 'delete',
							confirm: function() 
							{
								return confirm(l('delete record'));
							},
							icon: '<i class="icon icon-trash-o"></i> ',
						});

						var details = $('#send-history-details').gkButton({
							name: 'send-history-details',
							title: l('details'),
							className: 'btn-margin send-history-details',

							click: function(event) {
								var content = $('<div></div>');
								stepsButtons = createButtons();

								winDetails = ui.TaskWindow({
										width: 800,
										height: 500,
										className: 'gk-task-window-details',
										show: function(win) {},
									});

								winDetails.setHeader(l('details'));
								if (steps.length > 1)
									content.append(stepsButtons);
								content.append(detailsContent);
								winDetails.setContent(content);

								winDetails.show();								
							},
							icon: '<i class="icon icon-info-circle"></i> ',
						});

						var view = $('#send-history-view').gkButton({
							name: 'send-history-view',
							title: l('view'),
							className: 'btn btn-default btn-margin task-history-view',

							click: function(event) 
							{
								var content = $('<div></div>'),
									open,
									title,
									viewTemplate;
								var winDetails = gkWindow({
										width: 800,
										height: 500,
										className: 'gk-task-window-view',
										show: function(win) {},
									});

								var header = $('<span></span>');
								title = l('view template');
								open = $('<a href="javascript:{}" target="_blank" class="history-details-steps view-template-in-br-button">'+l('veiw in a new window')+'</a>');
								header.append(title);
								header.append(open);
								winDetails.setHeader(header);

								viewTemplate = $('<div class="view-template-in-br"></div>');

								$.postAjax({'submit': 'renderTemplateHistory', renderTemplateHistory: id_history}).done(function(response) {
									open.attr('href', response.url);

									var contentIframe = $('<iframe style="display: block; vertical-align: top; height: 462px;" scrolling="yes" src="'+(response.url)+'"> </iframe>');

									viewTemplate.html(contentIframe);

									var table = viewTemplate.find('table').first();
									if (table.length && content.parent().length) {
										content.parent().css({
											'background-color': table.css('background-color')
										});
									}
								});

								content.append(viewTemplate);

								winDetails.setContent(content);
								winDetails.show();
							},
							icon: '<i class="icon icon-eye"></i> ',
						});

						if (id_history > 0)
							div.append(view);

						div.append(details);
						div.append(button);

						return div;
					},

					emails_count: function(item, value) 
					{
						return '<span style="padding: 0; margin: 0; font-weight: bold; float: none;">'+(parseInt(value) ? value : '0')+'</span>';
					},

					emails_success: function(item, value) 
					{
						return '<span class="success-msg" style="padding: 0; margin: 0; float: none;">'+(parseInt(value) ? value : '0')+'</span>';
					},

					emails_error: function(item, value) 
					{
						return '<span class="error-msg" style="padding: 0; margin: 0; float: none;">'+(parseInt(value) ? value : '0')+'</span>';
					},

					unsubscribed: function(item, value)
					{
						var button = $('<a href="javascript:{}" class="btn btn-default task-error-msg-text" style="display:none;"><span class="icon"></span><span style="font-weight: bold; color: #F00;">'+value+'</span></a>');

						if (parseInt(value) <= 0 )
							return value;

						var detailsContent = $('<div></div>');

						function getDetail(id) 
						{
							$.postAjax({'submit': 'getUnsubscribedDetails', id_newsletter: id},'html').done(function(response){
								detailsContent.html(response);
							});
						}

						button.css({'display':'inline-block'});

						button.on('click', function(){
							getDetail(item.data.id_newsletter_pro_tpl_history);

							var content = $('<div></div>');
							var winDetails = ui.TaskWindow({
									width: 800,
									height: 500,
									className: 'gk-task-window-details',
									show: function(win) {},
								});

							winDetails.setHeader(l('unsubscribed'));

							content.append(detailsContent);
							winDetails.setContent(content);
							winDetails.show();
						});

						return button;
					},

					fwd_unsubscribed: function(item, value)
					{
						var button = $('<a href="javascript:{}" class="btn btn-default task-error-msg-text" style="display:none;"><span class="icon"></span><span style="font-weight: bold; color: #F00;">'+value+'</span></a>');

						if (parseInt(value) <= 0 )
							return value;

						var detailsContent = $('<div></div>');

						function getDetail(id) 
						{
							$.postAjax({'submit': 'getTaskFwdUnsubscribedDetails', id_newsletter: id},'html').done(function(response){
								detailsContent.html(response);
							});
						}

						button.css({'display':'inline-block'});

						button.on('click', function(){
							getDetail(item.data.id_newsletter_pro_tpl_history);

							var content = $('<div></div>');
							var winDetails = ui.TaskWindow({
									width: 800,
									height: 500,
									className: 'gk-task-window-details',
									show: function(win) {},
								});

							winDetails.setHeader(l('unsubscribed'));

							content.append(detailsContent);
							winDetails.setContent(content);
							winDetails.show();
						});

						return button;
					},

					error_msg: function(item, value) 
					{
						var error_msg = $('<a href="javascript:{}" class="btn btn-default task-error-msg-text" style="display:none;"><span class="icon"></span>'+(l('View'))+'</a>'),
							messages = getMessage(value);

						function getMessage(obj) 
						{
							if (typeof value === 'object') {
								var arr = [];
								for (var i in obj)
									arr.push(obj[i]);

								if (arr.length > 0)
									return arr.join('<br />');
							}
							else if (typeof value === 'string')
							{
								return value;
							}

							return false;
						}

						if (messages) {
							error_msg.css({display:'inline-block'});
							error_msg.show();
							error_msg.on('click', function() {

								var winMessage = ui.TaskWindow({
										width: 425,
										className: 'gk-task-window',
										show: function(win) {},
									});

								winMessage.setHeader(l('errors'));
								winMessage.setContent('<span class="error-msg" style="float: none;">'+(messages.replace(/\\'/g, '"'))+'</span>');
								winMessage.show();
							});
						}

						return error_msg;
					},
				}
			});

			var dataModelHistory = new gk.data.Model({
				id: 'id_newsletter_pro_task',
			});

			var dataSourceTaskHistory = new gk.data.DataSource({
				pageSize: 10,
				transport: {
					read: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=getTasksHistory',
						dataType: 'json',
					},

					destroy: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=deleteTask&deleteTask',
						type: 'DELETE',
						dateType: 'json',
						success: function(response, itemData) {
							if(!response)
								alert(l('delete task'));
						},
						error: function(data, itemData) {
							alert(l('delete task'));
						},
						complete: function(data, itemData) {},
					}
				},
				schema: {
					model: dataModelHistory
				},
				trySteps: 2,
				errors: 
				{
					read: function(xhr, ajaxOptions, thrownError) 
					{
						dataSourceTaskHistory.syncStepAvailableAdd(3000, function(){
							dataSourceTaskHistory.sync();
						});
					},
				},
			});

			dom.taskHistory.gkGrid({
				dataSource: dataSourceTaskHistory,
				selectable: false,
				currentPage: 1,
				pageable: true,
				template: {
					actions: function(item, value) 
					{
						var data = item.data,
							div = $('<div></div>'),
							steps = ( typeof item.data.steps !== 'undefined' ? item.data.steps.split(',') : null ),
							detailsContent = $('<div class="detail-content"></div>'),
							id_history = parseInt(item.data.id_newsletter_pro_tpl_history),
							stepsButtons;

						function getDetail(id) {
							$.postAjax({'submit': 'getTasksHistoryDetail', getTasksHistoryDetail: id},'html').done(function(response){
								detailsContent.html(response);
							});
						}

						function createButton(cfg) {

							var button = ({
									id: null,
									instance: null,
									init: function(cfg) {
										var self = this;
											id = cfg.step,
											count = cfg.count;

										self.id = id;
										self.instance = $('<a href="javascript:{}" class="history-details-steps" data-id="'+id+'">'+(l('step'))+' '+(count)+'</a>');

										if (count == 1) {
											getDetail(self.id);
											self.instance.addClass('selected');
										}

										(function addEvents(instance) {
											instance.on('click', function(event){
												click.call(instance, self ,event);
											});
										}(self.instance));

										function click(obj) {
											if (stepsButtons != null) {
												var buttons = stepsButtons.find('a.selected');
												$.each(buttons, function(index, button) {
													$(button).removeClass('selected');
												});
											}
											this.addClass('selected');
											getDetail(obj.id);
										}

										return self;
									},
									getInstance: function() {
										return this.instance;
									}
								}.init(cfg));
							return button;
						}

						function createButtons () {
							var count = 0,
								buttons = $('<div class="detail-buttons"></div>');

							$.each(steps, function(index, step) {
								var button = createButton({step:step, count: ++count});
								buttons.append(button.getInstance());
							});
							return buttons;
						}

						var button = $('#task-history-delete').gkButton({
							name: 'delete',
							title: l('delete'),
							className: 'btn btn-default btn-margin task-delete',
							item: item,
							command: 'delete',
							confirm: function() 
							{
								return confirm(l('delete record'));
							},
							icon: '<i class="icon icon-trash-o"></i> ',
						});

						var details = $('#task-history-details').gkButton({
							name: 'history-details',
							title: l('details'),
							className: 'btn-margin task-history-details',

							click: function(event) {
								var content = $('<div></div>');
								stepsButtons = createButtons();
								var winDetails = ui.TaskWindow({
										width: 800,
										height: 500,
										className: 'gk-task-window-details',
										show: function(win) {},
									});

								winDetails.setHeader(l('details'));
								if (steps.length > 1)
									content.append(stepsButtons);
								content.append(detailsContent);
								winDetails.setContent(content);
								winDetails.show();
							},
							icon: '<i class="icon icon-info-circle"></i> ',
						});

						var view = $('#task-history-view').gkButton({
							name: 'history-view',
							title: l('view'),
							className: 'btn btn-default btn-margin task-history-view',

							click: function(event) 
							{
								var content = $('<div></div>'),
									open,
									viewTemplate;

								var winDetails = gkWindow({
										width: 800,
										height: 500,
										className: 'gk-task-window-view',
										show: function(win) {},
									});

							var header = $('<span></span>');
								title = l('view template');
								open = $('<a href="javascript:{}" target="_blank" class="history-details-steps view-template-in-br-button">'+l('veiw in a new window')+'</a>');
								header.append(title);
								header.append(open);
								winDetails.setHeader(header);

								viewTemplate = $('<div class="view-template-in-br"></div>');

								$.postAjax({'submit': 'renderTemplateHistory', renderTemplateHistory: id_history}).done(function(response) {
									open.attr('href', response.url);

									var contentIframe = $('<iframe style="display: block; vertical-align: top; height: 462px;" scrolling="yes" src="'+(response.url)+'"> </iframe>');
									viewTemplate.html(contentIframe);

									var table = viewTemplate.find('table').first();
									if (table.length && content.parent().length) {
										content.parent().css({
											'background-color': table.css('background-color')
										});
									}
								});

								content.append(viewTemplate);

								winDetails.setContent(content);
								winDetails.show();
							},
							icon: '<i class="icon icon-eye"></i> ',
						});

						if (id_history > 0)
							div.append(view);

						div.append(details);
						div.append(button);
						return div;
					},

					emails_count: function(item, value) {
						return '<span style="padding: 0; margin: 0; font-weight: bold; float: none;">'+(parseInt(value) ? value : '0')+'</span>';
					},

					emails_success: function(item, value) {
						return '<span class="success-msg" style="padding: 0; margin: 0; float: none;">'+(parseInt(value) ? value : '0')+'</span>';
					},

					emails_error: function(item, value) {
						return '<span class="error-msg" style="padding: 0; margin: 0; float: none;">'+(parseInt(value) ? value : '0')+'</span>';
					},

					unsubscribed: function(item, value)
					{
						var button = $('<a href="javascript:{}" class="btn btn-default task-error-msg-text" style="display:none;"><span class="icon"></span><span style="font-weight: bold; color: #F00;">'+value+'</span></a>');

						if (parseInt(value) <= 0 )
							return value;

						var detailsContent = $('<div></div>');

						function getDetail(id) 
						{
							$.postAjax({'submit': 'getTaskUnsubscribedDetails', id_newsletter: id},'html').done(function(response){
								detailsContent.html(response);
							});
						}

						button.css({'display':'inline-block'});

						button.on('click', function(){
							getDetail(item.data.id_newsletter_pro_tpl_history);

							var content = $('<div></div>');
							var winDetails = ui.TaskWindow({
									width: 800,
									height: 500,
									className: 'gk-task-window-details',
									show: function(win) {},
								});

							winDetails.setHeader(l('unsubscribed'));

							content.append(detailsContent);
							winDetails.setContent(content);
							winDetails.show();
						});

						return button;
					},

					fwd_unsubscribed: function(item, value)
					{
						var button = $('<a href="javascript:{}" class="btn btn-default task-error-msg-text" style="display:none;"><span class="icon"></span><span style="font-weight: bold; color: #F00;">'+value+'</span></a>');

						if (parseInt(value) <= 0 )
							return value;

						var detailsContent = $('<div></div>');

						function getDetail(id) 
						{
							$.postAjax({'submit': 'getTaskFwdUnsubscribedDetails', id_newsletter: id},'html').done(function(response){
								detailsContent.html(response);
							});
						}

						button.css({'display':'inline-block'});

						button.on('click', function(){
							getDetail(item.data.id_newsletter_pro_tpl_history);

							var content = $('<div></div>');
							var winDetails = ui.TaskWindow({
									width: 800,
									height: 500,
									className: 'gk-task-window-details',
									show: function(win) {},
								});

							winDetails.setHeader(l('unsubscribed'));

							content.append(detailsContent);
							winDetails.setContent(content);
							winDetails.show();
						});

						return button;
					},

					error_msg: function(item, value) 
					{
						var error_msg = $('<a href="javascript:{}" class="btn btn-default task-error-msg-text" style="display:none;"><span class="icon"></span>'+(l('View'))+'</a>'),
							messages = getMessage(value);

						function getMessage(obj) 
						{
							if (typeof value === 'object') 
							{
								var arr = [];
								for (var i in obj)
									arr.push(obj[i]);

								if (arr.length > 0)
									return arr.join('<br />');
							}
							else if (typeof value === 'string')
							{
								return value;
							}

							return false;
						}

						if (messages) 
						{
							error_msg.css({display:'inline-block'});
							error_msg.show();
							error_msg.on('click', function() {

								var winMessage = ui.TaskWindow({
										width: 425,
										className: 'gk-task-window',
										show: function(win) {},
									});

								winMessage.setHeader('Errors');
								winMessage.setContent('<span class="error-msg" style="float: none;">'+(messages.replace(/\\'/g, '"'))+'</span>');
								winMessage.show();
							});
						}

						return error_msg;
					}
				}
			});

			dom.clearTaskHistory.on('click', function() {
				$.postAjax({'submit': 'clearTaskHistory', clearTaskHistory:true}).done(function(response) {
					if(response.status) {
						var taskHistory = dataSourceTaskHistory || NewsletterPro.modules.task.ui.components.taskHistory;
						taskHistory.sync();
					} else {
						alert(response.msg);
					}
				});
			});

			dom.clearSendHistory.on('click', function() {
				$.postAjax({'submit': 'clearSendHistory', clearSendHistory:true}).done(function(response) {
					if(response.status) {
						var sendHistory = dataSourceSendHistory || NewsletterPro.modules.task.ui.components.sendHistory;
						sendHistory.sync();
					} else {
						alert(response.msg);
					}
				});
			});

			dom.clearSendDetails.on('click', function(){
				box.showAjaxLoader(dom.clearSendDetails);

				$.postAjax({'submit': 'clearSendHistoryDetails'}).done(function(response){
					if (!response.status)
						box.alertErrors(response.errors);

				}).always(function(){
					box.hideAjaxLoader(dom.clearSendDetails);
				});
			});

			dom.clearTaskDetails.on('click', function(){
				box.showAjaxLoader(dom.clearTaskDetails);

				$.postAjax({'submit': 'clearTaskHistoryDetails'}).done(function(response){
					if (!response.status)
						box.alertErrors(response.errors);

				}).always(function(){
					box.hideAjaxLoader(dom.clearTaskDetails);
				});
			});

			dom.taskMoreInfoButton.on('click', function(event) {
				dom.taskMoreInfo.toggle();
				if (dom.taskMoreInfo.is(':visible')) {
					$(this).text(l('less info'));
				} else {
					$(this).text(l('more info'));
				}
			});

			ui.add('taskList', dataSource);
			ui.add('taskHistory', dataSourceTaskHistory);
			ui.add('sendHistory', dataSourceSendHistory);
			ui.add('taskWindow', win);
		});

		return self;
	},

	initStorage: function() {
		var box = NewsletterPro;

		this.storage = {
			template: this.dom.taskTemplateSelect.val(),
			emails: [],
			mysql_date: '',
			smtp: '',
			id_lang: null,
			id_newsletter_pro_smtp: 0,
			sleep: parseInt(this.dom.taskSleep.val()) || 5,
			send_method: 'mail',
		};
	},

	setStorage: function(name, value) {
		this.storage[name] = value;
	},

	ready: function(func) {
		var self = this;

		$(document).ready(function(){			

			var template = $('#task-template'),
				templateHTML = $($.trim(template.html()));

			if (typeof templateHTML[1] !== 'undefined')
				templateHTML = $(templateHTML[1]);

			self.dom = {
				chooseNewsletterTemplate : $('#change-newsletter-template'),
				taskButton: $('#new-task'),
				testEmailInput: $('#test-email-input'),
				taskList: $('#task-list'),
				taskHistory: $('#task-history'),
				clearTaskHistory: $('#clear-task-history'),
				clearTaskDetails: $('#clear-task-details'),

				clearSendHistory: $('#clear-send-history'),
				clearSendDetails: $('#clear-send-details'),

				sendHistory: $('#send-history'),

				taskMoreInfo: $('#task-more-info'),
				taskMoreInfoButton: $('#task-more-info-button'),

				taskTemplate: templateHTML,
				datepicker: templateHTML.find('#task-datepicker'),

				emailsCount: templateHTML.find('#selected_emails_count'),
				taskSmptSelect: templateHTML.find('#task-smtp-select'),
				taskTemplateSelect: templateHTML.find('#task-select-template'),
				taskSmtpTest: templateHTML.find('#task-smtp-test'),
				taskSmtpTestMessage: templateHTML.find('#task-smtp-test-message'),
				taskEmailTest: templateHTML.find('#task-email-test'),
				addTask: templateHTML.find('#add-task'),
				taskSleep: templateHTML.find('#task-sleep'),

				btnTaskLangSelectTest: $('#task-test-email-lang-select'),

				mailMethod: templateHTML.find('#task-mail-method'),
				smtpMethod: templateHTML.find('#task-smtp-method'),
				smtpSelectContainer: templateHTML.find('#div-task-smtp-select'),
			};

			func(self.dom);
		});
	},

	getStorage: function() {
		return this.storage;
	},

	taskClick: function(self) 
	{
		var dom = self.dom;
		var emails = self.getEmails();

		if (emails.length == 0) 
		{
			alert(this.data('trans-noemail'));
			return false;
		}

		self.setStorage('emails', emails);

		var ui = self.ui,
			win = ui.components.taskWindow;

		win.show();

		$.postAjax({'submit_template_controller': 'getNewsletterTemplates'}).done(function(response){
			if (response.length)
			{
				dom.taskTemplateSelect.empty();
				for (var i = 0; i < response.length; i++)
				{
					var template = response[i],
						option = '<option value="'+template.filename+'" '+(template.selected ? 'selected="selected"' : '')+'>'+template.name+'</option>';

					dom.taskTemplateSelect.append(option);
				}
			}
		});

		var smtp = NewsletterPro.modules.smtp.ui.components.taskSmptSelect,
			selected = smtp.getSelected();

		if (selected != null)
			self.setStorage('id_newsletter_pro_smtp', selected.data.id_newsletter_pro_smtp);
	},

	getEmails: function() {
		return NewsletterProControllers.SendController.getSelectedEmails();
	},

	ui: ({
		components: {},

		init: function() {
			return this;
		},

		add: function(name, value) {
			this.components[name] = value;
		},

		TaskWindow: function TaskWindow(cfg) {
			if (!(this instanceof TaskWindow))
				return new TaskWindow(cfg);

			var self = this;
				task = NewsletterPro.modules.task,
				l = NewsletterPro.translations.l(NewsletterPro.translations.modules.task);

			function setTemplate() {
				var background = $('<div class="gk-background"></div>'),
					template = $('<div><div class="gk-header"><span class="gk-title"></span><a href="javascript:{}" class="gk-close"><i class="icon icon-remove"></i></a></div><div class="bootstrap gk-content"></div></div>'),
					body = $('body'),
					width = cfg.width || 0,
					height = cfg.height || 0;

				background.css({
					width: '100%',
					height: '100%',
					top: 0,
					left: 0,
					position: 'fixed',
					display: 'none',
					'z-index': '99999',
				});

				background.appendTo(body);

				template.css({
					width: cfg.width || 'auto',
					height: cfg.height || 'auto',
					position: 'fixed',
					left: width != 0 ? body.width() / 2 - width / 2 : body.width() / 2,
					top: height != 0 ? $(window).height() / 2 - height / 2 : $(window).height() / 2,
					display: 'none',
					'z-index': '999999',
				});

				template.header = template.find('.gk-header');
				template.content = template.find('.gk-content');
				template.close = template.find('.gk-close');
				template.background = background;

				template.addClass('gk-task-window');
				template.addClass(cfg.className);
				background.addClass(cfg.className+'-'+'background');

				template.appendTo(body);

				addEvents(template, background);

				return template;
			}

			function addEvents(template, background) {
				template.close.on('click', function(event) {
					self.hide();
				});

				background.on('click', function(){
					template.close.trigger('click');
				});

				$(window).resize(function(event) {
					self.resetPosition();
				});
			}

			this.template = setTemplate();

			this.resetPosition = function() {
				self.template.css({
					left: $('body').width() / 2 - self.template.width() / 2,
					top: $(window).height() / 2 - self.template.height() / 2,
				});
			}

			this.setHeader = function(value) {
				this.template.header.find('.gk-title').html(value);
			};

			this.setContent = function(value) {
				this.template.content.html(value);
			};

			this.hide = function() {
				this.template.fadeOut(200);
				return this.template.background.fadeOut(200).promise();
			};

			this.show = function() {

				this.resetPosition();
				this.template.fadeIn(200);

				if (typeof cfg.show === 'function')
					cfg.show(self);

				return this.template.background.fadeIn(200).promise();
			};

			return this;
		},

	}.init()),

}.init(NewsletterPro));
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

NewsletterPro.namespace('modules.forward');
NewsletterPro.modules.forward = ({
	dom: null,
	box: null,
	init: function(box) 
	{
		var self = this;

		self.box = box;

		self.ready(function(dom) {
			var l = NewsletterPro.translations.l(NewsletterPro.translations.modules.forward);

			var dataModelForward = new gk.data.Model({
				id: 'id_newsletter_pro_forward',
			});

			var dataSourceForward = new gk.data.DataSource({
				pageSize: 10,
				transport: {
					read: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=getForwardList',
						dataType: 'json',
					},

					destroy: 
					{
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=deleteForwardFromEmail&id',
						dateType: 'json',
						success: function(response, itemData) 
						{
							if(!response)
								alert(l('delete record'));
						},
						error: function(data, itemData) 
						{
							alert(l('delete record'));
						},
						complete: function(data, itemData) {},
					},

					search: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=searchForwarder&value',
						dataType: 'json',
					},

				},
				schema: {
					model: dataModelForward
				},
				trySteps: 2,
				errors: 
				{
					read: function(xhr, ajaxOptions, thrownError) 
					{
						dataSourceForward.syncStepAvailableAdd(3000, function(){
							dataSourceForward.sync();
						});
					},
				},
			});

			dom.forwardList.gkGrid({
				dataSource: dataSourceForward,
				selectable: false,
				currentPage: 1,
				pageable: true,
				template: {
					count: function(item, value)
					{
						return '<span style="font-weight: bold;">'+item.data.count+'</span>';
					},

					actions: function(item, value) 
					{
						var data = item.data,
							template = $('<div></div>'),
							deleteRecord,
							details;

						deleteRecord = $('#forwarder-delete').gkButton({
							name: 'delete',
							title: l('delete'),
							className: 'btn btn-default btn-margin pull-right forwarder-delete',
							item: item,
							command: 'delete',
							icon: '<i class="icon icon-trash-o"></i> ',
							confirm: function() 
							{
								return confirm(l('delete added confirm'));
							},
						});

						var detailsContent = $('<div></div>');

						details = $('#forwarder-details').gkButton({
							name: 'forwarder-details',
							title: l('details'),
							className: 'btn btn-default btn-margin pull-right forwarder-details',
							icon: '<i class="icon icon-info-circle"></i> ',

							click: function(event) 
							{
								getDetail(item.data.from);

								var content = $('<div></div>');
								var winDetails = gkWindow({
										width: 800,
										height: 500,
										className: 'gk-task-window-details',
										show: function(win) {},
									});

								winDetails.setHeader(l('forward to emails'));

								content.append(detailsContent);
								winDetails.setContent(content);
								winDetails.show();
							}
						});

						append(details);
						append(deleteRecord);

						function append(value)
						{
							template.append(value);
						}

						function getDetail(email) 
						{
							$.postAjax({'submit': 'getForwarderDetails', email: email},'html').done(function(response){
								detailsContent.html(response);
							});
						}

						return template;
					}

				}
			});

			dom.forwardList.addHeader(function(columns){
				var tr, 
					td, 
					searchDiv,
					timer = null, 
					searchText;

				function makeRow(arr) 
				{
					tr = $('<tr></tr>');
					td = $('<th class="gk-header-datagrid customers-header" colspan="'+columns+'"></th>');

					$.each(arr, function(i, item){
						td.append(item);
					});

					tr.html(td);

					return tr;
				}

				searchDiv = $('<div class="customers-search-div"></div>');
				search = $('<input class="gk-input customers-search" type="text">');
				searchLoading = $('<span class="customers-search-loading" style="display: none;"></span>');

				search.on('keyup', function(event) {
					var val = $.trim(search.val());

					if (val.length < 3) 
					{
						dataSourceForward.clearSearch();
						return true;
					}

					searchLoading.show();

					if ( timer != null ) clearTimeout(timer);

					timer = setTimeout(function(){

						dataSourceForward.search(val).done(function(response){
							dataSourceForward.applySearch(response);
							searchLoading.hide();
						});

					}, 300);

				});
				searchText = $('<span>'+l('search')+':</span>');

				searchDiv.append(search);
				searchDiv.append(searchLoading);

				var clearFilters = $('#clear-filters')
						.gkButton({
							name: 'clear-filters',
							title: l('clear filters'),
							className: 'clear-filters',
							css: {
								'padding-left': '10px',
								'padding-right': '10px',
								'margin-left': '0',
								'margin-right': '0',
								'float': 'right',
							},
							attr: {
								'data-checked': 0,
							},

							click: function(event) 
							{

								search.val('');
								dataSourceForward.clearSearch();
							},
							icon: '<i class="icon icon-times"></i> ',
						});

				return makeRow([searchText, searchDiv, clearFilters]);
			}, 'prepend');

			dom.clearForwarders.on('click', function(){
				$.postAjax({'submit': 'clearForwarders'},'html').done(function(response){
					if (parseInt(response))
						dataSourceForward.sync();
					else
						alert(l('delete forwards records error'));
				});
			});

		});

		return self;
	},

	ready: function(func) 
	{
		var self = this;

		$(document).ready(function(){

			self.dom = {
				forwardList: $('#forward-list')	,
				clearForwarders: $('#clear-forwarders'),
			};

			func(self.dom);
		});
	},

	deleteForwardToEmail: function(element)
	{
		var l = NewsletterPro.translations.l(NewsletterPro.translations.modules.forward);

		$.postAjax({'submit': 'deleteForwardToEmail', email: element.data('email')},'html').done(function(response){
			if (parseInt(response))
				element.parent().parent().parent().remove();
			else
				alert(l('delete record'));	
		});
	}

}.init(NewsletterPro));
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

NewsletterPro.namespace('modules.statistics');
NewsletterPro.modules.statistics = ({
	domSave: null,
	dom: null,
	box: null,
	vars: {},
	events: {},
	init: function(box) {
		var self = this,
			statisticsDataSource,
			statisticsDataGrid;

		self.box = box;

		self.ready(function(dom){

			var statisticsDataModel = new gk.data.Model({
				id: 'id_product',
			});

			statisticsDataSource = new gk.data.DataSource({
				pageSize: 15,
				transport: {
					read: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=getStatistics',
						dataType: 'json',
					},
				},
				schema: {
					model: statisticsDataModel
				},
				trySteps: 2,
				errors: 
				{
					read: function(xhr, ajaxOptions, thrownError) 
					{
						statisticsDataSource.syncStepAvailableAdd(3000, function(){
							statisticsDataSource.sync();
						});
					},
				},
			});

			statisticsDataGrid = dom.statisticsTable.gkGrid({
				dataSource: statisticsDataSource,
				selectable: false,
				currentPage: 1,
				pageable: true,
				template: 
				{
					image: function (item, value) 
					{
						return '<img src="'+item.data.thumb_path+'">';
					},
					name: function(item, value) 
					{
						function trim(str, size) {
							size = size || 200;
							if (str.length > size)
								return str.slice(0,size) + '...';
							return str;
						}

						return '<a href="'+item.data.link+'" target="_blank" style="color: #00aff0;">'+trim(value)+'</a>';
					},
				}
			});

			dom.clearStatistics.on('click', function() {
				$.postAjax({'submit': 'clearStatistics'}).done(function(response) {
					if(response.status) {
						statisticsDataSource.sync();
					} else {
						alert(response.errors.join("\n"));
					}
				});
			});
		});

		return self;
	},

	ready: function(func) {
		var self = this;
		$(document).ready(function(){

			self.dom = {
				statisticsTable: $('#statistics-table'),
				clearStatistics: $('#clear-statistics'),
			};

			func(self.dom);
		});
	},

}.init(NewsletterPro));
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

NewsletterPro.namespace('modules.filters');
NewsletterPro.modules.filters = ({
	dom: null,
	box: null,
	init: function(box) {
		var self = this;

		self.box = box;

		self.ready(function(dom) {

		});
	},

	ready: function(func) {
		var self = this;
		$(document).ready(function(){
			self.dom = {

			};
			func(self.dom);
		});
	},
}.init(NewsletterPro));
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

NewsletterPro.namespace('modules.manageImages');
NewsletterPro.modules.manageImages = ({
	dom: null,
	box: null,
	init: function(box) {
		var self = this,
			imagesDataModel,
			imagesDataSource;
		self.box = box;

		function getImageWidth()
		{
			var value = $.trim(self.dom.imageWidth.val());

			if (/^\d+$/.test(value))
				return parseInt(value);

			self.dom.imageWidth.val('');
			return 0;
		}

		function uploadImage()
		{
			var dom = self.dom,
				form = dom.form;

			$.submitAjax({'submit': 'uploadImage', 'name': 'uploadImage', form: form, data: {'width': getImageWidth()} }).done(function(response){
				if (!response.status) 
					box.alertErrors(response.errors);
				else
					imagesDataSource.sync();
			});
		}

		self.ready(function(dom) 
		{
			var imagesGrid = dom.imagesGrid;
			var l = NewsletterPro.translations.l(NewsletterPro.translations.modules.manageImages),

			imagesDataModel = new gk.data.Model({
				id: 'id',
			});

			imagesDataSource = new gk.data.DataSource({
				pageSize: 15,
				transport: {
					read: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=getImages',
						dataType: 'json',
					},

					destroy: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=deleteImage&id',
						type: 'POST',
						dateType: 'json',
						success: function(response, itemData) {
							if(!response.status) {
								alert(response.errors.join("\n"));
							}
						},
						error: function(data, itemData) {
							alert(l('delete image error'));
						},
						complete: function(data, itemData) {},
					},					
				},
				schema: {
					model: imagesDataModel
				},
				trySteps: 2,
				errors: 
				{
					read: function(xhr, ajaxOptions, thrownError) 
					{
						imagesDataSource.syncStepAvailableAdd(3000, function(){
							imagesDataSource.sync();
						});
					},
				},
			});

			imagesGrid.gkGrid({
				dataSource: imagesDataSource,
				selectable: false,
				currentPage: 1,
				pageable: true,
				template: {
					preview: function(item) 
					{
						var data = item.data,
							img = $('<img src="'+data.thumb_link+'" style="width: 50px; height: 50px;">');
						return img;
					},

					size: function(item, value)
					{
						value = parseInt(value);
						return String(Math.round(value / 1024)) + 'kb';
					},

					dimensions: function(item)
					{
						var data = item.data;
						return data.width + 'x' + data.height;
					},

					actions: function(item)
					{
						var data = item.data,
							link = data.link,
							content = $('<div></div>'),
							openImage = $('<a href="'+link+'" target="_blank" class="btn btn-default btn-margin pull-right"><i class="icon icon-eye"></i> <span>'+(l('view image'))+'</span></a>'),
							deleteImage = $('<a href="javascript:{}" class="btn btn-default btn-margin pull-right"><i class="icon icon-trash-o"></i> <span>'+(l('delete image'))+'</span></a>');

						deleteImage.on('click', function(){
							item.destroy('status');
						});

						content.append(openImage);
						content.append(deleteImage);

						return content;
					},
				},

			});

			dom.btnUpload.on('click', function(){
				uploadImage();
			});
		});
	},

	ready: function(func) {
		var self = this;
		$(document).ready(function(){
			self.dom = {
				imagesGrid: $('#images-list'),
				imagesGridBox: $('#images-grid-box'),
				form: $('#upload-image-form'),
				btnUpload: $('#upload-image'),
				imageWidth: $('#upload-image-width'),
			};
			func(self.dom);
		});
	},
}.init(NewsletterPro));
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

NewsletterPro.namespace('components.NewsletterTemplate');
NewsletterPro.components.NewsletterTemplate = function NewsletterTemplate(cfg)
{
	if (!(this instanceof NewsletterTemplate))
		return new NewsletterTemplate(cfg);

	var self = this,
		box = NewsletterPro,
		langLength = box.dataStorage.get('all_languages').length;

	box.extendSubscribeFeature(this);

	this.l = NewsletterPro.translations.l(box.translations.components.NewsletterTemplate);
	this.cfg = cfg;
	this.createTemplate = box.modules.createTemplate;
	this.dom = {};
	this.tiny = [];
	this.tinyLangIndex = [];
	this.viewTabActive = false;
	this.view = {};
	this.view.products = [];
	this.template = {
		container: null,
		content: null,
		products: {},
	};

	this.triggerTitleChange = true;

	box.onObject.setCallback('tinyNewsletter', function(ed){
		self.add(ed);

		if (self.tiny.length == langLength)
		{
			$(document).ready(function(){

				self.createTemplate.tinyInitDfd.resolve();

				self.dom = {
					viewNewsletterTemplate: $('#view-newsletter-template-content'),
					saveTemplate: $('#save-newsletter-template'),
					saveAsTemplate: $('#save-as-newsletter-template'),
					exportHTML: $('#export-html'),
					inputImportHTML: $('#inputImportHTML'),
					inputImportHTMLForm: $('#inputImportHTMLForm'),
					templateStyle: $('#template-css-style'),
					templateGlobalStyle: $('#template-css'),
					templateTitle: $('[id^="page-title-"]'),
					templateHeader: $('[id^="template-header-"]'),
					templateFooter: $('[id^="template-footer-"]'),
					saveMessage: $('#save-newsletter-template-message'),
					langSelect: $('#newsletter-template-lang-select'),
					pageTitleMessage: $('#page-title-message'),
					vewInBrowser: $('#np-view-newsletter-template-in-browser'),
					color: {
						templateContainerColorObj: new jscolor.color(document.getElementById('template-container-color')),
						templateContentColorObj: new jscolor.color(document.getElementById('template-content-color')),
						productsBgColorObj: new jscolor.color(document.getElementById('products-bg-color')),
						productsNameColorObj: new jscolor.color(document.getElementById('products-name-color')),
						productsSDescriptionColorObj: new jscolor.color(document.getElementById('products-s-description-color')),
						productsDescriptionColorObj: new jscolor.color(document.getElementById('products-description-color')),
						productsPriceColorObj: new jscolor.color(document.getElementById('products-price-color')),
						linksColorObj: new jscolor.color(document.getElementById('links-color')),
						productsBorderColorObj: new jscolor.color(document.getElementById('products-border-color')),
					},
				};

				self.ready(self.dom);

			});
		}
	});
};

NewsletterPro.components.NewsletterTemplate.prototype.add = function(ed)
{
	var idLang = parseInt(ed.id.match(/\d+$/));
	this.tiny.push({
		id: ed.id,
		idLang: idLang,
		ed: ed,
	});

	this.tinyLangIndex.push(idLang);
};

NewsletterPro.components.NewsletterTemplate.prototype.ready = function(dom)
{
	var self = this,
		box = NewsletterPro,
		languageSwitcher = box.components.LanguageSelect.getInstanceById('#newsletter-template-lang-select');

	try {
		self.updateTemplate();
	} catch (e) {

	}

	self.parseTiny(function(id, idLang, ed){
		if (box.isTinyHigherVersion())
		{
			ed.on('change', function(ed, l){
				// call this to verify if the porducts exists into template
				self.updateBoth();
			});
		}
		else
		{
			ed.onChange.add(function(ed, l){
				// call this to verify if the porducts exists into template
				self.updateBoth();
			});
		}
	});

	try {
		var txtAreas = [dom.templateStyle, dom.templateGlobalStyle, dom.templateHeader, dom.templateFooter];

		$.each(txtAreas, function(i, item){

			item.on('keydown', function(event){
				var key = event.keyCode,
					textarea = item.get(0);

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
		});
	} catch (error) {
		console.warn(error);
	}

	// fix languge problems
	box.components.LanguageSelect.updateLanguages();

	this.subscribe('save', function(data){

	});

	this.subscribe('afterSave', function(data){

	});

	var getViewInBrowserUrl = function(idLang){
		idLang = idLang || NewsletterPro.dataStorage.get('id_selected_lang');

		return box.getUrl({
				'submit_template_controller': 'viewTemplate',
				'name': box.dataStorage.get('configuration.NEWSLETTER_TEMPLATE'),
				'id_lang': idLang,
		});
	};

	dom.vewInBrowser.on('click', function(){
		event.preventDefault();

		$(this).attr('href', getViewInBrowserUrl());

		window.open($(this).attr('href'),'_blank');
	});

	// console.log('RAMSASSSSSSS');
/*
	languageSwitcher.subscribe('change', function(obj){

		var lang = obj.lang,
			idLang = Number(lang.id_lang),
			viewInBrowserUrl = getViewInBrowserUrl(idLang);

		dom.vewInBrowser.attr('href', viewInBrowserUrl);
	});
*/

	NewsletterProComponents.objs.tabNewsletterTemplate.subscribe('change', function(item){
		var id = item.attr('id');

		if (id === 'tab_newsletter-template_1') 
		{
			self.viewTabActive = true;
			self.saveWriteView();
		}
		else if (id === 'tab_newsletter-template_0') 
			self.tinyRefresh();

		if (id === 'tab_newsletter-template_5' || id === 'tab_newsletter-template_2')
			self.dom.langSelect.hide();
		else
			self.dom.langSelect.show();

		if (id !== 'tab_newsletter-template_1')
			self.viewTabActive = false;
	});

	box.dataStorage.on('change', 'id_selected_lang', function(value){
		if (self.viewTabActive)
			self.render();
	});

	dom.saveTemplate.on('click', function(){
		var btn = $(this);
		box.showAjaxLoader(btn);
		self.saveTemplate().always(function(){
			box.hideAjaxLoader(btn);
		});
	});

	dom.saveAsTemplate.on('click', function(){
		var name = prompt(self.l('Enter the template name.'));

		if (name == '' || name == null)
				return false;

		var btn = $(this);
		box.showAjaxLoader(btn);
		self.saveTemplate(name).always(function(){

			// update the templates
			$.postAjax({'submit_template_controller': 'getNewsletterTemplates'}).done(function(response){
				box.dataStorage.set('templates', response);
			});
			
			box.hideAjaxLoader(btn);
		});
	});

	dom.templateTitle.on('change', function(event){
		var target = $(event.currentTarget),
			val = target.val(),
			idLang = Number(target.data('lang'));

		var header = self.getHeaderByIdLang(idLang);
		var headerVal = header.val();
		var headerNewVal = headerVal.replace(/<\s*?(title)\s*?.*?>(.*?)<\s*?\/\s*?\1\s*?>/, '<title>'+val+'</title>');
		header.val(headerNewVal);
	});

	dom.templateTitle.on('blur', function(){

		if (!self.triggerTitleChange)
			return false;

		var element = $(this),
			value = element.val(),
			idLang = Number(element.data('lang'));

		$.postAjax({ 'submit_template_controller': 'saveNewsletterPageTitle', saveNewsletterPageTitle : value, id_lang: idLang }).done(function(response) {

			if (response.success)
			{
				dom.pageTitleMessage.empty().show().append('\
					<span class="list-action-enable action-enabled">\
						<i class="icon-check"></i>\
					</span>\
				');
			}
			else
				box.alertErrors(response.errors);

			setTimeout(function() { 
				dom.pageTitleMessage.hide(); 
			}, 5000);
		});

	});


	dom.templateHeader.on('blur', function(){
		self.saveTemplate().done(function(response){
			
			self.setTitle(response.html);

			self.refreshStyle();
		});
	});

	dom.templateFooter.on('blur', function(){
		self.saveTemplate().done(function(response){
		});
	});

	dom.templateStyle.on('blur', function(){
		self.saveTemplate().done(function(response){
			self.refreshStyle();
		});
	});

	dom.templateGlobalStyle.on('blur', function(){
		self.saveTemplate().done(function(response){
			self.refreshStyle();
		});
	});

	dom.exportHTML.on('click', function(){
		event.preventDefault();

		var button = $(this),
			href = button.attr('href'),
			conf = confirm(self.l('Do you want to render the template variables?'));

		if (conf)
		{
			href = href.replace(/exportHTML(=\w+)?/, 'exportHTML&renderView');
			button.attr('href', href);
		}

		self.saveTemplate().done(function(response){
			window.location.href = href;
		});
	});

	dom.inputImportHTML.on('change', function(){

		if ($.trim(dom.inputImportHTML.val()) != '')
		{
			$.submitAjax({'submit_template_controller': 'inputImportHTML', 'form': dom.inputImportHTMLForm}).done(function(response){
				if (response.success)
				{

					self.createTemplate.vars.templateDataSource.sync(function(dataSource){
						var currentTemplate = dataSource.getItemByValue('data.filename', response.name);
						if (currentTemplate)
						{
							dataSource.setSelected(currentTemplate);
							self.createTemplate.changeTemplate(currentTemplate);
						}
					});
				}
				else
					box.alertErrors(response.errors);
			});
		}
	});
};

NewsletterPro.components.NewsletterTemplate.prototype.refreshStyle = function(css)
{
	this.parseTiny(function(id, idLang, ed){
		ed.refreshStyle();
	});
};

NewsletterPro.components.NewsletterTemplate.prototype.setTitle = function(html)
{
	for (var idLang in html)
	{
		this.triggerTitleChange = false;
		var title = this.getTitleByIdLang(idLang);
		title.val(html[idLang].title);
		this.triggerTitleChange = true;
	}
};

NewsletterPro.components.NewsletterTemplate.prototype.setTinyContent = function(html)
{
	for (var idLang in html)
	{
		var ed = this.getTinyByIdLang(idLang);
		if (ed)
		{
			ed.setContent(html[idLang].body);
		}

	}
};

NewsletterPro.components.NewsletterTemplate.prototype.saveTemplate = function(name)
{
	var self = this,
		box = NewsletterPro,
		dom = this.dom,
		css = self.stripComments(dom.templateStyle.val()),
		cssGlobal = self.stripComments(dom.templateGlobalStyle.val()),
		templateName,
		action;

	if (typeof name !== 'undefined')
	{
		action = 'saveAsNewsletterTemplate';
		templateName = name;
	}
	else
	{
		action = 'saveNewsletterTemplate';
		templateName = box.dataStorage.get('configuration.NEWSLETTER_TEMPLATE');
	}

	var  content = self.buildContent();

	this.publish('save', {
		saveAs: (action == 'saveAsNewsletterTemplate' ? true : false),
		action: action,
		templateName: templateName,
		content: content,
		css: css,
		cssGlobal: cssGlobal
	});

	return $.postAjax({'submit_template_controller': action, name: templateName, content: content, css: css, css_global: cssGlobal}).done(function(response){

		if (!response.success)
			box.alertErrors(response.errors);
		else
		{
			if (action === 'saveAsNewsletterTemplate')
			{
				self.createTemplate.vars.templateDataSource.sync(function(dataSource){
					var currentTemplate = dataSource.getItemByValue('data.filename', response.template_name);
					dataSource.setSelected(currentTemplate);
					self.createTemplate.changeTemplate(currentTemplate);
				});
			}

			dom.saveMessage.show().html('<p class="np-success-message">' + response.message + '</p>');
			setTimeout(function(){
				dom.saveMessage.hide();
			}, 5000);

		}

		self.publish('afterSave', {
			status: true,
			response: response
		});

	}).fail(function(response){
		self.publish('afterSave', {
			status: false,
			response: response
		});
	}).promise();

};

NewsletterPro.components.NewsletterTemplate.prototype.buildContent = function()
{
	var self = this,
		box = NewsletterPro,
		dom = this.dom;

	var template = {};

	this.parseTiny(function(id, idLang, ed){
		template[idLang] = {
			'title': self.getTitleByIdLang(idLang).val(),
			'body': ed.getContent(),
			'header': self.stripComments(self.getHeaderByIdLang(idLang).val()),
			'footer': self.getFooterByIdLang(idLang).val(),
		};
	});

	return template;
};

NewsletterPro.components.NewsletterTemplate.prototype.setTemplate = function(obj)
{
	var self = this,
		box = NewsletterPro,
		dom = this.dom,
		title = obj.title,
		header = obj.header,
		body = obj.body,
		footer = obj.footer,
		cssFile = obj.css_file,
		cssGlobalFile = obj.css_global_file,
		cssLink = obj.css_link,
		idSelectedLang = box.dataStorage.getNumber('id_selected_lang');

	this.parseTiny(function(id, idLang, ed){
		if (body.hasOwnProperty(idLang)) {
			ed.setContent(body[idLang]);
			
			if (cssLink.hasOwnProperty(idLang)) {
				ed.refreshStyle(cssLink[idLang]);
			}
		}
	});

	this.parseTitle(function(i, idLang, item){
		if (title.hasOwnProperty(idLang)) {
			item.val(title[idLang]);
		}
	});
	
	this.parseHeader(function(i, idLang, item){
		if (header.hasOwnProperty(idLang)) {
			item.val(header[idLang]);
		}
	});
	
	this.parseFooter(function(i, idLang, item){
		if (footer.hasOwnProperty(idLang)) {
			item.val(footer[idLang]);
		}
	});

	if (cssFile.hasOwnProperty(idSelectedLang)) {
		dom.templateStyle.val(cssFile[idSelectedLang]);
	}

	if (cssGlobalFile.hasOwnProperty(idSelectedLang)) {
		dom.templateGlobalStyle.val(cssGlobalFile[idSelectedLang]);
	}

	if (self.isViewActive)
		self.render();

	self.updateBoth();
};

NewsletterPro.components.NewsletterTemplate.prototype.getTitleByIdLang = function(idLang)
{
	return 	$(this.dom.templateTitle.filter(function(i, item){
		return (Number($(item).data('lang')) == Number(idLang));
	})[0]);
};

NewsletterPro.components.NewsletterTemplate.prototype.getHeaderByIdLang = function(idLang)
{
	return 	$(this.dom.templateHeader.filter(function(i, item){
		return (Number($(item).data('lang')) == Number(idLang));
	})[0]);
};

NewsletterPro.components.NewsletterTemplate.prototype.getFooterByIdLang = function(idLang)
{
	return 	$(this.dom.templateFooter.filter(function(i, item){
		return (Number($(item).data('lang')) == Number(idLang));
	})[0]);
};

NewsletterPro.components.NewsletterTemplate.prototype.parseTitle = function(func)
{
	$.each(this.dom.templateTitle, function(i, item){
		item = $(item)
		var idLang = Number(item.data('lang'));
		func(i, idLang, item);
	});
};

NewsletterPro.components.NewsletterTemplate.prototype.parseHeader = function(func)
{
	$.each(this.dom.templateHeader, function(i, item){
		item = $(item)
		var idLang = Number(item.data('lang'));
		func(i, idLang, item);
	});
};

NewsletterPro.components.NewsletterTemplate.prototype.parseFooter = function(func)
{
	$.each(this.dom.templateFooter, function(i, item){
		item = $(item)
		var idLang = Number(item.data('lang'));
		func(i, idLang, item);
	});
};

NewsletterPro.components.NewsletterTemplate.prototype.parseTiny = function(func)
{
	for (var i = 0; i < this.tiny.length; i++)
	{
		var item = this.tiny[i],
			ed = item.ed,
			id = item.id,
			idLang = Number(item.idLang);

		var ret = func(id, idLang, ed);

		if (typeof ret !== 'undefined')
			return ret;
	}
};

NewsletterPro.components.NewsletterTemplate.prototype.getTinyByIdLang = function(idLang)
{
	var index = this.tinyLangIndex.indexOf(Number(idLang));

	if (index != -1)
		return this.tiny[index].ed;
};

NewsletterPro.components.NewsletterTemplate.prototype.stripComments = function(str)
{
	return str.replace(/\/\*[\s\S]*?\*\//, '');
};

NewsletterPro.components.NewsletterTemplate.prototype.tinyRefresh = function()
{
	this.parseTiny(function(id, idLang, ed){
		if ($.browser.hasOwnProperty('mozilla')) 
			ed.setContent(ed.getContent());
	});
};

NewsletterPro.components.NewsletterTemplate.prototype.saveWriteView = function()
{
	var self = this;
	this.saveTemplate().done(function(response){
		if (response.success)
			self.render();
	});
};

NewsletterPro.components.NewsletterTemplate.prototype.render = function()
{
	var box = NewsletterPro,
		self = this,
		idLang = box.dataStorage.getNumber('id_selected_lang'),
		templateName = box.dataStorage.get('configuration.NEWSLETTER_TEMPLATE');

	$.postAjax({'submit_template_controller': 'renderTemplate', name: templateName, id_lang: idLang}).done(function(response){
		if (!response.success)
		{
			box.displayAlert(response.errors);
			self.writeView('');
		}
		else
			self.writeView(response.render);
	}).promise();
};

NewsletterPro.components.NewsletterTemplate.prototype.writeView = function(content)
{
	var self = this;

	if (this.getView().length > 0) 
	{
		var html = this.getView().get(0);

		html.innerHTML = content;

		setTimeout(function(){
			self.resizeView();
			self.updateBoth();
		}, 50);
	}
};

NewsletterPro.components.NewsletterTemplate.prototype.resizeView = function()
{
	if (this.getView().length > 0) 
	{
		var body = this.getView().find('body');
		if (body.length > 0) 
		{
			var wnt = this.dom.viewNewsletterTemplate.get(0);
			wnt.height = '';
			wnt.height = wnt.contentWindow.document.body.scrollHeight + "px";
		}
	}
};

NewsletterPro.components.NewsletterTemplate.prototype.getView = function()
{
	return this.dom.viewNewsletterTemplate.contents().find('html');
};

NewsletterPro.components.NewsletterTemplate.prototype.updateBoth = function()
{
	this.updateView();
	this.updateTemplate();
};

NewsletterPro.components.NewsletterTemplate.prototype.updateView = function()
{
	var self = this,
		box = NewsletterPro,
		view = this.view;

	view['container'] = this.getView().find('.newsletter-pro-container');
	view['content'] = view.container.find('.newsletter-pro-content');
	view['links'] = view.container.find('a');

	var products = view.container.find('.newsletter-pro-product');

	view.products = [];

	if (products.length > 0) 
	{
		for (var i = 0; i < products.length; i++) 
		{
			var product = $(products[i]),
				name = product.find('.newsletter-pro-name'),
				description = product.find('.newsletter-pro-description'),
				description_short = product.find('.newsletter-pro-description_short'),
				price = product.find('.newsletter-pro-price');

			var obj = {
				product: product,
				name: name,
				description: description,
				description_short: description_short,
				price: price,
			};

			// view.products = view.products || [];
			view.products.push(obj);
		}
	}
};

NewsletterPro.components.NewsletterTemplate.prototype.updateTemplate = function()
{
	var self = this,
		box = NewsletterPro,
		template = this.template,
		idSelectedLang = box.dataStorage.getNumber('id_selected_lang'),
		ct = this.createTemplate,
		vars = this.createTemplate.vars,
		ctDom = this.createTemplate.dom,
		dom = self.dom,
		tabs = NewsletterProComponents.objs.tabNewsletterTemplate;


	template['tinyBody'] = this.tinySelect('body');
	template['container'] = this.tinySelect('.newsletter-pro-container');
	template['content'] = this.tinySelect('.newsletter-pro-content');
	template['links'] = this.tinySelect('a');

	var products = this.tinySelect('.newsletter-pro-product');

	template.products = {};

	for (var idLang in products)
	{
		if (products[idLang].length > 0) 
		{
			var prod = [];

			for (var i = 0; i < products[idLang].length; i++) 
			{
				var product = $(products[idLang][i]),
					name = product.find('.newsletter-pro-name'),
					description = product.find('.newsletter-pro-description'),
					description_short = product.find('.newsletter-pro-description_short'),
					price = product.find('.newsletter-pro-price');

				var obj = {
					product: product,
					name: name,
					description: description,
					description_short: description_short,
					price: price,
				};

				prod.push(obj);
			}

			template.products[idLang] = prod;
		}
	}

	if (ct.varExists('templateWidthSlider')) 
	{
		var width = template.content[idSelectedLang].innerWidth();
		vars.templateWidthSlider.setValue(width);
	}

	var containerColor = template.container[idSelectedLang].css('background-color');
	if (/rgb/i.test(containerColor))
		containerColor = self.rgb2hex(containerColor);

	ctDom.templateContainerColor.val(containerColor);

	self.setStyleObject(template.tinyBody, {
		'background-color': '#'+containerColor
	});

	if (template.container[idSelectedLang].length > 0 ) 
	{
		dom.color.templateContainerColorObj.importColor();
		ctDom.templateContainerColor.parent().show();
	} 
	else 
	{
	 	ctDom.templateContainerColor.parent().hide();
	}

	// Content bg color
	var contentColor = template.content[idSelectedLang].css('background-color');
	if (/rgb/i.test(contentColor))
		contentColor = self.rgb2hex(contentColor);

	ctDom.templateContentColor.val(contentColor);


	if (template.content[idSelectedLang].length > 0)
	{
		dom.color.templateContentColorObj.importColor();

		ctDom.templateContentColor.parent().show();
		ctDom.sliderContainer.show();

		// show global css
		ctDom.tabGlobalCss.show();
	} 
	else 
	{
		ctDom.tabGlobalCss.hide();

		if (tabs.lastItem !== null && typeof tabs.lastItem !== 'undefined' )
		{	
			if (tabs.lastItem.attr('id') === 'tab_newsletter-template_2')
				tabs.buttons[0].click();
		}

		// hide global css
		ctDom.sliderContainer.hide();
		ctDom.templateContentColor.parent().hide();
	}

	// Product border color
	var products = template.products;
	
	if (products.hasOwnProperty(idSelectedLang) && products[idSelectedLang].length > 0) 
	{
		// Products bg color
		var product = products[idSelectedLang][0].product;
		if (product.length > 0) 
		{
			var color = product.css('background-color');

			if (/rgb/i.test(color))
				color = self.rgb2hex(color);

			ctDom.productsBgColor.val(color);
			ctDom.productsBgColor.parent().show();

			var borderW = product.css('border-width');

			if (/^[1-9]+/.test(borderW))
			{
				var color = product.css('border-color');

				if (/rgb/i.test(color))
					color = self.rgb2hex(color);	

				ctDom.productsBorderColor.parent().show();
				ctDom.productsBorderColor.val(color);

			} 
			else 
			{
				ctDom.productsBorderColor.parent().hide();
			}

		}
		else 
		{
			ctDom.productsBgColor.parent().hide();
			ctDom.productsBorderColor.parent().hide();
		}

		// Products name color
		var name = products[idSelectedLang][0].name;

		if (name.length > 0) {

			var color = name.css('color');
			if (/rgb/i.test(color))
				color = self.rgb2hex(color);	

			ctDom.productsNameColor.val(color);
			ctDom.productsNameColor.parent().show();
		}
		else 
		{
			ctDom.productsNameColor.parent().hide();
		}

		// Short description color
		var description_short = products[idSelectedLang][0].description_short;
		if (description_short.length > 0) 
		{
			var color = description_short.css('color')	;
			if (/rgb/i.test(color))
				color = self.rgb2hex(color);	

			ctDom.productsSDescriptionColor.val(color);
			ctDom.productsSDescriptionColor.parent().show();
		}
		else 
		{
			ctDom.productsSDescriptionColor.parent().hide();
		}

		// Description color
		var description = products[idSelectedLang][0].description;
		if (description.length > 0) {

			var color = description.css('color')	;
			if (/rgb/i.test(color))
				color = self.rgb2hex(color);	

			ctDom.productsDescriptionColor.val(color);
			ctDom.productsDescriptionColor.parent().show();
		}
		else 
		{
			ctDom.productsDescriptionColor.parent().hide();
		}

		// Price color
		var price = products[idSelectedLang][0].price;

		if (price.length > 0) 
		{
			var color = price.css('color')	;
			if (/rgb/i.test(color))
				color = self.rgb2hex(color);	

			ctDom.productsPriceColor.val(color);
			ctDom.productsPriceColor.parent().show();
		}
		else 
		{
			ctDom.productsPriceColor.parent().hide();
		}
	} 
	else 
	{
		ctDom.productsBgColor.val('FFFFFF');
		ctDom.productsBgColor.parent().hide();

		ctDom.productsBorderColor.val('FFFFFF');
		ctDom.productsBorderColor.parent().hide();

		ctDom.productsNameColor.val('FFFFFF');
		ctDom.productsNameColor.parent().hide();

		ctDom.productsSDescriptionColor.val('FFFFFF');
		ctDom.productsSDescriptionColor.parent().hide();

		ctDom.productsDescriptionColor.val('FFFFFF');
		ctDom.productsDescriptionColor.parent().hide();

		ctDom.productsPriceColor.val('FFFFFF');
		ctDom.productsPriceColor.parent().hide();
	}

	// }

	dom.color.productsBgColorObj.importColor();
	dom.color.productsBorderColorObj.importColor();
	dom.color.productsNameColorObj.importColor();
	dom.color.productsSDescriptionColorObj.importColor();
	dom.color.productsDescriptionColorObj.importColor();
	dom.color.productsPriceColorObj.importColor();

	// All links color
	ctDom.linksColor.val('FFFFFF');

	if (template.links[idSelectedLang].length > 0 ) 
	{
		ctDom.linksColor.parent().show();
	} 
	else 
	{
		ctDom.linksColor.parent().hide();
	}

	dom.color.linksColorObj.importColor();

	ct.refreshSliders();
};


NewsletterPro.components.NewsletterTemplate.prototype.tinySelect = function(name, idLang)
{
	var self = this,
		select = false;

	if (typeof idLang === 'undefined')
	{
		select = {};

		this.parseTiny(function(id, idLang, ed){
			select[idLang] = $(ed.dom.select(name));
		});
	}
	else
	{
		var ed = this.getTinyByIdLang(idLang);
		select = $(ed.dom.select(name));
	}

	return select;
};

NewsletterPro.components.NewsletterTemplate.prototype.setStyle = function(selection, name, value, lang)
{
	if (typeof lang !== 'undefined')
	{
		var ed = this.getTinyByIdLang(lang);
		
		if (ed)
			ed.dom.setStyle(selection, name, value);
	}
	else
	{		
		for (var idLang in selection)
		{
			var ed = this.getTinyByIdLang(idLang);

			if (ed)
				ed.dom.setStyle(selection[idLang], name, value);
		}
	}
};

NewsletterPro.components.NewsletterTemplate.prototype.setStyleObject = function(obj, css)
{
	for (var idLang in obj)
	{
		obj[idLang].css(css);
	}
};

NewsletterPro.components.NewsletterTemplate.prototype.isViewActive = function()
{
	return this.view.hasOwnProperty('container');
};

NewsletterPro.components.NewsletterTemplate.prototype.isLoadTemplate = function()
{
	return (typeof this.template.container === 'undefined' || this.template.container === null) ? false : true;
};

NewsletterPro.components.NewsletterTemplate.prototype.rgb2hex = function(rgb)
{
	rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
	if (rgb != null) 
	{
		return  ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
				("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
				("0" + parseInt(rgb[3],10).toString(16)).slice(-2);
	}
};
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

NewsletterPro.namespace('modules.sendNewsletters');
NewsletterPro.modules.sendNewsletters = ({
	domSave: null,
	dom: null,
	box: null,
	vars: {},
	events: {},
	init: function(box) 
	{
		var self = this;
		self.box = box;
		self.initCustomersGrid();
		self.initVisitorsGrid();
		self.initVisitorsGridNewsletterPro();
		self.initAddedGrid();

		this.ready(function(dom){
			var l = NewsletterPro.translations.l(NewsletterPro.translations.modules.sendNewsletters),
				exclusionWindow,
				exclusionWindowContent,
				dataModelExclusionEmails,
				dataSourceExclusionEmails,
				dataSourceExclusionEmailList,
				exclusionEmailFooter,
				performancesWindow,
				performancesWindowContent,
				dataModelConnection,
				dataSourceConnection,
				dataGridConnection,
				smtpSelect,
				filterSelection;

			box.dataStorage.on('change', 'count_send_connections', function(value){
				updateSendMethodDisplay();
			});
			// exclisionView
			exclusionViewWindow = new gkWindow({
				width: 640,
				height: 480,
				setScrollContent: 420,
				title: l('exclusion emails'),
				className: 'exclusion-window',
				show: function(win) 
				{
					if(typeof dataSourceExclusionEmailList !== 'undefined')
						dataSourceExclusionEmailList.sync();
				},
				close: function(win) {},
				content: function(win) 
				{
					var tpl = $('\
						<div id="exclusion-view-box">\
							<div style="margin-top: 10px;">\
								<h4>'+l('List of excluded emails')+'</h4>\
								<table id="exclusion-view" class="table table-bordered exclusion-view">\
									<thead>\
										<tr>\
											<th class="np-checkbox" data-template="checkbox">&nbsp;</th>\
											<th class="email" data-field="email">'+l('Email')+'</th>\
										</tr>\
										</thead>\
								</table>\
							</div>\
						<div>\
					');

					var exclusionEmailList = tpl.find('#exclusion-view');
					if (exclusionEmailList.length)
					{
						dataModelExclusionEmailList = new gk.data.Model({
							'id' : 'id_newsletter_pro_tpl_exclu',
						});

						dataSourceExclusionEmailList = new gk.data.DataSource({
							pageSize: 10,
							transport: {
								read: {
									url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=getExclusionList',
									dataType: 'json',
								},
							},
							schema: {
								model: dataModelExclusionEmailList
							},
							trySteps: 2,
							errors: 
							{
								read: function(xhr, ajaxOptions, thrownError) 
								{
									dataSourceExclusionEmailList.syncStepAvailableAdd(3000, function(){
										dataSourceExclusionEmailList.sync();
									});

								},
							},
						});

						exclusionEmailList.gkGrid({
							dataSource: dataSourceExclusionEmailList,
							checkable: false,
							selectable: false,
							currentPage: 1,
							pageable: true,
							template: {
								actions: function(item, value)
								{

								},
								/*checkbox: function(item, value) 
								{
									var checkBox = $('<input type="checkbox" value="'+value+'" '+(item.isChecked() ? 'checked="checked"' : '')+'> ');
									return checkBox;
								},
								emails_count: function(item, value)
								{
									return '<span style="font-weight: bold;">'+value+'</span>';
								},
								emails_success: function(item, value)
								{
									return '<span style="color: green; font-weight: bold;">'+value+'</span>';
								},
								emails_error: function(item, value)
								{
									return '<span style="color: red; font-weight: bold;">'+value+'</span>';
								},
								type: function(item, value)
								{
									return item.data.type;
								}*/
							}
						});
					}
					exclusionViewWindowContent = tpl;
					return exclusionViewWindowContent;
				}
			});

			// exclusionView
			exclusionWindow = new gkWindow({
				width: 640,
				height: 600,
				setScrollContent: 540,
				title: l('exclusion emails'),
				className: 'exclusion-window',
				show: function(win) 
				{
					if(typeof dataSourceExclusionEmails !== 'undefined')
						dataSourceExclusionEmails.sync();
				},
				close: function(win) {},
				content: function(win) 
				{
					var tpl = $('\
						<div id="exclusion-email-box">\
							<div class="form-group clearfix">\
								<div class="col-sm-4">\
									<label for="input-exclude-emails" class="control-label"><span class="label-tooltip">'+l('import from csv file')+'</span></label>\
								</div>\
								<div class="col-sm-8">\
									<form id="form-exclusion-emails" method="post" enctype="multipart/form-data">\
										<div class="input-group">\
											<span class="input-group-addon">'+l('File')+'</span>\
											<input type="file" name="exclusion_emails_emails" class="form-control">\
											<span class="input-group-addon">'+l('Separator')+'</span>\
											<input type="text" name="exclusion_emails_csv_separator" class="form-control text-center" value=";" style="width: 35px;">\
											<div class="clear"></div>\
										</div>\
									</form>\
								</div>\
								<div class="col-sm-8 col-sm-offset-4">\
									<a id="btn-add-exclusion-csv" class="btn btn-default pull-left" href="javascript:{}"><span class="btn-ajax-loader"></span><i class="icon icon-plus-square"></i> '+l('add to exclusion')+'</a>\
								</div>\
							</div>\
							<div style="margin-top: 10px;">\
								<h4>'+l('select emails from history')+'</h4>\
								<table id="exclusion-email-send-history" class="table table-bordered exclusion-email-send-history">\
									<thead>\
										<tr>\
											<th class="np-checkbox" data-template="checkbox">&nbsp;</th>\
											<th class="template" data-field="template">'+l('template name')+'</th>\
											<th class="date" data-field="date">'+l('template date')+'</th>\
											<th class="emails-count" data-field="emails_count">'+l('total emails')+'</th>\
											<th class="emails-success" data-field="emails_success">'+l('sent success')+'</th>\
											<th class="emails-error" data-field="emails_error">'+l('sent errors')+'</th>\
											<th class="type" data-template="type">'+l('type')+'</th>\
										</tr>\
										</thead>\
								</table>\
							</div>\
						<div>\
					');

					var exclusionEmailSendHistory = tpl.find('#exclusion-email-send-history');
					if (exclusionEmailSendHistory.length)
					{
						dataModelExclusionEmails = new gk.data.Model({
							'id' : 'id_newsletter_pro_tpl_history',
						});

						dataSourceExclusionEmails = new gk.data.DataSource({
							pageSize: 7,
							transport: {
								read: {
									url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=getHistoryExclusion',
									dataType: 'json',
								},
							},
							schema: {
								model: dataModelExclusionEmails
							},
							trySteps: 2,
							errors: 
							{
								read: function(xhr, ajaxOptions, thrownError) 
								{
									dataSourceExclusionEmails.syncStepAvailableAdd(3000, function(){
										dataSourceExclusionEmails.sync();
									});

								},
							},
						});

						exclusionEmailSendHistory.gkGrid({
							dataSource: dataSourceExclusionEmails,
							checkable: true,
							selectable: false,
							currentPage: 1,
							pageable: true,
							template: {
								actions: function(item, value)
								{

								},
								checkbox: function(item, value) 
								{
									var checkBox = $('<input type="checkbox" value="'+value+'" '+(item.isChecked() ? 'checked="checked"' : '')+'> ');
									return checkBox;
								},
								emails_count: function(item, value)
								{
									return '<span style="font-weight: bold;">'+value+'</span>';
								},
								emails_success: function(item, value)
								{
									return '<span style="color: green; font-weight: bold;">'+value+'</span>';
								},
								emails_error: function(item, value)
								{
									return '<span style="color: red; font-weight: bold;">'+value+'</span>';
								},
								type: function(item, value)
								{
									return item.data.type;
								}
							}
						});

						exclusionEmailSendHistory.addFooter(function(columns){
							var check = self.createCheckToggle('btn-exclusion-checkall', dataSourceExclusionEmails)
							check.addClass('pull-left');
							
							var tpl = $('\
								<div class="clearfix pull-left" style="margin-left: 4px;">\
									<input id="exclusion-remaingin-emails" class="valign-middle pull-left" type="checkbox" style="margin-left: 8px; margin-top: 9px; margin-right: 5px;">\
									<label for="exclusion-remaingin-emails" class="control-label valign-middle pull-left">'+l('remaining email')+'</label>\
									<input id="exclusion-sent-emails" class="align-middle pull-left" type="checkbox" checked="checked" style="margin-left: 8px; margin-top: 9px; margin-right: 5px;">\
									<label for="exclusion-sent-emails" class="control-label valign-middle pull-left">'+l('sent email')+'</label>\
								</div>\
							');

							var add = $('<a href="javascript:{}" class="btn btn-default pull-right"><span class="btn-ajax-loader"></span><i class="icon icon-plus-square"></i> '+l('add to exclusion')+'</a>');

							add.on('click', function(){
								var selectedItem = dataSourceExclusionEmails.getSelection();
								var selectedData = [];
								if (selectedItem.length > 0)
								{
									for(var obj in selectedItem) 
									{
										var data = selectedItem[obj].data,
											type = data.type,
											id = (data.hasOwnProperty('id_newsletter_pro_send') ? data.id_newsletter_pro_send : data.id_newsletter_pro_task);

										selectedData.push({
											'id': id,
											'type': type,
										});
									}
								}

								var re = $('#exclusion-remaingin-emails').is(':checked') ? 1 : 0;
								var se = $('#exclusion-sent-emails').is(':checked') ? 1 : 0;

								box.showAjaxLoader(add);
								$.postAjax({'submit': 'addHistoryEmailsToExclusion', 'data': selectedData, 'remainingEmails': re, 'sentEmails': se}).done(function(response){
									box.hideAjaxLoader(add);
									if (!response.success)
										box.alertErrors(response.errors);
									else
									{
										dom.exclusionEmailsCount.html(response.count);
										alert(box.displayAlert(response.msg));
									}
								}).always(function(){
									box.hideAjaxLoader(add);
								});
							});

							

							return exclusionEmailSendHistory.makeRow([check, tpl, add]);
						}, 'prepend');
					}

					var btnAddExclusionCsv = tpl.find('#btn-add-exclusion-csv');

					btnAddExclusionCsv.on('click', function(){
						box.showAjaxLoader(btnAddExclusionCsv);
						$.submitAjax({'submit': 'addCsvEmailsToExclusion', name: 'addCsvEmailsToExclusion', form: $('#form-exclusion-emails')}).done(function(response){
							box.hideAjaxLoader(btnAddExclusionCsv);
							if (response.success)
							{
								dom.exclusionEmailsCount.html(response.count);
								alert(box.displayAlert(response.msg));
							}
							else
								box.alertErrors(response.errors);
						}).always(function(){
							box.hideAjaxLoader(btnAddExclusionCsv);
						})
					});

					exclusionWindowContent = tpl;
					return exclusionWindowContent;
				}
			});

			var performancesWindowTpl;

			performancesWindow = new gkWindow({
				width: 640,
				height: 475,
				title: l('Performances & Limits'),
				className: 'performances-window',
				setScrollContent: 415,
				show: function(win) 
				{
					if(typeof dataSourceConnection !== 'undefined')
					{
						dataSourceConnection.sync(function(){
							box.dataStorage.set('count_send_connections', this.count());
						});
					}

					// update the smtp select
					var select = box.modules.smtp.ui.components.smptSelect;
					if (typeof select !== 'undefined' && typeof smtpSelect !== 'undefined')
					{
						var data = select.getData(),
						options = self.getSelectOptions(data);

						smtpSelect.refresh(options);
					}

				},
				close: function(win) {},
				content: function(win) 
				{
					var storage = box.dataStorage,
						getThrottlerTypeText = function(value) {
							value = value || storage.getNumber('configuration.SEND_THROTTLER_TYPE');
							return (value == box.define.SEND_THROTTLER_TYPE_EMAILS ? l('emails') : 'MB');
						},
						getThrottlerTypeButtonText = function(value) {
							value = value || storage.getNumber('configuration.SEND_THROTTLER_TYPE');
							return '(' + (value == box.define.SEND_THROTTLER_TYPE_EMAILS ? l('change limit to MB') : l('change limit to emails')) + ')';
						};

					var tpl = performancesWindowTpl = $('\
						<div id="np-performances-window-content" class="np-performances-window-content">\
							<h4>'+l('Define the sending performances and limits.')+'</h4>\
							<div class="np-send-method-sleep">\
								<input id="np-radio-send-method-sleep" name="SEND_METHOD" value="' + box.define.SEND_METHOD_DEFAULT + '" type="radio" ' + (storage.getNumber('configuration.SEND_METHOD') == box.define.SEND_METHOD_DEFAULT ? ' checked="checked" ' : '') + '>\
								<span data-for="np-radio-send-method-sleep">'+l('Send one newsletter at')+'</span> \
								<input id="np-send-method-sleep-input" class="gk-input" type="number" min="0" max="60" value="' + storage.get('configuration.SLEEP') + '"> \
								<span data-for="np-radio-send-method-sleep">'+l('seconds')+'</span>\
								<span style="display: inline-block; margin-bottom: 5px; font-style: italic;">('+l('for all connections')+')</span>\
							</div>\
							<div class="np-send-method-antiflod">\
								<input id="np-radio-send-method-antiflood" class="np-radio-send-method-antiflood" name="SEND_METHOD" value="' + box.define.SEND_METHOD_ANTIFLOOD + '" type="radio" ' + (storage.getNumber('configuration.SEND_METHOD') == box.define.SEND_METHOD_ANTIFLOOD ? ' checked="checked" ' : '') + '> \
								<span data-for="np-radio-send-method-antiflood">'+l('Antiflood & Speed limits')+'</span>\
								<span style="display: inline-block; margin-bottom: 5px; font-style: italic;">('+l('for each connection')+')</span>\
								<div class="np-send-method-antiflod-settings">\
									<div class="np-send-method-antiflod-row">\
										<input id="np-send-antifllod-active" name="SEND_ANTIFLOOD_ACTIVE" type="checkbox" ' + (storage.getNumber('configuration.SEND_ANTIFLOOD_ACTIVE') ? ' checked="checked" ' : '' ) + '> \
										<span data-for="np-send-antifllod-active">'+l('Reconnect after')+'</span> \
										<input id="np-send-antiflood-emails"class="gk-input" type="number" min="1" max="100" value="' + (storage.get('configuration.SEND_ANTIFLOOD_EMAILS')) + '"> \
										<span data-for="np-send-antifllod-active">'+l('emails sent, and pause')+'</span> \
										<input id="np-send-antiflood-sleep" class="gk-input" type="number" min="0" max="60" value="' + (storage.get('configuration.SEND_ANTIFLOOD_SLEEP')) + '"> \
										<span>'+l('seconds')+'</span>\
										<div id="np-send-antiflood-info" style="display: none; margin-left: 22px; margin-bottom: 5px;">\
											<br>\
											<span style="display: inline-block; font-style: italic;"></span>\
										</div>\
									</div>\
									<div class="np-send-method-antiflod-row">\
										<input id="np-send-throttler-active" name="SEND_THROTTLER_ACTIVE" type="checkbox" ' + (storage.getNumber('configuration.SEND_THROTTLER_ACTIVE') ? ' checked="checked" ' : '' ) + '> \
										<span data-for="np-send-throttler-active">'+l('Limit')+'</span> \
										<input id="np-send-throttler-limit" class="gk-input" type="number" min="1" max="5000" value="' + (storage.getNumber('configuration.SEND_THROTTLER_LIMIT')) + '"> \
										<span id="np-send-throttler-type-text" data-for="np-send-throttler-active">' + getThrottlerTypeText() + '</span> \
										<span data-for="np-send-throttler-active">'+l('per minute')+'</span> \
										<a id="np-send-throttler-changetype" class="np-send-throttler-changetype" href="javascript:{}">' + getThrottlerTypeButtonText() + '</a>\
										<div id="np-send-throttler-info" style="display:none; margin-left: 22px; margin-bottom: 5px;">\
											<br>\
											<span style="display: inline-block; font-style: italic;"></span>\
										</div>\
									</div>\
								</div>\
							</div>\
							<div style="margin-top: 20px;">\
								<h4 for="input-exclude-emails" class="label-spacing" style="margin-bottom: 0;">'+l('Add or remove connections')+'</h4>\
								<span style="color: red; font-style: italic;">'+l('Don\'t add to many connections from the same server. You will risk to be banned.')+'</span>\
								<span style="display: inline-block; margin-bottom: 5px; font-style: italic;">'+l('Leave the table empty if you want to have a single connection with the default configuration')+'</span>\
								<table id="np-send-connection" class="table table-bordered np-send-connection">\
									<thead>\
										<tr>\
											<th class="name" data-field="name">'+l('Connection Name')+'</th>\
											<th class="connection-type" data-field="connection_type">'+l('Connection Type')+'</th>\
											<th class="connection-test" data-template="connection_test">'+l('Test Connection')+'</th>\
											<th class="actions" data-template="actions">'+l('Actions')+'</th>\
										</tr>\
									</thead>\
								</table>\
							</div>\
							<div class="form-group clearfix" style="margin-top: 10px;">\
								<label class="control-label col-sm-3"><span class="label-tooltip">'+l('Backend limit')+'</span></label>\
								<div class="col-sm-9">\
									<input id="np-send-backend-limit" type="number" class="form-control fixed-width-xs" value="'+box.dataStorage.get('configuration.SEND_LIMIT_END_SCRIPT')+'" min="3" max="100">\
								<div>\
								<p class="help-block">'+l('Decrease this number if the newsletter stops from the sending process, and it\'s not continue.')+'</p>\
							</div>\
						</div>\
					');

					var dataFor = tpl.find('[data-for]');

					dataFor.css('cursor', 'default');

					dataFor.on('click', function(event){
						var currentTarget = $(event.currentTarget),
							id = currentTarget.data('for'),
							selector = $('#'+id);

						if (selector.length)
							selector.trigger('click');
					});

					var regexNumber = /^\d+$/;

					tpl.find('#np-send-backend-limit').on('change', function(){
						var value = Number($(this).val());

						if (isNaN(value))
							value = box.dataStorage.getNumber('configuration.SEND_LIMIT_END_SCRIPT');

						if (value >= 100)
							value = 100;
						else if (value <= 3)
							value = 3;

						$(this).val(value);

						box.dataStorage.set('configuration.SEND_LIMIT_END_SCRIPT', value);

						$.updateConfiguration('SEND_LIMIT_END_SCRIPT', value).done(function(response){
							if (!response.success)
								box.alertErrors(response.errors);
						});
					});

					tpl.find('#np-send-throttler-changetype').on('click', function(){
						var currentValue = storage.getNumber('configuration.SEND_THROTTLER_TYPE'),
							value = (currentValue ? 0 : 1),
							btn = $(this);

						tpl.find('#np-send-throttler-type-text').html(getThrottlerTypeText(value));
						btn.html(getThrottlerTypeButtonText(value));

						$.updateConfiguration('SEND_THROTTLER_TYPE', value).done(function(response){
							if (response.success)
								storage.set('configuration.SEND_THROTTLER_TYPE', value);
							else
								box.alertErrors(response.errors);

						}).always(function(){
							tpl.find('#np-send-throttler-type-text').html(getThrottlerTypeText());
							btn.html(getThrottlerTypeButtonText());
							updateSendMethodDisplay();
						});
					});

					tpl.find('[name="SEND_METHOD"]').on('change', function(){
						var btn = $(this),
							value = btn.val();

						$.updateConfiguration('SEND_METHOD', value).done(function(response){
							if (response.success)
								storage.set('configuration.SEND_METHOD', value);
							else
								box.alertErrors(response.errors);
						}).always(function(){
							updateSendMethodDisplay();
						});
					});

					tpl.find('#np-send-method-sleep-input').on('blur', function(){
						var btn = $(this),
							value = btn.val();

						if (String(value).match(regexNumber) === null || (value < 0 || value > 100))
						{
							btn.val(storage.get('configuration.SLEEP'));
							return;
						}

						$.updateConfiguration('SLEEP', value).done(function(response){
							if (response.success)
								storage.set('configuration.SLEEP', value);
							else
								box.alertErrors(response.errors);
						}).always(function(){
							updateSendMethodDisplay();
						});
					});

					tpl.find('#np-send-throttler-limit').on('blur', function(){
						var btn = $(this),
							value = btn.val();

						if (String(value).match(regexNumber) === null || (value < 1 || value > 5000))
						{
							btn.val(storage.get('configuration.SEND_THROTTLER_LIMIT'));
							return;
						}

						$.updateConfiguration('SEND_THROTTLER_LIMIT', value).done(function(response){
							if (response.success)
								storage.set('configuration.SEND_THROTTLER_LIMIT', value);
							else
								box.alertErrors(response.errors);
						}).always(function(){
							updateSendMethodDisplay();
						});
					});

					var antifloodValidateMsg = l('At least one antiflood option needs to be activated. If you want to activate the other antiflood option select it first.');

					tpl.find('#np-send-throttler-active').on('change', function(){

						if (!$(this).is(':checked') && !tpl.find('#np-send-antifllod-active').is(':checked'))
						{
							$(this).prop('checked', true);
							box.alertErrors([antifloodValidateMsg]);
							return;
						}

						var btn = $(this),
							value = Number(btn.is(':checked'));

						$.updateConfiguration('SEND_THROTTLER_ACTIVE', value).done(function(response){
							if (response.success)
								storage.set('configuration.SEND_THROTTLER_ACTIVE', value);
							else
								box.alertErrors(response.errors);
						}).always(function(){
							updateSendMethodDisplay();
						});
					});

					tpl.find('#np-send-antifllod-active').on('change', function(){

						if (!$(this).is(':checked') && !tpl.find('#np-send-throttler-active').is(':checked'))
						{
							$(this).prop('checked', true);
							box.alertErrors([antifloodValidateMsg]);
							return;
						}

						var btn = $(this),
							value = Number(btn.is(':checked'));

						$.updateConfiguration('SEND_ANTIFLOOD_ACTIVE', value).done(function(response){
							if (response.success)
								storage.set('configuration.SEND_ANTIFLOOD_ACTIVE', value);
							else
								box.alertErrors(response.errors);
						}).always(function(){
							updateSendMethodDisplay();
						});
					});

					tpl.find('#np-send-antiflood-emails').on('blur', function(){
						var btn = $(this),
							value = btn.val();

						if (String(value).match(regexNumber) === null || (value < 1 || value > 100))
						{
							btn.val(storage.get('configuration.SEND_ANTIFLOOD_EMAILS'));
							return;
						}

						$.updateConfiguration('SEND_ANTIFLOOD_EMAILS', value).done(function(response){
							if (response.success)
								storage.set('configuration.SEND_ANTIFLOOD_EMAILS', value);
							else
								box.alertErrors(response.errors);
						}).always(function(){
							updateSendMethodDisplay();
						});
					});

					tpl.find('#np-send-antiflood-sleep').on('blur', function(){
						var btn = $(this),
							value = btn.val();

						if (String(value).match(regexNumber) === null || (value < 0 || value > 60))
						{
							btn.val(storage.get('configuration.SEND_ANTIFLOOD_SLEEP'));
							return;
						}

						$.updateConfiguration('SEND_ANTIFLOOD_SLEEP', value).done(function(response){
							if (response.success)
								storage.set('configuration.SEND_ANTIFLOOD_SLEEP', value);
							else
								box.alertErrors(response.errors);
						}).always(function(){
							updateSendMethodDisplay();
						});
					});

					dataModelConnection = new gk.data.Model({
						'id': 'id_newsletter_pro_send_connection'
					});

					dataSourceConnection = new gk.data.DataSource({
						pageSize: 7,
						transport: {
							read: 
							{
								url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=ajaxGetConnections',
								dataType: 'json',
							},

							destroy: 
							{
								url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=ajaxDeleteConnection&id',
								type: 'POST',
								dateType: 'json',
								success: function(response, itemData) 
								{
									if(!response.success)
									{
										dataSourceConnection.sync(function(){
											box.dataStorage.set('count_send_connections', this.count());
										});
										box.alertErrors(response.errors);
									}
									else
									{
										var val = getConnections();
										if (val > 2)
											box.dataStorage.set('count_send_connections', --val);
										else
											box.dataStorage.set('count_send_connections', 1);
									}
								},
								error: function(data, itemData) 
								{
									dataSourceConnection.sync(function(){
										box.dataStorage.set('count_send_connections', this.count());
									});
									alert(l('The connection cannot be deleted.'));
								},
								complete: function(data, itemData) {},
							},
						},
						schema: {
							model: dataModelConnection
						},
						trySteps: 2,
						errors: {
							read: function(xhr, ajaxOptions, thrownError) 
							{
								dataSourceConnection.syncStepAvailableAdd(3000, function(){
									dataSourceConnection.sync(function(){
										box.dataStorage.set('count_send_connections', this.count());
									});
								});
							}
						},
						done: function() {
							box.dataStorage.set('count_send_connections', this.count());
						}
					});

					dataGridConnection = tpl.find('#np-send-connection');

					dataGridConnection.gkGrid({
						dataSource: dataSourceConnection,
						checkable: false,
						selectable: false,
						currentPage: 1,
						pageable: true,
						template: {
							actions: function(item, value) 
							{
								var deleteconnection = $('#delete-connection').gkButton({
									name: 'delete',
									title: l('delete'),
									className: 'connection-delete',
									item: item,
									command: 'delete',
									icon: '<i class="icon icon-trash-o"></i> '
								});

								return deleteconnection;
							},
							connection_test: function(item)
							{
								var btn = $('<a href="javascript:{}" class="btn btn-default" style="float: right;">\
												<span class="btn-ajax-loader" style="margin-top: 4px;"></span>\
												<span class="pull-left np-connection-status" style="display: none;"></span>\
												<i class="icon icon-envelope"></i> \
												'+l('Test')+'\
											</a>'),
									connectionStatus = btn.find('.np-connection-status'),
									connectionDefault = function() {
										connectionStatus.hide();
									},
									connectionYep = function() {
										connectionStatus.show().html('<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>');
									},
									connectionNup = function() {
										connectionStatus.show().html('<span class="list-action-enable action-disabled"><i class="icon icon-remove"></i></span>');
									};

								btn.on('click', function(){
									var idSmtp = !isNaN(Number(item.data.id_newsletter_pro_smtp)) ? Number(item.data.id_newsletter_pro_smtp) : 0;

									connectionDefault();
									box.showAjaxLoader(btn);
									$.postAjax({ 'submit': 'sendMailTest', sendMailTest: box.dataStorage.get('configuration.PS_SHOP_EMAIL'), id_smtp: idSmtp }).done(function(response) {
										if (!response.success)
										{
											connectionNup();
											box.alertErrors(response.errors);
										}
										else
											connectionYep();

									}).always(function(){
										box.hideAjaxLoader(btn);
									});
								});

								return btn;
							}
						}
					});

					dataGridConnection.addFooter(function(columns){

						var smtp = box.modules.smtp;

						var smtpSelectTemplate = $('<select id="connection-smtp-select" class="fixed-width-xxl"></select>');

						smtpSelect = smtp.ui.SelectOption({
							name: 'connectionSmptSelect',
							template: smtpSelectTemplate,
							className: 'gk-smtp-select',
							options: self.getSelectOptions(NewsletterPro.dataStorage.get('all_smtp')),
						});

						var add = $('<a href="javascript:{}" class="btn btn-default" style="float: right;">\
										<span class="btn-ajax-loader"></span>\
										<i class="icon icon-plus-square"></i> '+l('Add Connection')+'\
									</a>');

						add.on('click', function(){
							var selected = smtpSelect.getSelected();
							if (selected == null) 
							{
								alert(box.displayAlert(l('You smtp value has been selected.')));
								return;
							}
							
							if (!confirm(l('Are you sure you want to add a new connection? If you don\'t setup the connections properly you risk to be banned.' )))
								return;

							box.showAjaxLoader(add);

							$.postAjax({'submit': 'ajaxAddConnection', id_smtp: selected.data.id_newsletter_pro_smtp}).done(function(response){
								box.hideAjaxLoader(add);
								if (!response.success)
									box.alertErrors(response.errors);
								else
								{
									dataSourceConnection.sync(function(){
										box.dataStorage.set('count_send_connections', this.count());
									});
								}
							}).always(function(){
								box.hideAjaxLoader(add);
							});
						});

						return this.makeRow([smtpSelectTemplate, add]);
					}, 'prepend'); // end of dataGridConnection addFooter

					performancesWindowContent = tpl;
					return performancesWindowContent;
				}
			});

			updateSendMethodDisplay();

			dom.btnAddExclusion.on('click', function(){
				exclusionWindow.show();
			});

			dom.btnViewExclusion.on('click', function(){
				exclusionViewWindow.show();
			});

			dom.btnClearExclusion.on('click', function(){
				if (!confirm(l('Are you sure you want to clear exclusions?' )))
					return;

				box.showAjaxLoader(dom.btnClearExclusion);

				$.postAjax({'submit': 'clearExclusionEmails'}).done(function(response){
					if (response.success)
					{
						box.hideAjaxLoader(dom.btnClearExclusion);
						dom.exclusionEmailsCount.html(0);
						alert(box.displayAlert(response.msg));
					}
					else
						box.alertErrors(response.errors);
				}).always(function(){
					box.hideAjaxLoader(dom.btnClearExclusion);
				});
			});

			dom.btnPerformances.on('click', function(){
				performancesWindow.show();
			});

			function getConnections()
			{
				var connections = Number(box.dataStorage.get('count_send_connections')),
					defaultConnection = 1;

				return (!isNaN(connections) 
							? ( connections == 0 ? defaultConnection : connections )
							: defaultConnection );
			}

			function updateSendMethodDisplay()
			{
				var storage = box.dataStorage,
					define = box.define,
					connections = getConnections(),
					display = '',
					str = '',
					sendAntifloodEmails = storage.getNumber('configuration.SEND_ANTIFLOOD_EMAILS'),
					sendAntifloodSleep = storage.getNumber('configuration.SEND_ANTIFLOOD_SLEEP'),
					sendThrottlerLimit = storage.getNumber('configuration.SEND_THROTTLER_LIMIT');

				if (typeof performancesWindowTpl !== 'undefined' && performancesWindowTpl)
				{
					var npSendAntifloodInfo = performancesWindowTpl.find('#np-send-antiflood-info'),
						npSendThrottlerInfo = performancesWindowTpl.find('#np-send-throttler-info');

					if (storage.getNumber('count_send_connections') > 0)
					{
						var displayAntifloodInfoMsg = l('With #s connections send #s emails, and pause #s seconds.')
								.replace(/\#s/, connections)
								.replace(/\#s/, sendAntifloodEmails * connections)
								.replace(/\#s/, sendAntifloodSleep),
							displayThrottlerInfo = l('With #s connections limit #s emails per minute.')
								.replace(/\#s/, connections)
								.replace(/\#s/, sendThrottlerLimit * connections);

						npSendAntifloodInfo.show().find('span').html(displayAntifloodInfoMsg);
						npSendThrottlerInfo.show().find('span').html(displayThrottlerInfo);
					}
					else
					{
						npSendAntifloodInfo.hide();
						npSendThrottlerInfo.hide();
					}
				}

				if (storage.getNumber('configuration.SEND_METHOD') == define.SEND_METHOD_DEFAULT)
				{
					str = l('Send one newsletter at #s seconds - (for all connections).').replace(/\#s/, storage.getNumber('configuration.SLEEP'));
				}
				else
				{
					var antifloodMsg = l('(Antiflood) Send #s emails, and pause #s seconds.')
											.replace(/\#s/, sendAntifloodEmails * connections)
											.replace(/\#s/, sendAntifloodSleep),
						throttlerMsg = (storage.getNumber('configuration.SEND_THROTTLER_TYPE') == define.SEND_THROTTLER_TYPE_EMAILS 
											? l('(Speed limits) Limit  #s emails per minute.') 
											: l('(Speed limits) Limit  #s MB per minute.'))
												.replace(/\#s/, sendThrottlerLimit * connections);

					if (storage.getNumber('configuration.SEND_ANTIFLOOD_ACTIVE') && storage.getNumber('configuration.SEND_THROTTLER_ACTIVE'))
						str = antifloodMsg + ' / ' + throttlerMsg;
					else if (storage.getNumber('configuration.SEND_ANTIFLOOD_ACTIVE'))
						str = antifloodMsg;
					else if (storage.getNumber('configuration.SEND_THROTTLER_ACTIVE'))
						str = throttlerMsg;
					else
						str = l('No send method was selected.');
				}

				display = '<label class="control-label">' + str + '</label>\
							<span style="display: block; font-style: italic;">('+(l('#s connections').replace(/\#s/, connections))+')</span>';

				dom.sendMethodDisplay.html(display);
				return str;
			}


			filterSelection = new box.components.FilterSelection({
				customers: self.vars.filterCustomers,
				visitors: self.vars.filterVisitor,
				visitors_np: self.vars.filterVisitorNP,
				added: self.vars.filterAdded,

				customers_apply_callback: self.vars.applyFilerCustomersCallback,
				visitor_apply_callback: self.vars.applyFilerVisitorCallback,
				visitor_np_apply_callback: self.vars.applyFilerVisitorNpCallback,
				added_apply_callback: self.vars.applyFilerAddedCallback,
			});

			dom.addFilterSelection.on('click', function(){
				var filters = filterSelection.getFilters(),
					name = dom.nameFilterSelection.val();

				$.postAjax({'submit': 'addFilterSelection', name: name, filters: filters}).done(function(response){
					if (!response.success)
						box.alertErrors(response.errors);
					else
					{
						dom.deleteFilterSelection.show();
						dom.nameFilterSelection.val('');

						dom.filterSelection.empty();
						dom.filterSelection.append('<option value="0">- '+l('none')+' -</option>');

						for(var i = 0; i < response.filters.length; i++)
						{
							var filter = response.filters[i],
								option = '<option value="' + filter.id_newsletter_pro_filters_selection + '" ' + ($.trim(name) === $.trim(filter.name) ? 'selected="selected"' : '') + '>'+filter.name+'</option>';

							dom.filterSelection.append(option);

						}
					}
				});
			});

			dom.addFilterSelection.on('change', function(){
				var id = Number(dom.filterSelection.val());
				if (id > 0)
					dom.deleteFilterSelection.show();
				else
					dom.deleteFilterSelection.hide();

			});

			dom.deleteFilterSelection.on('click', function(){

				var id = Number(dom.filterSelection.val());

				if (id == 0)
					return false;

				$.postAjax({'submit': 'deleteFilterSelection', id: id}).done(function(response){
					if (!response.success)
						box.alertErrors(response.errors);
					else
					{
						$(dom.filterSelection.selector + ' ' + 'option[value="' + id + '"]').remove();
						filterSelection.clearfilters();
						
						if (dom.filterSelection.children().length <= 1)
							dom.deleteFilterSelection.hide();
					}
				});
			});

			function uncheckAll(list, button)
			{
				if (typeof list !== 'undefined')
				{
					// uncheck the selections
					list.uncheckAll();
					button.data('checked', false);
					button.changeTitle(l('check all'));
				}
			}

			dom.filterSelection.on('change', function(){

				var id = Number(dom.filterSelection.val());
				if (id == 0)
				{
					dom.deleteFilterSelection.hide();
					filterSelection.clearfilters();
					return false;
				}
				else
					dom.deleteFilterSelection.show();

				uncheckAll(self.vars.customers, self.vars.customersCheckAll);
				uncheckAll(self.vars.visitors, self.vars.visitorsCheckAll);
				uncheckAll(self.vars.visitorsNP, self.vars.visitorsNPCheckAll);
				uncheckAll(self.vars.added, self.vars.addedCheckAll);

				$.postAjax({'submit': 'getFilterSelectionById', id: id}).done(function(response){
					if (!response.success)
						box.alertErrors(response.errors);
					else
					{
						if (response.hasOwnProperty('value'))
						{
							filterSelection.clearfilters();
							filterSelection.applyFilters(response.value);
						}
						else
							box.alertErrors([l('An error occured.')]);
					}
				});
			});
		});

		return self;
	},

	addEvent: function(name, value) {
		this.events[name] = value;
	},

	triggerEvent: function(name) {
		if (this.events.hasOwnProperty(name) && typeof this.events[name] == 'function')
			this.events[name]();
	},

	addVar: function(name, value) {
		this.vars = this.vars || {};
		this.vars[name] = value;
	},

	ready: function(func) 
	{
		var self = this;
		$(document).ready(function(){

			self.domSave = self.domSave || {
				template : $($.trim($('#add-new-email-template').html()))
			};

			if (typeof self.domSave.template[1] !== 'undefined')
				self.domSave.template = $(self.domSave.template[1]);

			var template = self.domSave.template;
			var listOfInterestTemplate =  $($('#list-of-interest-template').html());

			self.dom = self.dom || {
				customersGrid: $('#customers-list'),
				visitorsGrid: $('#visitors-list'),
				visitorsNPGrid: $('#visitors-np-list'),
				addedGrid: $('#added-list'),
				customersCount: $('#customers-count'),
				visitorsCount: $('#visitors-count'),
				visitorsNPCount: $('#visitors-np-count'),
				addedCount: $('#added-count'),
				addNewEmailTemplate: template,
				addNewEmail: template.find('#add-new-email-action'),
				addNewEmailForm: template.find('#add-new-email-from'),
				addNewEmailError: template.find('#add-new-email-error'),

				btnAddExclusion: $('#btn-add-exclusion'),
				btnClearExclusion: $('#btn-clear-exclusion'),
				btnViewExclusion: $('#btn-view-exclusion'),
				exclusionEmailsCount: $('#exclusion-emails-count'),
				usersAjaxLoader: $('#users-ajax-loader'),
				visitorsAjaxLoader: $('#visitors-ajax-loader'),
				visitorsNPAjaxLoader: $('#visitors-np-ajax-loader'),
				addedAjaxLoader: $('#added-ajax-loader'),
				btnBouncedEmails: $('#btn-bounced-emails'),

				btnPerformances: $('#np-btn-performances'),
				sendMethodDisplay: $('#np-send-method-display'),

				addFilterSelection: $('#np-add-filter-selection'),
				deleteFilterSelection: $('#np-delete-filter-selection'),
				nameFilterSelection: $('#np-name-filter-selection'),
				filterSelection: $('#np-filter-selection'),
			};

			func(self.dom);
		});
	},

	createCheckToggle: function(name, dataSource) 
	{
		var l = NewsletterPro.translations.l(NewsletterPro.translations.modules.sendNewsletters);
		var button = $('#'+name)
			.gkButton({
				name: name,
				title: l('check all'),
				className: name,
				css: {
					'padding-left': '10px',
					'padding-right': '10px',
					'margin-left': '0',
					'display': 'inline-block',
				},
				attr: {
					'data-checked': 0,
				},

				click: function(event) {

					function isChecked() {
						return button.data('checked') ? true : false;
					};

					function toggleName(trueStr, falseStr) {
						if (isChecked()) {
							button.data('checked', false);
							button.changeTitle(falseStr);
							return false;
						} else {
							button.data('checked', true);
							button.changeTitle(trueStr);
							return true;
						}
					}

					if (toggleName(l('uncheck all'), l('check all'))) {
						dataSource.checkAll();
					} else {
						dataSource.uncheckAll();
					}
				},
			});
		return button;
	},

	getGenderImageById: function(idGender)
	{
		var img = '';
		switch(parseInt(idGender))
		{
			case 1:
				img = '<img src="../modules/newsletterpro/views/img/gender_1.png" style="margin-left: 7px;">';
			break;

			case 2:
				img = '<img src="../modules/newsletterpro/views/img/gender_2.png" style="margin-left: 7px;">';
			break;
		}

		return img;
	},

	isNewsletterProSubscriptionActive: function()
	{
		return NewsletterPro.dataStorage.getNumber('configuration.SUBSCRIPTION_ACTIVE');
	},

	getAjaxLoader: function()
	{
		var loader = $('<span class="ajax-loader" style="display: block;"></span>');
		return loader;
	},

	getRangeSelectionContent: function(func)
	{
		$.postAjax({'submit': 'getRangeSelectionContent'}, 'html').done(function(content){
			func($(content));
		});
	},

	resetButtons: function() 
	{

	},

	isPS16: function ()
	{
		return this.box.dataStorage.data.isPS16;
	},

	resetCustomersButton: function() 
	{

	},

	resetVisitorsButton: function() 
	{

	},

	resetVisitorsNPButton: function() 
	{

	},

	resetAddedButton: function() 
	{

	},

	buildExportToCSVData: function(dataSource, listRef)
	{
		var l = NewsletterPro.translations.l(NewsletterPro.translations.modules.sendNewsletters);

		var defaultSeparator = ';',
			exportForm = $('\
				<form id="' + NewsletterPro.uniqueId() + '" method="POST" action="' + NewsletterPro.dataStorage.get('ajax_url') + '#sendNewsletters">\
					<input type="hidden" name="export_all_columns" value="1">\
					<input type="hidden" name="export_email_addresses" value="1">\
					<input type="hidden" name="csv_separator" value="' + defaultSeparator + '">\
					<input type="hidden" name="list_ref" value="' + listRef + '">\
					<div id="np-export-csv-range-box">\
					</div>\
				</form>\
			'),
			separatorInput = exportForm.find('input[name="csv_separator"]'),
			exportRangeBox = exportForm.find('#np-export-csv-range-box');

		var btnExportCsv = $('\
			<a id="' + NewsletterPro.uniqueId()  + '" href="javascript:{}" class="btn btn-default pull-right">\
				<i class="icon icon-download"></i>\
				'+l('Export Selection')+'\
			</a>');

		btnExportCsv.on('click', function(){

			var selected = dataSource.getSelectedIds(true);
				separator = prompt(l('CSV Separator'), defaultSeparator);

			if (!selected.length) {
				alert(l('You must select at least an email address.'));
				return;
			}
			
			if (separator == null) {
				return;
			}

			separator = $.trim(separator);

			if (separator == ';' || separator == ',') {

				exportRangeBox.empty();

				for (var i = 0, length = selected.length; i < length; i++) {
					exportRangeBox.append('<input type="hidden" name="export_range[]" value="' + selected[i] + '">');
				}

				separatorInput.val(separator);
				exportForm.submit();
			} else {
				alert(l('Invalid CSV separator.'));
				return;
			}

		});

		return btnExportCsv;
	},

	initCustomersGrid: function() {
		var self = this;

		self.ready(function(dom) {
			var l = NewsletterPro.translations.l(NewsletterPro.translations.modules.sendNewsletters),
				customersDataModel,
				customersDataSource,
				customersGrid = dom.customersGrid,
				maxTotalSpent = 0,
				filterByCountryDataModel,
				filterByCountryDataSource,
				filterByCountryDataGrid,
				filterByCountrySearch;

			customersDataModel = new gk.data.Model({
				id: 'id_customer',
			});

			customersDataSource = new gk.data.DataSource({
				pageSize: 10,
				trySteps: 2,
				transport: 
				{
					read: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=getCustomers',
						dataType: 'json',
					},
					update: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=updateCustomer&id',
						dataType: 'json',
					},

					destroy: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=deleteCustomer&id',
						type: 'POST',
						dateType: 'json',
						success: function(response, itemData) {
							if(!response)
								alert(l('delete customer'));
						},
						error: function(data, itemData) {
							alert(l('delete customer'));
						},
						complete: function(data, itemData) {},
					},

					search: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=searchCustomer&value',
						dataType: 'json',
					},

					filter: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=filterCustomer',
						dataType: 'json',
					},

				},
				schema: {
					model: customersDataModel
				},
				errors: 
				{
					read: function(xhr, ajaxOptions, thrownError) 
					{
						customersDataSource.syncStepAvailableAdd(3000, function(){
							customersDataSource.sync();
						});
					},
				}
			});

			customersGrid.gkGrid({
				dataSource: customersDataSource,
				selectable: false,
				checkable: true,
				currentPage: 1,
				pageable: true,
				start: function()
				{
					dom.usersAjaxLoader.show();
				},
				done: function(dataSource) 
				{
					dom.customersCount.html(dataSource.items.length);
					dom.usersAjaxLoader.hide();
				},
				template: {
					img_path: function(item, value) {

						var div = $('<div></div>');
						var lang_img = '<img src="'+value+'">';
						var gender_img = self.getGenderImageById(item.data.id_gender);

						div.append(lang_img);
						div.append(gender_img);
						div.width('38');
						return div;
					},

					newsletter: function(item, value) 
					{
						var a = $('<a href="javascript:{}"></a>'),
							data = item.data;

						function isSubscribed() {
							return parseInt(item.data.newsletter) ? true : false;
						}

						function viewOnlySubscribed() {
							return NewsletterPro.dataStorage.getNumber('configuration.VIEW_ACTIVE_ONLY') ? true : false;
						}

						function switchSubscription() 
						{
							if (isSubscribed()) {
								a.html('<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>');
							} else {
								a.html('<span class="list-action-enable action-disabled"><i class="icon icon-remove"></i></span>');
							}
						}

						switchSubscription();

						a.on('click', function(e){
							e.stopPropagation();
							data.newsletter = (isSubscribed() ? 0 : 1);

							item.update().done(function(response) {
								if (!response) {
									alert('error on subscribe or unsubscribe');
								} else {
									if (viewOnlySubscribed()) {
										item.removeFromScreen();
									} else {
										switchSubscription();
									}
								}
							});
						});

						return a;
					},

					chackbox: function(item, value) 
					{
						var checkBox = $('<input type="checkbox" value="'+value+'" '+(item.isChecked() ? 'checked="checked"' : '')+'> ');
						return checkBox;
					},

					actions: function(item) {
						var deleteCustomer = $('#delete-customer')
							.gkButton({
								name: 'delete',
								title: l('delete'),
								className: 'customer-delete',
								item: item,
								command: 'delete',
								confirm: function() {
									return confirm(l('delete customer confirm'));
								},
								icon: '<i class="icon icon-trash-o"></i> ',
							});

						return deleteCustomer;
					},
				}
			});

			var checkToggle,
				checkToggleSearch,
				searchLoading,
				search;

			var footerActions = customersGrid.addFooter(function(columns){
				var tr, td;
				function makeRow(arr) 
				{
					tr = $('<tr></tr>');
					td = $('<td class="form-inline gk-footer" colspan="'+columns+'"></td>');

					$.each(arr, function(i, item){
						td.append(item);
					});

					tr.html(td);
					return tr;
				}

				function createCheckToggle(name) 
				{
					var button = $('#'+name)
						.gkButton({
							name: name,
							title: l('check all'),
							className: name,
							css: {
								'padding-left': '10px',
								'padding-right': '10px',
								'margin-left': '0',
								'display': 'inline-block',
							},
							attr: {
								'data-checked': 0,
							},

							click: function(event) {

								function isChecked() {
									return button.data('checked') ? true : false;
								};

								function toggleName(trueStr, falseStr) {
									if (isChecked()) {
										button.data('checked', false);
										button.changeTitle(falseStr);
										return false;
									} else {
										button.data('checked', true);
										button.changeTitle(trueStr);
										return true;
									}
								}

								if (toggleName(l('uncheck all'), l('check all'))) {
									customersDataSource.checkAll();
								} else {
									customersDataSource.uncheckAll();
								}
							},
						});
					return button;
				}

				checkToggle = createCheckToggle('check-toggle');
				checkToggleSearch = createCheckToggle('check-toggle-search');
				checkToggleSearch.addClass('gk-onfilter');
				checkToggleSearch.hide();

				self.addVar('customersCheckAll', checkToggle);

				btnExportCsv = self.buildExportToCSVData(customersDataSource, NewsletterPro.dataStorage.get('csv_export_list_ref.LIST_CUSTOMERS'));

				return makeRow([checkToggle, checkToggleSearch, btnExportCsv]);
			}, 'prepend');

			function clearCategoriesFilters()
			{
				var categoriesTree = NewsletterPro.modules.categoriesTree;
				categoriesTree.setNeedUncheckAll(true);
				categoriesTree.uncheckAllCategories(false);
			}

			function clearByPurchaseFilters()
			{

				searchByPurchase.clearVal();
				productList.removeItems();

				pfbGrid.dataSource.parse(function(item){
					item.removeFromScreen();
				});
				pfbGrid.dataSource.setData([]);
				pfbGrid.dataSource.sync();
			}

			function setFilterBirthdayFrom(val)
			{
				birthdayDate.from = val;
			}

			function setFilterBirthdayTo(val)
			{
				birthdayDate.to = val;
			}

			function clearByBirthdayFilter()
			{
				if (typeof birthdayFrom !== 'undefined')
				{
					birthdayFrom.val('');
					setFilterBirthdayFrom('');
				}

				if (typeof birthdayTo !== 'undefined')
				{
					birthdayTo.val('');
					setFilterBirthdayTo('');
				}
			}

			function clearRangeSelection()
			{
				resetSliderRange('clear', 1, customersCount());
			}

			var birthdayDate = {
				'from': '',
				'to': '',
			};

			function customersCount()
			{
				return customersDataSource.items.length;
			}

			function resetSliderRange(trigger, min, max)
			{
				if (trigger !== 'range' && typeof sliderRangeCustomer !== 'undefined')
				{
					var reset = {
						min : min,
						max : max,
						valueMin : min,
						valueMax : max,
						values : [min, max],
					};
					if (max <= 0)
					{
						reset['snap'] = 0;
						reset['min'] = 0;
						reset['max'] = 1;
					}
					sliderRangeCustomer.reset(reset);
					sliderRangeCustomer.resetPositionMin();
					sliderRangeCustomer.resetPositionMax();
				}
			}

			function resetTotalSpentFilter(min, max)
			{
				if (typeof sliderTotalSpent !== 'undefined')
				{
					var reset = {
						min: min,
						max: max,
						valueMin: min,
						valueMax: max,
						values: [min, max],
					};

					sliderTotalSpent.reset(reset);
					sliderTotalSpent.resetPositionMin();
					sliderTotalSpent.resetPositionMax();
				}
			}

			function clearTotalSpentFilter()
			{
				resetTotalSpentFilter(0, maxTotalSpent);
			}

			function clearFilterByCountries()
			{
				if (typeof filterByCountryDataSource !== 'undefined')
				{
					filterByCountrySearch.val('');
					filterByCountryDataSource.clearSearch();
					filterByCountryDataSource.uncheckAll();
				}
			}

			var sliderRangeCustomer;
			var sliderTotalSpent;
			var birthdayFrom;
			var birthdayTo;
			var fbbClear;

			var searchByPurchase;
			var productList;
			var pfbGrid;

			var headerActions1 = customersGrid.addHeader(function(columns){
				var tr, 
					td;
				function makeRow(arr) 
				{
					tr = $('<tr></tr>');
					td = $('<th class="gk-header-datagrid customers-header" colspan="'+columns+'"></th>');

					$.each(arr, function(i, item){
						td.append(item);
					});

					tr.html(td);

					return tr;
				}

				var filterGroups = $('#gk-filter-groups').gkDropDownMenu({
					title: l('groups'),
					name: 'gk-filter-groups',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},
					data: NewsletterPro.dataStorage.data.filter_groups,
					change: function(values) {
						appyFilters('groups');
					},
				});

				var dataLangs = NewsletterPro.dataStorage.data.filter_languages;
				$.each(dataLangs, function(i, lang){
					dataLangs[i].title = '<img src="' + lang.img_path + '" width="16" height="11">' + '<span style="margin-left: 10px;">'+lang.title+'</span>';
				});

				var filterLanguages = $('#gk-filter-languages').gkDropDownMenu({
					title: l('languages'),
					name: 'gk-filter-languages',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},
					data: dataLangs,
					change: function(values) {
						appyFilters('languages');
					},
				});

				var filterShops = $('#gk-filter-shops').gkDropDownMenu({
					title: l('shops'),
					name: 'gk-filter-shops',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},
					data: NewsletterPro.dataStorage.data.filter_shops,
					change: function(values) {
						appyFilters('shops');
					},
				});

				var filterGender = $('#gk-filter-gender').gkDropDownMenu({
					title: l('gender'),
					name: 'gk-filter-gender',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},

					data: NewsletterPro.dataStorage.get('filter_genders'),

					change: function(values) {
						appyFilters('gender');
					},
				});

				var filterSubscribed = $('#gk-filter-subscribed').gkDropDownMenu({
					title: l('subscribed'),
					name: 'gk-filter-gender',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},

					data: [
						{'title': l('yes'), 'value': 1},
						{'title': l('no'), 'value': 0},
					],

					change: function(values) {
						appyFilters('subscribed');
					},
				});
				
				var filterActive = $('#gk-filter-active').gkDropDownMenu({
					title: l('active'),
					name: 'gk-filter-active',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},

					data: [
						{'title': l('yes'), 'value': 1},
						{'title': l('no'), 'value': 0},
					],

					change: function(values) {
						appyFilters('active');
					},
				});
				var filterPostcode = $('#gk-filter-postcode').gkDropDownMenu({
					title: l('postcode'),
					name: 'gk-filter-postcode',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},

					data: [
						{'title': l('0'), 'value': 0},
						{'title': l('1'), 'value': 1},
						{'title': l('2'), 'value': 2},
						{'title': l('3'), 'value': 3},
						{'title': l('4'), 'value': 4},
						{'title': l('5'), 'value': 5},
						{'title': l('6'), 'value': 6},
						{'title': l('7'), 'value': 7},
						{'title': l('8'), 'value': 8},
						{'title': l('9'), 'value': 9},
					],

					change: function(values) {
						appyFilters('postcode');
					},
				});


				if (NewsletterPro.dataStorage.get('view_active_only'))
					filterSubscribed.hide();

				function getCategories(content) {
					var categories = content.find('input[type="checkbox"]');
					if (categories.length > 0)
						return categories;
					return [];
				}

				function getSelected(categoryTree) {
					var values = [];
					if (typeof categoryTree !== 'array' && typeof categoryTree !== 'undefined') {
						values = $.map(categoryTree, function(item){
							var item = $(item);
							var val = item.val();
							if ( val !== 'undefined' && item.is(':checked')) {
								return parseInt(val);
							}
						});
					}
					return values;
				}

				var categoryTree;
				var winCategoryTree = new gkWindow({
						width: 640,
						height: 540,
						title: l('category filter title'),
						className: 'category-filters',
						show: function(win) {},
						close: function(win) {},
						content: function(win) {
							$.postAjax({'submit': 'getCategoryTree'}, 'html').done(function(response){
								win.setContent(response);
							});
							return '';
						}
					});

				var filterCategories = $('#filter-categories')
					.gkButton({
						name: 'filter-categories',
						title: l('categories'),
						className: 'filter-categories',
						css: {
							'padding-left': '10px',
							'padding-right': '10px',
							'float': 'left',
							'margin-right': '10px',
							'position': 'relative',
						},
						attr: {
							'data-checked': 0,
						},
						click: function(event) {
							winCategoryTree.show();
						},
					});

				self.addEvent('clickOnCategoryBox', function(){
					categoryTree = getCategories(winCategoryTree.template);
					appyFilters('categories');
				});

				function getFilterByPurchaseContent(func)
				{
					$.postAjax({'submit': 'getFilterByPurchaseContent'}, 'html').done(function(content){
						func($(content));
					});
				}

				function getFiltersByPurchase()
				{
					var ids = [];
					pfbGrid.dataSource.parse(function(item)
					{
						ids.push(item.data.id);
					});

					return ids;
				}

				function buildFBPGrid(fpbDataGrid, data)
				{
					data = data || [];

					var fbpDataModel = new gk.data.Model({
						id: 'id_product',
					});

					var fbpDataSource = new gk.data.DataSource({
						pageSize: 7,
						transport: {
							data: data,
						},
						schema: {
							model: fbpDataModel
						},
					});

					fpbDataGrid.gkGrid({
						dataSource: fbpDataSource,
						selectable: false,
						currentPage: 1,
						pageable: true,
						template: {
							actions: function(item) 
							{
								var a = $('<a href="javascript:{}" class="btn btn-default"><i class="icon icon-times" style="margin-right: 7px;"></i> '+l('remove')+'</a>');
								a.on('click', function(){

									var id     = item.data.id_product;
									var search = pfbGrid.dataSource.getItemByValue('data.id_product', id);
									var gridData   = pfbGrid.dataSource.getData();
									if (search)
									{
										search.removeFromScreen()
										index = getProductIndexById(gridData, id);
										if (index > -1)
											gridData.splice(index, 1);
									}

									var pli = productList.getItems(false),
										len = pli.length;

									if (len > 0)
									{
										var current;
										for(var i = 0; i < len; i++)
										{
											current = pli[i];
											if (current.data.id == id)
											{
												current.remove();
												break;
											}
										}
									}

								});
								return a;
							},
							price_display: function(item, value)
							{
								return '<span style="font-weight: bold;">'+value+'</span>';
							},
							reference: function(item, value)
							{
								return '<span style="font-weight: bold;">'+value+'</span>';
							},
							image: function(item)
							{
								return '<img src="'+item.data.thumb_path+'" style="width: 40px; height: 40px;">';
							},
						}
					});

					return {
						'dataSource': fbpDataSource,
						'dataModel': fbpDataModel,
						'dataGrid': fpbDataGrid,
					};
				}

				function getProductIndexById(gridData, id)
				{
					for(var i = 0; i < gridData.length; i++)
					{
						var itm = gridData[i];

						if (itm.id_product == id)
							return i;
					}
					return -1;
				}

				var filterByInterest = $('#gk-customer-filter-by-interest-np').gkDropDownMenu({
					title: l('by list of interest'),
					name: 'gk-filter-loi',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},

					data: NewsletterPro.dataStorage.get('filter_list_of_interest'),
					activeClass: {
						enable: '',
						disable: 'btn-filter-list-of-interst-inactive',
					},
					change: function(values) 
					{
						appyFilters();
					},
				});

				var winByPurchase = new gkWindow({
						width: 640,
						height: 540,
						title: l('filter by purchase'),
						className: 'filter-by-purchase-window',
						show: function(win) {},
						close: function(win) {},
						content: function(win, parent) 
						{
							getFilterByPurchaseContent(function(content)
							{
								win.setContent(content);

								searchByPurchase = new gkSearch({
									read: {'submit': 'searchByPurchase'},
									element: content.find('#filter-poduct-search'),
									ajaxLoader: content.find('.product-search-span'),
									result: function(response)
									{
										var products = response.products;
										productList.createItems(products);
									},

									reset: function()
									{
										productList.removeItems();
									},
								});

								productList = new gkProductList({
									element: content.find('#filter-product-list'),
									inList: function(data)
									{
										var id     = data.id_product;
										var gridData   = pfbGrid.dataSource.getData();
										index = getProductIndexById(gridData, id);

										if (index > -1)
											return false;
										return true;
									},
									add: function(data, item)
									{
										var gridData   = pfbGrid.dataSource.getData();
										var id         = data.id_product;

										if (!pfbGrid.dataSource.getItemByValue('data.id_product', id))
											gridData.push(data);

										pfbGrid.dataSource.setData(gridData);
										pfbGrid.dataSource.sync().done(function()
										{
											appyFilters('purchase');
										});

									},

									remove: function(data, item)
									{
										var id     = data.id_product;
										var search = pfbGrid.dataSource.getItemByValue('data.id_product', id);
										var gridData   = pfbGrid.dataSource.getData();

										if (search)
										{
											search.removeFromScreen()

											index = getProductIndexById(gridData, id);
											if (index > -1)
												gridData.splice(index, 1);
										}

										appyFilters('purchase');
									},
								});

								pfbGrid = buildFBPGrid(content.find('#fbp-grid'));

							});

							return '<span class="ajax-loader" style="margin-left: 312px; margin-top: 224px;"></span>';
						}
					});

				var filterByPurchase = $('#by-purchase-filters')
					.gkButton({
						name: 'by-purchase-filters',
						title: l('by purchase'),
						className: 'by-purchase-filters',
						css: {
							'padding-left': '10px',
							'padding-right': '10px',
							'float': 'left',
							'margin-right': '10px',
						},
						attr: {
							'data-checked': 0,
						},
						click: function(event) {
							winByPurchase.show();
						},
					});

				function getFilterByBirthdayContent(func)
				{
					$.postAjax({'submit': 'getFilterByBirthdayContent', fbb_class: 'customers'}, 'html').done(function(content){
						func($(content));
					});
				}

				function getFilterByBirthday()
				{
					return birthdayDate;
				}

				function getMySqlDate(dateObj)
				{
					var year = dateObj.selectedYear,
						mounth = (String(dateObj.selectedMonth).length == 1 ? '0'+String(dateObj.selectedMonth+1) : String(dateObj.selectedMonth+1)),
						day = (String(dateObj.selectedDay).length == 1 ? '0'+String(dateObj.selectedDay) : String(dateObj.selectedDay));
					return mysql_date = year+'-'+mounth+'-'+day;
				}

				var winByBirthday = new gkWindow({
						width: 640,
						height: 320,
						title: l('filter by birthday'),
						className: 'filter-by-birthday-window',
						show: function(win) {},
						close: function(win) {},
						content: function(win, parent) 
						{
							getFilterByBirthdayContent(function(content)
							{
								win.setContent(content);

								birthdayFrom = content.find('#fbb-from-customers');
								birthdayTo = content.find('#fbb-to-customers');
								fbbClear = content.find('#fbb-clear-customers');								

								birthdayFrom.datepicker({ 
									dateFormat: self.box.dataStorage.get('jquery_date_birthday'),
									onSelect: function(date, dateObj)
									{
										setFilterBirthdayFrom(getMySqlDate(dateObj));
										appyFilters('birthday');
									},
									beforeShow: function(input, inst)
									{
										if (!inst.dpDiv.hasClass('date-birthday'))
											inst.dpDiv.addClass('date-birthday');
									}
								});

								birthdayTo.datepicker({
									dateFormat: self.box.dataStorage.get('jquery_date_birthday'),
									onSelect: function(date, dateObj)  
									{
										setFilterBirthdayTo(getMySqlDate(dateObj));
										appyFilters('birthday');
									},
									beforeShow: function(input, inst)
									{
										if (!inst.dpDiv.hasClass('date-birthday'))
											inst.dpDiv.addClass('date-birthday');
									}
								});

								birthdayFrom.on('change', function()
								{
									if ($.trim($(this).val()) == '')
									{
										setFilterBirthdayFrom('');
										appyFilters('birthday');
									}
								});

								birthdayTo.on('change', function()
								{
									if ($.trim($(this).val()) == '')
									{
										setFilterBirthdayTo('');
										appyFilters('birthday');
									}
								});

								fbbClear.on('click', function()
								{
									clearByBirthdayFilter();
									appyFilters('birthday');
								});

							});
							return '<span class="ajax-loader" style="margin-left: 310px; margin-top: 119px;"></span>';
						}
					});

				var filterByBirthday = $('#by-birthday-filters')
					.gkButton({
						name: 'by-birthday-filters',
						title: l('by birthday'),
						className: 'by-birthday-filters',
						css: {
							'padding-left': '10px',
							'padding-right': '10px',
							'margin-right': '10px',
							'float': 'left',
						},
						attr: {
							'data-checked': 0,
						},
						click: function(event) {
							winByBirthday.show();
						},
					});

				function getRangeSelection()
				{
					return {
						'min': (typeof sliderRangeCustomer !== 'undefined' && !isNaN(sliderRangeCustomer.getValueMin()) ? sliderRangeCustomer.getValueMin() : 0) ,
						'max': (typeof sliderRangeCustomer !== 'undefined' && !isNaN(sliderRangeCustomer.getValueMax()) ? sliderRangeCustomer.getValueMax() : 0) ,
					};
				}

				var winRangeSelection = new gkWindow({
						width: 640,
						height: 150,
						title: l('range selection'),
						className: 'range-selection-window',
						show: function(win) {
							if (typeof sliderRangeCustomer !== 'undefined')
								sliderRangeCustomer.refresh();
						},
						close: function(win) {},
						content: function(win, parent) 
						{
							customersDataSource.ready().done(function(){
								self.getRangeSelectionContent(function(content)
								{
									win.setContent(content);

									sliderRangeCustomer = gkSliderRange({
										target: content.find('#slider-range-selection'),
										min : 1,
										max : customersCount(),
										valueMin : 1,
										valueMax : customersCount(),
										editable: true,
										values : [1, customersCount()],

										move: function(obj) {},
										done: function(obj) 
										{
											appyFilters('range');
										},
									});
								});
							});

							return '<span class="ajax-loader" style="margin-left: 310px; margin-top: 36px;"></span>';
						}
					});
				
				var rangeSelection = $('#range-selection')
				.gkButton({
					name: 'range-selection',
					title: l('range selection'),
					className: 'range-selection',
					css: {
						'padding-left': '10px',
						'padding-right': '10px',
						'margin-right': '10px',
						'float': 'left',
					},
					attr: {
						'data-checked': 0,
					},
					click: function(event) 
					{
						winRangeSelection.show();
					},
				});

				var winFilterTotalSpentTemplate;
				var winFilterTotalSpent = new gkWindow({
					width: 640,
					height: 150,
					title: l('Total spent filter'),
					className: 'win-filter-total-spent',
					show: function(win) 
					{
						if (typeof sliderTotalSpent === 'undefined')
						{
							$.postAjax({'submit': 'getMaxTotalSpent'}).done(function(ts){
								maxTotalSpent = Number(ts);

								sliderTotalSpent = gkSliderRange({
									target: winFilterTotalSpentTemplate,
									min : 0,
									max : maxTotalSpent,
									prefix: ' ' + NewsletterPro.dataStorage.get('currency_default.sign'),
									valueMin : 0,
									valueMax : maxTotalSpent,

									values : [0, maxTotalSpent],

									move: function(obj) {},
									done: function(obj) 
									{
										appyFilters('total_spent');
									},
								});
							});
						}
						else
							sliderTotalSpent.refresh();
					},
					close: function(win) {},
					content: function(win, parent) 
					{
						winFilterTotalSpentTemplate = $('\
							<div id="slider-filter-total-spent" class="slider-filter-total-spent">\
								<div class="slider-container">\
									<label>'+l('Total spent')+'</label>\
									<div id="slider-range-selection"></div>\
								</div>\
							</div>\
						');

						return winFilterTotalSpentTemplate;
					}
				});

				var filterTotalSpent = $('#filter-total-spent')
				.gkButton({
					name: '',
					icon: '<i class="icon icon-usd"></i>',
					title: '',
					className: 'filter-total-spent',
					css: {
						'padding-left': '10px',
						'padding-right': '10px',
						'margin-right': '10px',
						'float': 'left',
					},
					attr: {
						'data-checked': 0,
					},
					click: function(event) 
					{
						winFilterTotalSpent.show();
					},
				});

				var winFilterByCountryTemplate;
				var winFilterByCountry = new gkWindow({
					width: 640,
					height: 475,
					setScrollContent: 415,
					title: l('Filter customers by country'),
					className: 'win-filter-total-spent',
					show: function(win)
					{
						if (typeof filterByCountryDataSource === 'undefined')
						{
							filterByCountryDataModel = new gk.data.Model({
								id: 'id_country',
							});

							filterByCountryDataSource = new gk.data.DataSource({
								pageSize: 10,
								trySteps: 2,
								transport: 
								{
									read: {
										url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=getCountries',
										dataType: 'json',
									},

									search: {
										url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=searchCountries&value',
										dataType: 'json',
									},
								},
								schema: {
									model: filterByCountryDataModel
								},
								errors: 
								{
									read: function(xhr, ajaxOptions, thrownError) 
									{
										filterByCountryDataSource.syncStepAvailableAdd(3000, function(){
											filterByCountryDataSource.sync();
										});
									},
								},
							});

							filterByCountryDataGrid = winFilterByCountryTemplate.find('#filter-by-country-list');

							filterByCountryDataGrid.gkGrid({
								dataSource: filterByCountryDataSource,
								selectable: false,
								checkable: true,
								currentPage: 1,
								pageable: true,
								start: function()
								{
									// dom.usersAjaxLoader.show();
								},
								done: function(dataSource) 
								{
									// dom.customersCount.html(dataSource.items.length);
									// dom.usersAjaxLoader.hide();
								},
								template: {
									active: function(item, value)
									{
										var active = Number(value);

										if (active)
											return '<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>';
										else
											return '<span class="list-action-enable action-disabled"><i class="icon icon-remove"></i></span>';
									},
									chackbox: function(item, value) 
									{
										var checkBox = $('<input type="checkbox" value="'+value+'" '+(item.isChecked() ? 'checked="checked"' : '')+'> ');
										return checkBox;
									},
								},
							});
					
							filterByCountryDataGrid.addHeader(function(columns){
								var timer = null,
									searchBox = $('\
										<div class="clearfix">\
											<span>'+l('Search')+':</span>\
											<div class="fixed-width-xxl filter-by-country-search-box">\
												<input id="filter-by-country-search" class="form-control filter-by-country-search" type="text">\
												<span id="filter-by-country-search-loading" class="filter-by-country-search-loading" style="display: none;"></span>\
											</div>\
											<a id="filter-by-country-clear-selection" href="javascript:{}" class="btn btn-default pull-right">\
												<i class="icon icon-remove"></i>\
												'+l('Clear Selection')+'\
											</a>\
										</div>\
									'),
									searchLoading = searchBox.find('#filter-by-country-search-loading'),
									clearSelection = searchBox.find('#filter-by-country-clear-selection');

								filterByCountrySearch = searchBox.find('#filter-by-country-search');

								filterByCountrySearch.on('keyup', function(event){
									var val = $.trim(filterByCountrySearch.val());

									if (val.length < 3) 
									{
										filterByCountryDataSource.clearSearch();
										return true;
									} 

									searchLoading.show();

									if (timer != null) clearTimeout(timer);

									timer = setTimeout(function(){

										filterByCountryDataSource.search(val).done(function(response){
											filterByCountryDataSource.applySearch(response);
											searchLoading.hide();
										});

									}, 300);
								});

								clearSelection.on('click', function(event){
									clearFilterByCountries();
								});

								return filterByCountryDataGrid.makeRow([searchBox]);
							});

						}
					},
					close: function(win) 
					{
						appyFilters('filter_by_country');
					},
					content: function(win, parent) 
					{
						winFilterByCountryTemplate = $('\
							<div class="form-group clearfix">\
								<table id="filter-by-country-list" class="table table-bordered filter-by-country-list">\
									<thead>\
										<tr>\
											<th class="chackbox" data-template="chackbox">&nbsp;</th>\
											<th class="np-fc-country-name" data-field="name">'+l('Country Name')+'</th>\
											<th class="np-fc-iso-code" data-field="iso_code">'+l('ISO Code')+'</th>\
											<th class="np-fc-active" data-field="active">'+l('Active')+'</th>\
										</tr>\
									</thead>\
								</table>\
							</div>\
						');

						return winFilterByCountryTemplate;
					}
				});

				var filterByCountry = $('#filter-by-country')
				.gkButton({
					name: '',
					icon: '<i class="icon icon-globe"></i>',
					title: '',
					className: 'filter-by-country',
					css: {
						'padding-left': '10px',
						'padding-right': '10px',
						'margin-right': '10px',
						'float': 'left',
					},
					attr: {
						'data-checked': 0,
					},
					click: function(event) 
					{
						winFilterByCountry.show();
					},
				});

				var getTotalSpent = function()
				{
					return {
						from: (typeof sliderTotalSpent !== 'undefined' && !isNaN(sliderTotalSpent.getValueMin()) ? sliderTotalSpent.getValueMin() : 0),
						to: (typeof sliderTotalSpent !== 'undefined' && !isNaN(sliderTotalSpent.getValueMax()) ? sliderTotalSpent.getValueMax() : 0),
					}
				};

				var getCountries = function()
				{
					var countriesIso = [],
						selection;

					if (typeof filterByCountryDataSource !== 'undefined')
					{
						selection = filterByCountryDataSource.getSelection();

						for (var i = 0; i < selection.length; i++) {
							countriesIso.push(selection[i].data.iso_code);
						}
					}

					return countriesIso;
				};

				function appyFilters(trigger) 
				{
					var filters = {
						groups: filterGroups.getSelected(),
						shops: filterShops.getSelected(),
						gender: filterGender.getSelected(),
						subscribed: filterSubscribed.getSelected(),
						languages: filterLanguages.getSelected(),
						active: filterActive.getSelected(),
						postcode: filterPostcode.getSelected(),
						categories: getSelected(categoryTree),
						by_interest: filterByInterest.getSelected(),
						purchased_product: getFiltersByPurchase(),
						by_birthday: getFilterByBirthday(),
						total_spent: getTotalSpent(),
						filter_by_country: getCountries(),
					};

					var breakFilters = false;

					$.each(filters, function(i, filter){
						if (filter.length) {
							breakFilters = true;
						}
					});

					if ($.trim(filters.by_birthday.from) == '' || $.trim(filters.by_birthday.to) == '' )
						delete filters['by_birthday'];
					else
						breakFilters = true;

					if (trigger == 'range')
					{
						filters['range_selection'] = getRangeSelection();

						if ($.trim(filters.range_selection.min) == 0 || $.trim(filters.range_selection.max) == 0 )
							delete filters['range_selection'];
						else
							breakFilters = true;
					}

					if ((filters.total_spent.from == 0 && filters.total_spent.to == 0) || (filters.total_spent.from == 0 && filters.total_spent.to == maxTotalSpent))
						delete filters['total_spent'];
					else
						breakFilters = true;

					if (filters.filter_by_country.length == 0)
						delete filters['filter_by_country'];
					else
						breakFilters = true;

					if (breakFilters) 
					{
						search.val('');
						checkToggle.hide();
						checkToggleSearch.css({'display': 'inline-block'});

						customersDataSource.filter(filters).done(function(response){
							customersDataSource.applySearch(response);
								resetSliderRange(trigger, 1, response.length);
						});
					} 
					else 
					{
						resetSliderRange(trigger, 1, customersCount());

						checkToggle.show();
						checkToggleSearch.hide();
						customersDataSource.clearSearch();
					}
				}

				var filtersText = $('<span style="margin-left: 0px; margin-right: 10px; float: left; line-height: 28px;">'+l('filters')+'</span>');

				self.reset                              = self.reset || {};
				self.reset.customer                     = self.reset.customer || {};
				self.reset.customer['filterGroups']     = filterGroups;
				self.reset.customer['filterLanguages']  = filterLanguages;
				self.reset.customer['filterShops']      = filterShops;
				self.reset.customer['filterGender']     = filterGender;
				self.reset.customer['filterSubscribed'] = filterSubscribed;
				self.reset.customer['filterActive'] = filterActive;
				self.reset.customer['filterPostcode'] = filterPostcode;
				self.reset.customer['filterCategories'] = filterCategories;
				self.reset.customer['filterByInterest'] = filterByInterest;
				self.reset.customer['filterByPurchase'] = filterByPurchase;
				self.reset.customer['filterByBirthday'] = filterByBirthday;
				self.reset.customer['filterTotalSpent'] = filterTotalSpent;
				self.reset.customer['filterByCountry'] = filterByCountry;
				self.reset.customer['rangeSelection']   = rangeSelection;

				self.addVar('filterCustomers', {
					'groups': filterGroups,
					'languages': filterLanguages,
					'shops': filterShops,
					'gender': filterGender,
					'subscribed': filterSubscribed,
					'active': filterActive,
					'postcode': filterPostcode,
					'by_category': filterCategories,
					'by_interest': filterByInterest,
					'by_purchase': filterByPurchase,
					'by_birthday': filterByBirthday,
					'total_spent': filterTotalSpent,
					'filter_by_country': filterByCountry,
					'range': rangeSelection,
				});

				self.addVar('applyFilerCustomersCallback', appyFilters);

				self.addVar('customers', customersDataSource);

				return makeRow([filtersText ,filterGroups, filterLanguages ,filterShops, filterGender, filterSubscribed, filterActive, filterPostcode, filterTotalSpent, filterByCountry, filterCategories, filterByInterest, filterByPurchase, filterByBirthday, rangeSelection]);
			}, 'prepend');

			var headerActions = customersGrid.addHeader(function(columns){
				var tr, 
					td, 
					searchDiv,
					timer = null, 
					searchText;

				function makeRow(arr) 
				{
					tr = $('<tr></tr>');
					td = $('<th class="gk-header-datagrid customers-header" colspan="'+columns+'"></th>');

					$.each(arr, function(i, item){
						td.append(item);
					});

					tr.html(td);

					return tr;
				}

				searchDiv = $('<div class="customers-search-div"></div>');
				search = $('<input class="form-control customers-search" type="text">');
				searchLoading = $('<span class="customers-search-loading" style="display: none;"></span>');

				search.on('keyup', function(event){
					var val = $.trim(search.val());

					if (val.length < 3) {
						checkToggle.show();
						checkToggleSearch.hide();

						customersDataSource.clearSearch();
						return true;
					} else {
						checkToggle.hide();
						checkToggleSearch.css({'display': 'inline-block'});
					}

					searchLoading.show();

					if ( timer != null ) clearTimeout(timer);

					timer = setTimeout(function(){

						customersDataSource.search(val).done(function(response){
							customersDataSource.applySearch(response);
							searchLoading.hide();
						});

					}, 300);

				});
				searchText = $('<span>'+l('search')+':</span>');

				searchDiv.append(search);
				searchDiv.append(searchLoading);

				var clearFilters = $('#clear-filters')
						.gkButton({
							name: 'clear-filters',
							title: l('clear filters'),
							className: 'clear-filters',
							css: {
								'padding-left': '10px',
								'padding-right': '10px',
								'margin-left': '0',
								'margin-right': '0',
								'position': 'absolute',
								'right': '5px',
							},
							attr: {
								'data-checked': 0,
							},

							click: function(event) {

								search.val('');
								self.reset.customer.filterGroups.uncheckAll();
								self.reset.customer.filterShops.uncheckAll();
								self.reset.customer.filterLanguages.uncheckAll();
								self.reset.customer.filterGender.uncheckAll();
								self.reset.customer.filterSubscribed.uncheckAll();
								self.reset.customer.filterActive.uncheckAll();
								self.reset.customer.filterPostcode.uncheckAll();

								clearCategoriesFilters();
								clearByPurchaseFilters();
								clearByBirthdayFilter();
								clearRangeSelection();
								clearTotalSpentFilter();
								clearFilterByCountries();

								checkToggle.show();
								checkToggleSearch.hide();
								customersDataSource.clearSearch();
							},
							icon: '<i class="icon icon-times"></i> ',
						});

				self.reset = self.reset || {};
				self.reset.customer = self.reset.customer || {};
				self.reset.customer['clearFilters']     = clearFilters;

				self.vars.filterCustomers['clear'] = clearFilters;

				return makeRow([searchText, searchDiv, clearFilters]);
			}, 'prepend');

			self.resetCustomersButton();


			var box = NewsletterPro;
			var winBouncedEmailsContent;

			var winBouncedEmails = new gkWindow({
				width: 640,
				title: l('bounced emails'),
				className: 'bounced-emails-window',
				show: function(win) {},
				close: function(win) {},
				content: function(win, parent) 
				{
					var form = $('\
						<form id="form-bounced-emails" method="post" enctype="multipart/form-data">\
							<div class="form-group clearfix">\
								<label class="control-label col-sm-4"><span class="label-tooltip">'+l('select the csv file')+'</span></label>\
								<div class="col-sm-8 clearfix">\
									<div class="input-group">\
										<span class="input-group-addon">'+l('File')+'</span>\
										<input type="file" name="bounced_emails" class="form-control" style="float: left; margin-right: 10px;">\
										<span class="input-group-addon">'+l('Separator')+'</span>\
										<input type="text" name="bounced_csv_separator" class="form-control" value=";" style="width: 30px; text-align: center; float: left;">\
										<div class="clear"></div>\
									</div>\
								</div>\
								<div class="clear"></div>\
							</div>\
							<div class="clear"></div>\
							<div class="form-group clearfix">\
								<label class="control-label padding-top col-sm-4">'+l('bounced emails method')+'</label>\
								<div class="col-sm-8 clearfix">\
									<div class="radio">\
										<label for="bounced-method-delete" class="in-win">\
											<input id="bounced-method-delete" type="radio" name="bounced_method" value="-1" checked>'+l('delete bounced emails')+'\
										</label>\
									</div>\
									<div class="radio">\
										<label for="bounced-method-unsubscribe" class="in-win">\
											<input id="bounced-method-unsubscribe" type="radio" name="bounced_method" value="0"> '+l('unsubscribe bounced emails')+'\
										</label>\
									</div>\
									<p class="help-block">'+l('bounced emails info')+'</p>\
									<div class="clear"></div>\
								</div>\
							</div>\
							<label class="control-label padding-top col-sm-4">'+l('apply on the lists')+'</label>\
							<div class="col-sm-8 clearfix">\
								<div class="checkbox">\
									<label for="bounced-customers" class="in-win">\
										<input id="bounced-customers" type="checkbox" name="bounced_customers_list" value="1">'+l('customers list')+'\
									</label>\
								</div>\
								<div class="checkbox">\
									<label for="bounced-visitors" class="in-win">\
										<input id="bounced-visitors" type="checkbox" name="bounced_visitors_list" value="1">'+l('visitors subscribed at module block newsletter')+'\
									</label>\
								</div>\
								<div class="checkbox">\
									<label for="bounced-visitors-np" class="in-win">\
										<input id="bounced-visitors-np" type="checkbox" name="bounced_visitors_np_list" value="1">'+l('visitors subscribed at module newsletter pro')+'\
									</label>\
								</div>\
								<div class="checkbox">\
									<label for="bounced-added" class="in-win">\
										<input id="bounced-added" type="checkbox" name="bounced_added_list" value="1">'+l('added list')+'\
									</label>\
								</div>\
							</div>\
							<div class="form-group clearfix">\
								<div class="col-sm-8 col-sm-offset-4">\
									<a id="submit-bounced-emails" href="javascript:{}" class="btn btn-default"><span class="btn-ajax-loader"></span> <i class="icon icon-eraser"></i> '+l('remove bounced')+'</a>\
								</div>\
							</div>\
							<div class="form-group clearfix">\
								<div style="display: block; height: auto; background-position: 5px; padding-top: 10px; padding-bottom: 10px;" class="hint clear">\
									<p style="margin-top: 0;" class="cron-link"><span style="color: black;">'+l('webhook url')+'</span> <span class="icon icon-cron-link"></span>'+box.dataStorage.get('bounce_link')+'</p>\
									<p style="margin-bottom: 0;">'+l('webhook info')+'</p>\
									<div class="clear"></div>\
								</div>\
							</div>\
						</form>\
					');

					var submitBouncedEmails = form.find('#submit-bounced-emails');
					
					submitBouncedEmails.on('click', function(event){
						var conf = confirm(l('confirm delete bounced'));

						if (!conf)
							return;

						box.showAjaxLoader(submitBouncedEmails);

						$.submitAjax({'submit': 'deleteBouncedEmails', name: 'deleteBouncedEmails', form: form}).done(function(response){
							box.hideAjaxLoader(submitBouncedEmails);
							if (response.success)
							{
								var vars = box.modules.sendNewsletters.vars;

								if (response.lists.indexOf('customers') != -1 && vars.hasOwnProperty('customers'))
									vars.customers.sync();
								
								if (response.lists.indexOf('visitors') != -1 && vars.hasOwnProperty('visitors'))
									vars.visitors.sync();
								
								if (response.lists.indexOf('visitors_np') != -1 && vars.hasOwnProperty('visitorsNP'))
									vars.visitorsNP.sync();
								
								if (response.lists.indexOf('added') != -1 && vars.hasOwnProperty('added'))
									vars.added.sync();

								alert(box.displayAlert(response.msg));
							}
							else
								box.alertErrors(response.errors);
						});
					});
					
					return form;
				}
			});

			dom.btnBouncedEmails.on('click', function(event){
				winBouncedEmails.show();
			});

		});
	},

	initVisitorsGrid: function() 
	{
		var self = this;
		self.ready(function(dom){

			// if this feature is active stop the code from execution
			if (self.isNewsletterProSubscriptionActive())
				return;

			var l = NewsletterPro.translations.l(NewsletterPro.translations.modules.sendNewsletters),
				visitorsDataModel,
				visitorsDataSource,
				visitorsGrid = dom.visitorsGrid;

			visitorsDataModel = new gk.data.Model({
				id: 'id',
			});

			visitorsDataSource = new gk.data.DataSource({
				pageSize: 5,
				transport: {
					read: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=getVisitors',
						dataType: 'json',
					},
					update: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=updateVisitor&id',
						dataType: 'json',
					},

					destroy: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=deleteVisitor&id',
						type: 'POST',
						dateType: 'json',
						success: function(response, itemData) {
							if(!response)
								alert(l('delete visitor'));
						},
						error: function(data, itemData) {
							alert(l('delete visitor'));
						},
						complete: function(data, itemData) {},
					},

					search: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=searchVisitor&value',
						dataType: 'json',
					},

					filter: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=filterVisitor',
						dataType: 'json',
					},

				},
				schema: {
					model: visitorsDataModel
				},
				trySteps: 2,
				errors: 
				{
					read: function(xhr, ajaxOptions, thrownError) 
					{
						visitorsDataSource.syncStepAvailableAdd(3000, function(){
							visitorsDataSource.sync();
						});
					},
				}
			});

			visitorsGrid.gkGrid({
				dataSource: visitorsDataSource,
				selectable: false,
				checkable: true,
				currentPage: 1,
				pageable: true,
				start: function()
				{
					dom.visitorsAjaxLoader.show();
				},
				done: function(dataSource) 
				{
					dom.visitorsCount.html(dataSource.items.length);
					dom.visitorsAjaxLoader.hide();
				},
				template: {
					chackbox: function(item, value) {
						var checkBox = $('<input type="checkbox" value="'+value+'" '+(item.isChecked() ? 'checked="checked"' : '')+'> ');			
						return checkBox;
					},

					img_path: function(item, value) {
						return '<img src="'+value+'">';
					},

					active: function(item, value) {
						var a = $('<a href="javascript:{}"></a>'),
							data = item.data;

						function isSubscribed() {
							return parseInt(item.data.active) ? true : false;
						}

						function viewOnlySubscribed() {
							return NewsletterPro.dataStorage.getNumber('configuration.VIEW_ACTIVE_ONLY') ? true : false;
						}

						function switchSubscription() 
						{
							if (isSubscribed()) {
								a.html('<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>');
							} else {
								a.html('<span class="list-action-enable action-disabled"><i class="icon icon-remove"></i></span>');
							}
						}

						switchSubscription();

						a.on('click', function(e){
							e.stopPropagation();

							data.active = (isSubscribed() ? 0 : 1);
							item.update().done(function(response) {
								if (!response) {
									alert('error on subscribe or unsubscribe');
								} else {
									if (viewOnlySubscribed()) {
										item.removeFromScreen();
									} else {
										switchSubscription();
									}
								}
							});
						});

						return a;
					},

					actions: function(item) {
						var deleteVisitor = $('#delete-visitor')
							.gkButton({
								name: 'delete',
								title: l('delete'),
								className: 'visitor-delete',
								item: item,
								command: 'delete',
								confirm: function() {
									return confirm(l('delete visitor confirm'));
								},
								icon: '<i class="icon icon-trash-o"></i> ',
							});

						return deleteVisitor;
					},
				}
			});

			function resetSliderRange(trigger, min, max)
			{
				if (trigger !== 'range' && typeof sliderRange !== 'undefined')
				{
					var reset = {
						min : min,
						max : max,
						valueMin : min,
						valueMax : max,
						values : [min, max],
					};
					if (max <= 0)
					{
						reset['snap'] = 0;
						reset['min'] = 0;
						reset['max'] = 1;
					}
					sliderRange.reset(reset);
					sliderRange.resetPositionMin();
					sliderRange.resetPositionMax();
				}
			}

			function clearRangeSelection()
			{
				resetSliderRange('clear', 1, visitorsCount());
			}

			function visitorsCount()
			{
				return visitorsDataSource.items.length;
			}

			var checkToggle,
				checkToggleSearch,
				searchLoading,
				search;

			var sliderRange;

			var footerActions = visitorsGrid.addFooter(function(columns){
				var tr, td;
				function makeRow(arr) {
					tr = $('<tr></tr>');
					td = $('<td class="gk-footer" colspan="'+columns+'"></td>');

					$.each(arr, function(i, item){
						td.append(item);
					});

					tr.html(td);
					return tr;
				}

				function createCheckToggle(name) {
					var button = $('#'+name)
						.gkButton({
							name: name,
							title: l('check all'),
							className: name,
							css: {
								'padding-left': '10px',
								'padding-right': '10px',
								'margin-left': '0'
							},
							attr: {
								'data-checked': 0,
							},

							click: function(event) {

								function isChecked() {
									return button.data('checked') ? true : false;
								};

								function toggleName(trueStr, falseStr) {
									if (isChecked()) {
										button.data('checked', false);
										button.changeTitle(falseStr);
										return false;
									} else {
										button.data('checked', true);
										button.changeTitle(trueStr);
										return true;
									}
								}

								if (toggleName(l('uncheck all'), l('check all'))) {
									visitorsDataSource.checkAll();
								} else {
									visitorsDataSource.uncheckAll();
								}
							}
						});
					return button;
				}

				checkToggle = createCheckToggle('check-toggle');
				checkToggleSearch = createCheckToggle('check-toggle-search');
				checkToggleSearch.addClass('gk-onfilter');
				checkToggleSearch.hide();

				self.addVar('visitorsCheckAll', checkToggle);

				btnExportCsv = self.buildExportToCSVData(visitorsDataSource, NewsletterPro.dataStorage.get('csv_export_list_ref.LIST_VISITORS'));

				return makeRow([checkToggle, checkToggleSearch, btnExportCsv]);
			}, 'prepend');

			var headerActions = visitorsGrid.addHeader(function(columns){
				var tr, 
					td, 
					searchDiv,
					timer = null, 
					searchText;

				function makeRow(arr) {
					tr = $('<tr></tr>');
					td = $('<th class="gk-header-datagrid visitors-header" colspan="'+columns+'"></th>');

					$.each(arr, function(i, item){
						td.append(item);
					});

					tr.html(td);

					return tr;
				}

				searchDiv = $('<div class="visitors-search-div" style="float: left; margin-right: 10px;"></div>');
				search = $('<input class="gk-input visitors-search" type="text">');
				searchLoading = $('<span class="visitors-search-loading" style="display: none;"></span>');

				search.on('keyup', function(event){
					var val = $.trim(search.val());

					if (val.length < 3) {
						checkToggle.show();
						checkToggleSearch.hide();

						visitorsDataSource.clearSearch();
						return true;
					} else {
						checkToggle.hide();
						checkToggleSearch.css({'display': 'inline-block'});
					}

					searchLoading.show();

					if ( timer != null ) clearTimeout(timer);

					timer = setTimeout(function(){

						visitorsDataSource.search(val).done(function(response){
							visitorsDataSource.applySearch(response);
							searchLoading.hide();
						});

					}, 300);

				});
				searchText = $('<span style="float: left; margin-right: 10px;">'+l('search')+':</span>');

				searchDiv.append(search);
				searchDiv.append(searchLoading);

				var filterShops = $('#gk-filter-shops').gkDropDownMenu({
					title: l('shops'),
					name: 'gk-filter-shops',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},
					data: NewsletterPro.dataStorage.data.filter_shops,
					change: function(values) {
						appyFilters('shops');
					},
				});

				var filterSubscribed = $('#gk-filter-subscribed-visitors').gkDropDownMenu({
					title: l('subscribed'),
					name: 'gk-filter-gender',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},

					data: [
						{'title': l('yes'), 'value': 1},
						{'title': l('no'), 'value': 0},
					],

					change: function(values) {
						appyFilters('subscribed');
					},
				});

				if (NewsletterPro.dataStorage.get('view_active_only'))
					filterSubscribed.hide();

				function getRangeSelection()
				{
					return {
						'min': (typeof sliderRange !== 'undefined' ? sliderRange.getValueMin() : 0) ,
						'max': (typeof sliderRange !== 'undefined' ? sliderRange.getValueMax() : 0) ,
					};
				}

				var winRangeSelection = new gkWindow({
						width: 640,
						height: 150,
						title: l('range selection'),
						className: 'range-selection-window',
						show: function(win) {
							if (typeof sliderRange !== 'undefined')
								sliderRange.refresh();
						},
						close: function(win) {},
						content: function(win, parent) 
						{
							visitorsDataSource.ready().done(function(){
								self.getRangeSelectionContent(function(content)
								{
									win.setContent(content);

									sliderRange = gkSliderRange({
										target: content.find('#slider-range-selection'),
										min : 1,
										max : visitorsCount(),
										valueMin : 1,
										valueMax : visitorsCount(),
										editable: true,
										values : [1, visitorsCount()],

										move: function(obj) {},
										done: function(obj) 
										{
											appyFilters('range');
										},
									});
								});
							});
							return '<span class="ajax-loader" style="margin-left: 310px; margin-top: 36px;"></span>';
						}
					});

				var rangeSelection = $('#range-selection-visitors')
				.gkButton({
					name: 'range-selection-visitors',
					title: l('range selection'),
					className: 'range-selection',
					css: {
						'padding-left': '10px',
						'padding-right': '10px',
						'float': 'left',
						'margin-right': '10px',
						'position': 'relative',
					},
					attr: {
						'data-checked': 0,
					},
					click: function(event) 
					{
						winRangeSelection.show();
					},
				});

				function appyFilters(trigger) 
				{
					var filters = {
						shops: filterShops.getSelected(),
						subscribed: filterSubscribed.getSelected(),
					};

					var breakFilters = false;
					$.each(filters, function(i, filter){
						if (filter.length) {
							breakFilters = true;
						}
					});

					if (trigger == 'range')
					{
						filters['range_selection'] = getRangeSelection();

						if ($.trim(filters.range_selection.min) == 0 || $.trim(filters.range_selection.max) == 0 )
							delete filters['range_selection'];
						else
							breakFilters = true;
					}

					if (breakFilters) {
						search.val('');
						checkToggle.hide();
						checkToggleSearch.css({'display': 'inline-block'});

						visitorsDataSource.filter(filters).done(function(response){
							visitorsDataSource.applySearch(response);
							resetSliderRange(trigger, 1, response.length);
						});

					} else {
						resetSliderRange(trigger, 1, visitorsCount());

						checkToggle.show();
						checkToggleSearch.hide();
						visitorsDataSource.clearSearch();
					}
				}

				var filtersText = $('<span style="margin-left: 20px; margin-right: 10px; float: left; line-height: 28px;">'+l('filters')+'</span>');
				var clearFilters = $('#clear-filters-visitors')
						.gkButton({
							name: 'clear-filters-visitors',
							title: l('clear filters'),
							className: 'clear-filters',
							css: {
								'padding-left': '10px',
								'padding-right': '10px',
								'margin-left': '0',
								'margin-right': '0',
								'position': 'absolute',
								'right': '5px',
							},
							attr: {
								'data-checked': 0,
							},

							click: function(event) {

								search.val('');
								filterShops.uncheckAll();
								filterSubscribed.uncheckAll();

								clearRangeSelection();

								checkToggle.show();
								checkToggleSearch.hide();
								visitorsDataSource.clearSearch();

							},
							icon: '<i class="icon icon-times"></i> ',
						});

				self.reset = self.reset || {};
				self.reset.visitor = {
					filterShops: filterShops,
					filterSubscribed: filterSubscribed,
					rangeSelection: rangeSelection,
					clearFilters: clearFilters,
				};

				self.addVar('filterVisitor', {
					'shops': filterShops,
					'subscribed': filterSubscribed,
					'range': rangeSelection,
					'clear': clearFilters,
				});

				self.addVar('applyFilerVisitorCallback', appyFilters);

				self.addVar('visitors', visitorsDataSource);

				return makeRow([searchText, searchDiv, filtersText, filterShops, filterSubscribed, rangeSelection, clearFilters]);
			}, 'prepend');

			self.resetVisitorsButton();
		});

	},

	initVisitorsGridNewsletterPro: function()
	{
		var self = this,
			box = this.box;

		self.ready(function(dom){

			// if this feature is not active stop the cod from acecution
			if (!self.isNewsletterProSubscriptionActive())
				return;

			var l = NewsletterPro.translations.l(NewsletterPro.translations.modules.sendNewsletters),
				visitorsNPDataModel,
				visitorsNPDataSource,
				visitorsNPGrid = dom.visitorsNPGrid,
				clearFilters;

			visitorsNPDataModel = new gk.data.Model({
				id: 'id_newsletter_pro_subscribers',
			});

			visitorsNPDataSource = new gk.data.DataSource({
				pageSize: 5,
				transport: {
					read: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=getVisitorsNP',
						dataType: 'json',
					},
					update: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=updateVisitorNP&id',
						dataType: 'json',
					},

					destroy: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=deleteVisitorNP&id',
						type: 'POST',
						dateType: 'json',
						success: function(response, itemData) {
							if(!response)
								alert(l('delete visitor'));
						},
						error: function(data, itemData) {
							alert(l('delete visitor'));
						},
						complete: function(data, itemData) {},
					},

					search: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=searchVisitorNP&value',
						dataType: 'json',
					},

					filter: {
						url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=filterVisitorNP',
						dataType: 'json',
					},

				},
				schema: {
					model: visitorsNPDataModel
				},
				trySteps: 2,
				errors: 
				{
					read: function(xhr, ajaxOptions, thrownError) 
					{
						visitorsNPDataSource.syncStepAvailableAdd(3000, function(){
							visitorsNPDataSource.sync();
						});
					},
				}
			});

			var visitorsNpTemplate = {
				img_path: function(item, value) 
				{
					var div = $('<div></div>');
					var lang_img = '<img src="'+value+'">';
					var gender_img = self.getGenderImageById(item.data.id_gender);

					div.append(lang_img);
					div.append(gender_img);
					div.width('38');
					return div;
				},

				active: function(item, value) 
				{
					var a = $('<a href="javascript:{}"></a>'),
						data = item.data;

					function isSubscribed() 
					{
						return parseInt(item.data.active) ? true : false;
					}

					function viewOnlySubscribed() 
					{
						return NewsletterPro.dataStorage.getNumber('configuration.VIEW_ACTIVE_ONLY') ? true : false;
					}

					function switchSubscription() 
					{
						if (isSubscribed()) 
							a.html('<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>');
						else 
							a.html('<span class="list-action-enable action-disabled"><i class="icon icon-remove"></i></span>');
					}

					switchSubscription();

					a.on('click', function(e){
						e.stopPropagation();
						data.active = (isSubscribed() ? 0 : 1);

						item.update().done(function(response) {
							if (!response) 
							{
								alert('error on subscribe or unsubscribe');
							} 
							else 
							{
								if (viewOnlySubscribed()) 
									item.removeFromScreen();
								else 
									switchSubscription();
							}
						});
					});

					return a;
				},

				chackbox: function(item, value) 
				{
					var checkBox = $('<input type="checkbox" value="'+value+'" '+(item.isChecked() ? 'checked="checked"' : '')+'> ');			
					return checkBox;
				},

				actions: function(item) 
				{
					var deleteVisitorNP = $('#delete-visitor-np')
						.gkButton({
							name: 'delete',
							title: l('delete'),
							className: 'added-delete',
							item: item,
							command: 'delete',
							confirm: function() {
								return confirm(l('delete added confirm'));
							},
							icon: '<i class="icon icon-trash-o"></i> ',
						});
					return deleteVisitorNP;
				},
			};

			var newCustomColumns = box.dataStorage.get('configuration.SHOW_CUSTOM_COLUMNS');
			var allVariables = box.dataStorage.get('custom_field.variables_types');
			var typesConst = box.dataStorage.get('custom_field.types_cost');
			var displayLength = 25;

			var winDisplayLimit = new gkWindow({
				width: 600,
				height: 400,
				setScrollContent: 340,
				title: l('Details'),
				className: 'np-costum-fields-win',
				show: function(win) {},
				close: function(win) {},
				content: function(win) {

				}
			});

			var newVariablesTypes = [];

			if (allVariables.length > 0)
			{
				for (var i = 0; i < allVariables.length; i++)
				{
					var variable = allVariables[i];
					if (newCustomColumns.indexOf(variable.variable_name) != -1)
						newVariablesTypes.push(variable);
				}

				for (var i = 0; i < newVariablesTypes.length; i++)
				{
					var item = newVariablesTypes[i];
					// has multiple values
					if (Number(item.type) == Number(typesConst.TYPE_CHECKBOX))
					{
						visitorsNpTemplate[item.variable_name] = function(item, value)
						{
							var showDetails = $('<a href="javascript:{}">...</a>');
							var displayStr = value;
							var displayValue = value;

							try
							{
								var array = jQuery.parseJSON(value);
								displayStr = array.join(', ');
							}
							catch(e)
							{

							}

							if (displayStr.length <= displayLength)
							{
								displayStr = displayStr;
								displayValue = displayStr;
							}
							else
							{
								displayValue = displayStr;
								tmpDisplayStr = displayStr.slice(0, displayLength);

								displayStr = $('<span>');
								displayStr.append(tmpDisplayStr);
								displayStr.append(showDetails);
							}

							showDetails.on('click', function(e){
								e.stopPropagation();
								winDisplayLimit.show(displayValue);
							});

							return displayStr;
						};
					}
					else
					{
						visitorsNpTemplate[item.variable_name] = function(item, value) 
						{
							var showDetails = $('<a href="javascript:{}">...</a>');
							var displayStr = value;

							if (displayStr.length <= displayLength)
								displayStr = displayStr;
							else
							{
								tmpDisplayStr = displayStr.slice(0, displayLength);

								displayStr = $('<span>');
								displayStr.append(tmpDisplayStr);
								displayStr.append(showDetails);
							}

							showDetails.on('click', function(e){
								e.stopPropagation();
								winDisplayLimit.show(value);
							});

							return displayStr;
						}
					}
				}
			}

			visitorsNPGrid.gkGrid({
				dataSource: visitorsNPDataSource,
				selectable: false,
				checkable: true,
				currentPage: 1,
				pageable: true,
				start: function()
				{
					dom.visitorsNPAjaxLoader.show();
				},
				done: function(dataSource) 
				{
					dom.visitorsNPCount.html(dataSource.items.length);
					dom.visitorsNPAjaxLoader.hide();
				},
				template: visitorsNpTemplate
			});

			function setFilterBirthdayFrom(val)
			{
				birthdayDate.from = val;
			}

			function setFilterBirthdayTo(val)
			{
				birthdayDate.to = val;
			}

			function clearByBirthdayFilter()
			{
				if (typeof birthdayFrom !== 'undefined')
				{
					birthdayFrom.val('');
					setFilterBirthdayFrom('');
				}

				if (typeof birthdayTo !== 'undefined')
				{
					birthdayTo.val('');
					setFilterBirthdayTo('');
				}
			}

			var birthdayDate = {
				'from': '',
				'to': '',
			};

			function clearRangeSelection()
			{
				resetSliderRange('clear', 1, visitorsNPCount());
			}

			function visitorsNPCount()
			{
				return visitorsNPDataSource.items.length;
			}

			function resetSliderRange(trigger, min, max)
			{
				if (trigger !== 'range' && typeof sliderRange !== 'undefined')
				{
					var reset = {
						min : min,
						max : max,
						valueMin : min,
						valueMax : max,
						values : [min, max],
					};
					if (max <= 0)
					{
						reset['snap'] = 0;
						reset['min'] = 0;
						reset['max'] = 1;
					}
					sliderRange.reset(reset);
					sliderRange.resetPositionMin();
					sliderRange.resetPositionMax();
				}
			}

			var checkToggle,
			checkToggleSearch,
			searchLoading,
			search,
			conditions = box.dataStorage.get('search_conditions.conditions'),
			conditionsConst = box.dataStorage.get('search_conditions.conditions_const'),
			allColumns = box.dataStorage.get('search_conditions.visitors_np_columns'),
			defaultConditionType = Number(conditionsConst.SEARCH_CONDITION_CONTAINS),
			defaultFieldValue = 0,
			selectFilterCondition,
			selectFilterField;

			var sliderRange;
			var birthdayFrom;
			var birthdayTo;
			var fbbClear;

			var footerActions = visitorsNPGrid.addFooter(function(columns){
				var tr, td;
				function makeRow(arr) 
				{
					tr = $('<tr></tr>');
					td = $('<td class="gk-footer" colspan="'+columns+'"></td>');

					$.each(arr, function(i, item){
						td.append(item);
					});

					tr.html(td);
					return tr;
				}

				var getDisplayCustomColumnsContent = function()
				{
					var fields = box.dataStorage.get('custom_field.variables'),
						selectedFields = box.dataStorage.get('configuration.SHOW_CUSTOM_COLUMNS'),
						renderColumns = '';

					for (var i = 0; i < fields.length; i++) 
					{
						var field = fields[i],
							name = '',
							split, 
							checked = selectedFields.indexOf(field) != -1 ? true : false;

						split = field.split('_');

						for (var j = 1; j < split.length; j++)
						{
							name += split[j][0].toUpperCase() + split[j].slice(1);
						}

						renderColumns += '\
							<div class="checkbox">\
								<label class="control-label in-win">\
									<input type="checkbox" name="np_show_custom_colums_'+i+'" value="'+field+'" '+(checked ? 'checked="checked"' : '')+'>\
									'+name+'\
								</label>\
							</div>';
					}

					var template = $('\
						<div class="form-group clearfix">\
							<label class="control-label col-sm-3" style="padding-top: 13px;">'+l('Show Columns')+'</label>\
							<div class="col-sm-9">\
								'+renderColumns+'\
							</div>\
						</div>\
						<div class="form-group clearfix">\
							<div class="col-sm-9 col-sm-offset-3">\
								<a href="javascript:{}" id="np-save-show-columns" class="btn btn-success pull-left"><i class="icon icon-save"></i> '+l('Save')+'</a>\
							</div>\
						</div>\
					');

					var btnShowColumns = template.find('#np-save-show-columns');

					btnShowColumns.on('click', function(){
						var columns = [];

						$.each(template.find('[name^="np_show_custom_colums"]:checked'), function(i, item){
							columns.push($(item).val());
						});

						$.postAjax({'submit_custom_field': 'saveShowColumns', columns: columns}).done(function(response){
							if (!response.success)
								box.alertErrors(response.errors);
							else
								location.reload();
						});
					});

					return template;
				};

				var winDisplayCustomColumns = new gkWindow({
					width: 600,
					height: 400,
					setScrollContent: 340,
					title: l('Display Custom Columns'),
					className: 'np-costum-fields-win',
					show: function(win) 
					{
						$.postAjax({'submit_custom_field': 'getCustomColumns'}).done(function(response){
							if (response.success)
							{
								box.dataStorage.set('custom_field.variables', response.variables);
								win.setContent(getDisplayCustomColumnsContent());
							}
						});
					},
					close: function(win) {},
					content: function(win) 
					{
						return getDisplayCustomColumnsContent();
					}
				});

				self.addVar('winDisplayCustomColumns', winDisplayCustomColumns);
				
				var displayCustomColumns = $('<a href="javascript:{}" class="btn btn-default pull-right"><i class="icon icon-eye"></i> '+l('Display Custom Columns')+'</a>');

				displayCustomColumns.on('click', function(){
					winDisplayCustomColumns.show();
				});

				function createCheckToggle(name) 
				{
					var button = $('#'+name)
						.gkButton({
							name: name,
							title: l('check all'),
							className: name,
							css: {
								'padding-left': '10px',
								'padding-right': '10px',
								'margin-left': '0'
							},
							attr: {
								'data-checked': 0,
							},

							click: function(event) 
							{
								function isChecked() 
								{
									return button.data('checked') ? true : false;
								};

								function toggleName(trueStr, falseStr) {
									if (isChecked()) {
										button.data('checked', false);
										button.changeTitle(falseStr);
										return false;
									} else {
										button.data('checked', true);
										button.changeTitle(trueStr);
										return true;
									}
								}

								if (toggleName(l('uncheck all'), l('check all')))
									visitorsNPDataSource.checkAll();
								else
									visitorsNPDataSource.uncheckAll();
							}
						});

					return button;
				}

				checkToggle = createCheckToggle('check-toggle');
				checkToggleSearch = createCheckToggle('check-toggle-search');
				checkToggleSearch.addClass('gk-onfilter');
				checkToggleSearch.hide();

				self.addVar('visitorsNPCheckAll', checkToggle);

				btnExportCsv = self.buildExportToCSVData(visitorsNPDataSource, NewsletterPro.dataStorage.get('csv_export_list_ref.LIST_VISITORS_NP'));

				btnExportCsv.css({
					'margin-right': '3px',
				});

				return makeRow([checkToggle, checkToggleSearch, displayCustomColumns, btnExportCsv]);
			}, 'prepend');

			var filterLanguages,
				filterShops,
				filterGender,
				filterSubscribed,
				filterByInterest,
				filterByBirthday,
				rangeSelection;

			var headerActions1 = visitorsNPGrid.addHeader(function(columns){
				var tr, 
					td, 
					searchDiv,
					timer = null, 
					searchText;

				function makeRow(arr) 
				{
					tr = $('<tr></tr>');
					td = $('<th class="gk-header-datagrid visitors-np-header" colspan="'+columns+'"></th>');

					$.each(arr, function(i, item){
						td.append(item);
					});

					tr.html(td);

					return tr;
				}

				filterLanguages = $('#gk-filter-languages').gkDropDownMenu({
					title: l('languages'),
					name: 'gk-filter-languages',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},
					data: NewsletterPro.dataStorage.data.filter_languages,
					change: function(values) {
						appyFilters();
					},
				});

				filterShops = $('#gk-filter-shops').gkDropDownMenu({
					title: l('shops'),
					name: 'gk-filter-shops',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},
					data: NewsletterPro.dataStorage.data.filter_shops,
					change: function(values) {
						appyFilters();
					},
				});

				filterGender = $('#gk-filter-gender-np').gkDropDownMenu({
					title: l('gender'),
					name: 'gk-filter-gender',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},

					data: NewsletterPro.dataStorage.get('filter_genders'),

					change: function(values) {
						appyFilters();
					},
				});


				filterSubscribed = $('#gk-filter-subscribed-np').gkDropDownMenu({
					title: l('subscribed'),
					name: 'gk-filter-gender',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},

					data: [
						{'title': l('yes'), 'value': 1},
						{'title': l('no'), 'value': 0},
					],

					change: function(values) {
						appyFilters('subscribed');
					},
				});
				
				if (NewsletterPro.dataStorage.get('view_active_only'))
					filterSubscribed.hide();

				filterByInterest = $('#gk-filter-by-interest-np').gkDropDownMenu({
					title: l('by list of interest'),
					name: 'gk-filter-gender',
					css: {
						'float': 'left',
						'margin-right': '10px',
					},

					data: NewsletterPro.dataStorage.get('filter_list_of_interest'),
					activeClass: {
						enable: '',
						disable: 'btn-filter-list-of-interst-inactive',
					},
					change: function(values) 
					{
						appyFilters();
					},
				});

				function getFilterByBirthdayContent(func)
				{
					$.postAjax({'submit': 'getFilterByBirthdayContent', fbb_class: 'visitorsNP'}, 'html').done(function(content){
						func($(content));
					});
				}

				function getFilterByBirthday()
				{
					return birthdayDate;
				}

				function getMySqlDate(dateObj)
				{
					var year = dateObj.selectedYear,
						mounth = (String(dateObj.selectedMonth).length == 1 ? '0'+String(dateObj.selectedMonth+1) : String(dateObj.selectedMonth+1)),
						day = (String(dateObj.selectedDay).length == 1 ? '0'+String(dateObj.selectedDay) : String(dateObj.selectedDay));
					return mysql_date = year+'-'+mounth+'-'+day;
				}

				var winByBirthday = new gkWindow({
						width: 640,
						height: 320,
						title: l('filter by birthday'),
						className: 'filter-by-birthday-window',
						show: function(win) {},
						close: function(win) {},
						content: function(win, parent) 
						{
							getFilterByBirthdayContent(function(content)
							{
								win.setContent(content);

								birthdayFrom = content.find('#fbb-from-visitorsNP');
								birthdayTo = content.find('#fbb-to-visitorsNP');
								fbbClear = content.find('#fbb-clear-visitorsNP');

								birthdayFrom.datepicker({ 
									dateFormat: self.box.dataStorage.get('jquery_date_birthday'),
									onSelect: function(date, dateObj)
									{
										setFilterBirthdayFrom(getMySqlDate(dateObj));
										appyFilters('birthday');
									},
									beforeShow: function(input, inst)
									{
										if (!inst.dpDiv.hasClass('date-birthday'))
											inst.dpDiv.addClass('date-birthday');
									}
								});

								birthdayTo.datepicker({
									dateFormat: self.box.dataStorage.get('jquery_date_birthday'),
									onSelect: function(date, dateObj)  
									{
										setFilterBirthdayTo(getMySqlDate(dateObj));
										appyFilters('birthday');
									},
									beforeShow: function(input, inst)
									{
										if (!inst.dpDiv.hasClass('date-birthday'))
											inst.dpDiv.addClass('date-birthday');
									}
								});

								birthdayFrom.on('change', function()
								{									
									if ($.trim($(this).val()) == '')
									{
										setFilterBirthdayFrom('');
										appyFilters('birthday');
									}
								});

								birthdayTo.on('change', function()
								{

									if ($.trim($(this).val()) == '')
									{
										setFilterBirthdayTo('');
										appyFilters('birthday');
									}
								});

								fbbClear.on('click', function()
								{

									clearByBirthdayFilter();
									appyFilters('birthday');
								});

							});
							return '<span class="ajax-loader" style="margin-left: 310px; margin-top: 119px;"></span>';
						}
					});

				filterByBirthday = $('#by-birthday-filters')
					.gkButton({
						name: 'by-birthday-filters',
						title: l('by birthday'),
						className: 'by-birthday-filters',
						css: {
							'padding-left': '10px',
							'padding-right': '10px',
							'float': 'left',
							'margin-right': '10px',
							'position': 'relative',
						},
						attr: {
							'data-checked': 0,
						},
						click: function(event) {
							winByBirthday.show();
						},
					});

				function getRangeSelection()
				{
					return {
						'min': (typeof sliderRange !== 'undefined' ? sliderRange.getValueMin() : 0) ,
						'max': (typeof sliderRange !== 'undefined' ? sliderRange.getValueMax() : 0) ,
					};
				}

				var winRangeSelection = new gkWindow({
					width: 640,
					height: 150,
					title: l('range selection'),
					className: 'range-selection-window',
					show: function(win) {
						if (typeof sliderRange !== 'undefined')
							sliderRange.refresh();
					},
					close: function(win) {},
					content: function(win, parent) 
					{
						visitorsNPDataSource.ready().done(function(){
							self.getRangeSelectionContent(function(content)
							{
								win.setContent(content);

								sliderRange = gkSliderRange({
									target: content.find('#slider-range-selection'),
									min : 1,
									max : visitorsNPCount(),
									valueMin : 1,
									valueMax : visitorsNPCount(),
									editable: true,
									values : [1, visitorsNPCount()],

									move: function(obj) {},
									done: function(obj) 
									{
										appyFilters('range');
									},
								});
							});
						});
						return '<span class="ajax-loader" style="margin-left: 310px; margin-top: 36px;"></span>';
					}
				});

				rangeSelection = $('#range-selection-added')
				.gkButton({
					name: 'range-selection-added',
					title: l('range selection'),
					className: 'range-selection',
					css: {
						'padding-left': '10px',
						'padding-right': '10px',
						'float': 'left',
						'margin-right': '10px',
						'position': 'relative',
					},
					attr: {
						'data-checked': 0,
					},
					click: function(event) 
					{
						winRangeSelection.show();
					},
				});

				function appyFilters(trigger) 
				{
					var filters = {
						shops: filterShops.getSelected(),
						languages: filterLanguages.getSelected(),
						gender: filterGender.getSelected(),
						subscribed: filterSubscribed.getSelected(),
						by_interest: filterByInterest.getSelected(),
						by_birthday: getFilterByBirthday(),
					};

					var breakFilters = false;
					$.each(filters, function(i, filter){
						if (filter.length) {
							breakFilters = true;
						}
					});

					if ($.trim(filters.by_birthday.from) == '' || $.trim(filters.by_birthday.to) == '' )
						delete filters['by_birthday'];
					else
						breakFilters = true;

					if (trigger == 'range')
					{
						filters['range_selection'] = getRangeSelection();

						if ($.trim(filters.range_selection.min) == 0 || $.trim(filters.range_selection.max) == 0 )
							delete filters['range_selection'];
						else
							breakFilters = true;
					}

					if (breakFilters) {
						search.val('');

						if (typeof selectFilterCondition !== 'undefined')
						{
							selectFilterCondition.val(defaultConditionType);
							selectFilterField.val(defaultFieldValue);
							
							box.dataStorage.set('search_conditions.selected_condition', defaultConditionType);
							box.dataStorage.set('search_conditions.selected_field', defaultFieldValue);
						}

						checkToggle.hide();
						checkToggleSearch.css({'display': 'inline-block'});

						visitorsNPDataSource.filter(filters).done(function(response){
							visitorsNPDataSource.applySearch(response);
							resetSliderRange(trigger, 1, response.length);

						});

					} else {
						resetSliderRange(trigger, 1, visitorsNPCount());

						checkToggle.show();
						checkToggleSearch.hide();
						visitorsNPDataSource.clearSearch();
					}
				}

				var filtersText = $('<span style="margin-left: 0px; margin-right: 10px; float: left; line-height: 28px;">'+l('filters')+'</span>');

				self.reset = self.reset || {};

				self.reset.visitorsNP = self.reset.visitorsNP || {};
				self.reset.visitorsNP['filterLanguages'] = filterLanguages;
				self.reset.visitorsNP['filterShops'] = filterShops;
				self.reset.visitorsNP['filterGender'] = filterGender;
				self.reset.visitorsNP['filterSubscribed'] = filterSubscribed;
				self.reset.visitorsNP['filterByInterest'] = filterByInterest;
				self.reset.visitorsNP['filterByBirthday'] = filterByBirthday;
				self.reset.visitorsNP['rangeSelection'] = rangeSelection;

				self.addVar('filterVisitorNP', {
					'languages': filterLanguages,
					'shops': filterShops,
					'gender': filterGender,
					'subscribed': filterSubscribed,
					'filter_by_interest': filterByInterest,
					'by_birthday': filterByBirthday,
					'range': rangeSelection,
				});

				self.addVar('applyFilerVisitorNpCallback', appyFilters);

				return makeRow([filtersText , filterLanguages ,filterShops, filterGender, filterSubscribed, filterByInterest, filterByBirthday, rangeSelection]);
			}, 'prepend');

			var headerActions = visitorsNPGrid.addHeader(function(columns){
				var tr, 
					td, 
					searchDiv,
					timer = null, 
					searchText,
					searchFilterDiv,
					searchFilter,
					searchFilterText;

				box.dataStorage.set('search_conditions.selected_condition', defaultConditionType);
				box.dataStorage.set('search_conditions.selected_field', defaultFieldValue);

				function makeRow(arr) 
				{
					tr = $('<tr></tr>');
					td = $('<th class="gk-header-datagrid visitors-np-header" colspan="'+columns+'"></th>');

					$.each(arr, function(i, item){
						td.append(item);
					});

					tr.html(td);

					return tr;
				}

				searchDiv = $('<div class="visitors-np-search-div"></div>');
				search = $('<input class="gk-input visitors-np-search" type="text">');
				searchLoading = $('<span class="visitors-np-search-loading" style="display: none;"></span>');

				search.on('keyup', function(event){
					var val = $.trim(search.val());

					// accept one value for integers
					if (val.length <= 0) {
						checkToggle.show();
						checkToggleSearch.hide();

						visitorsNPDataSource.clearSearch();
						return true;
					} else {
						checkToggle.hide();
						checkToggleSearch.css({'display': 'inline-block'});
					}

					searchLoading.show();

					if ( timer != null ) clearTimeout(timer);

					timer = setTimeout(function(){

						var conditions = {};

						if (typeof selectFilterCondition !== 'undefined')
						{
							conditions = {
								'selected_condition': Number(selectFilterCondition.val()),
								'selected_field': selectFilterField.val()
							};
						}

						visitorsNPDataSource.search(val, {'conditions': conditions}).done(function(response){
							visitorsNPDataSource.applySearch(response);
							searchLoading.hide();
						});

					}, 300);

				});
				searchText = $('<span>'+l('search')+':</span>');

				searchDiv.append(search);
				searchDiv.append(searchLoading);

				clearFilters = $('#clear-filters')
						.gkButton({
							name: 'clear-filters',
							title: l('clear filters'),
							className: 'clear-filters',
							css: {
								'padding-left': '10px',
								'padding-right': '10px',
								'margin-left': '0',
								'margin-right': '0',
								'position': 'absolute',
								'right': '5px',
							},
							attr: {
								'data-checked': 0,
							},

							click: function(event) {

								search.val('');

								if (typeof selectFilterCondition !== 'undefined')
								{
									selectFilterCondition.val(defaultConditionType);
									selectFilterField.val(defaultFieldValue);
									
									box.dataStorage.set('search_conditions.selected_condition', defaultConditionType);
									box.dataStorage.set('search_conditions.selected_field', defaultFieldValue);
								}

								filterShops.uncheckAll();
								filterGender.uncheckAll();
								filterSubscribed.uncheckAll();

								filterByInterest.uncheckAll();
								filterLanguages.uncheckAll();

								clearRangeSelection();
								clearByBirthdayFilter();

								checkToggle.show();
								checkToggleSearch.hide();
								visitorsNPDataSource.clearSearch();
							},
							icon: '<i class="icon icon-times"></i> ',
						});

				var searchConditionOptions = '';

				for (var key in conditions) {
					searchConditionOptions += '<option value="'+key+'" '+(Number(key) == defaultConditionType ? 'selected="selected"' : '')+'>'+conditions[key]+'</option>';
				}

				var searchConditionColumns = '';

				for (var i = 0; i < allColumns.length; i++) 
				{
					var column = allColumns[i],
						columnSplit = column.replace(/^np_/, '').split('_'),
						columnName = '';

					for (var j = 0; j < columnSplit.length; j++) {
						columnSplit[j] = columnSplit[j][0].toUpperCase() + columnSplit[j].slice(1);
					}

					columnName = columnSplit.join(' ');

					searchConditionColumns += '<option value="'+column+'">'+columnName+'</option>';
				}

				searchFilterDiv = $('\
					<div class="np-visitors-np-search-filter-condition-div">\
						<select id="np-visitors-np-select-search-filter-condition-div" class="form-control fixed-width-l">\
							'+searchConditionOptions+'\
						</select>\
					</div>\
					<div class="np-visitors-np-search-filter-columns-div">\
						<select id="np-visitors-np-select-search-filter-columns-div" class="form-control fixed-width-l">\
							<option value="'+defaultFieldValue+'" selected="selected">'+l('all fields')+'</option>\
							'+searchConditionColumns+'\
						</select>\
					</div>\
				');

				selectFilterCondition = searchFilterDiv.find('#np-visitors-np-select-search-filter-condition-div');
				selectFilterField = searchFilterDiv.find('#np-visitors-np-select-search-filter-columns-div');

				selectFilterCondition.on('change', function(){
					box.dataStorage.set('search_conditions.selected_condition', Number($(this).val()));
					search.trigger('keyup');
				});

				selectFilterField.on('change', function(){
					box.dataStorage.set('search_conditions.selected_field', $(this).val());
					search.trigger('keyup');
				});

				self.reset = self.reset || {};
				self.reset.visitorsNP = self.reset.visitorsNP || {};
				self.reset.visitorsNP['clearFilters'] = clearFilters;

				self.vars.filterVisitorNP['clear'] = clearFilters;

				self.addVar('visitorsNP', visitorsNPDataSource);

				return makeRow([searchText, searchDiv, searchFilterDiv, clearFilters]);
			}, 'prepend');

			self.resetVisitorsNPButton();
		});
	},

	initAddedGrid: function() {
		var self = this;

		self.ready(function(dom) {
			var l = NewsletterPro.translations.l(NewsletterPro.translations.modules.sendNewsletters),
				addedDataModel,
				addedDataSource,
				addedGrid = dom.addedGrid;

				addedDataModel = new gk.data.Model({
					id: 'id_newsletter_pro_email',
				});

				addedDataSource = new gk.data.DataSource({
					pageSize: 10,
					transport: {
						read: {
							url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=getAdded',
							dataType: 'json',
						},

						create: {
							url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=createAdded',
							dataType: 'json',
						},

						update: {
							url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=updateAdded&id',
							dataType: 'json',
						},

						destroy: {
							url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=deleteAdded&id',
							type: 'POST',
							dateType: 'json',
							success: function(response, itemData) {
								if(!response)
									alert(l('delete record'));
							},
							error: function(data, itemData) {
								alert(l('delete record'));
							},
							complete: function(data, itemData) {},
						},

						search: {
							url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=searchAdded&value',
							dataType: 'json',
						},

						filter: {
							url: NewsletterPro.dataStorage.get('ajax_url')+'&submit=filterAdded',
							dataType: 'json',
						},

					},
					schema: {
						model: addedDataModel
					},
					trySteps: 2,
					errors: 
					{
						read: function(xhr, ajaxOptions, thrownError) 
						{
							addedDataSource.syncStepAvailableAdd(3000, function(){
								addedDataSource.sync();
							});
						},
					},
				});

				addedGrid.gkGrid({
					dataSource: addedDataSource,
					selectable: false,
					checkable: true,
					currentPage: 1,
					pageable: true,
					start: function()
					{
						dom.addedAjaxLoader.show();
					},
					done: function(dataSource) 
					{
						dom.addedCount.html(dataSource.items.length);
						dom.addedAjaxLoader.hide();
					},
					template: {
						img_path: function(item, value) 
						{
							return '<img src="'+value+'">';
						},

						active: function(item, value) 
						{
							var a = $('<a href="javascript:{}"></a>'),
								data = item.data;

							function isSubscribed() {
								return parseInt(item.data.active) ? true : false;
							}

							function viewOnlySubscribed() {
								return NewsletterPro.dataStorage.getNumber('configuration.VIEW_ACTIVE_ONLY') ? true : false;
							}

							function switchSubscription() 
							{
								if (isSubscribed()) {
									a.html('<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>');
								} else {
									a.html('<span class="list-action-enable action-disabled"><i class="icon icon-remove"></i></span>');
								}
							}

							switchSubscription();

							a.on('click', function(e){
								e.stopPropagation();
								data.active = (isSubscribed() ? 0 : 1);

								item.update().done(function(response) {
									if (!response) {
										alert('error on subscribe or unsubscribe');
									} else {
										if (viewOnlySubscribed()) {
											item.removeFromScreen();
										} else {
											switchSubscription();
										}
									}
								});
							});

							return a;
						},

						chackbox: function(item, value) 
						{
							var checkBox = $('<input type="checkbox" value="'+value+'" '+(item.isChecked() ? 'checked="checked"' : '')+'> ');			
							return checkBox;
						},

						actions: function(item) 
						{
							var deleteAdded = $('#delete-added')
								.gkButton({
									name: 'delete',
									title: l('delete'),
									className: 'added-delete',
									item: item,
									command: 'delete',
									confirm: function() {
										return confirm(l('delete added confirm'));
									},
									icon: '<i class="icon icon-trash-o"></i> ',
								});
							return deleteAdded;
						},
					}
				});

				function clearRangeSelection()
				{
					resetSliderRange('clear', 1, addedCount());
				}
				function addedCount()
				{
					return addedDataSource.items.length;
				}
				function resetSliderRange(trigger, min, max)
				{
					if (trigger !== 'range' && typeof sliderRange !== 'undefined')
					{
						var reset = {
							min : min,
							max : max,
							valueMin : min,
							valueMax : max,
							values : [min, max],
						};
						if (max <= 0)
						{
							reset['snap'] = 0;
							reset['min'] = 0;
							reset['max'] = 1;
						}
						sliderRange.reset(reset);
						sliderRange.resetPositionMin();
						sliderRange.resetPositionMax();
					}
				}

				var checkToggle,
				checkToggleSearch,
				searchLoading,
				search;

				var sliderRange;

				var footerActions = addedGrid.addFooter(function(columns){
					var tr, td, addButton, winAdd;
					function makeRow(arr) {
						tr = $('<tr></tr>');
						td = $('<td class="gk-footer" colspan="'+columns+'"></td>');

						$.each(arr, function(i, item){
							td.append(item);
						});

						tr.html(td);
						return tr;
					}

					function createCheckToggle(name) {
						var button = $('#'+name)
							.gkButton({
								name: name,
								title: l('check all'),
								className: name,
								css: {
									'padding-left': '10px',
									'padding-right': '10px',
									'margin-left': '0'
								},
								attr: {
									'data-checked': 0,
								},

								click: function(event) 
								{
									function isChecked() {
										return button.data('checked') ? true : false;
									};

									function toggleName(trueStr, falseStr) {
										if (isChecked()) {
											button.data('checked', false);
											button.changeTitle(falseStr);
											return false;
										} else {
											button.data('checked', true);
											button.changeTitle(trueStr);
											return true;
										}
									}

									if (toggleName(l('uncheck all'), l('check all'))) {
										addedDataSource.checkAll();
									} else {
										addedDataSource.uncheckAll();
									}
								}
							});
						return button;
					}

					checkToggle = createCheckToggle('check-toggle');
					checkToggleSearch = createCheckToggle('check-toggle-search');
					checkToggleSearch.addClass('gk-onfilter');
					checkToggleSearch.hide();

					self.addVar('addedCheckAll', checkToggle);

					addButton = $('#add-new-email')
						.gkButton({
							title: l('add'),
							name: 'add-new-email',
							className: 'add-new-email btn-margin',
							css: {
								'margin-right': '0',
							},
							click: function(event) {
								winAdd.show();
							},
							icon: '<i class="icon icon-plus-square"></i> ',
						});

					function resetWindow() {
						var inputs = dom.addNewEmailTemplate.find('input[name="firstname"], input[name="lastname"], input[name="email"]');

						if (inputs.length) {
							$.each(inputs, function(i, item){
								$(item).val('');
							});
						}
						dom.addNewEmailError.html('');
					}

					winAdd = new gkWindow({
						width: 400,
						title: l('add title'),
						className: 'add-new-email-window',
						show: function(win) {},
						close: function(win) {
							resetWindow();
						},
						content: function(win) {

							var addNewEmail = dom.addNewEmail,
								form = dom.addNewEmailForm,
								addNewEmailError = dom.addNewEmailError;

							addNewEmail.on('click', function(e) {	
								addedDataSource.create(form.getFormData()).done(function(response){
									if (!response.status) {
										if (response.errors.length) {
											addNewEmailError.html(response.errors[0]);
										}
									} else {

										// need to fix the selection lost when i add a new email to the list 
										addedDataSource.sync();
										addedDataSource.dataGrid.footer.setCheckedInfo(0);

										resetWindow();
										win.hide();
									}
								});
							});

							return dom.addNewEmailTemplate;
						}
					});

					var emptyList = $('#added-empty')
						.gkButton({
							title: l('empty list'),
							name: 'added-empty',
							className: 'added-empty btn-margin',
							css: {
								'padding-left': '10px',
								'padding-right': '10px',
							},
							click: function(event) {
								if (confirm(l('empty list confirm'))) {
									$.postAjax({submit: 'emptyAddedEmails'}).done(function(response){
										if (!response.status) {
											alert(l('empty list error'));
										} else {
											addedDataSource.sync();
										}
									});
								}
							},
							icon: '<i class="icon icon-trash-o"></i> ',
						});

					var floatRight = $('<div></div>');
					floatRight.css({
						'display': 'inline-block',
						'float': 'right',
					});
					floatRight.append(emptyList);
					floatRight.append(addButton);

					btnExportCsv = self.buildExportToCSVData(addedDataSource, NewsletterPro.dataStorage.get('csv_export_list_ref.LIST_ADDED'));

					btnExportCsv.css({
						'margin-right': '2px',
					});

					return makeRow([checkToggle, checkToggleSearch, floatRight, btnExportCsv]);
				}, 'prepend');

				var headerActions = addedGrid.addHeader(function(columns){
					var tr, 
						td, 
						searchDiv,
						timer = null, 
						searchText;

					function makeRow(arr) {
						tr = $('<tr></tr>');
						td = $('<th class="gk-header-datagrid added-header" colspan="'+columns+'"></th>');

						$.each(arr, function(i, item){
							td.append(item);
						});

						tr.html(td);

						return tr;
					}

					searchDiv = $('<div class="added-search-div" style="margin-right: 10px; float: left;"></div>');
					search = $('<input class="gk-input added-search" type="text">');
					searchLoading = $('<span class="added-search-loading" style="display: none;"></span>');

					search.on('keyup', function(event){
						var val = $.trim(search.val());

						if (val.length < 3) {
							checkToggle.show();
							checkToggleSearch.hide();

							addedDataSource.clearSearch();
							return true;
						} else {
							checkToggle.hide();
							checkToggleSearch.css({'display': 'inline-block'});
						}

						searchLoading.show();

						if ( timer != null ) clearTimeout(timer);

						timer = setTimeout(function(){

							addedDataSource.search(val).done(function(response){
								addedDataSource.applySearch(response);
								searchLoading.hide();
							});

						}, 300);

					});
					searchText = $('<span style="margin-right: 10px; float: left;">'+l('search')+':</span>');

					searchDiv.append(search);
					searchDiv.append(searchLoading);

					var filterLanguages = $('#gk-filter-languages').gkDropDownMenu({
						title: l('languages'),
						name: 'gk-filter-languages',
						css: {
							'float': 'left',
							'margin-right': '10px',
						},
						data: NewsletterPro.dataStorage.data.filter_languages,
						change: function(values) {
							appyFilters();
						},
					});

					var filterShops = $('#gk-filter-shops').gkDropDownMenu({
						title: l('shops'),
						name: 'gk-filter-shops',
						css: {
							'float': 'left',
							'margin-right': '10px',
						},
						data: NewsletterPro.dataStorage.data.filter_shops,
						change: function(values) {
							appyFilters();
						},
					});

					var filterCSVName = $('#gk-filter-csv-name').gkDropDownMenu({
						title: l('CSV Name'),
						name: 'gk-filter-csv-name',
						css: {
							'float': 'left',
							'margin-right': '10px',
						},
						data: NewsletterPro.dataStorage.get('csv_name'),
						change: function(values) {
							appyFilters();
						},
					});

					var filterSubscribed = $('#gk-filter-subscribed-add').gkDropDownMenu({
						title: l('subscribed'),
						name: 'gk-filter-gender',
						css: {
							'float': 'left',
							'margin-right': '10px',
						},

						data: [
							{'title': l('yes'), 'value': 1},
							{'title': l('no'), 'value': 0},
						],

						change: function(values) {
							appyFilters('subscribed');
						},
					});

					if (NewsletterPro.dataStorage.get('view_active_only'))
						filterSubscribed.hide();

					function getRangeSelection()
					{
						return {
							'min': (typeof sliderRange !== 'undefined' ? sliderRange.getValueMin() : 0) ,
							'max': (typeof sliderRange !== 'undefined' ? sliderRange.getValueMax() : 0) ,
						};
					}

					var winRangeSelection = new gkWindow({
						width: 640,
						height: 150,
						title: l('range selection'),
						className: 'range-selection-window',
						show: function(win) {
							if (typeof sliderRange !== 'undefined')
								sliderRange.refresh();
						},
						close: function(win) {},
						content: function(win, parent) 
						{
							addedDataSource.ready().done(function(){
								self.getRangeSelectionContent(function(content)
								{
									win.setContent(content);

									sliderRange = gkSliderRange({
										target: content.find('#slider-range-selection'),
										min : 1,
										max : addedCount(),
										valueMin : 1,
										valueMax : addedCount(),
										editable: true,
										values : [1, addedCount()],

										move: function(obj) {},
										done: function(obj) 
										{
											appyFilters('range');
										},
									});
								});
							});
							return '<span class="ajax-loader" style="margin-left: 310px; margin-top: 36px;"></span>';
						}
					});

					var rangeSelection = $('#range-selection-added')
					.gkButton({
						name: 'range-selection-added',
						title: l('range selection'),
						className: 'range-selection',
						css: {
							'padding-left': '10px',
							'padding-right': '10px',
							'float': 'left',
							'margin-right': '10px',
							'position': 'relative',
						},
						attr: {
							'data-checked': 0,
						},
						click: function(event) 
						{
							winRangeSelection.show();
						},
					});				

					function appyFilters(trigger) 
					{
						var filters = {
							shops: filterShops.getSelected(),
							subscribed: filterSubscribed.getSelected(),
							languages: filterLanguages.getSelected(),
							csv_name: filterCSVName.getSelected(),
						};

						var breakFilters = false;
						$.each(filters, function(i, filter){
							if (filter.length) {
								breakFilters = true;
							}
						});

						if (trigger == 'range')
						{
							filters['range_selection'] = getRangeSelection();

							if ($.trim(filters.range_selection.min) == 0 || $.trim(filters.range_selection.max) == 0 )
								delete filters['range_selection'];
							else
								breakFilters = true;
						}

						if (breakFilters) {
							search.val('');
							checkToggle.hide();
							checkToggleSearch.css({'display': 'inline-block'});

							addedDataSource.filter(filters).done(function(response){
								addedDataSource.applySearch(response);
								resetSliderRange(trigger, 1, response.length);

							});

						} else {
							resetSliderRange(trigger, 1, addedCount());

							checkToggle.show();
							checkToggleSearch.hide();
							addedDataSource.clearSearch();
						}
					}

					var filtersText = $('<span style="margin-left: 20px; margin-right: 10px; float: left; line-height: 28px;">'+l('filters')+'</span>');
					var clearFilters = $('#clear-filters')
							.gkButton({
								name: 'clear-filters',
								title: l('clear filters'),
								className: 'clear-filters',
								css: {
									'padding-left': '10px',
									'padding-right': '10px',
									'margin-left': '0',
									'margin-right': '0',
									'position': 'absolute',
									'right': '5px',
								},
								attr: {
									'data-checked': 0,
								},

								click: function(event) {

									search.val('');
									filterShops.uncheckAll();
									filterCSVName.uncheckAll();
									filterSubscribed.uncheckAll();
									filterLanguages.uncheckAll();

									clearRangeSelection();

									checkToggle.show();
									checkToggleSearch.hide();
									addedDataSource.clearSearch();
								},
								icon: '<i class="icon icon-times"></i> ',
							});

					self.reset = self.reset || {};
					self.reset.added = {
						filterLanguages: filterLanguages,
						filterShops: filterShops,
						filterCSVName: filterCSVName,
						filterSubscribed: filterSubscribed,
						rangeSelection: rangeSelection,
						clearFilters: clearFilters,
					}
	
					self.addVar('filterAdded', {
						'languages': filterLanguages,
						'shops': filterShops,
						'csv_name': filterCSVName,
						'subscribed': filterSubscribed,
						'range': rangeSelection,
						'clear': clearFilters,
					});

					self.addVar('applyFilerAddedCallback', appyFilters);

					self.addVar('added', addedDataSource);

					return makeRow([searchText, searchDiv, filtersText , filterLanguages ,filterShops, filterCSVName, filterSubscribed, rangeSelection, clearFilters]);
				}, 'prepend');

				self.resetAddedButton();
		});

	},

	getSelectOptions: function(opt) 
	{
		opt = $.extend(true, {}, opt);
		var options = $.map(opt, function(option){
			var obj = {name: option.name, value: option.id_newsletter_pro_smtp, data: option};
			if (option.hasOwnProperty('selected')) {
				delete option['selected'];
				obj['selected'] = true;
			}
			return obj;
		});
		return options;
	},
}.init(NewsletterPro));
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

NewsletterPro.namespace('modules.sendManager');
NewsletterPro.modules.sendManager = ({
	define: {
		STATE_DEFAULT: 0,
		STATE_PAUSE: 1,
		STATE_IN_PROGRESS: 2,
		STATE_DONE: 3,
	},
	dom: null,
	box: null,
	vars: {},
	events: {},
	showErrorTimer: null,
	init: function(box) 
	{
		var self = this,
			l,
			controllers,
			syncNewsletters,
			emailsToSend,
			emailsSent,
			sendProgressbar,
			stopWasHit = false,
			startWasHit = false;

		this.ready(function(dom){
			var sleep = Number(box.dataStorage.get('email_sleep')) * 1000,
				defaultRefreshRate = 3000,
				refreshRate = (sleep > defaultRefreshRate ? sleep : defaultRefreshRate);

			l = self.l = box.translations.l(box.translations.modules.sendManager);
			controllers = NewsletterProControllers;

			syncNewsletters = new box.components.SyncNewsletters({
				syncErrorsLimit: 250,

				connection: {
					url: box.getUrl({'submit':'syncNewsletters'}),
					limit: 250, // 250 should be the default value
					data: {},
					refreshRate: refreshRate,
				},
				subscription: {
					ready: ['emailsToSend', 'emailsSent', 'progressbar']
				}
			});

			self.addVar('syncNewsletters', syncNewsletters);

			emailsToSend = new box.components.EmailsToSend({
				selector: dom.emailsToSend,
				fastPerformance: true,
				subscription: [
					[syncNewsletters, 'emailsToSend', 'sync']
				],
			});

			self.addVar('emailsToSend', emailsToSend);

			emailsSent = new box.components.EmailsSent({
				selector: dom.emailsSent,
				fastPerformance: true,
				subscription: [
					[syncNewsletters, 'emailsSent', 'sync']
				],
			});

			self.addVar('emailsSent', emailsSent);

			sendProgressbar = new box.components.SendProgressbar({
				selector: $('#send-progressbar'),
				subscription: [
					[syncNewsletters, 'progressbar', 'sync']
				]
			});

			self.addVar('sendProgressbar', sendProgressbar);

			syncNewsletters.subscribe('syncStart', function(response){
				stateSyncStart();
			});

			syncNewsletters.subscribe('syncPause', function(response){
				stateSyncPause();
			});

			syncNewsletters.subscribe('syncDone', function(response){
				stateSyncDone();
				box.modules.task.ui.components.sendHistory.sync();
			});

			syncNewsletters.subscribe('syncContinue', function(response){

			});

			syncNewsletters.subscribe('syncSuccess', function(response) {

			});

			syncNewsletters.subscribe('syncError', function(response){
				var message = response.message,
					alertErrors = response.alertErrors,
					display = response.display;

				if (display)
					self.showError(message, 10000); // 10000
				else if (alertErrors)
				{
					self.showError(message, 0);
					box.alertErrors(message);
				}
			});

			syncNewsletters.subscribe('syncEnd', function(response){
				if (stopWasHit)
				{
					stopWasHit = false;
					clearProgress();
				}

			});

			syncNewsletters.subscribe('beforeRequest', function(){
				if (startWasHit)
				{
					syncNewsletters.setData('getLastId', true);
					startWasHit = false;
				}
				else
					syncNewsletters.setData('getLastId', false);
			});


			emailsSent.subscribe('lastItemCreated', function(item){

			});

			emailsSent.subscribe('firstItemCreated', function(item){
				if (item && item.errors.length)
					self.showError(item.errors, 7000);
			});

			dom.startSendNewsletters.on('click', function(){
				clearProgress();

				var result = controllers.SendController.prepareEmails();

				if (typeof result === 'undefined')
					return;

				var buttonsState = getButtonsState();
				stateSyncStart();

				result.done(function(response){
					if (!response.status)
						restoreButtonsState(buttonsState);
					else
					{
						startWasHit = true;
					}
				}).fail(function(){
					restoreButtonsState(buttonsState);
				});
			});

			dom.pauseSendNewsletters.on('click', function(){
				var buttonsState = getButtonsState();

				stateSyncPause();
				self.pauseSendNewsletters().done(function(response){
					if (!response.success)
						restoreButtonsState(buttonsState);
				}).fail(function(){
					restoreButtonsState(buttonsState);
				});
			});

			dom.stopSendNewsletters.on('click', function(){
				var buttonsState = getButtonsState();

				stateSyncDone();
				self.stopSendNewsletters().done(function(response){
					if (!response.success)
						restoreButtonsState(buttonsState);
					else
					{
						stopWasHit = true;
						clearProgress();
						box.modules.task.ui.components.sendHistory.sync();
					}
				}).fail(function(){
					restoreButtonsState(buttonsState);
				});
			});

			dom.continueSendNewsletters.on('click', function(){
				var buttonsState = getButtonsState();

				stateSyncStart();

				self.continueSendNewsletters(true).done(function(response){
					if (!response.success)
						restoreButtonsState(buttonsState);
				}).fail(function(){
					restoreButtonsState(buttonsState);
				});
			});

			function clearProgress()
			{
				emailsToSend.clear();
				emailsSent.clear();
				sendProgressbar.clear();
				self.hideError();
				self.step3(false);
			}

			function stateSyncStart()
			{
				sendProgressbar.setPause(false);

				dom.pauseSendNewsletters.show();
				dom.stopSendNewsletters.show();

				dom.continueSendNewsletters.hide();
				dom.startSendNewsletters.hide();

				dom.newTask.hide();
				dom.prevStep.hide();
				self.step3(true);
			}

			function stateSyncPause()
			{
				sendProgressbar.setPause(true);

				dom.continueSendNewsletters.show();
				dom.stopSendNewsletters.show();

				dom.pauseSendNewsletters.hide();
				dom.startSendNewsletters.hide();

				dom.newTask.hide();
				dom.prevStep.hide();
				self.step3(false);
			}

			function stateSyncDone()
			{
				sendProgressbar.setPause(false);

				dom.startSendNewsletters.show();
				dom.newTask.show();
				dom.prevStep.show();

				dom.stopSendNewsletters.hide();
				dom.pauseSendNewsletters.hide();
				dom.continueSendNewsletters.hide();
				self.step3(false);

			}

			function getButtonsState()
			{
				return {
					startSendNewsletters: dom.startSendNewsletters.is(':visible'),
					stopSendNewsletters: dom.stopSendNewsletters.is(':visible'),
					pauseSendNewsletters: dom.pauseSendNewsletters.is(':visible'),
					continueSendNewsletters: dom.continueSendNewsletters.is(':visible'),
					prevStep: dom.prevStep.is(':visible'),
					newTask: dom.newTask.is(':visible'),
				}
			}

			function restoreButtonsState(buttonsState)
			{
				for(var key in buttonsState)
				{
					var visible = buttonsState[key];
					if (visible)
						dom[key].show();
					else
						dom[key].hide();
				}
			}
		});

		return self;
	},

	requireConnection: function(response)
	{
		var self = this;
		if (response.hasOwnProperty('require_connection') && response.require_connection)
		{
			setTimeout(function(){
				self.continueSendNewsletters();
			}, 2500);
			return true;
		}
		return false;
	},

	step3: function(bool)
	{
		var box = NewsletterPro,
			self = this;

		if (bool)
			self.dom.step3.html('<i class="icon icon-refresh icon-spin"></i>');
		else
			self.dom.step3.html('3');
	},

	connectionAvailable: function(func)
	{
		$.postAjax({'submit': 'connectionAvailable'}, 'json', false).done(function(bool){
			func(bool);
		});
	},

	startSendNewsletters: function(trigger)
	{
		var box = NewsletterPro,
			self = this;

		trigger = typeof trigger === 'undefined' ? 0 : Number(trigger);

		self.vars.syncNewsletters.sync();

		return $.postAjax({'submit': 'startSendNewsletters', 'trigger': trigger}, 'json', false).done(function(response){

			if (!response.success)
				self.showError(response.errors);

			self.requireConnection(response);

		}).fail(function(jqXHR, textStatus, errorThrown){
			self.showError(self.l('Error') + ' : ' + box.getXHRError(jqXHR));
		}).promise();
	},
	
	continueSendNewsletters: function(trigger)
	{
		var box = NewsletterPro,
			self = this;

		trigger = typeof trigger === 'undefined' ? 0 : Number(trigger);

		self.vars.syncNewsletters.sync();

		return $.postAjax({'submit': 'continueSendNewsletters', 'trigger': trigger}, 'json', false).done(function(response){

			if (!response.success)
				self.showError(response.errors);

			self.requireConnection(response);

		}).fail(function(jqXHR, textStatus, errorThrown){
			self.showError(self.l('Error') + ' : ' + box.getXHRError(jqXHR));
		}).promise();
	},

	pauseSendNewsletters: function()
	{
		var box = NewsletterPro,
			self = this;

		return $.postAjax({'submit': 'pauseSendNewsletters'}).done(function(response){

			if (!response.success)
				box.alertErrors(response.errors);
		}).promise();
	},

	stopSendNewsletters: function()
	{
		var box = NewsletterPro,
			self = this;

		return $.postAjax({'submit': 'stopSendNewsletters'}).done(function(response){
			if (!response.success)
				box.alertErrors(response.errors);
		}).promise();
	},

	/**
	 * Show an error on the screen
	 * @param  {[string]} msg       
	 * @param  {[int]} displayTime Time in miliseconds
	 */
	showError: function(msg, displayTime)
	{
		var self = this,
			dom = self.dom;

		if (typeof msg !== 'string')
			msg = msg.join('<br>');

		dom.lastSendErrorDiv.show();
		dom.lastSendError.show().html(msg);

		if (typeof displayTime !== 'undefined')
		{
			if (self.showErrorTimer != null)
				clearTimeout(self.showErrorTimer);

			if (displayTime === 0)
				return;

			self.showErrorTimer = setTimeout(function(){
				self.hideError();
			}, displayTime);
		}
	},

	hideError: function()
	{
		var self = this,
			dom = self.dom;

		dom.lastSendErrorDiv.hide();
		dom.lastSendError.hide().html('');
	},

	addEvent: function(name, value) {
		this.events[name] = value;
	},

	triggerEvent: function(name) {
		if (this.events.hasOwnProperty(name) && typeof this.events[name] == 'function')
			this.events[name]();
	},

	addVar: function(name, value) {
		this.vars = this.vars || {};
		this.vars[name] = value;
	},

	ready: function(func) 
	{
		var self = this;
		$(document).ready(function(){

			self.dom = self.dom || {
				startSendNewsletters: $('#send-newsletters'),
				stopSendNewsletters: $('#stop-send-newsletters'),
				pauseSendNewsletters: $('#pause-send-newsletters'),
				continueSendNewsletters: $('#continue-send-newsletters'),
				prevStep: $('#previous-send-newsletters'),
				newTask: $('#new-task'),

				emailsToSend: $('#emails-to-send'),
				emailsSent: $('#emails-sent'),

				lastSendErrorDiv: $('#last-send-error-div'),
				lastSendError: $('#last-send-error'),
				step3: $('#np-step-3')
			};

			func(self.dom);
		});
	},

}.init(NewsletterPro));
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

NewsletterPro.namespace('modules.smtp');
NewsletterPro.modules.smtp = ({
	dom: null,
	init: function(box) {
		var self = this;

		self.ready(function(dom) {
			var taskList,
				smtpOptions;

			domSettings();

			function domSettings()
			{
				if (isSmtpActive())
					showSMTP();
				else
					hideSMTP();
			}

			function hideSMTP()
			{
				if (dom.smptConfigBox.is(':visible'))
					dom.smptConfigBox.slideUp('slow');
			}

			function showSMTP()
			{
				if (!dom.smptConfigBox.is(':visible'))
					dom.smptConfigBox.slideDown('slow');
			}

			function isSmtpActive()
			{
				return Boolean(box.dataStorage.data.smtp_active);
			}

			function setListUnsubscribeSwitcher(value)
			{	 
				if (value) {
					dom.listUnsubscribeEmailBox.show();
					$('input[name="list_unsubscribe_active"][value="1"]').prop('checked', true);
					$('input[name="list_unsubscribe_active"][value="0"]').prop('checked', false);
				} else {
					dom.listUnsubscribeEmailBox.hide();
					$('input[name="list_unsubscribe_active"][value="0"]').prop('checked', true);
					$('input[name="list_unsubscribe_active"][value="1"]').prop('checked', false);
				}

			}

			function updateFields(data) 
			{
				var listUnsubscribeActive = Number(data.list_unsubscribe_active);

				if (listUnsubscribeActive) {
					setListUnsubscribeSwitcher(1);
				} else {
					setListUnsubscribeSwitcher(0);
				}

				dom.listUnsubscribeEmail.val(data.list_unsubscribe_email);

				$('#smtpForm [name=method]').prop('checked', false);

				if (data.method == 1)
					dom.smtpMethodMail.prop('checked', true);
				else
					dom.smtpMethodSmtp.prop('checked', true);
				
				hideFieldsByMethod(data.method);

				dom.smptId.val(data.id_newsletter_pro_smtp);
				dom.smtpName.val(data.name);

				dom.smtpFromName.val(data.from_name);
				dom.smtpFromEmail.val(data.from_email);
				dom.smtpFromReplyTo.val(data.reply_to);

				dom.smtpDomain.val(data.domain);
				dom.smtpServer.val(data.server);
				dom.smtpUser.val(data.user);
				dom.smtpPasswd.val(data.passwd);
				dom.smtpEncryption.val(data.encryption);
				dom.smtpPort.val(data.port);
			}

			function emptyFields() 
			{
				setListUnsubscribeSwitcher(0);

				dom.listUnsubscribeEmail.val('');

				dom.smptId.val('0');
				dom.smtpName.val('');

				dom.smtpFromName.val('');
				dom.smtpFromEmail.val('');
				dom.smtpFromReplyTo.val('');

				dom.smtpDomain.val('');
				dom.smtpServer.val('');
				dom.smtpUser.val('');
				dom.smtpPasswd.val('');
				dom.smtpEncryption.val('');
				dom.smtpPort.val('');
			}

			$('input[name="list_unsubscribe_active"]').on('change', function() {
				var val = Number($(this).val());
				if (val) {
					dom.listUnsubscribeEmailBox.slideDown();
				} else {
					dom.listUnsubscribeEmailBox.slideUp();
				}
			});

			var options = $.map(NewsletterPro.dataStorage.get('all_smtp'), function(option){
				var obj = {name: option.name, value: option.id_newsletter_pro_smtp, data: option};

				if (option.hasOwnProperty('selected')) {
					delete option['selected'];
					obj['selected'] = true;

					if (dom.smtpActive.is(':checked')) {
						var opt = $.extend(true, {}, option);

						opt.passwd = '';
						updateFields(opt);
					}
				}
				return obj;
			});

			if (options.length > 0) {
				dom.saveSmtp.show();
				dom.deleteSmtp.show();
			} else {
				dom.saveSmtp.hide();
				dom.deleteSmtp.hide();
			}

			var ui = self.ui;
			var select = ui.SelectOption({
					name: 'smptSelect',
					template: dom.selectSmtp,
					className: 'gk-smtp-select',
					options: options,
					onChange: function(select) 
					{
						var selected = select.getSelected();
						if (selected != null) 
						{
							var data = selected.data,
								val = Number(selected.data.id_newsletter_pro_smtp),
								opt = $.extend(true, {}, data);

							hideFieldsByMethod(data.method);

							opt.passwd = '';
							updateFields(opt);

							$.postAjax({'submit': 'changeSMTP', changeSMTP: val}).done(function(response){
								if (!response.status)
									dom.smtpMessage.empty().show().append('<div class="alert alert-danger">'+response.msg+'</div>');

								setTimeout( function() { dom.smtpMessage.hide(); }, 5000);
							});
						}

					}
				});

			var saveSmtp = ui.Button({
				name: 'saveSmtp',
				template: dom.saveSmtp,
				click: function() {

					dom.saveSmtpMessage.hide();

					$.submitAjax( {'submit': 'saveSMTP', name: 'saveSMTP', form: dom.smtpForm} ).done(function(response) {
						taskList = taskList || NewsletterPro.modules.task.ui.components.taskList;
						smtpOptions = smtpOptions || NewsletterPro.dataStorage;
						if ( response.status ) {
							var obj = response.obj;

							select.updateOption(obj);

							dom.saveSmtpSuccess.empty().show().append('<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>');
						} else {
							if (response.errors.length > 0)
								dom.saveSmtpMessage.empty().show().append(response.errors.join('<br />'));
							else
								dom.saveSmtpSuccess.empty().show().append('<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>');
						}
						$.postAjax({'submit': 'getAllSMTPJson', getAllSMTPJson: true}).done(function(res){

							smtpOptions.add('all_smtp', res);
							taskList.sync();
						});
						setTimeout( function() { dom.saveSmtpSuccess.hide(); }, 5000);
						setTimeout( function() { dom.saveSmtpMessage.hide(); }, 5000);
					});

				}
			});

			var addSmtp = ui.Button({
				name: 'addSmtp',
				template: dom.addSmtp,
				click: function() {
					var that = addSmtp;

					$.submitAjax({'submit': 'addSMTP', name: 'addSMTP', form: dom.smtpForm}).done(function(response){
						taskList = taskList || NewsletterPro.modules.task.ui.components.taskList;
						smtpOptions = smtpOptions || NewsletterPro.dataStorage;
						if (response.status) 
						{

							var obj = response.obj;
							select.addOption({name: obj.name, value: obj.name, data: obj, selected: true});
							dom.smptId.val(obj.id_newsletter_pro_smtp);
							dom.saveSmtpSuccess.empty().show().append('<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>');

							var opt = select.getOptions();

							if (opt.length > 0) {
								dom.saveSmtp.show();
								dom.deleteSmtp.show();
							}

						} 
						else
							dom.saveSmtpMessage.empty().show().append(response.errors.join('<br />'));

						$.postAjax({'submit': 'getAllSMTPJson', getAllSMTPJson: true}).done(function(res){
							smtpOptions.add('all_smtp', res);
							taskList.sync();
						});

						setTimeout( function() { dom.saveSmtpSuccess.hide(); }, 5000);
						setTimeout( function() { dom.saveSmtpMessage.hide(); }, 15000);
					});
				}
			});

			var deleteSmtp = ui.Button({
				name: 'deleteSmtp',
				template: dom.deleteSmtp,
				click: function() {
					var selected = select.getSelected();

					if (selected != null)
					{
						var id = selected.data.id_newsletter_pro_smtp;

						$.postAjax( {'submit': 'deleteSMTP', deleteSMTP: id} ).done(function(response) {

							if (response.hasOwnProperty('demo_mode') && response.demo_mode == true && response.errors.length > 0) {
								NewsletterPro.alertErrors(response.errors);
								return;
							}

							taskList = taskList || NewsletterPro.modules.task.ui.components.taskList;
							smtpOptions = smtpOptions || NewsletterPro.dataStorage;
							if (response.status) 
							{
								selected.destroy();

								var sel = select.getSelected();

								if (sel != null) {
									var obj = $.extend(true, {}, sel.data),
										val = obj.id_newsletter_pro_smtp;

									obj.passwd = '';
									dom.smptId.val(obj.id_newsletter_pro_smtp);

									$.postAjax({'submit': 'changeSMTP', changeSMTP: val}).done(function(res){
										if (!res.status)
											dom.smtpMessage.empty().show().append('<span class="error-msg">'+res.msg+'</span>');
										setTimeout( function() { dom.smtpMessage.hide(); }, 5000);
									});

									updateFields(obj);
								} else {
									emptyFields();

									dom.saveSmtp.hide();
									dom.deleteSmtp.hide();
								}
							} 
							else 
								box.alertErrors(response.errors);

							$.postAjax({'submit': 'getAllSMTPJson', getAllSMTPJson: true}).done(function(res){
								smtpOptions.add('all_smtp', res);
								taskList.sync();
							});
						});
					}
				}
			});

			dom.smtpActive.on('change', function(){
				if( $(this).is(':checked') ) 
				{
					$.postAjax({'submit': 'smtpActive', smtpActive: true}).done(function(response) {
						if( response.status == true ) 
						{
							select.enable();

							var selected = select.getSelected();

							if (selected != null)
							{
								var data = selected.data,
									opt = $.extend(true, {}, data);

								opt.passwd = '';

								updateFields(opt);
							}
							showSMTP();
						}
					});
				} 
				else 
				{
					$.postAjax({'submit': 'smtpActive', smtpActive: false}).done(function(response) {
						if( response.status == true ) 
						{
							select.disable();
							emptyFields();
							hideSMTP();
						}
					});
				}
			});

			dom.smtpTestInput.on('blur', function(){
				box.dataStorage.set('configuration.PS_SHOP_EMAIL', $(this).val());
			});

			var smtpTestButton = ui.Button({
				name: 'smtpTestButton',
				template: dom.smtpTestButton,
				click: function() 
				{
					var that = this,
						message = dom.smtpTestMessage,
						success = dom.smtpTestSuccess,
						email = dom.smtpTestInput.val(),
						buttonElement = $(this);

					success.hide(); 
					message.hide();
					
					box.showAjaxLoader(buttonElement);

					$.postAjax({ 'submit': 'sendMailTest', sendMailTest: email }).done(function( data ) {
						if( data.status )
							success.empty().show().append('<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>');
						else
							message.empty().show().append('<div class="alert alert-danger">' + data.msg + '</div>');

						setTimeout( function() { 
							success.hide(); 
							message.hide(); 
						}, 10000);
					}).always(function(){
						box.hideAjaxLoader(buttonElement);
					});
				}
			});

			$('#smtpForm [name="method"]').on('change', function(){
				hideFieldsByMethod($(this).val());
			});

			function hideFieldsByMethod(method)
			{
				switch(method)
				{
					// mail method
					case '1':
						dom.smtpOnly.slideUp();
					break;

					// smtp method
					case '2':
						dom.smtpOnly.slideDown();
					break;
				}
			}

		});
		return self;
	},

	ready: function(func) {
		var self = this;
		$(document).ready(function(){
			self.dom = {
				smtpOnly: $('#smtp-only'),
				selectSmtp: $('#select-smtp'),

				smtpActive: $('#smtp-active'),
				smtpFromName: $('#smtp-from-name'),
				smtpFromEmail: $('#smtp-from-email'),
				smtpFromReplyTo: $('#smtp-reply-to'),
				smtpMethodSmtp: $('#method-smtp'),
				smtpMethodMail: $('#method-mail'),

				smtpName: $('#smtp-name'),
				smtpDomain: $('#smtp-domain'),
				smtpServer: $('#smtp-server'),
				smtpUser: $('#smtp-user'),
				smtpPasswd: $('#smtp-passwd'),
				smtpEncryption: $('#smtp-encryption'),
				smtpPort: $('#smtp-port'),

				saveSmtp: $('#save-smtp'),
				addSmtp: $('#add-smtp'),
				deleteSmtp: $('#delete-smtp'),

				smtpMessage: $('#change-smtp-message'),
				saveSmtpMessage: $('#save-smtp-message'),
				saveSmtpSuccess: $('#save-smtp-success'),

				smtpForm: $('#smtpForm'),

				smtpTestInput: $('#smtp-test-email'),
				smtpTestMessage: $('#smtp-test-email-message'),
				smtpTestSuccess: $('#smtp-test-email-success'),
				smtpTestButton: $('#smtp-test-email-button'),

				listUnsubscribeEmailBox: $('#smtp-list-unsubscribe-email-box'),
				listUnsubscribeEmail: $('#smtp-list-unsubscribe-email'),

				smptId: $('#smpt-id'),

				smptConfigBox: $('#smpt-config-box'),
			};
			func(self.dom);
		});
	},

	each: function(array, func) {
		for (var name in array)
			func(array[name], name);
	},

	ui: ({
		components: {},
		init: function() {
			return this;
		},

		add: function(name, value) {
			this.components[name] = value;
		},

		SelectOption: function SelectOption(cfg) {
			if (!(this instanceof SelectOption))
				return new SelectOption(cfg);
			var main = NewsletterPro.modules.smtp,
				ui = NewsletterPro.modules.smtp.ui,
				self = this,
				name = cfg.name,
				template = cfg.template,
				className = cfg.className || '',
				options = cfg.options || [],
				change = cfg.onChange || null,
				sameAs = cfg.sameAs || null,
				selected;

			self.name = name;
			ui.add(name, self);

			function setTemplate(template) {
				template = $(template);
				self.template = template;

				template.attr({
					'autocomplete': 'off',
				});

				template.addClass('gk-select');
				template.addClass(className);

				template.options = [];
				main.each(options, function(opt_data) {
					addOption(opt_data);
				});

				addEvents(template);
			}

			function addEvents(template) {
				template.on('change', function(event){
					self.onChange.call(template, self);
				});
			}

			function addOption(opt_data) {
				self.template.show();

				var option = $('<option value="'+opt_data.value+'">'+opt_data.name+'</option>'),
					data = opt_data.data || {};

				if (opt_data.hasOwnProperty('selected') && opt_data.selected) {
					option.prop('selected', true);
					self.selected = option;
				}

				option.data = data;
				option.dataInit = opt_data;
				self.template.options.push(option);
				self.template.append(option);

				option.destroy = function() {
					var index = self.template.options.indexOf(this);
					if (index > -1)
						self.template.options.splice(index, 1);

					this.remove();

					var options = self.getOptions();

					if (options.length > 0)
						self.setSelected(options[0]);
					else {
						self.hide();
						self.selected = null;
					}
				}
			}

			function getInstanceByName(name) {
				var components = main.ui.components,
					name,
					component;

				for (name in components) {
					component = components[name];
					if (component.hasOwnProperty('name') && component.name === sameAs)
						return component;
				}
				return false;
			}

			setTemplate(template);

			self.addOption = function(opt_data) {
				addOption(opt_data);
			};

			self.updateOption = function(data) {
				var selected = self.getSelected();
				if (selected != null) {
					selected.data = data;
					selected.text(data.name);
					selected.val(data.name);
				}
			};

			self.getData = function() {
				var data = [];
				main.each(self.template.options, function(option){
					data.push(option.data);
				});
				return data;
			};

			self.getOptions = function() {
				var data = [];
				main.each(self.template.options, function(option){
					data.push(option);
				});
				return data;
			};

			self.getSelected = function() {
				return self.selected;
			};

			self.setSelected = function(value) {
				value['selected'] = true;
				value.prop('selected', true);
				self.selected = value;
			};

			self.onChange = function() {
				var options = self.getOptions(),
					value = this.val();

				var match = $.grep(options, function(item) {
					return item.dataInit.value === value;
				});

				var selected = self.getSelected();
				if (selected != null)
					delete selected.data['selected'];

				if (match.length > 0)
					self.setSelected(match[0]);

				if (typeof change === 'function')
					change.call(this, self);
			};

			self.disable = function() {
				self.template.prop('disabled', true);
			};

			self.enable = function() {
				self.template.prop('disabled', false);
			};

			self.hide = function() {
				self.template.hide();
			};

			self.show = function() {
				self.template.show();
			};

			self.refresh = function(opt_data) {

				var options = self.getOptions()
					selected = false;

				main.each(opt_data, function(opt) {
					if (opt.hasOwnProperty('selected') && opt.selected == true) {
						selected = true;
					}
				});

				main.each(options, function(option) {
					option.destroy();
				});

				main.each(opt_data, function(opt) {
					if (!selected) {
						opt['selected'] = true;
						selected = false;
					}
					addOption(opt);
				});
			};



			return self;
		}, // end of SelectOption

		Button: function Button(cfg) {
			if (!(this instanceof Button))
				return new Button(cfg);
			var main = NewsletterPro.modules.smtp,
				ui = NewsletterPro.modules.smtp.ui,
				self = this,
				template = cfg.template,
				name = cfg.name;

			function setTemplate(template) {
				template = $(template);

				addEvents(template);
				self.template = template;
			}

			function addEvents(template) {
				template.on('click', function() {
					self.click.call(template, self);
				});
			}

			setTemplate(template);

			self.click = function() {
				if (typeof cfg.click === 'function')
					cfg.click.call(this, self);
			};

			ui.add(name, self);
			return self;
		}, // end of SelectOption

	}.init()),

}.init(NewsletterPro));
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

NewsletterPro.namespace('modules.csv');
NewsletterPro.modules.csv = ({
	dom: null,
	init: function(box) {
		var self = this;

		self.ready(function(dom) {
			dom.nextStep.on('click', function(){
				var listRef = Number($('[name="exportEmailAddresses"]:checked').val());

				$.postAjax({'submit': 'getExportOptions', value: listRef }).done(function(response){
					if (response.success)
					{
						var columns = response.columns;
						
						buildTable(columns);

						dom.listRef.val(listRef);

						showDetails();
					}
					else
						box.alertErrors(response.errors);
				});
			});

			dom.btnBack.on('click', function(){
				hideDetails();
			});

			dom.btnExport.on('click', function(){
				dom.form.submit();
			});

			dom.checkAll.on('click', function(){
				$.each($('[name="export_csv_selected_columns[]"]'), function(i, item){
					$(item).prop('checked', true);
				});
			});

		});

		function showDetails()
		{
			self.dom.container.hide();
			self.dom.exportDetails.show();
		}

		function hideDetails()
		{
			self.dom.container.show();
			self.dom.exportDetails.hide();
		}

		function buildTable(columns)
		{
			self.dom.exportOptions.empty();

			var theadTemplate = '';
			for(var i = 0; i < columns.length; i++)
			{
				var column = columns[i];

				theadTemplate += '\
					<div class="checkbox">\
						<label class="control-label  in-win">\
							<input type="checkbox" name="export_csv_selected_columns[]" value="'+column+'">\
							'+formatColumn(column)+'\
						</label>\
					</div>';
			}

			self.dom.exportOptions.html(theadTemplate);
		}

		function formatColumn(name)
		{
			var exp = name.split('_');
			for(var i = 0; i < exp.length; i++)
			{
				var first = exp[i][0],
					rest = exp[i].slice(1);

				exp[i] = first.toUpperCase() + rest;
			}

			return exp.join(' ');
		}

		return self;
	},

	ready: function(func) {
		var self = this;
		$(document).ready(function(){
			self.dom = {
				btnBack: $('#np-export-email-addresses-back'),
				nextStep: $('#btn-export-email-addresses'),
				container: $('#import-export-container'),
				exportDetails: $('#export-details'),
				exportOptions: $('#np-export-email-options'),
				btnExport: $('#btn-export-csv'),
				form: $('#export-csv-form'),
				listRef: $('#export-csv-list-ref'),
				checkAll: $('#btn-export-csv-checkall'),
			};
			func(self.dom);
		});
	},
}.init(NewsletterPro));
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

NewsletterPro.namespace('modules.mailChimp');
NewsletterPro.modules.mailChimp = ({
	dom: null,
	box: null,
	init: function(box) {
		var self = this,
			dataStorage,
			chimpConfig,
			chimpSyncProgress,
			syncRefreshRate = 5000,
			l,
			exportTemplateWinDom = {},
			exportTemplateWin;

		self.box = box;

		function installChimp(api_key, list_id)
		{
			var dom = self.dom;

			addLoading(dom.installLoading);
			return $.postAjax({
				'chimp': 'installChimp', 
				'api_key': api_key,
				'list_id': list_id,
			}).always(function(){
				removeLoading(dom.installLoading);
			}).done(function(response){
				if (response.status) {
					installedChimpSettings();
					alert(response.message);
				} else {
					box.alertErrors(response.errors);
				}
			});
		}

		function uninstallChimp()
		{
			if (confirm(l('confirm uninstall chimp')))
			{
				var dom = self.dom;

				addLoading(dom.uninstallLoading);
				return $.postAjax({'chimp': 'uninstallChimp'})
					.always(function(){
						removeLoading(dom.uninstallLoading);
					}).done(function(response){
						if(response.status) {
							dom.inputChimpApiKey.val(''),
							dom.inputChimpListId.val('');
							uninstalledChimpSettings();
							alert(response.message);
						} else {
							box.alertErrors(response.errors);
						}
					});
			}
			return false;
		}

		function addLoading(element)
		{
			if (!element.hasClass('ajax-loader'))
				element.addClass('ajax-loader');
		}

		function removeLoading(element)
		{
			element.removeClass('ajax-loader');
		}

		function pingChimp()
		{
			return $.postAjax({'chimp': 'pingChimp'}).done(function(response){
				if (response.status) {
					alert(response.message);
				} else {
					box.alertErrors(response.errors);
				}
			});
		}

		function showChimpMenu()
		{

		}

		function showInstallButton()
		{
			var dom = self.dom;
			dom.btnUninstallChimp.hide();
			dom.btnInstallChimp.show();
			dom.chimpMenu.slideUp();
		}

		function showUninstallButton()
		{
			var dom = self.dom;
			dom.btnUninstallChimp.show();
			dom.btnInstallChimp.hide();
			dom.chimpMenu.slideDown();
		}

		function isChecked(checkbox)
		{
			if (checkbox.is(':checked'))
				return 1;
			return 0;
		}

		function getConfig(name) 
		{
			if (chimpConfig.hasOwnProperty(name))
				return chimpConfig[name];
			return false;
		}

		function configExists(name) 
		{
			if (chimpConfig.hasOwnProperty(name))
				return true;
			return false;
		}

		function updateSyncCheckbox(name, value, doneCallback)
		{
			value = ( value ? 1 : 0 );
			$.postAjax({'chimp': 'updateSyncCheckbox', 'name': name, 'value': value }).done(function(response){
				if (typeof doneCallback === 'function')
					doneCallback(response);

				if (!response.status)
					box.alertErrors(response.errors);
				else
				{
					box.dataStorage.data.chimp_config[name] = value;
				}
			});
		}

		function writeProgress(instance, users)
		{
			instance.total.html(users.total);
			instance.created.html(users.created);
			instance.updated.html(users.updates);
			instance.errors.html(users.errors);

			if (!users.done)
			{
				if (!isVisible(instance.box))
					instance.box.slideDown();

				if (users.in_progress)
					instance.ajaxLoader.show();
			}
			else
			{
				hideProgress(instance);
			}
		}

		function resetProgress(instance)
		{
			if (isVisible(instance.box))
			{
				instance.box.slideUp('slow', function(){
					instance.total.html(0);
					instance.created.html(0);
					instance.updated.html(0);
					instance.errors.html(0);
					instance.ajaxLoader.hide();
				});
			}
			else
			{
				instance.total.html(0);
				instance.created.html(0);
				instance.updated.html(0);
				instance.errors.html(0);
				instance.ajaxLoader.hide();
			}
		}

		function isVisible(box)
		{
			if (box.is(':visible'))
				return true;
			return false;
		}

		function hideProgress(instance)
		{
			var dfd = new $.Deferred();
			if(isVisible(instance.box))
			{
				instance.ajaxLoader.hide();
				setTimeout(function(){
					dfd.resolve();
					resetProgress(instance);
				}, 15000);
			}
			return dfd.promise();
		}

		function resetAllProgress()
		{
			hideProgress(self.dom.objSyncCustomersProgress);
			hideProgress(self.dom.objSyncVisitorsProgress);
			hideProgress(self.dom.objSyncAddedProgress);
			hideProgress(self.dom.objSyncOrdersProgress);
		}

		function stopSync()
		{
			return $.postAjax({'chimp': 'stopSync'}).done(function(response) {
				if (response.status)
				{
					resetAllProgress();
					hideBox();
					showSyncListButton();
				}
				else
					box.alertErrors(response.errors);
			});
		}

		function checkStatus(response)
		{
			var chimpSync = response.chimp_sync,
				added,
				visitors,
				customers;

			if (chimpSync.hasOwnProperty('ADDED_CHECKBOX'))
			{
				users = chimpSync.ADDED_CHECKBOX;
				writeProgress(self.dom.objSyncAddedProgress, users);
			}
			else
				hideProgress(self.dom.objSyncAddedProgress);

			if (chimpSync.hasOwnProperty('VISITORS_CHECKBOX'))
			{
				users = chimpSync.VISITORS_CHECKBOX;
				writeProgress(self.dom.objSyncVisitorsProgress, users);
			}
			else
				hideProgress(self.dom.objSyncVisitorsProgress);

			if (chimpSync.hasOwnProperty('CUSTOMERS_CHECKBOX'))
			{
				users = chimpSync.CUSTOMERS_CHECKBOX;
				writeProgress(self.dom.objSyncCustomersProgress, users);
			}
			else
				hideProgress(self.dom.objSyncCustomersProgress);

			if (chimpSync.hasOwnProperty('ORDERS_CHECKBOX'))
			{
				users = chimpSync.ORDERS_CHECKBOX;
				writeProgress(self.dom.objSyncOrdersProgress, users);
				self.dom.lastSyncOrders.html(users.date_add);
			}
			else
				hideProgress(self.dom.objSyncOrdersProgress);


			if (chimpSync.hasOwnProperty('ERRORS') && chimpSync.ERRORS.length > 0)
			{
				self.dom.objErrorMessageBox.box.show();
				self.dom.objErrorMessageBox.span.html(chimpSync.ERRORS.join('<br>'));
				setTimeout(function(){
					self.dom.objErrorMessageBox.box.hide();
					self.dom.objErrorMessageBox.span.html('');
				}, 15000);
			}

			if (chimpSync.hasOwnProperty('ERRORS_MESSAGE') && chimpSync.ERRORS_MESSAGE.length > 0)
			{
				var errorsArray = chimpSync.ERRORS_MESSAGE,
					errors = [],
					errorsDisplay;

				for (var i = 0, length = errorsArray.length; i <= length; i++) {

					if (typeof errorsArray[i] === 'object' && errorsArray[i].hasOwnProperty('error')) {
						errors.push(errorsArray[i].error);
					}
				}

				if (errors.length > 0) {
					errorsDisplay = errors.splice(0, 10);

					var contnet = l('Display first #s errors:').replace('#s', 10) + '<br><br>';
						contnet += errorsDisplay.join('<br>');
					self.dom.syncChimpErrorsMessage.show().html(contnet);

					setTimeout(function(){
						self.dom.syncChimpErrorsMessage.hide().html('');
					}, 15000);
				}

			}

			if (getInProgress(chimpSync).length == 0)
			{
				stopSync();
				return true;
			}

			return false;
		}

		function getInProgress(chimpSync)
		{
			return $.map(chimpSync, function(item, index){
				if (item.hasOwnProperty('done') && item.done == false)
					return item.done;
			});
		}

		function showBox()
		{
			var dom = self.dom;
			if (!isVisible(dom.syncListsProgressBox))
				dom.syncListsProgressBox.show();
		}

		function hideBox()
		{
			var dom = self.dom;
			if (isVisible(dom.syncListsProgressBox))
			{
				setTimeout(function(){
					dom.syncListsProgressBox.slideUp();
				}, 15000);
			}
		}

		function showSyncListButton()
		{
			var dom = self.dom;
			dom.btnSyncLists.show();
			dom.btnDeleteChimpOrders.show();
			dom.btnStopSyncLists.hide();
		}

		function hideSyncListButton()
		{
			var dom = self.dom;
			dom.btnSyncLists.hide();
			dom.btnDeleteChimpOrders.hide();
			dom.btnStopSyncLists.show();
		}

		function getSyncListsStatus()
		{	
			var dom = self.dom;
			showBox();
			hideSyncListButton();

			$.postAjax({'chimp': 'getSyncListsStatus'}).done(function(response){
				checkStatus(response);
			});

			interval(function(response, that){
				if (checkStatus(response))
					clearInterval(that);
			}, 'getSyncListsStatus' , syncRefreshRate);
		}

		function interval(func, php_function, time) 
		{
			var interval = setInterval(function(){
				$.postAjax({'chimp': php_function}).done(function(response){
					func(response, interval);
				});
			}, time);
		}

		function setSyncLists(data)
		{
			var dom = self.dom;
			ajaxStart(dom.btnSyncLists);
			$.postAjax({'chimp': 'setSyncLists', 'data': data}).done(function(response){
				if(!response.status)
					box.alertErrors(response.errors);
				else
				{
					getSyncListsStatus();
					startSyncLists();
				}
			}).always(function(){
				ajaxDone(dom.btnSyncLists);
			});
		}

		function deleteChimpOrders()
		{
			var dom = self.dom;
			ajaxStart(dom.btnDeleteChimpOrders);
			dom.btnSyncLists.hide();
			$.postAjax({'chimp': 'deleteChimpOrders'}).done(function(response){
				alert(box.displayAlert(response.msg));
				dom.lastSyncOrders.html(response.date_add);
			}).always(function(){
				ajaxDone(dom.btnDeleteChimpOrders);
				dom.btnSyncLists.show();
			});
		}

		function startSyncLists()
		{
			$.postAjax({'chimp': 'startSyncLists'}).done(function(response){
				if (response.hasOwnProperty('ADDED_CHECKBOX') && !response.ADDED_CHECKBOX.done)
					return startSyncLists();

				if (response.hasOwnProperty('VISITORS_CHECKBOX') && !response.VISITORS_CHECKBOX.done)
					return startSyncLists();

				if (response.hasOwnProperty('CUSTOMERS_CHECKBOX') && !response.CUSTOMERS_CHECKBOX.done)
					return startSyncLists();

				if (response.hasOwnProperty('ORDERS_CHECKBOX') && !response.ORDERS_CHECKBOX.done)
					return startSyncLists();
			});
		}

		function checkSyncInProgress(chimpSync)		
		{

			if (getInProgress(chimpSync).length > 0)
				getSyncListsStatus();
			else
				showSyncListButton();
		}

		function inArray(value, arr)
		{
			return (arr.indexOf(value) === -1 ? false : true);
		}

		function ajaxStart(target)
		{
			var ajaxLoader = target.find('.ajax-loader');
			if (ajaxLoader.length > 0) 
			{
				ajaxLoader.show();
			}
		}

		function ajaxDone(target)
		{
			var ajaxLoader = target.find('.ajax-loader');
			if (ajaxLoader.length > 0) 
			{
				ajaxLoader.hide();
			}
		}

		function buildChimpTemplateGrid(id, arr)
		{
			var table = '' +
			'<table id="'+id+'" class="table table-bordered '+id+'">\
				<thead>\
				<tr>';

				if (inArray('preview_image', arr))
					table += '<th class="preview_image" data-field="preview_image">'+l('preview image')+'</th>';

				if (inArray('name', arr))
					table += '<th class="name" data-field="name">'+l('name')+'</th>';

				if (inArray('layout', arr))
					table += '<th class="layout" data-field="layout">'+l('layout')+'</th>';

				if (inArray('category', arr))
					table += '<th class="category" data-field="category">'+l('category')+'</th>';

				if (inArray('date_created', arr))
					table += '<th class="date_created" data-field="date_created">'+l('date created')+'</th>';

				if (inArray('active', arr))
					table += '<th class="np-active" data-field="active">'+l('active')+'</th>';

			table += '<th class="actions" data-template="actions">'+l('actions')+'</th>\
				</tr>\
				</thead>\
			</table>';

			return $(table);
		}

		function buildGalleryGrid(data)
		{
			return buildGkGrid('chimp-template-gallery', data, 'gallery');
		}

		function buildBaseGrid(data)
		{
			return buildGkGrid('chimp-template-base', data, 'base');
		}

		function buildUserGrid(data)
		{
			return buildGkGrid('chimp-template-user', data, 'user');
		}

		function buildGkGrid( id, data, type )
		{
			var dataModel,
				dataSource,
				dataGrid = buildChimpTemplateGrid(id, ['name', 'layout', 'category', 'active']);

			dataModel = new gk.data.Model({
				id: 'id',
			});

			dataSource = new gk.data.DataSource({
				pageSize: 7,
				transport: {
					data: data,
				},
				schema: {
					model: dataModel
				},
			});

			dataGrid.gkGrid({
				dataSource: dataSource,
				selectable: false,
				currentPage: 1,
				pageable: true,
				template: {
					active: function(item, value) {
						return (value ? '<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>' : '<span class="list-action-enable action-disabled"><i class="icon icon-remove"></i></span>');
					},

					actions: function(item) 
					{
						var preview_image = item.data.preview_image,
							content       = $('<div></div>'),
							preview       = preview_image == null ? '' : $('<a href="'+preview_image+'" class="btn btn-default btn-margin pull-right" target="_blank"><i class="icon icon-eye" style="margin-right: 8px;"></i> '+l('preview')+'</a>'),
							add           = $('<a href="javascript:{}" class="btn btn-default btn-margin pull-right btn-import-chimp-tpl"><span class="ajax-loader" style="display: none; margin-left: 0; margin-top: 0;"></span><span class="import-text" style="display: inline-block;"><i class="icon icon-download" style="margin-right: 8px;"></i> '+l('import')+'</span></a>');

						add.on('click', function(){

								var importText = add.find('.import-text');
								var name    = item.data.name;
								ajaxStart(add);
								importText.css({'padding-right': '7px'});
								importTemplate(item.data.id, type, name).always(function(){
									ajaxDone(add);
									importText.css({'padding-right': '0'});		
								});

						});

						content.append(preview);
						content.append(add);

						return content;
					},
				}
			});

			return {
				'dataSource': dataSource,
				'dataModel': dataModel,
				'dataGrid': dataGrid,
			};
		}

		function importTemplate(chimpIdTemplate, type, name)
		{
			return $.postAjax({'chimp': 'getTemplateSource', 'template_id': chimpIdTemplate, 'type': type}).done(function(response){
				if (!response.status)
					alertErrors(response.errors);
				else
				{
					var template = response.template;
					var message = l('template name');
					var newName = popUpImport(message, name);

					if ( newName == '' || newName == null )
						return false;

					if (newName)
						saveTemplate(newName, template, 0);
					else
						return false;
				}
			});
		}

		function saveTemplate(name, content, override) {
			var dom = self.dom,
				override = typeof override !== 'undefined' ? override : 0;

			return $.postAjax({'chimp': 'importTemplate', 'name': name, 'content': content, 'override': override } ).done(function( response ) {
				if (!response.status) {
					NewsletterPro.alertErrors(response.errors);
				} else {

					if (response.worning.hasOwnProperty('101'))
					{
						var message = response.worning['101'].replace(/\&quot;/g, '"');
						if (prompt(message, name))
							saveTemplate(name, content, 1);
						else
							return false;
					}

					var createTemplate = NewsletterPro.modules.createTemplate;

					createTemplate.vars.templateDataSource.sync(function(dataSource){
						var currentTemplate = dataSource.getItemByValue('data.filename', response.template_name);
						dataSource.setSelected(currentTemplate);
						createTemplate.changeTemplate(currentTemplate);
					});
				}

			}).fail(function( response ) {
				NewsletterPro.alertErrors([l('import failure')]);
			}).always(function( response ) {

			});
		}

		function setImportTemplateData(obj)
		{
			importTemplateData.user    = obj.user;
			importTemplateData.base    = obj.base;
			importTemplateData.gallery = obj.gallery;
		}

		function getImportTemplateData()
		{
			return importTemplateData;
		}

		function getAllTemplates(func)
		{
			return $.postAjax({'chimp': 'getAllTemplates'}).done(function(response){
				func(response);
			});
		}

		function popUpImport(message, name)
		{
			return prompt(message, name);
		}

		function startLoading(contentBox, contentAjax)
		{
			contentBox.css({
				'opacity': '0.5'
			});

			contentAjax.show();
			contentAjax.css({
				position: 'absolute',
				display: 'block',
				margin: '0',
				padding: '0',
				left: 780 / 2 - contentAjax.width() / 2,
				top: 400 / 2 - contentAjax.height() / 2,
			});
		}

		function endLoading(contentBox, contentAjax)
		{
			contentBox.css({
				'opacity': '1'
			});
			contentAjax.hide();
		}

		function exportTemplateRequest(name, idLang, filename, override)
		{
			box.modules.createTemplate.newsletterTemplate.saveTemplate().done(function(resp){
				if (!resp.status)
				{
					box.alertErrors(resp.errors);
				}
				else
				{
					box.showAjaxLoader(exportTemplateWinDom.btnExport)

					$.postAjax({'chimp': 'exportTemplate', 'name': name, 'id_lang': idLang, 'filename': filename, 'override': override }).done(function(response){
						if (!response.status) 
						{
							box.alertErrors(response.errors);
						}
						else
						{
							if (response.name_exists)
							{
								var message = box.displayAlert(response.message);
								var conf = confirm(message);
								if (conf)
								{
									exportTemplate(name, idLang, 1);
									return true;
								}
								else
									return false;
							}

							exportTemplateWinDom.success.show();
							setTimeout(function(){
								exportTemplateWinDom.success.hide();
							}, 7000);

						}
					}).always(function(){
						box.hideAjaxLoader(exportTemplateWinDom.btnExport);
					});
				}
			});
		}

		function exportTemplate(name, idLang, override)
		{
			var info     = NewsletterPro.modules.createTemplate.vars.templateDataSource.selected.data,
				name     = name || info.name,
				filename = info.filename,
				override = typeof override === 'undefined' ? 0 : override;

			exportTemplateRequest(name, idLang, filename, override);
		}

		// this function will run if the Mail Chimp is installed
		function installedChimpSettings()
		{
			var dom = self.dom;
			dom.btnChimpImportHtml.show();
			dom.btnChimpExportHtml.show();
			showUninstallButton();
			dom.syncBackChimpContent.show();

		}

		// this function will run if the Mail Chimp is not installed
		function uninstalledChimpSettings()
		{
			var dom = self.dom;
			dom.btnChimpImportHtml.hide();
			dom.btnChimpExportHtml.hide();
			showInstallButton();
			dom.syncBackChimpContent.hide();
		}

		self.ready(function(dom) 
		{
			l                  = NewsletterPro.translations.l(NewsletterPro.translations.modules.mailChimp);
			dataStorage        = box.dataStorage.data;
			chimpConfig        = dataStorage.chimp_config;
			chimpSyncProgress  = dataStorage.chimp_sync,
			contentIsSet	   = null,
			importTemplateComponents = {
				user: null,
				base: null,
				gallery: null,
			},
			importTemplateData = {
				user: null,
				base: null,
				gallery: null,
			};

			var getMailChimpTemplateName = function(isoColde, addIso)
			{
				var tnArray = box.dataStorage.get('configuration.NEWSLETTER_TEMPLATE').replace(/_/, ' ').replace(/\.html$/, '').split(' '),
					templateName = '',
					firstLang = box.dataStorage.get('all_languages')[0],
					isoColde = typeof isoColde !== 'undefined' ? isoColde : firstLang.iso_code,
					addIso = typeof addIso !== 'undefined' ? addIso : true;

				for (var i = 0; i < tnArray.length; i++) {
					templateName += box.ucfirst(tnArray[i]) + ' ';
				}

				if (addIso)
					templateName += isoColde.toUpperCase();

				// if (typeof isoColde !== 'undefined')

				return templateName;
			};

			exportTemplateWin = new gkWindow({
				width: 600,
				height: 400,
				setScrollContent: 340,
				title: l('Export Template To MailChimp'),
				className: 'np-export-mailchimp-template-win',
				show: function(win) 
				{
					exportTemplateWinDom.templateName.html(getMailChimpTemplateName(null, false));
					exportTemplateWinDom.chimpTemplateName.val(getMailChimpTemplateName());
				},
				close: function(win) {},
				content: function(win)
				{
					var languages = box.dataStorage.get('all_languages'),
						template,
						lang,
						languagesOptions = '';
					
					for (var i = 0; i < languages.length; i++)
					{
						lang = languages[i];

						languagesOptions += '<option value="'+lang.iso_code+'">'+lang.name+'</option>';
					}

					template = $('\
						<div class="form-group clearfix">\
							<div class="row">\
								<div class="form-group clearfix">\
									<label class="control-label col-sm-4"><span class="label-tooltip">'+l('Template Name')+'</span></label>\
									<div class="col-sm-8">\
										<span id="np-export-mailchimp-template-name" style="margin-top: 6px; display: block;"></span>\
									</div>\
								</div>\
								<div class="form-group clearfix">\
									<label class="control-label col-sm-4"><span class="label-tooltip">'+l('Language')+'</span></label>\
									<div class="col-sm-8">\
										<select id="np-export-mailchimp-template-lang-select" class="form-control fixed-width-xxl">\
											'+languagesOptions+'\
										</select>\
									</div>\
								</div>\
								<div class="form-group clearfix">\
									<label class="control-label col-sm-4"><span class="label-tooltip">'+l('MailChimp Name')+'</span></label>\
									<div class="col-sm-8">\
										<input id="np-export-mailchimp-template-input-tn" type="text" class="form-control fixed-width-xxl" value="">\
									</div>\
								</div>\
								<div class="form-group clearfix">\
									<div class="col-sm-8 col-sm-offset-4">\
										<a id="np-export-mailchimp-template-btn" href="javascript:{}" class="btn btn-default">\
											<span class="btn-ajax-loader"></span>\
											<i class="icon icon-download"></i>\
											'+l('Export')+'\
										</a>\
										<span id="np-export-mailchimp-template-success" style="display: none;"><span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span></span>\
									</div>\
								</div>\
							</div>\
						</div>\
					');

					exportTemplateWinDom = {
						templateName: template.find('#np-export-mailchimp-template-name'),
						langSelect: template.find('#np-export-mailchimp-template-lang-select'),
						chimpTemplateName: template.find('#np-export-mailchimp-template-input-tn'),
						btnExport: template.find('#np-export-mailchimp-template-btn'),
						success: template.find('#np-export-mailchimp-template-success'),
					};

					exportTemplateWinDom.langSelect.on('change', function(){
						var ctn = exportTemplateWinDom.chimpTemplateName,
							isoColde = $(this).val(),
							templateName = getMailChimpTemplateName(isoColde);

						ctn.val(templateName);
					});

					exportTemplateWinDom.btnExport.on('click', function(){
						var templateName = exportTemplateWinDom.chimpTemplateName.val(),
							isoColde = exportTemplateWinDom.langSelect.val(),
							languages = box.dataStorage.get('all_languages'),
							idLang = 0;

						for (var i = 0; i < languages.length; i++)
						{
							var lang = languages[i];
							if (lang.iso_code === isoColde)
							{
								idLang = Number(lang.id_lang);
								break;
							}
						}

						exportTemplate(templateName, idLang);
					});

					return template;
				}
			});

			var importTemplateWin = gkWindow({
				width: 800,
				height: 500,
				title: l('import template from chimp'),
				className: 'gk-import-template-window-view',
				show: function(win) {

					var content = contentIsSet != null ? contentIsSet : $('<div id="gk-import-content" style="position: relative;"><div id="gk-import-template-content-box"></div><span class="tpl-content ajax-loader"></span></div>'),
						contentBox = content.find('#gk-import-template-content-box'),
						contentAjax = content.find('.tpl-content.ajax-loader');

					startLoading(contentBox, contentAjax);

					getAllTemplates(function(response)
					{
						endLoading(contentBox, contentAjax);

						if (!response.status)
						{
							box.alertErrors(response.errors);
						}
						else
						{
							setImportTemplateData({
								user: response.templates.user,
								base: response.templates.base,
								gallery: response.templates.gallery,
							});

							if (importTemplateComponents.user == null)
							{
								importTemplateComponents.user = buildUserGrid(getImportTemplateData().user);
								var tableBox = $('<div></div>');
								tableBox.append('<h4 class="chimp-import-tpl-title user-template">'+l('user template')+'</h4>');
								tableBox.append(importTemplateComponents.user.dataGrid);
								contentBox.append(tableBox);
							}
							else
							{
								importTemplateComponents.user.dataSource.setData(getImportTemplateData().user);
								importTemplateComponents.user.dataSource.sync();
							}

							if (importTemplateComponents.gallery == null)
							{
								importTemplateComponents.gallery = buildGalleryGrid(getImportTemplateData().gallery);
								var tableBox = $('<div></div>');
								tableBox.append('<h4 class="chimp-import-tpl-title">'+l('gallery template')+'</h4>');
								tableBox.append(importTemplateComponents.gallery.dataGrid);
								contentBox.append(tableBox);
							}
							else
							{
								importTemplateComponents.gallery.dataSource.sync(getImportTemplateData().gallery);
							}

							if (importTemplateComponents.base == null)
							{
								importTemplateComponents.base = buildBaseGrid(getImportTemplateData().base);
								var tableBox = $('<div></div>');
								tableBox.append('<h4 class="chimp-import-tpl-title">'+l('base template')+'</h4>');
								tableBox.append(importTemplateComponents.base.dataGrid);
								contentBox.append(tableBox);
							}
							else
							{
								importTemplateComponents.base.dataSource.sync(getImportTemplateData().base);
							}
						}
					});

					if (contentIsSet == null)
					{
						contentIsSet = content;
						importTemplateWin.setContent(content);
					}
				},
			});

			checkSyncInProgress(chimpSyncProgress);

			if (dataStorage.chimpIsInstalled) 
				installedChimpSettings();
			else 
				uninstalledChimpSettings();

			if (getConfig('ORDERS_CHECKBOX'))
				dom.syncOrdersButtonText.show();
			else
				dom.syncOrdersButtonText.hide();

			dom.checkboxSyncCustomers.prop('checked', getConfig('CUSTOMERS_CHECKBOX'))
			dom.checkboxSyncVisitors.prop('checked', getConfig('VISITORS_CHECKBOX'))
			dom.checkboxSyncAdded.prop('checked', getConfig('ADDED_CHECKBOX'))
			dom.checkboxSyncOrders.prop('checked', getConfig('ORDERS_CHECKBOX'));

			// add events 
			dom.btnUninstallChimp.on('click', function(){
				uninstallChimp();
			});

			dom.btnInstallChimp.on('click', function(){
				var api_key = dom.inputChimpApiKey.val(),
					list_id = dom.inputChimpListId.val();

				installChimp(api_key, list_id);
			});

			dom.btnPingChimp.on('click', function(){
				pingChimp();
			});

			dom.checkboxSyncCustomers.on('change', function(){
				updateSyncCheckbox('CUSTOMERS_CHECKBOX', isChecked($(this)));
			});

			dom.checkboxSyncVisitors.on('change', function(){
				updateSyncCheckbox('VISITORS_CHECKBOX', isChecked($(this)));
			});

			dom.checkboxSyncAdded.on('change', function(){
				updateSyncCheckbox('ADDED_CHECKBOX', isChecked($(this)));
			});

			dom.checkboxSyncOrders.on('change', function(){
				var checked = isChecked($(this));
				if (checked)
					dom.syncOrdersButtonText.show();
				else
					dom.syncOrdersButtonText.hide();

				updateSyncCheckbox('ORDERS_CHECKBOX', checked);
			});

			dom.btnSyncLists.on('click', function(){
				setSyncLists({
					'CUSTOMERS_CHECKBOX': isChecked(dom.checkboxSyncCustomers),
					'VISITORS_CHECKBOX': isChecked(dom.checkboxSyncVisitors),
					'ADDED_CHECKBOX': isChecked(dom.checkboxSyncAdded),
					'ORDERS_CHECKBOX': isChecked(dom.checkboxSyncOrders),
				});
			});

			dom.btnStopSyncLists.on('click', function(){
				stopSync();
			});

			dom.btnChimpImportHtml.on('click', function(){
				importTemplateWin.show();
			});

			dom.btnChimpExportHtml.on('click', function(){
				exportTemplateWin.show();
			});

			dom.btnDeleteChimpOrders.on('click', function(){
				deleteChimpOrders();
			});

			dom.resetSyncOrderDate.on('click', function(){
				$.postAjax({'chimp': 'resetSyncOrderDate'}).done(function(response){
					if (response.success)
						dom.lastSyncOrders.html(response.date_add);
					else
						box.alertErrors(response.errors);
				});
			});

			var start  = 0,
				limit = 25,
				backTotal = 0,
				backCreated = 0,
				backUpdated = 0,
				backErrors = 0;


			var refreshLists = function()
			{
				var sn = box.modules.sendNewsletters;
				
				sn.vars.customers.sync();

				if (sn.isNewsletterProSubscriptionActive())
					sn.vars.visitorsNP.sync();
				else
					sn.vars.visitors.sync();

				sn.vars.added.sync();
			};

			var syncListBackFunc = function(start, limit)
			{
				dom.syncListsBack.show();
				dom.objSyncListBack.box.show();
				dom.objSyncListBack.ajaxLoader.show();
				dom.btnSyncListsBack.addClass('disabled');
				box.showAjaxLoader(dom.btnSyncListsBack);

				$.postAjax({'chimp': 'syncListsBack', start: start, limit: limit}).done(function(response){
					if (response.success)
					{
						backTotal += response.total;
						backCreated += response.created;
						backUpdated += response.updated;
						backErrors += response.errors_count;

						dom.objSyncListBack.total.html(response.member_count);
						dom.objSyncListBack.created.html(backCreated);
						dom.objSyncListBack.updated.html(backUpdated);
						dom.objSyncListBack.errors.html(backErrors);

						if (response.total > 0)
						{
							start++;
							syncListBackFunc(start, limit);
						}
						else
						{
							dom.btnSyncListsBack.removeClass('disabled');
							dom.objSyncListBack.ajaxLoader.hide();

							setTimeout(function(){
								dom.syncListsBack.hide();
								dom.objSyncListBack.box.hide();
							}, 15000);

							refreshLists();
						}
					}
					else
					{
						refreshLists();

						dom.btnSyncListsBack.removeClass('disabled');
						dom.objSyncBackErrorMessageBox.box.show();
						dom.objSyncBackErrorMessageBox.span.show().html(box.displayAlert(response.errors, '<br>'));
					}
				}).always(function(){
					box.hideAjaxLoader(dom.btnSyncListsBack);
				})
			};

			dom.btnSyncListsBack.on('click', function(){

				start = 0;
				limit = 25;
				backTotal = 0;
				backCreated = 0;
				backUpdated = 0;
				backErrors = 0;

				syncListBackFunc(start, limit);
			});
		});
	},

	ready: function(func) 
	{
		var self = this;
		$(document).ready(function(){

			var syncAddedProgress     = $('#sync-added-progress');
			var syncVisitorsProgress  = $('#sync-visitors-progress');
			var syncCustomersProgress = $('#sync-customers-progress');
			var syncOrdersProgress    = $('#sync-orders-progress');
			var errorMessageBox       = $('#sync-error-message-box');

			// var syncListBackBox       = $('#sync-lists-back-progress-box');
			var syncListBackError     = $('#sync-list-back-error-message-box');
			var syncListBackProgress  = $('#sync-list-back-progress');

			self.dom = {
				btnInstallChimp: $('#install-chimp'),
				btnPingChimp: $('#ping-chimp'),

				inputChimpApiKey: $('#chimp-api-key'),
				inputChimpListId: $('#chimp-list-id'),

				installLoading: $('#install-chimp-loading'),

				lastSyncOrders: $('#last-sync-orders'),

				btnUninstallChimp: $('#uninstall-chimp'),
				uninstallLoading: $('#uninstall-chimp-loading'),

				checkboxSyncCustomers: $('#sync-customers'),
				checkboxSyncVisitors: $('#sync-visitors'),
				checkboxSyncAdded: $('#sync-added'),
				checkboxSyncOrders: $('#sync-orders'),

				resetSyncOrderDate: $('#reset-sync-order-date'),

				syncOrdersButtonText: $('#sync-orders-button-text'),

				btnSyncLists: $('#sync-lists'),
				btnStopSyncLists: $('#stop-sync-lists'),

				btnDeleteChimpOrders: $('#delete-chimp-orders'),

				syncListsProgressBox: $('#sync-lists-progress-box'),

				syncChimpErrorsMessage: $('#sync-chimp-errors-message'),

				chimpMenu: $('#chimp-menu'),

				objSyncAddedProgress: {
					box: syncAddedProgress,
					total: syncAddedProgress.find('.sync-emails-total'),
					created: syncAddedProgress.find('.sync-emails-created'),
					updated: syncAddedProgress.find('.sync-emails-updated'),
					errors: syncAddedProgress.find('.sync-emails-errors'),
					ajaxLoader: syncAddedProgress.find('.ajax-loader'),
				},

				objSyncVisitorsProgress: {
					box: syncVisitorsProgress,
					total: syncVisitorsProgress.find('.sync-emails-total'),
					created: syncVisitorsProgress.find('.sync-emails-created'),
					updated: syncVisitorsProgress.find('.sync-emails-updated'),
					errors: syncVisitorsProgress.find('.sync-emails-errors'),
					ajaxLoader: syncVisitorsProgress.find('.ajax-loader'),
				},

				objSyncCustomersProgress: {
					box: syncCustomersProgress,
					total: syncCustomersProgress.find('.sync-emails-total'),
					created: syncCustomersProgress.find('.sync-emails-created'),
					updated: syncCustomersProgress.find('.sync-emails-updated'),
					errors: syncCustomersProgress.find('.sync-emails-errors'),
					ajaxLoader: syncCustomersProgress.find('.ajax-loader'),
				},

				objSyncOrdersProgress: {
					box: syncOrdersProgress,
					total: syncOrdersProgress.find('.sync-emails-total'),
					created: syncOrdersProgress.find('.sync-emails-created'),
					updated: syncOrdersProgress.find('.sync-emails-updated'),
					errors: syncOrdersProgress.find('.sync-emails-errors'),
					ajaxLoader: syncOrdersProgress.find('.ajax-loader'),
				},

				objErrorMessageBox : {
					box: errorMessageBox,
					span: errorMessageBox.find('.sync-error-message'),
				},

				btnChimpImportHtml: $('#chimp-import-html'),
				btnChimpExportHtml: $('#chimp-export-html'),

				syncListsBack: $('#sync-lists-back-progress-box'),
				btnSyncListsBack: $('#sync-chimp-lists-back'),

				objSyncBackErrorMessageBox: {
					box: syncListBackError,
					span: syncListBackError.find('.sync-error-message')
				},

				objSyncListBack: {
					box: syncListBackProgress,
					total: syncListBackProgress.find('.sync-emails-total'),
					created: syncListBackProgress.find('.sync-emails-created'),
					updated: syncListBackProgress.find('.sync-emails-updated'),
					errors: syncListBackProgress.find('.sync-emails-errors'),
					ajaxLoader: syncListBackProgress.find('.ajax-loader'),
				},

				syncBackChimpContent: $('#sync-back-chimp-content'),
			};

			func(self.dom);
		});
	},
}.init(NewsletterPro));
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

var NewsletterProControllers = {
	init : function() {
		var self = this,
			box = NewsletterPro;

		jQuery(document).ready(function($) {

			// init currencyes
			box.components.CurrencySelect.initBySelection($('.gk_currency_select'));

			self.l = NewsletterPro.translations.l(NewsletterPro.translations.constrollers);
			
			self.IndexController.init({
			});

			self.NavigationController.init();
			self.SendController.init();
			self.SettingsController.init();
			self.TemplateController.init();
			self.UpgradeController.init();
			self.ClearCacheController.init();
			
		});
	},

	elem : {},
	vars : {},
	objs : {},
	fcts : {},
	addElement : function( name, elem ) {
		this.elem[name] = elem;
		return this;
	},
	addVariable : function( name, varsiable ) {
		this.vars[name] = varsiable;
		return this;
	},
	addObject : function( name, obj ) {
		this.objs[name] = obj;
		return this;
	}, 
	addFunction : function( name, fct ) {

	},

	IndexController:
	{
		init: function(dom)
		{
			
		}
	},

	SendController :
	{
		vars : {},
		elem : {},
		addVariable : function( name, varsiable ) {
			this.vars[name] = varsiable;
			return this;
		},
		addElement : function( name, elem ) {
			this.elem[name] = elem;
			return this;
		},
		init : function() {
			var self = this;

				 // function sendTestEmail() 
			self.addElement( 'testEmail', $('#test-email') )
				.addElement( 'testEmailButton', self.elem.testEmail.find('#test-email-button') )
				.addElement( 'testEmailInput', self.elem.testEmail.find('#test-email-input') )
				.addElement( 'testEmailCheckbox', self.elem.testEmail.find('#test-email-checkbox') )
				.addElement( 'testEmailMessage', self.elem.testEmail.find('#test-email-message') )
				.addElement( 'smtpTestEmail', $('#smtp-test-email') )
				.addElement( 'smtpTestEmailMessage', $('#smtp-test-email-message') )
				 // function selectAllCustomers
				.addElement( 'selectAllCustomersUserList',$('#user-list') )
				.addElement( 'selectAllCustomersFilters', $('.dropdown-menu') )
				.addElement( 'selectAllCustomersCount', $('#select-all-customers-count') )
				.addElement('testSendEmailBox', $('#test-send-email-box'))
				.addElement('sendTestEmailLangSwitcher', $('#send-test-email-language-switcher'))
				 // function addEmail 
				.addElement( 'addEmailButton', $('#add-email-button') )
				.addElement( 'addEmailInput', $('#add-email-input') )
				.addElement( 'addEmailMessage', $('#add-email-message') )
				.addElement( 'addedSubscribedList', $('#added-subscribed ul') )

				 // function deleteEmail 
				.addElement( 'usersMessage', $('#users-subscribed-message') )
				.addElement( 'visitorsMessage', $('#visitors-subscribed-message') )
				.addElement( 'addedMessage', $('#add-email-message') )
				 // function sleepNewsletter 
				.addElement( 'sleepNewsletterMessage', $('#email-sleep-message') )
				 // function sendNewsletters 
				.addElement( 'sendNewsletters', $('#send-newsletters') )
				.addElement( 'newTask', $('#new-task') )
				.addElement( 'stopSendNewsletters', $('#stop-send-newsletters') )
				.addElement( 'continueSendNewsletters', $('#continue-send-newsletters') )
				.addElement( 'pauseSendNewsletters', $('#pause-send-newsletters') )
				.addElement( 'emailsToSendCount', $('#emails-to-send-count') )
				.addElement( 'emailsSentCountSucc', $('#emails-sent-count-succ') )
				.addElement( 'emailsSentCountErr', $('#emails-sent-count-err') )
				.addElement( 'previousSendNewsletters', $('#previous-send-newsletters') )

				.addElement( 'lastSendErrorDiv', $('#last-send-error-div') )
				.addElement( 'lastSendError', $('#last-send-error') )

			self.addVariable( 'pauseNewsletters', false )
				.addVariable( 'stopNewsletters', false )
				.addVariable( 'emailsSentCountSucc', 0 )
				.addVariable( 'emailsSentCountErr', 0 )

			self.elem.testEmailCheckbox.on( 'change', function( event ) {
				if ( $(this).is(':checked') )
					self.elem.testSendEmailBox.show();
				else
					self.elem.testSendEmailBox.hide();
			});

			var langSelect = new NewsletterPro.components.LanguageSelect({
				selector: self.elem.sendTestEmailLangSwitcher,
				languages: NewsletterPro.dataStorage.get('all_languages'),
				click: function(lang, key) {
					var idLang = Number(lang.id_lang);
				},
			});
		},

		sendTestEmail : function(element)
		{
			var self = this,
				box = NewsletterPro,
				messageElement = self.elem.testEmailMessage,
				messageSuccessElement = $('#test-email-success-message'),
				email = self.elem.testEmailInput.val(),
				idLang = box.dataStorage.get('id_selected_lang');

			messageSuccessElement.hide();
			messageElement.hide();
			box.showAjaxLoader(element);
			$.postAjax({ 'submit': 'sendTestEmail', sendTestEmail: email, idLang: idLang }).done(function( data ) 
			{
				if (data.status)
					messageSuccessElement.show().html('<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>');
				else
					messageElement.empty().show().append('<div class="alert alert-danger">'+data.msg+'</div>');

				setTimeout( function() { 
					messageSuccessElement.hide();
					messageElement.hide();
				}, 5000);
			}).always(function(){
				box.hideAjaxLoader(element);
			});
		}, // end of sendTestEmail

		addEmail : function() {
			var self = this;

			$.postAjax({ 'submit': 'addEmail', addEmail : self.elem.addEmailInput.val() }).done(function( data ) {

				if( data.status == true ) {
					var cls = self.elem.addedSubscribedList.find('li:last').hasClass('odd') ? 'even' : 'odd';

					var template = '';
						template += '<li class="'+cls+'">';
						template += data.template;
						template += '</li>';

					self.elem.addedSubscribedList.append(template);

					var eItem = self.elem.addedSubscribedList.find('li').last();
					NewsletterProComponents.objs.addedSubscribed.createItems( eItem );
					self.elem.addEmailMessage.empty().show().append('<span class="success-msg">'+data.msg+'</span>');
				} else 
					self.elem.addEmailMessage.empty().show().append('<span class="error-msg">'+data.msg+'</span>');

				setTimeout( function() { self.elem.addEmailMessage.fadeOut('slow'); }, 5000);
			});
		}, // end of addEmail

		sleepNewsletter : function( element ) {
			var self = this;
			$.postAjax({ 'submit': 'sleepNewsletter', sleepNewsletter: element.val() }).done(function( data ) {
				if  ( data.status )
					self.elem.sleepNewsletterMessage.empty().show().append('<span class="success-icon">&nbsp;</span>');
				else
					self.elem.sleepNewsletterMessage.empty().show().append('<span class="error-msg">'+data.msg+'</span>');

				setTimeout( function() { self.elem.sleepNewsletterMessage.hide(); }, 5000);
			});
		}, // end of sleepNewsletter

		// Select all customers emails from database  
		allCustomers : [],
		selectAllCustomers : function( element ) {
			var self = this;

			if (element.is(':checked') )
			{
				self.elem.selectAllCustomersFilters.css({ position : 'relative', zoom : '1' });
				self.elem.selectAllCustomersUserList.slideUp( 'slow' ) ;

				$.postAjax({ 'submit': 'selectAllCustomers', selectAllCustomers: true }).done(function( data ) {
					self.elem.selectAllCustomersCount.empty().append(data.length);
					self.allCustomers = data;
				});
			}
			else
			{
				self.elem.selectAllCustomersCount.empty().append('0');
				self.elem.selectAllCustomersUserList.slideDown( 'slow' , function() {
					self.elem.selectAllCustomersFilters.css({position : 'absolute', zoom : '1'});
					self.allCustomers = [];
				});
			}

		}, // end of selectAllCustomers

		getSelectedEmails : function() {
			var self = this,
				customerAllDbEmails = [],
				visitorsAllDbEmails = [],
				addedAllDbEmails = [],
				customers = NewsletterPro.modules.sendNewsletters.vars.customers,
				visitors,
				added = NewsletterPro.modules.sendNewsletters.vars.added,
				selectedEmails = [],
				emails;

			if (NewsletterPro.modules.sendNewsletters.isNewsletterProSubscriptionActive())
				visitors = NewsletterPro.modules.sendNewsletters.vars.visitorsNP;
			else
				visitors = NewsletterPro.modules.sendNewsletters.vars.visitors;

			if (customerAllDbEmails.length) {
				console.warn('this feature is not active');
			} else {
				emails = customers.getSelectedEmails();
				selectedEmails = selectedEmails.concat(emails);
			}

			if (visitorsAllDbEmails.length) {
				console.warn('this feature is not active');
			} else {
				emails = visitors.getSelectedEmails();
				selectedEmails = selectedEmails.concat(emails);
			}

			if (addedAllDbEmails.length) {
				 console.warn('this feature is not active');
			} else {
				emails = added.getSelectedEmails();
				selectedEmails = selectedEmails.concat(emails);
			}

			return selectedEmails;
		}, // end of getSelectedEmails

		/**
		 * Prepare emails for sending
		 * @param  {[array]} emails This parameter is optional
		 */
		prepareEmails : function(emails) 
		{
			var box = NewsletterPro,
				self = this,
				selected;

			if (typeof emails !== 'undefined')
				selected = emails;
			else
				selected = self.getSelectedEmails();

			if( selected.length == 0 )
			{
				alert(NewsletterProControllers.l('no email selected'));
				return;
			}

			var users = JSON.stringify(selected);
			return $.postAjax({'submit' : 'prepareEmails', prepareEmails:users}).done(function(response) {
				if (!response.status)
					NewsletterPro.alertErrors(response.errors.join("\n"));
				else 
				{
					NewsletterPro.modules.sendManager.startSendNewsletters(true);
				}
			}).promise();

		}, // end of prepareEmails

		goToNextStep : function() {
			var self = this;
			var emailsToSend = NewsletterProComponents.objs.emailsToSend;
			var emailsSent = NewsletterProComponents.objs.emailsSent;

			$.postAjax({'submit': 'goToNextStep',goToNextStep: true}).done(function( data ) {
				if( data.exit == true || self.vars.stopSendNewsletters == true ) {
					self.exitAction();
					return;
				}

				if( self.vars.pauseNewsletters == true )
					return;

				emailsToSend.createItems( data.emails );
				emailsSent.removeAllItems();

				self.sendNewsletters();
			});
		}, // end of goToNextStep

		sendNewsletters : function() 
		{
			console.warn('This function "sendNewsletters" is no longer used.');
			return;

			var self = this;
			var emailsToSend = NewsletterProComponents.objs.emailsToSend,
				dom = self.elem,
				l = NewsletterProControllers.l;

			if( emailsToSend.getLength() == 0 ) {
					self.goToNextStep();
			} else {

				self.setStop(false);
				self.setPause(false);

				if( self.elem.sendNewsletters.is(':visible') ) {
					self.elem.sendNewsletters.hide();
					self.elem.newTask.hide();
				}

				if( !self.elem.stopSendNewsletters.is(':visible') )
					self.elem.stopSendNewsletters.show();

				if( self.elem.previousSendNewsletters.is(':visible') )
					self.elem.previousSendNewsletters.hide();

				$.postAjax({'submit': 'sendNewsletters', sendNewsletters: emailsToSend.getFirstItem().email, exit : false}, 'json', false)
					.done(function( data ) {

					}).always( function( data ) {
						try 
						{
							if( typeof data.responseText != 'undefined' )
								var data = $.parseJSON( data.responseText );

						} 
						catch (e) 
						{
							console.warn(e.message);

							var first = emailsToSend.moveFirst();
							if ( typeof first !== 'undefined' && first != false ) {
								var template = '';
								template += '<span class="error-icon" style="float: right;"> </span>';
								first.instance.append( template );

								self.sendNewsletters();
							}

							return;
						}

						if ( data == null )
							return;

						if (typeof data.errors === 'object' && data.errors.hasOwnProperty('template')) {
							alert(data.errors.template);
						} else if (data.errors.length) {
							if (!dom.lastSendErrorDiv.is(':visible'))
								dom.lastSendErrorDiv.slideDown('slow');
							dom.lastSendError.html(data.errors.join('<br>'));
						}

						if( data.exit == true || self.vars.stopSendNewsletters == true ) {
							self.exitAction();
							return;
						}

						var first = emailsToSend.moveFirst();
						if ( typeof first !== 'undefined' && first != false ) {
							var template = '';

							if (data.hasOwnProperty('fwd_emails_success') && parseInt(data.fwd_emails_success) > 0)
							{
								var fwdCount = parseInt(data.fwd_emails_success);
								var fwsTrans = l('forwards');
								if (fwdCount == 1)
									fwsTrans = l('forward');

								template += '<span style="color: green; margin-left: 25px;">+'+fwdCount+' '+fwsTrans+'</span>';
							}

							template += '<span class="'+( data.status == true ? 'success-icon' : 'error-icon' )+'" style="float: right;"> </span>';
							first.instance.append( template );
						}

						if( self.vars.pauseNewsletters == true )
							return;

						self.elem.emailsSentCountSucc.text(data.emails_success);
						self.elem.emailsSentCountErr.text(data.emails_error);
						var remaining = parseInt(data.emails_count) - (parseInt(data.emails_success) + parseInt(data.emails_error));
						self.elem.emailsToSendCount.html( remaining );

						self.sendNewsletters();
					});
				}
		}, // end of sendNewsletters

		exitAction : function() {
			var self = this,
				dom = self.elem;

			self.setPause(true);

			if ( !self.elem.sendNewsletters.is(':visible') ) {
				self.elem.sendNewsletters.show();
				self.elem.newTask.show();
			}

			if ( self.elem.stopSendNewsletters.is(':visible') ) {
				self.elem.stopSendNewsletters.hide();
			}

			if ( self.elem.continueSendNewsletters.is(':visible') )
				self.elem.continueSendNewsletters.hide();

			if( !self.elem.previousSendNewsletters.is(':visible') )
					self.elem.previousSendNewsletters.show();

			setTimeout(function(){
				dom.lastSendErrorDiv.slideUp();
			}, 7000);

			NewsletterPro.modules.task.ui.components.sendHistory.sync();
		}, // end of exitAction

		setPause : function( bool ) {
			var self = this;
			self.vars.pauseNewsletters = bool;

			if ( bool == true ) {
				self.elem.pauseSendNewsletters.hide();
				self.elem.continueSendNewsletters.show();
			} else {
				self.elem.pauseSendNewsletters.show();
				self.elem.continueSendNewsletters.hide();
			}
		}, // end of setPause

		setStop : function( bool ) {
			var self = this;

			self.vars.stopSendNewsletters =  bool;
		}, // end of setStop

		stopNewsletters : function() {
			var dfd = $.Deferred();
			var self = this;
			var emailsToSend = NewsletterProComponents.objs.emailsToSend;
			var emailsSent = NewsletterProComponents.objs.emailsSent;

			self.setStop( true );

			$.postAjax({'submit': 'stopNewsletters', stopNewsletters: true}).done(function(){
				NewsletterPro.modules.task.ui.components.sendHistory.sync();
				dfd.resolve();
			});

			emailsToSend.removeAllItems();
			emailsSent.removeAllItems();

			self.elem.sendNewsletters.show();
			self.elem.newTask.show();

			self.elem.stopSendNewsletters.hide();

			self.elem.continueSendNewsletters.hide();

			self.elem.pauseSendNewsletters.hide();

			self.elem.previousSendNewsletters.show();

			self.elem.emailsSentCountSucc.text('0');
			self.elem.emailsSentCountErr.text('0');
			self.elem.emailsToSendCount.html('0');

			return dfd.promise();
		}, // end of stopNewsletters

	}, // end of SendController

	SettingsController : 
	{
		init : function() {
			var self = this;

			self.addElement('smtpForm', $('#smtpForm') );

			self.addElement('smtpName', $('#smtp-name') );
			self.addElement('smtpDomain', $('#smtp-domain') );
			self.addElement('smtpPasswd', $('#smtp-passwd') );
			self.addElement('smtpServer', $('#smtp-server') );
			self.addElement('smtpEncryption', $('#smtp-encryption') );
			self.addElement('smtpPort', $('#smtp-port') );
			self.addElement('smtpUser', $('#smtp-user') );
			self.addElement('saveSmtp', $('#save-smtp') );
			self.addElement('addSmtp', $('#add-smtp') );
			self.addElement('deleteSmtp', $('#delete-smtp') );

			self.addElement('saveSmtpMessage', $('#save-smtp-message') );

			self.addElement( 'ganalyticsId', $('#ganalytics-id') );
			self.addElement( 'ganalyticsIdMessage', $('#ganalytics-id-message') );

			self.addElement( 'setParams', $('#set-params') );
			self.addElement( 'setParamsDefault', $('#set-params-default') );
			self.addElement( 'setParamsSave', $('#set-params-save') );
			self.addElement( 'setParamsSaveMessage', $('#set-params-save-message') );

			self.addElement( 'campaignForm', $('#campaignForm') );

			self.addElement('utm_source', $('#utm_source') );
			self.addElement('utm_medium', $('#utm_medium') );
			self.addElement('utm_campaign', $('#utm_campaign') );
			self.addElement('utm_content', $('#utm_content') );

		},
		vars : {},
		addVariable : function( name, varsiable ) {
			this.vars[name] = varsiable;
			return this;
		},
		elem : {},
		addElement : function( name, elem ) {
			this.elem[name] = elem;
			return this;
		},
		objs : {},
		addObject : function( name, obj ) {
			this.objs[name] = obj;
			return this;
		},

		viewActiveOnly : function( elem ) {
			$.postAjax({'submit': 'viewActiveOnly', viewActiveOnly : elem.val()}).done(function(data) {

				if ( data.status )
					location.reload();

			});
		},

		convertCssToInlineStyle : function( elem ) {
			$.postAjax({'submit': 'convertCssToInlineStyle', convertCssToInlineStyle : elem.val()}).done(function(data) {

				if ( data.status )
					location.reload();

			});
		},

		productFriendlyURL : function( elem ) {
			$.postAjax({'submit': 'productFriendlyURL', productFriendlyURL : elem.val()}).done(function(data) {

				if ( data.status )
					location.reload();
			});
		},

		debugMode : function( elem )
		{
			$.postAjax({'submit': 'debugMode', debugMode : elem.val()}).done(function(data) {

				if ( data.status )
					location.reload();
			});
		},

		useCache : function( elem ) 
		{
			$.postAjax({'submit': 'useCache', value : elem.val()}).done(function(data) {

				if ( data.status )
					location.reload();
			});
		},

		runMultimpleTasks : function( elem ) {
			$.postAjax({'submit': 'runMultimpleTasks', runMultimpleTasks : elem.val()}).done(function(data) {

				if ( data.status )
					location.reload();
			});
		},

		displayCustomerAccountSettings : function( elem ) {
			$.postAjax({'submit': 'displayCustomerAccountSettings', 'value' : elem.val()}).done(function(response) {

				if ( response.status )
					location.reload();
			});
		},

		subscribeByCategory : function( elem ) {
			$.postAjax({'submit': 'subscribeByCategory', 'value' : elem.val()}).done(function(response) {

				if ( response.status )
					location.reload();
			});
		},

		subscribeByCListOfInterest : function( elem ) {
			$.postAjax({'submit': 'subscribeByCListOfInterest', 'value' : elem.val()}).done(function(response) {

				if ( response.status )
					location.reload();
			});
		},

		sendNewsletterOnSubscribe : function( elem ) {
			$.postAjax({'submit': 'sendNewsletterOnSubscribe', 'value' : elem.val()}).done(function(response) {

				if ( response.status )
					location.reload();
			});
		},

		forwardingFeatureActive : function( elem ) 
		{
			$.postAjax({'submit': 'forwardingFeatureActive', 'value' : elem.val()}).done(function(response) {

				if ( response.status )
					location.reload();
			});
		},

		sendEmbededImagesActive : function( elem ) 
		{
			$.postAjax({'submit': 'sendEmbededImagesActive', 'value' : elem.val()}).done(function(response) {

				if (response.status)
					location.reload();
				else
					NewsletterPro.alertErrors(response.errors);
			});
		},

		chimpSyncUnsubscribed : function(elem)
		{
			$.postAjax({'submit': 'chimpSyncUnsubscribed', 'value' : elem.val()}).done(function(response) {

				if (response.status)
					location.reload();
				else
					NewsletterPro.alertErrors(response.errors);
			});
		},

		displayOnliActiveProducts : function( elem ) {
			$.postAjax({'submit': 'displayOnliActiveProducts', displayOnliActiveProducts : elem.val()}).done(function(data) {

				if ( data.status )
					location.reload();
			});
		},

		emptyAddedEmails : function() {
			$.postAjax({ 'submit': 'emptyAddedEmails', emptyAddedEmails : true}).done(function(data) {
				if( data.status == true )
					location.reload();
			});
		},

		updateGAnalyticsID : function( elem ) {
			var self = this;
			var val = elem.val();

			$.postAjax({'submit': 'updateGAnalyticsID', updateGAnalyticsID:val}).done(function(data) {
				if(data.status == true ) {
					self.elem.ganalyticsIdMessage.empty().show().append('<span class="success-icon">&nbsp;</span>');
				} else {
					self.elem.ganalyticsIdMessage.empty().show().append('<span class="error-icon">&nbsp;</span>');
				}
				setTimeout( function() { self.elem.ganalyticsIdMessage.hide(); }, 5000);
			});
		},

		checkIfCampaignIsRunning: function(elem) {
			NewsletterPro.showAjaxLoader(elem);
			$.postAjax({'submit': 'checkIfCampaignIsRunning'}).done(function(response) {
				if (response.status) {
					alert(response.msg);
				} else {
					alert(response.errors.join("\n"));
				}
			}).always(function(){
				NewsletterPro.hideAjaxLoader(elem);
			});

		},

		activeGAnalytics : function( elem ) {
			var self = this;
			if( elem.is(':checked') ) {
				$.postAjax({'submit': 'activeGAnalytics', activeGAnalytics:true}).done(function(data) {
					if(data.status == true ) {
						self.elem.ganalyticsId.prop('disabled', false);
					}
				});
			} else {
				$.postAjax({'submit': 'activeGAnalytics', activeGAnalytics:false}).done(function(data) {
					if(data.status == true ) {
						self.elem.ganalyticsId.prop('disabled', true);
					}
				});
			}
		},

		universalAnaliytics: function(elem)
		{
			var self = this;
			if( elem.is(':checked') ) 
				$.postAjax({'submit': 'universalAnaliytics', universalAnaliytics:true});
			else
				$.postAjax({'submit': 'universalAnaliytics', universalAnaliytics:false});
		},

		activeCampaign : function( elem ) {
			var self = this;
			if( elem.is(':checked') ) {
				$.postAjax({'submit': 'activeCampaign', activeCampaign:true}).done(function(data) {
					if(data.status == true ) {
						NewsletterProComponents.objs.selectedProducts.removeItems();
						NewsletterProComponents.objs.productList.element.empty();

						self.elem.utm_source.prop('disabled', false);
						self.elem.utm_medium.prop('disabled', false);
						self.elem.utm_campaign.prop('disabled', false);
						self.elem.utm_content.prop('disabled', false);

						self.elem.setParams.prop('disabled', false);

						if( self.elem.setParamsDefault.hasClass('disabled') )
							self.elem.setParamsDefault.removeClass('disabled');
						self.elem.setParamsDefault.attr('onclick', 'NewsletterProControllers.SettingsController.makeDefaultParameteres();');

						if( self.elem.setParamsSave.hasClass('disabled') )
							self.elem.setParamsSave.removeClass('disabled');
						self.elem.setParamsSave.attr('onclick', 'NewsletterProControllers.SettingsController.saveCampaign();');
					}
				});
			} else {
				$.postAjax({'submit': 'activeCampaign', activeCampaign:false}).done(function(data) {
					if(data.status == true ) {
						NewsletterProComponents.objs.selectedProducts.removeItems();
						NewsletterProComponents.objs.productList.element.empty();

						self.elem.utm_source.prop('disabled', true);
						self.elem.utm_medium.prop('disabled', true);
						self.elem.utm_campaign.prop('disabled', true);
						self.elem.utm_content.prop('disabled', true);

						self.elem.setParams.prop('disabled', true);

						if( !self.elem.setParamsDefault.hasClass('disabled') )
							self.elem.setParamsDefault.addClass('disabled');
						self.elem.setParamsDefault.prop('onclick', false);

						if( !self.elem.setParamsSave.hasClass('disabled') )
							self.elem.setParamsSave.addClass('disabled');
						self.elem.setParamsSave.prop('onclick', false);
					}
				});
			}
		},

		makeDefaultParameteres : function() {
			var self = this;
			$.postAjax({'submit': 'makeDefaultParameteres', makeDefaultParameteres:true}).done(function(data) {
				if(data.status == true ) {
					self.elem.setParams.val(data.params);

					self.elem.utm_source.val(data.campaign.UTM_SOURCE);
					self.elem.utm_medium.val(data.campaign.UTM_MEDIUM);
					self.elem.utm_campaign.val(data.campaign.UTM_CAMPAIGN);
					self.elem.utm_content.val(data.campaign.UTM_CONTENT);
				}
			});
		},

		saveCampaign : function() {
			var self = this;

			var utmSource = self.elem.utm_source;
			var utmMedium = self.elem.utm_medium;
			var utmCampaign = self.elem.utm_campaign;
			var utmContent = self.elem.utm_content;

			utmSource.val( utmSource.val().replace(/[&?]/, '') );
			utmMedium.val( utmMedium.val().replace(/[&?]/, '') );
			utmCampaign.val( utmCampaign.val().replace(/[&?]/, '') );
			utmContent.val( utmContent.val().replace(/[&?]/, '') );

			$.submitAjax( {'submit': 'saveCampaign', name: 'saveCampaign', form: self.elem.campaignForm} ).done(function(data) {
				if ( data.status )
					self.elem.setParamsSaveMessage.empty().show().append('<span class="success-icon">&nbsp;</span>');
				else if( data.status === false )
					self.elem.setParamsSaveMessage.empty().show().append('<span class="error-icon">&nbsp;</span>');

				setTimeout( function() { self.elem.setParamsSaveMessage.hide(); }, 5000);
			});
		},
	},

	TemplateController : 
	{
		init : function() {
			var self = this;

				 // function saveNesletterTemplate 
			self.addElement( 'newsletterTemplateTitle', $('#page-title') )
				.addElement( 'saveNewsletterTemplateMessage', $('#save-newsletter-template-message') )
				 // function viewNewsletterTemplate 
				.addElement( 'viewNewsletterTemplateContent', $('#view-newsletter-template-content') )
				.addElement( 'newsletterTemplateContent', $('#newsletter-template-content') )
				 // function toggleShowProductTpl 
				.addElement( 'productTemplate', $('#product-template') )
				 // function saveProductTemplate 
				.addElement( 'saveProductTemplateMessage', $('#save-product-template-message') )
				 // function viewProductTemplate 
				.addElement( 'productTemplateContent', $('#product-template-content') )
				.addElement( 'viewProductTemplateContent', $('#view-product-template-content') )
				 // function deleteImage 
				.addElement( 'deleteImageMessage', $('#delete-image-message') )
				.addElement( 'deleteImage', $('#images') )
				.addElement( 'deleteImageEmptyShow', $('.images-empty-show') )
				.addElement( 'deleteImageEmptyHide', $('.images-empty-hide') )
				.addElement( 'deleteImageNavigation', $('.images-navigation') )
		},
		vars : {},
		addVariable : function( name, varsiable ) {
			this.vars[name] = varsiable;
			return this;
		},
		elem : {},
		addElement : function( name, elem ) {
			this.elem[name] = elem;
			return this;
		},
		objs : {},
		addObject : function( name, obj ) {
			this.objs[name] = obj;
			return this;
		},

		viewNewsletterTemplate : function() {
			var self = this;
			return $.postAjax({'submit': 'viewNewsletterTemplate', viewNewsletterTemplate: true}, 'html').done(function(data) {
				var content = $(data);
				self.elem.viewNewsletterTemplateContent.show().empty().append(content);

				var createTemplate = NewsletterPro.modules.createTemplate;
				createTemplate.updateBoth();
			});
		}, // end of viewNewsletterTemplate

		saveToggleNewsletterTemplate : function( element ) {
			var self = this;

			var buttonName = element.find('span');

			if( self.elem.newsletterTemplateContent.is(':visible') ) {
				self.saveNewsletterTemplate();
				buttonName.html(element.data('name').edit);

			} else {
				self.elem.newsletterTemplateContent.show();
				self.elem.viewNewsletterTemplateContent.hide();
				buttonName.html(element.data('name').view);

			}
		}, // end of saveToggleNewsletterTemplate

		saveAsNewsletterTemplate : function ( element )
		{
			var self = this;
			var name = prompt(element.data('message'), '');

			if ( name == '' || name == null )
				return false;

			$.postAjax({ 'submit': 'saveAsNewsletterTemplate', saveAsNewsletterTemplate : name, content : tinyMCE.get('newsletter_template_text').getContent(), title : self.elem.newsletterTemplateTitle.val() }).done(function(data) {
					var content = '';
					if ( data.status )
						location.reload();
					else
						content = '<p class="error-save">' + data.msg + '</p>';

					self.elem.saveNewsletterTemplateMessage.show().empty().append(content);
			});
		}, // end of saveAsNewsletterTemplate

		changeNewsletterTemplate : function( element ) {
			var self = this;

			$.postAjax({'submit': 'changeNewsletterTemplate', changeNewsletterTemplate: element.val() }).done(function(data) {
				if ( data.status )
					location.reload();
				else
					self.elem.changeNewsletterTemplateMessage.empty().append( '<span class="error-msg">'+data.msg +'</span>');

				setTimeout( function() { self.elem.changeNewsletterTemplateMessage.hide(); }, 5000);
			});
		}, // end of changeNewsletterTemplate

		changeProductTemplate : function( element ) {
			var self = this;

			$.postAjax({'submit': 'changeProductTemplate', changeProductTemplate: element.val() }).done(function(data) {
				if ( data.status )
					location.reload();
				else
					self.elem.changeProductTemplateMessage.empty().append( '<span class="error-msg">'+data.msg +'</span>');

				setTimeout( function() { self.elem.changeProductTemplateMessage.hide(); }, 5000);
			});

		}, // end of changeProductTemplate

		changeProductImageSize : function( element ) {
			var self = this;

			$.postAjax({'submit': 'changeProductImageSize', changeProductImageSize : element.val()}).done(function(data) {
				if ( data.status == true )
					location.reload();
				else
					self.elem.changeProductImageSizeMessage.empty().append( '<span class="error-msg">'+data.msg +'</span>');

				setTimeout( function() { self.elem.changeProductImageSizeMessage.hide(); }, 5000);
			});
		}, // end of changeProductImageSize

		changeProductCurrency : function ( element ) {
			var self = this;

			$.postAjax({'submit': 'changeProductCurrency', changeProductCurrency : element.val()}).done(function(data) {
				if ( data.status )
					location.reload();
				else
					self.elem.changeProductCurrencyMessage.empty().append( '<span class="error-msg">'+data.msg +'</span>');

				setTimeout( function() { self.elem.changeProductCurrencyMessage.hide(); }, 5000);
			}); 
		}, // end of changeProductCurrency

		changeProductLanguage : function ( id ) {
			$.postAjax({'submit': 'changeProductLanguage', changeProductLanguage : id}).done(function(data) {
				if ( data.status )
					location.reload();
				else
					self.changeProductLanguageMessage.empty().append( '<span class="error-msg">'+data.msg +'</span>');

				setTimeout( function() { self.elem.changeProductLanguageMessage.hide(); }, 5000);
			});
		}, // end of changeProductLanguage

		saveProductNumberPerRow : function( element ) {
			var self = this;

	 		var val = parseInt(element.val());
	 		val = (/^\d{1}$/.test(val)) ? parseInt(val) : 3;
	 		element.val(val);

			$.postAjax({'submit': 'saveProductNumberPerRow', saveProductNumberPerRow: element.val()}).done(function(data) {

				if( data.status == true )
					self.elem.saveProductNumberPerRowMessage.empty().show().append('<span class="success-icon">&nbsp;</span>');
				else
					self.elem.saveProductNumberPerRowMessage.empty().show().append('<span class="error-msg">'+data.msg+'</span>');

				setTimeout( function() { self.elem.saveProductNumberPerRowMessage.hide(); }, 5000);

				if( NewsletterProComponents.objs.selectedProducts !== 'undefined' )	{
					NewsletterProComponents.objs.selectedProducts.columns = element.val();
				}
			});

		}, // end of saveProductNumberPerRow

		toggleShowProductTpl : function( element ) {
			var self = this;
			if ( self.elem.productTemplate.is(':visible') ) {
				self.elem.productTemplate.slideUp('slow');
				element.find('span.text').empty().html(element.data('name').show);
			} else {
				self.elem.productTemplate.css('display', 'inline-block').hide().slideDown('slow');
				element.find('span.text').empty().html(element.data('name').hide);
			}
		}, // end of saveProductNumberPerRow

		saveProductTemplate : function() {
			var self = this;
			var tinyContent;

			if (NewsletterPro.dataStorage.get('is_product_template')) {
				tinyContent = $('#product-template-content-textarea').val();
			} else {
				tinyContent = tinyMCE.get('product_template_text').getContent();
			}

			NewsletterPro.modules.selectProducts.updateProductTemplateView(tinyContent);

			$.postAjax({'submit': 'saveProductTemplate', saveProductTemplate : tinyContent, numberPerRow : NewsletterPro.dataStorage.data.product_tpl_nr }).done(function(data) {

				var content = '';
				if ( data.type )
					content = '<p class="success-save">' + data.message + '</p>';
				else
					content = '<p class="error-save">' + data.message + '</p>';

				self.elem.saveProductTemplateMessage.show().empty().append(content);
				
				NewsletterPro.dataStorage.set('product_template', data.content);
				NewsletterPro.components.Product.changeTemplate(data.content);

			}).fail(function( data ) {
				self.elem.saveProductTemplateMessage.show().empty().append('<p class="error-save">Save failure</p>');
			}).always(function( data ) {
				self.viewProductTemplate().always(function() { self.elem.productTemplateContent.hide(); });
			});
		}, // end of saveProductTemplate

		saveToggleProductTemplate  : function ( element ) 
		{
			var self = this;

			var buttonName = element.find('span');

			if ( self.elem.productTemplateContent.is(':visible') ) {
				self.saveProductTemplate();
				buttonName.html(element.data('name').edit);

			} else {
				self.elem.productTemplateContent.show();
				self.elem.viewProductTemplateContent.hide();
				buttonName.html(element.data('name').view);
			}
		}, // end of toggleProductSaveView

		viewProductTemplate : function() {
			var self = this;
				selectProducts = NewsletterPro.modules.selectProducts;

			$.postAjax({ 'submit': 'getProductContent', getProductContent : true}, 'html').done(function(data) {
				if( NewsletterProComponents.objs.selectedProducts !== 'undefined' )	{
						NewsletterProComponents.objs.selectedProducts.template = data;
				}
			});

			return $.postAjax({ 'submit': 'viewProductTemplate', viewProductTemplate: true}, 'html').done(function(data) {
				self.elem.viewProductTemplateContent.show().empty().append(data + '<div style="clear: both; height: 7px;">&nbsp</div>');
				selectProducts.refreshProducts();
			});
		}, // end of viewProductTemplate

		loadProductTemplate: function() {
			var self = this;

			return $.postAjax({ 'submit': 'getProductContent', getProductContent : true}, 'html').done(function(data) {
				if( NewsletterProComponents.objs.selectedProducts !== 'undefined' )	{
					NewsletterProComponents.objs.selectedProducts.template = data;
				}
			}).promise();
		},

		isProductTemplateLoaded: function() {
			return NewsletterProComponents.objs.selectedProducts.template == '' ? false : true;
		},

		saveAsProductTemplate : function( element ) {
			var self = this;
			var nbProducts = NewsletterPro.modules.selectProducts.vars.nbProducts;
			var name = prompt(element.data('message'), '');
			if ( name == '' || name == null )
				return false;

			var content;

			if (NewsletterPro.dataStorage.get('is_product_template')) {
				content = $('#product-template-content-textarea').val();
			} else {
				content = tinyMCE.get('product_template_text').getContent();
			}

			$.postAjax({ 'submit': 'saveAsProductTemplate', saveAsProductTemplate : name, content : content, numberPerRow : nbProducts.val() }).done(function(data) {
				var content = '';
				if ( data.status ) {
					NewsletterPro.modules.selectProducts.vars.templateDataSource.sync();
					var fullContent = data.full_content;
					NewsletterPro.dataStorage.set('product_template', fullContent);
				}
				else
					content = '<p class="error-save">' + data.msg + '</p>';

				self.elem.saveProductTemplateMessage.show().empty().append(content);
			});

		}, // end of saveAsProductTemplate

		deleteImage : function( element, id ) {
			var self = this;

			$.postAjax({ 'submit': 'deleteImage', deleteImage : id }).done(function(data) {

				if ( data.status ) {
					element.parent().parent().remove();
					if ( self.elem.deleteImage.children().length == 0 ) {
						self.elem.deleteImageEmptyShow.show();
						self.elem.deleteImageEmptyHide.hide();
						self.elem.deleteImageNavigation.hide();
					}
				} else
					self.elem.deleteImageMessage.empty().show().append('<span class="error-msg">'+data.msg+'</span>');

				setTimeout( function() { self.elem.deleteImageMessage.hide(); }, 5000);

			});

		}, // end of deleteImage

		showProductHelp : function() 
		{
			var self = this,
				l = NewsletterProControllers.l;

			if (typeof self.productTemplateWin === 'undefined' ) {

				self.productTemplateWin = new gkWindow({
					width: 640,
					height: 540,
					title: l('view available variables product'),
					className: 'newsletter-help-win',
					show: function(win) {

					},
					close: function(win) {

					},
					content: function(win) {
						$.postAjax({'submit': 'showProductHelp'}, 'html').done(function(response) {
							if (typeof self.showProductHelpContent === 'undefined') {
								self.showProductHelpContent = self.showProductHelpContent || response;
								win.setContent(response);
							}
						});
						return '';
					}
				});
			}

			self.productTemplateWin.show();

		}, // end of showProductHelp

		showNewsletterHelp : function() 
		{
			var self = this,
				l = NewsletterProControllers.l;

			if (typeof self.newsletterTemplateWin === 'undefined' ) {

				self.newsletterTemplateWin = new gkWindow({
					width: 640,
					height: 540,
					title: l('view available variables'),
					className: 'newsletter-help-win',
					show: function(win) {},
					close: function(win) {},
					content: function(win) {
						$.postAjax({'submit': 'showNewsletterHelp'}, 'html').done(function(response) {
							if (typeof self.showNewsletterHelpContent === 'undefined') {
								self.showNewsletterHelpContent = self.showNewsletterHelpContent || response;
								win.setContent(response);
							}
						});
						return '';
					}
				});
			}

			self.newsletterTemplateWin.show();
		}, // end of showNewsletterHelp

	}, // end of TemplateController

	NavigationController : 
	{
		init : function() {
			var self = this,
				l = NewsletterProControllers.l;

			self.addElement( 'categoriesList', $('#categories-list') )
				.addElement( 'categoriesListLi', self.elem.categoriesList.find('li') )
				.addElement( 'productList', $('#product-list') )
				.addElement( 'productListDsiplayImages', $('#display-product-image-container') )
				.addElement( 'productListDsiplayImagesMessage', $('#display-product-image-message') )
				.addElement( 'toggleCategoriesButton', $('#toggle-categories') )
				.addElement( 'productSearch', $('#poduct-search') )
				.addElement( 'addedList', $('#added-list') )

			self.addVariable( 'categoriesListWidth', ( parseFloat(self.elem.categoriesList.width()) / parseFloat(self.elem.categoriesList.parent().width()) ) * 100 + '%' )
				.addVariable( 'productListMarginLeft', ( parseFloat(self.elem.productList.css('margin-left')) / parseFloat(self.elem.productList.parent().css('width')) ) * 100 + '%' )

			if( typeof $.getCookie('toggleCategories') === 'undefined' || $.getCookie('toggleCategories') == 'false' ) {
				self.addVariable( 'categoriesVisibility', true );
			} else {
				self.elem.categoriesList.addClass('categories-list-slide-toggle-hide');
				self.elem.productList.addClass('product-list-slide-toggle-hide');
				self.elem.productListDsiplayImages.css('margin-left', 0);
				self.addVariable( 'categoriesVisibility', false );
				self.elem.toggleCategoriesButton.css('background-position', 'bottom left');
			}

			if( window.location.hash == '#viewImported' ) 
			{
				window.location.hash = '#sendNewsletters';
				self.goToStep( 5, self.elem.addedList );
			}
		},

		vars : {},
		addVariable : function( name, varsiable ) {
			this.vars[name] = varsiable;
			return this;
		},
		elem : {},
		addElement : function( name, elem ) {
			this.elem[name] = elem;
			return this;
		},
		objs : {},
		addObject : function( name, obj ) {
			this.objs[name] = obj;
			return this;
		},

		toggleCategories : function( element ) {
			var self = this;
			var speed = 500;

			if( self.vars.categoriesVisibility == true ) {
				self.elem.categoriesListLi.css('width', self.elem.categoriesListLi.width() + 'px');
				self.elem.categoriesList.css('overflow', 'hidden');
				self.elem.productList.animate({
					'margin-left' : 0,
				}, speed);

				self.elem.productListDsiplayImages.animate({
					'margin-left' : 0,
				}, speed);

				self.elem.categoriesList.animate({
					'width' : '1px',
					'padding-left': '1px'
				}, speed, function() {
					self.vars.categoriesVisibility = false;
					element.css('background-position', 'bottom left');
					$.setCookie('toggleCategories', true);
				});

			} else {
				self.elem.productList.animate({
					'margin-left' : self.vars.productListMarginLeft,
				}, speed);

				self.elem.productListDsiplayImages.animate({
					'margin-left' : self.vars.productListMarginLeft,
				}, speed);

				self.vars.categoriesVisibility = true;

				self.elem.categoriesList.animate({
					'width' : self.vars.categoriesListWidth,
					'padding-left' : 0
				}, speed, function() {
					self.elem.categoriesList.css('overflow', 'visible');
					element.css('background-position', 'top left');
					self.elem.categoriesListLi.css('width', 'auto');
					$.setCookie('toggleCategories', false);
				});
			}

		}, // end of toggleCategories

		displayProductImage : function( element ) {
			var self = this;

			$.postAjax({'submit': 'displayProductImage', displayProductImage: element.prop('checked') }).done(function(data) {
				if( data.status )
					location.reload();
				else
					self.elem.productListDsiplayImagesMessage.empty().show().append('<span class="error-msg">'+data.msg+'</span>');

				setTimeout( function() { self.elem.productListDsiplayImagesMessage.hide(); }, 5000);

			});
		}, // end of displayProductImage

		goToStep : function( step, offset ) {
			offset = offset || $('#content');
			NewsletterProComponents.objs.tabItems.trigger('tab_newsletter_' + step );
			 $('html, body').animate({
					scrollTop: offset.offset().top
				}, 1000);
		},

		viewImported: function()
		{		
			NewsletterPro.modules.sendNewsletters.vars.added.sync().done(function(dataSource){
				NewsletterProComponents.objs.tabItems.trigger('tab_newsletter_5');
				$('html, body').animate({
					scrollTop: parseInt($('#added-list').offset().top) - 120
				}, 1000);
			});
		}

	}, // end of NavigationController

	UpgradeController: {

		init : function() 
		{
			var self = this,
				l = NewsletterProControllers.l;
		},

		execute: function(element)
		{
			var box = NewsletterPro,
				responseBox = $('#update-module-response');

			box.showAjaxLoader(element);
			responseBox.hide().removeClass('error').removeClass('success');
			$.postAjax({'submit': 'updateModule'}).done(function(response) {
				if (response.status)
				{
					var message = response.message.join('<br>'),
						seconds = 5;

					responseBox.addClass('success').show();

					responseBox.html(message.replace('%s', seconds));

					var interval = setInterval(function(){
						responseBox.html(message.replace('%s', --seconds));
						if (seconds <= 0)
						{
							location.reload();
							clearInterval(interval);
						}
					}, 1000);

					element.hide();
				}
				else
				{
					var errors = box.displayAlert(response.errors.join('<br>'));
					responseBox.addClass('error').html(errors).show();
				}

			}).always(function(){
				box.hideAjaxLoader(element);
			});
		}
	}, // end of UpgradeController

	ClearCacheController: {
		init: function()
		{
			var self = this,
				l = NewsletterProControllers.l;
		},

		clear: function(element)
		{
			var box = NewsletterPro;

			$.updateConfiguration('SHOW_CLEAR_CACHE', 0).done(function(response){
				if (!response.success)
					box.alertErrors(response.errors);
				else
					$('#clear-cache-box').hide();
			});
		}
	},
}

NewsletterProControllers.init();
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

NewsletterPro.namespace('modules.ourModules');
NewsletterPro.modules.ourModules = ({
	dom: null,
	init: function(box) {
		var self = this;

		self.ready(function(dom) {

		});

		return self;
	},

	ready: function(func) {
		var self = this;
		$(document).ready(function(){
			self.dom = {

			};
			func(self.dom);
		});
	},

	each: function(array, func) {
		for (var name in array)
			func(array[name], name);
	},

}.init(NewsletterPro));