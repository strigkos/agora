<?php
	// CHECK LOG
	include_once('admin/settings1.php');
	include_once('modules/modValidity.php');
	include_once('modules/modParameters.php');
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="keywords" content="βελτιστοποιηση στις μηχανες αναζητησης, αγορα υπηρεσιων, διαδικτυακή διαφήμιση" />
	<meta name="description" content=" Ηλεκτρονική αγορά υπηρεσιών!" />
	<?php
		//include_once('modules/jsGMapsAPI.php');
		include_once('interface/jQueryUI.php');
	?>
	<script type="text/javascript" src="<?php echo $siteName; ?>interface/js/jquery.ui.datepicker.js"></script>
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
		<h1 class="inner">Πληρωμές</h1>
		<h2 class="inner">Ηλεκτρονικές πληρωμές</h2>
		<table>
			<tr>
				<tr>
					<td>
					<strong>Μπείτε στον κόσμο του ηλεκτρονικού εμπορίου δημιουργώντας [ΔΩΡΕΑΝ] ένα λογαριασμό PayPal!<br /><br /></strong>
					<!-- Begin PayPal Logo --><A HREF="https://www.paypal.com/gr/mrb/pal=WQARJVT6LG2F4" target="_blank"><IMG  SRC="http://images.paypal.com/en_US/i/bnr/paypal_mrb_banner.gif" BORDER="0" ALT="Sign up for PayPal and start accepting credit card payments instantly."></A><!-- End PayPal Logo --></td>
					<!-- <td><img src="<? echo $siteName; ?>payments/paysafecard-logo.gif" /></td> -->
					<!-- <td><img src="<? echo $siteName; ?>payments/paypal-logo.gif" /></td> -->
				</tr>
				<tr>
					<td colspan="3" style="border-top:1px grey dashed"><h3>Καταχωρήσεις</h3></td>
				</tr>
				<tr>
					<td>
						<p>
							<em>Η αγορά μπορεί να διεκπεραιωθεί ακόμα και αν δεν έχετε λογαριασμό στο PayPal, 
							<br />με χρήση της κάρτας σας πιστωτικής ή <b>προπληρωμένης</b>. 
							<br />Το PayPal είναι το πιο διαδεδομένο και ασφαλές σύστημα ηλεκτρονικών πληρωμών.</em>
						</p>
						<?php
							// fregg
							include('payments/btnBuyServices.php');
						?>
					</td>
					<td>
					</td>
					<td>
					</td>
				</tr>
				<tr>
					<td colspan="3" style="border-top:1px grey dashed"><h3>Διαφήμιση</h3></td>
				</tr>
				<tr>
					<td>
						<p>
						<strong> Με 1 € </strong><em>βάζετε τη διαφήμισή σας στην αρχική μας σελίδα για ένα μήνα.
						<br />Πληρώνετε γρήγορα και με ασφάλεια μέσω PayPal και μας στέλντε τη απόδειξη πληρωμής
						<br />μαζί με μία φτογραφία και υποδείξεις για την καταχώρηση της διαφήμισής σας.</em>
					</p>
					<?php
						// mike
						include('payments/btnAdvertize.php');
					?>
					</td>
					<td>
					</td>
					<td>
					</td>
				</tr>
				<tr>
					<td colspan="3" style="border-top:1px grey dashed"><h3>Χορηγίες</h3></td>
				</tr>
				<tr>
					<td>
						<p>
							<em>Κάνετε απλά μία χορηγία στους Χάρτες Αγοράς!</em>
						</p>
						<?php
							// mike
							include('payments/btnDonate.php');
						?>
					</td>
					<td>
					</td>
					<td>
					</td>
				</tr>
			</table>
			<hr />
			<h2 class="inner">Πληρωμή σε τράπεζα</h2>
			<p align="justify">Η πληρωμή μπορεί να γίνει μέσω τραπέζης με κατάθεση στους παρακάτω λογαριασμούς
				<ul>					
					<li>ΕΘΝΙΚΗ ΤΡΑΠΕΖΑ: GR16 0110 8630 0000 8636 0090 426</li>
					<!--
					<li>ΤΡΑΠΕΖΑ ΠΕΙΡΑΙΩΣ</li>
					<li>ΕΜΠΟΡΙΚΗ ΤΡΑΠΕΖΑ</li>
					-->
				</ul>
			</p>
			<br />
		</div>
	<div class="clear"></div>
	</div>
	<div class="clear"></div>
	</div>
	<div class="clear"></div>
	<?php 
		include_once('modules/partBottomMenu.php');
	?>
</div>

</body>
</html>