<?php
/**
 * Store Commander
 *
 * @category administration
 * @author Store Commander - support@storecommander.com
 * @version 2015-09-15
 * @uses Prestashop modules
 * @since 2009
 * @copyright Copyright &copy; 2009-2015, Store Commander
 * @license commercial
 * All rights reserved! Copying, duplication strictly prohibited
 *
 * *****************************************
 * *           STORE COMMANDER             *
 * *   http://www.StoreCommander.com       *
 * *            V 2015-09-15               *
 * *****************************************
 *
 * Compatibility: PS version: 1.1 to 1.6.1
 *
 **/

$licence = SCI::getConfigurationValue('SC_LICENSE_KEY');
if(empty($licence))
    $licence = "demo";
$post = array(
    "licence" => $licence
, "email" => $sc_agent->email
, "type" => "sc_foulefactory_popup"
);
$headers = array();
sc_file_post_contents('http://api.storecommander.com/Tracking/InsertRow', $post, $headers);

if($user_lang_iso=="fr")
require_once (dirname(__FILE__)."/".$user_lang_iso.'.php');

if ($user_lang_iso == 'fr') {
    $url_more_infos = "http://www.storecommander.com/redir.php?dest=2016061012";
} else {
    $url_more_infos = "http://www.storecommander.com/redir.php?dest=2016061011";
}

