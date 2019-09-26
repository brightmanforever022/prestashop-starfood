<?php

$id_lang=intval(Tools::getValue('id_lang'));
$show_archived=intval(Tools::getValue('show_archived',0));
require_once("lib/php/foulefactory/FFApi.php");
require_once("lib/php/foulefactory/FfProject.php");

if($user_lang_iso=="fr")
require_once (dirname(__FILE__)."/".$user_lang_iso.'.php');

$xml = '';

$sql = "SELECT * FROM "._DB_PREFIX_."sc_ff_project WHERE 1=1 ".(!empty($show_archived)?"":" AND status!='archived' ")." ORDER BY id_project DESC";
$res=Db::getInstance()->ExecuteS($sql);
foreach($res AS $row)
{
    if(!empty($row["id_ff_project"]) && $row["status"]=="processing")
    {
        $FFProject = new FfProject((int)$row["id_project"]);
        if($FFProject->checkFinish()===true)
        {
            $FFProject->status = "finished";
            $FFProject->save();
            $row["status"] = "finished";
        }
    }

    $btn = "";
    $btn_title = "";
    $status = $row["status"];
    if($row["status"]=="created" && empty($debug))
        $status = _l("To configure");
    elseif($row["status"]=="configured" && empty($debug))
        $status = _l("To quote");
    elseif($row["status"]=="to_pay")
    {
        if(empty($debug))
            $status = _l("To pay");
        $btn = '<a href="javascript: void(0);" onclick="setStatus(\''.(int)$row["id_project"].'\', \'pay\');"><img src="lib/img/money.png" alt="'._l('Pay').'" title="'._l('Pay').'" /></a>';
        $btn_title = _l('Pay');
    }
    elseif($row["status"]=="waiting_payment" && empty($debug))
        $status = _l("Payment in progress");
    elseif($row["status"]=="error_payment")
    {
        if(empty($debug))
            $status = _l("To pay").' / '._l("Error during last payment");
        $btn = '<a href="javascript: void(0);" onclick="setStatus(\''.(int)$row["id_project"].'\', \'pay\');"><img src="lib/img/money.png" alt="'._l('Pay').'" title="'._l('Pay').'" /></a>';
        $btn_title = _l('Pay');
    }
    elseif($row["status"]=="paid")
    {
        if(empty($debug))
            $status = _l("Paid");
        $btn = '<a href="javascript: void(0);" onclick="setStatus(\''.(int)$row["id_project"].'\', \'start\');"><img src="lib/img/accept.png" alt="'._l('Start').'" title="'._l('Start').'" /></a>';
        $btn_title = _l('Start');
    }
    elseif($row["status"]=="processing" && empty($debug))
        $status = _l("In progress");
    elseif($row["status"]=="finished")
    {
        if(empty($debug))
            $status = _l("Finished");
        $btn = '<a href="javascript: void(0);" onclick="setStatus(\''.(int)$row["id_project"].'\', \'imported\');"><img src="lib/img/database_add.png" alt="'._l('Import').'" title="'._l('Import').'" /></a>';
        $btn_title = _l('Import');
    }
    elseif($row["status"]=="imported" && empty($debug))
        $status = _l("Imported");
    elseif($row["status"]=="archived" && empty($debug))
        $status = _l("Archived");

    $estimated_end = "-";

    if(empty($row["started_at"]) || $row["started_at"]=="0000-00-00 00:00:00")
        $row["started_at"] = "-";
    else
    {
        $date = new DateTime($row["started_at"]);
        $row["started_at"] = $date->format('Y-m-d H:i');
    }

    if(empty($row["duration"]) || $row["duration"]<=0)
        $row["duration"] = "-";
    else
    {
        if(!empty($row["started_at"]) && $row["started_at"]!="0000-00-00 00:00:00")
        {
            $date = new DateTime($row["started_at"]);
            $date->add(new DateInterval('PT'.$row["duration"].'M'));
            $estimated_end = str_replace(":","h",$date->format('Y-m-d H:i'));
        }

        $hours = floor( $row["duration"] / 60 );
        $minutes = $row["duration"] % 60;
        $row["duration"] = $hours."h".str_pad($minutes, 2, "0", STR_PAD_LEFT);;
    }
    $row["started_at"] = str_replace(":","h",$row["started_at"]);

    if(empty($row["tarif"]) || $row["tarif"]<=0)
        $row["tarif"] = "-";

    $nb_pdt = 0;
    $cat = new Category((int)$row["id_category"]);
    $nb = $cat->getProducts($id_lang,1,1,null,null,true,false);
    if(!empty($nb))
        $nb_pdt = $nb;

    $xml .= "<row id=\"".(int)$row["id_project"]."\">";
    $xml .= "<cell><![CDATA[".$row["name"]."]]></cell>";
    $xml .= "<cell><![CDATA[".$row["type"]."]]></cell>";
    $xml .= "<cell><![CDATA[".$row["created_at"]."]]></cell>";
    $xml .= "<cell><![CDATA[".$status."]]></cell>";
    $xml .= "<cell title=\"".$btn_title."\"><![CDATA[".$btn."]]></cell>";
    $xml .= "<cell><![CDATA[".$estimated_end."]]></cell>";
    $xml .= "<cell><![CDATA[".$row["started_at"]."]]></cell>";
    $xml .= "<cell><![CDATA[".$row["duration"]."]]></cell>";
    $xml .= "<cell><![CDATA[".$nb_pdt."]]></cell>";
    $xml .= "<cell><![CDATA[".$row["tarif"]."]]></cell>";
    $xml .= "</row>";
}

