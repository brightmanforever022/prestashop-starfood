<?php

/*
 * Script sends messages to server. Should be called by cron.
 */

require '../../config/config.inc.php';
require 'lib/MSSSMessageCreator.php';
require 'lib/MSSSClientStockUpdater.php';
require 'lib/MSSSRequestSender.php';
require 'lib/MSSSLog.php';


// read messages that need to be sent
$stockUpdater = new MSSSClientStockUpdater();
$messages = $stockUpdater->getMessagesToSend();
//echo "messages:\n";
//print_r($messages);

// check if we need to send something
if (!count($messages))
{
    return;
}

// create message
$msgToSend = MSSSMessageCreator::createMessage($messages, Configuration::get('MSSS_CLIENT_SECRET'));

// send message
$answer = MSSSRequestSender::sendPostRequest(Configuration::get('MSSS_CLIENT_SERVER_NOTIFICATION_URL'), array('msg' => $msgToSend,
    'sourceId'=>  Configuration::get('MSSS_CLIENT_SOURCE_ID')));
//echo "answer: \n";
//print_r($answer);

// check answer
if ($answer['status'] != '200')
{
    // send message to admin about error
    MSSSLog::reportError('bad response from server', "status: {$answer['status']}\nresponse: {$answer['responseBody']}\n" . 
            print_r($messages, true));

    // mark messages processed with error
    $stockUpdater->markMessagesProcessedWithError();
}
else
{
    // update stock
    $newQuantities = json_decode($answer['responseBody'], true);
    if (!is_array($newQuantities))
    {
        $errors = 'Unexpected answer from server: '.print_r($answer['responseBody'], true);
        // mark messages processed with error
        $stockUpdater->markMessagesProcessedWithError();
    }
    else
    {
        $errors = MSSSClientStockUpdater::updateStockBySku($newQuantities);
    }
    if (!empty($errors))
    {
        MSSSLog::reportError('errors during stock update by client notification', $errors);
    }
    else
    {
        $stockUpdater->markMessagesProcessed();
    }
}