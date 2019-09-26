<div class="div_form" style="width: 800px; text-align: left;">
    <br/>
    <button class="btn btn_tarif <?php echo (!in_array($project->status, array("configured","to_pay"))?'lightgrey':($project->status=="configured"?'clickable':'darkgrey clickable')); ?>" style="margin-right: 30px;">2/ <?php echo _l('Request quote'); ?></button>
    <strong>3/ <?php echo _l('Price:'); ?> <?php echo (!empty($project->tarif) && $project->tarif>0?$project->tarif."€":"-") ?></strong>
    <button class="btn btn_pay <?php echo (in_array($project->status, array("to_pay","error_payment","waiting_payment") )?'clickable':'lightgrey'); ?>" style="margin-left: 30px;margin-right: 30px;">4/ <?php echo _l('Pay'); ?></button>
    <button class="btn btn_start  <?php echo ($project->status=="paid"?'clickable':'lightgrey'); ?>">5/ <?php echo _l('Start'); ?></button>
    <br/>
    <?php
        $amount = Configuration::get("SC_FOULEFACTORY_AMOUNT");
        if(empty($amount) || $amount<=0)
            $amount = 0;
        if($amount<=1000) {
        echo '<center>'._l('Currently spent:')." ".$amount."€".'</center>';

        $link = "";
        if ($user_lang_iso == 'fr') {
            $link = "http://www.storecommander.com/redir.php?dest=2016061022";
        } else {
            $link = "http://www.storecommander.com/redir.php?dest=2016061021";
        }
        ?>
    <center><?php echo _l('If your total spending goes over 1000€, please refer to ').'<a href="'.$link.'" target="_blank">'._l('this page').'</a>'; ?></center><?php } ?>
</div>
<?php /*<br/>
<div class="div_form" style="width: 800px;">
    <div class="checkout-wrap">
        <ul class="checkout-bar">

            <li class="first <?php echo ($etape>="2"?"visited":"active"); ?>"><?php echo _l('Configure'); ?></li>

            <li class="btn_tarif <?php echo ($etape=="2"?"active clickable":($etape=="3"?"visited clickable":($etape>"3"?"visited":""))); ?>"><?php echo _l('Request quote'); ?></li>

            <li class="<?php echo ($etape>="3"?"visited":""); ?>"><?php echo _l('Price:'); ?> <?php echo (!empty($project->tarif) && $project->tarif>0?$project->tarif."€":"/") ?></li>

            <li class="btn_pay next <?php echo ($etape=="3"?"active clickable":($etape>"3"?"visited":"")) ?>"><?php echo _l('Pay'); ?></li>

            <li class="btn_start last <?php echo ($etape=="4"?"active clickable":($etape>"4"?"visited":"")) ?>"><?php echo _l('Start'); ?></li>

        </ul>
    </div>
</div>*/ ?>
<div class="clear"></div>

<script type="text/javascript">
    $(".btn_tarif.clickable").on( "click", function() {
        parent.setStatus('<?php echo $id_project; ?>', 'get_quote');
    });
    $(".btn_pay.clickable").on( "click", function() {
        parent.setStatus('<?php echo $id_project; ?>', 'pay');
    });
    $(".btn_start.clickable").on( "click", function() {
        parent.setStatus('<?php echo $id_project; ?>', 'start');
    });
</script>