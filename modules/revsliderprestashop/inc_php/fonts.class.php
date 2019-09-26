<?php

 
if(!class_exists('ThemePunch_Fonts')) {
	 
	class ThemePunch_Fonts {
		
		public function add_new_font($new_font){
			
			if(!isset($new_font['url']) || strlen($new_font['url']) < 3) return __('Wrong parameter received', REVSLIDER_TEXTDOMAIN);
			if(!isset($new_font['handle']) || strlen($new_font['handle']) < 3) return __('Wrong handle received', REVSLIDER_TEXTDOMAIN);
			
			$fonts = unserialize(sdsconfig::getval('tp-google-fonts'));
			
			if(!empty($fonts)){
				foreach($fonts as $font){
					if($font['handle'] == $new_font['handle']) return __('Font with handle already exist, choose a different handle', REVSLIDER_TEXTDOMAIN);
				}
			}
			
			$new = array('url' => $new_font['url'], 'handle' => $new_font['handle']);
			
			$fonts[] = $new;
			
			$do = sdsconfig::setval('tp-google-fonts', $fonts);
			if($do)
				return true;
		}
		
	
		public function edit_font_by_handle($edit_font){
			
			if(!isset($edit_font['handle']) || strlen($edit_font['handle']) < 3) return __('Wrong Handle received', REVSLIDER_TEXTDOMAIN);
			if(!isset($edit_font['url']) || strlen($edit_font['url']) < 3) return __('Wrong Params received', REVSLIDER_TEXTDOMAIN);
			
			$fonts = $this->get_all_fonts();
			
			if(!empty($fonts)){
				foreach($fonts as $key => $font){
					if($font['handle'] == $edit_font['handle']){
						$fonts[$key]['handle'] = $edit_font['handle'];
						$fonts[$key]['url'] = $edit_font['url'];
						
						$do = sdsconfig::setval('tp-google-fonts', $fonts);

						return true;
					}
				}
			}
			
			return false;
		}
		
	
		public function remove_font_by_handle($handle){
			
			$fonts = $this->get_all_fonts();
			
			if(!empty($fonts)){
				foreach($fonts as $key => $font){
					if($font['handle'] == $handle){
						unset($fonts[$key]);
						$do = sdsconfig::setval('tp-google-fonts', $fonts);
						return true;
					}
				}
			}
			
			return __('Font not found! Wrong handle given.', REVSLIDER_TEXTDOMAIN);
		}
		
	
		public function get_all_fonts(){
		
			$fonts = unserialize(sdsconfig::getval('tp-google-fonts'));
			return $fonts;
		}

		public function get_all_fonts_handle(){
			$fonts = array();
			$font = unserialize(sdsconfig::getval('tp-google-fonts'));
			if(!empty($font)){
				foreach($font as $f){
					$fonts[] = $f['handle'];
				}
			}
			return $fonts;
		}
	
		public function register_fonts(){
			$fonts = $this->get_all_fonts();
			if(!empty($fonts)){
				$http = (is_ssl()) ? 'https' : 'http';
				foreach($fonts as $font){
					if($font !== ''){
				
					$font_url = $http.'://fonts.googleapis.com/css?family='.$font['url'];
					Context::getcontext()->controller->addCSS($font_url);
					}
				}
			}
		}
	
		public static function propagate_default_fonts(){
			$default = array (
					array('url' => 'Open+Sans:300,400,600,700,800', 'handle' => 'open-sans'),
					array('url' => 'Raleway:100,200,300,400,500,600,700,800,900', 'handle' => 'raleway'),
					array('url' => 'Droid+Serif:400,700', 'handle' => 'droid-serif' )
				); 
			$fonts = unserialize(sdsconfig::getval('tp-google-fonts'));
			if(!empty($fonts)){
				foreach($default as $d_key => $d_font){
					$found = false;
					foreach($fonts as $font){
						if($font['handle'] == $d_font['handle']){
							$found = true;
							break;
						}
					}
					if($found == false)
						$fonts[] = $default[$d_key];
				}
				sdsconfig::setval('tp-google-fonts', $fonts);
			}else{
				sdsconfig::setval('tp-google-fonts', $default);
			}
		}
	}
}
?>