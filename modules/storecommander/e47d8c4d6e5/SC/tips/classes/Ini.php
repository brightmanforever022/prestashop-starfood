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

if (!defined("PATH_INI_DEFAULT")) // call from SC application (core_tips.php)
{
	define("BASE_PATH", dirname(__FILE__)."/../");
	define("PATH_INI_DEFAULT", BASE_PATH."ini/");
	define("PATH_INI", BASE_PATH."../../SC_TOOLS/tips/");
}

class ini
{
	private $inistr; // chaine de caractère = contenu du fichier .ini à écrire
	public $filename; // nom du fichier
	public $content = array(); // tableau contenu du fichier ini lu

	public function __construct ($user_id, $commentaire = false) {
		$this->inistr = (!$commentaire) ? ' ' : ';'.$commentaire;
		$this->filename = PATH_INI."display_user_".$user_id.".ini";
		if (!file_exists($this->filename)){
			if (!is_dir(PATH_INI)) 
			{
				$old = umask(0);
				mkdir(PATH_INI);
				umask($old);
			}
			copy(PATH_INI_DEFAULT.'display_default.ini',$this->filename);
		}
	}

	public function ajouter_array ($array) {
		foreach ($array as $key => $val) {
			if (is_array($val)) {
				$this->sous_tableau($val, $key);
			}
			else if (is_string($key)) {
				$this->ajouter_valeur($key, $val);
			}
		}
	}
	public function lire (){
		if(!empty($this->filename)){
			$this->content = parse_ini_file($this->filename);
		}
	}

	public function reset_tip(){
		$this->content['tip'] = "";
		$this->ecrire(true);
		}

	private function sous_tableau ($tab, $groupe = false) {
		if ($groupe) {
			$this->inistr .= "\n".'['.$groupe.']';
		}
		foreach ($tab as $key => $val) {
			if (!$this->ajouter_valeur($key, $val)) return false;
		}
		$this->inistr .= "\n";
		return true;
	}

	private function ajouter_valeur ($key, $val) {
		if (is_array($val)) {
			echo '<strong>Erreur :</strong> Impossible d\'ajouter une valeur';
			return false;
		}
		else if (is_string($val) OR is_double($val) OR is_int($val)) {
			$this->inistr .= "\n".$key.' = "'.$val.'"';
		}
		else {
			echo '<strong>Erreur :</strong> Le type de donnée n\'est pas supporté';
			return false;
		}
		return true;
	}

	public function ecrire($rewrite = false) {
		$c = true;
		if (file_exists($this->filename)) {
			if ($rewrite) {
				@unlink($filename);
			}
			else if (!$rewrite) {
				echo '<strong>Erreur fatale :</strong> Le fichier ini existe déjà';
				$c = false;
				return false;
			}
		}
		if ($c) {
			$fichier = fopen($this->filename, 'w');
			if (!$fichier) {
				echo '<strong>Erreur fatale :</strong> Impossible d\'ouvrir le fichier';
				return false;
			}
			if (!fwrite($fichier, $this->inistr)) {
				echo '<strong>Erreur fatale :</strong> Impossible d\'écrire dans le fichier';
				return false;
			}
			fclose($fichier);
		}
	}
}
