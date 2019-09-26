<?php

// CSS AND JS
require_once(dirname(__FILE__)."/_header.php");

// Question and supp instructions
$question = '"'._l("La caractéristique à saisir est :").' <span id="name_feature"></span>"';
require_once(dirname(__FILE__)."/_first_part.php");

$enabled_sources = array(
    "shortdesc"=>_l('Short description'),
    "desc"=>_l('Description'),
    "img"=>_l('Image')
);

// Features fields
?>
    <div class="div_form">
        <div class="form_label">
            <?php echo _l('Product features to work on')?>*
        </div>
        <select id="id_feature">
            <option value="">-- <?php echo _l('Choose'); ?> --</option>
            <?php
            $features = Feature::getFeatures((int)$id_lang, false);
            foreach($features as $feature) { ?>
                <option value="<?php echo $feature["id_feature"]; ?>" <?php if(!empty($params["id_feature"]) && $params["id_feature"]==$feature["id_feature"]) echo "selected"; ?>><?php echo $feature["name"]; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="div_form">
        <div class="form_label">
            <?php echo _l('Suggested values')?>
        </div>
        <select id="feature_values" class="multiple" multiple style="height: 6em;">
            <?php
            $feature_values_actual = "";
            if(!empty($params["id_feature"])) {

                if(!empty($params["feature_values"]))
                {
                    $feature_values_actual = str_replace("-",",",trim(trim($params["feature_values"],"-")));
                    $feature_values = explode("-",trim(trim($params["feature_values"],"-")));
                }

                $values = FeatureValue::getFeatureValuesWithLang((int)$id_lang, (int)$params["id_feature"], false);
                foreach($values as $value) {  ?>
                    <option value="<?php echo $value["id_feature_value"]; ?>" <?php if(in_array($value["id_feature_value"],$feature_values)) echo "selected"; ?>><?php echo $value["value"]; ?></option>
                <?php }
            } ?>
        </select>
        <div style="float: left; margin-left: 5px; font-size: 12px; line-height: 20px;">
            <span id="feature_values_selectall" style="cursor: pointer;">+ <?php echo _l('Select all')?></span><br/>
            <span id="feature_values_unselectall" style="cursor: pointer;">- <?php echo _l('Unselect all')?></span>
        </div>
    </div>
    <script>
        var feature_values = new Array();
        <?php $i=0; foreach($features as $feature) {
        $array = array();
        $values = FeatureValue::getFeatureValuesWithLang((int)$id_lang, (int)$feature["id_feature"], false);
        foreach($values as $value)
            $array[$value["id_feature_value"]]=$value["value"];

        echo 'feature_values['.$feature["id_feature"].']='.json_encode($array).';'."\n";
        ?>
        <?php $i++; }

        echo 'var feature_values_actual = ['.$feature_values_actual.'];'."\n";
        ?>
        var feature_name = $( "#id_feature option:selected" ).html();
        $("#name_feature").html(feature_name);

        $( "#id_feature" ).on( "change", function() {
            var id_feature = $(this).val();
            var feature_name = $( "#id_feature option:selected" ).html();
            $("#name_feature").html(feature_name);
            $( "#feature_values").html("");
            if(feature_values[id_feature]!=undefined && feature_values[id_feature]!="" && feature_values[id_feature]!=null)
            {
                var values = feature_values[id_feature];
                $.each(values, function( id_feature_value, value ) {
                    var selected = "";
                    if($.inArray( id_feature_value, feature_values_actual ))
                        selected = "selected";
                    $( "#feature_values").append('<option value="'+id_feature_value+'" '+selected+'>'+value+'</option>');
                });
            }
        });
        $( "#feature_values_selectall" ).on( "click", function() {
            $( "#feature_values option").prop('selected', true);
        });
        $( "#feature_values_unselectall" ).on( "click", function() {
            $( "#feature_values option").prop('selected', false);
        });
    </script>
    <div class="div_form">
        <div class="form_label big">
            <?php echo _l('If the feature value found is not present in the list')?>*
        </div>
        <select id="feature_after_process" class="for_big">
            <option value="nothing" <?php if(empty($params["feature_after_process"]) || $params["feature_after_process"]=="nothing") echo "selected"; ?>><?php echo _l('Do not process and don\'t enter the feature'); ?></option>
            <option value="add_feature_value" <?php if(!empty($params["feature_after_process"]) && $params["feature_after_process"]=="add_feature_value") echo "selected"; ?>><?php echo _l('Add as feature value'); ?></option>
            <option value="add_custom_feature_value" <?php if(!empty($params["feature_after_process"]) && $params["feature_after_process"]=="add_custom_feature_value") echo "selected"; ?>><?php echo _l('Add as customized feature value'); ?></option>
        </select>
    </div>
    <div class="div_form">
        <div class="form_label">
            <?php echo _l('Expected quality result')?>*
        </div>
        <select id="quality">
            <?php /*<option value="good" <?php if(empty($params["quality"]) || $params["quality"]=="good") echo "selected"; ?>><?php echo _l('Good'); ?></option>*/ ?>
            <option value="higher" <?php if(!empty($params["quality"]) && $params["quality"]=="higher") echo "selected"; ?>><?php echo _l('Higher'); ?></option>
            <option value="excellent" <?php if(!empty($params["quality"]) && $params["quality"]=="excellent") echo "selected"; ?>><?php echo _l('Excellent'); ?></option>
        </select>
    </div>
<?php

// Quality and source + submit BTN
require_once(dirname(__FILE__)."/_second_part.php");

// form validation
$checkfields = 'var val_id_feature = $("#id_feature").val();
    if(val_id_feature==undefined || val_id_feature==null || val_id_feature=="" || val_id_feature==0)
    {
        var msg = \''._l('You need to select a feature.',1).'\';
        parent.dhtmlx.message({text:msg,type:"error",expire:10000});
        errors = true;
    }

    var feature_values = "";
    var val_feature_values = $("#feature_values").val();
    /*if(val_feature_values==undefined || val_feature_values==null || val_feature_values=="" || val_feature_values==0)
    {
        var msg = \''._l('You need to select at least one feature value.',1).'\';
        parent.dhtmlx.message({text:msg,type:"error",expire:10000});
        errors = true;
    }
    else*/
        feature_values = val_feature_values;

    var val_feature_after_process = $("#feature_after_process").val();';

$postfields = " ,'id_feature':val_id_feature
                ,'feature_values':feature_values
                ,'feature_after_process':val_feature_after_process ";

require_once(dirname(__FILE__)."/_form_js.php");

// Footer
require_once(dirname(__FILE__)."/_footer.php");