<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class OrderPaymentStatusCore extends ObjectModel
{
    /** @var string Name */
    public $name;

    /** @var string Display state in the specified color */
    public $color;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'order_payment_status',
        'primary' => 'id_order_payment_status',
        'multilang' => false,
        'fields' => array(
            'color' =>        array('type' => self::TYPE_STRING, 'validate' => 'isColor'),
            /* Lang fields */
            'name' =>        array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 40),
        ),
    );


    /**
    * Get all available order statuses
    *
    * @return array Order statuses
    */
    public static function getStatuses()
    {
        $cache_id = 'OrderPaymentStatus::getStatuses';
        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'order_payment_status` ORDER BY `name` ASC');
            Cache::store($cache_id, $result);
            return $result;
        }
        return Cache::retrieve($cache_id);
    }

    
    /**
     * Checks if given status used
     * @param type $statusId
     */
    static function isUsed($statusId)
    {
        return Db::getInstance()->getValue('select id_order from '._DB_PREFIX_.'orders where payment_status_id='.$statusId);
    }
}
