<?php

 
if(!class_exists('sds_rev_hooks_class')) {

	class sds_rev_hooks_class {
	
		public function add_new_hook($new_font){
			
			if(!isset($new_font['hookname'])) return __('Wrong parameter received', REVSLIDER_TEXTDOMAIN);
			
			$fonts = unserialize(sdsconfig::getval('sds_rev_hooks'));
			
			if(!empty($fonts)){
				foreach($fonts as $font){
					if($font['hookname'] == $new_font['hookname']) return __('Hook Already exist, choose a different Hook', REVSLIDER_TEXTDOMAIN);
				}
			}

			$new = array('hookname' => $new_font['hookname']);

			$fonts[] = $new;
			$do = sdsconfig::setval('sds_rev_hooks', $fonts);
			//start register hook
				$mod_obj = Module::getInstanceByName('revsliderprestashop');
				$mod_obj->registerHook($new_font['hookname']);
			//End register hook
			if($do)
				return true;
		}

		public function edit_hook_by_hookname($edit_font){
			
			if(!isset($edit_font['hookname'])) return __('Wrong Hook received', REVSLIDER_TEXTDOMAIN);
			
			$fonts = $this->get_all_hooks();
			
			if(!empty($fonts)){
				foreach($fonts as $key => $font){
					if($font['hookname'] == $edit_font['hookname']){
						$fonts[$key]['hookname'] = $edit_font['hookname'];
						$do = sdsconfig::setval('sds_rev_hooks', $fonts);
						return true;
					}
				}
			}
			
			return false;
		}
	
		public function remove_hook_by_hookname($handle){
			
			$fonts = $this->get_all_hooks();
			
			if(!empty($fonts)){
				foreach($fonts as $key => $font){
					if($font['hookname'] == $handle){
						unset($fonts[$key]);
						//start unregister hook
							$mod_obj = Module::getInstanceByName('revsliderprestashop');
							$id_hook = Hook::getIdByName($handle);
							$mod_obj->unregisterHook($id_hook);
						//End unregister hook
						$do = sdsconfig::setval('sds_rev_hooks', $fonts);
						return true;
					}
				}
			}
			
			return __('Hook not found! Wrong Hook given.', REVSLIDER_TEXTDOMAIN);
		}
	
		public function get_all_hooks(){
			
			$fonts = unserialize(sdsconfig::getval('sds_rev_hooks'));
			return $fonts;
		}
		
		public function get_all_hooks_handle(){
			$fonts = array();
			$font = unserialize(sdsconfig::getval('sds_rev_hooks'));
			if(!empty($font)){
				foreach($font as $f){
					$fonts[] = $f['hookname'];
				}
			}
			return $fonts;
		}
		
		public static function propagate_default_hooks(){
			$default = array (
				); 
			$fonts = unserialize(sdsconfig::getval('sds_rev_hooks'));
			if(!empty($fonts)){
				foreach($default as $d_key => $d_font){
					$found = false;
					foreach($fonts as $font){
						if($font['hookname'] == $d_font['hookname']){
							$found = true;
							break;
						}
					}
					if($found == false)
						$fonts[] = $default[$d_key];
				}
				sdsconfig::setval('sds_rev_hooks', $fonts);
			}else{
				sdsconfig::setval('sds_rev_hooks', $default);
			}
		}
	}
}
?>