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

NewsletterPro.namespace('modules.sendManager');
NewsletterPro.modules.sendManager = ({
	define: {
		STATE_DEFAULT: 0,
		STATE_PAUSE: 1,
		STATE_IN_PROGRESS: 2,
		STATE_DONE: 3,
	},
	dom: null,
	box: null,
	vars: {},
	events: {},
	showErrorTimer: null,
	init: function(box) 
	{
		var self = this,
			l,
			controllers,
			syncNewsletters,
			emailsToSend,
			emailsSent,
			sendProgressbar,
			stopWasHit = false,
			startWasHit = false;

		this.ready(function(dom){
			var sleep = Number(box.dataStorage.get('email_sleep')) * 1000,
				defaultRefreshRate = 3000,
				refreshRate = (sleep > defaultRefreshRate ? sleep : defaultRefreshRate);

			l = self.l = box.translations.l(box.translations.modules.sendManager);
			controllers = NewsletterProControllers;

			syncNewsletters = new box.components.SyncNewsletters({
				syncErrorsLimit: 250,

				connection: {
					url: box.getUrl({'submit':'syncNewsletters'}),
					limit: 250, // 250 should be the default value
					data: {},
					refreshRate: refreshRate,
				},
				subscription: {
					ready: ['emailsToSend', 'emailsSent', 'progressbar']
				}
			});

			self.addVar('syncNewsletters', syncNewsletters);

			emailsToSend = new box.components.EmailsToSend({
				selector: dom.emailsToSend,
				fastPerformance: true,
				subscription: [
					[syncNewsletters, 'emailsToSend', 'sync']
				],
			});

			self.addVar('emailsToSend', emailsToSend);

			emailsSent = new box.components.EmailsSent({
				selector: dom.emailsSent,
				fastPerformance: true,
				subscription: [
					[syncNewsletters, 'emailsSent', 'sync']
				],
			});

			self.addVar('emailsSent', emailsSent);

			sendProgressbar = new box.components.SendProgressbar({
				selector: $('#send-progressbar'),
				subscription: [
					[syncNewsletters, 'progressbar', 'sync']
				]
			});

			self.addVar('sendProgressbar', sendProgressbar);

			syncNewsletters.subscribe('syncStart', function(response){
				stateSyncStart();
			});

			syncNewsletters.subscribe('syncPause', function(response){
				stateSyncPause();
			});

			syncNewsletters.subscribe('syncDone', function(response){
				stateSyncDone();
				box.modules.task.ui.components.sendHistory.sync();
			});

			syncNewsletters.subscribe('syncContinue', function(response){

			});

			syncNewsletters.subscribe('syncSuccess', function(response) {

			});

			syncNewsletters.subscribe('syncError', function(response){
				var message = response.message,
					alertErrors = response.alertErrors,
					display = response.display;

				if (display)
					self.showError(message, 10000); // 10000
				else if (alertErrors)
				{
					self.showError(message, 0);
					box.alertErrors(message);
				}
			});

			syncNewsletters.subscribe('syncEnd', function(response){
				if (stopWasHit)
				{
					stopWasHit = false;
					clearProgress();
				}

			});

			syncNewsletters.subscribe('beforeRequest', function(){
				if (startWasHit)
				{
					syncNewsletters.setData('getLastId', true);
					startWasHit = false;
				}
				else
					syncNewsletters.setData('getLastId', false);
			});


			emailsSent.subscribe('lastItemCreated', function(item){

			});

			emailsSent.subscribe('firstItemCreated', function(item){
				if (item && item.errors.length)
					self.showError(item.errors, 7000);
			});

			dom.startSendNewsletters.on('click', function(){
				clearProgress();

				var result = controllers.SendController.prepareEmails();

				if (typeof result === 'undefined')
					return;

				var buttonsState = getButtonsState();
				stateSyncStart();

				result.done(function(response){
					if (!response.status)
						restoreButtonsState(buttonsState);
					else
					{
						startWasHit = true;
					}
				}).fail(function(){
					restoreButtonsState(buttonsState);
				});
			});

			dom.pauseSendNewsletters.on('click', function(){
				var buttonsState = getButtonsState();

				stateSyncPause();
				self.pauseSendNewsletters().done(function(response){
					if (!response.success)
						restoreButtonsState(buttonsState);
				}).fail(function(){
					restoreButtonsState(buttonsState);
				});
			});

			dom.stopSendNewsletters.on('click', function(){
				var buttonsState = getButtonsState();

				stateSyncDone();
				self.stopSendNewsletters().done(function(response){
					if (!response.success)
						restoreButtonsState(buttonsState);
					else
					{
						stopWasHit = true;
						clearProgress();
						box.modules.task.ui.components.sendHistory.sync();
					}
				}).fail(function(){
					restoreButtonsState(buttonsState);
				});
			});

			dom.continueSendNewsletters.on('click', function(){
				var buttonsState = getButtonsState();

				stateSyncStart();

				self.continueSendNewsletters(true).done(function(response){
					if (!response.success)
						restoreButtonsState(buttonsState);
				}).fail(function(){
					restoreButtonsState(buttonsState);
				});
			});

			function clearProgress()
			{
				emailsToSend.clear();
				emailsSent.clear();
				sendProgressbar.clear();
				self.hideError();
				self.step3(false);
			}

			function stateSyncStart()
			{
				sendProgressbar.setPause(false);

				dom.pauseSendNewsletters.show();
				dom.stopSendNewsletters.show();

				dom.continueSendNewsletters.hide();
				dom.startSendNewsletters.hide();

				dom.newTask.hide();
				dom.prevStep.hide();
				self.step3(true);
			}

			function stateSyncPause()
			{
				sendProgressbar.setPause(true);

				dom.continueSendNewsletters.show();
				dom.stopSendNewsletters.show();

				dom.pauseSendNewsletters.hide();
				dom.startSendNewsletters.hide();

				dom.newTask.hide();
				dom.prevStep.hide();
				self.step3(false);
			}

			function stateSyncDone()
			{
				sendProgressbar.setPause(false);

				dom.startSendNewsletters.show();
				dom.newTask.show();
				dom.prevStep.show();

				dom.stopSendNewsletters.hide();
				dom.pauseSendNewsletters.hide();
				dom.continueSendNewsletters.hide();
				self.step3(false);

			}

			function getButtonsState()
			{
				return {
					startSendNewsletters: dom.startSendNewsletters.is(':visible'),
					stopSendNewsletters: dom.stopSendNewsletters.is(':visible'),
					pauseSendNewsletters: dom.pauseSendNewsletters.is(':visible'),
					continueSendNewsletters: dom.continueSendNewsletters.is(':visible'),
					prevStep: dom.prevStep.is(':visible'),
					newTask: dom.newTask.is(':visible'),
				}
			}

			function restoreButtonsState(buttonsState)
			{
				for(var key in buttonsState)
				{
					var visible = buttonsState[key];
					if (visible)
						dom[key].show();
					else
						dom[key].hide();
				}
			}
		});

		return self;
	},

	requireConnection: function(response)
	{
		var self = this;
		if (response.hasOwnProperty('require_connection') && response.require_connection)
		{
			setTimeout(function(){
				self.continueSendNewsletters();
			}, 2500);
			return true;
		}
		return false;
	},

	step3: function(bool)
	{
		var box = NewsletterPro,
			self = this;

		if (bool)
			self.dom.step3.html('<i class="icon icon-refresh icon-spin"></i>');
		else
			self.dom.step3.html('3');
	},

	connectionAvailable: function(func)
	{
		$.postAjax({'submit': 'connectionAvailable'}, 'json', false).done(function(bool){
			func(bool);
		});
	},

	startSendNewsletters: function(trigger)
	{
		var box = NewsletterPro,
			self = this;

		trigger = typeof trigger === 'undefined' ? 0 : Number(trigger);

		self.vars.syncNewsletters.sync();

		return $.postAjax({'submit': 'startSendNewsletters', 'trigger': trigger}, 'json', false).done(function(response){

			if (!response.success)
				self.showError(response.errors);

			self.requireConnection(response);

		}).fail(function(jqXHR, textStatus, errorThrown){
			self.showError(self.l('Error') + ' : ' + box.getXHRError(jqXHR));
		}).promise();
	},
	
	continueSendNewsletters: function(trigger)
	{
		var box = NewsletterPro,
			self = this;

		trigger = typeof trigger === 'undefined' ? 0 : Number(trigger);

		self.vars.syncNewsletters.sync();

		return $.postAjax({'submit': 'continueSendNewsletters', 'trigger': trigger}, 'json', false).done(function(response){

			if (!response.success)
				self.showError(response.errors);

			self.requireConnection(response);

		}).fail(function(jqXHR, textStatus, errorThrown){
			self.showError(self.l('Error') + ' : ' + box.getXHRError(jqXHR));
		}).promise();
	},

	pauseSendNewsletters: function()
	{
		var box = NewsletterPro,
			self = this;

		return $.postAjax({'submit': 'pauseSendNewsletters'}).done(function(response){

			if (!response.success)
				box.alertErrors(response.errors);
		}).promise();
	},

	stopSendNewsletters: function()
	{
		var box = NewsletterPro,
			self = this;

		return $.postAjax({'submit': 'stopSendNewsletters'}).done(function(response){
			if (!response.success)
				box.alertErrors(response.errors);
		}).promise();
	},

	/**
	 * Show an error on the screen
	 * @param  {[string]} msg       
	 * @param  {[int]} displayTime Time in miliseconds
	 */
	showError: function(msg, displayTime)
	{
		var self = this,
			dom = self.dom;

		if (typeof msg !== 'string')
			msg = msg.join('<br>');

		dom.lastSendErrorDiv.show();
		dom.lastSendError.show().html(msg);

		if (typeof displayTime !== 'undefined')
		{
			if (self.showErrorTimer != null)
				clearTimeout(self.showErrorTimer);

			if (displayTime === 0)
				return;

			self.showErrorTimer = setTimeout(function(){
				self.hideError();
			}, displayTime);
		}
	},

	hideError: function()
	{
		var self = this,
			dom = self.dom;

		dom.lastSendErrorDiv.hide();
		dom.lastSendError.hide().html('');
	},

	addEvent: function(name, value) {
		this.events[name] = value;
	},

	triggerEvent: function(name) {
		if (this.events.hasOwnProperty(name) && typeof this.events[name] == 'function')
			this.events[name]();
	},

	addVar: function(name, value) {
		this.vars = this.vars || {};
		this.vars[name] = value;
	},

	ready: function(func) 
	{
		var self = this;
		$(document).ready(function(){

			self.dom = self.dom || {
				startSendNewsletters: $('#send-newsletters'),
				stopSendNewsletters: $('#stop-send-newsletters'),
				pauseSendNewsletters: $('#pause-send-newsletters'),
				continueSendNewsletters: $('#continue-send-newsletters'),
				prevStep: $('#previous-send-newsletters'),
				newTask: $('#new-task'),

				emailsToSend: $('#emails-to-send'),
				emailsSent: $('#emails-sent'),

				lastSendErrorDiv: $('#last-send-error-div'),
				lastSendError: $('#last-send-error'),
				step3: $('#np-step-3')
			};

			func(self.dom);
		});
	},

}.init(NewsletterPro));