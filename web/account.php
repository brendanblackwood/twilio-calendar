<?php
	
	require '../lib/common.php';
	
	$bAjax = isset($_SERVER["HTTP_X_REQUESTED_WITH"]) ? $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest" : false;
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $bAjax) {
		$aSubmit = $_REQUEST['submit'];
		// Format the phone number in the form +1XXXXXXXXXX of +0XXXXXXXXXX
		// I'm not sure if the collect call format +0 ever actually comes up, but might as well have it
		$aSubmit['phone'] = str_replace(array('+','-','(',')','.','-'), '', $aSubmit['phone']);
		if (substr($aSubmit['phone'], 0, 1) == '0' || substr($aSubmit['phone'], 0, 1) == '1') {
			$aSubmit['phone'] = '+' . $aSubmit['phone'];
		} else {
			$aSubmit['phone'] = '+1' . $aSubmit['phone'];
		}

		$aErrors = validateInput($aSubmit);

		// If there were no errors, connect to mysql and insert the data
		if (empty($aErrors)) {
			mysql_connect($aConfig['mysql']['host'], $aConfig['mysql']['user'], $aConfig['mysql']['pass']);
			
			// Make sure we are using the correct database
			$sDb = $aConfig['mysql']['db_name'];
			mysql_query("use $sDb");
			
			$sQuery = sprintf("INSERT INTO account (phone, email, pass, name) VALUES ('%s', '%s', '%s', '%s')",
				mysql_real_escape_string($_POST['phone']),
				mysql_real_escape_string($_POST['email']),
				mysql_real_escape_string($_POST['password']),
				mysql_real_escape_string($_POST['name']));
			$bResult = mysql_query($sQuery);
			
			if (!$bResult) {
				$aError['mysql'] = "The deebees aren't doing what they should. You should go do something else and try again later.";
			}
		}
		
		$aReturn = array(
			'errors' => $aErrors,
			'message' => "Hooray, It worked! You can now text or call (415) 599-2671 and enter 16706552 to schedule Google Calendar events.",
		);
		echo json_encode($aReturn);
		
	} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		// Format the phone number in the form +1XXXXXXXXXX of +0XXXXXXXXXX
		// I'm not sure if the collect call format +0 ever actually comes up, but might as well have it
		$_POST['phone'] = str_replace(array('+','-','(',')','.','-'), '', $_POST['phone']);
		if (substr($_POST['phone'], 0, 1) == '0' || substr($_POST['phone'], 0, 1) == '1') {
			$_POST['phone'] = '+' . $_POST['phone'];
		} else {
			$_POST['phone'] = '+1' . $_POST['phone'];
		}

		$aErrors = validateInput($_POST);

		// If there were no errors, connect to mysql and insert the data
		if (empty($aErrors)) {
			mysql_connect($aConfig['mysql']['host'], $aConfig['mysql']['user'], $aConfig['mysql']['pass']);
			
			// Make sure we are using the correct database
			$sDb = $aConfig['mysql']['db_name'];
			mysql_query("use $sDb");
			
			$sQuery = sprintf("INSERT INTO account (phone, email, pass, name) VALUES ('%s', '%s', '%s', '%s')",
				mysql_real_escape_string($_POST['phone']),
				mysql_real_escape_string($_POST['email']),
				mysql_real_escape_string($_POST['password']),
				mysql_real_escape_string($_POST['name']));
			$bResult = mysql_query($sQuery);
			
			if (!$bResult) {
				$aError['mysql'] = "The deebees aren't doing what they should. You should go do something else and try again later.";
			}
		}
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
<html>
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
		<form method="post" action="account.php">
			<label for="name">Name</label>
			<input type="text" name="name" placeholder="Name" />
			<label for="phone">Phone Number (U.S. Only)</label>
			<input type="text" name="phone" placeholder="Phone #" />
			<label for="email">Google Calendar Email Address <span class="note">This is usually the same as your gmail.com email address</span></label>
			<input type="text" name="email" placeholder="Email" />
			<label for="password">Google Calendar Password<span class="note">This is usually the same as your gmail.com password</span></label>
			<input type="text" name="password" placeholder="Password" />
			<input type="submit" />
		</form>
		<?php } ?>
	</body>	
</html>
<?php } ?>