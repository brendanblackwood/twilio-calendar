<?php
	
	require '../lib/common.php';
	
	$bAjax = isset($_SERVER["HTTP_X_REQUESTED_WITH"]) ? $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest" : false;
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $bAjax) {
		// Format the phone number in the form +1XXXXXXXXXX of +0XXXXXXXXXX
		// I'm not sure if the collect call format +0 ever actually comes up, but might as well have it
		$_REQUEST['phone'] = str_replace(array('+','-','(',')','.','-'), '', $_REQUEST['phone']);
		if (substr($_REQUEST['phone'], 0, 1) == '0' || substr($_REQUEST['phone'], 0, 1) == '1') {
			$_REQUEST['phone'] = '+' . $_REQUEST['phone'];
		} else {
			$_REQUEST['phone'] = '+1' . $_REQUEST['phone'];
		}

		$aErrors = validateInput($_REQUEST);

		// If there were no errors, connect to mysql and insert the data
		if (empty($aErrors)) {
			mysql_connect($aConfig['mysql']['host'], $aConfig['mysql']['user'], $aConfig['mysql']['pass']);
			
			// Make sure we are using the correct database
			$sDb = $aConfig['mysql']['db_name'];
			mysql_query("use $sDb");
			
			$sQuery = sprintf("INSERT INTO account (phone, email, pass, name) VALUES ('%s', '%s', '%s', '%s')",
				mysql_real_escape_string($_REQUEST['phone']),
				mysql_real_escape_string($_REQUEST['email']),
				mysql_real_escape_string($_REQUEST['password']),
				mysql_real_escape_string($_REQUEST['name']));
			$bResult = mysql_query($sQuery);
			
			if (!$bResult) {
				$sStatus = 'error';
				if (strpos(mysql_error(), 'Duplicate entry') !== false) {
					$aMessages['mysql'] = "That phone number is already in use. Do you already have an account?";
				} else {
					$aMessages['mysql'] = "The deebees aren't doing what they should. You should go do something else and try again later.";
				}
			} else {
				$sStatus = 'success';
				$aMessages = array('success' => 'Hooray, It worked! You can now text or call (415) 599-2671 and enter 16706552 to schedule Google Calendar events.');
			}
		} else {
			$sStatus = 'fail';
			$aMessages = $aErrors;
		}
		
		$aReturn = array(
			'status' => $sStatus,
			'data' => array('messages' => $aMessages),
		);
		echo json_encode($aReturn);
	}
	
	/**
	 * Validates form input and returns an array of error messages (empty if no errors)
	 */
	function validateInput($aInput)
	{
		$aErrors = array();

		// Email address validation sucks
		$bValidEmail = preg_match('!^([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c .\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c\x00-\x7f)*\x22)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c .\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c\x00-\x7f)*\x22))*\x40([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c .\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c\x00-\x7f)*\x5d)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c .\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c\x00-\x7f)*\x5d))+$!', $aInput['email']) ? true : false;

		if ($bValidEmail) {
			$sEmail = $aInput['email'];
		} else {
			$aErrors['email'] = 'Please enter a valid gmail email address';
		}

		if (strlen($aInput['name']) < 100) {
			$sName = $aInput['name'];
		} else {
			$aErrors['name'] = 'Please enter a name. It may be no longer than 100 characters';
		}

		// I don't actually know what gmail's password max length is
		if (!empty($aInput['password']) && strlen($aInput['password']) < 50) {
			$sPassword = $aInput['password'];
		} else {
			$aErrors['password'] = 'Please enter your gmail password. It may be no longer than 50 characters';
		}

		if (strlen($aInput['phone']) == 0) {
			$aErrors['phone'] = 'Please enter a valid US phone number.';
		}

		return $aErrors;
	}
?>

<?php if (!$bAjax) { ?>
<!DOCTYPE HTML>
<html>
    <head>
        <title></title>
        <link rel="stylesheet" href="css/reset.css" media="screen" />
        <link rel="stylesheet" href="css/960.css" media="screen" />
        <link rel="stylesheet" href="css/layout.css" media="screen" />
    </head>
	<body>
		<?php if ($_SERVER['REQUEST_METHOD'] == 'POST') { ?>
		<?php 
			if ($aErrors) {
				echo "<ul>";
				foreach ($aErrors as $sError) {
					echo "<li>$sError</li>";
				}
				echo "</ul>";
			} else {
				echo "<p>Hooray, It worked!<p><br /><p>You can now text or call (415) 599-2671 and enter 16706552 to schedule Google Calendar events.";
			}
		?>
		<?php } else { ?>
		<div class="container_12">
        
            <div class="calendar-body grid_8">
            	<div class="calendar-body-date">
            		<span class="day"></span>
            		<span class="month"></span>
            		<span class="year"></span>
            	</div>
            	<div class="calendar-body-inner">
            	<div id="global"></div>
                <form name="register" method="post" action="account.php">
                
                	<input type="text" name="name" class="grid_4 alpha omega" placeholder="Your Name" value="" /><div data-type="name" class="error"></div>
                	<div class="clear"></div>
                	<input type="text" name="phone" class="grid_4 alpha omega" placeholder="Your Phone" value="" /><div data-type="phone" class="error"></div>
                	<div class="clear"></div>
                	<input type="text" name="email" class="grid_4 alpha omega" placeholder="Your Gmail" value="" /><div data-type="email" class="error"></div>
                	<div class="clear"></div>
                	<input type="text" name="password" class="grid_4 alpha omega" placeholder="Your Gmail Password" value="" /><div data-type="password" class="error"></div>
                	<div class="clear"></div>
                	<input type="submit" />
                </form>
            	</div>
            	<ul class="calendar-body-grid grid_6 alpha omega">
            		<li><h4>7:00</h4></li>
            		<li><h4>8:00</h4></li>
            		<li><h4>9:00</h4></li>
            		<li><h4>10:00</h4></li>
            		<li><h4>11:00</h4></li>
            		<li><h4>12:00</h4></li>
            	</ul>
            </div>
        
        </div>
		<?php } ?>
    	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
    	<script src="js/twilio.js"></script>
	</body>	
</html>
<?php } ?>
