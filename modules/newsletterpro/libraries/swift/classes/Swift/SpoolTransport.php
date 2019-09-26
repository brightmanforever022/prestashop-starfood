<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2009 Fabien Potencier <fabien.potencier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Stores Messages in a queue.
 *
 * @author  Fabien Potencier
 */
class NewsletterPro_Swift_SpoolTransport extends NewsletterPro_Swift_Transport_SpoolTransport
{
    /**
     * Create a new SpoolTransport.
     *
     * @param NewsletterPro_Swift_Spool $spool
     */
    public function __construct(NewsletterPro_Swift_Spool $spool)
    {
        $arguments = NewsletterPro_Swift_DependencyContainer::getInstance()
            ->createDependenciesFor('transport.spool');

        $arguments[] = $spool;

        call_user_func_array(
            array($this, 'NewsletterPro_Swift_Transport_SpoolTransport::__construct'),
            $arguments
        );
    }

    /**
     * Create a new SpoolTransport instance.
     *
     * @param NewsletterPro_Swift_Spool $spool
     *
     * @return NewsletterPro_Swift_SpoolTransport
     */
    public static function newInstance(NewsletterPro_Swift_Spool $spool)
    {
        return new self($spool);
    }
}
