<?php 

namespace Http;

class Request 
{
	private $server;
	private $query;
	private $body;
	public function __construct(array $server = null, array $query = null, array $body = null) 
	{
		$this->server = $server;
		$this->query = $query;
		$this->body = $body;
	}
	public static function fromGlobals() 
	{
		return new self($_SERVER, $_GET, $_POST);
	}
	public function getQueryParams() {
		return $this->query;
	}
	public function getBody() {
		return $this->body;
	}
	public function getUri() {
		return $this->server['REQUEST_URI'];
	}
}