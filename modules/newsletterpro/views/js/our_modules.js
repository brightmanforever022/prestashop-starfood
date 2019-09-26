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