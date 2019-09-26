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

	function needOpenTip()
	{
		global $sc_agent;
		$openTips = true;

		if (file_exists('tips/content/astuce.xml')) {
			$xml = simplexml_load_file('tips/content/astuce.xml');
			$tab = array();
			$allTips = array();
			require_once('tips/classes/Ini.php');
			$Ini = new Ini($sc_agent->id_employee);
			$Ini->lire();

			$date = new DateTime();
			$today = $date->format("Y-m-d"); // date du jour au format 2015-04-24
			$last_date = ($Ini->content['date_display']); // date de la derniere lecture de l'astuce

			if(isset($Ini->content['mode'])){
				switch((int)$Ini->content['mode']){
					case 1: // A chaque ouverture
						// on ne fait rien, $openTips = true
						break;
					case 2: // Une fois par jour
						$newDate = new DateTime($last_date);
						$newDate->add(new DateInterval('P1D')); // date de la derniere lecture + 1 jour
						$lastDatePlus1Day = $newDate->format("Y-m-d");
						if($today < $lastDatePlus1Day) {
							$openTips = false; // si la date est supérieur à 24 h
						}
						break;
					case 3: // Une fois par semaine
						$newDate = new DateTime($last_date);
						$newDate->add(new DateInterval('P7D')); // date de la derniere lecture + 7 jour
						$lastDatePlus7Days = $newDate->format("Y-m-d");
						if($today < $lastDatePlus7Days){
							$openTips = false;  // si la date est comprise entre 1 et 7 jours
						}
						break;
					case 4: // Une fois par mois
						$newDate = new DateTime($last_date);
						$newDate->add(new DateInterval('P30D')); // date de la derniere lecture + 7 jour
						$lastDatePlus30Days = $newDate->format("Y-m-d");
						if($today < $lastDatePlus30Days){
							$openTips = false; // si la date est comprise entre 7 et 30 jours
						}
						break;
					case 5: // Jamais d'astuce
						$openTips = false; // on n'affiche plus rien
						break;
				}
			}
		}else{
			$openTips = false;
		}
		return $openTips;
	}

	if (needOpenTip())
	{
?>
<script type="text/javascript">
	$(document).ready(function(){
		onMenuClick('help_tips_display', 0, 0);
	});
</script>
<?php
	}
