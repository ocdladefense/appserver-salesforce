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
    
    
    }
    
    public function generateOrder($contactId, $pricebookEntryId, $org = null) {

      return $this->invokeMethod("CustomOrder", "generateOrder", array(
        "customerId" => $contactId,
        "pricebookEntryId" => $pricebookEntryId
      ));

    }

    public function generateOrderTest(){

      $contactId = "0031U00001WaiGcQAJ"; //Specific to your org!
      $pricebookEntryId = "01u1U000001tWTwQAM"; //Specific to your org!

      return $this->invokeMethod("CustomOrder", "generateOrder", array(
        "customerId" => $contactId,
        "pricebookEntryId" => $pricebookEntryId
      ));

  }


  public function invokeMethod($class, $method, $params = array(), $orgName = null) {

		// Specify org configurations.
		$orgName = $orgName ?: self::DEFAULT_ORG_ALIAS;
		$org = load_org($orgName);		
		
		// Discover WSDL files.
		$orgWsdl = path_to_wsdl("enterprise", $orgName);
		$clientWsdl = path_to_wsdl($class, $orgName);


		$sc = new SoapConnection($orgWsdl);
		$client = $sc->login($org["username"],$org["password"],$org["token"]);
		$client->load($clientWsdl);
		
		
		return $client->execute($class, $method, $params);
	}
    

    
    



}

