<div class="div_form">
    <div class="form_label">
        <?php echo _l('Your project')?>
    </div>
    <?php if(!empty($question)) echo $question; ?>
</div>
<div class="div_form">
    <div class="form_label">
        <?php echo _l('Additionnal instructions')?>
    </div>
    <input type="text" id="instructions" value="<?php echo $project->instructions; ?>" />
</div>

<br/>