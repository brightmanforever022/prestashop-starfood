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