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