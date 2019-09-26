<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * General utility class in NewsletterPro_Swift Mailer, not to be instantiated.
 *
 *
 * @author Chris Corbyn
 */
abstract class NewsletterPro_Swift
{
    /** NewsletterPro_Swift Mailer Version number generated during dist release process */
    const VERSION = '@SWIFT_VERSION_NUMBER@';
    
    public static $initialized = false;
    public static $inits = array();

    /**
     * Registers an initializer callable that will be called the first time
     * a SwiftMailer class is autoloaded.
     *
     * This enables you to tweak the default configuration in a lazy way.
     *
     * @param mixed $callable A valid PHP callable that will be called when autoloading the first NewsletterPro_Swift class
     */
    public static function init($callable)
    {
        self::$inits[] = $callable;
    }

    /**
     * Internal autoloader for spl_autoload_register().
     *
     * @param string $class
     */
    public static function autoload($class)
    {
        // Don't interfere with other autoloaders
        if (0 !== strpos($class, 'NewsletterPro_Swift_')) {
            return;
        }

        $classname = $class;

        $cls_exp = explode('_', $class);

        if (!empty($cls_exp))
        {
            $classname = '';
            foreach ($cls_exp as $key => $value) 
            {
                if ($key == 0)
                    $classname .= $value . '_';
                else
                    $classname .= $value . '/';
            }

            $classname = rtrim($classname, '/_');
        }

        $classname = preg_replace('/^NewsletterPro_Swift\//', 'Swift/', $classname);

        // $path = dirname(__FILE__).'/'.str_replace('_', '/', $class).'.php';
        $path = dirname(__FILE__).'/'.$classname.'.php';

        if (!file_exists($path)) {
            return;
        }


        require $path;

        if (self::$inits && !self::$initialized) {
            self::$initialized = true;
            foreach (self::$inits as $init) {
                call_user_func($init);
            }
        }
    }

    /**
     * Configure autoloading using NewsletterPro_Swift Mailer.
     *
     * This is designed to play nicely with other autoloaders.
     *
     * @param mixed $callable A valid PHP callable that will be called when autoloading the first NewsletterPro_Swift class
     */
    public static function registerAutoload($callable = null)
    {
        if (null !== $callable) {
            self::$inits[] = $callable;

        }
        spl_autoload_register(array('NewsletterPro_Swift', 'autoload'));
    }
}
