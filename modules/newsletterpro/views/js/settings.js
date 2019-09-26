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