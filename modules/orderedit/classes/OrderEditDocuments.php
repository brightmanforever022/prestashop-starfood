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

class OrderEditDocuments extends OrderEdit
{
    private function deleteOrderInvoice($id_invoice)
    {
        $invoice = new OrderInvoice($id_invoice);

        if (Validate::isLoadedObject($invoice)) {
            $invoice_payments = self::getInvoicePayments($id_invoice);

            self::purgeApparentInvoiceInfos($id_invoice);

            if ($invoice_payments) {
                Db::getInstance()->delete(
                    'order_payment',
                    '`id_order_payment` IN('.(implode(',', $invoice_payments)).')'
                );
            }

            Db::getInstance()->autoExecute(
                _DB_PREFIX_.'order_detail',
                array(
                    'id_order_invoice' => 0
                ),
                'UPDATE',
                '`id_order_invoice` = '.(int)$id_invoice
            );
        }
    }

    private static function purgeApparentInvoiceInfos($id_invoice)
    {
        $tables = array(
            'order_invoice',
            'order_invoice_payment',
            'order_invoice_tax'
        );

        foreach ($tables as $table) {
            Db::getInstance()->delete($table, '`id_order_invoice` = '.(int)$id_invoice);
        }
    }

    private static function getInvoicePayments($id_invoice)
    {
        $prepared = array();
        $result = Db::getInstance()->ExecuteS(
            'SELECT
                `id_order_payment`
            FROM
                `'._DB_PREFIX_.'order_invoice_payment`
            WHERE
                `id_order_invoice` = '.(int)$id_invoice
        );

        if ($result && count($result)) {
            foreach ($result as $payment) {
                array_push($prepared, (int)$payment['id_order_payment']);
            }
        }

        return count($prepared) ? $prepared : false;
    }

    private static function deleteOrderPayments($payment_ids)
    {
        return Db::getInstance()->Execute(
            'DELETE FROM
                `'._DB_PREFIX_.'order_payment`
            WHERE
                `id_order_payment` IN ('.(implode($payment_ids)).')'
        );
    }
}
