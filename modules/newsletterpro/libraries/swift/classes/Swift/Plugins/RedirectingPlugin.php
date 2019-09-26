<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Redirects all email to a single recipient.
 *
 * @author     Fabien Potencier
 */
class NewsletterPro_Swift_Plugins_RedirectingPlugin implements NewsletterPro_Swift_Events_SendListener
{
    /**
     * The recipient who will receive all messages.
     *
     * @var mixed
     */
    private $_recipient;

    /**
     * List of regular expression for recipient whitelisting
     *
     * @var array
     */
    private $_whitelist = array();

    /**
     * Create a new RedirectingPlugin.
     *
     * @param mixed $recipient
     * @param array  $whitelist
     */
    public function __construct($recipient, array $whitelist = array())
    {
        $this->_recipient = $recipient;
        $this->_whitelist = $whitelist;
    }

    /**
     * Set the recipient of all messages.
     *
     * @param mixed $recipient
     */
    public function setRecipient($recipient)
    {
        $this->_recipient = $recipient;
    }

    /**
     * Get the recipient of all messages.
     *
     * @return mixed
     */
    public function getRecipient()
    {
        return $this->_recipient;
    }

    /**
     * Set a list of regular expressions to whitelist certain recipients
     *
     * @param array $whitelist
     */
    public function setWhitelist(array $whitelist)
    {
        $this->_whitelist = $whitelist;
    }

    /**
     * Get the whitelist
     *
     * @return array
     */
    public function getWhitelist()
    {
        return $this->_whitelist;
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

        // conditionally save current recipients

        if ($headers->has('to')) {
            $headers->addMailboxHeader('X-NewsletterPro_Swift-To', $message->getTo());
        }

        if ($headers->has('cc')) {
            $headers->addMailboxHeader('X-NewsletterPro_Swift-Cc', $message->getCc());
        }

        if ($headers->has('bcc')) {
            $headers->addMailboxHeader('X-NewsletterPro_Swift-Bcc', $message->getBcc());
        }

        // Filter remaining headers against whitelist
        $this->_filterHeaderSet($headers, 'To');
        $this->_filterHeaderSet($headers, 'Cc');
        $this->_filterHeaderSet($headers, 'Bcc');

        // Add each hard coded recipient
        $to = $message->getTo();
        if (null === $to) {
            $to = array();
        }

        foreach ( (array) $this->_recipient as $recipient) {
            if (!array_key_exists($recipient, $to)) {
                $message->addTo($recipient);
            }
        }
    }

    /**
     * Filter header set against a whitelist of regular expressions
     *
     * @param NewsletterPro_Swift_Mime_HeaderSet $headerSet
     * @param string $type
     */
    private function _filterHeaderSet(NewsletterPro_Swift_Mime_HeaderSet $headerSet, $type)
    {
        foreach ($headerSet->getAll($type) as $headers) {
            $headers->setNameAddresses($this->_filterNameAddresses($headers->getNameAddresses()));
        }
    }

    /**
     * Filtered list of addresses => name pairs
     *
     * @param array $recipients
     * @return array
     */
    private function _filterNameAddresses(array $recipients)
    {
        $filtered = array();

        foreach ($recipients as $address => $name) {
            if ($this->_isWhitelisted($address)) {
                $filtered[$address] = $name;
            }
        }

        return $filtered;
    }

    /**
     * Matches address against whitelist of regular expressions
     *
     * @param $recipient
     * @return bool
     */
    protected function _isWhitelisted($recipient)
    {
        if (in_array($recipient, (array) $this->_recipient)) {
            return true;
        }

        foreach ($this->_whitelist as $pattern) {
            if (preg_match($pattern, $recipient)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Invoked immediately after the Message is sent.
     *
     * @param NewsletterPro_Swift_Events_SendEvent $evt
     */
    public function sendPerformed(NewsletterPro_Swift_Events_SendEvent $evt)
    {
        $this->_restoreMessage($evt->getMessage());
    }

    private function _restoreMessage(NewsletterPro_Swift_Mime_Message $message)
    {
        // restore original headers
        $headers = $message->getHeaders();

        if ($headers->has('X-NewsletterPro_Swift-To')) {
            $message->setTo($headers->get('X-NewsletterPro_Swift-To')->getNameAddresses());
            $headers->removeAll('X-NewsletterPro_Swift-To');
        } else {
            $message->setTo(null);
        }

        if ($headers->has('X-NewsletterPro_Swift-Cc')) {
            $message->setCc($headers->get('X-NewsletterPro_Swift-Cc')->getNameAddresses());
            $headers->removeAll('X-NewsletterPro_Swift-Cc');
        }

        if ($headers->has('X-NewsletterPro_Swift-Bcc')) {
            $message->setBcc($headers->get('X-NewsletterPro_Swift-Bcc')->getNameAddresses());
            $headers->removeAll('X-NewsletterPro_Swift-Bcc');
        }
    }
}
