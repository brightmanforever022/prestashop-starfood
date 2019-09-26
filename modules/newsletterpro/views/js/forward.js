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