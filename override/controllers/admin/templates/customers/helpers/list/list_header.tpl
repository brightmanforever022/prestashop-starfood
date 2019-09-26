{extends file="themes/default/template/controllers/customers/helpers/list/list_header.tpl"}
{block name="preTable"}

<div id="searchByLetterPanel">
<div class="btn-group" role="group" aria-label="Search" data-toggle="buttons">
{foreach $searchbar_letters as $letter}
{if isset($search_char_selected) && ($search_char_selected == $letter)}
{$input_checked=true}
{else}
{$input_checked=false}
{/if}
	<label class="btn {if $input_checked}btn-primary active{else}btn-default{/if}">
	<input {if $input_checked}checked="checked"{/if} type="radio" name="search_char" value="{$letter}" class="search-by-letter" data-char="{$letter}"> {$letter}
	</label>
{/foreach}
	<label class="btn btn-default">
	<input type="radio" name="search_char" value="-" class="search-by-letter"> *
	</label>

</div>
</div>
<br><br>


<script>
$(function(){
	$('#searchByLetterPanel').on('change', 'input.search-by-letter', function(){
		$(this).parents('form').submit();
	});
});
</script>

{/block}
