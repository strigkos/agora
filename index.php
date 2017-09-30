<?php
	/*
		Σ : 14/10/2015
	*/
	include_once('admin/settings1.php');
	include_once('modules/modParameters.php');
	$siteName = $schema['charts'];
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="keywords" content="οδηγος αγορας, καταλογος επιχειρησεων, εμπορικα καταστηματα, Θεσσαλονικη, καταλογος εταιρειων, νεες επιχειρησεις, οδηγος αγορας, Βορεια Ελλαδα, ηλεκτρονικος καταλογος επιχειρησεων" />
	<meta name="description" content="Ηλεκτρονικός κατάλογος επιχειρήσεων, επαγγελματιών και καταστημάτων της Βορείου Ελλάδος. Χάρτης Αγοράς, όλες οι ελληνικές επιχειρήσεις σε ένα χάρτη για αναζήτηση ανά περιοχή ή ανά κλάδο." />
	<?php
		include_once('modules/modHead.php');
	?>
	<link href="<?php echo $schema['charts']; ?>interface/css/maplet.css" rel="stylesheet" type="text/css" />
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
			<?php
				include_once('content/contentFront.php');
			?>
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
	<?php 
		include_once('interface/jQueryUI.php');
	?>
</body>
</html>