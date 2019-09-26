<?php

// CSS AND JS
require_once(dirname(__FILE__)."/_header.php");

// Question and supp instructions
$question = '"'._l("Write the short description").'"';
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
            <option value="10" <?php if(!empty($params["quality"]) && $params["quality"]=="10") echo "selected"; ?>><?php echo _l('10 words'); ?></option>
            <option value="20" <?php if(!empty($params["quality"]) && $params["quality"]=="20") echo "selected"; ?>><?php echo _l('20 words'); ?></option>
            <option value="30" <?php if(!empty($params["quality"]) && $params["quality"]=="30") echo "selected"; ?>><?php echo _l('30 words'); ?></option>
            <option value="50" <?php if(!empty($params["quality"]) && $params["quality"]=="50") echo "selected"; ?>><?php echo _l('50 words'); ?></option>
            <option value="100" <?php if(!empty($params["quality"]) && $params["quality"]=="100") echo "selected"; ?>><?php echo _l('100 words'); ?></option>
        </select>
    </div>
    <div style="border: 1px solid #d88e0d;color: #d88e0d; background: #f7ecd9; padding: 10px; margin: 10px;">
        <?php echo _l('A short description is limited to ')._s('CAT_SHORT_DESC_SIZE')._l(' characters.')?>
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