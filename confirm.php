<?php
	/// 
	// CHECK LOG
	include_once('admin/settings1.php');
	include_once('modules/modValidity.php');

	$textFeed = array("\r\n", "\n", "\r");
	$htmlFeed = '<br />';

	$msg0 = '';
	$msg1 = '';
	$enable = 0;

	// LEVEL: 1=Proessional, 2=Picture, 3=Offer, 4=Company(ies), 5=Activities
	$level = -1; 

	// DECLARE VARIABLES and SET VALUES
	$proID = -1;
	$companyID = -1;
	$multipleStores = 0;
	$alias = '';
	$proName = ' - ';
	$lastName = ' - ';
	$firstName = ' - ';
	$description = ' - ';
	$phone = ' - ';
	$email = ' - ';
	$url = ' - ';
	$notes = ' - ';
	$rank = 0;
	$styl = 0;
	$today = getdate();
	$regDate = $today['year'] . '-' . $today['mon'] . '-' . $today['mday'];
	$duration = 60;
	$operator = '';
	$confirmNumber = '';

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

	// FIND via companyID
	if($_SERVER['REQUEST_METHOD']=='GET' && !empty($_GET['confirmNumber']) && !empty($_GET['operator']))
	{
		$confirmNumber = stripper($_GET['confirmNumber']);
		$operator = stripper($_GET['operator']);
		$proExists = @mysql_query(" SELECT * FROM bizProfessional
									WHERE email='" . $operator . "' AND confirmation='" . $confirmNumber . "' ", $db);
		$proDetails = mysql_fetch_array($proExists);
		if(empty($proDetails[0])) 
		{
			$enable = -1;
			//header('Location: ../');
		}
		elseif($proDetails[0] > 0 && !empty($proDetails[1]) && $proDetails[3]==1) 
		{
			$alias = $proDetails[2];
			// DO!!!!
			// Update Pro SET rank = 2
			$qPro2 = @mysql_query(" UPDATE bizProfessional 
									SET operator='" . $operator . "', rank=2" .
									" WHERE alias='" . $alias . "'", $db);
			mysql_query($qPro2);

			// Insert into bizOperator
			$fullName = $proName . ' ' . $lastName;
			$qUser = @mysql_query(" INSERT INTO bizOperator (email, fullName, telephone, log, curPass, newPass, confirmation)
									VALUES ('" . $operator . "', '" . $fullName . "', '" . $phone . "', " . 0 . ", '', '" . rand() . "', '' )", $db);
			mysql_query($qUser);

			// SEND MAIL to biz
			$subject = 'Κατοχύρωση επαγγελματία - Katoxyrwsi epaggelmatia ';
			$sender = 'From: Xartes Agoras Contact <' . $siteMail . '>';
			$context = " Ο χρήστης " . $operator . " κατοχύρωσε τον επαγγελματία " .  $alias;
			$context .=  "\r\n";

			//send
			if($environment == 'prd')
			{
				$encode = 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n";
				$sentmail = mail($bizInbox, $subject, $context, $encode . $sender);
			}
			else
			{
				$myFile = "mailFile.htm";
				$fh = fopen($myFile, 'w');
				fwrite($fh, '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\r\n");
				fwrite($fh, "<title>" . $subject . "</title>" . "\r\n");
				fwrite($fh, str_replace($textFeed, $htmlFeed, $context));
				fclose($fh);
			}
			$enable=1;
		}
		// Professional
		$proExists = @mysql_query(" SELECT * FROM bizProfessional WHERE alias='" . $alias . "' ", $db);
		$proDetails = mysql_fetch_array($proExists);
		$proID = $proDetails[0];
		$proName = $proDetails[1];
		$alias = $proDetails[2];
		$rank = $proDetails[3];
		$description = $proDetails[4];
		$picture = $proDetails[5];
		$styl = $proDetails[6];
		$lastName = $proDetails[7];
		$firstName = $proDetails[8];
		$phone = $proDetails[9];
		$email = $proDetails[10];
		$url = $proDetails[11];
		$notes = $proDetails[12];
		$regDate = $proDetails[13];
		$duration = $proDetails[14];
		$lastUpdate = $proDetails[15];
		$operator = $proDetails[16];

		// Company
		$getCompany = @mysql_query(" SELECT * FROM bizCompany WHERE proAlias = '$alias' ", $db);
		$companyDetails = mysql_fetch_array($getCompany);
		$companyID = $companyDetails[0];
		//$proAlias = $companyDetails[1];
		$lati = $companyDetails[2];
		$longi = $companyDetails[3];
		$zip = $companyDetails[4];
		$label = $companyDetails[5];
		$discription = $companyDetails[6];
		$areaID = $companyDetails[7]; //area
		
		if($areaID>0)
		{
			$getArea = @mysql_query(" SELECT id, cityID, districtID, regionID FROM geoAreaNames WHERE id = '$areaID' ", $db);
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
		
		// Activities
		$getActivities = @mysql_query(" SELECT occupation 
										FROM bizActivities
										WHERE company = '$companyID' 
										ORDER BY occupation ", $db);
		$r = 0;
		while($occupationRecord = mysql_fetch_row($getActivities))
		{
			$occupations[$r] = $occupationRecord[0];
			$r++;
		}
	}
	else
	{
		$enable = 0;
	}
	
	switch($enable)
	{
		case -1:
			$msg0 = ' Κάποιο σφάλμα συνέβη! ';
			$msg1 = ' Αν θέλετε μπορείτε να επικοινωνήσετε με τους διαχειριστές. ';
			break;
		case 0:
			$msg0 = ' Δώστε τις σωστές παραμέτρους! ';
			$msg1 = ' Αν θέλετε μπορείτε να επικοινωνήσετε με τους διαχειριστές. ';
			break;
		case 1:
			$msg0 = ' Η καταχώρησή σας επιβεβαιώθηκε και αναβαθμίστηκε! ';
			$msg1 = ' Για όποιες αλλαγές θέλετε να στείλετε στο μέλλον χρησιμοποιήστε τον ίδιο λογαριασμό ηλ. ταχυδρομείου (' . $email . ')';
			break;
		/*
		case 2:
			$msg0 = ' Η αποστολή ολοκληρώθηκε επιτυχώς! '; 
			$msg1 = ' Ελέγξτε τα εισερχόμενα του ' . $email . ' για οδηγίες ώστε να ολοκληρώσετε την αναβάθμιση της καταχώρησης. ';
			break;
		*/
		default :
			break;
	}
	
	// Activities drop down menus ???
	$occupationsQuery = "SELECT id, nameEL FROM bizOccupation ORDER BY nameEL ";
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
			if(isset($occupations[$i]) && $occupationRecord[0] == $occupations[$i]) { $occupationInList[$i] .= 'selected="selected"'; }
			$occupationInList[$i] .= '>' . $occupationRecord[1];
			$occupationInList[$i] .= '</option>';
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="keywords" content="επιεβεβαίωση καταχώρησης" />
	<meta name="description" content=" Ολοκλήρωση διαδικασίας καταχώρησης!" />
	<?php
		include_once('interface/jQueryUI.php');
		include_once('modules/jsGMapsAPI.php');
		include_once('modules/modHead.php');
	?>
</head>

<body onload="showMarker();">
<?php
	include_once('modules/partSideMenu.php');
?>

<div id="wrapper">
<div id="workZone">

<div class="subscribeZone">
<div class="subscribeTop">
	<?php
			include_once('modules/partLogo.php');
	?>
<div class="textZone">
		<h1 class="inner">Καταχώρηση επαγγελματία</h1>
		<table width="88%" cellpadding="2" cellspacing="2"><tr>
			<td width="50%"><h5><?php print $msg0 ?></h5></td></td>
		</tr></table>
		<?php
		if($enable<=1)
		{?>
			<b><?php print $msg1; ?></b>
			<br />
			<hr />
			<div class="ourprojectrow">
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
				<table width="910px">
				<tr>
					<td><b> ψευδώνυμο χρήστη </b></td>
					<td><input readonly="readonly" type="text" name="alias" value="<?php print $alias; ?>" size="40" readonly="readonly" /></td>
					<td><i><?php if($proID>0) { print 'έχει καταχωρηθεί: ' . $proID; } else { print 'δεν έχει καταχωρηθεί'; } ?></i></td>
					<td><b>χειριστής: </b><i><?php print $operator; ?></i></td>
				</tr>
		<?php
		}
		if($enable==1)
		{
		?>
			<tr>
				<td><b> Επωνυμία επιχείρησης: </b></td>
				<td><input readonly="readonly" tabindex="3" name="proName" type="text" size="40" value="<?php print $proName; ?>" /></td>
				<td><b> τηλέφωνo: </b></td>
				<td><input readonly="readonly" name="phone" value="<?php print $phone; ?>" type="text" size="14" maxlength="14" title="έως 14 ψηφία!" /></td>
			</tr>
			<tr>
				<td><b> Επώνυμο: </b></td>
				<td><input readonly="readonly" tabindex="4" name="lastName" type="text" size="40" value="<?php print $lastName; ?>" /></td>
				<td><b> Ημερομηνία καταχώρησης: </b></td>
				<td><input readonly="readonly" name="regDate" type="date" size="10" value="<?php print $regDate; ?>" />
				</td>
			</tr>
			<tr>
				<td ><b> Όνομα: </b></td>
				<td><input readonly="readonly" tabindex="5" name="firstName" type="text" size="40" value="<?php print $firstName; ?>" /></td>
				<td><b> Διάρκεια (μήνες): </b></td>
				<td><input readonly="readonly" name="duration" type="text" size="3" value="<?php print $duration; ?>" readonly="readonly"/></td>
			</tr>
			<tr>
				<td><b> email: </b></td>
				<td><input readonly="readonly" name="email" type="text" size="40" value="<?php print $email; ?>" /></td>
				<td><b> Ιστοσελίδα: </b></td>
				<td><input readonly="readonly" name="url" type="text" size="40" value="<?php print $url; ?>" /></td>
			</tr>
			<tr>
				<td><b> Περιγραφή: </b></td>
				<td colspan="3"><textarea readonly="readonly" name="description" size="1000" cols="82" rows="10" ><?php print trim($description); ?></textarea></td>
			</tr>
			<tr>
				<tr>
				<td><b> Δείκτης εμφάνισης: </b></td>
				<td><?php include('modules/inRank.php'); ?></td>
				<td><b> Στυλ εμφάνισης: </b></td>
				<td><?php include('modules/inStyle.php'); ?></td>
			</tr>
			<tr>
				<td><b> Φωτογραφία: </b></td>
				<td><img border="1px" width="100px" height="100px" src="interface/images/notavailable.jpg" /></td>
				<td colspan="2"><input type="file" name="picture" accept="image/*" /></td>
			</tr>
			<tr><td colspan="4"><hr /></td></tr>
			<tr>
				<td><b> Προσφορά: </b></td>
				<td align="right"> 
					από: <input readonly="readonly" type="date" name="start" size="19" maxlength="19" value="<?php print $start; ?>" />
					<br /> <br /> 
					έως: <input readonly="readonly" type="date" name="end" size="19" maxlength="19" value="<?php print $end; ?>" />
				</td>
				<td colspan="2"><textarea readonly="readonly" name="offerComments" size="500" cols="40" rows="5" ><?php print trim($offerComments); ?></textarea></td>
			</tr>
			<tr><td colspan="4"><hr /></td></tr>
			<tr>
				<td align="center" rowspan="12" colspan="2">
					<!--map-->
					<table class="mainTable" cellpadding="0" cellspacing="0" align="center">
						<tr valign="top">
							<td style="padding: 5px;"><div id="map_canvas1" class="mapCanvasStyle"></div></td>
						</tr>
					</table>
					<!--map-->
				</td>
				<td><b> Διακριτικός Τίτλος:</b></td>
				<td><input readonly="readonly" name="label" type="text" size="40" value="<?php print $label; ?>" /></td>
			</tr>
			<tr>
				<td>αα επιχείρησης</td>
				<td><input readonly="readonly" name="companyID" type="text" size="12" value="<?php print $companyID; ?>" /></td>
			</tr>
			<tr>
				<td><input readonly="readonly" id="lati" name="lati" type="text" size="25" value="<?php print $lati; ?>" /></td>
				<td><input readonly="readonly" id="longi" name="longi" type="text" size="25" value="<?php print $longi; ?>" /></td>
			</tr>
			<tr>
				<td><b> Οδός: </b></td>
				<td><input readonly="readonly" id="street" name="street" type="text" size="40" value="<?php print $street; ?>"/></td>
			</tr>
			<tr>
				<td><b> Αριθμός/Κτίριο: </b></td>
				<td><input readonly="readonly" id="building" name="building" type="text" size="40" value="<?php print $building; ?>" /></td>
			</tr>
			<tr>
				<td><b> Ταχυδρομικός Κώδικας: </b></td>
				<td><input readonly="readonly" id="addres2" name="zip" type="text" size="5" maxlength="5" value="<?php print $zip; ?>"/></td>
			</tr>
			
			<tr>
				<td><b> τηλέφωνo 1: </b></td>
				<td><input readonly="readonly" value="<?php print $tel1; ?>" name="tel1" type="text" size="14" maxlength="14" title="έως 14 ψηφία!" /></td>
			</tr>
			<tr>
				<td><b> τηλέφωνo 2: </b></td>
				<td><input readonly="readonly" value="<?php print $tel2; ?>" name="tel2" type="text" size="14" maxlength="14" title="έως 14 ψηφία!" /></td>
			</tr>
			<tr>
				<td><b> κινητό: </b></td>
				<td><input readonly="readonly" value="<?php print $tel3; ?>" name="tel3" type="text" size="14" maxlength="14" title="έως 14 ψηφία!" /></td>
			</tr>
			<tr>
				<td><b> τηλεομοιοτυπία (φαξ): </b></td>
				<td><input readonly="readonly" value="<?php print $telefax; ?>" name="telefax" type="text" size="14" maxlength="14" title="έως 14 ψηφία!" /></td>
			</tr>
			<tr>
				<td><b>ηλ. αλληλογραφία(1): </b></td>
				<td><input readonly="readonly" value="<?php print $email1; ?>" name="email1" type="text" size="40" maxlength="50" /></td>
			</tr>
			<tr>
				<td><b>ηλ. αλληλογραφία(2): </b></td>
				<td><input readonly="readonly" value="<?php print $email2; ?>" name="email2" type="text" size="40" maxlength="50" /></td>
			</tr>

			<tr valign="bottom">
			<td>
				<b>Κατηγορίες </b>
			</td>
			<td id="company" colspan="2">
				<select style="width:160px" name="occupation1"><?php echo $occupationInList[0] ?></select>
				<select style="width:160px" name="occupation2"><?php echo $occupationInList[1] ?></select>
				<select style="width:160px" name="occupation3"><?php echo $occupationInList[2] ?></select>
			</td>
			<td>
				<sup> Aλλο που δε βρίσκεται στη λίστα </sup>
				<br />
				<input tabindex="23" value="" name="extrActivity" type="text" size="40" maxlength="50" />
			</td>
			</tr>
			<tr>
				<td><b><p> ΠΕΡΙΟΧΗ </p></b></td>
				<td colspan="3">
					<div id="myArea"><?php include_once('modules/inGeoAreaNames.php');	?></div>
				</td>
			</tr>

<!-- ws edw -->
			<?php 
			}
			elseif($enable==2)
			{
			?>
				<tr><td>
			<?php
				print $checkMail;
			?>
			</td></tr>
<tr><td colspan="4">
<br /><br /><hr /><br />
			<?php
			}
			?>			


			<?php
			if($enable==2)
			{		
				include('modules/partContact.php');
			}
			?>
</td></tr>
			</table>
			</form>
			</div>
			
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