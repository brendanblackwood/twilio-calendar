<?php
	require 'common.php';
	
	$fp = fopen('debug.log', 'a');
	$log = print_r($_REQUEST, true) . "\n";
	
	if ($_REQUEST['TranscriptionStatus'] == 'completed') {
		$sText = $_REQUEST['TranscriptionStatus'];
	} else if ($_REQUEST['Body']) {
		$sText = $_REQUEST['Body'];
	} else {
		// @todo add some failure logic
	}
	
	try {
		createQuickAddEvent($oGoogleClient, $sText);
	} catch (Exception $e) {
		// @todo add some failure logic
		$log .= $e->getMessage() . "\n";
	}
	
	fwrite($fp, $log, strlen($log));
	fclose($fp);
?>