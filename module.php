<?php

class SalesforceModule extends Module{

    private $sfdc;

    private $responseBody;

    public function __construct(){
        parent::__construct();
    }

    
    public function generateOrder($contactId = null, $pricebookEntryId = null, $apexClass = null, $apexMethod = null)
    {

        $contactId = "0031U00001WaiGcQAJ"; //Specific to your org!
        $pricebookEntryId = "01u1U000001tWTwQAM"; //Specific to your org!
        $apexClass = "CustomOrder";
        $apexMethod = "generateOrder";

        //The parameters expected by the webservice method in your apex class.
        $wsParams = array(
            "customerId" => $contactId,
            "pricebookEntryId" => $pricebookEntryId
        );

        $this->responseBody = new stdClass();
        $this->responseBody->orderNumber = null;

        $this->setUpSoapClientConnection();

        $this->login();

        $this->setSoapClientConfiguration($apexClass);

        $client = $this->generateClient();

        try 
        {
            // call the web service via post
            $response = $client->$apexMethod($wsParams);
            $this->responseBody->orderNumber = $response->result;
        }
        catch (Exception $e) 
        {
            global $errors;
            $errors = $e->faultstring;
            $this->responseBody->error = "Error attempting to call webservice via post ".$errors;
        }
        return $this->responseBody;
    }

    private function setUpSoapClientConnection(){

        $this->sfdc = new SforcePartnerClient();
        // create a connection using the partner wsdl
        return $this->sfdc->createConnection("../config/wsdl/enterprise.wsdl");
    }

    private function login(){

        $loginResult = false;

        try {
            $loginResult = $this->sfdc->login(SALESFORCE_USERNAME,SALESFORCE_PASSWORD,SALESFORCE_SECURITY_TOKEN);
        } catch (Exception $e) {
            $this->responseBody->error = "Failed to login to SforcePartnerClient". $e->faultstring;
        }
    }

    private function setSoapClientConfiguration($apexClass){

        //Parse the URL and send it to the configFile
        $parsedURL = parse_url($this->sfdc->getLocation());
        define ("_SFDC_SERVER_", substr($parsedURL['host'],0,strpos($parsedURL['host'], '.')));
        define ("_WS_NAME_", $apexClass);
        define ("_WS_WSDL_", "../config/wsdl/" . _WS_NAME_ . ".wsdl.xml"); // NOTE: duplicate reference to wsdl file is above.  Fix.
        define ("_WS_ENDPOINT_", 'https://' . _SFDC_SERVER_ . '.salesforce.com/services/wsdl/class/' . _WS_NAME_);
        define ("_WS_NAMESPACE_", 'http://soap.sforce.com/schemas/class/' . _WS_NAME_);

    }

    private function generateClient(){

        $client = new SoapClient(_WS_WSDL_);
        $sforce_header = new SoapHeader(_WS_NAMESPACE_, "SessionHeader", array("sessionId" => $this->sfdc->getSessionId()));
        $client->__setSoapHeaders(array($sforce_header));

        return $client;
    }


}

