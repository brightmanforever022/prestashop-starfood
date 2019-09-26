<?php

class Language extends LanguageCore
{
    /**
     * Load all languages in memory for caching
     */
    public static function loadLanguages()
    {
        self::$_LANGUAGES = array();

        $sql = 'SELECT l.*, ls.`id_shop`
				FROM `'._DB_PREFIX_.'lang` l
				LEFT JOIN `'._DB_PREFIX_.'lang_shop` ls ON (l.id_lang = ls.id_lang)';

        $result = Db::getInstance()->executeS($sql);
        
        // set default first
        $defLangId = Configuration::get('PS_LANG_DEFAULT');
        foreach ($result as $row) 
        {
            if ($row['id_lang'] == $defLangId)
            {
                if (!isset(self::$_LANGUAGES[(int) $row['id_lang']]))
                {
                    self::$_LANGUAGES[(int) $row['id_lang']] = $row;
                }
                self::$_LANGUAGES[(int) $row['id_lang']]['shops'][(int) $row['id_shop']] = true;
                break;
            }
        }
        
        // others after
        foreach ($result as $row) 
        {
            if ($row['id_lang'] != $defLangId)
            {
                if (!isset(self::$_LANGUAGES[(int) $row['id_lang']]))
                {
                    self::$_LANGUAGES[(int) $row['id_lang']] = $row;
                }
                self::$_LANGUAGES[(int) $row['id_lang']]['shops'][(int) $row['id_shop']] = true;
            }
        }
    }
}
