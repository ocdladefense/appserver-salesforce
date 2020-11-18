<?php


namespace Salesforce;

use Http\HttpConstants;
use Http\HttpHeader;	
use Http\HttpRequest;
use Http\Http;

//Change the wsdl extensions back to xml?
//pass in the clientwsdl name?
//remove configDir

class SoapConnection {


	private $req = null;

	private $httpConfig;


	public function __construct($url) {

		$this->httpConfig = array(
			"returntransfer" => true,
			"useragent" => "Mozilla/5.0",
			"followlocation" => true,
			"ssl_verifyhost" => false,
			"ssl_verifypeer" => false
		);

		
		$req = new HttpRequest($url);	

		$req->addHeader(new HttpHeader("Content-Type", "text/xml; charset=UTF-8"));
		$req->addHeader(new HttpHeader("SOAPAction", "login"));
		$req->setMethod(\Http\HTTP_METHOD_POST);

		$this->req = $req;
	}



	//pass in path to login
	//sc->login("","","",$pathToXml);
	//sc->login($pathToXml);
	//sc->login($pathToXml, $username, $password);
	//sc->login($username)
	//sc->login($clientWsdl, $namespace, $pathToXml);
	public function login($login = null, $password = null, $token = null ) {

		if($password !== null){

			throw new Exception("LOGIN_ERROR: Function");
		}

		$load = file_get_contents($login);

		if($load === false){
			throw new Exception("NO_FILE_CONTENT: There is no file or content at {$login}.");
		}
		
		$this->req->setBody($load);

		$http = new Http($this->httpConfig);

		$resp = $http->send($this->req);

		$xml = $resp->xml();

		$sessionId = $xml->getElementsByTagName("sessionId")[0]->nodeValue;

		return new \Salesforce\SoapClient($sessionId);
	}

}