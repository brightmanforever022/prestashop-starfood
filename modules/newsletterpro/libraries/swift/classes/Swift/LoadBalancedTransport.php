<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Redundantly and rotationally uses several Transport implementations when sending.
 *
 * @author     Chris Corbyn
 */
class NewsletterPro_Swift_LoadBalancedTransport extends NewsletterPro_Swift_Transport_LoadBalancedTransport
{
    /**
     * Creates a new LoadBalancedTransport with $transports.
     *
     * @param array $transports
     */
    public function __construct($transports = array())
    {
        call_user_func_array(
            array($this, 'NewsletterPro_Swift_Transport_LoadBalancedTransport::__construct'),
            NewsletterPro_Swift_DependencyContainer::getInstance()
                ->createDependenciesFor('transport.loadbalanced')
            );

        $this->setTransports($transports);
    }

    /**
     * Create a new LoadBalancedTransport instance.
     *
     * @param array $transports
     *
     * @return NewsletterPro_Swift_LoadBalancedTransport
     */
    public static function newInstance($transports = array())
    {
        return new self($transports);
    }
}
