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
	NewsletterPro.namespace('components.SyncNewsletters');
	NewsletterPro.components.SyncNewsletters = function SyncNewsletters(cfg)
	{
		if (!(this instanceof SyncNewsletters))
			return new SyncNewsletters(cfg);

		if (typeof cfg.connection === 'undefined')
			throw new Error('You must setup the connection.');

		var box = NewsletterPro,
			self = this;

		this.l               = box.translations.l(box.translations.components.SyncNewsletters);
		this.cfg             = cfg;
		this.connection      = cfg.connection;
		this.limit           = cfg.connection.limit || 2500;
		this.url             = cfg.connection.url;
		this.data            = cfg.data || {};
		this.syncErrorsLimit = cfg.syncErrorsLimit || 1000;
		this.selectors       = cfg.selectors || {};
		this.refreshRate     = cfg.refreshRate || 5000;

		this.data['limit'] = this.limit;

		this.subscription    = {};
		this.syncErrorsCount = 0;
		this.readyLength     = 0;
		this.sendId          = 0;

		this.syncInterval    = null;
		this.syncStart       = false;
	}

	NewsletterPro.components.SyncNewsletters.prototype.init = function()
	{
		// run the synchronisation
		this.sync();
	};

	NewsletterPro.components.SyncNewsletters.prototype.sync = function()
	{
		if (this.isSyncInProgress())
			return false;

		var self = this,
			sendManager = NewsletterPro.modules.sendManager,
			define = sendManager.define;

		self.publish('beforeRequest');

		this.syncInterval = setInterval(function(){
			self.getEmails(
				// success
				function(response, textStatus, jqXHR)
				{
					self.sendId = (response.id > 0 ? response.id : self.sendId);

					self.publish('emailsToSend', getEmailsToSendPublish(response, false));
					self.publish('emailsSent', getEmailsSentPublish(response, false));
					self.publish('progressbar', getProgressbarPublish(response, false));

					self.publish('syncSuccess', {
						response: response,
						textStatus: textStatus,
						jqXHR: jqXHR
					});

					if (!self.syncStart)
					{
						self.syncStart = true;

						self.publish('syncStart', {
							active: response.active,
							state: response.state,
						});
					}

					if (response.active && define.STATE_PAUSE == response.state)
					{
						self.publish('syncPause', {
							active: response.active,
							state: response.state,
						});

						self.clearSync();
					}
					else if (!response.active || define.STATE_DONE == response.state)
					{
						if (self.sendId)
						{
							$.postAjax({'submit': 'syncNewsletters', 'id': self.sendId, 'limit': self.limit}, 'json', false).done(function(response){

								self.sendId = 0;
								self.publish('emailsToSend', getEmailsToSendPublish(response, true));
								self.publish('emailsSent', getEmailsSentPublish(response, false));
								self.publish('progressbar', getProgressbarPublish(response, true));

								self.publish('syncEnd', {
									active: response.active,
									state: response.state,
								});

							}).fail(function(jqXHR, textStatus, errorThrown){
								addRequestError(jqXHR, textStatus, errorThrown);
							});	
						}

						self.publish('syncDone', {
							active: response.active,
							state: response.state,
						});

						self.clearSync();
					}
					else if (response.active && define.STATE_DEFAULT == response.state)
					{

						self.publish('syncContinue', {
							active: response.active,
							state: response.state,
						});

						sendManager.startSendNewsletters();
					}
				}, 
				// error 
				function(jqXHR, textStatus, errorThrown) 
				{
					addRequestError(jqXHR, textStatus, errorThrown);
				}
			);
		}, this.refreshRate);

		function getProgressbarPublish(response, completed)
		{
			completed = completed || false;

			return {
				errors: response.emails_error,
				success: response.emails_success,
				emailsCount: response.emails_count,
				done: (define.STATE_DONE == response.state),
				completed: completed,
			};
		}

		function getEmailsToSendPublish(response, completed)
		{
			completed = completed || false;

			return {
				remaining: response.remaining,
				emailsToSend: response.emails_to_send,
				completed: completed,
			};
		}

		function getEmailsSentPublish(response, completed)
		{
			completed = completed || false;

			return {
				success: response.emails_success,
				errors: response.emails_error,
				emailsSent: response.emails_sent,
				completed: completed,
			};
		}

		function addRequestError(jqXHR, textStatus, errorThrown)
		{
			self.syncErrorsCount++;

			var message,
				login = (jqXHR.getResponseHeader('Login') === 'true' ? true : false);
				alertErrors = false,
				display = false;

			if (login)
			{
				message = self.l('The login session has expired. You must refresh the browser and login again. The next time when you are login check the button "Stay logged in".');
				alertErrors = true;
				self.clearSync();
			}
			else
			{
				message = self.l('Error ocurred at newsletter synchronisation') + ' : ' + NewsletterPro.getXHRError(jqXHR);
				
				if (self.syncErrorsCount >= self.syncErrorsLimit)
				{
					alertErrors = true;
					self.clearSync();
				}
				else
					display = true;
			}

			self.publish('syncError', {
				message: message,
				alertErrors: alertErrors,
				display: display,
				jqXHR: jqXHR,
				textStatus: textStatus,
				errorThrown: errorThrown,
			});
		}

		return true;
	};

	NewsletterPro.components.SyncNewsletters.prototype.clearSync = function()
	{
		if (this.isSyncInProgress())
		{
			clearInterval(this.syncInterval);
			this.syncInterval = null;
			this.syncStart = false;
			return true;
		}
		return false;
	};

	NewsletterPro.components.SyncNewsletters.prototype.isSyncInProgress = function()
	{
		return (this.syncInterval != null);
	};

	NewsletterPro.components.SyncNewsletters.prototype.getEmails = function(success, error, complete)
	{
		return $.ajax({
			url: this.url,
			type: 'POST',
			dataType: 'json',
			data: this.data,
			success: function(data, textStatus, jqXHR)
			{
				if (typeof success === 'function')
					success(data, textStatus, jqXHR);
			},
			error: function(jqXHR, textStatus, errorThrown)
			{
				if (typeof error === 'function')
					error(jqXHR, textStatus, errorThrown);
			},
			complete: function(jqXHR, textStatus)
			{
				if (typeof complete === 'function')
					complete(jqXHR, textStatus);
			}
		}).promise();
	};

	NewsletterPro.components.SyncNewsletters.prototype.setData = function(name, value)
	{
		this.data[name] = value;
	};

	NewsletterPro.components.SyncNewsletters.prototype.subscribe = function(eventName, func, instance)
	{
		if (!this.subscription.hasOwnProperty(eventName))
			this.subscription[eventName] = [];

		this.subscription[eventName].push({
			func: func,
			instance: instance
		});

		if (this.cfg.hasOwnProperty('subscription') && this.cfg.subscription.hasOwnProperty('ready'))
		{
			if (this.cfg.subscription.ready.indexOf(eventName) != -1)
				this.readyLength++;

			if (this.readyLength == this.cfg.subscription.ready.length)
				this.init();
		}
	};

	NewsletterPro.components.SyncNewsletters.prototype.publish = function(eventName, data)
	{
		if (this.subscription.hasOwnProperty(eventName))
		{
			for (var i = 0; i < this.subscription[eventName].length; i++) {

				var result = (typeof data === 'function' ? data() : data);
				var func = this.subscription[eventName][i].func;
				var instance = this.subscription[eventName][i].instance || this;
				func.call(instance, result);
			}
		}
	};


}(jQueryNewsletterProNew));