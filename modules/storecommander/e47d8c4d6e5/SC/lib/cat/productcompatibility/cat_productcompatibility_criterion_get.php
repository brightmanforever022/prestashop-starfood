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

$id_lang=(int)Tools::getValue('id_lang');
$for_filter=(int)Tools::getValue('for_filter',0);
$id_filter=(int)Tools::getValue('id_filter',0);
$need_used=(int)Tools::getValue('used',null);

function getFilters()
{
    global $id_lang;
    $sql = "SELECT fl.*, f.position
            FROM "._DB_PREFIX_."ukoocompat_filter_lang fl
            LEFT JOIN "._DB_PREFIX_."ukoocompat_filter f ON fl.id_ukoocompat_filter = f.id_ukoocompat_filter
            WHERE fl.id_lang = ".(int)$id_lang."
            ORDER BY f.position";
    return Db::getInstance()->ExecuteS($sql);
}

function renderFilters()
{
    $return = '';
    if($res = getFilters())
    {
        foreach($res as $row){
            $return .= '<row id="'.$row['id_ukoocompat_filter'].'">';
            $return .= '<userdata name="id_ukoocompat_filter">'.$row['id_ukoocompat_filter'].'</userdata>';
            $return .= '<cell><![CDATA['.$row['name'].']]></cell>';
            $return .= '</row>';
        }
	}
    return $return;
}

function getCriterions()
{
    global $id_filter,$id_lang;
    $sql = "SELECT ct.*, ctl.value
            FROM "._DB_PREFIX_."ukoocompat_criterion_lang ctl
            LEFT JOIN "._DB_PREFIX_."ukoocompat_criterion ct ON ct.id_ukoocompat_criterion = ctl.id_ukoocompat_criterion
            WHERE ctl.id_lang = ".(int)$id_lang;
    if($id_filter) {
        $sql .= " AND ct.id_ukoocompat_filter = ".(int)$id_filter;
    }
    $sql .= " ORDER BY ct.position";
    return Db::getInstance()->ExecuteS($sql);
}

function renderCriterions()
{
    global $need_used;
    $return = '';

    if(!empty($need_used)) {
        $sql='SELECT COUNT(id_ukoocompat_compat) as used, id_ukoocompat_criterion
                FROM '._DB_PREFIX_.'ukoocompat_compat_criterion
                GROUP BY id_ukoocompat_criterion';
        $res=Db::getInstance()->ExecuteS($sql);

        $used_arr = array();
        foreach($res as $row){
            $used_arr[$row['id_ukoocompat_criterion']] = $row['used'];
        }
    }

    if($res = getCriterions())
    {
        foreach($res as $row){
            $return .= '<row id="'.$row['id_ukoocompat_criterion'].'">';
            $return .= '<userdata name="id_ukoocompat_criterion">'.$row['id_ukoocompat_criterion'].'</userdata>';
            $return .= '<userdata name="id_ukoocompat_filter">'.$row['id_ukoocompat_filter'].'</userdata>';
            $return .= '<cell><![CDATA['.$row['value'].']]></cell>';
            $return .= '<cell>'.$row['position'].'</cell>';
            if(!empty($need_used) && !empty($used_arr)) {
                $return .= '<cell>'.(!empty($used_arr[$row['id_ukoocompat_criterion']]) ? $used_arr[$row['id_ukoocompat_criterion']] : 0).'</cell>';
            }
            $return .= '</row>';
        }
    }
    return $return;
}

if(stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")){
    header("Content-type: application/xhtml+xml");
}else{
    header("Content-type: text/xml");
}
echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");

###FILTERS
if($for_filter) {
?>
    <rows>
        <head>
            <beforeInit>
                <call command="attachHeader"><param><![CDATA[#text_filter]]></param></call>
            </beforeInit>
            <column id="filters" width="*" type="ro" align="left" sort="str"><?php echo _l('Search filters') ?></column>
        </head>
        <?php
            echo renderFilters();
        ?>
    </rows>
<?php
###VALUES
} else {
?>
    <rows>
        <head>
            <beforeInit>
                <call command="attachHeader"><param><![CDATA[#text_filter,#numeric_filter]]></param></call>
            </beforeInit>
            <column id="value" width="*" type="ed" align="left" sort="int"><?php echo _l('Value') ?></column>
            <column id="position" width="*" type="ro" align="left" sort="int"><?php echo _l('Position') ?></column>
            <?php if(!empty($need_used)){ ?>
                <column id="used" width="*" type="ro" align="left" sort="int"><?php echo _l('Used X times') ?></column>
            <?php } ?>
        </head>
        <?php
            echo renderCriterions();
        ?>
    </rows>
<?php
}
?>
