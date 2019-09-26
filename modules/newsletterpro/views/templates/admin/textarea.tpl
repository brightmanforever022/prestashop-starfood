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

<div {if isset($content_name)}id="{$content_name|escape:'html':'UTF-8'}"{/if} style="display:none;">
	<textarea cols="100" rows="10" type="text" id="{$input_name|escape:'html':'UTF-8'}" name="{$input_name|escape:'html':'UTF-8'}" class="{if isset($class_name)}{$class_name|escape:'html':'UTF-8'}{else}autoload_rte{/if}" style="width: 950px; height: 500px;">{$input_value|htmlentitiesUTF8}</textarea>
	<span class="counter" max="{if isset($max)}{$max|escape:'html':'UTF-8'}{else}none{/if}"></span>
</div>

<script type="text/javascript">
	NewsletterPro.dataStorage.append('tiny_init', {
		'content_name': '{if isset($content_name)}{$content_name|escape:'html':'UTF-8'}{/if}',
		'input_name': '{$input_name|escape:'html':'UTF-8'}',
		'class_name': '{$class_name|escape:'html':'UTF-8'}',
		'config': '{if isset($config)}{$config|escape:'html':'UTF-8'}{else}default_config{/if}',
		'content_css': {if isset($content_css) && is_array($content_css)}$.parseJSON('{$content_css|json_encode}'){elseif !empty($content_css)}'{$content_css|escape:'quotes':'UTF-8'}'{else}null{/if},
		'multilang': false,
		'id_lang': 0,
		'init_callback': {if isset($init_callback)}'{$init_callback|escape:'quotes':'UTF-8'}'{else}null{/if},
		'plugins': {if isset($plugins)}'{$plugins|escape:'quotes':'UTF-8'}'{else}null{/if},
	});
</script>