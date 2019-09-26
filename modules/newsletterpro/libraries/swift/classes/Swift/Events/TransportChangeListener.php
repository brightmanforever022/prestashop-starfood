<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Listens for changes within the Transport system.
 *
 * @author     Chris Corbyn
 */
interface NewsletterPro_Swift_Events_TransportChangeListener extends NewsletterPro_Swift_Events_EventListener
{
    /**
     * Invoked just before a Transport is started.
     *
     * @param NewsletterPro_Swift_Events_TransportChangeEvent $evt
     */
    public function beforeTransportStarted(NewsletterPro_Swift_Events_TransportChangeEvent $evt);

    /**
     * Invoked immediately after the Transport is started.
     *
     * @param NewsletterPro_Swift_Events_TransportChangeEvent $evt
     */
    public function transportStarted(NewsletterPro_Swift_Events_TransportChangeEvent $evt);

    /**
     * Invoked just before a Transport is stopped.
     *
     * @param NewsletterPro_Swift_Events_TransportChangeEvent $evt
     */
    public function beforeTransportStopped(NewsletterPro_Swift_Events_TransportChangeEvent $evt);

    /**
     * Invoked immediately after the Transport is stopped.
     *
     * @param NewsletterPro_Swift_Events_TransportChangeEvent $evt
     */
    public function transportStopped(NewsletterPro_Swift_Events_TransportChangeEvent $evt);
}
