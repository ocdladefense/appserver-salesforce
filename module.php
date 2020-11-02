<?php


class SalesforceModule extends Module{
    private $deps = array();

    public function __construct(){
        parent::__construct();
        $this->routes = salesforceModRoutes();
        $this->dependencies = $this->deps;
        $this->files = array();
        $this->name = "salesforce";
    }
    
    
}
function salesforceModRoutes()
{   
    $salesforceModRoutes = array(
        "generate-order" => array(
            "callback" => "generateOrder",
            "files" => array()
        ),
        "customer-profile-id" => array(
            "callback" => "getCustomerProfileIdFromSalesforce",
            "files" => array()
        )
    );
    return $salesforceModRoutes;
}




function generateOrder($contactId, $pricebookEntryId)
{
    $responseBody = new stdClass();
    $responseBody->orderNumber = null;

    require_once ('../vendor/salesforce/soapclient/SforcePartnerClient.php');
    require_once ('../vendor/salesforce/soapclient/SforceHeaderOptions.php');

    $sfdc = new SforcePartnerClient();
    // create a connection using the partner wsdl
    $SoapClient = $sfdc->createConnection("../config/enterprise.wsdl");
    $loginResult = false;

    try {
        // log in with username, password and security token if required
        $loginResult = $sfdc->login(SALESFORCE_USERNAME,SALESFORCE_PASSWORD,SECURITY_TOKEN);
    } catch (Exception $e) {
        $responseBody->error = "Failed to login to SforcePartnerClient". $e->faultstring;
    }

    //Parse the URL and send it to the configFile
    $parsedURL = parse_url($sfdc->getLocation());
    define ("_SFDC_SERVER_", substr($parsedURL['host'],0,strpos($parsedURL['host'], '.')));
    define ("_WS_NAME_", "CustomOrder");
    define ("_WS_WSDL_", "../config/" . _WS_NAME_ . ".wsdl.xml");
    define ("_WS_ENDPOINT_", 'https://' . _SFDC_SERVER_ . '.salesforce.com/services/wsdl/class/' . _WS_NAME_);
    define ("_WS_NAMESPACE_", 'http://soap.sforce.com/schemas/class/' . _WS_NAME_);

    $client = new SoapClient(_WS_WSDL_);
    $sforce_header = new SoapHeader(_WS_NAMESPACE_, "SessionHeader", array("sessionId" => $sfdc->getSessionId()));
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






function getCustomerProfileIdFromSalesforce($contactId)
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

function getCustomerByContactId($contactId, $instance_url, $access_token) 
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