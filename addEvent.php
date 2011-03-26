<?php
	require 'lib/common.php';
	
	$fp = fopen('debug.log', 'a');
	$log = print_r($_REQUEST, true) . "\n";
	
	if ($_REQUEST['TranscriptionStatus'] == 'completed') {
		$sText = $_REQUEST['TranscriptionStatus'];
		$sFromNumber = $_REQUEST['From'];
	} else if ($_REQUEST['Body']) {
		$sText = $_REQUEST['Body'];
		$sFromNumber = $_REQUEST['From'];
	} else {
		// @todo add some failure logic
	}
	
	try {
		if ($aAccount = getAccount($sFromNumber)) {
			$oGoogleClient = getGoogleClient($aAccount['email'], $aAccount['pass']);
			createQuickAddEvent($oGoogleClient, $sText);
		} else {
			// this person doesn't exist in the system, we should probably message that to them in some way
		}
	} catch (Exception $e) {
		// @todo add some failure logic
		$log .= $e->getMessage() . "\n";
	}
	
	fwrite($fp, $log, strlen($log));
	fclose($fp);
?>