<?php

	// Runs the composer autoloader.
	require("../bootstrap.php");

	$module = new SalesforceModule();
	$out = $module->runReport("CurrentMembers");
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