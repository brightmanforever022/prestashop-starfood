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
	NewsletterPro.namespace('modules.myAccount');
	NewsletterPro.modules.myAccount = ({
		box: null,
		dom: null,
		init: function(box) {
			var self = this;

			self.box = box;

			function getCategories(content) {
				var categories = content.find('input[type="checkbox"]');
				if (categories.length > 0)
					return categories;
				return [];
			}

			function getSelected(categoryTree) {
				var values = [];
				if (typeof categoryTree !== 'array') {
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

			self.ready(function(dom){
				var categoryTree;
	
				dom.submit.click(function(){
					categoryTree = categoryTree || getCategories(dom.categoryTree);
					var selected = getSelected(categoryTree);
				});
			});

			return this;
		},

		ready: function(func) {
			var self = this;

			$(document).ready(function(){
				self.dom = {
					categoryTree: $('#category-tree'),
					submit: $('input[name="submitNewsletterProSettings"]')
				};

				func(self.dom);
			});
		},

	}.init(NewsletterPro));
}(jQueryNewsletterProNew));