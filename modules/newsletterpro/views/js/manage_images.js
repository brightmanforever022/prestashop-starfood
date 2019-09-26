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