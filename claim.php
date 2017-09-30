<?php

	/*
	/// A customer can claim his company 
	/// or a user can notice a proble for a company
	// Σ : 9/1/2016
	*/
	
	if(file_exists('admin/settings1.php')) {
		include_once('admin/settings1.php');
	}
	include_once('modules/modParameters.php');
	include_once('modules/modValidity.php');
	include_once('modules/reCAPTCHA/recaptchalib.php');

	/* Site key */
	$publickey = "6LcSueUSAAAAAIpMmJKshXsnVP5zzHmf59HQJxlQ";
	
	/* Secret key */
	$privatekey = "6LcSueUSAAAAAHkq6GMFvmcLriIWEBlHFLTb8254";
	
	$message = '';
	$error0 = '';
	$error = '';
	
	$thisName = '';
	$thisArea = '';
	$thisStreet = '';
	$thisBuilding = '';

	function companyData($companyID)
	{
		$findCompany = null;
		if(!empty($companyID))
		{
			$strFind = @mysql_query("SELECT bizProfessional.rank, bizCompany.rank, alias, 
											bizProfessional.proName, bizCompany.area, bizCompany.street, bizCompany.building
									FROM bizProfessional, bizCompany
									WHERE bizCompany.proAlias = bizProfessional.alias
									AND bizCompany.id=" . $companyID);
			$findCompany = mysql_fetch_array($strFind);
		}
		return $findCompany;
	}
	
	/* Με GET */
	if(isset($_GET["companyID"])) {
		$companyID = keepOnlyNumbers($_GET['companyID']);
		$companyRow = companyData($companyID);
		$alias = $companyRow[2];

		if(empty($companyRow[2])) // no such companyID
		{
			Header('Location:charts.php?view=map1');
		}
		elseif($companyRow[0]<=1) // typical
		{
			$message = $companyRow[3] . ' ' . $companyRow[5] . ' ' . $companyRow[6];
			if($companyRow[1]<0)
			{
				$message .= '<br /><br /> Η καταχώρηση αυτή έχει παρατηρηθεί για με την αιτιολογία: <i>' . echoReasons($companyRow[1]) . '</i>'; 
			}
		}
		elseif($companyRow[0]==2) // ALLREADY CONFIRMED
		{
			Header('Location:charts.php?view=map1');
		}
		elseif($companyRow[0]>=3) // CUSTOMER
		{
			Header('Location:professional.php?alias=' . $alias);
		}
	}

	/* Με POST */
	elseif(isset($_POST['companyID']) && isset($_POST['reason'])) {
		$companyID = keepOnlyNumbers($_POST['companyID']);
		$reason = -keepOnlyNumbers($_POST['reason']);
		$companyRow = companyData($companyID);
		$message = $companyRow[3] . ' ' . $companyRow[5] . ' ' . $companyRow[6];

		$resp = recaptcha_check_answer ($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
		if(!$resp->is_valid)
		{
			$error0 = 'Λάθος επαλήθευση! Ξαναπροσπαθήστε.';
			//die ( . "(reCAPTCHA said: " . $resp->error . ")");
			$recaptcha = 0;
		}
		else
		{
			$recaptcha = 1;
			$qClaim = mysql_query("UPDATE bizCompany SET rank=" . $reason . " WHERE id = '$companyID'", $db);
			mysql_query($qClaim);
			if($qClaim)
			{
				if($environment == 'prd') // on-line
				{
					$claimSuccess = true;
					$message = ' Η παρατήρησή σας θα αναφερθεί στο διαχειριστή. Ευχαριστούμε!';
					$subject = ' Παρατήρηση καταχώρησης - Paratirisi Kataxwrisis';
					$context = $message;
					$sender = 'From: Xartes Agoras Contact <' . $siteMail . '>';
					$encode =  'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n";
					$sentmail = mail($bizInbox, $subject, $context, $encode . $sender);
				}
				else
				{
					$claimSuccess = true;
					$message = ' Η παρατήρησή σας θα αναφερθεί στο διαχειριστή. Ευχαριστούμε!';
				}
			}
		}
	}
	else {
		$companyID = 1;
		$alias = 'example';
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="keywords" content="αξιολόγηση καταχώρησης, καταχώρηση επχιχείρησης, αναζήτηση επιχείρησης, αναζήτηση εταιρείας" />
	<meta name="description" content="Αυτή η επιχείρηση είναι δικιά μου. Αξιολόγηση καταχώρησης." />
	<?php
		include_once('interface/jQueryUI.php');
		include_once('modules/modHead.php');
	?>
</head>

<body>

	<?php
		include_once('modules/partSideMenu.php');
	?>

<div id="wrapper">

<div id="workZone">
<div class="subscribeZone">
<div class="subscribeTop">
	<?php
		include('modules/partLogo.php');
	?>
	<div class="textZone">
		<h1 class="inner"> Σχόλια - Παρατηρήσεις - Κριτική </h1>
			<h3><?php print $message; ?></h3>
		<div class="ourprojectrow">
		<?php 
		if(empty($claimSuccess))
		{
			
			?>
			<br /> 
			<b><a class="projects" href="quickInsert.php?alias=<?php echo $alias; ?>"> Αυτή η επιχείρηση είναι δικιά μου! </a><b>
			<br />
			<br />
			<br />
			<h6 class="inner"> Λόγος </h6>
			Επιλέξτε ένα από τους παρακάτω λόγους για παρατήρηση και πατήστε [Υποβολή]!
			<br />
			Εάν συντρέχουν δύο ή παραπάνω λόγοι επιλέξτε τον πιο σημαντικό ξεκινώντας από πάνω προς τα κάτω!
			<br /><br />
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
				<table width="96%">
					<tr>
						<td align="center">
							<?php						
								echo recaptcha_get_html($publickey, $error);
								echo $error0;
							?>
							<br />
							<sub>Συμπληρώστε οπωσδήποτε το παραμορφωμένο κείμενο</sub>
						</td>
						<td align="left" valign="top">						
							<input name="companyID" type="hidden" value="<?php echo $companyID; ?>" />
							<br />
							<strong>
							<?php
								for($i=-4; $i<=-1; $i++)
								{
									print '<input name="reason" type="radio" value="' . $i .'" /> ' . echoReasons($i) . '<br /><br />';
								}
							?>
							<br />
						</strong></td>
					</tr>
					<tr>
						<td align="center" colspan="2">
							<br />
							<input type="submit" name="submit" class="button" value="Υποβολή!" />
							<br />
						</td>
					</tr>
				</table>
				</form>
				<br />
				<?php 
		}
		else
		{
			?>
			<h6 class="inner">
			<?php 
				//print $message; 
			?>
			</h6>
			<a href="map">Επιστροφή στο χάρτη</a>
			<?php
		}
		?>

        </div>
		<?php 
			include_once('modules/partContact.php');
		?>
<div class="clear"></div>
</div>
</div>
<div class="clear"></div>
</div>
<div class="clear"></div>
</div>
	<?php
		include_once('modules/partBottomMenu.php');
	?>
</div>
</body>
</html>
