<?php
	/*
		Σ : 14/10/2015
	*/
	require_once($_ENV['charts_path'] . 'admin/settings1.php');
	$siteName = $schema['charts'];
	$schema['seo'] = '';
	if(isset($_GET["alias"]))
	{
		$alias = $_GET['alias'];
	}
	else
	{
		$alias = 'infoplace';
	}
	
	$countQuery = @mysql_query("SELECT COUNT(*) FROM  bizProfessional WHERE alias = '$alias' ", $db);
	$ifExists = mysql_fetch_array($countQuery);

	if($ifExists[0]>0)
	{
	}
	else // alias does not exists
	{
		header("Location:error");
		//$alias = 'photo-gamos';
	}
	
	$proQuery = @mysql_query("SELECT proName, alias, rank, description, styl 
							FROM bizProfessional 
							WHERE alias = '$alias' AND rank BETWEEN 3 AND 4 ", $db);
	$proInfo = mysql_fetch_array($proQuery);

	// OFFERS
	$offerQuery = @mysql_query("SELECT * FROM orgEvent WHERE proAlias = '$alias' ", $db);
	$offerInfo = mysql_fetch_array($offerQuery);

	// OFFICES
	$companyQuery = @mysql_query("SELECT bizCompany.id, proAlias, nameEL, street, building, tel1, tel2, tel3, fax, email1, email2
								FROM bizCompany, geoAreaNames
								WHERE proAlias = '$alias' AND bizCompany.area = geoAreaNames.id
								ORDER BY id ", $db);	
	// ACTIVITIES
	$activitiesQuery = @mysql_query("SELECT bizOccupation.nameEL
									FROM bizActivities, bizOccupation, bizCompany
									WHERE bizCompany.proAlias='" . $alias . "' AND bizCompany.id=bizActivities.company
									AND bizActivities.occupation=bizOccupation.id ", $db);
	$pageKeywordsPro = '';
	while($activitiesResults = mysql_fetch_row($activitiesQuery))
	{
		$pageKeywordsPro .= $activitiesResults[0] . ', ';
	}
	// PAGE DESCRIPTION
	$pageDescriptionPro = $proInfo[3];
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<?php 
		echo '<meta name="keywords" content="' . $pageKeywordsPro . '" />
		';
		echo '<meta name="description" content="' . $pageDescriptionPro . '" />
		';
		if(isset($proInfo[4]))
		{
			$styl = $proInfo[4];
		}
		else 
		{
			$styl = 0;
		}
		include_once('modules/modHeadPro.php');
		echo '<link href="' . $schema['charts'] . 'interface/css/company' . $styl . '.css" rel="stylesheet" type="text/css"/>';
	?>
</head>
<body>
<div id="wrapper">
<div id="workZone">

	<div class="subscribeZone">
	<div class="subscribeTop">

		<div class="textZone">
			<h1 class="inner"><?php echo ' - ' . $proInfo[0] . ' - '; ?><br /></h1>
			<h2 class="inner"><?php echo $alias; ?></h2>
			<p align="justify">
				<img class="aboutus-img" src="<?php echo $schema['charts']; ?>modules/blob.php?alias=<?php echo $alias; ?>" />
				<?php echo $proInfo[3]; ?>
				<br />
				ιστολόγιο: <a class="projects" title="<?php echo 'ιστολόγιο επαγγελματία'; ?>" href="<?php echo $schema['charts'] . $alias; ?>" /> TE.EU/<?php print $alias; ?></a>
			</p>
			<div class="clear"></div>

			<div class="aboutcolumnzone">
				<?php
				$item = 0;
				$col = 0;
				while($companyInfo=mysql_fetch_row($companyQuery))
				{
					$item = $item + 1;
					$col = 2 - ($item%2);
					?>
						<div class="aboutcolumn<?php echo $col; ?>">
							<h5> Κατάστημα - Έδρα ( <?php echo $item; ?> )</h5>
							<img src="interface/images/ico-med-1.png" alt="" class="abouticon" />
							Δνση: <b><a class="projects"><?php print $companyInfo[3] . ' ' . $companyInfo[4] . ', ' . $companyInfo[2]; ?></a></b>
							<br />
							τηλέφωνα: <b>
								<?php 
									print $companyInfo[5];
									if(isset($companyInfo[6]) && $companyInfo[6]!='') { print ' - ' . $companyInfo[6]; }
									if(isset($companyInfo[7]) && $companyInfo[7]!='') { print ' - ' . $companyInfo[7]; }
								?>
							</b>
							<br />
							fax: <b><?php print $companyInfo[8]; ?></b>
							<br />
							e-mail: <b><a class="projects">
								<?php 
									print $companyInfo[9];
									if(!empty($companyInfo[10])) { print  ' - ' . $companyInfo[10]; }
								?>
							</a></b>
							<br />
						</div>
					<?php
					if($col==2) { print '<div class="clear"></div>'; }
					}
				?>
					<div class="clear"></div>
					<div class="aboutcolumn1">
						<h5> Προσφορές </h5>
						<img src="interface/images/ico-med-2.png" alt="" class="abouticon" />
						<?php
							if($offerInfo[1]!='')
							{
								print $offerInfo[1];
							}
							else
							{
								print ' ΔΕΝ ΥΠΑΡΧΟΥΝ ΠΡΟΣΦΟΡΕΣ! ';
							}
						?>
						<br />
						από: <b><?php print $offerInfo[2]; ?></b>
						<br />
						έως: <b><?php print $offerInfo[3]; ?></b>
					</div>
					<div class="aboutcolumn2">
						<h5> Αρχεία </h5>
						<table align="center"><tr>
						<?php
						$files = '<td align="center"> Δεν υπάρχουν αρχεία! </a></td>';
						if($proInfo[2]>=3)
						{
							$exclude = array("customers/.", "..", "customers/..", "customers/ex");
							$dir = array();
							if(is_dir('customers/' . $alias))
							{
								$dir = scandir('customers/' . $alias);
								$files = '';
								for ($x=0; $x<count($dir); $x++)
								{
									if(($x-2)%4==0) { $files .= '<tr>'; }
									if (!is_dir($dir[$x]) && !in_array($dir[$x], $exclude))
									{
										$files .= '	<td align="center">
													<a href="' . $siteName . 'customers/' . $alias . '/' . $dir[$x] . '">' . 
													'<img width="40px" height="40px" src="customers/' . $alias . '/' . $dir[$x] . '" />'
													.'</a></td>';
									}
									if(($x-2)%4==3) { $files .= '</tr>'; }
								}
							}
							else
							{
								$files = 'Δεν υπάρχουν πολυμέσα!';
							}
						}
						else
						{
							$files = 'Δεν υπάρχει ο χρήστης!';
						}
						echo $files;
						?>
						</tr></table>
						<br />
						<br />
			</div>
			<div class="clear"></div>
			<h5> Χάρτης </h5>
			<?php 
				include_once('modules/partProMap.php');
			?>
		</div><!-- textZone -->
		<div class="clear"></div>

	</div><!-- subscribeTop -->
	<div class="clear"></div>
	</div> <!-- subscribeZone -->
	<div class="clear"></div>

</div> <!-- workZone -->
	<?php
		// include_once('modules/partBottomMenu.php');
	?>
</div> <!-- wrapper -->

</body>
</html>