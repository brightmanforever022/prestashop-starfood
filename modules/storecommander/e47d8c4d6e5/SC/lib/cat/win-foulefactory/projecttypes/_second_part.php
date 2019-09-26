<br/>
<div class="div_form">
    <div class="form_label">
        <?php echo _l('If the result is undefined')?>*
    </div>
    <select id="undefined">
        <option value="" <?php if(empty($params["undefined"])) echo "selected"; ?>><?php echo _l('No action'); ?></option>
        <option value="remove" <?php if(!empty($params["undefined"]) && $params["undefined"]=="remove") echo "selected"; ?>><?php echo _l('Remove the product from its category'); ?></option>
        <option value="subcat" <?php if(!empty($params["undefined"]) && $params["undefined"]=="subcat") echo "selected"; ?>><?php echo _l('Associate the product to category').' "'.$project->name.'/'._l('Undefined').'"'; ?></option>
    </select>
</div>
<?php if(!empty($enabled_sources)) { ?>
<div class="div_form">
    <div class="form_label">
        <?php echo _l('Source')?>*
    </div>
    <?php
    $sources = explode("-",trim(trim($project->source,"-")));
    foreach($enabled_sources as $id=>$name) { ?>
        <input type="checkbox" class="chk" id="source_<?php echo $id; ?>" value="1" <?php if(in_array($id,$sources)) echo "checked"; ?> style="" /> <?php echo $name; ?>
    <?php } ?>
</div>
<?php } else { ?>
    <input type="hidden" id="source_none" value="none" />
<?php } ?>
<br/>

<div class="div_form" style="width: 800px;">
    <br/>
    <button style="float: right;" class="btn <?php echo ($project->status=="created"?"clickable":(in_array($project->status, array("configured","to_pay"))?"darkgrey clickable":"lightgrey")); ?>" id="btn_save">1/ <?php echo _l('Save'); ?></button>
    <br/><?php
    $nb_pdt = 0;
    $cat = new Category((int)$project->id_category);
    $nb = $cat->getProducts($id_lang,1,1,null,null,true,false);
    if(!empty($nb))
        $nb_pdt = $nb;
    ?><?php echo "<strong>"._l('Number of products to process:')."</strong> ".$nb_pdt; ?>
    <?php if($project->status=="created" || $project->status=="configured" || $project->status=="to_pay") { ?>
    <div style="border: 1px solid #57aed1;color: #57aed1; background: #e4f7ff; padding: 10px; margin: 10px;">
        <?php echo _l('Associate products in the category "FouleFactory" > "').$project->name._l('" to update the list of products to be handled.')?>
    </div>
    <?php } ?>
</div>