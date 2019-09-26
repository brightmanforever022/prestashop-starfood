{if $product}	
<span>{l s='Today Deal' mod='leocustomajax'}</span>
<ul class="deal-clock lof-clock-{$product.id_product}-detail list-inline">
	{if isset($product.js) && $product.js == 'unlimited'}{/if}
</ul>
{if isset($product.js) && $product.js != 'unlimited'}	
	<script type="text/javascript">
		{literal}
		jQuery(document).ready(function($){{/literal}
			var text_d = '{l s='Day' mod='leocustomajax'}';
			var text_h = '{l s='Hours' mod='leocustomajax'}';
			var text_m = '{l s='Mins' mod='leocustomajax'}';
			var text_s = '{l s='Secs' mod='leocustomajax'}';
			$(".lof-clock-{$product.id_product}-detail").lofCountDown({literal}{{/literal}
				TargetDate:"{$product.js.month}/{$product.js.day}/{$product.js.year} {$product.js.hour}:{$product.js.minute}:{$product.js.seconds}",
				DisplayFormat:'<li>%%D%% <br/>'+text_d+'</li><li>%%H%% <br/>'+text_h+'</li><li>%%M%% <br/>'+text_m+'</li><li>%%S%% <br/>'+text_s+'</li>',
				FinishMessage: "{$product.finish}"
			{literal}
			});
		});
		{/literal}
	</script>
{/if}
{/if}