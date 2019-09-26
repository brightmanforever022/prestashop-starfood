<?php

class AddressFormat extends AddressFormatCore
{

    /**
     * Generates the full address text with Microformat attributes
     *
     * @param Address $address
     * @param array $patternRules A defined rules array to avoid some pattern
     * @param string $newLine A string containing the newLine format
     * @param string $separator A string containing the separator format
     * @param array $style
     * @return string
     */
    public static function generateAddress(Address $address, $patternRules = array(), $newLine = "\r\n", $separator = ' ', $style = array())
    {
        $microformat = array(
            'p-given-name' => 'firstname',
            'p-family-name' => 'lastname',
            'p-tel' => array('phone', 'phone_mobile'),
            'p-org' => 'company',
            'p-vat' => 'vat_number',
            'p-street-address' => 'address1',
            'p-extended-address' => 'address2',
            'p-locality' => 'city',
            'p-postal-code' => 'postcode',
            'p-region' => 'State:name',
            'p-country-name' => 'Country:name'
        );
        $addressFields = AddressFormat::getOrderedAddressFields($address->id_country);
        $addressFormatedValues = AddressFormat::getFormattedAddressFieldsValues($address, $addressFields);
    
        $addressText = '<p class="h-card">';
        foreach ($addressFields as $line) {
            if (($patternsList = preg_split(self::_CLEANING_REGEX_, $line, -1, PREG_SPLIT_NO_EMPTY))) {
                $tmpText = array();
                $microformatIndex = null;
                foreach ($patternsList as $pi => $pattern) {
                    foreach($microformat as $microKey => $psKey){
                        if( is_string($psKey) && ($pattern == $psKey) ){
                            $microformatIndex = $microKey;
                        }
                        elseif( is_array($psKey) && in_array($pattern, $psKey)){
                            $microformatIndex = $microKey;
                        }
                    }
    
                    if ((!array_key_exists('avoid', $patternRules)) ||
                        (array_key_exists('avoid', $patternRules) && !in_array($pattern, $patternRules['avoid']))) {
                            $tmpText[$pi] = (isset($addressFormatedValues[$pattern]) && !empty($addressFormatedValues[$pattern])) ?
                            (((isset($style[$pattern])) ?
                                (sprintf($style[$pattern], $addressFormatedValues[$pattern])) :
                                $addressFormatedValues[$pattern]).$separator) : '';
                        }
                        if(($microformatIndex !== false) && !empty(trim($tmpText[$pi]))){
                            $tmpText[$pi] = '<span class="'.$microformatIndex.'">'. $tmpText[$pi] .'</span>';
                        }
                        $tmpText[$pi] = trim($tmpText[$pi]);
                }
                $tmpText = implode('', $tmpText);
                $addressText .= (!empty($tmpText)) ? $tmpText.$newLine: '';
            }
        }
        $addressText .= '</p>';
    
        $addressText = preg_replace('/'.preg_quote($newLine, '/').'$/i', '', $addressText);
        $addressText = rtrim($addressText, $separator);
        return $addressText;
    }
    
}