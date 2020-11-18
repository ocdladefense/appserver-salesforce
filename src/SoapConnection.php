<?php


namespace Salesforce;

use Http\HttpConstants;
use Http\HttpHeader;	
use Http\HttpRequest;
use Http\Http;
use Http\Curl;
use \stdClass;



class SoapConnection {


	private $req = null;
	
	

	public function __construct() {


			$loginType = "admin"; //Other option is community login;




			$config = array(
				"returntransfer" => true,
				"useragent" => "Mozilla/5.0",
				"followlocation" => true,
				"ssl_verifyhost" => false,
				"ssl_verifypeer" => false
			);

			$url = "https://login.salesforce.com/services/Soap/u/50.0";
			$request = new HttpRequest($url);	

			$request->addHeader(new HttpHeader("Content-Type", "text/xml; charset=UTF-8"));
			$request->addHeader(new HttpHeader("SOAPAction", "login"));
			
			$pathToLogin = $loginType == "admin" ? $dir."/config/soap-login-admin-user.xml" : 
				$dir."/config/soap-login-community-user.xml";

			$request->setMethod(\Http\HTTP_METHOD_POST);
	}



	
	public function login($username = null, $password = null, $token = null) {
		

		$load = file_get_contents($pathToLogin);

		if($load === false){
			throw new Exception("NO_FILE_CONTENT: There is no file or content at {$pathToLogin}.");
		}
		$request->setBody($load);

		$http = new Http($config);

		$resp = $http->send($request);

		$xml = $resp->xml();

		$sessionId = $xml->getElementsByTagName("sessionId")[0]->nodeValue;


		$clientWsdl 	= "{$basePath}/config/wsdl/enterprise.wsdl";
		$namespace 		= "http://soap.sforce.com/schemas/class/CustomOrder";

		$sessionHeader = new SoapHeader($namespace, 'SessionHeader', array (
			'sessionId' => $sessionId
		));

		$client = new SoapClient($clientWsdl);
		$client->__setSoapHeaders($sessionHeader);


		return $client;
	}




}