<?php
	/*
		Σ : 20/8/2016
		Αντιγράφτηκε από το submit.php για να υλοποιηθεί η καταχώρηση Συλλόγου
		Απλά αποστέλεται e-mail στον Διαχειριστή - Just submit via email by visitor
		αλλά ίσως να μη χρειαστεί αν γίνει με Contact Form του Wordpress
		Σελίδα Υποβολής Συλλόγου μέσω Ηλ. Ταχυδρομείου : Page#11
	*/

	ob_start();
	@session_start();
	require_once(getenv('charts_path') . 'admin/settings1.php');
	include_once($schema['abs_dir'] . 'modules/modParameters.php');
	include_once('modules/modValidity.php');
	require_once('modules/modFunctions.php');
	include_once($schema['abs_dir'] . 'modules/modSetLanguage.php');
    
  $feedStyle['warn'] = 'ui-state-highlight ui-corner-all'; // warning
  $feedStyle['error'] = 'ui-state-error ui-corner-all'; // error
  $feedBackStyle = '';
	
  $msg0 = msgA('p1101', $uLanguage, $db);
  //$msg1 = 'Εάν επιλέξετε δικό σας ψευδώνυμο, ελέγξτε τη διαθεσιμότητά του!';
  $subject = '';
  $context = '';
  $availMsg = '';
  $successLevel = 0;

  // temporarily without login or recaptcha
  $enable = 1;
  $disable = '';

  // DECLARE VARIABLES and SET VALUES
  // professional
  $proID = -1;
  $companyID = -1;
  $checking = 'biznes' . rand();
  $alias = '';
  $proName = '';
  $lastName = '';
  $proNameLatin = '';
  $description = '';
  $countryCode = '0030';
  $phone = '';
  $email = '';
  $url = '';
  $fbpage = '';
  $rank = 2;

  // ΔΙΕΥΘΥΝΣΗ
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

  //  bizProfessional
  if(isset($_POST['checking'])) { $checking = stripper($_POST['checking']); }
  if(isset($_POST['alias'])) { $alias = stripper($_POST['alias']); }
  if(isset($_POST['proName'])) { $proName = stripper($_POST['proName']); }
  if(isset($_POST['lastName'])) { $lastName = stripper($_POST['lastName']); }
  if(isset($_POST['proNameLatin'])) { $proNameLatin = stripper($_POST['proNameLatin']); }
  if(isset($_POST['phone'])) { $phone = keepOnlyNumbers($_POST['phone']); }
  if(isset($_POST['email'])) { $email = isValidEMail(stripper($_POST['email'])); }
	if(isset($_POST['url'])) { $url = stripper($_POST['url']); }		if(isset($_POST['fbpage'])) { $fbpage = stripper($_POST['fbpage']); }	
	if(isset($_POST['newsList']) && $_POST['newsList']=='on') {
		$newsList = 1;	}
  else { $newsList = 0; }  
  if(isset($_POST['description'])) { $description = stripper($_POST['description']); }
  if(isset($_POST['url'])) { $url = stripper($_POST['url']); }
  ///////////////////////////////////////////////////////////////////////////////////////////////////
  //Get the uploaded file information
  //print_r ($_FILES['emvlima']['name']);
  if(isset($_FILES['emvlima'])) {
    $uFileName = $_FILES['emvlima']['name'];
    $uFileName = basename($uFileName);
    // Get the file extension of the file
    $uFileType = substr($uFileName, strrpos($uFileName, '.') + 1);
    $uFileSize = $_FILES['emvlima']['size'];
    $uFileSize = $uFileSize/1024; //size in KBs

    //copy the temp. uploaded file to uploads folder
    $uFilePath = $schema['abs_dir'] . 'customers/' . $uFileName;
    $tmp_path = $_FILES['emvlima']['tmp_name'];
    
    if(is_uploaded_file($tmp_path)) {
      if(!copy($tmp_path,$uFilePath)) {
        $errors .= '\n error while copying the uploaded file';
      }
    }
  }
  ///////////////////////////////////////////////////////////////////////////////////////////////////

  // bizCompany - orgOffice  
  if(isset($_POST['lati'])) { $lati = stripper($_POST['lati']); }
  if(isset($_POST['longi'])) { $longi = stripper($_POST['longi']); }
  if(isset($_POST['zip'])) { $zip = stripper($_POST['zip']); }
  if(isset($_POST['label'])) { $label = stripper($_POST['label']); }
  if(!empty($_POST['block'])) {
    $areaID = stripper($_POST['block']);
  }
  elseif(!empty($_POST['city'])) {
    $areaID = stripper($_POST['city']);
  }
  elseif(!empty($_POST['district'])) {
    $areaID = stripper($_POST['district']);
  }
  elseif(!empty($_POST['region'])) {
    $areaID = stripper($_POST['region']);
  }
  else {
    $areaID = 0;
  }

  if(isset($_POST['street'])) { $street = stripper($_POST['street']); }
  if(isset($_POST['building'])) { $building = stripper($_POST['building']); }
  if(isset($_POST['tel1'])) { $tel1 = keepOnlyNumbers($_POST['tel1']); }
  if(isset($_POST['tel2'])) { $tel2 = keepOnlyNumbers($_POST['tel2']); }
  if(isset($_POST['tel3'])) { $tel3 = keepOnlyNumbers($_POST['tel3']); }
  if(isset($_POST['telefax'])) { $telefax = keepOnlyNumbers($_POST['telefax']); }
  if(isset($_POST['email1'])) { $email1 = stripper($_POST['email1']); }
  
  // FIND companyID
  /*  if (empty($checking)) { $checking = 'mike'; } */
      
  $proSelect = " SELECT DISTINCT id, proAlias FROM bizCompany WHERE proAlias ='" . $checking . "' LIMIT 1";
  $findPro = @mysql_query($proSelect, $db);
  $proDetails = mysql_fetch_array($findPro);

  if($proDetails[0]>0) {
    $enable = 0;
    $disable = 'disabled="disabled"';
    $availImg = 'images/notavail.png';
    $availMsg = 'δεν είναι διαθέσιμο'; 
    $alias='';
  }
  else {
    // alias not exists 
    $enable=1;
    $disable='';
    $availImg = 'images/available.png';
    $availMsg = 'είναι διαθέσιμο';
    $alias=$checking;
  }

	// TRY INSERT OR UPDATE
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["submit"]) && $enable==1 && $disable=='') {
		$context .= "\r\n";
		if(empty($proName)) {
			$enable = 1;
			$msg0 = msgA('p1102', $uLanguage, $db);
			/* Εισάγετε όλα τα υποχρωτικά στοιχεία */
			$msg1 = ' Παρακαλώ εισάγετε την Επωνυμία του Συλλόγου! ';
			$feedBackStyle = $feedStyle['error'];
		}
		elseif(empty($phone) || strlen($phone)<10 ) {
			$enable = 1;
			$msg0 = msgA('p1102', $uLanguage, $db);
			/* Εισάγετε όλα τα υποχρωτικά στοιχεία */
			$msg1 = ' Εισάγεται έναν έγκυρο τηλεφωνικό αριθμό! ';
			$feedBackStyle = $feedStyle['error'];
		}
		elseif(strlen($url)<5 && strlen($fbpage)<5) {
			$enable = 1;
			$msg0 = msgA('p1102', $uLanguage, $db);
			$msg1 = ' Εισάγεται τουλάχιστον έναν διαδικτυακό τόπο (website ή facebook) ';
			$feedBackStyle = $feedStyle['warn'];
		}
		elseif(empty($lati) || empty($longi)) {
			$enable = 1;
			$msg0 = 'Οι συντεταγμένες είναι υποχρεωτικές ';
			$msg1 = 'Κάντε κλικ πάνω στο χάρτη στο σημείο που βρίσκονται τα γραφεία του Συλλόγου και θα περαστούν αυτόματα ';
			$feedBackStyle = $feedStyle['warn'];
		}
		else {
			$context .= $proName . ' - ' . $proNameLatin;
			$context .= "\r\n";
			$context .= $url . ' - ' . $fbpage;
			$context .= "\r\n";
			$context .= ' - ' . $description;				  
			$msg0 = ' Εισαγωγή Νέου Συλλόγου! ';
			$successLevel = 1;
			// Company
			$street = str_replace(', Ελλάδα' , '', $street);
			$street = str_replace($zip, '', $street);
			if(!empty($street)) {
				$context .= "\r\n";
				$context .= "\r\n";
				$context .= 'Διεύθυνση : ' . $street;
				$context .= "\r\n";
				if(!empty($building)) {
					$context .= "\r\n";
					$context .= ' - Κτίριο : ' . $building;
				}
				$context .= 'Τ.Κ. : ' . str_replace(' ' , '', $zip);
				$context .= "\r\n";
				$context .= 'Επικοινωνία : ' . $tel1 . ', ' . $tel2 . ', ' . $tel3 . ', ' . $email1;
				if(!empty($telefax)) { $context .= ', ' . $telefax; }
				$context .= "\r\n";
				$context .= 'Συντεταγμένες : ' . $lati . ' - ' . $longi;
				$successLevel = 3;
			}
			else {
				$msg0 .= ' Δεν δόθηκε έδρα! ';
				$successLevel = -3;
			}
			$context .= "\r\n";
			$context .= "\r\n";
			$context .= 'Εκπρόσωπος / Διαχειριστής : ' . $lastName . ', ' . $phone . ', ' . $email;
			if ($newsList==1) { $context .= "\r\n + Προθήκη στη λίστα ενημερώσεων "; }
			// SEND eMAIL
			if ($successLevel >= 3) {
				/* $schema['mail-biz'] = 'strigkos@gmail.com'; */
				$mSubject = 'Αίτημα Καταχώρησης Συλλόγου';
				include('modules/modSendMail.php');
			}
			else {
				$msg1 = 'ΔΕΝ ΣΤΑΛΘΗΚΕ Η ΚΑΤΑΧΩΡΗΣΗ!';
				$feedBackStyle = $feedStyle['error'];
			}
		}
	}
	elseif(isset($_POST["reset"])) {
		header('Location: ' . $_SERVER['PHP_SELF']);
	}
  
  // JUST SHOW ALL DATA
  if($checking!='' && $successLevel > 0) {
    // Professional
    $getPro = @mysql_query(" SELECT * FROM bizProfessional WHERE alias = '$checking' ", $db);
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

    // Company
    if($successLevel>=3 || $companyID>0) {
      $getCompany =  @mysql_query(" SELECT * FROM bizCompany WHERE id = '$companyID' ", $db);
      $companyDetails = mysql_fetch_array($getCompany);
      $companyID = $companyDetails[0];
      $lati = $companyDetails[2];
      $longi = $companyDetails[3];
      $zip = $companyDetails[4];
      $label = $companyDetails[5];
      $discription = $companyDetails[6];
      $areaID = $companyDetails[7];
      if($areaID>0) {
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
    if($successLevel>=4 && $companyID>0) {
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
  $r = 2; // παράμετρος για map research
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="description" content="Καταχωρήστε ένα Σύλλογο στο χάρτη!" />
  <meta name="description" content="Καταχωρήστε ένα Σύλλογο στο χάρτη!" />
	<?php
		include_once('modules/jsGMapsAPI.php');
		include_once('interface/jQueryUI.php');
	?>
	<script type="text/javascript" src="<?php echo $schema['charts']; ?>interface/js/jquery.ui.datepicker.js"></script>
	<?php
		include('modules/modHead.php');
		$schema['seo'] = 'no';
		if(file_exists('modules/jsTrackingCode.php')) {
			include_once('modules/jsTrackingCode.php');
		}
	?>
  <!-- <title></title> -->
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

    <h1 class="inner"><?php echo msg1(1, $uLanguage, $db); ?></h1>
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
	<table class="inputs">
		<tr class="last">
			<td class="<?php echo $feedBackStyle; ?>">
				<div style="padding: 0 .5em;">
				<?php
					print $msg0;
					print '<br />';
					if(!empty($msg1)) { print $msg1; }
				?>
				</div>
			</td>
		</tr>
    </table/>
	<br>
    <h2 class="inner"><?php print msgA('p1103', $uLanguage, $db); ?></h2>
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
	<table class="inputs">
		<tr>
			<td><b>Ον/μο υπεύθυνου<br> επικοινωνίας *</b></td>
			<td><input tabindex="1" name="lastName" type="text" size="40" value="<?php print $lastName; ?>" <?php echo $disable; ?> placeholder="Νίκος Παππάς" title="Εισάγετε ένα ονομ/επώνυμο" required /></td>
			<td><b><?php print msgA('lblPhone', $uLanguage, $db); ?> * </b></td>
			<td>
			  <input tabindex="2" placeholder="0030" readonly="readonly" name="countryCode" value="<?php print $countryCode; ?>" <?php echo $disable; ?> type="label" size="4" maxlength="4" />
			  <input tabindex="3" name="phone" value="<?php print $phone; ?>" <?php echo $disable; ?> type="text" size="10" maxlength="10" title="Εισάγετε 10 ψηφία!" required />
			</td>
		</tr>
		<tr>
			<td><b>Ηλ. αλληλογραφία</b><br>e-mail</td>
			<td><input tabindex="8" placeholder="info@ellhnika.gr" name="email" type="text" size="40" value="<?php print $email; ?>" <?php echo $disable; ?> /></td>
			<td><input tabindex="9" name="newsList" type="checkbox" checked value="on" <?php echo $disable; ?> />λίστα ενημερώσεων</td>
			<td></td>
		</tr>
		<tr class="newArea"><td colspan="4"><div class="ourprojectrow"></div></td></tr>
		<tr>
			<td colspan="4">
				<h2 class="inner"><?php print msgA('p1104', $uLanguage, $db); ?></h2>
			</td>
		</tr>
		<tr>
		<td><b>Επωνυμία<br />Συλλόγου *</b></td>
		<td><input tabindex="1" placeholder="Επωνυμία Συλλόγου" name="proName" type="text" size="40" value="<?php print $proName; ?>" <?php echo $disable; ?> required /></td>
		<td><b>Επωνυμία Συλλόγου<br />στα ΛΑΤΙΝΙΚΑ</b></td>
		<td><input tabindex="2" placeholder="Club Name" name="proNameLatin" type="text" size="40" value="<?php print $proNameLatin; ?>" <?php echo $disable; ?> /></td>
      </tr>
	  
      <tr>
        <td><b>Ιστοσελίδα</b></td>        
        <td><input tabindex="6" name="url" type="url" size="40" value="<?php print $url; ?>" <?php echo $disable; ?> placeholder="www"/></td>
        <td><b>Facebook</b> ή άλλο μέσο κοινωνικής δικτύωσης</td>        
        <td><input tabindex="7" name="fbpage" type="url" size="40" value="<?php print $fbpage; ?>" <?php echo $disable; ?> placeholder="fb, G+, ..."/></td>
      </tr>

      <tr>
        <td><b>Έμβλημα Συλλόγου</b></td>
        <td><input tabindex="2" name="emvlima" type="file" /></td>
        <td><em>τύπος αρχείου : jpg, png, gif</em></td>
      </tr>

      <tr>
        <td colspan="1" style="vertical-align: top"><b>Λίγα λόγια για το Σύλλογο</b></td>
        <td colspan="3">
          <textarea tabindex="10" placeholder="Λίγα λόγια για το Σύλλογο" name="description" size="480" cols="80" rows="4" <?php echo $disable; ?> ><?php print $description; ?></textarea>
        </td>
      </tr>

      <tr class="last">
        <td></td>
        <td><input tabindex="" name="firstName" type="hidden" size="40" value="" <?php echo $disable; ?> /></td>
      </tr>
      <?php
      if($enable==1 && $disable=='')
      {
      ?>
      <tr class="newArea"><td colspan="4"><div class="ourprojectrow"></div></td></tr>
      <tr><td colspan="4"><h2 class="inner"><?php print msgA('p1105', $uLanguage, $db); ?></h2></td></tr>
      <tr>
        <td rowspan="10" colspan="2">
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
        <td colspan="2">
        <b> Επιλέξτε το σημείο στο χάρτη και τα στοιχεία θα μεταφερθούν αυτόματα</b>
        <br>...ή μπορείτε να πληκτρολογήσετε τη διεύθυνση στα παρακάτω πεδία
        </td>
      </tr>
      <tr>
        <td><input required placeholder="Κάνετε κλικ στο χάρτη" id="lati" name="lati" type="text" readonly="readonly" size="20" value="<?php print $lati; ?>" > * </td>
        <td><input required placeholder="Κάνετε κλικ στο χάρτη" id="longi" name="longi" type="text" readonly="readonly" size="20" value="<?php print $longi; ?>" > * </td>
      </tr>
      <tr>
        <td><b> Οδός, αριθμός * </b><br>με κλικ στο χάρτη</td>
        <td><input tabindex="14" placeholder="Κάνετε κλικ στο χάρτη" title="Κάνετε κλικ στο χάρτη.." id="street" name="street" type="text" size="40" value="<?php print $street; ?>" required /></td>
      </tr>
      <tr>
        <td><b> Κτίριο \ Όροφος</b><br> </td>
        <td><input tabindex="15" placeholder="..συμπληρώστε πληροφορίες αν χρειάζεται"  id="building" name="building" type="text" size="40" value="<?php print $building; ?>" /></td>
      </tr>
      <tr>
        <td><b> Ταχυδρομικός Κώδικας</b></td>
        <td><input tabindex="16" id="addres2" name="zip" type="text" size="6" maxlength="6" value="<?php print $zip; ?>"/></td>
      </tr>
            
      <tr>
        <td><b>Τηλέφωνo </b>σταθερό<br> </td>
        <td><input tabindex="17" value="<?php print $tel1; ?>" name="tel1" type="text" size="14" maxlength="14" title="έως 14 ψηφία!" /></td>
      </tr>
      <tr>
        <td><b>2<sup>ο</sup> Τηλέφωνo</b><br> </td>
        <td><input tabindex="18" value="<?php print $tel2; ?>" name="tel2" type="text" size="14" maxlength="14" title="έως 14 ψηφία!" /></td>
      </tr>
		<tr>
			<td><b>Κινητό </b>τηλέφωνο<br> </td>
			<td><input tabindex="19" value="<?php print $tel3; ?>" name="tel3" type="text" size="14" maxlength="14" title="έως 14 ψηφία!" /></td>
		</tr>
		<tr>
			<td><b>Ηλ. αλληλογραφία</b><br>e-mail έδρας</td>
			<td><input tabindex="21" value="<?php print $email1; ?>" name="email1" type="text" size="40" maxlength="50" /></td>
		</tr>
      <tr>
        <td><b> τηλεομοιοτυπία </b><br>fax</td>
        <td><input tabindex="20" value="<?php print $telefax; ?>" name="telefax" type="text" size="14" maxlength="14" title="έως 14 ψηφία!" /></td>
      </tr>
		
		<tr class="newArea"><td colspan="4">
			<!-- <div class="ourprojectrow"></div> -->
			<br>
		</td></tr>

      <tr>
        <td colspan="4" align="center">
			<input type="submit" name="submit" class="button" value="Αποστολή!" <?php echo $disable; ?> />
			<br />
			<br>
		</td>
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
		/* include_once('modules/partBottomMenu.php'); */
	?>
</div>
</body>
</html>