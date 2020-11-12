<?php


namespace Force;
use \SforcePartnerClient as SforcePartnerClient;
use \stdClass;



class SoapConnection {


	private $sfdc = null;
	
	

	public function __construct($wsdl, $clientWsdl = null) {
		
		// Instantiate the partner Client.
		$sfdc = new SforcePartnerClient();
		
		// Create a connection using the appropriate wsdl
		$sfdc->createConnection($wsdl);

		// Just moved this code.
		$url = $sfdc->getLocation();
		  
		
		// $parser = parse_url($url);
		// $server = substr($parser['host'],0,strpos($parser['host'], '.'));
		// $endpoint = "https://{$server}.salesforce.com/services/wsdl/class/{$class}";
		// define ("_SFDC_SERVER_", $server); // done	
		// define ("_WS_ENDPOINT_", $endpoint);
		
		$this->sfdc = $sfdc;
	}
	
	
	public function login($username, $password, $token = null) {
		
		try {
			// Returns a LoginResult object.
			$result = $this->sfdc->login($username, $password, $token);

		} catch (Exception $e) {
			$ip = "52.whatever";//get_current_ip_address();
			$message = $e->faultstring ." CHECK THAT YOUR IP ADDRESS {$ip} IS WHITELISTED.";
		
			throw new Exception($message);
		}

		return new SoapClient($this->sfdc->getSessionId());
	}




}