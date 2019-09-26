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
	NewsletterPro.namespace('components.EmailsSent');
	NewsletterPro.components.EmailsSent = function EmailsSent(cfg)
	{
		if (!(this instanceof EmailsSent))
			return new EmailsSent(cfg);

		if (typeof cfg.selector === 'undefined')
			throw new Error('You must define a selector.')
		
		var box  = NewsletterPro
			self = this;

		this.l               = box.translations.l(box.translations.components.EmailsSent);
		this.selector        = cfg.selector;
		this.fastPerformance = (typeof cfg.fastPerformance === 'undefined' ? true : cfg.fastPerformance);
		this.emailsData      = [];
		this.emails          = [];
		this.sentSuccess     = 0;
		this.sentErrors      = 0;

		if (typeof cfg.subscription === 'undefined')
			throw new Error('You must set the subscription value.')

		this.subscription       = cfg.subscription;

		this.dom 			 = this.initDom();
		this.items           = [];

		box.extendSubscribeFeature(this);
		box.subscription(this, this.subscription);

		this.windowErrors = new gkWindow({
			width: 640,
			height: 400,
			setScrollContent: 340,
			title: this.l('Sent errors'),
			className: 'np-emails-sent-window-error',
			show: function(win) {},
			close: function(win) {},
			content: function(win) 
			{
				return '';
			}
		});
	}

	NewsletterPro.components.EmailsSent.prototype.sync = function(response)
	{
		var success = response.success,
			errors = response.errors,
			emailsSent = response.emailsSent,
			completed = response.completed;

		if (this.fastPerformance)
			this.clearOptimized(emailsSent);
		else
		{
			this.clear();
			this.createItems(emailsSent);
		}

		this.writeSentSuccess(success);
		this.writeSentErrors(errors);
	};

	NewsletterPro.components.EmailsSent.prototype.clear = function()
	{
		this.writeSentSuccess(0);
		this.writeSentErrors(0);
		this.emailsData = [];
		this.emails     = [];
		this.items      = [];
		this.dom.list.empty();
	};

	NewsletterPro.components.EmailsSent.prototype.clearOptimized = function(newItems)
	{
		var self = this,
			addItems = [],
			removeEmails = [],
			newEmails = [],
			oldEmails = self.emails;

		for(var i = 0; i < newItems.length; i++)
		{
			newEmails.push(newItems[i].email);

			if (oldEmails.indexOf(newItems[i].email) == -1)
				addItems.push(newItems[i]);
		}

		for(var i = 0; i < oldEmails.length; i++)
		{
			if (newEmails.indexOf(oldEmails[i]) == -1)
				removeEmails.push(oldEmails[i]);
		}

		if (newEmails.length > 0)
		{
			self.removeItems(removeEmails).done(function(){
				self.createItemsOptimised(addItems);
			});
		}
	};

	NewsletterPro.components.EmailsSent.prototype.writeSentSuccess = function(value)
	{
		this.sentSuccess = parseInt(value);
		this.dom.sentSuccess.html(this.sentSuccess);
	};

	NewsletterPro.components.EmailsSent.prototype.writeSentErrors = function(value)
	{
		this.sentErrors = parseInt(value);
		this.dom.sentErrors.html(this.sentErrors);
	};

	NewsletterPro.components.EmailsSent.prototype.initDom = function()
	{
		this.selector.html(this.getTemplate());

		return {
			selector: this.selector,
			sentSuccess: this.selector.find('.emails-sent-count-succ'),
			sentErrors: this.selector.find('.emails-sent-count-err'),
			list: this.selector.find('.userlist'),
		};
	};

	NewsletterPro.components.EmailsSent.prototype.removeItems = function(emails)
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

	NewsletterPro.components.EmailsSent.prototype.createItems = function(emailsData)
	{
		this.emailsData = emailsData;

		for(var i = 0; i < this.emailsData.length; i++) 
		{
			var data = this.emailsData[i];
			this.emails.push(data.email);
			this.createItem(data);

			if (i == 0)
				this.publish('firstItemCreated', this.getItemByEmail(this.emailsData[i].email));

			if (i == this.emailsData.length - 1)
				this.publish('lastItemCreated', this.getItemByEmail(this.emailsData[i].email));
		}
	};

	NewsletterPro.components.EmailsSent.prototype.createItemsOptimised = function(emailsData)
	{
		var dfd = new $.Deferred();

		for (var i = emailsData.length - 1; i >= 0; i--)
		{
			this.emails.unshift(emailsData[i].email);
			this.emailsData.unshift(emailsData[i]);

			this.createItem(emailsData[i], 'prepend');

			if (i == emailsData.length - 1)
				this.publish('firstItemCreated', this.getItemByEmail(emailsData[i].email));

			if (i == 0)
			{
				this.publish('lastItemCreated', this.getItemByEmail(emailsData[i].email));
				dfd.resolve();
			}
		}

		return dfd.promise();
	};

	NewsletterPro.components.EmailsSent.prototype.createItem = function(data, creationMethod)
	{
		var creationMethod = creationMethod || 'append', // append or prepend
			className      = this.getNextClassName(creationMethod),
			email          = data.email,
			status         = data.status,
			fwd            = data.fwd,
			errors         = data.errors;

		return ({
			template: null,
			parent: null,
			className: className,
			email: email,
			status: status,
			fwd : fwd,
			errors: errors,
			creationMethod: creationMethod,
			init: function(parent)
			{
				var self = this;

				this.parent = parent;
				this.template = this.getItemTemplate();

				this.btnErrors = this.template.find('.np-emails-sent-error');

				this.btnErrors.on('click', function(){
					if (self.errors.length)
					{
						var msg = '';
							msg += '<span style="font-weight: bold;">' + self.email + '</span> <br>';
							msg += '<br>' + self.errors.join('<br>');

						self.parent.windowErrors.setContent(msg);
						self.parent.windowErrors.show();
					}
				});

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
					this.parent.emailsData.splice(index, 1);
					return true;
				}
				return false;

			},
			getItemTemplate: function()
			{
				var successIcon = '<span class="list-action-enable action-enabled"><i class="icon icon-check"></i></span>',
					errorIcon = '<span class="list-action-enable action-disabled"><i class="icon icon-remove"></i></span>';

				return $('\
					<li class="'+this.className+'" data-email="'+this.email+'">\
						<span class="np-emails-sent-email">' + this.email + '</span>\
						<span class="status np-emails-sent-status">\
						'+(this.status ? successIcon : errorIcon)+'\
						</span>\
						<a href="javascript:{}" class="np-emails-sent-error" style="'+(this.errors.length > 0 ? 'display: inline-block;' : 'display: none;')+'"></a>\
						<span class="np-emails-sent-fwd" style="'+(this.fwd > 0 ? 'display: inline-block;' : 'display: none;')+'"> + ' + this.fwd + ' '+this.parent.l('forwarders')+'</span>\
						<div class="clear" style="clear: both;"></div>\
					</li>\
				');
			},
			getClassName: function()
			{
				return this.className;
			}
		}.init(this));
	};

	NewsletterPro.components.EmailsSent.prototype.getNextClassName = function(creationMethod)
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

	NewsletterPro.components.EmailsSent.prototype.getTemplate = function()
	{
		return $('\
			<label class="control-label">'+this.l('Emails sent:')+' <span class="emails-sent-count-succ" style="color: green;">0</span> ' + this.l('sent') + ', <span class="emails-sent-count-err" style="color: red;">0</span> ' + this.l('errors') + '</label>\
			<div class="clear">&nbsp;</div>\
			<ul class="userlist"></ul>\
			<div class="clear">&nbsp;</div>\
		');
	};

	NewsletterPro.components.EmailsSent.prototype.getIndex = function(email)
	{
		return this.emails.indexOf(email);
	};

	NewsletterPro.components.EmailsSent.prototype.getItemByEmail = function(email)
	{
		var index = this.getIndex(email);
		if (index != -1)
			return this.items[index];
		return false;
	};

	NewsletterPro.components.EmailsSent.prototype.removeItemByEmail = function(email)
	{
		var item = this.getItemByEmail(email);
		if (item)
			return item.remove();
		return false;
	};
}(jQueryNewsletterProNew));