<?php
	/*
		Σ : 24/12/2015
		Just submit via email by visitor
		Θα μεταφερθεί σε Contact Form του WordPress
		Σελίδα Υποβολής Εκδήλωσης μέσω Ηλ. Ταχυδρομείου
		page : p12
	*/

	ob_start();
	@session_start();
	require_once($_ENV['charts_path'] . 'admin/settings1.php');
	include_once($schema['abs_dir'] . 'modules/modParameters.php');
	include_once('modules/modValidity.php');
	require_once('modules/modFunctions.php');
	include_once($schema['abs_dir'] . 'modules/modSetLanguage.php');
	
	// warning
	$feedStyle['warn'] = 'ui-state-highlight ui-corner-all';
	// error
	$feedStyle['error'] = 'ui-state-error ui-corner-all';
	$feedBackStyle = '';

	$msg0 = msgA('p1101', $uLanguage, $db);
	$subject = '';
	$context = '';
	$availMsg = '';
	$successLevel = 0;

	// temporarily without login or reCaptcha
	$enable = 1;
	$disable = '';

	/* DECLARE VARIABLES and SET VALUES - ΑΡΧΙΚΟΠΟΙΗΣΗ */
	$newsList = (isset($_POST['newsList']) && $_POST['newsList']=='on') ? (1) : (0);
	$proID = -1;
	$companyID = -1;
	$checking = 'biznes' . rand();
	$alias = '';
	$proName = $proNameLatin = '';
	$lastName = '';
	$description = '';
	$rank = 2;
	$offTitle = $offTitleLatin = '';
	$offerComments = $offerCommentLatin = '';
	$start = '2015-12-01';
	$end = '';
	$lati = '';
	$longi = '';
	$zip = '';
	$urlSite = '';
	$urlFB = '';
	$kostos = 0;
	$street = ''; 
	$building = ''; 
	$tel1 = '';
	$contactPerson = '';

	//  bizProfessional
	if(isset($_POST['checking'])) { $checking = stripper($_POST['checking']); }
	if(isset($_POST['alias'])) { $alias = stripper($_POST['alias']); }
	if(isset($_POST['proName'])) { 	$proName = stripper($_POST['proName']); }
	if(isset($_POST['proNameLatin'])) { $proNameLatin = stripper($_POST['proNameLatin']); }
	if(isset($_POST['lastName'])) { $lastName = stripper($_POST['lastName']); }
	if(isset($_POST['description'])) { $description = stripper($_POST['description']); }

	// bizOffer
	if(isset($_POST['offerComments'])) { $offerComments = stripper($_POST['offerComments']); }
	if(isset($_POST['offerCommentLatin'])) { $offerCommentLatin = stripper($_POST['offerCommentLatin']); }
	if(isset($_POST['offTitle'])) { $offTitle = stripper($_POST['offTitle']); }
	if(isset($_POST['offTitleLatin'])) { $offTitleLatin = stripper($_POST['offTitleLatin']); }
	if(isset($_POST['start'])) { $start = $_POST['start']; }
	if(isset($_POST['end'])) { $end = $_POST['end']; }
	
	if(isset($_POST['urlSite'])) { $urlSite = stripper($_POST['urlSite']); }
	if(isset($_POST['urlFB'])) { $urlFB = stripper($_POST['urlFB']); }
	//////////////////////////////////////////////////////////////////
	//Get the uploaded file information
	//print_r ($_FILES['emvlima']['name']);
	if(isset($_FILES['emvlima'])) {
		$uFileName = $_FILES['emvlima']['name'];
		$uFileName = basename($uFileName);

		// Get the file extension of the file
		$uFileType = substr($uFileName, strrpos($uFileName, '.') + 1);
		
		// Check file-size
		$uFileSize = $_FILES['emvlima']['size'];
		$uFileSize = $uFileSize/1024; //size in KBs
		if ($uFileSize > 1024) { 
			// Πολύ μεγάλο αρχείο
		}
		else {
			//copy the temp uploaded file to uploads folder
			$uFilePath = $schema['abs_dir'] . 'customers/' . $uFileName;
			$tmp_path = $_FILES['emvlima']['tmp_name'];
		}

		if(is_uploaded_file($tmp_path)) {
			if(!copy($tmp_path, $uFilePath)) {
				$errors .= '\n error while copying the uploaded file';
			}
		}
	}
	/////////////////////////////////////////////////////////////////////////////
	
	if(isset($_POST['kostos'])) { $kostos = keepOnlyNumbers($_POST['kostos']); }
	
	// bizCompany
	if(isset($_POST['companyID'])) { $companyID = keepOnlyNumbers($_POST['companyID']); }
	if(isset($_POST['lati'])) { $lati = stripper($_POST['lati']); }
	if(isset($_POST['longi'])) { $longi = stripper($_POST['longi']); }
	if(isset($_POST['zip'])) { $zip = stripper($_POST['zip']); }
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
	$street = str_replace(' , Ελλάδα', '', $street);
	$street = str_replace($zip, '', $street);
	if(isset($_POST['building'])) { $building = stripper($_POST['building']); }
	if(isset($_POST['tel1'])) { $tel1 = keepOnlyNumbers($_POST['tel1']); }
	if(isset($_POST['contactPerson'])) { $contactPerson = $_POST['contactPerson']; }

	// FIND companyID
	$proSelect = " SELECT id, alias FROM bizProfessional  WHERE alias='" . $checking . "'";
	$findPro = @mysql_query($proSelect, $db);
	$proDetails = mysql_fetch_array($findPro);
	if($proDetails[0]>0) {
		$enable = 0;
		$disable = 'disabled="disabled"';
		$availImg = 'images/notavail.png';
		$availMsg = 'δεν είναι διαθέσιμο'; 
		$alias='';
	}

	/* alias not exists */
	else {
		$enable=1;
		$disable='';
		$availImg = 'images/available.png';
		$availMsg = 'είναι διαθέσιμο';
		$alias=$checking;
	}

	// TRY INSERT OR UPDATE
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["submit"]) && $enable==1 && $disable=='') {
		$context .= "\r\n";
		$context .= "\r\n";

		if(!isset($_POST['offTitle'])) {
			$successLevel = -1;
			$feedBackStyle = $feedStyle['error'];
			$msg1 = msgA('p1101', $uLanguage, $db);
		}
	
		if(empty($urlSite) && empty($urlFB) && empty($tmp_path)) {
			$successLevel = -1;
			$feedBackStyle = $feedStyle['error'];
			$msg1 = msgA('p1106', $uLanguage, $db);
		}

		if($successLevel<0) {
			$msg0 = ' Δεν έχετε εισάγει τα απαραίτητα στοιχεία! ';
			$feedBackStyle = $feedStyle['error'];
		}
		else {
			$context .= ' ΣΤΟΙΧΕΙΑ ΓΙΑ ΤΗΝ ΕΚΔΗΛΩΣΗ / EVENT ';
			$msg0 = '';
			$successLevel = 1;
			/* OFFER */
			if($offTitle != '' && $start != '' && $end != '') {
				$context .= "\r\n" ;
				$context .= 'Τίτλος : ';
				$context .=  $offTitle . ' - ' . $offTitleLatin;
				$context .= "\r\n" ;
				$context .=  'Περιγραφή : ' . $offerComments . ' - ' . $offerCommentLatin;
				$context .= "\r\n" ;
				$context .= $urlSite . ' - ' . $urlFB ;
				$context .= "\r\n" ;
				$context .= 'από : ' . $start . ' - έως : ' . $end;
				$context .= "\r\n" ;
				$context .=  'Τιμή συμμετοχής : ' . $kostos . ' €';
				//$successLevel = 2;
			}
			else {
				$msg0 .= " Δε δόθηκε τίτλος ή ημερομηνία! ";
				$feedBackStyle = $feedStyle['warn'];
				//$successLevel = 2;
			}
			
			/* ΟΝΟΜΑ ΦΟΡΕΑ */
			if(empty($proName)) {
				$proName = 'Χωρίς φορέα';
			}
			if(empty($proNameLatin)) {
				$proNameLatin = 'NO club';
			}

			/* Company */ 
			if(!empty($street)) {
				$context .= "\r\n";
				$context .= "\r\n" ;
				$context .= ' ΤΟΠΟΘΕΣΙΑ ';
				$context .= "\r\n" ;
				$context .= $street . ', ' . $building . ', ' . $zip;
				$context .= "\r\n" ;
				$context .= 'Συντεταγμένες : ' . $lati . ' - ' . $longi;
				$context .= "\r\n";
				$context .= 'Επικοινωνία : ' . $tel1 . ', ' . $contactPerson;
				$context .= "\r\n";
				$context .= "\r\n";
				$context .= 'ΦΟΡΕΙΣ : ' . $proName . ', ' . $proNameLatin;
				$successLevel = 3;
			}
			else {
				$feedBackStyle = $feedStyle['error'];
				$msg0 .= ' Δεν δόθηκε τοποθεσία! ';
				$successLevel = -3;
			}

			/* SEND MAIL */
			if ($successLevel >= 3) {
				/*$schema['mail-biz'] = 'strigkos@gmail.com';*/
				$mSubject = 'Αίτημα Καταχώρησης Εκδήλωσης';
				include('modules/modSendMail.php'); 
			}
			else {
			}
		}
	}

	elseif(isset($_POST["reset"])) {
		header('Location: ' . $_SERVER['PHP_SELF']);
	}

	// JUST SHOW ALL DATA
	if($checking!='' && $successLevel > 10) {
		
		/* Offer */
		if($successLevel>=2) {		
			$getOffer = @mysql_query(" SELECT description, start, end FROM bizOffer WHERE proAlias = '$checking' ", $db);
			$offerDetails = mysql_fetch_array($getOffer);
			$offerComments = $offerDetails[0];
			$start = $offerDetails[1];
			$end = $offerDetails[2];
		}
		
		/* Professional */
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
	}
	switch ($uLanguage) {
		case 'el':
			$metaTitle = 'Ανακοίνωση εκδήλωσης' . ' - ' .  $schema['metaTitle'];
			$metaDesc = 'Ανακοίνωση εκδήλωσης' . ' - ' .  $schema['metaDesc'];
			break;
		case 'en':
			$metaTitle = 'Event announcement' . ' - ' .  $schema['metaTitle'];
			$metaDesc = 'Event announcement' . ' - ' .  $schema['metaDesc'];
			break;
		default:
			$metaTitle = msgA('p12Title', $uLanguage, $db) . ' - ' . $schema['metaTitle'] ;
			$metaDesc =  msgA('p12Desc', $uLanguage, $db) . ' - ' . $schema['metaDesc'];
	}
