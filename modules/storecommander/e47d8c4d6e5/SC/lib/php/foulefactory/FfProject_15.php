<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class FfProject extends ObjectModel
{
    public $id;

    public $id_ff_project;
    public $name;
    public $instructions;
    public $type;
    public $started_at;
    public $duration;
    public $tarif;
    public $status;
    public $percent;
    public $source;
    public $id_category;
    public $params;
    public $nb_product;
    public $created_at;
    public $updated_at;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'sc_ff_project',
        'primary' => 'id_project',
        'multilang' => false,
        'fields' => array(
            'id_ff_project' => 			    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'name' => 						array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 255),
            'instructions' =>            	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'type' => 						array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 100),
            'started_at' =>             	array('type' => self::TYPE_DATE, 'validate' => 'isGenericName', 'size' => 19),
            'duration' => 			    	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'tarif' => 			            array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
            'status' => 					array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 100),
            'percent' => 			    	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'source' => 					array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'id_category' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'params' =>                    	array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'nb_product' =>                 array('type' => self::TYPE_STRING, 'validate' => 'isUnsignedInt'),
            'created_at' =>             	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'updated_at' =>             	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        )
    );

    public function add($autodate = true, $null_values = false)
    {
        $this->created_at=date("Y-m-d");
        $this->updated_at=date("Y-m-d");
        $this->status="created";

        // CREATION CATEGORY
        $id_parent=SCI::getConfigurationValue("SC_FOULEFACTORY_CATEGORY");
        $name=$this->name;

        $newcategory=new Category();
        $newcategory->id_parent=$id_parent;
        $newcategory->level_depth=$newcategory->calcLevelDepth();
        $newcategory->active=0;

        if (SCMS)
        {
            $shops = Shop::getShops(false,null,true);
            $newcategory->id_shop_list = $shops;
        }

        $languages = Language::getLanguages(true);
        foreach($languages AS $lang)
        {
            $newcategory->link_rewrite[$lang['id_lang']]=link_rewrite($name);
            $newcategory->name[$lang['id_lang']]=$name;
        }
        $newcategory->add();

        if (!in_array(1,$newcategory->getGroups()))
            $newcategory->addGroups(array(1));
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $shops=Category::getShopsByCategory((int)$id_parent);
            foreach($shops AS $shop)
            {
                $position = Category::getLastPosition((int)$id_parent, $shop['id_shop']);
                if (!$position)
                    $position = 1;
                $newcategory->addPosition($position, $shop['id_shop']);
            }
        }
        $this->id_category = $newcategory->id;

        $return = parent::add($autodate, $null_values);
        return $return;
    }

    public function delete()
    {
        $cat = new Category((int)$this->id_category);
        $cat->delete();

        if (!parent::delete())
            return false;
        else
            return true;
    }

    public function checkFinish()
    {
        $return = false;
        if(!empty($this->id_ff_project))
        {
            $FF_ID = SCI::getConfigurationValue("SC_FOULEFACTORY_ID");
            $FF_APIKEY =SCI::getConfigurationValue("SC_FOULEFACTORY_APIKEY");
            $api = new FFApi($FF_ID,$FF_APIKEY);
            $sub_url = "projects/".$this->id_ff_project."/taskLines";
            $FF_return = $api->queryGet($sub_url);
            if($FF_return['status_code']=='200')
            {
                $nb_tasklines = count($FF_return['response']);
                $nb_valid = 0;
                if(!empty($nb_tasklines) && $nb_tasklines>0)
                {
                    foreach($FF_return['response'] as $taskline)
                    {
                        if($this->type=="feature" && !empty($taskline->TaskLinesAnswers) && count($taskline->TaskLinesAnswers)==2)
                            $nb_valid++;
                        elseif(($this->type=="desc_short" || $this->type=="desc_long") && !empty($taskline->TaskLinesAnswers[0][1]))
                            $nb_valid++;
                    }

                    if($nb_valid==$nb_tasklines)
                        $return = true;
                }
            }
        }

        return $return;
    }

    static public function existProjects()
    {
        $return = false;

        $sql = "SELECT id_project FROM "._DB_PREFIX_."sc_ff_project";
        $res=Db::getInstance()->ExecuteS($sql);
        if(!empty($res) && count($res)>0)
            $return = true;

        return $return;
    }

    static public function getByIdCategory($id_category)
    {
        $return = null;
        if(!empty($id_category))
        {
            $sql = "SELECT id_project FROM "._DB_PREFIX_."sc_ff_project WHERE id_category='".(int)$id_category."'";
            $res=Db::getInstance()->ExecuteS($sql);
            if(!empty($res[0]["id_project"]))
                $return = new FfProject((int)$res[0]["id_project"]);
        }
        return $return;
    }
}