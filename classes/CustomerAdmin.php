<?php

/**
 * Class purposed to set other validation rules for customers that are created from admin area
 */

class CustomerAdmin extends Customer
{
    function __construct($id = null)
    {
        parent::__construct($id);
        //$this->definition['fields'][]
    }
}