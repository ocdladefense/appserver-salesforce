<?php

	// Runs the composer autoloader.
	require("../bootstrap.php");

	// Uncomment below to enable logging.
	// Curl::$LOG_FILE = BASE_PATH . "/log/curl.log";


	$pathToLogin = "../config/soap-login-admin-user.xml";

	$pathToWsdl = "../config/myDefaultOrg-CustomOrder.wsdl";


	// Uncomment if using generate order.
	$contactId = "0031U00001WaiGcQAJ"; 
	
	// Uncomment if using generate order.
	$pricebookEntryId = "01u1U000001tWTwQAM";
			
			
	// Returns the OrderNumber of the newly-created Order.
	$module = new SalesforceModule();
	$out = $module->generateOrder($pathToLogin, $pathToWsdl, $contactId, $pricebookEntryId);
?>
<!doctype html>
<html>
	<head>
		<title>Order Creation</title>
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