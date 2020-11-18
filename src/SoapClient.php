<?php

namespace Salesforce;
use SoapHeader;
use \SoapClient as PHPSoapClient;

class SoapClient {

    private $sessionId;

    private $wsdl;

    public function __construct($sessionId){

        $this->sessionId = $sessionId;

        
    }

    public function execute($class, $method, $params = null){

		$namespace = "http://soap.sforce.com/schemas/class/{$class}";

		$sessionHeader = new SoapHeader($namespace, 'SessionHeader', array (
			'sessionId' => $this->sessionId
		));


        $client = new PHPSoapClient($this->wsdl);

        $client->__setSoapHeaders($sessionHeader);
        
        $out = $client->$method($params);

		return $out->result;
    }

    public function load($wsdl){

        if(!file_exists($wsdl)){

            throw new Exception("WSDL_ERROR:  No wsdl file at {$wsdl}");
        }

        $this->wsdl = $wsdl;

    }
}