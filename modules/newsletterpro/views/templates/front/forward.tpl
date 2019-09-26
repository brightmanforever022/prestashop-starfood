{*
* 2013-2015 Ovidiu Cimpean
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright 2013-2015 Ovidiu Cimpean
* @version   Release: 4
*}

{capture name=path}
<span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>{l s='Forward'  mod='newsletterpro'}
{/capture}

<div id="newsletterpro-forward" class="box clearfix">
{include file="$tpl_dir./errors.tpl"}

	<div id="ajax-errors" class="alert alert-danger" style="display: none;"></div>
	<div class="clear"></div>
	<div id="ajax-success" class="alert alert-success" style="display: none;"></div>
	<div class="clear"></div>

	{if isset($dispalyForm) && dispalyForm == true}
	<div id="dispalyForm">
		<h1 class="page-subheading">{l s='Forward newsletter' mod='newsletterpro'}</h1>
		<p class="info-title">
			<span>{l s='Make sure your friends receive our newsletters the next time when we will send them.' mod='newsletterpro'}</span>
			<br>
			<span id="fwd-limit" class="fwd-limit">{l s='You can add %s friends emails for forwarding.' mod='newsletterpro'}</span>
		</p>

		<div id="fwd-left-side" class="col-sm-6 left-side" style="display: none;">
			<label>{l s='Add your friend email address:' mod='newsletterpro'}</label>
			<div id="emails-list" class="emails-list">
				<div class="required form-group">
					<input id="first-email" class="validate form-control" data-validate="isEmail" type="text" name="email_{$fwd_limit|intval}" value="">
				</div>
			</div>
			<a href="javascript:{}" id="add-new-email" class="btn btn-default button button-small">
				<span>{l s='Add New Email' mod='newsletterpro'}</span>
			</a>
		</div>
		<div id="fwd-right-side" class="col-sm-6 right-side" style="display: none;">
			<label>{l s='You have forward the newsletter to:' mod='newsletterpro'}</label>
			<div>
				<table id="friends-emails-list" class="table friends-emails-list"></table>
			</div>
		</div>
		<script type="text/javascript">
			NewsletterPro.dataStorage.add('fwdLimit', '{$fwd_limit|intval}');
			NewsletterPro.dataStorage.add('ajaxLink', '{$ajax_link|escape:'quotes':'UTF-8'}');
			NewsletterPro.dataStorage.add('translations', {
				'ajax request error' : "{l s='An error occurred at the ajax request!' mod='newsletterpro'}"
			});
			NewsletterPro.dataStorage.add('emailsJs', $.parseJSON('{$emails_js|escape:'quotes':'UTF-8'}'));
		</script>
	{/if}
	</div>
	<div class="clear"></div>
</div>

