<?php
/**
 * OrderEdit
 *
 * @category  Module
 * @author    silbersaiten <info@silbersaiten.de>
 * @support   silbersaiten <support@silbersaiten.de>
 * @copyright 2016 silbersaiten
 * @version   1.2.0
 * @link      http://www.silbersaiten.de
 * @license   See joined file licence.txt
 */

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
require_once(dirname(__FILE__).'/orderedit.php');

if (Employee::checkPassword(Tools::getValue('iem'), Tools::getValue('iemp'))) {
    $context = Context::getContext();

    $context->employee = new Employee(Tools::getValue('iem'));
    $context->cookie->passwd = Tools::getValue('iemp');
    $orderedit = new OrderEdit();
    $action = Tools::getValue('action', false);

    if ($action && method_exists($orderedit, 'execute'.Tools::ucfirst($action))) {
        $requisites = $orderedit->getExecutionPrerequisites();
        Hook::exec(
            'orderEditActionBefore'.Tools::ucfirst($action),
            array('orderedit' => $orderedit, 'std_rq' => &$requisites)
        );
        $orderedit->{'execute'.Tools::ucfirst($action)}($requisites);
        Hook::exec(
            'orderEditActionAfter'.Tools::ucfirst($action),
            array('orderedit' => $orderedit, 'std_rq' => $requisites)
        );
    } elseif ($action == 'getEditLink') {
        $id_order = Tools::getValue('id_order');

        if ($id_order) {
            die($orderedit->getEditLink($id_order));
        }
    }
} else {
    die(Tools::displayError('Please log in first'));
}
