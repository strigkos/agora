<?php
	/*
		Σ : 23/10/2015
		INSERT A ORGANIZATION AND AN OFFICE
	*/

	include_once('admin/settings1.php');
	include_once($schema['abs_dir'] . 'modules/modParameters.php');
	include_once($schema['abs_dir'] . 'modules/modValidity.php');

	ob_start();
	@session_start();

	/// Initial Parameters
	$email = '';
	$msg = '';
	$_SESSION["userLog"] = -1;

	/// CHECK WP LOGIN
	require_once($schema['abs_cms'] . 'wp-load.php');
	require_once($schema['abs_cms'] . 'wp-config.php');
	require_once($schema['abs_cms'] . 'wp-blog-header.php');
	$current_user = wp_get_current_user();

	// SESSION LOG
	$_SESSION["fullName"] = $current_user->display_name;
	$_SESSION["userName"] = $current_user->user_login;
	$_SESSION["userID"] = $current_user->user_email;
	$_SESSION["userLog"] = $current_user->user_level;

	$feedStyle['warn'] = 'ui-state-highlight ui-corner-all'; // warning
	$feedStyle['error'] = 'ui-state-error ui-corner-all'; // error
	$feedBackStyle = $feedStyle['warn'];
	$schema['seo']='no';

	$msg0 = $msg1 = '';
	$subject = '';
	$mailContext = '';
	$availMsg = '';
	$successLevel = 0;
	
	// temporarily without login or recaptcha
	$enable = 1;
	$disable = '';

	// DECLARE VARIABLES and SET VALUES
	// user
	$email = '';
	$newsList = (isset($_POST['newsList']) && $_POST['newsList']=='on') ? (1) : (0);
	
	// professional
	$proID = -1;
	$companyID = -1;
	$checking = ''; // 'biznes' . rand();
	//$alias = '';
	$proName = '';
	$lastName = '';
	$firstName = '';
	$description = '';
	$notes = '[host]'; // host
	$regDate = '2012-01-01'; // today
	$duration = 24; // today
	$phone = '';
	//$email = '';
	$url = '';
	$rank = 1;
	
	if (empty($_SESSION["userID"]))
	{
		//δε μπορεί να καταχωρήσει
		$operator = 'biznes';
	}
	else
	{
		$operator = $_SESSION["userID"];
	}

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
	$skype = ''; 
	
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
	if(isset($_POST['description'])) { $description = nl2br(stripper($_POST['description'])); }
	if(isset($_POST['url'])) { $url = stripper($_POST['url']); }
	
	// bizOffer
	if(isset($_POST['offerComments'])) { $offerComments = nl2br(stripper($_POST['offerComments'])); }
	if(isset($_POST['start'])) { $start = stripper($_POST['start']); }
	if(isset($_POST['end'])) { $end = stripper($_POST['end']); }

	// bizCompany
	if(isset($_POST['companyID'])) { $companyID = keepOnlyNumbers($_POST['companyID']); }
	if(isset($_POST['lati'])) { $lati = stripper($_POST['lati']); }
	if(isset($_POST['longi'])) { $longi = stripper($_POST['longi']); }
	if(isset($_POST['zip'])) { $zip = stripper($_POST['zip']); }
	if(isset($_POST['label'])) { $label = stripper($_POST['label']); }
	if(isset($_POST['street'])) { $street = stripper($_POST['street']); }
	$cantry = array($zip, ", Ελλάς", ", ΕΛΛΑΣ", ", Ελλάδα", ", ΕΛΛΑΔΑ", ", Hellas", ", HELLAS", ", Greece", ", GREECE");
	$street = str_replace($cantry, '', $street); 
	if(isset($_POST['building'])) { $building = stripper($_POST['building']); }
	if(isset($_POST['tel1'])) { $tel1 = keepOnlyNumbers($_POST['tel1']); }
	if(isset($_POST['tel2'])) { $tel2 = keepOnlyNumbers($_POST['tel2']); }
	if(isset($_POST['tel3'])) { $tel3 = keepOnlyNumbers($_POST['tel3']); }
	if(isset($_POST['telefax'])) { $telefax = keepOnlyNumbers($_POST['telefax']); }
	if(isset($_POST['email1'])) { $email1 = stripper($_POST['email1']); }
	if(isset($_POST['skype'])) { $skype = stripper($_POST['skype']); }
	
	// Activity
	if(isset($_POST['occupation1'])) { $occupations[0] = stripper($_POST['occupation1']); }
	if(isset($_POST['occupation2'])) { $occupations[1] = stripper($_POST['occupation2']); }
	if(isset($_POST['occupation3'])) { $occupations[2] = stripper($_POST['occupation3']); }
	if(isset($_POST['extrActivity'])) { $extrActivity = stripper($_POST['extrActivity']); }

	// CHECK DATA
	if(!empty($checking))
	{
		// exists?
		$proSelect = " SELECT id, alias FROM bizProfessional  WHERE alias='" . $checking . "'";
		$findPro = @mysql_query($proSelect, $db);
		$proDetails = mysql_fetch_array($findPro);

		// ckeck
		if($proDetails[0]>0 || empty($checking))
		{
			$enable = -1;
			$msg0 = 'Επιλέξτε πρώτα ένα διαθέσιμο αναγνωριστικό.'; 
			$feedBackStyle = $feedStyle['error'];
		}	
		elseif(!isValidEmail($email))
		{
			$enable = -1;
			$msg0 .= ' Εισάγετε όλα τα υποχρωτικά στοιχεία του επαγγελματία. ';
			$msg1 .= ' Εισάγεται έναν έγκυρο email! ';
			$feedBackStyle = $feedStyle['error'];
		}
		else if (empty($lati) && empty($tel1) && empty($tel3) && empty($email1))
		{
			$enable = -1;
			$msg0 = 'Καταχωρήστε τουλάχιστον έναν δημοφιλή τρόπο επικοινωνίας (συντεταγμένες έδρας ή τηλέφωνο(1) ή κινητό ή e-mail)'; 
			$feedBackStyle = $feedStyle['error'];
		}
		else if (empty($occupations[0]) && empty($occupations[1]) && empty($occupations[2]) && empty($extrActivity))
		{
			$enable = -1;
			$msg0 = 'Καταχωρήστε μία δραστηριότητα!'; 
			$feedBackStyle = $feedStyle['error'];
		}
		else if ($url)
		{
			if(isValidURL($url) == '')
			{
				$enable = -1;
				$msg0 = 'Η ιστοσελίδα που δηλώσατε δεν υπαρχει! Διορθώστε ή αφαιρέστε την.'; 
				$feedBackStyle = $feedStyle['error'];
			}
			else if (isValidURL($url) != $url)
			{
				$enable = -1;
				$msg0 = 'Επαληθεύστε την ιστοσελίδα που δώσατε!';
				$feedBackStyle = $feedStyle['error'];
				$url = isValidURL($url);
			}
		}
	}
	else // checking not given
	{
		$enable = 0;
		$feedBackStyle = $feedStyle['warn'];
		$msg0 = ' Τα πεδία με αστερίσκο είναι υποχρεωτικά! ';
		$msg1 = ' Eπιλέξετε πρώτα ένα μοναδικό αναγνωριστικό και ελέγξτε τη διαθεσιμότητά του! ';
	}

	// TRY INSERT OR UPDATE
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["submit"]) && $enable==1)
	{
		$mailContext .= "\r\n";
		$mailContext .= "\r\n";
		$mailContext .= ' ΕΙΣΑΓΩΓΗ ΕΠΠΑΓΓΕΛΜΑΤΙΑ: ';
		$mailContext .= '<strong><a href="' . $siteName . $checking . '">' . $domain . '/' . $checking . '</a></strong>';
		$mailContext .= '<br />' . $proName . ', ' . $lastName . ', ' . $firstName;
		$mailContext .= '<br />' . $phone . ', ' . $email . ', ' . $url . ', ' . $description;
		$msg0 = ' Εισαγωγή νέου επαγγελματία: ';
		$insertUser = " INSERT INTO bizProfessional (proName, alias, rank, description, picture, styl, lastName, firstName, phone, email, url, notes, regDate, duration, operator) 
						VALUES ('" . $proName . "', '" . $checking . "', 1, '" . $description . "', '0', '0', '" . $lastName . "', '" . $firstName . "', '" . $phone . "', '" . $email . "', '" . $url . "', '" . $notes . "', '" . $regDate . "', " . $duration . ", '" . $operator . "')";
		$insertOK = mysql_query($insertUser, $db);
		if (!$insertOK) 
		{
			echo $insertUser;
			die('Invalid query: ' . mysql_error());
		}
		else
		{
			//$proID = mysql_insert_id();
			$successLevel = 1;
			// OFFER
			if(strlen($offerComments) >= 20 && $start != '' && $end != '')
			{
				//$deleteOffer = " DELETE FROM bizOffer WHERE proAlias = '" . $checking . "'";
				//mysql_query($deleteOffer, $db);
				$insertOffer = " INSERT INTO bizOffer (proAlias, description, start, end) 
								VALUES ('" . $checking . "', '" . $offerComments . "', '" . $start . "', '" . $end . "')";
				$offerOK = mysql_query($insertOffer, $db);
				if($offerOK)
				{
					/*
					print $insertOffer;
					print 'ok';
					print '(' . $offerOK . ')';
					*/
				}
				else
				{
					/*
					print $insertOffer . 'Not ok';
					print '(' . $offerOK . ')';
					*/
				}
			}
			
			// Company-Office-Store
			if(!empty($street))
			{
				$insertCompany = " INSERT INTO bizCompany
				(proAlias, lat, lng, zip, label, description, street, building, tel1, tel2, tel3, fax, email1, email2) 
								VALUES 
				('" . $checking . "', '" . $lati . "', '" . $longi . "', '" . $zip . "', '" . $label . "', '" . $description 
				. "', '" . $street. "', '" . $building. "', '" . $tel1. "', '" . $tel2. "', '" . $tel3
				. "', '" . $telefax. "', '" . $email1. "', '" . $skype . "')";
				$companyOK = mysql_query($insertCompany, $db);
				if($companyOK)
				{
					/*
					print $insertCompany;
					print 'ok';
					print '(' . $companyOK . ')';
					*/
				}
				else
				{
					/*
					print $insertCompany . 'Not ok';
					print '(' . $companyOK . ')';
					*/
				}

				$mailContext .= "\r\n";
				$mailContext .= "\r\n";
				$mailContext .= ' ΕΔΡΑ: ';
				$mailContext .= $label . ', ' . $street . ' ' . $building . ', ' . $zip . ', '
							. $tel1 . ', ' . $tel2 . ', ' . $tel3 . ', ' . $telefax . ', ' 
							. $email1 . ', ' . $skype;
				$mailContext .= "\r\n";
				$mailContext .= '(' . $lati . ', ' . $longi . ')' ;
				$mailContext .= "\r\n";
				$successLevel = 3;
			}
			else
			{
				$msg0 .= ' Δεν δόθηκε έδρα! ';
				$successLevel = -3;
			}
		}
		
		// Activities
		$mailContext .= '<br /> ΔΡΑΣΤΗΡΙΟΤΗΤΕΣ: ';
		$ocupationGiven = 0;
		$mailContext .= '<ol>';
		for($i=0; $i<=2; $i++)
		{
			// DELETE PREVIOUS
			if(isset($occupations[$i]) && $occupations[$i]>0)
			{
				// insert activity
				$strOcuName = @mysql_query(" SELECT nameEL FROM  bizOccupation WHERE id =" . $occupations[$i] , $db);
				$qOcuName = mysql_fetch_array($strOcuName);
				if($qOcuName)
				{
					$ocupationGiven = $ocupationGiven + 1;
					$mailContext .= '<li>' . $qOcuName[0] . '</li>';
				}
				else
				{
					$msg1 .= ' Σφάλμα στα στοιχεία της κατηγορίας (' . $occupations[$i] . ')';
				}
			}
			else
			{
				$msg1 .= '<li> (δε δόθηκε) </li>';
			}
		}
		$mailContext .= '</ol>';
		if(!empty($extrActivity))
		{
			$ocupationGiven = $ocupationGiven + 1;
			$mailContext .= "\r\n";
			$mailContext .= "\r\n";
			$mailContext .= ' επιπλέον δραστηριότητα: ' . $extrActivity;
		}
		
		if($ocupationGiven > 0)
		{
			//$successLevel
		}
		else
		{
			$successLevel = -4;
		}
		// nl2br
		// SEND MAIL
		if ($successLevel >= 3)
		{
			$regDetails = nl2br(mailContext) . '[' . $userID . ']';
			if($evironment == 'dev')
			{
				// sent
				$userID = str_replace('.', '!', $mail);
				$userID = str_replace('@', '~', $userID);
				
				$mailContext .= "\r\n" . $siteName . '/update.php' . '?alias=' . $checking . '&userID=' . $userID . "\r\n";
				$subject = 'ΕΙΣΑΓΩΓΗ ΕΠΑΓΓΕΛΜΑΤΙΑ - PROFESSIONAL DETAILS';
				$sender = 'From: Xartes Agoras Contact <' . $siteMail . '>';
				$encode =  'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n";
				$sentmail = mail($bizInbox, $subject, $mailContext, $encode . $sender);
			}
			else
			{
				// write file
				$msg1 = 'Στάλθηκε mail';
			}
			$insertUser = " INSERT INTO bizUser (email, fullName, telephone, log, curPass, newPass, confirmation, newsList) 
							VALUES ('" . $email . "', '" . $lastName . "', '" . $phone . "', '1', '0', '0', '0', '" . $newsList . "')";
			$insertOK = mysql_query($insertUser, $db);
			if (!$insertOK) 
			{
				//die('Invalid query: ' . mysql_error());
			}
		}
		else
		{
			$msg1 = 'Δεν στάλθηκε η καταχώρηση!';
			if($successLevel==-4)
			{
				$msg1 .= ' Δε δόθηκε κατηγορία επαγγέλματος! ';
			}
			$feedBackStyle = $feedStyle['error'];
		}
	}
	elseif(isset($_POST["reset"]))
	{
		header('Location: ' . $_SERVER['PHP_SELF']);
	}
	
	// Activities drop down menus
	$occupationsQuery = "SELECT id, nameEL FROM bizOccupation ORDER BY nameEL ";
	$occupationInList[0] = '';
	$occupationInList[1] = '';
	$occupationInList[2] = '';
	$i = 0;
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
		include_once('modules/modHead.php');
		include_once('modules/jsGMapsAPI.php');
		include_once('interface/jQueryUI.php');
		/*
		if(file_exists($schema['ads_dir'] . 'modules/jsTrackingCode.php') && $schema['envi'] == 'prd') {
			include_once($schema['ads_dir'] . 'modules/jsTrackingCode.php');
		}
		*/
	?>
</head>

<body onload="showMarker();">
	<div id="wrapper">
	<div id="workZone">
	<div class="subscribeZone">
	<div class="subscribeTop">
	<?php
			include('modules/partLogo.php');
	?>
	<div class="textZone">	
		<h1 class="inner">Καταχώρηση επαγγελματία</h1>
		<h4 class="inner">
		<?php
			if(isset($_SESSION["userMail"]))
			{
				echo('Καλωήρθατε <i> ' . $_SESSION["userAlias"] . '</i>, διεύθυνη χρήστη: ' . $_SESSION["userMail"]);
			}
			else
			{
				print 'Είστε εδώ ως επισκέπτης. Δεν έχετε δικαιώματα καταχωρήσεων. Κάντε είσοδο για να διαχειρίστείτε τις καταχωρήσεις σας!';
			}
		?>
		</h4>
		<?php 
		if($successLevel>0)
		{
		?>
			<table class="inputs">
			<tr>
				<td width="60%"><b> Η καταχώρηση σας στάλθηκε στο διαχειριστή και θα καταχωρηθεί σύντομα. Ευχαριστούμε!</b></td>
				<td width="40%" ></td>
			</tr>
			<tr>
			<td>
			<?php
				print $regDetails; 
			?>
			</td>
			</tr>
			</table>
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
		<div style="width:800px">
			<h2 style="float:left" class="inner">Καταχώρηση επαγγελματία και μιας έδρας του!</h2>
			<span style="margin-top:7px; color:purple; float:right;">
				<a style="color:purple" href="<?php echo $_SERVER['REQUEST_URI']; ?>"> Καινούρια φόρμας!</a> | 
				<b><a href="//<?php echo $schema['charts']; ?>login">Χειριστής</a></b> : <i><?php print $operator; ?></i>
			</span>
		</div>
		<div class="clear"></div>
		<table class="inputs">
			<tr class="last">
			<td class="<?php echo $feedBackStyle; ?>" style="margin:10px; padding:0.7em;">
			<b>
			<?php
				print $msg0;
				print '<br />';
				print '<br />';
				print $msg1;
			?>
			</b>
			<br />
			</td>
			</tr>
		</table/>
			<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data" name="new-pro">
			<table class="inputs">
			<tr class="newArea"><td colspan="4"><div class="ourprojectrow"></div></td></tr>
			<tr><td colspan="4">
				<h2 class="inner">Στοιχεία επαγγελματία<em> (δεν εμφανίζονται στους επισκέτπες) </em></h2>
			</td></tr>
			<tr>
				<td colspan="4">
					<table style="width:100%; border: 2px green solid">
						<tr>
							<td rowspan="2">
								<b> αναγνωριστικό <br /> (alias)*</b>
							</td>
							<td style="width:400px; height:40px">				
								<?php echo $schema['nice']; ?>/<input onchange="xmlhttpPost('multicast/check-alias.php', 'new-pro', 'checked', '<img src=\'multicast/pleasewait.gif\'>'); return false;" tabindex="1" name="checking" id="cehcking" type="text" maxlength="30" size="40" placeholder="epipla-ampelokipoi" value="<?php print $checking; ?>" />
							</td>
							<td>
								<div name="checked" id="checked"></div>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<span style="font-style:italic; line-height:150%">Μία διεύθυνση της μορφής <a href="<?php echo $schema['site']; ?>photo-gamos" target="_blank"><b><?php echo $schema['nice'] ?>/photo-gamos</b></a>
								είναι πολύ φιλική στις μηχανές αναζήτησης ώστε να βοηθήσει να αναδειχθείτε και μεταδίδεται εύκολα στους συνεργάτες σας.</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			
			<tr>
				<td><b>ηλ-ταχυδρομείο<br />(e-mail) *</b></td>
				<td><input tabindex="2" name="email" type="text" size="40" value="<?php print $email; ?>" <?php echo $disable; ?> /></td>
				<td> Επωνυμία<br />επιχείρησης </td>
				<td><input name="proName" type="text" size="40" value="<?php print $proName; ?>" <?php echo $disable; ?> /></td>
			</tr>
			<tr>
				<td><b> Λίστα ενημερώσεων </b></td>
				<td><input tabindex="3" name="newsList" type="checkbox" checked="checked" <?php echo $disable; ?> /></td>
				<td> Επώνυμο </td>
				<td><input name="lastName" type="text" size="40" value="<?php print $lastName; ?>" <?php echo $disable; ?> /></td>
			</tr>
			<tr class="last">
				<td><b> Τηλέφωνo</b></td>
				<td><input tabindex="4" name="phone" value="<?php print $phone; ?>" <?php echo $disable; ?> type="text" size="20" maxlength="14" title="έως 14 ψηφία!" /></td>
				<td > Όνομα </td>
				<td><input name="firstName" type="text" size="40" value="<?php print $firstName; ?>" <?php echo $disable; ?> /></td>
			</tr>
			<tr class="newArea"><td colspan="4"><div class="ourprojectrow"></div></td></tr>
			<tr><td colspan="4">
				<h2 class="inner"> Επιπλέον πληροφορίες <em> (ορατές στους επισκέπτες) </em></h2>
			</td></tr>
			<tr>
				<td><b> Ιστοσελίδα </b></td>
				<td><i>http://</i><input tabindex="8" placeholder="www.yoursite.gr"  name="url" type="text" size="40" value="<?php print $url; ?>" <?php echo $disable; ?> /></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td><b> Περιγραφή </b></td>
				<td colspan="3">
				<textarea tabindex="9"  placeholder="Δώστε μία περιγραφή της δραστηριότητάς σας. Ένα κείμενο 200 με 300 λέξεις."  name="description" size="500" cols="80" rows="10" <?php echo $disable; ?> ><?php print trim($description); ?></textarea></td>
			</tr>
			<tr>
				<td><b> Προσφορές </b></td>
				<td colspan="2">
					<textarea tabindex="12" placeholder="Δώστε μία προσφορά (τουλάχιστον 20 χαρακτήρες)"  name="offerComments" size="500" cols="60" rows="5" <?php echo $disable; ?> ><?php print trim($offerComments); ?></textarea>
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
			<tr class="newArea"><td colspan="4"><div class="ourprojectrow"></div></td></tr>
			<tr><td colspan="4"><h2 class="inner">Στοιχεία έδρας <em> (ορατά στους επισκέπτες) </em></h2></td></tr>
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
				<td><input tabindex="13" name="label" type="text" size="40" placeholder="Αίγλη Cafe" value="<?php print $label; ?>" /></td>
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
				<td> τηλέφωνo 2 </td>
				<td><input value="<?php print $tel2; ?>" name="tel2" type="text" size="14" maxlength="14" title="έως 14 ψηφία!" /></td>
			</tr>
			<tr>
				<td><b> κινητό </b></td>
				<td><input tabindex="19" value="<?php print $tel3; ?>" name="tel3" type="text" size="14" maxlength="14" title="έως 14 ψηφία!" /></td>
			</tr>
			<tr>
				<td> τηλεομοιοτυπία (φαξ) </td>
				<td><input value="<?php print $telefax; ?>" name="telefax" type="text" size="14" maxlength="14" title="έως 14 ψηφία!" /></td>
			</tr>
			<tr>
				<td><b>ηλ. αλληλογραφία </b></td>
				<td><input tabindex="21" value="<?php print $email1; ?>" name="email1" type="text" size="40" maxlength="50" /></td>
			</tr>
			<tr>
				<td><b> Skype </b></td>
				<td><input tabindex="22" value="<?php print $skype; ?>" name="skype" type="text" size="40" maxlength="50" /></td>
			</tr>

			<tr>
				<td>
					<b>Κατηγορίες *</b>
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
				<td colspan="2" align="center">
					<input type="submit" name="submit" class="button" value="Αποστολή!" <?php echo $disable; ?> /><br />
				</td>
				<td colspan="2" align="center">
					<input type="reset" name="reset" class="button" value="Καθαρισμός!" <?php echo $disable; ?> /><br />
				</td>
			</tr>
		</table>
		</form>
		<hr />
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
			include_once('modules/partSideMenu.php');
			include_once('modules/partBottomMenu.php');
		?>
		<script src="multicast/ajaxsbmt.js" type="text/javascript"></script>
		<script type="text/javascript" src="<?php echo $schema['site']; ?>interface/js/jquery.ui.datepicker.js"></script>
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
	</div>
</body>
</html>