<?php
	
	$fp = fopen('debug.log', 'a');
	$log = print_r($_Request, true) . "\n";
	
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
	
	$user = 'twilio.calendar@gmail.com';
	$pass = 'twiliopassword';
	
	$googleClient = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, Zend_Gdata_Calendar::AUTH_SERVICE_NAME);
	
	function createQuickAddEvent ($client, $quickAddText) {
	  $gdataCal = new Zend_Gdata_Calendar($client);
	  $event = $gdataCal->newEventEntry();
	  $event->content = $gdataCal->newContent($quickAddText);
	  $event->quickAdd = $gdataCal->newQuickAdd('true');
	  $newEvent = $gdataCal->insertEvent($event);
	}
	
	// Twilio REST API version
	$ApiVersion = "2010-04-01";
	
	// Set our AccountSid and AuthToken
	$AccountSid = "AC69f6fa7be2dfe44bd025ec500998793b";
	$AuthToken = "59ca7697be0c9a94d39a305728785f85";
	
	// Instantiate a new Twilio Rest Client
	$twilioClient = new TwilioRestClient($AccountSid, $AuthToken);
	
	$people = array(
		'8314192996' => "Brendan Blackwood",
	);
	//$_REQUEST['TranscriptionText'] = "dinner at raymond's at 8pm tonight";
	// $fp = fopen('debug.log', 'a');
	// $log = var_dump($_REQUEST);
	// fwrite($fp, $log, strlen($log));
	
	if ($_REQUEST['TranscriptionStatus'] == 'completed') {
		try {
			createQuickAddEvent($googleClient, $_REQUEST['TranscriptionText']);
		} catch (Exception $e) {
			// something went wrong, we should probably do something about it
			log .= $e->getMessage() . "\n";
		}
	}
	
	fwrite($fp, $log, strlen($log));
	fclose($fp);
?>