?>

<!DOCTYPE html>
<html lang="<?php echo $uLanguage; ?>">
<head>
	<meta charset="utf-8" />
	<meta name="description" content="<?php echo $metaDesc; ?>" />

	<?php
		include_once('modules/jsGMapsAPI.php');
		include_once('interface/jQueryUI.php');
	?>

	<script type="text/javascript" src="<?php echo $siteName; ?>interface/js/jquery.ui.datepicker.js"></script>
	<script>
		/* $.noConflict(); */
		/* $(function()  */
		jQuery(document).ready(function($) {

			$( "#fromDate" ).datepicker();
			$( "#toDate" ).datepicker();
			$( "#fromDate" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
			$( "#toDate" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
		});
	</script>

  <?php

    include('modules/modHead.php');

    if(file_exists('modules/jsTrackingCode.php') && $schema['seo']=='yes') {
		include_once('modules/jsTrackingCode.php');
	}

  ?>
  <title <?php echo $metaTitle; ?>></title>
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
	<h1 class="inner"><?php echo msg1(3, $uLanguage, $db); ?></h1>
	<?php 
		if($successLevel>0) {
	?>
	<table class="inputs">
		<tr>
			<td width="60%"><b> Η καταχώρηση σας στάλθηκε στο διαχειριστή και θα καταχωρηθεί σύντομα!</b></td>
			<td width="40%" style="border:#0000FF thin solid; background:#FFFFCC">
				<font color="#0000FF">Ευχαριστούμε!</font>
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
    else {
    ?>
    <h2 class="inner"><?php echo msgA('nEvH2', $uLanguage, $db); ?></h2>
    <table class="inputs">
		<tr class="last">
		<td class="<?php echo $feedBackStyle; ?>">
		<div style="padding:0 .35em;">
		<?php
		print $msg0;
		print '<br />';
		if(!empty($msg1)) { 
			print $msg1; 
		}
		else {
			echo msgA('p1106', $uLanguage, $db);
		}
		?>
		</div>
		</td>
		</tr>
    </table/>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
	<table class="inputs">
		<tr >
			<td colspan="4">
				<div class="ourprojectrow"></div>
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<h2 class="inner"> Πληροφορίες για την Εκδήλωση</h2>
				Οι διευθύνσεις ιστού (URLs) που θα δώσετε μπορεί να περιλαμβάνουν μια αναλυτική περιγραφή ή μια εικόνα (αφίσα κλπ)
				<br>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b><label for="offTitle" style="display:block; width:200px">Τίτλος Εκδήλωσης *</label></b>
				<input tabindex="1" placeholder="Τίτλος" name="offTitle" size="65" <?php echo $disable; ?> value="<?php print $offTitle; ?>" required title="Ο τίτλος είναι υποχρεωτικός" />
			</td>
			<td colspan="2">
				<label for="offTitleLatin" style="display:block; width:200px">Τίτλος Εκδήλωσης στα Αγγλικά</label> 
				<input tabindex="3" placeholder="Title" name="offTitleLatin" size="65" <?php echo $disable; ?> value="<?php print $offTitleLatin; ?>" />
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<b><label style="display:inline-block; width:280px">Περιγραφή Εκδήλωσης</label></b>
				<textarea tabindex="2" placeholder="Δώστε μία περιγραφή"  name="offerComments" size="480" cols="50" rows="4" <?php echo $disable; ?> ><?php print trim($offerComments); ?></textarea>
			</td>
			<td colspan="2">
				<label style="display:block; width:280px">Περιγραφή Εκδήλωσης στα Αγγλικά<label>
				<textarea tabindex="4" placeholder="Description"  name="offerCommentLatin" size="480" cols="50" rows="4" <?php echo $disable; ?> ><?php print trim($offerCommentLatin); ?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<label style="display:block; width: 200px;"><b>ΗΜΕΡΟΜΗΝΙΕΣ *</b></label>
				από <input required title="Οι ημερομηνίες είναι υποχρεωτικές" tabindex="5" placeholder="2015-12-01" min="2015-12-01" type="date" id="fromDate" name="start" size="19" maxlength="19" value="<?php print $start; ?>" <?php echo $disable; ?> />
				- έως <input required title="Οι ημερομηνίες είναι υποχρεωτικές"  tabindex="6" placeholder="2015-12-01" min="2015-12-01" type="date" id="toDate" name="end" size="19" maxlength="19" value="<?php print $end; ?>" <?php echo $disable; ?> />
			</td>
			<td colspan="2">
				<label style="display:block; width: 200px;">Κόστος Συμμετοχής</label>
				<input tabindex="10" name="kostos" type="number" maximum size="3" maxlength="3" value="<?php print $kostos; ?>" placeholder="0" min="0" max="999" /> €
			</td>
		</tr>
		<tr>
			<td colspan="1">
				<label style="display:inline-block; width: 260px;"><b>Ιστοσελίδα</b> εκδήλωσης **</label>
			</td>
			<td colspan="3">
				<input tabindex="7" placeholder="http://istoselida.gr/"  name="urlSite" type="text" size="50" value="<?php print $urlSite; ?>" <?php echo $disable; ?> />
			</td>
		</tr>
		<tr>
			<td colspan="1">
				<label style="display:inline-block; width: 260px;"><b>Facebook</b> εκδήλωσης **</label>
			</td>
			<td colspan="3">
				<input tabindex="8" placeholder="http://fb.com/event/"  name="urlFB" type="text" size="50" value="<?php print $urlFB; ?>" <?php echo $disable; ?> />
			</td>
		</tr>
		<tr>
			<td colspan="1">
				<label style="display:inline-block; width: 260px;"><b>Έντυπο Εκδήλωσης</b> ** <label>
			</td>
			<td colspan="3">
				<input type="file" style="display:inline-block;" tabindex="9" name="emvlima"  />
				<em>τύπος αρχείου : jpg, png, gif</em>
			</td>
		</tr>
		<?php
			if($enable==1 && $disable=='') { 
		?>
		<tr class="newArea"><td colspan="4"><div class="ourprojectrow"></div></td></tr>
		<tr><td colspan="4"><h2 class="inner">Στοιχεία τοποθεσίας</h2></td></tr>
		<tr>
			<td rowspan="9" colspan="2">
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
			<td>
				* <br>
				<input title="Κάνετε κλικ στο χάρτη" placeholder="Κάνετε κλικ στο χάρτη" id="lati" name="lati" type="text" readonly="readonly" size="20" value="<?php print $lati; ?>" />
			</td>
			<td>
				* <br>
				<input title="Κάνετε κλικ στο χάρτη" placeholder="Κάνετε κλικ στο χάρτη" id="longi" name="longi" type="text" readonly="readonly" size="20" value="<?php print $longi; ?>" />
			</td>
		</tr>
		<tr>
			<td><b> Οδός * </b><sub> (με κλικ στο χάρτη) </sub> </td>
			<td><input required tabindex="31" placeholder="Κάνετε κλικ στο χάρτη" title="Κάνετε κλικ στο χάρτη.." id="street" name="street" type="text" size="40" value="<?php print $street; ?>"/></td>
		</tr>

      <tr>

        <td><b> Αριθμός/Κτίριο </b></td>

        <td><input tabindex="32" placeholder="..διορθώστε αν χριάζεται με πληκτρολόγηση"  id="building" name="building" type="text" size="40" value="<?php print $building; ?>" /></td>

      </tr>

      <tr>

        <td><b> Ταχυδρομικός Κώδικας </b></td>

        <td><input tabindex="33" id="addres2" name="zip" type="text" size="5" maxlength="5" value="<?php print $zip; ?>"/></td>

      </tr>

	<tr>
		<td><b>Υπεύθυνος επικοινωνίας</b></td>

        <td><input tabindex="34" value="<?php print $contactPerson; ?>" name="contactPerson" type="text" size="40" maxlength="50" /></td>
	</tr>
	<tr>
		<td><b>τηλέφωνo</b></td>
        <td><input tabindex="35" value="<?php print $tel1; ?>" name="tel1" type="text" size="14" maxlength="14" title="έως 14 ψηφία!" /></td>
	</tr>
	<tr>
		<td><br></td>
		<td><br></td>
	</tr>
	<tr>
		<td><br></td>
		<td><br></td>
	</tr>
	<tr class="newArea">
		<td colspan="4"><div class="ourprojectrow"></div></td>
	</tr>
	<tr>
		<td colspan="4">
			<h2 class="inner">Στοιχεία Συλλόγου</h2>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<label>Φορείς εκδήλωσης</label>
			<br>
			<input tabindex="41" placeholder="Επωνυμία Συλλόγου"  name="proName" type="text" size="60" value="<?php print $proName; ?>" <?php echo $disable; ?> />
		</td>
        <td colspan="2">
			<label>Φορέας εκδήλωσης στα ΛΑΤΙΝΙΚΑ</label>
			<br>
			<input tabindex="42" placeholder="Club Name" name="proNameLatin" type="text" size="60" value="<?php print $proNameLatin; ?>" <?php echo $disable; ?> />
		</td>
	</tr>
	<tr>
        <td colspan="4" align="center">
        <input type="submit" name="submit" class="button" value="Αποστολή!" <?php echo $disable; ?> /><br /></td>
	</tr>
      <?php
      }
      else {
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
	</div>
</body>
</html>