<?php

class SoapConnection {

        public function __construct($username, $password, $token){


                $resp = new stdClass();
                $resp->error = null;
                $resp->something = null;
                // Will be false if the file is not found.
                $orgWsdl = path_to_wsdl("enterprise", $orgName);
                        

                
                // We can set these variables first.
                $namespace = "http://soap.sforce.com/schemas/class/{$class}";
                define ("_WS_NAME_", $class); 
                define ("_WS_WSDL_", $orgWsdl); 
                define ("_WS_NAMESPACE_", $namespace);
                
                
                // Instantiate the partner Client.
                $sfdc = new SforcePartnerClient();
                // Create a connection using the appropriate wsdl
                $sfdc->createConnection($orgWsdl);
                
                


                // Just moved this code.
                $url = parse_url($sfdc->getLocation());
                $server = substr($url['host'],0,strpos($url['host'], '.'));
                $endpoint = "https://{$server}.salesforce.com/services/wsdl/class/{$class}";
                define ("_SFDC_SERVER_", $server); // done	
                define ("_WS_ENDPOINT_", $endpoint);



                try {
                // Returns a LoginResult object.
                $result = $sfdc->login($org["username"],$org["password"],$org["token"]);

                } catch (Exception $e) {
                        $ip = "52.whatever";//get_current_ip_address();
                        $message = $e->faultstring ." CHECK THAT YOUR IP ADDRESS {$ip} IS WHITELISTED.";
                        "Failed to login to SforcePartnerClient". $message;
                        throw new Exception($message);
                }

        }



        private function execute($client, $method, $params = array()) {

                $clientWsdl = path_to_wsdl($class, $orgName);

                $client = new SoapClient($clientWsdl);
                
                
                $header = new SoapHeader($namespace, "SessionHeader", array(
                        "sessionId" => $sfdc->getSessionId()
                ));

                $client->__setSoapHeaders(array($header));

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
}