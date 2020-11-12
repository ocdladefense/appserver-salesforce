<?php
/**
 * Example module for executing Apex using SOAP.
 *
 *
 *  Example usage:
 *    
 *      $module = new SalesforceModule();
 *
 *			$data = $module->runReport("CurrentMembers");
 *
 *      file_save_contents($data, "/path/to/file");
 **/


use Force\SoapClient as SoapClient;
use Force\SoapConnection as SoapConnection;


// If this file is used outside of its framework,
//  then create a class stub for Module.
if(!class_exists("Module")) {

	class Module {}
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

			$sc = new SoapConnection($orgWsdl);
			$client = $sc->login($username, $password, $token);
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

