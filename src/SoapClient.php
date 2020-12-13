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

$stream_context = [
    'http' => [
        'content_type'  => 'text/xml;charset=utf-8',
    ]
];

			$options = array(
				"stream_context" => $stream_context,
				"trace" => true,
				"encoding" => "utf-8",
				"cache_wsdl" => WSDL_CACHE_NONE
			);
        $client = new PHPSoapClient($this->wsdl, $options);

        $client->__setSoapHeaders($sessionHeader);
        
        try {
        	$out = $client->$method($params);
				} catch(\SoapFault $e) {
					print "<h2>".$e->getMessage() . "</h2>";
					var_dump($client->__getLastRequestHeaders());
					var_dump($client->__getLastRequest());
					var_dump($client->__getLastResponseHeaders());
					var_dump($client->__getLastResponse());
					exit;
				}				
		return $out->result;
    }

    public function load($wsdl){

        if(!file_exists($wsdl)){

            throw new Exception("WSDL_ERROR:  No wsdl file at {$wsdl}");
        }

        $this->wsdl = $wsdl;

    }
}