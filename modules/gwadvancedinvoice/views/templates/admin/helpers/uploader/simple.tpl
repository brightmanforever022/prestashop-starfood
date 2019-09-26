{*
* Do not edit the file if you want to upgrade the module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2016 Globo ., Jsc
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}

{if isset($max_files) && $files|count >= $max_files}
<div class="row">
	<div class="alert alert-warning">{l s='You have reached the limit (%s) of files to upload, please remove files to continue uploading' sprintf=$max_files  mod='gwadvancedinvoice'}</div>
</div>
{else}
<div class="form-group">
	<div class="col-sm-6">
		<input id="{$id|escape:'htmlall':'UTF-8'}" type="file" name="{$name|escape:'htmlall':'UTF-8'}"{if isset($multiple) && $multiple} multiple="multiple"{/if} class="hide" />
		<div class="dummyfile input-group">
			<span class="input-group-addon"><i class="icon-file"></i></span>
			<input id="{$id|escape:'htmlall':'UTF-8'}-name" type="text" name="filename" readonly />
			<span class="input-group-btn">
				<button id="{$id|escape:'htmlall':'UTF-8'}-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
					<i class="icon-folder-open"></i> {if isset($multiple) && $multiple}{l s='Add files' mod='gwadvancedinvoice'}{else}{l s='Add file' mod='gwadvancedinvoice'}{/if}
				</button>
				{if (!isset($multiple) || !$multiple) && isset($files) && $files|count == 1 && isset($files[0].download_url)}
				<a href="{$files[0].download_url|escape:'htmlall':'UTF-8'}">
					<button type="button" class="btn btn-default">
						<i class="icon-cloud-download"></i>
						{if isset($size)}{l s='Download current file (%skb)' sprintf=$size  mod='gwadvancedinvoice'}{else}{l s='Download current file'  mod='gwadvancedinvoice'}{/if}
					</button>
				</a>
				{/if}
			</span>
		</div>
	</div>
</div>
<script type="text/javascript">
{if isset($multiple) && isset($max_files)}
	var {$id|escape:'htmlall':'UTF-8'}_max_files = {$max_files - $files|count};
{/if}
	$(document).ready(function(){
		$('#{$id|escape:'htmlall':'UTF-8'}-selectbutton').click(function(e) {
			$('#{$id|escape:'htmlall':'UTF-8'}').trigger('click');
		});
		$('#{$id|escape:'htmlall':'UTF-8'}-name').click(function(e) {
			$('#{$id|escape:'htmlall':'UTF-8'}').trigger('click');
		});
		$('#{$id|escape:'htmlall':'UTF-8'}-name').on('dragenter', function(e) {
			e.stopPropagation();
			e.preventDefault();
		});
		$('#{$id|escape:'htmlall':'UTF-8'}-name').on('dragover', function(e) {
			e.stopPropagation();
			e.preventDefault();
		});
		$('#{$id|escape:'htmlall':'UTF-8'}-name').on('drop', function(e) {
			e.preventDefault();
			var files = e.originalEvent.dataTransfer.files;
			$('#{$id|escape:'htmlall':'UTF-8'}')[0].files = files;
			$(this).val(files[0].name);
		});
		$('#{$id|escape:'htmlall':'UTF-8'}').change(function(e) {
			if ($(this)[0].files !== undefined)
			{
				var files = $(this)[0].files;
				var name  = '';
				$.each(files, function(index, value) {
					name += value.name+', ';
				});
				$('#{$id|escape:'htmlall':'UTF-8'}-name').val(name.slice(0, -2));
			}
			else // Internet Explorer 9 Compatibility
			{
				var name = $(this).val().split(/[\\/]/);
				$('#{$id|escape:'htmlall':'UTF-8'}-name').val(name[name.length-1]);
			}
		});
		if (typeof {$id|escape:'htmlall':'UTF-8'}_max_files !== 'undefined')
		{
			$('#{$id|escape:'htmlall':'UTF-8'}').closest('form').on('submit', function(e) {
				if ($('#{$id|escape:'htmlall':'UTF-8'}')[0].files.length > {$id|escape:'htmlall':'UTF-8'}_max_files) {
					e.preventDefault();
					alert('{l s='You can upload a maximum of %s files'|sprintf:$max_files  mod='gwadvancedinvoice'}');
				}
			});
		}
	});
</script>
{/if}