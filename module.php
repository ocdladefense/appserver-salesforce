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

		// , $apexClass, $apexMethod, $orgName
		public function invokeMethod($class, $method, $params = array(), $org = null) {
				$org = $org ?: self::DEFAULT_ORG_ALIAS;
				
				$orgWsdl = path_to_wsdl($org);
				$clientWsdl = path_to_wsdl($class);
				
        $resp = new stdClass();
        $resp->error = null;
        $resp->something = null;
				
		
				// We can set these variables first.
				$namespace = "http://soap.sforce.com/schemas/class/{$class}";
        define ("_WS_NAME_", $class); 
        define ("_WS_WSDL_", $wsdl); 
        define ("_WS_NAMESPACE_", $namespace);
		
		
				// Instantiate the partner Client.
        $sfdc = new SforcePartnerClient();
        // Create a connection using the appropriate wsdl
        $sfdc->createConnection($wsdl);
        $url = parse_url($sfdc->getLocation());
        $server = substr($url['host'],0,strpos($url['host'], '.'));
				$endpoint = "https://{$server}.salesforce.com/services/wsdl/class/{$class}";
        define ("_SFDC_SERVER_", $server); // done	
        define ("_WS_ENDPOINT_", $endpoint);
        
        
        
        exit;
        

        // Returns a LoginResult object.
        $result = $sfdc->login(SALESFORCE_USERNAME,SALESFORCE_PASSWORD,SALESFORCE_SECURITY_TOKEN);

        try {
          $sfdc->login();
        } catch (Exception $e) {
					$resp->error = "Failed to login to SforcePartnerClient". $e->faultstring;
        }
				
				
        // $this->setSoapClientConfiguration($class);

        $client = new SoapClient($wsdl);
        $header = new SoapHeader($namespace, "SessionHeader", array(
        	"sessionId" => $sfdc->getSessionId()
        ));
        $client->__setSoapHeaders(array($header));

        return $this->send($client, $method, $params);
		}
    
    
    public function runReport($orgAlias, $reportName) {
    
    
    }
    
    public function generateOrder($contactId, $pricebookEntryId, $org = null) {



        //The parameters expected by the webservice method in your apex class.
        $this->wsParams = array(
            "customerId" => $contactId,
            "pricebookEntryId" => $pricebookEntryId
        );


			// "CustomOrder", "generateOrder"

    }


	


    private function soapInit($server, $class) {

			
								

    }

    private function generateClient(){


    }

    private function send($client, $method, $params = array()) {

				$result = null;
				
        try 
        {
            // Call the web service via post
            $resp = $client->{$method}($params);
            $result = $resp->result;
        }
        catch (Exception $e) 
        {
            global $errors; // todo probably remove this.
            $errors = $e->faultstring;
            $result = "Error attempting to call webservice via post {$errors}";
        }
        
        
        return $result;
    }

    public function generateOrderTest(){

        $contactId = "0031U00001WaiGcQAJ"; //Specific to your org!
        $pricebookEntryId = "01u1U000001tWTwQAM"; //Specific to your org!

        return $this->generateOrder($contactId, $pricebookEntryId, "myOrg");
    }
}

