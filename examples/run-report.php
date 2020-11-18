<?php

	// Runs the composer autoloader.
	require("../bootstrap.php");

	// Uncomment below to enable logging.
	// Curl::$LOG_FILE = "log/curl.log";


	$pathToLogin = "../config/soap-login-admin-user.wsdl";

	$pathToWsdl = "../config/myDefaultOrg-CustomOrder.wsdl";


	$contactId = "0031U00001WaiGcQAJ"; // Specific to your org!
	$pricebookEntryId = "01u1U000001tWTwQAM"; // Specific to your org!
			
	$module = new SalesforceModule();
	$out = $module->generateOrder($pathToLogin, $pathToWsdl, $contactId, $pricebookEntryId); 
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
			<?php print $out; ?>
		</main>
	
		<footer>
		
		</footer>
	</body>

</html>