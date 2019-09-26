<?php
/**
 * Store Commander
 *
 * @category administration
 * @author Store Commander - support@storecommander.com
 * @version 2015-09-15
 * @uses Prestashop modules
 * @since 2009
 * @copyright Copyright &copy; 2009-2015, Store Commander
 * @license commercial
 * All rights reserved! Copying, duplication strictly prohibited
 *
 * *****************************************
 * *           STORE COMMANDER             *
 * *   http://www.StoreCommander.com       *
 * *            V 2015-09-15               *
 * *****************************************
 *
 * Compatibility: PS version: 1.1 to 1.6.1
 *
 **/

class ExtensionPMCM
{
    static $modInstance;

    static public function hasPMCacheManager()
    {
        $return = false;
        if (!empty(self::$modInstance) && is_object(self::$modInstance)) {
            $return = self::$modInstance;
        } else {
            $version = SCI::getConfigurationValue("PM_CM_LAST_VERSION");
            if (!empty($version)) {
                if ($moduleInstance = Module::getInstanceByName('pm_cachemanager')) {
                    $return = $moduleInstance;
                    self::$modInstance = $moduleInstance;
                }
            }
        }
        return $return;
    }

    /*
     * DeleteFunctions
     */

    static public function clearFromSC()
    {
        $modInstance = self::hasPMCacheManager();
        if (!empty($modInstance) && is_object($modInstance))
        {
            if (is_callable(array($modInstance, 'clearCacheFromSC')))
                call_user_func(array($modInstance, 'clearCacheFromSC'));
        }
    }


    static public function clearFromIdsProduct($ids)
    {
        $modInstance = self::hasPMCacheManager();
        if (!empty($modInstance) && is_object($modInstance))
        {
            if (version_compare($modInstance->version, '1.2.8', '<='))
                self::clearFromSC();
            else
            {
                if(!is_array($ids))
                    $ids = explode(",",$ids);
                if(!empty($ids))
                    pm_cachemanager::deleteCacheFromIdProduct($ids);
            }
        }
    }

    static public function clearFromIdsCategory($ids_cat)
    {
        $modInstance = self::hasPMCacheManager();
        if (!empty($modInstance) && is_object($modInstance))
        {
            if (version_compare($modInstance->version, '1.2.8', '<='))
                self::clearFromSC();
            else
            {
                if(!is_array($ids_cat))
                    $ids_cat = explode(",",$ids_cat);

                $ids = array();
                foreach($ids_cat as $id_cat)
                {
                    $ids_product = $modInstance->getIdsProductFromIdCategory($id_cat);
                    if(!empty($ids_product))
                    {
                        if(!empty($ids) && count($ids)>0)
                            $ids = array_merge($ids, $ids_product);
                        else
                            $ids = $ids_product;
                    }
                }

                if(!empty($ids) && $modInstance->_isFilledArray($ids))
                    pm_cachemanager::deleteCacheFromIdProduct($ids);
            }
        }
    }

    static public function clearFromIdsAttributeGroup($ids_ag)
    {
        $modInstance = self::hasPMCacheManager();
        if (!empty($modInstance) && is_object($modInstance))
        {
            if (version_compare($modInstance->version, '1.2.8', '<='))
                self::clearFromSC();
            else
            {
                if(!is_array($ids_ag))
                    $ids_ag = explode(",",$ids_ag);

                $ids = array();
                foreach($ids_ag as $id_ag)
                {
                    $ids_product = $modInstance->getIdsProductFromIdAttributeGroup($id_ag);
                    if(!empty($ids_product))
                    {
                        if(!empty($ids) && count($ids)>0)
                            $ids = array_merge($ids, $ids_product);
                        else
                            $ids = $ids_product;
                    }
                }

                if(!empty($ids) && $modInstance->_isFilledArray($ids))
                    pm_cachemanager::deleteCacheFromIdProduct($ids);
            }
        }
    }

    static public function clearFromIdsAttribute($ids_a)
    {
        $modInstance = self::hasPMCacheManager();
        if (!empty($modInstance) && is_object($modInstance))
        {
            if (version_compare($modInstance->version, '1.2.8', '<='))
                self::clearFromSC();
            else
            {
                if(!is_array($ids_a))
                    $ids_a = explode(",",$ids_a);

                $ids = array();
                foreach($ids_a as $id_a)
                {
                    $ids_product = $modInstance->getIdsProductFromIdAttribute($id_a);
                    if(!empty($ids_product))
                    {
                        if(!empty($ids) && count($ids)>0)
                            $ids = array_merge($ids, $ids_product);
                        else
                            $ids = $ids_product;
                    }
                }

                if(!empty($ids) && $modInstance->_isFilledArray($ids))
                    pm_cachemanager::deleteCacheFromIdProduct($ids);
            }
        }
    }

    static public function clearFromIdsFeature($ids_f)
    {
        $modInstance = self::hasPMCacheManager();
        if (!empty($modInstance) && is_object($modInstance))
        {
            if (version_compare($modInstance->version, '1.2.8', '<='))
                self::clearFromSC();
            else
            {
                if(!is_array($ids_f))
                    $ids_f = explode(",",$ids_f);

                $ids = array();
                foreach($ids_f as $id_f)
                {
                    $ids_product = $modInstance->getIdsProductFromIdFeature($id_f);
                    if(!empty($ids_product))
                    {
                        if(!empty($ids) && count($ids)>0)
                            $ids = array_merge($ids, $ids_product);
                        else
                            $ids = $ids_product;
                    }
                }

                if(!empty($ids) && $modInstance->_isFilledArray($ids))
                    pm_cachemanager::deleteCacheFromIdProduct($ids);
            }
        }
    }

    static public function clearFromIdsFeatureValue($ids_fv)
    {
        $modInstance = self::hasPMCacheManager();
        if (!empty($modInstance) && is_object($modInstance))
        {
            if (version_compare($modInstance->version, '1.2.8', '<='))
                self::clearFromSC();
            else
            {
                if(!is_array($ids_fv))
                    $ids_fv = explode(",",$ids_fv);

                $ids = array();
                foreach($ids_fv as $id_fv)
                {
                    $ids_product = $modInstance->getIdsProductFromIdFeatureValue($id_fv);
                    if(!empty($ids_product))
                    {
                        if(!empty($ids) && count($ids)>0)
                            $ids = array_merge($ids, $ids_product);
                        else
                            $ids = $ids_product;
                    }
                }

                if(!empty($ids) && $modInstance->_isFilledArray($ids))
                    pm_cachemanager::deleteCacheFromIdProduct($ids);
            }
        }
    }
}