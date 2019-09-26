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
$id_lang = Tools::getValue("id_lang");

if($user_lang_iso=="fr")
require_once (dirname(__FILE__)."/".$user_lang_iso.'.php');

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
        width: 130px;
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
    <div class="form_big" style="float: left; width: 100%;line-height: 30px; margin-bottom: 20px;">
        <center><img src="lib/img/logo.png" alt="Store Commander" /> <img src="lib/img/Foule-Factory-Logo.png" height="50px;" style="margin-left: 20px;" alt="Store Commander" /></center>
        <center style="font-weight: bold; font-size: 16px;"><?php echo _l('Inscrivez-vous gratuitement et boostez votre productivité grâce à FouleFactory !')?></center>
    </div>
    <div class="form_big" style="float: left; width: 800px; margin-left: 25px;">
        <div style="float: left; width: 400px;">
            <div>
                <div class="form_label">
                    <?php echo _l('Gender:')?> *
                </div>
                <input type="radio" class="chk" name="idGender" value="1" id="idGender_1" /> <?php echo _l('Female')?>
                <input type="radio" class="chk" name="idGender" value="2" id="idGender_2" /> <?php echo _l('Male')?>
            </div>
            <div style="clear:both; margin-bottom: 10px;"></div>
            <div>
                <div class="form_label">
                    <?php echo _l('Firstname:')?> *
                </div>
                <input type="text" id="firstName" value="<?php echo $sc_agent->firstname; ?>" style="float: left; margin-left: 10px;" />
            </div>
            <div style="clear:both; margin-bottom: 10px;"></div>
            <div>
                <div class="form_label">
                    <?php echo _l('Name:')?> *
                </div>
                <input type="text" id="name" value="<?php echo $sc_agent->lastname; ?>" style="float: left; margin-left: 10px;" />
            </div>
            <div style="clear:both; margin-bottom: 10px;"></div>
            <div>
                <div class="form_label">
                    <?php echo _l('Email:')?> *
                </div>
                <input type="text" id="email" value="<?php echo $sc_agent->email; ?>" style="float: left; margin-left: 10px;" />
            </div>
            <div style="clear:both; margin-bottom: 10px;"></div>
            <div>
                <div class="form_label">
                    <?php echo _l('Phone:')?> *
                </div>
                <input type="text" id="phone" style="float: left; margin-left: 10px;" />
            </div>
            <div style="clear:both; margin-bottom: 10px;"></div>
            <div>
                <div class="form_label">
                    <?php echo _l('Birthday:')?> *
                </div>
                <input type="text" id="birthday" style="float: left; margin-left: 10px;" />
                <div style="clear:both;"></div>
                <div style="float: left; margin-left: 130px; font-style: italic;"><?php echo _l('(yyyy-mm-dd)')?></div>
            </div>
        </div>
        <div style="float: left; width: 400px;">
            <div>
                <div class="form_label">
                    <?php echo _l('Company:')?>
                </div>
                <input type="text" id="company" style="float: left; margin-left: 10px;" />
            </div>
            <div style="clear:both; margin-bottom: 10px;"></div>
            <div>
                <div class="form_label">
                    <?php echo _l('Address 1:')?> *
                </div>
                <input type="text" id="address1" style="float: left; margin-left: 10px;" />
            </div>
            <div style="clear:both; margin-bottom: 10px;"></div>
            <div>
                <div class="form_label">
                    <?php echo _l('Address 2:')?>
                </div>
                <input type="text" id="address2" style="float: left; margin-left: 10px;" />
            </div>
            <div style="clear:both; margin-bottom: 10px;"></div>
            <div>
                <div class="form_label">
                    <?php echo _l('City:')?> *
                </div>
                <input type="text" id="city" style="float: left; margin-left: 10px;" />
            </div>
            <div style="clear:both; margin-bottom: 10px;"></div>
            <div>
                <div class="form_label">
                    <?php echo _l('Postal code:')?> *
                </div>
                <input type="text" id="postalCode" style="float: left; margin-left: 10px;" />
            </div>
            <div style="clear:both; margin-bottom: 10px;"></div>
            <div>
                <div class="form_label">
                    <?php echo _l('Country:')?> *
                </div>
                <select id="countryCode" style="float: left; margin-left: 10px;">
                    <option value="">-- Select --</option>
                    <?php
                    $countries = Country::getCountries((int)$id_lang,true);
                    foreach($countries as $country)
                        echo '<option value="'.$country["iso_code"].'">'.$country["country"].'</option>';
                    ?>
                </select>
            </div>
        </div>
        <div style="clear:both; margin-bottom: 20px;"></div>
        <div style="margin-bottom: 5px;">
            <button class="btn" id="ff_register" style="width: 100%;"><?php echo _l('Register and preparing Store Commander'); ?></button>
            <button class="btn blue" id="ff_noregister" style="display:none;float: right;width: 100%;"><?php echo _l('Service only available in France'); ?></button>
        </div>
        <em>* <?php echo _l('Mandatory fields')?></em>
    </div>
    <div style="clear:both; margin-bottom: 20px;"></div>
    <center><?php echo _l('Use FouleFactory - Store Commander integration to complete your product pages extremely fast.')?></center>
    <center><a href="<?php
        if ($user_lang_iso == 'fr') {
            echo "http://www.storecommander.com/redir.php?dest=2016061012";
        } else {
            echo "http://www.storecommander.com/redir.php?dest=2016061011";
        }
        ?>" target="_blank"><?php echo _l('Click here to get more information')?></a></center>
    <div style="clear:both; margin-bottom: 2em;"></div>
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

    $( document ).ready(function() {
        $( "#countryCode" ).change(function() {
            var val = $(this).val();
            if(val!="FR")
            {
                $("#ff_register").hide();
                $("#ff_noregister").show();
            }
            else
            {
                $("#ff_register").show();
                $("#ff_noregister").hide();
            }
        });
    });
    $("#ff_register").on( "click", function() {
        var val_idGender = $("input[name=idGender]:checked").val();
        var val_firstName = $("#firstName").val();
        var val_name = $("#name").val();
        var val_email = $("#email").val();
        var val_phone = $("#phone").val();
        var val_birthday = $("#birthday").val();
        var val_company = $("#company").val();

        var val_address1 = $("#address1").val();
        var val_address2 = $("#address2").val();
        var val_city = $("#city").val();
        var val_postalCode = $("#postalCode").val();
        var val_countryCode = $("#countryCode").val();

        var error = false;

        if(val_idGender==undefined || val_idGender==null || val_idGender=="" || val_idGender==0)
        {
            var msg = '<?php echo _l('You need to enter your gender.',1)?>';
            dhtmlx.message({text:msg,type:'error',expire:10000});
            error = true;
        }
        if(val_firstName==undefined || val_firstName==null || val_firstName=="" || val_firstName==0)
        {
            var msg = '<?php echo _l('You need to enter your firstname.',1)?>';
            dhtmlx.message({text:msg,type:'error',expire:10000});
            error = true;
        }
        if(val_name==undefined || val_name==null || val_name=="" || val_name==0)
        {
            var msg = '<?php echo _l('You need to enter your lastname.',1)?>';
            dhtmlx.message({text:msg,type:'error',expire:10000});
            error = true;
        }
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
        if(val_phone==undefined || val_phone==null || val_phone=="" || val_phone==0)
        {
            var msg = '<?php echo _l('You need to enter your phone.',1)?>';
            dhtmlx.message({text:msg,type:'error',expire:10000});
            error = true;
        }
        if(val_birthday==undefined || val_birthday==null || val_birthday=="" || val_birthday==0)
        {
            var msg = '<?php echo _l('You need to enter your birthday.',1)?>';
            dhtmlx.message({text:msg,type:'error',expire:10000});
            error = true;
        }
        else if(!checkDate(val_birthday))
        {
            var msg = '<?php echo _l('You need to enter a valid birthday.',1)?>';
            dhtmlx.message({text:msg,type:'error',expire:10000});
            error = true;
        }

        if(val_address1==undefined || val_address1==null || val_address1=="" || val_address1==0)
        {
            var msg = '<?php echo _l('You need to enter your address.',1)?>';
            dhtmlx.message({text:msg,type:'error',expire:10000});
            error = true;
        }
        if(val_city==undefined || val_city==null || val_city=="" || val_city==0)
        {
            var msg = '<?php echo _l('You need to enter your city.',1)?>';
            dhtmlx.message({text:msg,type:'error',expire:10000});
            error = true;
        }
        if(val_postalCode==undefined || val_postalCode==null || val_postalCode=="" || val_postalCode==0)
        {
            var msg = '<?php echo _l('You need to enter your postal code.',1)?>';
            dhtmlx.message({text:msg,type:'error',expire:10000});
            error = true;
        }
        if(val_countryCode==undefined || val_countryCode==null || val_countryCode=="" || val_countryCode==0)
        {
            var msg = '<?php echo _l('You need to enter your country.',1)?>';
            dhtmlx.message({text:msg,type:'error',expire:10000});
            error = true;
        }

        if(error == false)
        {
            $("#div_content").hide();
            $("#div_loading").fadeIn();
            $.post('index.php?ajax=1&act=cat_win-foulefactory_register&action=register',
                {
                    'action':'register',
                    'idGender': val_idGender,
                    'firstName': val_firstName,
                    'name': val_name,
                    'email': val_email,
                    'phone': val_phone,
                    'birthday': val_birthday,
                    'company': val_company,
                    'address1': val_address1,
                    'address2': val_address2,
                    'city': val_city,
                    'postalCode': val_postalCode,
                    'countryCode': val_countryCode
                },function(data){
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
</script>