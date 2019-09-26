<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$sql = array();

$sql[] = 'CREATE TABLE ss_notification_slave
  (
    id          int           auto_increment,
    sku         varchar(40)   not null,
    change_qty  int           not null,
    status      tinyint       not null,
    pid         int           not null DEFAULT \'0\',  

    primary key(id)
  )
 ENGINE = '._MYSQL_ENGINE_.';

CREATE INDEX ss_notification_slaveIDX1 ON ss_notification_slave(sku);
CREATE INDEX ss_notification_slaveIDX2 ON ss_notification_slave(status);
CREATE INDEX ss_notification_slaveIDX3 ON ss_notification_slave(pid);';


foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
