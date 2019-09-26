<?php

class Dispatcher extends DispatcherCore
{
	protected function __construct()
	{
        $this->loadInvModulesRoutes();
        
		$this->use_routes = (bool)Configuration::get('PS_REWRITING_SETTINGS');

		// Select right front controller
		if (defined('_PS_ADMIN_DIR_'))
		{
			$this->front_controller = self::FC_ADMIN;
			$this->controller_not_found = 'adminnotfound';
			$this->default_controller = 'adminhome';
			$this->use_routes = false;
		}
		else if (Tools::getValue('fc') == 'module')
		{
			$this->front_controller = self::FC_MODULE;
			$this->controller_not_found = 'pagenotfound';
			$this->default_controller = 'default';
		}
		else
		{
			$this->front_controller = self::FC_FRONT;
			$this->controller_not_found = 'pagenotfound';
			$this->default_controller = 'index';
		}
		
		$this->setRequestUri();

		// Switch language if needed (only on front)
		if ($this->front_controller == self::FC_FRONT)
			Tools::switchLanguage();

		if (Language::isMultiLanguageActivated())
			$this->multilang_activated = true;

		$this->loadRoutes();
	}
    
    private function loadInvModulesRoutes()
    {
        $inv_modules = Db::getInstance()->ExecuteS("SELECT `module` FROM `"._DB_PREFIX_."inv_modules`"); // all modules by Invertus, intalled on your system
        if ($inv_modules) // if table exists and it is not empty
        {
            foreach($inv_modules as $module)
            {
                $module = $module['module']; // module name
                include_once(_PS_MODULE_DIR_ . "$module/{$module}.php"); // include module class
                if(class_exists($module)) // if module class is successfully included
                {
                    $moduleInstance = new $module; // call module class
                    $moduleInstance->addRoutes($this->default_routes);
                }
            }
        }
    }
}