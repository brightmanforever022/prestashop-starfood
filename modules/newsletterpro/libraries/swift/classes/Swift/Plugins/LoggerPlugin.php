<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Does real time logging of Transport level information.
 *
 * @author     Chris Corbyn
 */
class NewsletterPro_Swift_Plugins_LoggerPlugin implements NewsletterPro_Swift_Events_CommandListener, NewsletterPro_Swift_Events_ResponseListener, NewsletterPro_Swift_Events_TransportChangeListener, NewsletterPro_Swift_Events_TransportExceptionListener, NewsletterPro_Swift_Plugins_Logger
{
    /** The logger which is delegated to */
    private $_logger;

    /**
     * Create a new LoggerPlugin using $logger.
     *
     * @param NewsletterPro_Swift_Plugins_Logger $logger
     */
    public function __construct(NewsletterPro_Swift_Plugins_Logger $logger)
    {
        $this->_logger = $logger;
    }

    /**
     * Add a log entry.
     *
     * @param string $entry
     */
    public function add($entry)
    {
        $this->_logger->add($entry);
    }

    /**
     * Clear the log contents.
     */
    public function clear()
    {
        $this->_logger->clear();
    }

    /**
     * Get this log as a string.
     *
     * @return string
     */
    public function dump()
    {
        return $this->_logger->dump();
    }

    /**
     * Invoked immediately following a command being sent.
     *
     * @param NewsletterPro_Swift_Events_CommandEvent $evt
     */
    public function commandSent(NewsletterPro_Swift_Events_CommandEvent $evt)
    {
        $command = $evt->getCommand();
        $this->_logger->add(sprintf(">> %s", $command));
    }

    /**
     * Invoked immediately following a response coming back.
     *
     * @param NewsletterPro_Swift_Events_ResponseEvent $evt
     */
    public function responseReceived(NewsletterPro_Swift_Events_ResponseEvent $evt)
    {
        $response = $evt->getResponse();
        $this->_logger->add(sprintf("<< %s", $response));
    }

    /**
     * Invoked just before a Transport is started.
     *
     * @param NewsletterPro_Swift_Events_TransportChangeEvent $evt
     */
    public function beforeTransportStarted(NewsletterPro_Swift_Events_TransportChangeEvent $evt)
    {
        $transportName = get_class($evt->getSource());
        $this->_logger->add(sprintf("++ Starting %s", $transportName));
    }

    /**
     * Invoked immediately after the Transport is started.
     *
     * @param NewsletterPro_Swift_Events_TransportChangeEvent $evt
     */
    public function transportStarted(NewsletterPro_Swift_Events_TransportChangeEvent $evt)
    {
        $transportName = get_class($evt->getSource());
        $this->_logger->add(sprintf("++ %s started", $transportName));
    }

    /**
     * Invoked just before a Transport is stopped.
     *
     * @param NewsletterPro_Swift_Events_TransportChangeEvent $evt
     */
    public function beforeTransportStopped(NewsletterPro_Swift_Events_TransportChangeEvent $evt)
    {
        $transportName = get_class($evt->getSource());
        $this->_logger->add(sprintf("++ Stopping %s", $transportName));
    }

    /**
     * Invoked immediately after the Transport is stopped.
     *
     * @param NewsletterPro_Swift_Events_TransportChangeEvent $evt
     */
    public function transportStopped(NewsletterPro_Swift_Events_TransportChangeEvent $evt)
    {
        $transportName = get_class($evt->getSource());
        $this->_logger->add(sprintf("++ %s stopped", $transportName));
    }

    /**
     * Invoked as a TransportException is thrown in the Transport system.
     *
     * @param NewsletterPro_Swift_Events_TransportExceptionEvent $evt
     */
    public function exceptionThrown(NewsletterPro_Swift_Events_TransportExceptionEvent $evt)
    {
        $e = $evt->getException();
        $message = $e->getMessage();
        $this->_logger->add(sprintf("!! %s", $message));
        $message .= PHP_EOL;
        $message .= 'Log data:'.PHP_EOL;
        $message .= $this->_logger->dump();
        $evt->cancelBubble();
        throw new NewsletterPro_Swift_TransportException($message);
    }
}
