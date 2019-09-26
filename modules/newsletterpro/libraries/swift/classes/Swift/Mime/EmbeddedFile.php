<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * An embedded file, in a multipart message.
 *
 * @author     Chris Corbyn
 */
class NewsletterPro_Swift_Mime_EmbeddedFile extends NewsletterPro_Swift_Mime_Attachment
{
    /**
     * Creates a new Attachment with $headers and $encoder.
     *
     * @param NewsletterPro_Swift_Mime_HeaderSet      $headers
     * @param NewsletterPro_Swift_Mime_ContentEncoder $encoder
     * @param NewsletterPro_Swift_KeyCache            $cache
     * @param NewsletterPro_Swift_Mime_Grammar        $grammar
     * @param array                     $mimeTypes optional
     */
    public function __construct(NewsletterPro_Swift_Mime_HeaderSet $headers, NewsletterPro_Swift_Mime_ContentEncoder $encoder, NewsletterPro_Swift_KeyCache $cache, NewsletterPro_Swift_Mime_Grammar $grammar, $mimeTypes = array())
    {
        parent::__construct($headers, $encoder, $cache, $grammar, $mimeTypes);
        $this->setDisposition('inline');
        $this->setId($this->getId());
    }

    /**
     * Get the nesting level of this EmbeddedFile.
     *
     * Returns {@see LEVEL_RELATED}.
     *
     * @return int
     */
    public function getNestingLevel()
    {
        return self::LEVEL_RELATED;
    }
}
