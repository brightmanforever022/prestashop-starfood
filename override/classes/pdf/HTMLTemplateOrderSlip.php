<?php

class HTMLTemplateOrderSlip extends HTMLTemplateOrderSlipCore
{
    function getContent()
    {
        $this->smarty->assign(array('link' => Context::getContext()->link));
        return parent::getContent();
    }
}

