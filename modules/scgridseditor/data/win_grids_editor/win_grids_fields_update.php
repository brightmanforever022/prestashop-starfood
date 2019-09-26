<?php

$action = Tools::getValue("action");
$type = str_replace("type_", "", Tools::getValue("type", "products"));
$grid_select = Tools::getValue("grid_select");

$name_lang = intval(Tools::getValue("id_lang", 0));
$iso = "en";
if (strtolower(Language::getIsoById($name_lang)) == "fr") {
    $iso = "fr";
}

require(dirname(__FILE__) . "/win_grids_tools.php");
$types_list = array(
    "products",
    "combinations",
    "customers",
    "orders",
    "productsort",
    "msproduct",
    "mscombination",
    "image",
    "propspeprice",
    "winspeprice",
    "propsupplier"
);

if (!empty($type) && in_array($type, $types_list) && !empty($action) && !empty($grid_select)) {
    $file = SC_TOOLS_DIR . 'grids_' . $type . '_conf.xml';

    if ($type == "products") {
        $type_temp = "product";
    } elseif ($type == "customers") {
        $type_temp = "customer";
    } elseif ($type == "orders") {
        $type_temp = "order";
    } elseif ($type == "combinations") {
        $type_temp = "combination";
    } else {
        $type_temp = $type;
    }

    // CREATE FILE IF NOT EXIST
    if (!file_exists($file)) {
        $content = '<?xml version="1.0" encoding="UTF-8"?>
<extension>
  <xml_version><![CDATA[' . SC_EXTENSION_VERSION . ']]></xml_version>
  <grids></grids>
  <fields></fields>
</extension>';
        file_put_contents($file, $content);
    }

    if ($action == "update_position") {
        $newvalue = Tools::getValue("newvalue", "");

        $update = true;

        if (empty($newvalue)) {
            $update = false;
        }

        if ($update) {
            $list = array();
            $exp = explode(";", $newvalue);
            foreach ($exp as $row) {
                $temp = explode(",", $row);
                $list[$temp[1]] = $temp[0];
            }
            ksort($list);

            $content = "";
            if (file_exists($file)) {
                $content = file_get_contents($file);
            }

            // IS DEFAULT
            if (!gridIsInXML($grid_select, $content)) {

                addNewGrid($type, $content, $grid_select, null, implode(",", $list));
            } else {
                // UPDATE FIELD
                $dom = new DOMDocument();
                $dom->preserveWhiteSpace = false;
                $dom->formatOutput = true;
                $dom->load($file);

                $nodeGridList = $dom->getElementsByTagname('grid');
                foreach ($nodeGridList as $nodeGrid) {
                    $nodeName = $nodeGrid->getElementsByTagname('name')->item(0);
                    if ($nodeName->nodeValue == $grid_select) {
                        $nodeText = $nodeGrid->getElementsByTagname("value")->item(0);
                        $nodeText->nodeValue = '';
                        $v = $nodeText->ownerDocument->createCDATASection(implode(",", $list));
                        $nodeText->appendChild($v);
                    }
                }
                $dom->save($file);

                $content = file_get_contents($file);
                $content = str_replace("<grids/>", "<grids></grids>", $content);
                $content = str_replace("<fields/>", "<fields></fields>", $content);
                file_put_contents($file, $content);
            }
        }
    } else {
        if ($action == "update") {
            $field_updated = Tools::getValue("field", "");
            $value_updated = Tools::getValue("value", "");
            $newvalue = Tools::getValue("newvalue", "");

            $update = true;

            if (empty($field_updated)) {
                $update = false;
            }
            if (empty($value_updated)) {
                $update = false;
            }

            if (in_array($value_updated, array("text", "width", "sort")) && empty($newvalue)) {
                $update = false;
            }

            if ($value_updated == "width" && !empty($newvalue) && $newvalue > 500) {
                $newvalue = 500;
            } else {
                if ($value_updated == "width" && !empty($newvalue) && $newvalue < 40) {
                    $newvalue = 40;
                } else {
                    if ($value_updated == "width" && empty($newvalue)) {
                        $newvalue = 80;
                    }
                }
            }

            if ($update) {
                // UPDATE FIELD
                $dom = new DOMDocument();
                $dom->preserveWhiteSpace = false;
                $dom->formatOutput = true;
                $dom->load($file);

                $in_file = false;
                $nodeFieldList = $dom->getElementsByTagname('field');
                foreach ($nodeFieldList as $nodeField) {
                    $nodeName = $nodeField->getElementsByTagname('name')->item(0);
                    if ($nodeName->nodeValue == $field_updated) {
                        if ($value_updated == "text") {
                            $updatedNode = $nodeField->getElementsByTagname('fr')->item(0);
                            $updatedNode->nodeValue = '';
                            $v = $updatedNode->ownerDocument->createCDATASection($newvalue);
                            $updatedNode->appendChild($v);

                            $updatedNodeBIS = $nodeField->getElementsByTagname('en')->item(0);
                            $updatedNodeBIS->nodeValue = '';
                            $v = $updatedNodeBIS->ownerDocument->createCDATASection($newvalue);
                            $updatedNodeBIS->appendChild($v);
                        } else {
                            $updatedNode = $nodeField->getElementsByTagname($value_updated)->item(0);
                            if(!empty($updatedNode))
                            {
                                $updatedNode->nodeValue = '';
                                $v = $updatedNode->ownerDocument->createCDATASection($newvalue);
                                $updatedNode->appendChild($v);
                            }
                            else
                            {
                                $newElementField = $dom->createElement($value_updated);
                                $v = $newElementField->ownerDocument->createCDATASection($newvalue);
                                $newElementField->appendChild($v);
                                $nodeField->appendChild($newElementField);
                            }
                        }
                        $in_file = true;
                    }
                }
                if (!$in_file) {
                    // GENERATES FIELD
                    $params_fields = SCI::getGridFields($type_temp);
                    if ($type_temp == "combination") {
                        $params_fields['ATTR'] = array(
                            'text' => _l('Attributes'),
                            'width' => 80,
                            'align' => 'right',
                            'type' => 'coro',
                            'sort' => 'str',
                            'color' => '',
                            'filter' => '#select_filter'
                        );
                    }

                    if (!empty($params_fields[$field_updated])) {
                        $params_fields[$field_updated][$value_updated] = $newvalue;

                        $xml_fields = array(
                            "name" => $field_updated,
                            "table" => "",
                            "text" => $params_fields[$field_updated]["text"],
                            "width" => $params_fields[$field_updated]["width"],
                            "align" => $params_fields[$field_updated]["align"],
                            "celltype" => $params_fields[$field_updated]["type"],
                            "answertype" => "",
                            "sort" => $params_fields[$field_updated]["sort"],
                            "color" => $params_fields[$field_updated]["color"],
                            "filter" => $params_fields[$field_updated]["filter"],
                            "footer" => $params_fields[$field_updated]["footer"],
                            "forceUpdateCombinationsGrid" => "",
                            "options" => "",
                            "onEditCell" => "",
                            "onAfterUpdate" => "",
                            "onBeforeUpdate" => "",
                            "SQLSelectDataSelect" => "",
                            "rowData" => "",
                            "afterGetRows" => ""
                        );

                        $nodeFields = $dom->getElementsByTagname('fields')->item(0);

                        $fieldElement = $dom->createElement("field");
                        $nodeFields->appendChild($fieldElement);

                        foreach ($xml_fields as $name_element => $value_element) {
                            if ($name_element != "text") {
                                $newElement = $dom->createElement($name_element);
                                $v = $newElement->ownerDocument->createCDATASection($value_element);
                                $newElement->appendChild($v);

                                $fieldElement->appendChild($newElement);
                            } else {
                                $newElementText = $dom->createElement("text");
                                $fieldElement->appendChild($newElementText);

                                $newElementTextFr = $dom->createElement("fr");
                                $v = $newElementTextFr->ownerDocument->createCDATASection(_l($value_element));
                                $newElementTextFr->appendChild($v);
                                $newElementText->appendChild($newElementTextFr);

                                $newElementTextEn = $dom->createElement("en");
                                $v = $newElementTextEn->ownerDocument->createCDATASection($value_element);
                                $newElementTextEn->appendChild($v);
                                $newElementText->appendChild($newElementTextEn);
                            }
                        }
                    }
                }
                $dom->save($file);

                $content = file_get_contents($file);
                $content = str_replace("<grids/>", "<grids></grids>", $content);
                $content = str_replace("<fields/>", "<fields></fields>", $content);
                file_put_contents($file, $content);
            }
        } else {
            if ($action == "delete") {
                $ids = Tools::getValue("ids", "");
                if (!empty($ids)) {
                    $ids = explode(",", $ids);

                    $content = "";
                    if (file_exists($file)) {
                        $content = file_get_contents($file);
                    }

                    // IS DEFAULT
                    if (!gridIsInXML($grid_select, $content)) {

                        addNewGrid($type, $content, $grid_select);
                    }

                    // UPDATE LIST
                    $dom = new DOMDocument();
                    $dom->preserveWhiteSpace = false;
                    $dom->formatOutput = true;
                    $dom->load($file);

                    $in_file = false;
                    $nodeGridList = $dom->getElementsByTagname('grid');
                    foreach ($nodeGridList as $nodeGrid) {
                        $nodeName = $nodeGrid->getElementsByTagname('name')->item(0);
                        if ($nodeName->nodeValue == $grid_select) {
                            $nodeValue = $nodeGrid->getElementsByTagname('value')->item(0);

                            $list = array();
                            $original_list = $nodeValue->nodeValue;
                            $exp = explode(",", $original_list);
                            foreach ($exp as $field) {
                                if (!in_array($field, $ids)) {
                                    $list[] = $field;
                                }
                            }

                            $nodeValue->nodeValue = '';
                            $v = $nodeValue->ownerDocument->createCDATASection(implode(",", $list));
                            $nodeValue->appendChild($v);
                        }
                    }
                    $dom->save($file);

                    $content = file_get_contents($file);
                    $content = str_replace("<grids/>", "<grids></grids>", $content);
                    $content = str_replace("<fields/>", "<fields></fields>", $content);
                    file_put_contents($file, $content);
                }
            } else {
                if ($action == "delete_fields") {
                    $ids = Tools::getValue("ids", "");
                    if (!empty($ids)) {
                        $ids = explode(",", $ids);

                        // UPDATE LIST
                        $dom = new DOMDocument();
                        $dom->preserveWhiteSpace = false;
                        $dom->formatOutput = true;
                        $dom->load($file);

                        $nodeFieldList = $dom->getElementsByTagname('field');
                        foreach ($nodeFieldList as $nodeField) {
                            $nodeName = $nodeField->getElementsByTagname('name')->item(0);
                            if (in_array($nodeName->nodeValue, $ids)) {
                                $nodeField->parentNode->removeChild($nodeField);
                            }
                        }
                        $dom->save($file);

                        $content = file_get_contents($file);
                        $content = str_replace("<grids/>", "<grids></grids>", $content);
                        $content = str_replace("<fields/>", "<fields></fields>", $content);
                        file_put_contents($file, $content);
                    }
                }
            }
        }
    }
}






