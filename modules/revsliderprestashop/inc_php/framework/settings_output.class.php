<?php



	class UniteSettingsOutputRev{

		

		protected $arrSettings = array(); 

		protected $settings;

		protected $formID;


		public function init(UniteSettingsRev $settings){

			$this->settings = new UniteSettingsRev();

			$this->settings = $settings;

		}

		protected function drawOrderbox($setting){

						

			$items = $setting["items"];

			$arrItems = array();

					

			if(!empty($setting["value"]) && 

				getType($setting["value"]) == "array" &&

				count($setting["value"]) == count($items)){

				

				$savedItems = $setting["value"];

								

				foreach($savedItems as $value){

					$text = $value;

					if(isset($items[$value]))

						$text = $items[$value];

					$arrItems[] = array("value"=>$value,"text"=>$text);	

				}

			}		
			else{

				foreach($items as $value=>$text)

					$arrItems[] = array("value"=>$value,"text"=>$text);

			}

			

			

			?>

			<ul class="orderbox" id="<?php echo $setting["id"]?>">

			<?php 

				foreach($arrItems as $item){

					$itemKey = $item["value"];

					$itemText = $item["text"];

					

					$value = (getType($itemKey) == "string")?$itemKey:$itemText;

					?>

						<li>

							<div class="div_value"><?php echo $value?></div>

							<div class="div_text"><?php echo $itemText?></div>

						</li>

					<?php 

				} 

			?>

			</ul>

			<?php 

		}

		

		

		//-----------------------------------------------------------------------------------------------

		//draw advanced order box

		protected function drawOrderbox_advanced($setting){

			

			$items = $setting["items"];

			if(!is_array($items))

				$this->throwError("Orderbox error - the items option must be array (items)");

				

			//get arrItems modify items by saved value			

			

			if(!empty($setting["value"]) && 

				getType($setting["value"]) == "array" &&

				count($setting["value"]) == count($items)):

				

				$savedItems = $setting["value"];

				

				//make assoc array by id:

				$arrAssoc = array();

				foreach($items as $item)

					$arrAssoc[$item[0]] = $item[1];

				

				foreach($savedItems as $item){

					$value = $item["id"];

					$text = $value;

					if(isset($arrAssoc[$value]))

						$text = $arrAssoc[$value];

					$arrItems[] = array($value,$text,$item["enabled"]);

				}

			else: 

				$arrItems = $items;

			endif;

			

			?>	

			<ul class="orderbox_advanced" id="<?php echo $setting["id"]?>">

			<?php 

			foreach($arrItems as $arrItem){

				switch(getType($arrItem)){

					case "string":

						$value = $arrItem;

						$text = $arrItem;

						$enabled = true;

					break;

					case "array":

						$value = $arrItem[0];

						$text = (count($arrItem)>1)?$arrItem[1]:$arrItem[0];

						$enabled = (count($arrItem)>2)?$arrItem[2]:true;

					break;

					default:

						$this->throwError("Error in setting:".$setting.". unknown item type.");

					break;

				}

				

				$checkboxClass = $enabled ? "div_checkbox_on" : "div_checkbox_off";

				

					?>

						<li>

							<div class="div_value"><?php echo $value?></div>

							<div class="div_checkbox <?php echo $checkboxClass?>"></div>

							<div class="div_text"><?php echo $text?></div>

							<div class="div_handle"></div>

						</li>

					<?php 

			}

			

			?>

			</ul>

			<?php 			

		}
		public function drawHeaderIncludes(){

			

			$arrSections = $this->settings->getArrSections();

			$arrControls = $this->settings->getArrControls();

			

			$formID = $this->formID;

			

			$arrOnReady = array();

			$arrJs = array();

			

			

			$arrJs[] = "g_settingsObj['$formID'] = {}";

			

			
			if(!empty($arrControls)){

				$strControls = json_encode($arrControls);

				$arrJs[] = "g_settingsObj['$formID'].jsonControls = '".$strControls."'";

				$arrJs[] = "g_settingsObj['$formID'].controls = JSON.parse(g_settingsObj['$formID'].jsonControls);";

			}
			echo "<script type='text/javascript'>\n";

			foreach($arrJs as $line){

				echo $line."\n";

			}

				

			if(!empty($arrOnReady)):

				//put onready

				echo "$(document).ready(function(){\n";

				foreach($arrOnReady as $line){

					echo $line."\n";

				}				

				echo "});";

			endif;

			echo "\n</script>\n";

			

		}

		

		

		//-----------------------------------------------------------------------------------------------

		// draw after body additional settings accesories

		public function drawAfterBody(){

			$arrTypes = $this->settings->getArrTypes();

			foreach($arrTypes as $type){

				switch($type){

					case self::TYPE_COLOR:

						?>

							<div id='divPickerWrapper' style='position:absolute;display:none;'><div id='divColorPicker'></div></div>

						<?php

					break;

				}

			}

		}

		protected function prepareToDraw(){
			$this->settings->setSettingsStateByControls();
		}
	}
?>