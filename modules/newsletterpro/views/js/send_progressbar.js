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
	NewsletterPro.namespace('components.SendProgressbar');
	NewsletterPro.components.SendProgressbar = function SendProgressbar(cfg)
	{
		if (!(this instanceof SendProgressbar))
			return new SendProgressbar(cfg);

		var box  = NewsletterPro,
			self = this,
			winTimer = null;

		if (!cfg.hasOwnProperty('selector'))
			throw new Error('You must define the selector.');

		this.selector = cfg.selector;

		if (!cfg.hasOwnProperty('subscription'))
			throw new Error('You must set the subscription value.')

		this.subscription = cfg.subscription;

		this.percent     = 0;
		this.sent        = 0;
		this.total       = 0;
		this.sentErrors  = 0;
		this.sentSuccess = 0;
		this.pause       = false;

		box.subscription(this, this.subscription);

		this.selector.html(getTemplate());

		this.dom = {
			selector: this.selector,
			bg: this.selector.find('.np-send-progressbar-bg'),
			bar: this.selector.find('.np-send-progressbar-bar'),
			percent: this.selector.find('.np-send-progressbar-percent'),
			totalBox: this.selector.find('.np-send-progressbar-total-box'),
			totalLeft: this.selector.find('.np-send-progressbar-total-left'),
			totalRight: this.selector.find('.np-send-progressbar-total-right'),
			errors: this.selector.find('.np-send-progressbar-error'),
			success: this.selector.find('.np-send-progressbar-success'),
			errSuccBox: this.selector.find('.np-send-progressbar-err-succ-box'),
		};

		this.selector.addClass('np-send-progressbar');


		$(window).resize(function(){

			if (winTimer !== null)
				clearTimeout(winTimer);

			winTimer = setTimeout(function(){
				self.refresh();
			}, 500);
		});
	}

	NewsletterPro.components.SendProgressbar.prototype.clear = function()
	{
		this.setPercent(0);
		this.setTotal(0);
		this.setSent(0);
		this.setSentErrors(0);
		this.setSentSuccess(0);
	};

	NewsletterPro.components.SendProgressbar.prototype.sync = function(response)
	{	
		var errors = Number(response.errors),
			success = Number(response.success),
			emailsCount = Number(response.emailsCount);
			done = response.done,
			completed = response.completed,
			total = Number(errors + success);

		if (!done || completed)
			this.set(total, emailsCount, errors, success);
	};

	NewsletterPro.components.SendProgressbar.prototype.set = function(sent, total, errors, success)
	{
		if (sent > total)
			sent = total;

		var percent = Number(sent / total) * 100;
		this.setPercent(percent);
		this.setTotal(total);
		this.setSent(sent);
		this.setSentErrors(errors);
		this.setSentSuccess(success);
	};

	NewsletterPro.components.SendProgressbar.prototype.setPause = function(value)
	{
		this.pause = Boolean(value);

		if (this.isPause())
			this.dom.bar.addClass('np-send-progressbar-bar-pause');
		else
			this.dom.bar.removeClass('np-send-progressbar-bar-pause');
	};

	NewsletterPro.components.SendProgressbar.prototype.isPause = function()
	{
		return this.pause;
	};

	NewsletterPro.components.SendProgressbar.prototype.get = function()
	{
		return this.percent;
	};

	NewsletterPro.components.SendProgressbar.prototype.refresh = function()
	{
		this.setPercent(this.percent);
	};

	NewsletterPro.components.SendProgressbar.prototype.setPercent = function(value)
	{
		var self = this;

		this.percent = value;
		this.dom.percent.html(parseFloat(value).toFixed(2)+'%');

		var selectorWidth = self.selector.width(),
			percentWidth = self.dom.percent.width(),
			leftPadding = 10,
			percentPercent = ((percentWidth + leftPadding) / selectorWidth) * 100,
			percentLeft = value - percentPercent;

		self.dom.percent.animate({
			'left': (percentLeft) + '%'
		}, {
			progress: function()
			{
				percentProgress();
			},

			complete: function()
			{
				percentProgress();
			},
			queue: false,
		});

		var totalBoxPadding = (leftPadding / selectorWidth) * 100,
			totalBoxMarginLeft = (percentWidth <= 5 ? 5 : 0);

		self.dom.totalBox.animate({
			'left': (value + totalBoxPadding) + '%',
			'margin-left': totalBoxMarginLeft + 'px',
		}, {
			queue: false,
		});

		this.dom.bar.animate({
			'width': value+'%'
		}, {
			progress: function() 
			{
				setPositions();
			},

			complete: function()
			{
				setPositions();
			},
			queue: false,
		});

		function percentProgress()
		{
			if (percentLeft > 0)
			{
				self.dom.percent.css({
					'opacity': 1
				});
			}
			else
			{
				self.dom.percent.css({
					'opacity': 0
				});
			}
		}

		function setPositions()
		{
			// hide elements on the screen
			var errSuccPosition = self.dom.errSuccBox.position().left,
				errSuccWidth = self.dom.errSuccBox.width(),
				totalPosition = self.dom.totalBox.position().left + self.dom.totalBox.width(),
				selectorWidth = self.selector.width(),
				barWidth = self.dom.bar.width(),
				leftSpace = selectorWidth - barWidth;

			if (totalPosition >= errSuccPosition)
			{
				self.dom.totalBox.css({
					'opacity': 0
				});
			}
			else
			{
				self.dom.totalBox.css({
					'opacity': 1
				});
			}

			if (leftSpace <= errSuccWidth)
			{
				var percentLeft = self.dom.percent.position().left,
					errSuccWidth = self.dom.errSuccBox.width(),
					errSuccBoxLeft = ((percentLeft - errSuccWidth - 10) / selectorWidth) * 100;

				self.dom.errSuccBox.css({
					'right': 'initial',
					'left': errSuccBoxLeft + '%',
				});
			}
			else
			{
				self.dom.errSuccBox.css({
					'right': '5px',
					'left': 'initial',
				});
			}
		}
	};

	NewsletterPro.components.SendProgressbar.prototype.setTotal = function(value)
	{
		this.total   = value;
		this.dom.totalRight.html(value);
	};

	NewsletterPro.components.SendProgressbar.prototype.setSent = function(value)
	{
		this.sent    = value;
		this.dom.totalLeft.html(value);
	};

	NewsletterPro.components.SendProgressbar.prototype.setSentErrors = function(value)
	{
		this.sentErrors = value;
		this.dom.errors.html(value);
	};

	NewsletterPro.components.SendProgressbar.prototype.setSentSuccess = function(value)
	{
		this.sentSuccess = value;
		this.dom.success.html(value);
	};



	function getTemplate()
	{
		return $('\
			<div class="np-send-progressbar-bg"></div>\
			<div class="np-send-progressbar-bar"></div>\
			<div class="np-send-progressbar-percent"></div>\
			<div class="np-send-progressbar-total-box">\
				<span><span class="np-send-progressbar-total-left">0</span> <span>/</span> <span class="np-send-progressbar-total-right">0</span></span>\
			</div>\
			<div class="np-send-progressbar-err-succ-box">\
				<div class="np-send-progressbar-error">0</div>\
				<div class="np-send-progressbar-success">0</div>\
				<div class="clear" style="clear: both;"></div>\
			</div>\
		');
	}
}(jQueryNewsletterProNew));