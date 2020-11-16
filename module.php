<?php
/**
 * Example module for executing Apex using SOAP.
 *
 * Example curl call:
 *
 *   curl https://login.salesforce.com/services/Soap/u/50.0 \
 * 		-H "Content-Type: text/xml; charset=UTF-8" \
 * 		-H "SOAPAction: login" -d @login-community.txt | xmllint --format -
 *
 *
 *  Example usage:
 *    	require("developerforce/force.com-toolkit-for-php/*);
 *			require("src/SoapClient.php");
 *			require("src/SoapConnection.php");
 *			require("module.php"); // this file.
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


// use Force\SoapClient as SoapClient;
use Force\SoapConnection as SoapConnection;


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
    public function runReport($reportName) {
    
    	$credentials = array(
    		"/path/to/enterprise-wsdl.wsdl",
    		"/path/to/client-wsdl.wsdl",
    		"myUsername",
    		"myPassword",
    		"myToken"
    	);
			
			return $this->invokeMethodWithCredentials("IABCReports","run",$reportName,$credentials);
    }



		/**
		 * Generate a Salesforce Order using a sample ContactId and Product.
		 *
		 * This invokes a SOAP method to generate the Order record in Salesforce.
		 *
		 * @return String Order.OrderNumber
		 */
    public function generateOrderTest(){

      $contactId = "0031U00001WaiGcQAJ"; // Specific to your org!
      $pricebookEntryId = "01u1U000001tWTwQAM"; // Specific to your org!

      return $this->generateOrder($contactId, $pricebookEntryId);
  	}
  	
  	
  	
  	
  	
		/**
		 * Generate a Salesforce Order from the given contact id and product.
		 *
		 *  This invokes a SOAP method to generate the Order record in Salesforce.
		 *
		 *  @return String Order.OrderNumber
		 */
    public function generateOrder($contactId, $pricebookEntryId) {

			
      return $this->invokeMethod("CustomOrder", "generateOrder", array(
        "customerId" => $contactId,
        "pricebookEntryId" => $pricebookEntryId
      ));
    }


		public function testReport($reportName = "myReport") {
		
			$clientWsdl 	= "/var/www/webapp/appserver/config/wsdl/iabc-production-Reports.wsdl";
			$namespace 		= "http://soap.sforce.com/schemas/class/Reports";
      $sessionId = "00Df2000000BUEo!ARYAQFpRt._py.xStgyoq3SE1Ex8iHT_fMUFivX1FbJO0P3e5VaKyJe.lSf4O3C2bhqXV5eAGogCmOBZMZWEpD9BGqTtVgMv";
			$sessionHeader = new SoapHeader($namespace, 'SessionHeader', array (
			 'sessionId' => $sessionId
			));
      $client = new SoapClient($clientWsdl);
			$client->__setSoapHeaders($sessionHeader);
      $resp = $client->run("currentMembers");
      
      return $resp->result;
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

			$sc = new SoapConnection($orgWsdl);

			exit;
			
			
			// $client = $sc->login($username, $password, $token);
			

			
			$client->load($clientWsdl);
		

			return $client->execute($class, $method, $params);
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

