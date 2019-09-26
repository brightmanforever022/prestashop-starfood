<?php
$show_thanks = false;
if(isset($_POST['saveTrendsActive']))
{
    $value = Tools::getValue("trends_active", "0");
    $local_settings["APP_TRENDS"]['value']=$value;
    saveSettings();
    if($value=="1")
        $show_thanks = true;

    $licence = SCI::getConfigurationValue('SC_LICENSE_KEY');
    if(empty($licence))
        $licence = "demo";

    $idShops = SCI::getConfigurationValue('SC_TRENDS_ID_SHOPS');
    if(!empty($idShops))
        $idShops = json_decode($idShops, TRUE);

    if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $protocol = (version_compare(_PS_VERSION_, '1.5.0.2', '>=') ? Tools::getShopProtocol() : (SCI::getConfigurationValue('PS_SSL_ENABLED') ? 'https://' : 'http://'));
        $shops = ShopCore::getShops(false);
        foreach ($shops as $shop)
        {
            $urlSql = Db::getInstance()->ExecuteS('SELECT CONCAT(domain, physical_uri, virtual_uri) AS url
					FROM '._DB_PREFIX_.'shop_url
					WHERE id_shop = '.(int)$shop["id_shop"].'
					ORDER BY main DESC
					LIMIT 1');
            $url = "";
            if(!empty($urlSql[0]["url"]))
                $url = $protocol.$urlSql[0]["url"];

            $headers = array();
            $headers[] = "SCLICENSE: " . $licence;
            $headers[] = "EMAIL: " . $sc_agent->email;
            $headers[] = "SHOPID: " . $shop['id_shop'];
            $headers[] = "SHOPURL: " .$url;
            $headers[] = "SCVERSION: " .SC_VERSION;
            if(!empty($idShops[$shop['id_shop']]))
                $headers[] = "ID_SHOP: " .$idShops[$shop['id_shop']];
            if($value=="1")
            {
                $return = sc_file_post_contents('http://api.storecommander.com/Trends/RegisterShop', '', $headers);
                $return = json_decode($return, true);
                if(!empty($return['result']) && $return['result']=="OK" && !empty($return['code']) && $return['code']=="200" && !empty($return['id']))
                {
                    if(empty($idShops))
                        $idShops = array();
                    $exp = explode("_", $return['id']);
                    $idShops[$exp[0]] = $exp[1];
                    $idShops_encoded = json_encode($idShops);
                    SCI::updateConfigurationValue('SC_TRENDS_ID_SHOPS', $idShops_encoded);
                }
            }
            else
                $return = sc_file_post_contents('http://api.storecommander.com/Trends/UnRegisterShop', '', $headers);
        }
    }
    else
    {
        $url = Tools::getShopDomain(true).__PS_BASE_URI__;
        $headers = array();
        $headers[] = "SCLICENSE: " . $licence;
        $headers[] = "EMAIL: " . $sc_agent->email;
        $headers[] = "SHOPID: 0";
        $headers[] = "SHOPURL: " .$url;
        $headers[] = "SCVERSION: " .SC_VERSION;
        if(!empty($idShops[$shop['id_shop']]))
            $headers[] = "ID_SHOP: " .$idShops[$shop['id_shop']];
        if($value=="1")
        {
            $return = sc_file_post_contents('http://api.storecommander.com/Trends/RegisterShop', '', $headers);
            $return = json_decode($return, true);
            if(!empty($return['result']) && $return['result']=="OK" && !empty($return['code']) && $return['code']=="200" && !empty($return['id']))
            {
                if(empty($idShops))
                    $idShops = array();
                $exp = explode("_", $return['id']);
                $idShops[$exp[0]] = $exp[1];
                $idShops_encoded = json_encode($idShops);
                SCI::updateConfigurationValue('SC_TRENDS_ID_SHOPS', $idShops_encoded);
            }
        }
        else
            $return = sc_file_post_contents('http://api.storecommander.com/Trends/UnRegisterShop', '', $headers);
    }
}
$trends_active = _s("APP_TRENDS");
?><style type="text/css">
    label {
        color: #ffffff;
        line-height: 24px;
        font-size: 20px;
        font-family: Tahoma;
        font-style: italic;
    }
    .btn {
        background: linear-gradient(#e2efff, #d3e7ff) repeat scroll 0 0 rgba(0, 0, 0, 0);
        border: 1px solid #a4bed4;
        color: #34404b;
        font-size: 11px;
        height: 27px;
        overflow: hidden;
        position: relative;
        font-weight: bold;
        cursor: pointer;
        float: right;
        margin-right: 15px;
    }
     a:

    div { font-family: Tahoma;
        font-size: 11px !important; }
</style>
<body style="background: #8E3CA3;">
<script type="text/javascript" src="<?php echo SC_JQUERY; ?>"></script>
<form action="" method="post">
<input type="checkbox" name="trends_active" id="trends_active" onchange="$('#saveTrendsActive').click();" value="1" <?php if($trends_active=="1") echo "checked"; ?> /> <label for="trends_active" style="cursor: pointer;"><?php echo _l('Yes I want to participate in E-commerce Trends project and I agree to send statistics'); ?></label>

<input type="submit" name="saveTrendsActive" id="saveTrendsActive" onclick="parent.dhxlRowForm.progressOn();" style="display: none;" class="btn" value="<?php echo _l('Save'); ?>" />
</form>
<script>
    parent.dhxlRowForm.progressOff();
    <?php if($show_thanks) { ?>
    var msg = '<?php echo _l('Thank you for your participation!',1)?>';
    parent.dhtmlx.message({text:msg,type:'success',expire:10000});
    <?php } ?>
</script>
</body>
