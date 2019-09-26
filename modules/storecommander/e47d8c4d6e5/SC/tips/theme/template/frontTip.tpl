<?php
if($lang_iso == "fr"){ ?>
	<div id="conteneur">
		<div id="header">
			<div id="name">
				<h4> <?php echo $name_fr; ?></h4>
			</div>
		</div>
		<div id="intro">
			<hr>
			<p><?php echo $intro_fr; ?></p>
		</div>
		<div id="content">
			<div id="arrow_button_left">
				<a href="javascript:wTips.attachURL('tips/index.php?disp=fronttip', true);void(0);" title="Astuce precedente"><img src="tips/theme/images/button_arrow_left22a.png"/></a>
			</div>
			<div id="arrow_button_right">
				<a href="javascript:wTips.attachURL('tips/index.php?disp=fronttip', true);void(0);" title="Astuce suivante"><img src="tips/theme/images/button_arrow_right22a.png"/></a>
			</div>
			<div id="photo">
				<p><a href="<?php echo $linkphoto_fr; ?>" target="_blank"><?php echo $affiche; ?></a></p>
			</div>
			<div id="notice">
				<p><?php echo $notice; ?></p>
			</div>
		</div>
			<div id="conclusion">
				<p><?php echo $conclusion_fr; ?> </p>
			</div>
			<div id="preference">
				<br/>
				<select name="categories" id="categories" onClick="if(this.value!=0) wTips.attachURL('tips/index.php?id='+this.value, true);">
				   <option value="0">Trouver une astuce</option>
					<?php
						foreach ($arrCateg_fr as $k => $arrTip){
							echo '<optgroup label="'.$k.'">';
							foreach ($arrTip as $tip){
								if ($tip['name_fr']!='')
									echo '<option value="'.$tip['id'].'">'.$tip['id'].' - '.$tip['name_fr']."</option>\n";
							}
							echo "</optgroup>\n";
						}
					?>
				</select>
				<br/>
				<br/>
				<ul>
					<li><a href="javascript:wTips.attachURL('tips/index.php?disp=preference', true);void(0);" title="Préférences d'affichage"><img src="tips/theme/images/preference_icone.png"/> Préférences</a></li>
				</ul>
				<br/>
			</div>
		<div id="footer">
		</div>
	</div>
<?php
}
else{
	?>
	<div id="conteneur">
		<div id="header">
			<div id="name">
				<h4> <?php echo $name_en; ?></h4>
			</div>
		</div>
		<div id="intro">
			<hr>
			<p><?php echo $intro_en; ?></p>
		</div>
		<div id="content">
			<div id="arrow_button_left">
				<a href="javascript:wTips.attachURL('tips/index.php?disp=fronttip', true);void(0);" title="Previous tip"><img src="tips/theme/images/button_arrow_left22a.png"/></a>
			</div>
			<div id="arrow_button_right">
				<a href="javascript:wTips.attachURL('tips/index.php?disp=fronttip', true);void(0);" title="Next tip"><img src="tips/theme/images/button_arrow_right22a.png"/></a>
			</div>
			<div id="photo">
				<p><a href="<?php echo $linkphoto_en; ?>" target="_blank"><?php echo $affiche_en; ?></a></p>
			</div>
			<div id="notice">
				<p><?php echo $notice; ?></p>
			</div>
		</div>
			<div id="conclusion">
				<p><?php echo $conclusion_en; ?> </p>
			</div>
			<div id="preference">
				<br/>
				<select name="categories" id="categories" onClick="if(this.value!=0) wTips.attachURL('tips/index.php?id='+this.value, true);">
				   <option value="0">Find a tip</option>
					<?php
						foreach ($arrCateg_en as $k => $arrTip){
							echo '<optgroup label="'.$k.'">';
							foreach ($arrTip as $tip){
								if ($tip['name_en']!='')
									echo '<option value="'.$tip['id'].'">'.$tip['id'].' - '.$tip['name_en']."</option>\n";
							}
							echo "</optgroup>\n";
						}
						?>
				</select>
				<br/>
				<br/>
				<ul>
					<li><a href="javascript:wTips.attachURL('tips/index.php?disp=preference', true);void(0);" title="Display preferences"><img src="tips/theme/images/preference_icone.png"/> Settings</a></li>
				</ul>
			</div>
<!--		<div id="footer">
		</div>-->
	</div>
<?php
}

if ($hideNavigation)
{
?>
<script>
	$(document).ready(function(){
	$('#arrow_button_left img').hide();
	$('#arrow_button_right img').hide();
	});
</script>
<?php
}