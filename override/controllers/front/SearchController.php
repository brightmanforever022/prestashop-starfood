<?php

class SearchController extends SearchControllerCore
{
    public function initContent()
    {
        parent::initContent();
        $this->context->smarty->assign(array('hide_left_column' => 1));        
    }
}