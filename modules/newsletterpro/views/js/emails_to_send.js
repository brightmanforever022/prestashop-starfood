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
	NewsletterPro.namespace('components.EmailsToSend');
	NewsletterPro.components.EmailsToSend = function EmailsToSend(cfg)
	{
		if (!(this instanceof EmailsToSend))
			return new EmailsToSend(cfg);

		if (typeof cfg.selector === 'undefined')
			throw new Error('You must define a selector.')
		
		var box  = NewsletterPro
			self = this;

		this.l = box.translations.l(box.translations.components.EmailsToSend);
		this.selector        = cfg.selector;
		this.fastPerformance = (typeof cfg.fastPerformance === 'undefined' ? true : cfg.fastPerformance);
		this.emails          = [];
		this.remainingEmails = 0;

		if (typeof cfg.subscription === 'undefined')
			throw new Error('You must set the subscription value.')

		this.subscription       = cfg.subscription;

		this.dom 			 = this.initDom();
		this.items           = [];

		box.extendSubscribeFeature(this);
		box.subscription(this, this.subscription);
	}

	NewsletterPro.components.EmailsToSend.prototype.sync = function(response)
	{
		var remaining = response.remaining,
			emailsToSend = response.emailsToSend,
			completed = response.completed;

		// this function need to be optimized

		if (this.fastPerformance)
			this.clearOptimized(emailsToSend);
		else
		{
			this.clear();
			this.createItems(emailsToSend);
		}

		this.writeRemainingEmails(remaining);
	};

	NewsletterPro.components.EmailsToSend.prototype.clear = function()
	{
		this.writeRemainingEmails(0);
		this.emails = [];
		this.items  = [];
		this.dom.list.empty();
	};

	NewsletterPro.components.EmailsToSend.prototype.writeRemainingEmails = function(value)
	{
		this.remainingEmails = parseInt(value);
		this.dom.remainingEmails.html(this.remainingEmails);
	};


	NewsletterPro.components.EmailsToSend.prototype.initDom = function()
	{
		this.selector.html(this.getTemplate());

		return {
			selector: this.selector,
			remainingEmails: this.selector.find('.emails-to-send-count'),
			list: this.selector.find('.userlist'),
		};
	};

	NewsletterPro.components.EmailsToSend.prototype.createItems = function(emails)
	{
		this.emails = emails;

		for(var i = 0; i < this.emails.length; i++) {
			this.createItem(this.emails[i]);
		}
	};

	NewsletterPro.components.EmailsToSend.prototype.createItemsOptimised = function(emailsData)
	{

		var dfd = new $.Deferred();

		for (var i = 0; i < emailsData.length; i++)
		{
			this.emails.push(emailsData[i]);
			this.createItem(emailsData[i], 'append');

			// this are inversed, there are not like createItem
			if (i == 0)
				this.publish('firstItemCreated', this.getItemByEmail(emailsData[i]));

			if (i == emailsData.length - 1)
			{
				this.publish('lastItemCreated', this.getItemByEmail(emailsData[i]));
				dfd.resolve();
			}
		}

		return dfd.promise();
	};

	NewsletterPro.components.EmailsToSend.prototype.createItem = function(email, creationMethod)
	{
		var creationMethod = creationMethod || 'append', // append or prepend
			className = this.getNextClassName(creationMethod);

		return ({
			template: null,
			parent: null,
			className: className,
			email: email,
			creationMethod: creationMethod,
			init: function(parent)
			{
				this.parent = parent;
				this.template = this.getItemTemplate();
				this.add();

				if (creationMethod === 'append')
					parent.items.push(this);
				else
					parent.items.unshift(this);	
			},
			add: function()
			{
				this.parent.dom.list[this.creationMethod](this.template);
			},
			render: function()
			{
				if (!this.parent.dom.list.find('[data-email="'+this.email+'"]'))
					this.add();
			},
			remove: function()
			{
				this.template.remove();
				var index = this.parent.emails.indexOf(this.email);
				if (index != -1)
				{
					this.parent.emails.splice(index, 1);
					this.parent.items.splice(index, 1);
					return true;
				}
				return false;

			},
			getItemTemplate: function()
			{
				// classes "odd" or "even"
				return $('\
					<li class="'+this.className+'" data-email="'+this.email+'">\
						<span class="email_text">'+this.email+'</span>\
						<span>&nbsp;</span>\
					</li>\
				');
			},
			getClassName: function()
			{
				return this.className;
			}
		}.init(this));
	};

	NewsletterPro.components.EmailsToSend.prototype.clearOptimized = function(newEmails)
	{
		var self = this,
			addEmails = [],
			removeEmails = [],
			oldEmails = self.emails;

		for(var i = 0; i < newEmails.length; i++)
		{
			if (oldEmails.indexOf(newEmails[i]) == -1)
				addEmails.push(newEmails[i]);
		}

		for(var i = 0; i < oldEmails.length; i++)
		{
			if (newEmails.indexOf(oldEmails[i]) == -1)
				removeEmails.push(oldEmails[i]);
		}

		self.removeEmails(removeEmails).done(function(){
			self.createItemsOptimised(addEmails);
		});
	};

	NewsletterPro.components.EmailsToSend.prototype.removeEmails = function(emails)
	{
		var dfd = new $.Deferred();

		var indexes = [];
		for(var i = 0; i < emails.length; i++)
		{
			var index = this.emails.indexOf(emails[i]);
			if (index != -1)
				indexes.push(index);
		}

		indexes.sort();

		if (indexes.length)
		{
			for(var i = indexes.length - 1; i >= 0; i--)
			{
				var item = this.items[indexes[i]];

				if (typeof item !== 'undefined')
					item.remove();


				if (i == 0)
					dfd.resolve();
			}
		}
		else
			dfd.resolve();

		return dfd.promise();
	};

	NewsletterPro.components.EmailsToSend.prototype.getNextClassName = function(creationMethod)
	{
		var className,
			len = this.items.length,
			creationMethod = creationMethod || 'append';

		if (len)
		{
			if (creationMethod == 'append')
				className = this.items[len - 1].getClassName();
			else
				className = this.items[0].getClassName();
		}
		else
			className = 'even';

		return (className == 'odd' ? 'even' : 'odd');
	};

	NewsletterPro.components.EmailsToSend.prototype.getTemplate = function()
	{
		return $('\
			<label class="control-label">'+this.l('Emails to send:')+' <span class="emails-to-send-count">0</span> '+this.l('remaining')+'</label>\
			<div class="clear">&nbsp;</div>\
			<ul class="userlist"></ul>\
			<div class="clear">&nbsp;</div>\
		');
	};

	NewsletterPro.components.EmailsToSend.prototype.getIndex = function(email)
	{
		return this.emails.indexOf(email);
	};

	NewsletterPro.components.EmailsToSend.prototype.getItemByEmail = function(email)
	{
		var index = this.getIndex(email);
		if (index != -1)
			return this.items[index];
		return false;
	};

	NewsletterPro.components.EmailsToSend.prototype.removeItemByEmail = function(email)
	{
		var item = this.getItemByEmail(email);
		if (item)
			return item.remove();
		return false;
	};
}(jQueryNewsletterProNew));