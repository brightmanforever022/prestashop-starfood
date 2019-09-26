<?php

// CSS AND JS
require_once(dirname(__FILE__)."/_header.php");

// Question and supp instructions
$question = '"'._l("Write the description").'"';
require_once(dirname(__FILE__)."/_first_part.php");

// fields
?>
    <div style="border: 1px solid #d88e0d;color: #d88e0d; background: #f7ecd9; padding: 10px; margin: 10px; margin-top: -10px;">
        <?php echo _l('All products must be active to be accessible for the workers!')?>
    </div>
    <div class="div_form">
        <div class="form_label">
            <?php echo _l('Expected description size')?>*
        </div>
        <select id="quality">
            <option value="50" <?php if(!empty($params["quality"]) && $params["quality"]=="50") echo "selected"; ?>><?php echo _l('50 words'); ?></option>
            <option value="100" <?php if(!empty($params["quality"]) && $params["quality"]=="100") echo "selected"; ?>><?php echo _l('100 words'); ?></option>
            <option value="150" <?php if(!empty($params["quality"]) && $params["quality"]=="150") echo "selected"; ?>><?php echo _l('150 words'); ?></option>
            <option value="250" <?php if(!empty($params["quality"]) && $params["quality"]=="250") echo "selected"; ?>><?php echo _l('250 words'); ?></option>
            <option value="350" <?php if(!empty($params["quality"]) && $params["quality"]=="350") echo "selected"; ?>><?php echo _l('350 words'); ?></option>
            <option value="500" <?php if(!empty($params["quality"]) && $params["quality"]=="500") echo "selected"; ?>><?php echo _l('500 words'); ?></option>
            <option value="750" <?php if(!empty($params["quality"]) && $params["quality"]=="750") echo "selected"; ?>><?php echo _l('750 words'); ?></option>
        </select>
    </div>
<?php

// Quality and source + submit BTN
require_once(dirname(__FILE__)."/_second_part.php");

// form validation
$checkfields = '';

$postfields = " ";

require_once(dirname(__FILE__)."/_form_js.php");

// Footer
require_once(dirname(__FILE__)."/_footer.php");