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
	NewsletterPro.namespace('components.FrontWindow');
	NewsletterPro.components.FrontWindow = function FrontWindow(cfg)
	{
		if (!(this instanceof FrontWindow))
			return new FrontWindow(cfg);

		var selfStatic     = NewsletterPro.components.FrontWindow,
			self           = this,
			selector       = cfg.selector,
			className	   = cfg.className,
			defaultContent = getDefaultContent(selector),
			dom            = buildTemplate(selector),
			closeDisabled  = false;

		selfStatic.addInstance(selector.selector, self);

		if (typeof cfg.content === 'function')
			setContent(cfg.content(this));
		else if ($.trim(defaultContent) != '')
			setContent(defaultContent);

		resize();

		// add window events
		$(window).resize(function(){
			resize();
		});

		$.each([dom.bg, dom.cross], function(i, item){
			item.click(function(event){
				event.preventDefault();
				event.stopPropagation();
				self.close();
			});
		});

		// public functions
		this.show = function show(content)
		{
			if (typeof content !== 'undefined')
				setContent(content);

			selector.fadeIn('fast');

			resize();

			if (typeof cfg.show === 'function')
				cfg.show(self);

		};

		this.open = function open(content)
		{
			self.show(content);
		};

		this.hide = function hide()
		{
			selector.fadeOut('fast');
		};

		this.close = function close()
		{
			if (typeof cfg.close === 'function')
				cfg.close(self);

			if (!isCloseDisabled())
				this.hide();
		};

		this.setContent = function setContent(html)
		{
			setContent(html);
		};

		this.disableClose = function disableClose()
		{
			closeDisabled = true;
		};

		this.enableClose = function enableClose()
		{
			closeDisabled = false;
		};

		// this is only for preview in backoffice
		if (selector.hasClass('in-preview'))
		{
			this.disableClose();
			selector.css({
				'position': 'relative',
			});

			dom.body.css({
				'margin-bottom': dom.body.css('top'),
			});
			this.show();
		}

		// private functions
		function buildTemplate(selector)
		{
			var crossClassName = NewsletterPro.dataStorage.get('configuration.CROSS_TYPE_CLASS');

			var bg      = $('<div class="gk-front-window-bg"> </div>');
			var cross   = $('<span class="'+crossClassName+' np-cross"></span>');
			var body    = $('<div class="gk-front-window '+(typeof cfg.className !== 'undefined' ? cfg.className : '')+'"></div>');
			var content = $('<div class="gk-front-window-content gk-front-window-scrollbar"></div>');

			selector.addClass('gk-front-window-box');

			bg.css({
				'width': '100%',
				'height': '100%',
			});

			selector.append(bg);
			body.append(cross);
			body.append(content);
			selector.append(body);
			selector.hide();

			return {
				bg: bg,
				cross: cross,
				body: body,
				content: content,
				selector: selector,
			};
		}

		function resizeCondition(fullHeight, windowHeight, scrollHeight)
		{
			return fullHeight >= windowHeight && scrollHeight >= dom.content.height();
		}

		function fixSize()
		{
			var windowHeight = $(window).height(),
				children = dom.content.children(),
				scrollHeight = children.height(),
				bodyTop = parseInt(dom.body.css('top')),
				bodyPadding = dom.body.outerHeight() - dom.body.height(),
				bodyHeight = scrollHeight + bodyPadding,
				fullHeight = bodyHeight + (2 * bodyTop),
				minTop = 15;

			if (resizeCondition(fullHeight, windowHeight, scrollHeight))
			{
				setTop(minTop);

				dom.content.css({
					'height': windowHeight - bodyPadding - (2 * minTop),
					'overflow-y': 'scroll',
				});

				if (!resizeCondition(fullHeight, windowHeight, scrollHeight))
				{
					var center = (windowHeight - bodyHeight) / 2;
					var top = (center > cfg.top ? top : center);

					setTop(top);

					dom.content.css({
						'height': 'auto',
						'overflow-y': 'hidden',
					});
				}
			}
			else
			{

				setTop(cfg.top);

				dom.content.css({
					'height': 'auto',
					'overflow-y': 'hidden',
				});
			}
		}

		function getDefaultContent(selector)
		{
			var content = selector.html();
			selector.empty();
			return content;
		}

		function setContent(html)
		{
			dom.content.html(html);
		}

		function convertValueToPixel(value, windowWidth)
		{
			if (/\%/.test(value))
			{
				percentValue = parseInt(value);
				value = percentValue * windowWidth / 100; 
			}
			else if (/px/.test(value))
				value = parseInt(value);

			return value;
		}

		function setWidth(value)
		{
			if (typeof value !== 'undefined')
			{
				var windowWidth = $(window).width();

				value = convertValueToPixel(value, windowWidth);

				var maxWidth = null,
					minWidth = null;

				if (typeof cfg.maxWidth !== 'undefined' || cfg.maxWidth != null)
					maxWidth = convertValueToPixel(cfg.maxWidth, windowWidth);

				if (typeof cfg.minWidth !== 'undefined' || cfg.minWidth != null)
					minWidth = convertValueToPixel(cfg.minWidth, windowWidth);

				if (maxWidth != null && value >= maxWidth)
					value = maxWidth;
				else if (minWidth != null && value <= minWidth)
					value = minWidth;

				dom.body.css({
					'width': value / windowWidth * 100 + '%'
				});

				var marginLeft = ((((windowWidth - value)/2)/windowWidth)*100) + '%';
				var marginRight = marginLeft;

				dom.body.css({
					'margin-left': marginLeft,
					'margin-right': marginRight,
				});
			}
		}

		function setHeight(value)
		{
			if (typeof value !== 'undefined')
			{
				dom.body.height(value);
			}
		}

		function centerWindow()
		{
			dom.body.css({
				'top': (($(window).height() - dom.body.height()) / 2 ) + 'px'
			});
		}

		function setTop(value)
		{
			dom.body.css({
				'top': value + 'px'
			});	
		}

		function resize()
		{
			setWidth(cfg.width);
			setHeight(cfg.height);

			if (issetTop())
				setTop(cfg.top);
			else 
				centerWindow();

			fixSize();
		}

		function issetTop()
		{
			if (typeof cfg.top === 'undefined')
				return false;
			return true;
		}

		function isCloseDisabled()
		{
			return closeDisabled;
		}
	};

	NewsletterPro.components.FrontWindow.instances = {};

	NewsletterPro.components.FrontWindow.addInstance = function(id, value)
	{
		var instances = NewsletterPro.components.FrontWindow.instances;
		instances[id] = value;
	};

	NewsletterPro.components.FrontWindow.getInstanceById = function(id)
	{
		var instances = NewsletterPro.components.FrontWindow.instances;

		if (instances.hasOwnProperty(id))
			return instances[id];
		return false;
	};

	NewsletterPro.components.FrontWindow.getInstances = function()
	{
		return NewsletterPro.components.FrontWindow.instances;
	};

}(jQueryNewsletterProNew));