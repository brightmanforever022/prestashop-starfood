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
	NewsletterPro.namespace('components.Viewport');
	NewsletterPro.components.Viewport = function Viewport(cfg)
	{
		if (!(this instanceof Viewport))
			return new FrontWindow(cfg);

		var self = this;

		this.width = window.innerWidth;
		this.operators = ['<', '<=', '==', '!=', '>', '>='];
		this.defaultOperator = '==';
		this.sizes = ['xs', 'sm', 'md', 'lg'];
	}

	NewsletterPro.components.Viewport.prototype.is = function(size, operator)
	{
		operator = operator || this.defaultOperator;

		var screenSize,
			screenSizeIndex,
			sizeIndex = this.sizes.indexOf(size),
			result = false;

		if (this.sizes.indexOf(size) == -1)
			throw new Error('The operator ' + operator + ' does not exists.')

		if (this.operators.indexOf(operator) == -1)
			throw new Error('The operator ' + operator + ' does not exists.')

		this.width = window.innerWidth;

		if (this.width < 768) 
			screenSize = 'xs';
		else if (this.width >= 768 &&  this.width < 992) 
			screenSize = 'sm';
		else if (this.width >= 992 &&  this.width < 1200) 
			screenSize = 'md';
		else  
			screenSize = 'lg';

		screenSizeIndex = this.sizes.indexOf(screenSize);

		switch(operator)
		{
			case '<':
				result = (screenSizeIndex < sizeIndex);
				break;

			case '<=':
				result = (screenSizeIndex <= sizeIndex);
				break;

			case '==':
				result = (screenSizeIndex == sizeIndex);
				break;

			case '!=':
				result = (screenSizeIndex != sizeIndex);
				break;

			case '>':
				result = (screenSizeIndex > sizeIndex);
				break;

			case '>=':
				result = (screenSizeIndex >= sizeIndex);
				break;
		}

		return result;
	};
}(jQueryNewsletterProNew));