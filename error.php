<?php
	/// GENERAL Erro-Page
	// σ
	header("HTTP/1.0 404 Not Found");
	header('Location:http://traditionalevents.eu/tour/not-found');
?>
<!DOCTYPE html>
<html>
<head>
	<META http-equiv="refresh" content="0;URL=http://traditionalevents.eu/news/not-found">
	<meta charset="utf-8" />
	<meta name="keywords" content="οδηγος αγορας, καταλογος επιχειρησεων, εμπορικα καταστηματα, Θεσσαλονικη, καταλογος εταιρειων, νεες επιχειρησεις, οδηγος αγορας, Βορεια Ελλαδα, ηλεκτρονικος καταλογος επιχειρησεων" />
	<meta name="description" content="Η σελίδα δε βρέθηκε" />
	<?php
		include_once('admin/settings1.php');
		include_once('modules/modHead2.php');
		include_once('interface/jQueryUI.php');
	?>
</head>
<body>
	<div id="wrapper">
	<div id="workZone">
	<div class="subscribeZone">
	<div class="subscribeTop">
	<?php
		include('modules/partLogo.php');
	?>
	<div class="textZone">
		<h4><em>H Σελίδα δε βρέθηκε...</em></h4>
	</div>
	</div>
	<div class="clear"></div>
	</div>
	<div class="clear"></div>
	</div>
	<?php 
		include_once('modules/partBottomMenu.php');
		include_once('modules/partSideMenu.php');	
	?>
	</div>
</body>
</html>