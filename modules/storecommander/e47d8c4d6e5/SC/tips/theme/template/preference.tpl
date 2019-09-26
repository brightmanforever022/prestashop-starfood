<br/>
<br/>
<br/>
<br/>
<?php 
if($lang_iso == "fr"){ ?>
	<div id="conteneur">
		<div id="header">
			<h3> Pr&eacute;f&eacute;rences d'affichage </h3>
		</div>
		<div id="body">
			<form method="post" action="index.php?disp=fronttip" onsubmit="wTips.attachURL('tips/index.php?disp=preference&frequence='+$('#frequence').val(), true);return false;">
				<select id="frequence" name="frequence">
					<option value="1"<?php echo ((int)$Ini->content['mode']==1?' selected':'')?>>A chaque ouverture</option>
					<option value="2"<?php echo ((int)$Ini->content['mode']==2?' selected':'')?>>Une fois par jour</option>
					<option value="3"<?php echo ((int)$Ini->content['mode']==3?' selected':'')?>>Une fois par semaine</option>
					<option value="4"<?php echo ((int)$Ini->content['mode']==4?' selected':'')?>>Une fois par mois</option>
					<option value="5"<?php echo ((int)$Ini->content['mode']==5?' selected':'')?>>Jamais</option>
				</select>
				<input type="submit" value="valider">	
			</form>
		</div>
		<div id="footer">
			<div id="conclusion">
			</div>
			<div id="preference">
				<ul>
					<li> Nombre d'astuces lues : <?php echo $tipCount;?> <a href="javascript:wTips.attachURL('tips/index.php?disp=fronttip&reset_tip', true);void(0);">Remettre à zéro les astuces</a></li><br/><br/><br/>
					<li><a href="javascript:wTips.attachURL('tips/index.php?disp=fronttip', true);void(0);">Retour aux astuces</a></li><br/>
				</ul>
				<br/>
			</div>
		</div>
	</div>
<?php
}
else{ 
	?>
	<div id="conteneur">
		<div id="header">
			<h3> Display settings </h3>
		</div>
		<div id="body">
			<form method="post" action="index.php?disp=fronttip" onsubmit="wTips.attachURL('tips/index.php?disp=preference&frequence='+$('#frequence').val(), true);return false;">
				<select id="frequence" name="frequence">
					<option value="1"<?php echo ((int)$Ini->content['mode']==1?' selected':'')?>>Each opening</option>
					<option value="2"<?php echo ((int)$Ini->content['mode']==2?' selected':'')?>>Once a day</option>
					<option value="3"<?php echo ((int)$Ini->content['mode']==3?' selected':'')?>>Once a week</option>
					<option value="4"<?php echo ((int)$Ini->content['mode']==4?' selected':'')?>>Once a month</option>
					<option value="5"<?php echo ((int)$Ini->content['mode']==5?' selected':'')?>>Never</option>
				</select>
				<input type="submit" value="Save">	
			</form>
		</div>
		<div id="footer">
			<div id="conclusion">
			</div>
			<div id="preference">
				<ul>
					<li>Total played tips : <?php echo $tipCount;?> <a href="javascript:wTips.attachURL('tips/index.php?disp=fronttip&reset_tip', true);void(0);">Reset tips</a></li><br/><br/><br/>
					<li><a href="javascript:wTips.attachURL('tips/index.php?disp=fronttip', true);void(0);">Back to tips</a></li><br/>
				</ul>
				<br/>
			</div>
		</div>
	</div>
<?php
}
?>