//XML HEADER

//include XML Header (as response will be in xml format)
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}
echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");

?>
<rows id="0">
    <head>
        <beforeInit>
            <call command="attachHeader"><param><![CDATA[#text_filter,#select_filter,#text_filter,#select_filter,,#numeric_filter,#text_filter,#text_filter,#numeric_filter,#numeric_filter]]></param></call>
        </beforeInit>
        <column id="name" width="200" type="ed" align="left" sort="str"><?php echo _l('Projet')?></column>
        <column id="type" width="160" type="coro" align="left" sort="str"><?php echo _l('Type')?>
            <option value="feature"><?php echo _l('Enter feature')?></option>
            <option value="desc_short"><?php echo _l('Description - short')?></option>
            <option value="desc_long"><?php echo _l('Description - long')?></option>
        </column>
        <column id="created_at" width="80" type="ro" align="right" sort="str"><?php echo _l('Date')?></column>
        <?php if (!empty($debug)) { ?>
            <column id="status_update" width="100" type="coro" align="left" sort="str"><?php echo _l('Status'); ?>
                <option value="created"><?php echo _l('To configure'); ?></option>
                <option value="configured"><?php echo _l('To quote'); ?></option>
                <option value="to_pay"><?php echo _l('To pay'); ?></option>
                <option value="waiting_payment"><?php echo _l('Payment in progress'); ?></option>
                <option value="error_payment"><?php echo _l("To pay").' / '._l("Error during last payment"); ?></option>
                <option value="paid"><?php echo _l('Paid')?></option>
                <option value="processing"><?php echo _l('In progress')?></option>
                <option value="finished"><?php echo _l('Finished')?></option>
                <option value="imported"><?php echo _l('Imported')?></option>
                <option value="archived"><?php echo _l('Archived')?></option>
            </column>
        <?php } else { ?>
            <column id="status" width="100" type="ro" align="left" sort="str"><?php echo _l('Status')?></column>
        <?php } ?>
        <column id="btn" width="40" type="ro" align="center" sort="str"></column>
        <column id="percent" width="100" type="ro" align="right" sort="int"><?php echo _l('Estimated end date')?></column>
        <column id="started_at" width="100" type="ro" align="right" sort="str"><?php echo _l('Started at')?></column>
        <column id="duration" width="40" type="ro" align="right" sort="str"><?php echo _l('Duration')?></column>
        <column id="nb_pdt" width="60" type="ro" align="right" sort="int"><?php echo _l('Products nb.')?></column>
        <column id="tarif" width="60" type="ro" align="right" sort="int"><?php echo _l('Prices')?></column>
    </head>
    <?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('ff_projects').'</userdata>'."\n";
    echo $xml;
    ?>
</rows>
