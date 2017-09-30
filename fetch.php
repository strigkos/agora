<?php
	/* 
		Σ : 14/10/2015
		////////////////////
		/// THE MAP PAGE ///
		////////////////////
		require_once($_ENV['charts_path'] . 'admin/settings1.php');
	*/
	require_once(getenv('charts_path') . 'admin/settings1.php');
	include_once($schema['abs_dir'] . 'modules/modParameters.php');
	$siteName = $schema['charts'];
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="keywords" content="" />
  <meta name="description" content="Σύλλογοι, Εκδηλώσεις, Αξιοθέατα, καταστημάτα, επιχειρήσεις, χάρτης" />
  <link href="<?php echo $siteName; ?>interface/css/maps.css" rel="stylesheet" type="text/css" />
<?php
	/*
		<!-- GMapsLib - API key V3 -->
		<script src="http://maps.google.com/maps?file=api&v=3.6&key=<?php echo $schema['gAPI_key']; ?>&sensor=false&hl=el" type="text/javascript"></script>
		<script src="http://maps.googleapis.com/maps/api/js?key=<?php echo $schema['browser_key']; ?>&sensor=false" type="text/javascript"></script>
	*/
?>
	<!-- Google API Key -->
	<script src="http://maps.googleapis.com/maps/api/js?&sensor=true" type="text/javascript"></script>
	<?php
		include_once('interface/jQueryUI.php');
		include_once('modules/jsGeoAreaLoad.php');
		include_once('modules/modHead.php');
		$schema['seo'] = 'yes';
		if(file_exists('modules/jsTrackingCode.php')) {
			include_once('modules/jsTrackingCode.php');
		}
	?>

</head>
<body>
  <div id="wrapper">
    <div id="workZone">
      <?php
        include_once('modules/partWorkMap.php');
        include_once('modules/partSideMenu.php');
      ?>
    </div>
    <?php
		if(1) { include_once('modules/partSearch.php'); }
		/*
		include_once('modules/partBottomMenu.php');
		*/
    ?>
  </div>
</body>
</html>