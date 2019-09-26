<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Header Signer Interface used to apply Header-Based Signature to a message
 *
 * @author     Xavier De Cock <xdecock@gmail.com>
 */
interface NewsletterPro_Swift_Signers_HeaderSigner extends NewsletterPro_Swift_Signer, NewsletterPro_Swift_InputByteStream
{
    /**
     * Exclude an header from the signed headers
     *
     * @param string $header_name
     *
     * @return NewsletterPro_Swift_Signers_HeaderSigner
     */
    public function ignoreHeader($header_name);

    /**
     * Prepare the Signer to get a new Body
     *
     * @return NewsletterPro_Swift_Signers_HeaderSigner
     */
    public function startBody();

    /**
     * Give the signal that the body has finished streaming
     *
     * @return NewsletterPro_Swift_Signers_HeaderSigner
     */
    public function endBody();

    /**
     * Give the headers already given
     *
     * @param NewsletterPro_Swift_Mime_SimpleHeaderSet $headers
     *
     * @return NewsletterPro_Swift_Signers_HeaderSigner
     */
    public function setHeaders(NewsletterPro_Swift_Mime_HeaderSet $headers);

    /**
     * Add the header(s) to the headerSet
     *
     * @param NewsletterPro_Swift_Mime_HeaderSet $headers
     *
     * @return NewsletterPro_Swift_Signers_HeaderSigner
     */
    public function addSignature(NewsletterPro_Swift_Mime_HeaderSet $headers);

    /**
     * Return the list of header a signer might tamper
     *
     * @return array
     */
    public function getAlteredHeaders();
}
