<?php

NewsletterPro_Swift_DependencyContainer::getInstance()
    ->register('message.message')
    ->asNewInstanceOf('NewsletterPro_Swift_Message')

    ->register('message.mimepart')
    ->asNewInstanceOf('NewsletterPro_Swift_MimePart')
;
