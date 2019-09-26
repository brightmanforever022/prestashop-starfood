<?php
include('../conf/definitions/de.inc.php');
echo'<html><head><link rel=stylesheet type="text/css" href="style.css"></head><body>';

echo "<h3>".$DMC_TEXT[$_GET['option']]."</h3>";	

echo "<p>".$DMC_TEXT[$_GET['option']."_DESC"]."</p>";	
echo '<br/><center><a href="JavaScript:window.close()">Close</a></center>';

echo "</body></html>";
?>