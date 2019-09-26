<?php

class AdminController extends AdminControllerCore
{
    public function init()
    {
        // makes employye/admin session never expiring (only cookie lifetime limit affects)
        $this->context->cookie->last_activity = time();
        parent::init();
    }
}