ob_start();
?>
<style>
    div {
        color: #4a535e;
        font-family: Tahoma;
    }
    input,select {
        border: 1px solid #ccc;
        border-radius: 3px;
        box-shadow: none;
        color: #555;
        height: 20px;
        width: 100px;
    }
    .form_big input,.form_big select {
        width: 200px;
    }
    input.chk {
        width: auto;
        height: auto;
    }
   .btn {
       background-color: #D70D3C;
       border: 1px solid #D70D3C;
       color: #fff;
       border-radius: 3px;
       line-height: 21px;
       padding: 6px 15px;
       transition: all 0.2s ease-out 0s;
       cursor: pointer;
   }
   .btn:focus,.btn:hover,.btn:active {
       background-color: #D70D3C;
       border-color: #D70D3C;
       color: #fff;
   }
    .btn.blue {
        background: rgba(0, 0, 0, 0) linear-gradient(#e5f0fd, #d3e6fe) repeat scroll 0 0;
        border: 1px solid #a4bed4;
        color: #34404b;
        cursor: auto;
    }

   .form_label {
       float: left;
       width: 120px;
       height: 20px;
       text-align: right;
   }

    a {
        color: #428bca;
        text-decoration: none;
    }
    a:focus,a:hover,a:active {
        text-decoration: underline;
    }
</style>
<div style="padding: 5px 10px 20px;" id="div_content">
    <div class="form_big" style="float: left; width: 845px; padding-right: 20px; line-height: 30px; margin-bottom: 20px;">
       <center><img src="lib/img/logo.png" alt="Store Commander" /> <img src="lib/img/Foule-Factory-Logo.png" height="50px;" style="margin-left: 20px;" alt="Store Commander" /></center>
        <center style="font-weight: bold; font-size: 16px;"><?php echo _l('Inscrivez-vous gratuitement et boostez votre productivité grâce à FouleFactory !')?></center>
    </div>
    <div class="form_big" style="float: left; width: 400px; padding-right: 20px;">
        <strong style="margin: 0 10%;"><?php echo _l('Create an account'); ?></strong><br/><br/>
        <div>
            <div class="form_label">
                <?php echo _l('Email:')?> *
            </div>
            <input type="text" id="email" value="<?php echo $sc_agent->email; ?>" style="float: left; margin-left: 10px;" />
        </div>
        <div style="clear:both; margin-bottom: 20px;"></div>
        <div style="float: left;  width: 80%; margin: 0 10%;">
            <button class="btn" id="ff_register" style="width: 100%;"><?php echo _l('I subscribe'); ?></button>
        </div>
    </div>
    <div class="form_big" style="float: left; width: 400px;padding-left: 20px; border-left: 5px solid #D70D3C;">
        <strong style="margin: 0 10%;"><?php echo _l('Existing account?'); ?></strong><br/><br/>
        <div>
            <div class="form_label">
                <?php echo _l('Your ID:')?>
            </div>
            <input type="text" id="yourid" style="float: left; margin-left: 10px;" />
        </div>
        <div style="clear:both; margin-bottom: 10px;"></div>
        <div>
            <div class="form_label">
                <?php echo _l('Your API key:')?>
            </div>
            <input type="text" id="yourapikey" style="float: left; margin-left: 10px;" />
        </div>
        <div style="clear:both; margin-bottom: 20px;"></div>
        <div style="float: left; width: 80%; margin: 0 10%;">
            <button class="btn" id="ff_login" style="width: 100%;"><?php echo _l('Connecting and preparing'); ?></button>
        </div>
    </div>
    <div style="clear:both; margin-bottom: 20px;"></div>
    <center><?php echo _l('Use FouleFactory - Store Commander integration to complete your product pages extremely fast.')?></center>
    <center><a href="<?php echo $url_more_infos; ?>" target="_blank" onclick="addTracking('sc_foulefactory_moreinfos');"><?php echo _l('Click here to get more information')?></a></center>
    <div style="clear:both; margin-bottom: 5px;"></div>
    <center><a href="<?php echo $url_more_infos; ?>" target="_blank" onclick="addTracking('sc_foulefactory_moreinfos');"><img src="lib/cat/win-foulefactory/Foule-Factory-info.png" alt="" /></a></center>
</div>
<div style="padding: 50px 10px; text-align: center; display: none;" id="div_loading">
    <img src="lib/img/loading.gif" alt="" /><br/>
    <?php echo _l('Working progress'); ?>
</div>
<?php
$content = ob_get_contents();
ob_end_clean();
$content = str_replace("\n","",$content);
$content = str_replace("\r","",$content);
$content = str_replace("'","\'",$content);
?>
<script type="text/javascript">
    wCatFoulefactory.setDimension(900, 600);
    wCatFoulefactory.setText("<?php echo _l('Login to your FouleFactory account')?>");
    wCatFoulefactory.attachHTMLString('<?php echo $content; ?>');

    $("#ff_register").on( "click", function() {
        var val_email = $("#email").val();

        var error = false;

        if(val_email==undefined || val_email==null || val_email=="" || val_email==0)
        {
            var msg = '<?php echo _l('You need to enter your email.',1)?>';
            dhtmlx.message({text:msg,type:'error',expire:10000});
            error = true;
        }
        else if(!checkEmail(val_email))
        {
            var msg = '<?php echo _l('You need to enter a valid email.',1)?>';
            dhtmlx.message({text:msg,type:'error',expire:10000});
            error = true;
        }
        if(error == false)
        {
            $("#div_content").hide();
            $("#div_loading").fadeIn();
            $.get('index.php?ajax=1&act=cat_win-foulefactory_subscribe&id_lang=<?php echo $id_lang; ?>',function(data){
                $('#jsExecute').html(data);
            });
        }
    });
    $("#ff_login").on( "click", function() {
        var val_id = $("#yourid").val();
        var val_apikey = $("#yourapikey").val();

        if(val_id==undefined || val_id==null || val_id=="" || val_id==0)
        {
            var msg = '<?php echo _l('You need to enter your ID.',1)?>';
            dhtmlx.message({text:msg,type:'error',expire:10000});
        }
        else if(val_apikey==undefined || val_apikey==null || val_apikey=="" || val_apikey==0)
        {
            var msg = '<?php echo _l('You need to enter your API key.',1)?>';
            dhtmlx.message({text:msg,type:'error',expire:10000});
        }
        else
        {
            $("#div_content").hide();
            $("#div_loading").fadeIn();
            $.post('index.php?ajax=1&act=cat_win-foulefactory_register&action=login',{'action':'login', 'ID':val_id,'APIKEY':val_apikey},function(data){
                if(data.status!=undefined && data.status=="success")
                {
                    $.get('index.php?ajax=1&act=cat_win-foulefactory_init',function(data){
                        $('#jsExecute').html(data);
                    });
                }
                else if(data.status!=undefined && data.status=="error")
                {
                    var msg = data.message;
                    dhtmlx.message({text:msg,type:'error',expire:10000});
                    $("#div_loading").hide();
                    $("#div_content").fadeIn();
                }
            },'json');
        }
    });

    function checkDate(date)
    {
        var pattern = new RegExp("(19|20)[0-9]{2}-(0|1)[0-9]-[0-3][0-9]");
        if(date.match(pattern))
            return true;
        return false;
    }
    function checkEmail(email) {
        var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

    function addTracking(type)
    {
        if(type!=undefined && type!=null && type!="" && type!=0)
        {
            $.post('index.php?ajax=1&act=all_get-checking',{'type':type},function(data){});
        }
    }
</script>