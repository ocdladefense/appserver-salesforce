<?php

	// Runs the composer autoloader.
	require("../bootstrap.php");

	// Uncomment below to enable logging.
	// Curl::$LOG_FILE = BASE_PATH . "/log/curl.log";


	$pathToLogin = "../config/soap-login-community-user.xml";

	$pathToWsdl = "../config/iabc-production-Reports.wsdl";


	// Uncomment if using generate order.
	// $contactId = "0031U00001WaiGcQAJ"; // Specific to your org!
	
	// Uncomment if using generate order.
	// $pricebookEntryId = "01u1U000001tWTwQAM"; // Specific to your org!
			
	$module = new SalesforceModule();
	$out = $module->runReport($pathToLogin, $pathToWsdl, "CurrentMembers"); 
?>
<!doctype html>
<html>
	<head>
		<title>Reports</title>
		<meta charset="utf-8" />
	</head>
	
	<body>
		<header>
		
		</header>
	
		<main>
			<?php print "<pre>" . print_r($out,true) . "</pre>"; ?>
		</main>
	
		<footer>
		
		</footer>
	</body>

</html>