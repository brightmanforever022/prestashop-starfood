<?php
/*
 * This file is part of SwiftMailer.
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Replaces the sender of a message.
 *
 * @author     Arjen Brouwer
 */
class NewsletterPro_Swift_Plugins_ImpersonatePlugin implements NewsletterPro_Swift_Events_SendListener
{
    /**
     * The sender to impersonate.
     *
     * @var String
     */
    private $_sender;

    /**
     * Create a new ImpersonatePlugin to impersonate $sender.
     *
     * @param string $sender address
     */
    public function __construct($sender)
    {
        $this->_sender = $sender;
    }

    /**
     * Invoked immediately before the Message is sent.
     *
     * @param NewsletterPro_Swift_Events_SendEvent $evt
     */
    public function beforeSendPerformed(NewsletterPro_Swift_Events_SendEvent $evt)
    {
        $message = $evt->getMessage();
        $headers = $message->getHeaders();

        // save current recipients
        $headers->addPathHeader('X-NewsletterPro_Swift-Return-Path', $message->getReturnPath());

        // replace them with the one to send to
        $message->setReturnPath($this->_sender);
    }

    /**
     * Invoked immediately after the Message is sent.
     *
     * @param NewsletterPro_Swift_Events_SendEvent $evt
     */
    public function sendPerformed(NewsletterPro_Swift_Events_SendEvent $evt)
    {
        $message = $evt->getMessage();

        // restore original headers
        $headers = $message->getHeaders();

        if ($headers->has('X-NewsletterPro_Swift-Return-Path')) {
            $message->setReturnPath($headers->get('X-NewsletterPro_Swift-Return-Path')->getAddress());
            $headers->removeAll('X-NewsletterPro_Swift-Return-Path');
        }
    }
}
