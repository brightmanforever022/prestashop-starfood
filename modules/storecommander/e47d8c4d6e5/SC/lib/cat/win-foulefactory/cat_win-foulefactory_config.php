<?php

if($user_lang_iso=="fr")
require_once (dirname(__FILE__)."/".$user_lang_iso.'.php');

if(!empty($_POST["form_ff_config"]))
{
    $yourid=(Tools::getValue('yourid'));
    $yourapikey=(Tools::getValue('yourapikey'));

    SCI::updateConfigurationValue("SC_FOULEFACTORY_ID", $yourid);
    SCI::updateConfigurationValue("SC_FOULEFACTORY_APIKEY", $yourapikey);

    $params = array();

    $sources = "-";
    if(!empty($_POST["source_shortdesc"]))
        $sources .= "shortdesc-";
    if(!empty($_POST["source_desc"]))
        $sources .= "desc-";
    if(!empty($_POST["source_img"]))
        $sources .= "img-";
    $params["source"] = $sources;

    $params["undefined"]=(Tools::getValue('undefined'));
    $params["quality"]=(Tools::getValue('quality','good'));

    SCI::updateConfigurationValue("SC_FOULEFACTORY_DEFAULT_VALUES", serialize($params));
}
?>
<script type="text/javascript" src="<?php echo SC_JQUERY; ?>"></script>
<style>
    div {
        color: #4a535e;
    }
    .div_form {
        clear: both;
        margin-bottom: 10px;
        line-height: 30px;
    }
    input,select {
        border: 1px solid #ccc;
        border-radius: 3px;
        box-shadow: none;
        color: #555;
        height: 30px;
        width: 400px;
    }
    input.chk {
        width: auto;
        height: auto;
    }
    select.multiple {
        width: 180px;
        float: left;
    }
    .btn {
        background-color: #ff9600;
        border: 1px solid #eea236;
        color: #fff;
        border-radius: 3px;
        line-height: 31px;
        padding: 6px 15px;
        transition: all 0.2s ease-out 0s;
        cursor: pointer;
    }
    .btn:focus,.btn:hover,.btn:active {
        background-color: #ffaa00;
        border-color: #d58512;
        color: #fff;
    }

    .form_label {
        float: left;
        width: 200px;
        height: 30px;
        line-height: 30px;
        text-align: right;
        margin-right: 10px;
    }

    a {
        color: #428bca;
        text-decoration: none;
    }
    a:focus,a:hover,a:active {
        text-decoration: underline;
    }
</style>
<form method="POST" id="form_ff_config">
    <input type="hidden" name="form_ff_config" value="1" />
    <?php
    $FF_ID = SCI::getConfigurationValue("SC_FOULEFACTORY_ID");
    $FF_APIKEY =SCI::getConfigurationValue("SC_FOULEFACTORY_APIKEY");
    ?>
    <div><strong><?php echo _l('API information')?></strong></div>
    <div class="div_form">
        <div class="form_label">
            <?php echo _l('Your ID:')?>
        </div>
        <input type="text" name="yourid" value="<?php echo $FF_ID; ?>" />
    </div>
    <div class="div_form">
        <div class="form_label">
            <?php echo _l('Your API key:')?>
        </div>
        <input type="text" name="yourapikey" value="<?php echo $FF_APIKEY; ?>" />
    </div>
    <br/>
    <?php
    $FF_DEFAULT_VALUES = SCI::getConfigurationValue("SC_FOULEFACTORY_DEFAULT_VALUES");
    $params = unserialize($FF_DEFAULT_VALUES);
    ?>
    <div><strong><?php echo _l('Default values')?></strong></div>
    <div class="div_form">
        <div class="form_label">
            <?php echo _l('Source')?>
        </div>
        <?php
        $sources = explode("-",trim(trim($params['source'],"-")));
        ?>
        <input type="checkbox" class="chk" name="source_shortdesc" value="1" <?php if(in_array("shortdesc",$sources)) echo "checked"; ?> style="" /> <?php echo _l('Short description')?>
        <input type="checkbox" class="chk" name="source_desc" value="1" <?php if(in_array("desc",$sources)) echo "checked"; ?> style="" /> <?php echo _l('Description')?>
        <input type="checkbox" class="chk" name="source_img" value="1" <?php if(in_array("img",$sources)) echo "checked"; ?> style="" /> <?php echo _l('Image')?>
    </div>
    <div class="div_form">
        <div class="form_label">
            <?php echo _l('If the result is undefined')?>
        </div>
        <select name="undefined">
            <option value="" <?php if(empty($params["undefined"])) echo "selected"; ?>><?php echo _l('No action'); ?></option>
            <option value="remove" <?php if(!empty($params["undefined"]) && $params["undefined"]=="remove") echo "selected"; ?>><?php echo _l('Remove the product from its category'); ?></option>
            <option value="subcat" <?php if(!empty($params["undefined"]) && $params["undefined"]=="subcat") echo "selected"; ?>><?php echo _l('Associate the product to category').' "'._l('Current project').'/'._l('Undefined').'"'; ?></option>
        </select>
    </div>
    <div class="div_form">
        <div class="form_label">
            <?php echo _l('Expected quality result')?>
        </div>
        <select name="quality">
            <?php /*<option value="good" <?php if(empty($params["quality"]) || $params["quality"]=="good") echo "selected"; ?>><?php echo _l('Good'); ?></option>*/ ?>
            <option value="higher" <?php if(!empty($params["quality"]) && $params["quality"]=="higher") echo "selected"; ?>><?php echo _l('Higher'); ?></option>
            <option value="excellent" <?php if(!empty($params["quality"]) && $params["quality"]=="excellent") echo "selected"; ?>><?php echo _l('Excellent'); ?></option>
        </select>
    </div>
    <div class="div_form" style="width: 610px; text-align: right;">
        <br/>
        <button class="btn" onclick="$('#form_ff_config').submit();" id="btn_save"><?php echo _l('Save'); ?></button>
    </div>
</form>
<?php
