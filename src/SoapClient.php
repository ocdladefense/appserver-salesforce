<?php

  
namespace Force;

use \SoapClient as LibSoapClient;
use \SoapHeader as SoapHeader;
use \stdClass;


class SoapClient {

	// The client needs only a valid sessionId to do its work.
	private $sessionId = null;
	

	private $client = null;

	

	public function __construct($sessionId, $wsdl = null) {
		$this->sessionId = $sessionId;

		if(null != $wsdl) {
			$this->load($wsdl);
		}
	}


	public function load($wsdl) {
		$this->client = new LibSoapClient($wsdl);
	}
		
		
		
	public function execute($class, $method, $params = array()) {
		
		if(null == $this->client) {
			throw new \Exception("SOAP_CONFIGURATION_ERROR: WSDL file was not loaded.");
		}
		

		// We can set these variables first.
		$namespace = "http://soap.sforce.com/schemas/class/{$class}";
		// define("_WS_NAME_", $class); 
		// define("_WS_NAMESPACE_", $namespace);
		
		
		
		$client = $this->client;
		
		$result = null;

		
		// Prepare the client and header(s).
		$header = new SoapHeader($namespace, "SessionHeader", array(
			"sessionId" => $this->sessionId
		));
		$client->__setSoapHeaders(array($header));

		try {
			// Call the web service via post
			$resp = $client->{$method}($params);
			$result = $resp->result;
		}
		catch (Exception $e) {
			global $errors; // todo probably remove this.
			$errors = $e->faultstring;
			$result = "Error attempting to call webservice via post {$errors}";
		}


		return $result;
	}
		
}