<?php

class CMS extends CMSCore
{
    public static $definition = array(
        'table' => 'cms',
        'primary' => 'id_cms',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => array(
            'id_cms_category' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'position' =>            array('type' => self::TYPE_INT),
            'indexation' =>         array('type' => self::TYPE_BOOL),
            'active' =>            array('type' => self::TYPE_BOOL),

            /* Lang fields */
            'meta_description' =>    array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'meta_keywords' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'meta_title' =>            array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
            'link_rewrite' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isLinkRewrite', 'required' => true, 'size' => 128),
            'content' =>            array('type' => self::TYPE_HTML, 'lang' => true, 'size' => 3999999999999),
        ),
    );
}
