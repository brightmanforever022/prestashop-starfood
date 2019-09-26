<?php

class AdminCurrenciesController extends AdminCurrenciesControllerCore
{
    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Currencies'),
                'icon' => 'icon-money'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Currency name'),
                    'name' => 'name',
                    'size' => 30,
                    'maxlength' => 32,
                    'required' => true,
                    'hint' => $this->l('Only letters and the minus character are allowed.')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('ISO code'),
                    'name' => 'iso_code',
                    'maxlength' => 32,
                    'required' => true,
                    'hint' => $this->l('ISO code (e.g. USD for Dollars, EUR for Euros, etc.).')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Numeric ISO code'),
                    'name' => 'iso_code_num',
                    'maxlength' => 32,
                    'required' => true,
                    'hint' => $this->l('Numeric ISO code (e.g. 840 for Dollars, 978 for Euros, etc.).')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Symbol'),
                    'name' => 'sign',
                    'maxlength' => 8,
                    'required' => true,
                    'hint' => $this->l('Will appear in front office (e.g. $, &euro;, etc.)')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Exchange rate'),
                    'name' => 'conversion_rate',
                    'maxlength' => 11,
                    'required' => true,
                    'hint' => $this->l('Exchange rates are calculated from one unit of your shop\'s default currency. For example, if the default currency is euros and your chosen currency is dollars, type "1.20" (1&euro; = $1.20).')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Currency format'),
                    'name' => 'format',
                    'maxlength' => 11,
                    'required' => true,
                    'hint' =>$this->l('Applies to all prices (e.g. $1,240.15).'),
                    'options' => array(
                        'query' => array(
                            array('key' => 1, 'name' => 'X0,000.00 ('.$this->l('Such as with Dollars').')'),
                            array('key' => 2, 'name' => '0 000,00X ('.$this->l('Such as with Euros').')'),
                            array('key' => 3, 'name' => 'X0.000,00'),
                            array('key' => 4, 'name' => '0,000.00X'),
                            array('key' => 5, 'name' => '0\'000.00X'), // Added for the switzerland currency
                            array('key' => 6, 'name' => '0.000,00X'),
                        ),
                        'name' => 'name',
                        'id' => 'key'
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Decimals'),
                    'name' => 'decimals',
                    'required' => false,
                    'is_bool' => true,
                    'hint' => $this->l('Display decimals in prices.'),
                    'values' => array(
                        array(
                            'id' => 'decimals_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'decimals_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Spacing'),
                    'name' => 'blank',
                    'required' => false,
                    'is_bool' => true,
                    'hint' => $this->l('Include a space between symbol and price (e.g. $1,240.15 -> $ 1,240.15).'),
                    'values' => array(
                        array(
                            'id' => 'blank_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'blank_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                )
            )
        );

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            );
        }

        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
        );

        return AdminController::renderForm();
    }
}
