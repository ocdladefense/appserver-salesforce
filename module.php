<?php


/**
 * Example module for executing Apex using SOAP.
 *
 * Example curl call:
 *
 *   curl https://login.salesforce.com/services/Soap/u/50.0 \
 * 		-H "Content-Type: text/xml; charset=UTF-8" \
 * 		-H "SOAPAction: login" -d @soap-login-community-user.xml | xmllint --format -
 *
 *
 *  Example usage:
 *    	require("bootstrap.php");
 *
 *      $module = new SalesforceModule();
 *
 *			$data = $module->runReport("CurrentMembers");
 *
 *			// Save the 
 *      if(false === file_put_contents("/where/to/save/data.csv", $data)) {
 *				print "There was an error.";
 *			}
 *
 **/



use Salesforce\SoapConnection as SoapConnection;

// If this file is used outside of its framework,
//  then create a class stub for Module.
if(!class_exists("Module")) {

	class Module {
		public function __construct(){}
	}
}



class SalesforceModule extends Module {

	const DEFAULT_ORG_ALIAS = "myDefaultOrg";


    public function __construct() {
        parent::__construct();
    }


	/**
	 * Run an IABC report.
	 *
	 *  Execute a report on the IABC Salesforce instance.
	 *  
	 * @return - Returns a Blob or csv file, or array, that can be 
	 *  saved and further processed.
	 */
    public function runReport($pathToLoginXml, $pathToWsdl, $reportName) {
    
			$url = "https://login.salesforce.com/services/Soap/u/50.0";

			$sc = new SoapConnection($url);

			$client = $sc->login($pathToLoginXml);

			$client->load($pathToWsdl);

			return  $client->execute("Reports", "run", array("reportName" => $reportName));
    }



		/**
		 * Generate a Salesforce Order using a sample ContactId and Product.
		 *
		 * This invokes a SOAP method to generate the Order record in Salesforce.
		 *
		 * @return String Order.OrderNumber
		 */
    public function generateOrderTest(){

		

			$loginType = "admin"; //Other option is community login;

			$pathToLogin = $loginType == "admin" ? getPathToConfig() ."/wsdl/soap-login-admin-user.wsdl" : 
				getPathToConfig() ."/wsdl/soap-login-community-user.wsdl";

			$pathToWsdl = getPathToConfig() . "/wsdl/myDefaultOrg-CustomOrder.wsdl";
	

			$contactId = "0031U00001WaiGcQAJ"; // Specific to your org!
			$pricebookEntryId = "01u1U000001tWTwQAM"; // Specific to your org!


			return $this->generateOrder($pathToLogin, $pathToWsdl, $contactId, $pricebookEntryId);
  	}
  	
  	
  	
  	
  	
		/**
		 * Generate a Salesforce Order from the given contact id and product.
		 *
		 *  This invokes a SOAP method to generate the Order record in Salesforce.
		 *
		 *  @return String Order.OrderNumber
		 */
    public function generateOrder($pathToLogin, $pathToWsdl, $contactId, $pricebookEntryId) {
		
			$url = "https://login.salesforce.com/services/Soap/u/50.0";

			$params = array("customerId" => $contactId, "pricebookEntryId" => $pricebookEntryId);

			$sc = new SoapConnection($url);

			$client = $sc->login($pathToLogin);

			$client->load($pathToWsdl);

			return  $client->execute("CustomOrder", "generateOrder", $params);
	}
	









	/**
	 * Invoke any remote SOAP method.
	 *
	 *  We can invoke an Apex class and method using the class name 
	 *   and method name.
	 *
	 * @return Object (anything).
	 */
	public function invokeMethodWithCredentials($class, $method, $params = array(), $credentials = null) {

		list($orgWsdl, $clientWsdl, $username, $password, $token) = $credentials;


		if(null != $orgWsdl && !file_exists($orgWsdl)) {
			throw new Exception("Org WSDL file not found!");
		}
		
		if(null != $clientWsdl && !file_exists($clientWsdl)) {
			throw new Exception("Client WSDL file not found!");			
		}

		$sc = new SoapConnection();

		try {
			// Call the web service via post
			$resp = $client->{$method}($params);
			$result = $resp->result;
		}
		catch (Exception $e) {
			global $errors; // todo probably remove this.
			$errors = $e->faultstring;
			$result = "Error attempting to call webservice {$class}:{$method}:{$errors}";
		}
	}
	

    
	public function invokeMethod($class, $method, $params = array(), $orgAlias = null) {


		// Load an org configuration.
		$orgAlias = null == $orgAlias ?  self::DEFAULT_ORG_ALIAS : $orgAlias;
		$org = load_org($orgAlias);		
		
		// Discover WSDL files.
		$orgWsdl = path_to_wsdl("enterprise", $orgAlias);
		$clientWsdl = path_to_wsdl($class, $orgAlias);
		
		return $this->invokeMethodWithCredentials($class, $method, $params, array(
			$orgWsdl, $clientWsdl, $org["username"], $org["password"], $org["token"]
		));
	}
    



}

