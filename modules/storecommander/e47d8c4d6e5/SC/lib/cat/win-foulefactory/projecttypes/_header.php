<script type="text/javascript" src="<?php echo SC_JQUERY; ?>"></script>
<link rel="stylesheet" href="lib/css/ff_progressbar_style.css" /
<link href='http://fonts.googleapis.com/css?family=PT+Sans+Caption:400,700' rel='stylesheet' type='text/css'>
<style>
    div {
        color: #4a535e;
    }
    .clear {clear: both;}
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
        width: 280px;
        float: left;
    }
    input.for_big,select.for_big {
        margin-left: 210px;
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

    .btn.darkgrey {
        background-color: #7b7b7b;
        border: 1px solid #474747;
        color: #fff;
    }
    .btn.darkgrey:focus,.btn.darkgrey:hover,.btn.darkgrey:active {
        background-color: #929292;
        border-color: #7b7b7b;
        color: #fff;
    }
    .btn.lightgrey {
        background-color: #c8c8c8;
        border: 1px solid #b6b6b6;
        color: #fff;
        cursor: auto;
    }
    .btn.lightgrey:focus,.btn.lightgrey:hover,.btn.lightgrey:active {
        background-color: #c8c8c8;
        border: 1px solid #b6b6b6;
        color: #fff;
        cursor: auto;
    }

    .form_label {
        float: left;
        width: 300px;
        height: 30px;
        line-height: 30px;
        text-align: right;
        margin-right: 10px;
    }
    .form_label.big {
        float: none;
        width: 100%;
        text-align: left;
        margin-right: 0px;
    }

    a {
        color: #428bca;
        text-decoration: none;
    }
    a:focus,a:hover,a:active {
        text-decoration: underline;
    }

    .clickable {cursor: pointer};
</style>

<?php
$etape = 1;
if($project->status=="created")
    $etape = 1;
elseif($project->status=="configured")
    $etape = 2;
elseif(in_array($project->status, array("to_pay","error_payment","waiting_payment")))
    $etape = 3;
elseif($project->status=="paid")
    $etape = 4;
elseif(in_array($project->status, array("processing","archived","finished","imported")))
    $etape = 5;
?>
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
</div>
<div class="clear"></div>
<br/>