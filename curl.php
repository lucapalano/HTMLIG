<?php

/*
 * A cURL PHP class which implements the singleton design pattern
 */
class curl {

	# private properties
	private static $instance = null;
	private $curlResource = null;
	private $requestsOutputs = array();

	# the static method for the singleton
	public static function instance() {
		if (self::$instance === null) {
			self::$instance = new curl();
		}
		return self::$instance;
	}

	# the constructor with initial settings
	private function __construct() {
		$this->setCurlResource(curl_init());

		# Default options
		$this->setOption(CURLOPT_RETURNTRANSFER, 1);
	}

	# close a cURL session
	public function __destruct() {
		curl_close($this->getCurlResource());
	}

	# set a cURL option
	public function setOption($name, $value) {
		$curlSetOption = curl_setopt($this->getCurlResource(), $name, $value);
		if (!$curlSetOption) {
			throw new RuntimeException('setOption: unable to set the cURL option');
		}
	}

	# set a bunch of cURL options passed through array
	public function setOptions($options) {
		foreach ($options as $key => $value) {
			$this->setOption($key, $value);
		}
	}

	# open the connection and get the result
	public function startConnection() {
		$result = curl_exec($this->getCurlResource());
		if ($result === FALSE) {
			throw new RuntimeException('startConnection: unable to execute the connection');
		} else {
			array_push($this->requestsOutputs, $result);
			return $result;
		}
	}



	# getter and setter methods

	public function setCurlResource($curlResource) {
		$this->curlResource = $curlResource;
	}

	public function getCurlResource() {
		return $this->curlResource;
	}

}




?>