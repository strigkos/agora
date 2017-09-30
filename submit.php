<?php
	/// Just submit via email by visitor
	// Θα μεταφερθεί σε Contact Form του WordPress
	// σ
	include_once('admin/settings1.php');
	include_once('modules/modValidity.php');
	include_once('modules/modParameters.php');
	
	$feedStyle['warn'] = 'ui-state-highlight ui-corner-all'; // warning
	$feedStyle['error'] = 'ui-state-error ui-corner-all'; // error
	$feedBackStyle = '';
	
	$msg0 = 'Τα πεδία με αστερίσκο είναι υποχρεωτικά!';
	$msg1 = ''; //'Εάν επιλέξετε δικό σας ψευδώνυμο, ελέγξτε τη διαθεσιμότητά του!';
	$subject = '';
	$context = '';
	$availMsg = '';
	$successLevel = 0;
	// temporarily without login or recaptcha
	$enable = 1;
	$disable = '';

	// DECLARE VARIABLES and SET VALUES
	// user
	$newsList = (isset($_POST['newsList']) && $_POST['newsList']=='on') ? (1) : (0);
	// professional
	$proID = -1;
	$companyID = -1;
	$checking = 'biznes' . rand();
	$alias = '';
	$proName = '';
	$lastName = '';
	$firstName = '';
	$description = '';
	$phone = '';
	$email = '';
	$url = '';
	$rank = 2;

	$offerComments = ''; 
	$start = ''; 
	$end = ''; 
	$lati = ''; 
	$longi = ''; 
	$zip =''; 
	$label = ''; 

	$street = ''; 
	$building = ''; 
	$tel1 = ''; 
	$tel2 = ''; 
	$telefax = ''; 
	$tel3 = ''; 
	$email1 = ''; 
	$email2 = ''; 

	$occupations[0] = 0; 
	$occupations[1] = 0; 
	$occupations[2] = 0;
	$extrActivity = '';

	//  bizProfessional
	if(isset($_POST['checking'])) { $checking = stripper($_POST['checking']); }
	if(isset($_POST['alias'])) { $alias = stripper($_POST['alias']); }
	if(isset($_POST['proName'])) { $proName = stripper($_POST['proName']); }
	if(isset($_POST['lastName'])) { $lastName = stripper($_POST['lastName']); }
	if(isset($_POST['firstName'])) { $firstName = stripper($_POST['firstName']); }
	if(isset($_POST['phone'])) { $phone = keepOnlyNumbers($_POST['phone']); }
	if(isset($_POST['email'])) { $email = isValidEMail(stripper($_POST['email'])); }
	if(isset($_POST['description'])) { $description = stripper($_POST['description']); }
	if(isset($_POST['url'])) { $url = stripper($_POST['url']); }

	// bizOffer
	if(isset($_POST['offerComments'])) { $offerComments = stripper($_POST['offerComments']); }
	if(isset($_POST['start'])) { $start = stripper($_POST['start']); }
	if(isset($_POST['end'])) { $end = stripper($_POST['end']); }

	// bizCompany
	if(isset($_POST['companyID'])) { $companyID = keepOnlyNumbers($_POST['companyID']); }
	if(isset($_POST['lati'])) { $lati = stripper($_POST['lati']); }
	if(isset($_POST['longi'])) { $longi = stripper($_POST['longi']); }
	if(isset($_POST['zip'])) { $zip = stripper($_POST['zip']); }
	if(isset($_POST['label'])) { $label = stripper($_POST['label']); }
	if(!empty($_POST['block']))
	{
		$areaID = stripper($_POST['block']);
	}
	elseif(!empty($_POST['city']))
	{
		$areaID = stripper($_POST['city']);
	}
	elseif(!empty($_POST['district']))
	{
		$areaID = stripper($_POST['district']);
	}
	elseif(!empty($_POST['region']))
	{
		$areaID = stripper($_POST['region']);
	}
	else
	{
		$areaID = 0;
	}

	if(isset($_POST['street'])) { $street = stripper($_POST['street']); }
	if(isset($_POST['building'])) { $building = stripper($_POST['building']); }
	if(isset($_POST['tel1'])) { $tel1 = keepOnlyNumbers($_POST['tel1']); }
	if(isset($_POST['tel2'])) { $tel2 = keepOnlyNumbers($_POST['tel2']); }
	if(isset($_POST['tel3'])) { $tel3 = keepOnlyNumbers($_POST['tel3']); }
	if(isset($_POST['telefax'])) { $telefax = keepOnlyNumbers($_POST['telefax']); }
	if(isset($_POST['email1'])) { $email1 = stripper($_POST['email1']); }
	if(isset($_POST['email2'])) { $email2 = stripper($_POST['email2']); }
	
	// Activity
	if(isset($_POST['occupation1'])) { $occupations[0] = stripper($_POST['occupation1']); }
	if(isset($_POST['occupation2'])) { $occupations[1] = stripper($_POST['occupation2']); }
	if(isset($_POST['occupation3'])) { $occupations[2] = stripper($_POST['occupation3']); }
	if(isset($_POST['extrActivity'])) { $extrActivity = stripper($_POST['extrActivity']); }

	// FIND companyID
	$proSelect = " SELECT id, alias FROM bizProfessional  WHERE alias='" . $checking . "'";
	$findPro = @mysql_query($proSelect, $db);
	$proDetails = mysql_fetch_array($findPro);

	if($proDetails[0]>0)
	{
		$enable = 0;
		$disable = 'disabled="disabled"';
		$availImg = 'images/notavail.png';
		$availMsg = 'δεν είναι διαθέσιμο'; 
		$alias='';
	}
	else // alias not exists
	{
		$enable=1;
		$disable='';
		$availImg = 'images/available.png';
		$availMsg = 'είναι διαθέσιμο';
		$alias=$checking;
	}

	// TRY INSERT OR UPDATE
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["submit"]) && $enable==1 && $disable=='')
	{
		$context .= "\r\n";
		$context .= "\r\n";
		/*
		if(empty($alias))
		{
			$enable = -1;		
			$msg0 = ' Εισάγετε όλα τα υποχρωτικά στοιχεία του επαγγελματία. ';
			$msg1 = ' Δώστε ψευδωνύμο! ';
			$feedBackStyle = $feedStyle['error'];
		}
		else
		*/
		if(empty($proName))
		{
			$enable = 1;		
			$msg0 = ' Εισάγετε όλα τα υποχρωτικά στοιχεία του επαγγελματία. ';
			$msg1 = ' Δώστε όνομα επιχείρησης! ';
			$feedBackStyle = $feedStyle['error'];
		}
		elseif(empty($phone) || strlen($phone)<10 )
		{
			$enable = 1;
			$msg0 = ' Εισάγετε όλα τα υποχρωτικά στοιχεία του επαγγελματία. ';
			$msg1 = ' Εισάγεται έναν έγκυρο τηλεφωνικό αριθμό! ';
		}
		elseif(empty($email))
		{
			$enable = 1;
			$msg0 = ' Εισάγετε όλα τα υποχρωτικά στοιχεία του επαγγελματία. ';
			$msg1 = ' Εισάγεται έναν έγκυρο email! ';
		}
		else
		{
			$context .= ' ΕΙΣΑΓΩΓΗ ΕΠΠΑΓΓΕΛΜΑΤΙΑ: ';
			$context .= $checking . ', ' . $proName . ', ' . $lastName . ', ' . $firstName . ', ' . $phone . ', ' . $email . ', ' 
					 . $url . ', ' . $description;
			$msg0 = ' Εισαγωγή νέου επαγγελματία! ';
			$successLevel = 1;

			// OFFER
			if($offerComments != '' && $start != '' && $end != '')
			{
				$context .= "\r\n" ;
				$context .= "\r\n" ;
				$context .= ' ΠΡΟΣΦΟΡΑ: ';
				$context .=  $offerComments . ', ' . $start . ' - ' . $end;
				//$successLevel = 2;
			}
			else
			{
				$msg0 .= " Δε δόθηκε προσφορά! ";
				$feedBackStyle = $feedStyle['warn'];
				//$successLevel = 2;
			}
			
			// Company
			if(!empty($street))
			{
				$context .= "\r\n";
				$context .= "\r\n";
				$context .= ' ΕΔΡΑ: ';
				$context .= $checking . ', ' . $lati . ', ' . $longi . ', ' . $zip . ', ' . $label . ', '. $areaID . ', ' 
						 . $street . ', ' . $building . ', ' . $tel1 . ', ' . $tel2 . ', ' . $tel3 . ', ' . $telefax . ', ' 
						 . $email1 . ', ' . $email2;
				$context .= "\r\n";
				$context .= "\r\n";
				$successLevel = 3;
			}
			else
			{
				$msg0 .= ' Δεν δόθηκε έδρα! ';
				$successLevel = -3;
			}
			
			// Activities
			$context .= ' ΔΡΑΣΤΗΡΙΟΤΗΤΕΣ: ';
			$ocupationGiven = 0;
			for($i=0; $i<=2; $i++)
			{
				if(isset($occupations[$i]) && $occupations[$i]>0)
				{
					$strOcuName = @mysql_query(" SELECT nameEL FROM  bizOccupation WHERE id =" .$occupations[$i] , $db);
					$qOcuName = mysql_fetch_array($strOcuName);
					$context .= ' -' . $qOcuName[0];
					if($qOcuName)
					{
						$ocupationGiven = $ocupationGiven + 1;
						//$successLevel = $successLevel + 1;
						$msg1 .= ' (' . ($i+1) .  ') Δόθηκε ';
					}
					else
					{
						$msg1 .= ' Σφάλμα στα στοιχεία της κατηγορίας! ';
					}
				}
				else
				{
					$msg1 .= ' (' . ($i+1) .  ') ΔΕΝ Δόθηκε ';
				}				
			}
			
			if(!empty($extrActivity))
			{
				$ocupationGiven = $ocupationGiven + 1;
				$context .= "\r\n";
				$context .= "\r\n";
				$context .= ' επιπλέον δραστηριότητα: ' . $extrActivity;
			}
			
			if($ocupationGiven > 0)
			{
				//$successLevel
			}
			else
			{
				$successLevel = -4;
			}
			
			// SEND MAIL
			if ($successLevel >= 3)
			{
				if($siteName == 'prd')
				{
					$subject = 'Αίτηση για καταχώρηση - Aitisi gia Kataxwrisi';
					$sender = 'From: Xartes Agoras Contact <' . $siteMail . '>';
					$encode =  'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n";
					$sentmail = mail($bizInbox, $subject, $context, $encode . $sender);
				}
				else
				{
					$msg1 = 'Στάλθηκε mail';
				}
				$insertUser = " INSERT INTO  bizUser (email, fullName, telephone, log, curPass, newPass, confirmation, newsList) 
											VALUES ('" . $email . "', '" . $lastName . "', '" . $phone . "', '1', '0', '0', '0', '" . $newsList . "')";
				$insertOK = mysql_query($insertUser, $db);
				if (!$insertOK) 
				{
					//die('Invalid query: ' . mysql_error());
				}
			}
			else
			{
				$msg1 = 'ΔΕΝ ΣΤΑΛΘΗΚΕ Η ΚΑΤΑΧΩΡΗΣΗ!';
				if($successLevel==-4)
				{
					$msg1 .= ' Δε δόθηκε κατηγορία επαγγέλματος! ';
				}
				$feedBackStyle = $feedStyle['error'];
			}
		}
	}
	elseif(isset($_POST["reset"]))
	{
		header('Location: ' . $_SERVER['PHP_SELF']);
	}
	
	// JUST SHOW ALL DATA
	if($checking!='' && $successLevel > 0)
	{
		// Professional
		$getPro = @mysql_query(" SELECT * FROM  bizProfessional WHERE alias = '$checking' ", $db);
		$proDetails = mysql_fetch_array($getPro);
		$proID = $proDetails[0];
		$proName = $proDetails[1];
		$rank = $proDetails[3];
		$description = $proDetails[4];
		$styl = $proDetails[6];
		$lastName = $proDetails[7];
		$firstName = $proDetails[8];
		$phone = $proDetails[9];
		$email = $proDetails[10];
		$url = $proDetails[11];

		// Offer
		if($successLevel>=2)
		{
			$getOffer = @mysql_query(" SELECT description, start, end FROM bizOffer WHERE proAlias = '$checking' ", $db);
			$offerDetails = mysql_fetch_array($getOffer);
			$offerComments = $offerDetails[0];
			$start = $offerDetails[1];
			$end = $offerDetails[2];
		}
		// Company
		if($successLevel>=3 || $companyID>0)
		{
			$getCompany =  @mysql_query(" SELECT * FROM bizCompany WHERE id = '$companyID' ", $db);
			$companyDetails = mysql_fetch_array($getCompany);
			$companyID = $companyDetails[0];
			$lati = $companyDetails[2];
			$longi = $companyDetails[3];
			$zip = $companyDetails[4];
			$label = $companyDetails[5];
			$discription = $companyDetails[6];
			$areaID = $companyDetails[7];
			if($areaID>0)
			{
				$getArea =  @mysql_query(" SELECT id, cityID, districtID, regionID FROM geoAreaNames WHERE id = '$areaID' ", $db);
				$areaDetails = mysql_fetch_array($getArea);
				$block = $areaDetails[0];
				$city = $areaDetails[1];
				$district = $areaDetails[2];
				$region = $areaDetails[3];
			}
			$street = $companyDetails[8];
			$building = $companyDetails[9];
			$tel1 = $companyDetails[10];
			$tel2 = $companyDetails[11];
			$tel3 = $companyDetails[12];
			$telefax = $companyDetails[13];
			$email1 = $companyDetails[14];
			$email2 = $companyDetails[15];
		}
		// Activities
		if($successLevel>=4 && $companyID>0)
		{
			$getActivities = @mysql_query(" SELECT occupation 
											FROM bizActivities 
											WHERE company = '$companyID' ORDER BY occupation ", $db);
			$r = 0;
			while($occupationRecord = mysql_fetch_row($getActivities))
			{
				$occupations[$r] = $occupationRecord[0];
				$r++;
			}
		}
	}
	
	// Activities drop down menus
	$occupationsQuery = "SELECT id, nameEL FROM bizOccupation ORDER BY priority DESC, nameEL ";
	$occupationInList[0] = '';
	$occupationInList[1] = '';
	$occupationInList[2] = '';
	$i=0;
	for($i=0; $i<=2; $i++)
	{
		$occupationsList = @mysql_query($occupationsQuery, $db);
		$occupationInList[$i] .= '<option selected="selected"> - Χωρίς επιλογή - </option>';
		while($occupationRecord = mysql_fetch_row($occupationsList))
		{
			$occupationInList[$i] .= '<option value="' . $occupationRecord[0] . '"';
			if(isset($occupations[$i]) && $occupationRecord[0] == $occupations[$i]) 
			{
				$occupationInList[$i] .= 'selected="selected"'; 
			}
			$occupationInList[$i] .= '>' . $occupationRecord[1];
			$occupationInList[$i] .= '</option>';
		}
	}
	$r = 2;
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="keywords" content="γρηγορη καταχωρηση επιχειρησης, καταχώρηση επιχείρησης, δωρεάν καταχώρηση επιχείρησης, δωρεαν καταχωρηση" />
	<meta name="description" content="Καταχωρήστε άμεσα την επιχείρησή σας!" />
	<?php
		include_once('modules/jsGMapsAPI.php');
		include_once('interface/jQueryUI.php');
	?>
	<script type="text/javascript" src="<?php echo $siteName; ?>interface/js/jquery.ui.datepicker.js"></script>
	<script>
		//$.noConflict();
		jQuery(document).ready(function($)
		//$(function() 
		{
			$( "#fromDate" ).datepicker();
			$( "#toDate" ).datepicker();
			$( "#fromDate" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
			$( "#toDate" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
		});
	</script>
	<?php
		include('modules/modHead.php');
		if(file_exists('social/GoogleAnalytics.js'))
		{
			include_once('social/GoogleAnalytics.js');
		}
	?>
</head>

<body onload="showMarker();">
<?php
	include('modules/partSideMenu.php');
?>

<div id="wrapper">
<div id="workZone">

<div class="subscribeZone">
<div class="subscribeTop">
	<?php
			include('modules/partLogo.php');
	?>
	<div class="textZone">	
		<h1 class="inner">Καταχώρηση επαγγελματία (χωρίς είσοδο χρήστη)</h1>
		<?php 
		if($successLevel>0)
		{
		?>
			<table class="inputs">
			<tr>
			<td width="60%"><b> Η καταχώρηση σας στάλθηκε στο διαχειριστή και θα καταχωρηθεί σύντομα!</b></td>
			<td width="40%" style="border:#0000FF thin solid; background:#FFFFCC"><font color="#0000FF">
				Ευχαριστούμε!
			</font>
			</td>
			</tr>
			<tr>
			<td>
			<?php
				$order = array("\r\n", "\n", "\r");
				$replace = '<br />';
				print str_replace($order, $replace, $context); 
			?>
			</td>
			</tr>
			</table>
			<br />
			<br />
			<hr />
			<br />
			<div>
				<p>
					<h5 class="inner"><b>
						Σύντομα θα δείτε την καταχώρησή σας στο χάρτη!
					</b></h5>
					<a href="map"> Χάρτης </a>
				</p>
			</div>
			<br />
			<br />
		<?php
		}
		else
		{
		?>
		<h2 class="inner">Καταχώρηση επαγγελματία και μιας έδρας του! | <a href="purchase"> Πλήρωμές!</h2></a>
		<table class="inputs">
			<tr class="last">
			<td class="<?php echo $feedBackStyle; ?>">
			<div style="margin-top: 20px; padding: 0 .7em;"> 
			<?php
				print $msg0;
				print '<br />';
				print $msg1;
			?>
			</div>
			</td>
			</tr>
		</table/>
			<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
			<table class="inputs">
			<tr class="newArea"><td colspan="4"><div class="ourprojectrow"></div></td></tr>
			<tr><td colspan="4">
				<h2 class="inner">Στοιχεία επαγγελματία</h2>
			</td></tr>
			<tr>
				<td><b> Επωνυμία<br />επιχείρησης * </b></td>
				<td><input tabindex="3" name="proName" type="text" size="40" value="<?php print $proName; ?>" <?php echo $disable; ?> /></td>
				<td><b> τηλέφωνo * </b></td>
				<td><input tabindex="4" name="phone" value="<?php print $phone; ?>" <?php echo $disable; ?> type="text" size="14" maxlength="14" title="έως 14 ψηφία!" /></td>
			</tr>
			<tr>
				<td><b> Επώνυμο </b></td>
				<td><input tabindex="5" name="lastName" type="text" size="40" value="<?php print $lastName; ?>" <?php echo $disable; ?> /></td>
				<td ><b> Όνομα </b></td>
				<td><input tabindex="6" name="firstName" type="text" size="40" value="<?php print $firstName; ?>" <?php echo $disable; ?> /></td>
			</tr>
			<tr class="last">
				<td><b> Τύπος καταχώρησης </b></td>
				<td><?php include('modules/inRankSub.php'); ?></td>
				<td><b> email **</b></td>
				<td><input tabindex="7" name="email" type="text" size="40" value="<?php print $email; ?>" <?php echo $disable; ?> />
				<sup> Υποχρωτικό αν θέλετε καταχώρηση με ιστολόγιο! </sup>
				</td>
			</tr>
			<tr class="last">
				<td><b></b></td>
				<td></td>
				<td><b> Λίστα ενημερώσεων </b></td>
				<td><input tabindex="8" name="newsList" type="checkbox" value="on" <?php echo $disable; ?> />
				</td>
			</tr>
			<tr class="newArea"><td colspan="4"><div class="ourprojectrow"></div></td></tr>
			<tr><td colspan="4">
				<h2 class="inner"> Επιπλέον πληροφορίες </h2>
				Αν επιλέξετε τύπο καταχώρησης: <i><?php print rankDescription(4) ?>!</i> μπορείτε να καταχωρήσετε και τις παρακάτω πληροφορίες για την επιχείρησή σας!
			</td></tr>
			<tr>
				<td><b> Ιστοσελίδα </b></td>
				<td><input tabindex="8" placeholder="www.yoursite.gr" name="url" type="text" size="40" value="<?php print $url; ?>" <?php echo $disable; ?> /></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td><b> Περιγραφή </b></td>
				<td colspan="3">
				<textarea tabindex="9"  placeholder="Δώστε μία περιγραφή της δραστηριότητάς σας"  name="description" size="500" cols="60" rows="10" <?php echo $disable; ?> ><?php print trim($description); ?></textarea></td>
			</tr>
			<tr>
				<td><b> Προσφορές </b></td>
				<td colspan="2">
					<textarea tabindex="12" placeholder="Δώστε μία προσφορά"  name="offerComments" size="500" cols="60" rows="5" <?php echo $disable; ?> ><?php print trim($offerComments); ?></textarea>
				</td>
				<td>  
					από 
					<br />
					<input tabindex="10" placeholder="2012-02-01" type="date" id="fromDate" name="start" size="19" maxlength="19" value="<?php print $start; ?>" <?php echo $disable; ?> /><br />
					έως 
					<br />
					<input tabindex="11" placeholder="2012-12-31" type="date" id="toDate" name="end" size="19" maxlength="19" value="<?php print $end; ?>" <?php echo $disable; ?> />
					<br />
				</td>
			</tr>
			<?php
			if($enable==1 && $disable=='')
			{
			?>
			<tr class="newArea"><td colspan="4"><div class="ourprojectrow"></div></td></tr>
			<tr><td colspan="4"><h2 class="inner">Στοιχεία έδρας</h2></td></tr>
			<tr>
				<td rowspan="11" colspan="2">
					<!--map-->
					<table class="mainTable" cellpadding="0" cellspacing="0">
						<tr>
							<td style="padding: 5px;">
								<div id="map_canvas1" class="mapCanvasStyle" title="Κάνετε κλικ πάνω στο χάρτη για να πάρετε τις συντεταγμένες και τη διεύθυνση."></div>
							</td>
						</tr>
					</table>
					<!--map-->
				</td>
				<td><b> Διακριτικός Τίτλος </b></td>
				<td><input tabindex="13" name="label" type="text" size="40" value="<?php print $label; ?>" /></td>
			</tr>
			<tr>
				<td><input title="Κάνετε κλικ στο χάρτη" placeholder="Κάνετε κλικ στο χάρτη" id="lati" name="lati" type="text" readonly="readonly" size="20" value="<?php print $lati; ?>" /> * </td>
				<td><input title="Κάνετε κλικ στο χάρτη" placeholder="Κάνετε κλικ στο χάρτη" id="longi" name="longi" type="text" readonly="readonly" size="20" value="<?php print $longi; ?>" /> * </td>
			</tr>
			<tr>
				<td><b> Οδός * </b><sub> (με κλικ στο χάρτη) </sub> </td>
				<td><input tabindex="14" placeholder="Κάνετε κλικ στο χάρτη" title="Κάνετε κλικ στο χάρτη.." id="street" name="street" type="text" size="40" value="<?php print $street; ?>"/></td>
			</tr>
			<tr>
				<td><b> Αριθμός/Κτίριο </b></td>
				<td><input tabindex="15" placeholder="..διορθώστε αν χριάζεται με πληκτρολόγηση"  id="building" name="building" type="text" size="40" value="<?php print $building; ?>" /></td>
			</tr>
			<tr>
				<td><b> Ταχυδρομικός Κώδικας </b></td>
				<td><input tabindex="16" id="addres2" name="zip" type="text" size="5" maxlength="5" value="<?php print $zip; ?>"/></td>
			</tr>
            
			<tr>
				<td><b> τηλέφωνo 1 </b></td>
				<td><input tabindex="17" value="<?php print $tel1; ?>" name="tel1" type="text" size="14" maxlength="14" title="έως 14 ψηφία!" /></td>
			</tr>
			<tr>
				<td><b> τηλέφωνo 2 </b></td>
				<td><input tabindex="18" value="<?php print $tel2; ?>" name="tel2" type="text" size="14" maxlength="14" title="έως 14 ψηφία!" /></td>
			</tr>
			<tr>
				<td><b> κινητό </b></td>
				<td><input tabindex="19" value="<?php print $tel3; ?>" name="tel3" type="text" size="14" maxlength="14" title="έως 14 ψηφία!" /></td>
			</tr>
			<tr>
				<td><b> τηλεομοιοτυπία (φαξ) </b></td>
				<td><input tabindex="20" value="<?php print $telefax; ?>" name="telefax" type="text" size="14" maxlength="14" title="έως 14 ψηφία!" /></td>
			</tr>
			<tr>
				<td><b>ηλ. αλληλογραφία(1) </b></td>
				<td><input tabindex="21" value="<?php print $email1; ?>" name="email1" type="text" size="40" maxlength="50" /></td>
			</tr>
			<tr>
				<td><b>ηλ. αλληλογραφία(2) </b></td>
				<td><input tabindex="22" value="<?php print $email2; ?>" name="email2" type="text" size="40" maxlength="50" /></td>
			</tr>

			<tr>
				<td>
					<b>Κατηγορίες </b>
				</td>
				<td id="company" colspan="2">
					<select style="width:160px" name="occupation1"><?php echo $occupationInList[0] ?></select>
					<select style="width:160px" name="occupation2"><?php echo $occupationInList[1] ?></select>
					<select style="width:160px" name="occupation3"><?php echo $occupationInList[2] ?></select>
				</td>
				<td>
					<sup> Άλλη κατηγορία που δε βρίσκεται στη λίστα </sup>
					<br />
					<input tabindex="23" placeholder="Άλλη κατηγορία" value="<?php print $extrActivity; ?>" name="extrActivity" type="text" size="40" maxlength="50" />
				</td>
			</tr>
		
			<tr>
				<td colspan="4" align="center">
				<input type="submit" name="submit" class="button" value="Αποστολή!" <?php echo $disable; ?> /><br /></td>
			</tr>
			<?php
			}
			else
			{
			}
			?>
			
		</table>
		</form>
		<br />
		<?php
		}

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