<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Proxy for quoted-printable content encoders.
 *
 * Switches on the best QP encoder implementation for current charset.
 *
 * @author     Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 */
class NewsletterPro_Swift_Mime_ContentEncoder_QpContentEncoderProxy implements NewsletterPro_Swift_Mime_ContentEncoder
{
    /**
     * @var NewsletterPro_Swift_Mime_ContentEncoder_QpContentEncoder
     */
    private $safeEncoder;

    /**
     * @var NewsletterPro_Swift_Mime_ContentEncoder_NativeQpContentEncoder
     */
    private $nativeEncoder;

    /**
     * @var null|string
     */
    private $charset;

    /**
     * Constructor.
     *
     * @param NewsletterPro_Swift_Mime_ContentEncoder_QpContentEncoder       $safeEncoder
     * @param NewsletterPro_Swift_Mime_ContentEncoder_NativeQpContentEncoder $nativeEncoder
     * @param string|null                                      $charset
     */
    public function __construct(NewsletterPro_Swift_Mime_ContentEncoder_QpContentEncoder $safeEncoder, NewsletterPro_Swift_Mime_ContentEncoder_NativeQpContentEncoder $nativeEncoder, $charset)
    {
        $this->safeEncoder = $safeEncoder;
        $this->nativeEncoder = $nativeEncoder;
        $this->charset = $charset;
    }

    /**
     * Make a deep copy of object
     */
    public function __clone()
    {
        $this->safeEncoder = clone $this->safeEncoder;
        $this->nativeEncoder = clone $this->nativeEncoder;
    }

    /**
     * {@inheritdoc}
     */
    public function charsetChanged($charset)
    {
        $this->charset = $charset;
    }

    /**
     * {@inheritdoc}
     */
    public function encodeByteStream(NewsletterPro_Swift_OutputByteStream $os, NewsletterPro_Swift_InputByteStream $is, $firstLineOffset = 0, $maxLineLength = 0)
    {
        $this->getEncoder()->encodeByteStream($os, $is, $firstLineOffset, $maxLineLength);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'quoted-printable';
    }

    /**
     * {@inheritdoc}
     */
    public function encodeString($string, $firstLineOffset = 0, $maxLineLength = 0)
    {
        return $this->getEncoder()->encodeString($string, $firstLineOffset, $maxLineLength);
    }

    /**
     * @return NewsletterPro_Swift_Mime_ContentEncoder
     */
    private function getEncoder()
    {
        return 'utf-8' === $this->charset ? $this->nativeEncoder : $this->safeEncoder;
    }
}
