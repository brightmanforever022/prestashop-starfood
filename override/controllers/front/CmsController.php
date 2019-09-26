<?php

class CmsController extends CmsControllerCore
{
    function setMedia()
    {
        parent::setMedia();
        
        $this->addCSS(_PS_CSS_DIR_ . 'jquery.fancybox-1.3.4.css', 'screen');
        $this->addJqueryPlugin('fancybox');
    }
}
