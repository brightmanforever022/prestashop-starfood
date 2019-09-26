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

<script id="task-template" type="text/template" style="display: none;">
	<div>
		<div class="new-task-box">
			<div class="form-group clearfix">
				<label class="control-label">{l s='You have selected' mod='newsletterpro'} <strong><span id="selected_emails_count">0</span></strong> {l s='emails' mod='newsletterpro'}</label>
			</div>

			<div class="form-group clearfix">
				<div class="form-inline">
					<label class="control-label pull-left">{l s='Send one newsletter at' mod='newsletterpro'}</label>
					<input id="task-sleep" class="form-control pull-left text-center task-sleep" type="number" step="1" value="{$email_sleep|intval}">
					<label class="control-label aona-seconds pull-left">{l s='seconds' mod='newsletterpro'}</label>
				</div>
			</div>
			
			<div class="form-group clearfix">
				<label class="control-label col-sm-4"><span class="label-tooltip">{l s='Date' mod='newsletterpro'}</span></label>
				<div class="col-sm-8">
					<input type="text" id="task-datepicker" class="form-control task-datepicker">
				</div>
			</div>


			<div class="form-group clearfix">
				<label class="control-label col-sm-4"><span class="label-tooltip">{l s='Template' mod='newsletterpro'}</span></label>
				<div class="col-sm-8">
					<div class="clearfix task-new-smtp"> 
						<select autocomplete="off" id="task-select-template" class="float-left gk-smtp-select gk-select">
							{foreach $newsletter_template_list as $template}
								<option value="{$template.filename|escape:'html':'UTF-8'}" {if $template.selected == true} selected="selected" {/if}>{$template.name|replace:'.html':''}</option>
							{/foreach}
						</select>
					</div>
				</div>
			</div>

			<div class="form-group clearfix">
				<label class="control-label col-sm-4">{l s='Send method' mod='newsletterpro'}</label>
				<div class="col-sm-8">
					<div class="radio">
						<label class="in-win" for="task-mail-method">
							<input id="task-mail-method" type="radio" name="task-send-method" value="mail" checked="checked">
							{l s='Function mail()' mod='newsletterpro'}
						</label>
					</div>
					<div class="radio">
						<label class="in-win" for="task-smtp-method">
							<input id="task-smtp-method" type="radio" name="task-send-method" value="smtp">
							{l s='SMTP configuration' mod='newsletterpro'}
						</label>
					</div>
				</div>
			</div>

			<div id="div-task-smtp-select" class="form-group clearfix" style="display: none;">
				<label class="control-label col-sm-4"><span class="label-tooltip">{l s='Select SMTP' mod='newsletterpro'}</span></label>
				<div class="col-sm-8 task-new-smtp">
					<select id="task-smtp-select"></select>
				</div>
			</div>
			
			<div class="form-group clearfix">
				<label class="control-label col-sm-4"><span class="label-tooltip">{l s='Send a test' mod='newsletterpro'}</span></label>
				<div class="col-sm-8">
					<input id="task-email-test" class="form-control task-email-test" type="text" value="{$shop_email|escape:'html':'UTF-8'}">
				</div>
			</div>

			<div class="form-group clearfix">
				<div class="col-sm-8 col-sm-offset-4">
					<div class="form-inline">
						<div class="form-group">
							<div id="task-test-email-lang-select"></div>
						</div>
						<div class="form-group">
							<a href="javascript:{}" id="task-smtp-test" class="btn btn-default pull-left task-smtp-test">
								<span class="btn-ajax-loader" style="display: none;"></span>
								<i class="icon icon-envelope"></i> {l s='Send a test' mod='newsletterpro'}
							</a>
						</div>
					</div>
				</div>
			</div>

			<div class="clearfix">
				<div id="task-smtp-test-message" class="col-sm-12 task-smtp-test-message"></div>
			</div>
			
			<div class="clearfix">
				<a href="javascript:{}" id="add-task" class="btn btn-default pull-right">
					<i class="icon icon-plus-square"></i>
					{l s='Add Task' mod='newsletterpro'}
				</a>
			</div>

		</div>
	</div>
</script>

