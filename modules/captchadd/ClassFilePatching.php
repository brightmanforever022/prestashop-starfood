<?php
/**
 * LBS File Patching Class - Original code from http://www.librasoft.fr/ By Yannick MOREL (support@librasoft.fr)
 *
 * You can use this class free of charge, just take time to thank me on the forum, and don't remove these lines.
 * Don't hesitate to improve this class and post enhancements on forum or to send it by mail.
 *
 *  @author    Librasoft <contact@librasoft.fr>
 *  @copyright 2008-2014 Librasoft
 *  @version   1.0
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class LbsFilePatching
{
	public $last_error;
	private $buffer;
	private $untouchbuffer;
	private $file_name;
	private $action;
	private $matchstring;
	private $targetstring;
	private $is_loaded;
	private $is_defined;
	private $cr;

	public function load($file_name)
	{
		/* Fonction qui permet de charger un fichier dans le buffer */
		$this->last_error = '';
		$result = Tools::file_get_contents($file_name);
		if ($result === false)
		{	// Impossible de lire le fichier
			$this->last_error = 'Impossible de lire le fichier source';
			$this->is_loaded = false;
			$this->cr = '';
			return false;
		}
		else
		{
			$this->buffer = $result;
			$this->untouchbuffer = $result;
			$this->file_name = $file_name;
			$this->is_loaded = true;
			$this->get_CrType();
			return true;
		}
	}

	private function get_CrType()
	{
		/* Fonction qui permet de déterminer le type de retour chariot. */
		if (strpos($this->buffer, chr(13).chr(10)) === false)
		{
			if (strpos($this->buffer, chr(13)) === false)
				$this->cr = chr(10);    // Mac
			else
				$this->cr = chr(13);    // linux
		}
		else
			$this->cr = chr(13).chr(10);    // windows
	}

	public function save($backup = false, $backup_filename = '')
	{
		/* Fonction qui permet de sauvegarder le buffer dans le fichier d'origine avec possibilité de backup */
		/*		Si $backup = true un backup sera créé */
		/*		Si $backup_filename est spécifié le backup sera créé dans ce fichier là, sinon un backup avec la date et l'heure dans le nom du fichier sera créé   */
		$this->last_error = '';
		if ($this->is_loaded)
		{
			if ($backup)
			{
				/* Un backup est demandé */
				if ($backup_filename == '')
					$backup_filename = str_replace($this->file_name, pathinfo($this->file_name, PATHINFO_FILENAME), pathinfo($this->file_name, PATHINFO_FILENAME).'_'.date('Ymd-Hi').'.'.pathinfo($this->file_name, PATHINFO_EXTENSION));
				/* On sauvegarde le fichier non modifié */
				$result = file_put_contents($backup_filename, $this->untouchbuffer);
				if ($result === false)
				{	// Impossible de créer le backup
					$this->last_error = 'Impossible de créer le backup demandé';
					return false;
				}
			}
			$result = file_put_contents($this->file_name, $this->buffer);
			if ($result === false)
			{	// Impossible de sauvegarder le fichier
				$this->last_error = 'Impossible de sauvegarder le fichier';
				return false;
			}
			else   // Sauvegarde réussie avec succès
				return true;
		}
		else
		{	// Il n'y a pas de buffer sur lequel travaillé
			$this->last_error = 'Il n\'y a pas de buffer sur lequel travaillé';
			return false;
		}
	}

	public function setaction($action, $matchstring, $targetstring)
	{
		/* Fonction qui permet de fixer l'objectif */
		/*		action = replace, addbefore, addafter   */
		/*		matchstring = la chaine a chercher pour se positionner dans le fichier */
		/*		targetstring = la chaine à insérer */
		/*		Si §§§ est trouvé dans les chaines, cela sera considéré comme un retour charriot */
		if ($action != 'replace' && $action != 'addbefore' && $action != 'addafter')
		{
			$this->last_error = 'La définition de l\'action a échouée';
			$this->clearaction();
			return false;
		}
		else
		{
			$matchstring = str_replace('§§§', $this->cr, $matchstring);		// On remplace le caractère §§§ par un retour charriot
			$targetstring = str_replace('§§§', $this->cr, $targetstring);
			$this->action = $action;
			$this->matchstring = $matchstring;
			$this->targetstring = $targetstring;
			$this->is_defined = true;
			return true;
		}
	}

	public function clearaction()
	{
		/* Fonction qui reset l'action à effectuer */
		$this->action = '';
		$this->matchstring = '';
		$this->targetstring = '';
		$this->is_defined = false;
	}

	public function install()
	{
		/* Fonction qui permet d'installer la modification */
		$this->last_error = '';
		if ($this->is_loaded && $this->is_defined)
		{
			if ($this->iscompatible())
			{	// Le fichier est elligible à la modification
				if ($this->isinstalled())   	// Le fichier est déjà modifié
					return true;
				else
				{
					switch ($this->action)
					{
						case 'replace':			// Dans le cas d'un replace
							$stringtoput = $this->targetstring;
							break;
						case 'addbefore':		// Dans le cas d'un ajout avant
							$stringtoput = $this->targetstring.$this->cr.$this->matchstring;
							break;
						case 'addafter':    	// Dans le cas d'un ajout après
							$stringtoput = $this->matchstring.$this->cr.$this->targetstring;
							break;
						default:    			// Action n'est pas défini, on renvoi faux par défaut
							return false;
					}
					/* On update le buffer */
					$this->buffer = str_replace($this->matchstring, $stringtoput, $this->buffer);
					return true;
				}
			}
			else
			{
				$this->last_error = 'Le fichier à patcher n\'est pas compatible avec la modification demandée';
				return false;
			}
		}
		else
		{	// la classe n'est pas totalement initiée
			$this->last_error = 'la classe n\'est pas totalement initiée';
			return false;
		}
	}

	public function iscompatible()
	{
		/* Fonction qui permet de savoir si la modification est possible */
		$this->last_error = '';
		if ($this->is_loaded && $this->is_defined)
		{
			if (strpos($this->buffer, $this->matchstring) === false)
			{
				// La chaine a matché n'existe pas dans le fichier,  on vérifie que la modification ne soit pas déjà installée
				if (strpos($this->buffer, $this->targetstring) === false)
				{
					// La modification n'a pas déjà été installée, on a donc un fichier incompatible
					$this->last_error = 'Le fichier n\'est pas compatible';
					return false;
				}
				else	// La modification est déjà installée
					return true;
			}
			else	// La modification n'est pas installée mais est possible
				return true;
		}
		else
		{	// la classe n'est pas totalement initiée
			$this->last_error = 'la classe n\'est pas totalement initiée';
			return false;
		}
	}

	public function isinstalled()
	{
		/* Fonction qui permet de savoir si la modification est installée ou pas */
		$this->last_error = '';
		if ($this->is_loaded && $this->is_defined)
		{
			switch ($this->action)
			{
				case 'replace':			// Dans le cas d'un replace
					$stringtosearch = $this->targetstring;
					break;
				case 'addbefore':		// Dans le cas d'un ajout avant
					$stringtosearch = $this->targetstring.$this->cr.$this->matchstring;
					break;
				case 'addafter':    	// Dans le cas d'un ajout après
					$stringtosearch = $this->matchstring.$this->cr.$this->targetstring;
					break;
				default:    			// Action n'est pas défini, on renvoi faux par défaut
					return false;
			}
			if (strpos($this->buffer, $stringtosearch) === false)    // La chaine de destination n'a pas été trouvée
				return false;
			else	// La chaine a été trouvée c'est donc bien installé
				return true;
		}
		else
		{	// la classe n'est pas totalement initiée
			$this->last_error = 'la classe n\'est pas totalement initiée';
			return false;
		}
	}

	public function uninstall()
	{
		/* Fonction qui permet de désinstaller la modification */
		$this->last_error = '';
		if ($this->is_loaded && $this->is_defined)
		{
			if ($this->isinstalled())
			{	// Le fichier est modifié, on le remet d'origine
				switch ($this->action)
				{
					case 'replace':			// 	Dans le cas d'un replace
						$stringtosearch = $this->targetstring;
						break;
					case 'addbefore':		// 	Dans le cas d'un ajout avant
						$stringtosearch = $this->targetstring.$this->cr.$this->matchstring;
						break;
					case 'addafter':    	// Dans le cas d'un ajout après
						$stringtosearch = $this->matchstring.$this->cr.$this->targetstring;
						break;
					default:				// Action n'est pas défini, on renvoi faux par défaut
						return false;
				}
				/* On update le buffer */
				$this->buffer = str_replace($stringtosearch, $this->matchstring, $this->buffer);
				return true;
			}
			else
				return true;
		}
		else
		{	// la classe n'est pas totalement initiée
			$this->last_error = 'la classe n\'est pas totalement initiée';
			return false;
		}
	}
}
