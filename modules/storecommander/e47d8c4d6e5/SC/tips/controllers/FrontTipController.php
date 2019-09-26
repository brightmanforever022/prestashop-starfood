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

	if (file_exists(BASE_CONTENT.'astuce.xml')) {
		$xml = simplexml_load_file(BASE_CONTENT.'astuce.xml');
		$tab = array();
		$allTips = array();
		$Ini = new Ini($user_id);
		$Ini->lire();

		if(isset($_GET["reset_tip"])) { // si reset_tip on supprime les données de tip
			$Ini->reset_tip();
		}
		$date = new DateTime();
		$today = $date->format("Y-m-d"); // date du jour au format 2015-04-24
		$last_date = $Ini->content['date_display']; // date de la derniere lecture d'une astuce
		$alreadyRead = explode(',',$Ini->content['tip']);
		$arrCateg = array(); // déclaration du tableau

		foreach($xml->astuces->astuce as $astuce) {
			$id=(int) $astuce->id;
			$name_fr=(string) $astuce->name_fr;
			$intro_fr=(string) $astuce->intro_fr;
			$photo_fr=(string) $astuce->photo_fr;
			$linkphoto_fr=(string) $astuce->linkphoto_fr;
			$video_fr=(string) $astuce->video_fr;
			$conclusion_fr=(string) $astuce->conclusion_fr;
			$category_fr=(string) $astuce->category_fr;
			$name_en=(string) $astuce->name_en;
			$intro_en=(string) $astuce->intro_en;
			$photo_en=(string) $astuce->photo_en;
			$linkphoto_en=(string) $astuce->linkphoto_en;
			$video_en=(string) $astuce->video_en;
			$conclusion_en=(string) $astuce->conclusion_en;
			$category_en=(string) $astuce->category_en;
			if(!array_key_exists($category_fr,$arrCateg)){ // on verifie que $category_fr est bien une clé de $arrCateg
				$arrCateg[$category_fr] = array();
			}
			$arrCateg_fr[$category_fr][] = array('id'=>$id,'name_fr'=>$name_fr); // on rempli le tableau qui a pour clé $category_fr

			if(!array_key_exists($category_en,$arrCateg)){ // idem en anglais
				$arrCateg[$category_en] = array();
			}
			$arrCateg_en[$category_en][] = array('id'=>$id,'name_en'=>$name_en);

			$fields = (array("id" => $id,"name_fr"=> $name_fr,"intro_fr"=> $intro_fr,"linkphoto_fr"=> $linkphoto_fr,"video_fr"=> $video_fr,"conclusion_fr"=> $conclusion_fr,"category_fr"=> $category_fr,"name_en"=> $name_en,"intro_en"=> $intro_en,"linkphoto_en"=> $linkphoto_en,"video_en"=> $video_en,"conclusion_en"=> $conclusion_en, "category_en"=> $category_en,));
			$allTips[(int)$id] = $fields;

			if (!in_array((int)$id,$alreadyRead))
				$tab[(int)$id] = $fields;
		}

		// cacher la navigation s'il n'y a plus d'astuce à afficher
		$hideNavigation = 0;

		if	(!isset($_GET["id"])&&(count($tab)>0)){
			$_GET["id"]=array_rand($tab, 1); // prendre au hasard parmi les astuces
		}

		$affiche = '';
		$notice = '';
		if	(isset($_GET["id"]) && array_key_exists($_GET["id"], $allTips)){
			$id = $_GET["id"];
			$name_fr = $allTips[$id]["name_fr"];
			$intro_fr = $allTips[$id]["intro_fr"];
			$linkphoto_fr = $allTips[$id]["linkphoto_fr"];
			$video_fr = $allTips[$id]["video_fr"];
			$conclusion_fr = $allTips[$id]["conclusion_fr"];
			$category_fr = $allTips[$id]["category_fr"];
			$name_en = $allTips[$id]["name_en"];
			$intro_en = $allTips[$id]["intro_en"];
			$linkphoto_en = $allTips[$id]["linkphoto_en"];
			$video_en = $allTips[$id]["video_en"];
			$conclusion_en = $allTips[$id]["conclusion_en"];
			$category_en = $allTips[$id]["category_en"];
			if (isset($id)){
				$Ini->content['tip'] = trim($Ini->content['tip'] .','.$id , ',');
				$Ini->content['date_display'] = $today;
			}
			$Ini->ajouter_array($Ini->content);
			$Ini->ecrire(true);
			if($lang_iso == "fr")
			{
				$photo_path_fr = '';
				if(file_exists(BASE_CONTENT.$id.'-fr.jpg')){
					$photo_path_fr = PATH_CONTENT.$id.'-fr.jpg';
					$affiche = '<img src='."$photo_path_fr".'>';
					$notice = 'Retrouvez tous nos articles sur notre <strong><a href="http://support.storecommander.com/home" target="_blank">Plateforme Support</a></strong>';
				}
				if(!empty($video_fr)){
					$affiche = "<iframe width="."490"." height="."290"." src="."https://www.youtube.com/embed/".$video_fr." frameborder="."0"." allowfullscreen></iframe>";
					$notice = 'Retrouvez toutes nos vidéos sur notre <strong><a href="https://www.youtube.com/user/StoreCommanderVideos" target="_blank">Chaîne YouTube</a></strong>.';
				}
			}
			if($lang_iso != "fr")
			{
				$photo_path_en = '';
				if(file_exists(BASE_CONTENT.$id.'-en.jpg')) {
					$photo_path_en = PATH_CONTENT.$id.'-en.jpg';
					$affiche_en = '<img src='."$photo_path_en".'>';
					$notice = 'Find all our articles on our <strong><a href="http://support.storecommander.com/home" target="_blank">support platform</a></strong>.';
				}
				if(!empty($video_en)){
					$affiche_en = "<iframe width="."490"." height="."290"." src="."https://www.youtube.com/embed/".$video_en." frameborder="."0"." allowfullscreen></iframe>";
					$notice = 'Watch all our videos on our <strong><a href="http://www.youtube.com/user/StoreCommanderVideos" target="_blank">YouTube Channel</a></strong>';
				}
			}
		}
		if	(!isset($_GET["id"])&&(count($tab)==0)){
			$Ini->content['date_display'] = $today;
			$Ini->ajouter_array($Ini->content);
			$Ini->ecrire(true);
			$hideNavigation = 1;
			$name_fr = "Vous avez lu toutes les astuces disponibles.";
			$intro_fr = "De nouvelles astuces seront disponibles dans la prochaine mise &agrave; jour de Store Commander !<br/>Pour revoir toutes les astuces, <a href=\"javascript:wTips.attachURL('tips/index.php?disp=fronttip/&reset_tip', true);void(0);\">cliquez ici pour remettre &agrave; z&eacute;ro les astuces</a>";
			$video_fr = "";
			$affiche = '<img src="'.PATH_CONTENT.'notip_fr.jpg"/>';
			$conclusion_fr = "";
			$name_en = "You read all tips available.";
			$intro_en = "More tips will be added in the next update of Store Commander!<br/>To review the tips, <a href=\"javascript:wTips.attachURL('tips/index.php?disp=fronttip/&reset_tip', true);void(0);\">click here to reset tips</a>";
			$affiche_en = '<img src="'.PATH_CONTENT.'notip_en.jpg"/>';
			$video_en = "";
			$conclusion_en = "";
			$notice = '';
		}

	}else{
		echo 'Cannot open file astuce.xml.';
	}

	// Appel du template
	require_once (PATH_TEMPLATE."header.tpl");
	require_once (PATH_TEMPLATE."frontTip.tpl");
	require_once (PATH_TEMPLATE."footer.tpl");
