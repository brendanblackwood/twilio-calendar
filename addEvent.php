<?php
	
	$fp = fopen('debug.log', 'a');
	$log = print_r($_REQUEST, true) . "\n";
	
	set_include_path(get_include_path() . PATH_SEPARATOR . 'lib');
	
	// Pull in Twilio PHP library
	require 'Twilio/twilio.php';
	
	// Pull in Zend PHP Gdata client library
	require 'Zend/Loader.php';
	Zend_Loader::loadClass('Zend_Gdata');
	Zend_Loader::loadClass('Zend_Gdata_AuthSub');
	Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
	Zend_Loader::loadClass('Zend_Gdata_HttpClient');
	Zend_Loader::loadClass('Zend_Gdata_Calendar');
	
	// Get configs
	$aConfig = parse_ini_file('calendar.ini');
	
	$oGoogleClient = Zend_Gdata_ClientLogin::getHttpClient($aConfig['google']['user'], $aConfig['google']['pass'], Zend_Gdata_Calendar::AUTH_SERVICE_NAME);
	
	// Twilio REST API version
	$sApiVersion = "2010-04-01";
	
	// Set our AccountSid and AuthToken
	$sAccountSid = $aConfig['twilio']['sid'];
	$sAuthToken = $aConfig['twilio']['authtoken'];
	
	// Instantiate a new Twilio Rest Client
	$oTwilioClient = new TwilioRestClient($sAccountSid, $sAuthToken);
	
	$aPeople = array(
		'8314192996' => "Brendan Blackwood",
	);
	//$_REQUEST['TranscriptionText'] = "dinner at raymond's at 8pm tonight";
	// $fp = fopen('debug.log', 'a');
	// $log = var_dump($_REQUEST);
	// fwrite($fp, $log, strlen($log));
	
	if ($_REQUEST['TranscriptionStatus'] == 'completed') {
		try {
			createQuickAddEvent($oGoogleClient, $_REQUEST['TranscriptionText']);
		} catch (Exception $e) {
			// something went wrong, we should probably do something about it
			$log .= $e->getMessage() . "\n";
		}
	}
	
	fwrite($fp, $log, strlen($log));
	fclose($fp);
	
	// Stolen from Google's API documentation 
	// http://code.google.com/apis/calendar/data/1.0/developers_guide_php.html#AuthClientLogin
	function createQuickAddEvent ($client, $quickAddText) {
		$gdataCal = new Zend_Gdata_Calendar($client);
		$event = $gdataCal->newEventEntry();
		$event->content = $gdataCal->newContent($quickAddText);
		$event->quickAdd = $gdataCal->newQuickAdd('true');
		$newEvent = $gdataCal->insertEvent($event);
	}
?>