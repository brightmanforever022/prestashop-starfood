<?php

NewsletterPro_Swift_DependencyContainer::getInstance()
    ->register('transport.smtp')
    ->asNewInstanceOf('NewsletterPro_Swift_Transport_EsmtpTransport')
    ->withDependencies(array(
        'transport.buffer',
        array('transport.authhandler'),
        'transport.eventdispatcher',
    ))

    ->register('transport.sendmail')
    ->asNewInstanceOf('NewsletterPro_Swift_Transport_SendmailTransport')
    ->withDependencies(array(
        'transport.buffer',
        'transport.eventdispatcher',
    ))

    ->register('transport.mail')
    ->asNewInstanceOf('NewsletterPro_Swift_Transport_MailTransport')
    ->withDependencies(array('transport.mailinvoker', 'transport.eventdispatcher'))

    ->register('transport.loadbalanced')
    ->asNewInstanceOf('NewsletterPro_Swift_Transport_LoadBalancedTransport')

    ->register('transport.failover')
    ->asNewInstanceOf('NewsletterPro_Swift_Transport_FailoverTransport')

    ->register('transport.spool')
    ->asNewInstanceOf('NewsletterPro_Swift_Transport_SpoolTransport')
    ->withDependencies(array('transport.eventdispatcher'))

    ->register('transport.null')
    ->asNewInstanceOf('NewsletterPro_Swift_Transport_NullTransport')
    ->withDependencies(array('transport.eventdispatcher'))

    ->register('transport.mailinvoker')
    ->asSharedInstanceOf('NewsletterPro_Swift_Transport_SimpleMailInvoker')

    ->register('transport.buffer')
    ->asNewInstanceOf('NewsletterPro_Swift_Transport_StreamBuffer')
    ->withDependencies(array('transport.replacementfactory'))

    ->register('transport.authhandler')
    ->asNewInstanceOf('NewsletterPro_Swift_Transport_Esmtp_AuthHandler')
    ->withDependencies(array(
        array(
            'transport.crammd5auth',
            'transport.loginauth',
            'transport.plainauth',
            'transport.ntlmauth',
            'transport.xoauth2auth',
        ),
    ))

    ->register('transport.crammd5auth')
    ->asNewInstanceOf('NewsletterPro_Swift_Transport_Esmtp_Auth_CramMd5Authenticator')

    ->register('transport.loginauth')
    ->asNewInstanceOf('NewsletterPro_Swift_Transport_Esmtp_Auth_LoginAuthenticator')

    ->register('transport.plainauth')
    ->asNewInstanceOf('NewsletterPro_Swift_Transport_Esmtp_Auth_PlainAuthenticator')

    ->register('transport.xoauth2auth')
    ->asNewInstanceOf('NewsletterPro_Swift_Transport_Esmtp_Auth_XOAuth2Authenticator')

    ->register('transport.ntlmauth')
    ->asNewInstanceOf('NewsletterPro_Swift_Transport_Esmtp_Auth_NTLMAuthenticator')

    ->register('transport.eventdispatcher')
    ->asNewInstanceOf('NewsletterPro_Swift_Events_SimpleEventDispatcher')

    ->register('transport.replacementfactory')
    ->asSharedInstanceOf('NewsletterPro_Swift_StreamFilters_StringReplacementFilterFactory')
;
