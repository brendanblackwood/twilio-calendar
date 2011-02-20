<?php
	
	// Pull in Twilio PHP library
	require '../twilio/twilio.php';
	
	// Pull in Zend PHP Gdata client library
	set_include_path(get_include_path() . PATH_SEPARATOR . '/Users/elderz/Documents/dev/twilio/gdata/library');
	require 'Zend/Loader.php';
	Zend_Loader::loadClass('Zend_Gdata');
	Zend_Loader::loadClass('Zend_Gdata_AuthSub');
	Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
	Zend_Loader::loadClass('Zend_Gdata_HttpClient');
	Zend_Loader::loadClass('Zend_Gdata_Calendar');
	
	$user = 'elderz@gmail.com';
	$pass = 'livius';
	
	$googleClient = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, Zend_Gdata_Calendar::AUTH_SERVICE_NAME);
	
	createQuickAddEvent($client, "Dinner at Joe's on Thursday at 8 PM");
	
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
		'+18314192996' => "Brendan Blackwood",
	);
?>