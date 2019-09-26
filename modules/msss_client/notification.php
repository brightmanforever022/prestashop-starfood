<?php

/*
 * Script is called by server to notify client about stock update
 */

require '../../config/config.inc.php';
require 'lib/MSSSMessageCreator.php';
require 'lib/MSSSClientStockUpdater.php';
require 'lib/MSSSLog.php';

// check message
$errors = '';
if (empty($_REQUEST['msg']))
{
    $errors .= "\nNo msg parameter in request";
}

if (!empty($errors))
{
    MSSSLog::reportError('wrong server notification', $errors);
    return;
}

// parse message
try
{
    $messages = MSSSMessageCreator::parseMessage($_REQUEST['msg'], Configuration::get('MSSS_CLIENT_SECRET'));
}
catch (Exception $e)
{
    MSSSLog::reportError('decrypt failure', $e->getMessage());
    return;
}

// update stock
$errors .= MSSSClientStockUpdater::updateStockBySku($messages);
if (!empty($errors))
{
    MSSSLog::reportError('errors during stock update by message(notification) from server', $errors);
}

echo 'Ok';
