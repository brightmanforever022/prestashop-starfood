{*
* This is file is tpl code of first step to create an invoice. Do not edit the file if you want to upgrade in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2016 Globo ., Jsc
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}
<div class="row" id="template_box">
    <div class="bootstrap">
        <div class="col-lg-12">
            {if isset($templates) && $templates}
                {foreach $templates as $key=>$template}
                  <div class="col-xs-6 col-md-3 template_item">
                    <div class="input-group">
                      <label class="template_wp">
                          <input  type="radio" name="choose_design" value="{$key|escape:'htmlall':'UTF-8'}" />
                          <img  class="thumbnail" style="max-width:100%;" src="{$module_dir|escape:'htmlall':'UTF-8'}{$template.thumbnail|escape:'htmlall':'UTF-8'}" alt=""/>
                      </label>
                    </div> 
                  </div>          
              {/foreach}
          {/if}
    </div>
</div>