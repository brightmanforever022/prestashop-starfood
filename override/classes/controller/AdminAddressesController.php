<?php

class AdminAddressesController extends AdminAddressesControllerCore
{
    public function __construct()
    {
        parent::__construct();
        $this->fields_list['address1']['filter_key'] = 'a!address1';
        $this->fields_list['postcode']['filter_key'] = 'a!postcode';
        $this->fields_list['city']['filter_key'] = 'a!city';
    }
}
