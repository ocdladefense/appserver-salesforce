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
				
        $resp = new stdClass();
        $resp->error = null;
        $resp->something = null;
				
				
				$org = load_org();
				

				// Will be false if the file is not found.
				$orgWsdl = path_to_wsdl("enterprise");
				$clientWsdl = path_to_wsdl($class);
				



				
				if(false === $orgWsdl) {
					throw new Exception("INVALID_WSDL: Wsdl file for $org could not be found.");
				}
				
				

				
		
				// We can set these variables first.
				$namespace = "http://soap.sforce.com/schemas/class/{$class}";
        define ("_WS_NAME_", $class); 
        define ("_WS_WSDL_", $orgWsdl); 
        define ("_WS_NAMESPACE_", $namespace);
		
		
				// Instantiate the partner Client.
        $sfdc = new SforcePartnerClient();
        // Create a connection using the appropriate wsdl
        $sfdc->createConnection($orgWsdl);
        
        

        




        try {
					// Returns a LoginResult object.
          $result = $sfdc->login($org["username"],$org["password"],$org["token"]);
        } catch (Exception $e) {
        	$ip = "52.whatever";//get_current_ip_address();
					$message = $e->faultstring ." CHECK THAT YOUR IP ADDRESS {$ip} IS WHITELISTED.";
					"Failed to login to SforcePartnerClient". $message;
					throw new Exception($message);
        }
				

				
				// Just moved this code.
        $url = parse_url($sfdc->getLocation());
        $server = substr($url['host'],0,strpos($url['host'], '.'));
				$endpoint = "https://{$server}.salesforce.com/services/wsdl/class/{$class}";
        define ("_SFDC_SERVER_", $server); // done	
        define ("_WS_ENDPOINT_", $endpoint);
        
        
        

        
        // $this->setSoapClientConfiguration($class);

        // --> End of Step 1
        
        
        
				if(false === $clientWsdl) {
					throw new Exception("INVALID_WSDL: Wsdl file for $class could not be found.");
				}

        $client = new SoapClient($clientWsdl);
        
        
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

        return $this->invokeMethod("CustomOrder", "generateOrder", array(
        	"customerId" => $contactId,
        	"pricebookEntryId" => $pricebookEntryId
        ));
    }
}

