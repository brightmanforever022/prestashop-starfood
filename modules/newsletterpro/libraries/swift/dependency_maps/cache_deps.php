<?php

NewsletterPro_Swift_DependencyContainer::getInstance()
    ->register('cache')
    ->asAliasOf('cache.array')

    ->register('tempdir')
    ->asValue('/tmp')

    ->register('cache.null')
    ->asSharedInstanceOf('NewsletterPro_Swift_KeyCache_NullKeyCache')

    ->register('cache.array')
    ->asSharedInstanceOf('NewsletterPro_Swift_KeyCache_ArrayKeyCache')
    ->withDependencies(array('cache.inputstream'))

    ->register('cache.disk')
    ->asSharedInstanceOf('NewsletterPro_Swift_KeyCache_DiskKeyCache')
    ->withDependencies(array('cache.inputstream', 'tempdir'))

    ->register('cache.inputstream')
    ->asNewInstanceOf('NewsletterPro_Swift_KeyCache_SimpleKeyCacheInputStream')
;
