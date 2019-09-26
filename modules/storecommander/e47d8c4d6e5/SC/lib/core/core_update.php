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

	$submitUpdate = (int)Tools::getValue('submitUpdate');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<script type="text/javascript" src="<?php echo SC_JQUERY;?>"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$('input[name="cgu_agreed"]').click(function(){
				var buttondisabled = $('input#cgu_validator');
				if ($(this).is(':checked')) {
					buttondisabled.removeAttr('disabled');
				} else {
					buttondisabled.prop("disabled",true);
				}
			});

			$('form').on('submit', function() {
				$(this).find('input#cgu_validator').prop('disabled', true);
			});
		});
	</script>
</head>
<body>

<h2><?php echo _l('Store Commander update',1); ?></h2>

<?php if (empty($submitUpdate) || $submitUpdate == 0) { ?>
<div id="cgu">
	<h3><?php echo _l('You need to accept our Terms & Conditions to update Store Commander'); ?></h3>
	<form action="" method="post">
		<input type="hidden" name="submitUpdate" value="1"/>
		<input type="checkbox" name="cgu_agreed"/> <a href="<?php echo '../SC/terms-'.($user_lang_iso != 'fr' ? 'en' : $user_lang_iso).'.html'; ?>" target="_blank"><?php echo _l('I accept Terms & Conditions'); ?></a><br/><br/>
		<input id="cgu_validator" type="submit" value="<?php echo _l("Update"); ?>" disabled/>
	</form>
</div>
<?php } else { ?>

<div id="register">
	<?php
	if (SCI::getConfigurationValue('SC_LICENSE_KEY','')=='')
		die(_l('You have to register your license key in the [Help > Register your license] menu to update Store Commander.'));

	// check rights
	$notWritableFiles=array();
	$writePermissions=octdec('0'.substr(decoct(fileperms(realpath(SC_PS_PATH_DIR.'img/p'))),-3));
	$writePermissionsOCT=substr(decoct(fileperms(realpath(SC_PS_PATH_DIR.'img/p'))),-3);
	dirCheckWritable(SC_DIR,$notWritableFiles);
	if (count($notWritableFiles))
	{
		$dirStrSize=strlen(SC_PS_PATH_ADMIN_DIR);
		echo _l('Some files are not writable, please change the permission of these files:').' ('.$writePermissionsOCT.')'.'<br/><br/>';
		foreach($notWritableFiles AS $k => $file){
			echo substr($file,$dirStrSize).'<br/>';
			if ($k > 20)
			{
				echo '...';
				exit;
			}
		}
		exit;
	}

	$newPackages=checkSCVersion(true,true);

	if (count($newPackages)==0) die(_l('No update found.'));

	$checkSupport=sc_file_get_contents('http://www.storecommander.com/files/getsupport_'.SCI::getConfigurationValue('SC_LICENSE_KEY', '').'_checkonly.php');
	if ($checkSupport=='LICENSENOTFOUND')
		die(_l('Error: your license is not found on our server, please contact support.'));
	if ($checkSupport=='EXPIRED')
	{
		die(_l('The period entitling you to download Store Commander updates and benefit from support has expired.').'<br/><br/>'.
				_l('If you wish to benefit from future updates and new features, please log onto your account here:').'<br/>'.
				'<a href="http://www.storecommander.com/'.($user_lang_iso=='fr'?'':'en/').'my-licenses.php" target="_blank">http://www.storecommander.com/'.($user_lang_iso=='fr'?'':'en/').'my-licenses.php</a>'.'<br/><br/>'.
				_l('and click on Updates & support 6 or 12 months, or upgrade to a higher license plan.').'<br/><br/>'.
				'<a href="http://support.storecommander.com" target="_blank">'._l('Please contact us for any sales inquiries you may have.').'</a>'
				);
	}

	include(SC_DIR.'lib/php/pclzip.lib.php');

	$tmp_folder=SC_DIR.'sc_update_tmp';

	if (!is_dir($tmp_folder)) 
	{
		$old = umask(0);
		mkdir($tmp_folder,$writePermissions);
		umask($old);
	}
	$localVersions=json_decode(SCI::getConfigurationValue('SC_VERSIONS',0),true);
	if ($localVersions==NULL) $localVersions=array();


	echo _l('Updating...').'<br/><br/>';
	if (Tools::getValue('DEBUG',0)==1)
	{
		echo '<pre>';
		var_dump($newPackages);
		echo '</pre>';
	}
	foreach($newPackages AS $pack)
	{
		echo _l('Downloading pack').' '.$pack['filename'].'...<br/>';
		$pack['url']=str_replace('KEY',SCI::getConfigurationValue('SC_LICENSE_KEY', '').'_'.sc_phpversion().'_'._PS_VERSION_,$pack['url']);
		$data=sc_file_get_contents($pack['url']);
		if (Tools::getValue('DEBUG',0)==1)
			echo $pack['url'].'<br/>';
		echo ' ('.(strlen($data)/1000).'K)<br/>';
		file_put_contents($tmp_folder.'/'.$pack['filename'],$data);
		if (filesize($tmp_folder.'/'.$pack['filename']) == 0)
		{
			echo _l('Error with archive (filesize = 0 Ko)').'<br/>';
		}else{
			echo _l('Opening zip archive...').'<br/>';
			$archive = new PclZip($tmp_folder.'/'.$pack['filename']);
			echo _l('Extracting zip archive...').'<br/>';
			$old = umask(0);
			$archive->extract(PCLZIP_OPT_PATH,$tmp_folder.'/',PCLZIP_OPT_SET_CHMOD,$writePermissions);
			umask($old);
			$localVersions[$pack['shortname']]=$pack;
			echo _l('End of extraction').'<br/><br/>';
		}
	}
	echo _l('Copying files...').'<br/><br/>';
	dirMove($tmp_folder.'/SC',realpath(SC_DIR.'../'), true);

	if (!isset($_GET['updatekeepzipfile'])) // for debug purpose
		dirRemove($tmp_folder);

	SCI::updateConfigurationValue('SC_VERSIONS',json_encode($localVersions));
	$local_settings["APP_TRENDS"]['value']=1;
	saveSettings();

    $licence = SCI::getConfigurationValue('SC_LICENSE_KEY');
    if(empty($licence))
        $licence = "demo";
    getServicesStatus($licence);
    checkServicesStatus();

	echo _l('Update finished!').' '.'<a href="index.php" target="_top">'._l('Click here to refresh the application').'</a>';
	?>
</div>
<?php } ?>
</body>
</html>
