{**
* OrderEdit
*
* @category  Module
* @author    silbersaiten <info@silbersaiten.de>
* @support   silbersaiten <support@silbersaiten.de>
* @copyright 2015 silbersaiten
* @version   1.0.0
* @link      http://www.silbersaiten.de
* @license   See joined file licence.txt
*}
{$href|escape:'html':'UTF-8'|@parse_url|@print_r}
<a class="pointer" title="{$action|escape:'html':'UTF-8'}" onclick="if (confirm('{$confirm|escape:'html':'UTF-8'}')) document.location = '{$location_ok|escape:'html':'UTF-8'}'; else document.location = '{$location_ko|escape:'html':'UTF-8'}';">
	<img src="../img/admin/duplicate.png" alt="{$action|escape:'html':'UTF-8'}" /> !!!!!
</a>