<script id="add-new-email-template" type="text/template" style="display: none;">
	<div>
		<div>
			<form id="add-new-email-from" class="add-new-email-from" method="POST">
				<div class="clearfix">
					<div class="form-group clearfix">
						<label class="control-label col-sm-4">
							<span class="label-tooltip">{l s='First Name' mod='newsletterpro'}</span>
						</label>
						<div class="col-sm-8">
							<input class="form-control" type="text" name="firstname">
						</div>
					</div>

					<div class="form-group clearfix">
						<label class="control-label col-sm-4">
							<span class="label-tooltip">{l s='Last Name' mod='newsletterpro'}</span>
						</label>
						<div class="col-sm-8">
							<input class="form-control" type="text" name="lastname">
						</div>
					</div>


					<div class="form-group clearfix">
						<label class="control-label col-sm-4">
							<span class="label-tooltip">{l s='Email' mod='newsletterpro'}</span>
						</label>
						<div class="col-sm-8">
							<input class="form-control" type="text" name="email">
						</div>
					</div>


					<div class="form-group clearfix">
						<label class="control-label col-sm-4">
							<span class="label-tooltip">{l s='Shop' mod='newsletterpro'}</span>
						</label>
						<div class="col-sm-8">
							<select class="gk-select" name="id_shop">
								{foreach $shops as $shop}
									<option value="{$shop.value|escape:'html':'UTF-8'}">{$shop.title|escape:'html':'UTF-8'}</option>
								{/foreach}
							</select>
						</div>
					</div>


					<div class="form-group clearfix">
						<label class="control-label col-sm-4">
							<span class="label-tooltip">{l s='Language' mod='newsletterpro'}</span>
						</label>
						<div class="col-sm-8">
							<select class="gk-select" name="id_lang">
								{foreach $languages as $lang}
									<option value="{$lang.id_lang|escape:'html':'UTF-8'}" {if $default_lang == $lang.id_lang} selected="selected" {/if}>{$lang.name|escape:'html':'UTF-8'}</option>
								{/foreach}
							</select>
						</div>
					</div>

				</div>
				<a id="add-new-email-action" class="btn btn-default add-new-email-action" href="javascript:{ldelim}{rdelim}">
					<i class="icon icon-plus-square"></i>
					{l s='Add' mod='newsletterpro'}
				</a>
				<div id="add-new-email-error" class="error-msg" style="margin: 0; float: none; display: inline-block;">
				</div>
			</form>
			<div class="clear" style="clear: both;"></div>
		</div>
	</div>
</script>

<script id="list-of-interest-template" type="text/template" style="display: none;">
	<div>
		<div id="list-of-interest-template-add" class="list-of-interest-template-add">
			<div class="form-group clearfix input-list">
				<div class="col-sm-6 text-left">
					<label class="control-label"><span class="label-tooltip">{l s='Name' mod='newsletterpro'}</span></label>
				</div>
				<div class="col-sm-6">
					<div id="add-new-fs-langs" class="pull-right add-new-fs-langs gk_lang_select"></div>
				</div>
			</div>
			<div class="form-group clearfix">
				{foreach $all_active_languages as $lang}
					<input data-lang="{$lang.id_lang|escape:'html':'UTF-8'}" name="loi_input_{$lang.id_lang|escape:'html':'UTF-8'}" type="text" class="form-control" style="{if $lang.id_lang == $default_lang}display: block;{else}display: none;{/if}">
				{/foreach}
			</div>
			<div class="clearfix">
				<a href="javascript:{}" id="add-loi-button" class="btn btn-default add-button"><i class="icon icon-plus-square"> </i> {l s='Add' mod='newsletterpro'}</a>
			</div>
		</div>

		<div id="list-of-interest-template-update" class="list-of-interest-template-update">
			<div class="form-group clearfix input-list">
				
				<div class="form-group clearfix">
					<div class="col-sm-6 text-left">
						<div class="row">
							<label class="control-label"><span class="label-tooltip">{l s='Name' mod='newsletterpro'}</span></label>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="row">
							<div id="update-fs-langs" class="update-fs-langs gk_lang_select" style="float: right;"></div>
						</div>
					</div>
				</div>

				<div class="form-group clearfix">
					{foreach $all_active_languages as $lang}
						<input data-lang="{$lang.id_lang|escape:'html':'UTF-8'}" name="loi_input_update_{$lang.id_lang|escape:'html':'UTF-8'}" type="text" class="form-control" style="{if $lang.id_lang == $default_lang}display: block;{else}display: none;{/if}">
					{/foreach}
				</div>
			</div>

			<div class="clearfix">
				<div class="form-inline">
					<div class="form-group">
						<label class="control-label" style="padding: 0;"><span class="label-tooltip">{l s='Position' mod='newsletterpro'}</span></label>
						<input id="loi-position" name="loi_position" type="text" class="form-control text-center fixed-width-xs position">
					</div>
					<div class="form-group pull-right">
						<a href="javascript:{}" id="update-loi-button" class="btn btn-default update-button"><i class="icon icon-save"> </i> {l s='Save' mod='newsletterpro'}</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</script>

<!-- this is in use -->
<div id="voucher-alert-box" class="voucher-alert-box" style="display: none;"> </div>