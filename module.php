<?php

class SalesforceModule extends Module{

    public function __construct(){
        parent::__construct();
    }

    
public function generateOrder()//$contactId, $pricebookEntryId)
{

    $contactId = "0031U00001KcND7QAN";
    $pricebookEntryId = "01u1U000000z8x4QAA";
    $responseBody = new stdClass();
    $responseBody->orderNumber = null;

    $SoapClient = $this->getSoapClient();
    $loginResult = false;

    try {
        // log in with username, password and security token if required
        $loginResult = $sfdc->login(SALESFORCE_USERNAME,SALESFORCE_PASSWORD,SALESFORCE_SECURITY_TOKEN);
    } catch (Exception $e) {
        $responseBody->error = "Failed to login to SforcePartnerClient". $e->faultstring;
    }

    //Parse the URL and send it to the configFile
    $parsedURL = parse_url($sfdc->getLocation());
    define ("_SFDC_SERVER_", substr($parsedURL['host'],0,strpos($parsedURL['host'], '.')));
    define ("_WS_NAME_", "CustomOrder");
    define ("_WS_WSDL_", "../config/wsdl/" . _WS_NAME_ . ".wsdl.xml");
    define ("_WS_ENDPOINT_", 'https://' . _SFDC_SERVER_ . '.salesforce.com/services/wsdl/class/' . _WS_NAME_);
    define ("_WS_NAMESPACE_", 'http://soap.sforce.com/schemas/class/' . _WS_NAME_);

    $client = new SoapClient(_WS_WSDL_);
    $sforce_header = new SoapHeader(_WS_NAMESPACE_, "SessionHeader", array("sessionId" => $sfdc->getSessionId()));
    // var_dump($sforce_header);exit;
    $client->__setSoapHeaders(array($sforce_header));

    try 
    {
        // call the web service via post
        $wsParams=array('customerId'=>$contactId,'pricebookEntryId' => $pricebookEntryId);
        $response = $client->generateOrder($wsParams);
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

    // require_once ('vendor/developerforce/force.com-toolkit-for-php/soapclient/SforcePartnerClient.php');
    // require_once ('vendor/developerforce/force.com-toolkit-for-php/soapclient/SforceHeaderOptions.php');

    $sfdc = new SforcePartnerClient();
    // create a connection using the partner wsdl
    $SoapClient = $sfdc->createConnection("../config/wsdl/enterprise.wsdl");

    return $SoapClient;
}

private function getOAuthConfig(){

    $oauth_config = array(
        "oauth_url" => SALESFORCE_LOGIN_URL,
        "client_id" => SALESFORCE_CLIENT_ID,
        "client_secret" => SALESFORCE_CLIENT_SECRET,
        "username" => SALESFORCE_USERNAME,
        "password" => SALESFORCE_PASSWORD,
        "security_token" => SALESFORCE_SECURITY_TOKEN,
        "redirect_uri" => SALESFORCE_REDIRECT_URI
    );

    return $oauth_config;
}





public function getCustomerProfileIdFromSalesforce($contactId)
{
    $phpResponse = new stdClass();
    $phpResponse->profileId = null;

    $response = getOauthTokenWithPassword();
    $json = $response->getPhpArray();
    $_SESSION['access_token'] = $json['access_token'];
    $_SESSION['instance_url'] = $json['instance_url'];
    $response = getCustomerByContactId($contactId, $_SESSION['instance_url'],  $_SESSION['access_token']);
    $response = json_decode($response->customer);

    if(!empty($response->error))
        $phpResponse->error = $response->error;
    $phpResponse->profileId = $response->profileId__c;

    return $phpResponse;
}

public function getCustomerByContactId($contactId, $instance_url, $access_token) 
{
    try{
        $response = new stdClass();
        $response->customer = null;
    
        $url = "$instance_url/services/data/v20.0/sobjects/contact/$contactId";
        $request = new HTTPRequest($url);
        $request-> addHeaders("Authorization: OAuth $access_token");
        $response->customer = $request-> makeHTTPRequest();
        $status = $request->getStatus();
    
        if ( $status != 200 )
        {
            $response->error = "Error: call to URL $url failed with status $status, response , curl_error " . 
                    $request->getError() . ", curl_errno " . $request->getErrorNum();
        }
    }
    catch(Exception $e){
        $response->error = $e.getMessage();
    }

    return $response;
}
}