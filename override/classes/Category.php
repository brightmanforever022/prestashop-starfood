<?php
  // Wheelronix Ltd. development team
  // site: http://www.wheelronix.com
  // mail: info@wheelronix.com
  //


class Category extends CategoryCore
{
	public $color_code;
    public $filter_mark_color;
    
    public function getFields()
    {
    	$fields = parent::getFields();
    	$fields['color_code'] = pSQL($this->color_code);
    	$fields['filter_mark_color'] = pSQL($this->filter_mark_color);
    	return $fields;
    }
}