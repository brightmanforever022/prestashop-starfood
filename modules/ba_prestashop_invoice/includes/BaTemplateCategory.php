<?php

class BaTemplateCategory //extends ObjectModel
{
    /*
    var $definition = array(
        'table' => 'invoice_tpl_category',
        'primary' => 'id',
        'fields' => array(
            'secure_key' =>                array('type' => self::TYPE_STRING, 'validate' => 'isMd5', 'copy_post' => false),
            ));
    */
    
    /**
     * @return array with list of all categories ordered by id: ['id'=>'name']
     */
    static function getList()
    {
        $db = Db::getInstance();
        $sqlRes = $db->executeS('select id, name from '._DB_PREFIX_.'ba_invoice_tpl_category order by id', false);
        $result = [];
        
        while($row = $db->nextRow($sqlRes))
        {
            $result[$row['id']] = $row['name'];
        }
        
        return $result;
    }
    
    
    /**
     * 
     * @param type $templateId
     * @return returns array with ids of categories, there template is attached
     */
    static function getTemplateCategoryIds($templateId)
    {
        $db = Db::getInstance();
        $sqlRes = $db->executeS('select category_id from '._DB_PREFIX_.'ba_invoice_tpl_to_category where template_id='.$templateId, false);
        $result = [];
        while($row = $db->nextRow($sqlRes))
        {
            $result []= $row['category_id'];
        }
        
        return $result;
    }
    
    
    /**
     * @return array(categoryId=>, categoryName=> templates=>['id'=>, 'name'=>])
     */
    static function getTemplatesGroupedByCategory()
    {
        $db = Db::getInstance();
        $sqlRes = $db->executeS('select c.id as cat_id, c.name as cat_name, t.id as tpl_id, t.name as tpl_name from '._DB_PREFIX_.
                'ba_invoice_tpl_category c inner join '._DB_PREFIX_.'ba_invoice_tpl_to_category tc on c.id=tc.category_id inner join '.
                _DB_PREFIX_.'ba_prestashop_invoice t on t.id=tc.template_id order by c.name, t.name', false);
        $result = [];
        $curCatId = 0;
        $curCatIndex = -1;
        while($row = $db->nextRow($sqlRes))
        {
            if ($curCatId != $row['cat_id'])
            {
                $curCatId = $row['cat_id'];
                $curCatIndex++;
                $result[] = ['categoryId'=>$row['cat_id'], 'categoryName'=>$row['cat_name'], 'templates'=>[]];
            }
            
            $result[$curCatIndex]['templates'] []= ['id'=>$row['tpl_id'], 'name'=>$row['tpl_name']];
        }
        
        return $result;
    }
    
    
    /**
     * Saves template categories, ovewriting exiting
     * @param type $templateId
     * @param type $categoryIds
     */
    static function saveTemplateCats($templateId, $categoryIds)
    {
        $db = Db::getInstance();
        // delete old cats
        $db->execute('delete from '._DB_PREFIX_.'ba_invoice_tpl_to_category where template_id='.$templateId);
        
        // insert new
        $sqlTail = '';
        foreach($categoryIds as $categoryId)
        {
            $sqlTail .= (empty($sqlTail)?'':', ').'('.$templateId.','.$categoryId.')';
        }
        
        return $db->execute('insert into '._DB_PREFIX_.'ba_invoice_tpl_to_category(template_id, category_id) values '.$sqlTail);
    }
}

