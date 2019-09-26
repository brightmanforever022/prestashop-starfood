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