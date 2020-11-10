<?php

class SalesforceModule extends Module{

    public function __construct(){
        parent::__construct();
    }

    
    public function generateOrder()//$contactId, $pricebookEntryId)
    {

        $contactId = "0031U00001WaiGcQAJ"; //Specific to your org!
        $pricebookEntryId = "01u1U000001tWTwQAM"; //Specific to your org!
        $responseBody = new stdClass();
        $responseBody->orderNumber = null;

        $SoapClient = $this->getSoapClient();

        $sfdc = new SforcePartnerClient();
        $SoapClient = $sfdc->createConnection("../config/wsdl/enterprise.wsdl");

        $loginResult = false;

        try {
            $loginResult = $sfdc->login(SALESFORCE_USERNAME,SALESFORCE_PASSWORD,SALESFORCE_SECURITY_TOKEN);
        } catch (Exception $e) {
            $responseBody->error = "Failed to login to SforcePartnerClient". $e->faultstring;
        }


        //Parse the URL and send it to the configFile
        $parsedURL = parse_url($sfdc->getLocation());
        define ("_SFDC_SERVER_", substr($parsedURL['host'],0,strpos($parsedURL['host'], '.')));
        define ("_WS_NAME_", "CustomOrder"); // NOTE: CustomOrder should not be hard-coded.  Pass it in via the route for now.
        define ("_WS_WSDL_", "../config/wsdl/" . _WS_NAME_ . ".wsdl.xml"); // NOTE: duplicate reference to wsdl file is above.  Fix.
        define ("_WS_ENDPOINT_", 'https://' . _SFDC_SERVER_ . '.salesforce.com/services/wsdl/class/' . _WS_NAME_);
        define ("_WS_NAMESPACE_", 'http://soap.sforce.com/schemas/class/' . _WS_NAME_);

        $client = new SoapClient(_WS_WSDL_);
        $sforce_header = new SoapHeader(_WS_NAMESPACE_, "SessionHeader", array("sessionId" => $sfdc->getSessionId()));
        // var_dump($sforce_header);exit;
        $client->__setSoapHeaders(array($sforce_header));

        try 
        {
            // call the web service via post
            $params = array(
                    "customerId" => $contactId,
                    "pricebookEntryId" => $pricebookEntryId
            );
            $response = $client->generateOrder($params); // NOTE: generateOrder should not be hard-coded but should be passed in via the route.
            $responseBody->orderNumber = $response->result;
        }
        catch (Exception $e) 
        {
            global $errors;
            $errors = $e->faultstring;
            $responseBody->error = "Error attempting to call webservice via post ".$errors;
        }
        return $responseBody;
    }

    public function getSoapClient(){

        $sfdc = new SforcePartnerClient();
        // create a connection using the partner wsdl
        $SoapClient = $sfdc->createConnection("../config/wsdl/enterprise.wsdl");

        return $SoapClient;
    }
}

