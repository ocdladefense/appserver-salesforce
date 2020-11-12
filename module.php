<?php

class SalesforceModule extends Module {

		const DEFAULT_ORG_ALIAS = "myDefaultOrg";

    private $sfdc;

    private $client;

    private $wsParams;

    private $responseBody;


    public function __construct() {
        parent::__construct();
    }


		public function myTest() {
			$this->invokeMethod("foobar","baz");
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

		// , $apexClass, $apexMethod, $orgName
  public function invokeMethod($class, $method, $params = array(), $orgName = null) {


				

        $orgName = $orgName ?: self::DEFAULT_ORG_ALIAS;

        $org = load_org($orgName);
        
        $sc = new SoapConnection($org["username"], $org["password"], $org["token"]);
        
        $result = $sc->execute($class, $method, $params);

    }
    

    
    
    public function runReport($orgAlias, $reportName) {
    
    
    }


}

